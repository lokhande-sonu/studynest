<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Customer;
use App\Models\ProductStockInventory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PDF;

use Log;
use Mail;
use App\Mail\OrderStatusUpdateMail;
use App\Helpers\NotificationHelper;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with('customer')->orderBy('order_id', 'desc')->get();

        if ($request->routeIs('management.order-report.index')) {
            return view('management.order-report', compact('orders'));
        }

        if ($request->routeIs('management.transaction-report.index')) {
            return view('management.transaction-report', compact('orders'));
        }

        return view('management.order', compact('orders'));
    }
    
    /**
     * Show Order Report with Filters
     */
    public function OrderReportindex(Request $request)
    {
        $query = Order::with('customer')->orderBy('order_id', 'desc');

        // 🔹 Date filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('order_date_time', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        // 🔹 Customer filter
        if ($request->filled('customer_id')) {
            $query->where('order_placed_cust_id', $request->customer_id);
        }

        // 🔹 Order status filter
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        $orders = $query->get();
        $customers = Customer::orderBy('cust_name')->get();

        return view('management.order-report', compact('orders', 'customers'));
    }
    
    

    /**
     * Export Excel (WITH SAME FILTERS)
     */
    public function exportExcel(Request $request)
    {
        $orders = $this->filteredOrders($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray([
            'Order ID',
            'Customer',
            'Mobile',
            'Order Date',
            'Amount',
            'Payment Status',
            'Order Status',
        ], null, 'A1');

        $row = 2;

        foreach ($orders as $order) {
            $sheet->fromArray([
                $order->order_id,
                $order->customer->cust_name ?? 'N/A',
                $order->customer->cust_mobile ?? 'N/A',
                date('d/m/Y h:i A', strtotime($order->order_date_time)),
                $order->order_total_amt,
                $this->paymentStatusLabel($order->order_payment_status),
                $this->orderStatusLabel($order->order_status),
            ], null, 'A' . $row);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'order-report-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $fileName
        );
    }

    /**
     * Export PDF (WITH SAME FILTERS)
     */
    public function exportPdf(Request $request)
    {
        $orders = $this->filteredOrders($request);

        $pdf = PDF::loadView('management.exports.order-report-pdf', compact('orders'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('order-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Shared filtered query
     */
    private function filteredOrders(Request $request)
    {
        $query = Order::with('customer')->orderBy('order_id', 'desc');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('order_date_time', [
                $request->from_date . ' 00:00:00',
                $request->to_date . ' 23:59:59',
            ]);
        }

        if ($request->filled('customer_id')) {
            $query->where('order_placed_cust_id', $request->customer_id);
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        return $query->get();
    }

    private function orderStatusLabel($status)
    {
        return \App\Enums\OrderStatus::label($status);
    }

    private function paymentStatusLabel($status)
    {
        return match ((int)$status) {
            1 => 'Paid',
            2 => 'Pending',
            default => 'Failed',
        };
    }


    /**
     * Display the specified order details.
     */
    public function show($id)
    {
        $order = Order::with('customer')->findOrFail($id);
        
        return view('management.order-details', compact('order'));
        
        
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => 'required|integer',
        ]);

        $newStatus = (int) $request->order_status;

        if (!$this->allowedStatusTransition($order->order_status, $newStatus)) {
            return redirect()->back()->with('error', 'Invalid order status transition from "' . OrderStatus::label($order->order_status) . '" to "' . OrderStatus::label($newStatus) . '".');
        }

        $this->applyStatus($order, $newStatus);

        $statusText = $this->orderStatusLabel($order->order_status);

        $delivery = $order->order_delivery_details;

        try {
            Mail::to($delivery['email'] ?? null)
                ->send(new OrderStatusUpdateMail(
                    $order,
                    $statusText,
                    now()->format('d M Y h:i A')
                ));
        } catch (\Exception $e) {
            Log::error('Order Status Update Email Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        // WhatsApp & SMS Notification to Customer
        try {
            if ($delivery['phone'] ?? null) {
                NotificationHelper::notify('order_status_update', [
                    'mobile' => $delivery['phone'],
                    'name' => ($delivery['first_name'] ?? 'Customer') . ' ' . ($delivery['last_name'] ?? ''),
                    'order_id' => $order->order_id,
                    'status' => $statusText
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Order Status Update Notification Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return redirect()->back()->with('success', 'Order updated successfully.');
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($request->has('status')) {
            $newStatus = (int) $request->status;

            if (!$this->allowedStatusTransition($order->order_status, $newStatus)) {
                return redirect()->back()->with('error', 'Invalid order status transition from "' . OrderStatus::label($order->order_status) . '" to "' . OrderStatus::label($newStatus) . '".');
            }

            $this->applyStatus($order, $newStatus);

            $statusText = $this->orderStatusLabel($order->order_status);

            $delivery = $order->order_delivery_details;

            try {
                Mail::to($delivery['email'] ?? null)
                    ->send(new OrderStatusUpdateMail(
                        $order,
                        $statusText,
                        now()->format('d M Y h:i A')
                    ));
            } catch (\Exception $e) {
                Log::error('Order Status Update Email Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }

            // WhatsApp & SMS Notification to Customer
            try {
                if ($delivery['phone'] ?? null) {
                    NotificationHelper::notify('order_status_update', [
                        'mobile' => $delivery['phone'],
                        'name' => ($delivery['first_name'] ?? 'Customer') . ' ' . ($delivery['last_name'] ?? ''),
                        'order_id' => $order->order_id,
                        'status' => $statusText
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Order Status Update Notification Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }

            return redirect()->back()->with('success', 'Order status updated successfully.');
        }

        // Payment status update if needed
        if ($request->has('payment_status')) {
            $order->order_payment_status = (int) $request->payment_status;
            if ($order->order_payment_status == 1) {
                $order->order_paid_amt = $order->order_total_amt;
                $order->order_due_amt = 0;
                $order->order_payment_date_time = now();
            }
            $order->order_updated_at = now();
            $order->save();
            return redirect()->back()->with('success', 'Payment status updated successfully.');
        }

        return redirect()->back()->with('error', 'Status not provided.');
    }

    /**
     * Whether a management-side status transition is allowed.
     */
    private function allowedStatusTransition($from, $to): bool
    {
        $from = (int) $from;
        $to = (int) $to;

        if ($from === $to) {
            return true;
        }

        $transitions = [
            OrderStatus::PENDING   => [OrderStatus::CONFIRMED, OrderStatus::CANCELLED],
            OrderStatus::CONFIRMED => [OrderStatus::DELIVERED, OrderStatus::CANCELLED],
            OrderStatus::DELIVERED => [],
            OrderStatus::CANCELLED => [],
        ];

        return in_array($to, $transitions[$from] ?? [], true);
    }

    /**
     * Persist a new status and keep stock in sync (restore on cancel).
     */
    private function applyStatus(Order $order, int $newStatus): void
    {
        $oldStatus = (int) $order->order_status;

        if ($newStatus === OrderStatus::CANCELLED && $oldStatus !== OrderStatus::CANCELLED) {
            $this->restoreOrderStock($order);
        }

        $order->order_status = $newStatus;
        $order->order_updated_at = now();
        $order->save();
    }

    private function restoreOrderStock(Order $order): void
    {
        foreach ($order->order_items as $item) {
            $stock = \App\Services\CartService::stockFor((int) $item['product_id'], $item['variant_id'] ?? null);

            if ($stock) {
                $stock->increment('available_stock', (int) ($item['product_qty'] ?? 1));
            }
        }
    }
    
    /**
     * Update the Payment status of the specified order.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($request->has('pay_status')) {
            $order->order_payment_status = $request->pay_status;
            $order->order_paid_amt = $order->order_due_amt;
            $order->order_updated_at = now();
            $order->save();
            return redirect()->back()->with('success', 'Order Payment updated successfully.');
        }
        
        // Payment status update if needed
        if ($request->has('payment_status')) {
             $order->order_payment_status = $request->payment_status;
             $order->order_updated_at = now();
             $order->save();
             return redirect()->back()->with('success', 'Payment status updated successfully.');
        }

        return redirect()->back()->with('error', 'Status not provided.');
    }

    /**
     * Generate Invoice
     */
    public function generateInvoice($id)
    {
        $order = Order::with('customer')->findOrFail($id);
        
        $pdf = \PDF::loadView('management.invoice', compact('order'));
        return $pdf->download('invoice-' . $order->order_id . '.pdf');
    }

    /**
     * Export Order Report
     */
    public function exportOrderReport()
    {
        // $id here might be a placeholder or used for filtering if needed, 
        // but typically reports are for a collection.
        // Assuming we export ALL orders for the report currently displayed.
        // Or if $id is meant to be a filter type (like 'all', 'today'), handle accordingly.
        // For simplicity, we export all.

        $orders = Order::with('customer')->orderBy('order_id', 'desc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Order ID');
        $sheet->setCellValue('B1', 'Customer Name');
        $sheet->setCellValue('C1', 'Date');
        $sheet->setCellValue('D1', 'Amount');
        $sheet->setCellValue('E1', 'Payment Status');
        $sheet->setCellValue('F1', 'Order Status');

        $row = 2;
        foreach ($orders as $order) {
            $sheet->setCellValue('A' . $row, $order->order_id);
            $sheet->setCellValue('B' . $row, $order->customer->cust_name ?? 'N/A');
            $sheet->setCellValue('C' . $row, date('d M Y, h:i A', strtotime($order->order_date_time)));
            $sheet->setCellValue('D' . $row, $order->order_total_amt);
            $sheet->setCellValue('E' . $row, $order->order_payment_status);
            $sheet->setCellValue('F' . $row, $order->order_status);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $fileName = 'orders-report-' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName) .'"');
        $writer->save('php://output');
        exit;
    }

    public function transactionReport(Request $request)
    {
        $query = Order::with('customer');
    
        // Date filter (payment date)
        if ($request->filled('from_date')) {
            $query->whereDate('order_payment_date_time', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('order_payment_date_time', '<=', $request->to_date);
        }
    
        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('order_placed_cust_id', $request->customer_id);
        }
    
        // Order status filter
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }
    
        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('order_payment_status', $request->payment_status);
        }
    
        $orders = $query->orderBy('order_id', 'desc')->get();
        $customers = Customer::orderBy('cust_name')->get();
    
        return view('management.transaction-report', compact('orders', 'customers'));
    }
    
    public function exportTransactionReportExcel(Request $request)
    {
        $query = Order::with('customer');
    
        if ($request->filled('from_date')) {
            $query->whereDate('order_payment_date_time', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('order_payment_date_time', '<=', $request->to_date);
        }
    
        if ($request->filled('customer_id')) {
            $query->where('order_placed_cust_id', $request->customer_id);
        }
    
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }
    
        if ($request->filled('payment_status')) {
            $query->where('order_payment_status', $request->payment_status);
        }
    
        $orders = $query->orderBy('order_id', 'desc')->get();
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Header
        $sheet->fromArray([
            ['Order ID', 'Customer', 'Items Qty', 'Order Date', 'Total Amt', 'Payment Status', 'Order Status', 'Transaction']
        ], null, 'A1');
    
        $row = 2;
        foreach ($orders as $order) {
            $sheet->fromArray([
                $order->order_id,
                $order->customer->cust_name ?? 'N/A',
                $order->order_items_qty,
                date('d/m/Y h:i A', strtotime($order->order_date_time)),
                $order->order_total_amt,
                $order->order_payment_status == 1 ? 'Paid' : 'Pending',
                \App\Enums\OrderStatus::label($order->order_status),
                $order->order_payment_id . ' via ' .
                ($order->order_payment_mode == 1 ? 'COD' : 'Online')
            ], null, 'A' . $row);
    
            $row++;
        }
    
        $fileName = 'transaction-report-' . date('Y-m-d') . '.xlsx';
    
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $fileName);
    }
    
    public function exportTransactionReportPdf(Request $request)
    {
        $query = Order::with('customer');
    
        if ($request->filled('from_date')) {
            $query->whereDate('order_payment_date_time', '>=', $request->from_date);
        }
    
        if ($request->filled('to_date')) {
            $query->whereDate('order_payment_date_time', '<=', $request->to_date);
        }
    
        if ($request->filled('customer_id')) {
            $query->where('order_placed_cust_id', $request->customer_id);
        }
    
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }
    
        if ($request->filled('payment_status')) {
            $query->where('order_payment_status', $request->payment_status);
        }
    
        $orders = $query->orderBy('order_id', 'desc')->get();
    
        $pdf = Pdf::loadView('management.exports.transaction-report-pdf', compact('orders'))
                  ->setPaper('a4', 'landscape');
    
        return $pdf->download('transaction-report-' . date('Y-m-d') . '.pdf');
    }

}
