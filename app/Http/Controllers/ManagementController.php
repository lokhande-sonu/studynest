<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\PreBooking;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function dashboard()
    {
        /* =======================
         * BASIC COUNTS (CUMULATIVE)
         * ======================= */
        $totalOrders = Order::count();

        // Status 2 is 'Order Placed' (Pending)
        $pendingOrders = Order::where('order_status', 2)->count();

        $totalCustomers = Customer::where('cust_status', 1)->count();

        $totalRevenue = Order::where('order_payment_status', 1)->sum('order_paid_amt');

        /* =======================
         * NEW ORDERS TODAY
         * ======================= */
        $newOrdersToday = Order::whereDate('order_created_at', Carbon::today())->count();

        /* =======================
         * TODAY'S DAILY SUMMARY
         * ======================= */
        $todayOrders = Order::whereDate('order_created_at', Carbon::today())->count();
        $todayPending = Order::whereDate('order_created_at', Carbon::today())->where('order_status', \App\Enums\OrderStatus::PENDING)->count();
        $todayConfirmed = Order::whereDate('order_created_at', Carbon::today())->where('order_status', \App\Enums\OrderStatus::CONFIRMED)->count();
        $todayDelivered = Order::whereDate('order_created_at', Carbon::today())->where('order_status', \App\Enums\OrderStatus::DELIVERED)->count();
        $todaySales = Order::whereDate('order_created_at', Carbon::today())->where('order_payment_status', 1)->sum('order_paid_amt');
        $todayNewCustomers = Customer::whereDate('cust_created_at', Carbon::today())->count();
        $todayPrebookings = PreBooking::whereDate('created_at', Carbon::today())->count();
        $todayPaymentSummary = Order::whereDate('order_payment_date_time', Carbon::today())->where('order_payment_status', 1)->sum('order_paid_amt');

        /* =======================
         * RECENT ORDERS (for dashboard table)
         * ======================= */
        $recentOrders = Order::with('customer')
            ->orderBy('order_id', 'desc')
            ->limit(10)
            ->get();

        /* =======================
         * CURRENT MONTH SALES
         * ======================= */
        $currentMonthSales = Order::whereMonth('order_created_at', now()->month)
            ->whereYear('order_created_at', now()->year)
            ->where('order_payment_status', 1)
            ->sum('order_paid_amt');

        /* =======================
         * WEEKLY SALES (CURRENT MONTH)
         * ======================= */
        $weeklySales = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Week 1: Days 1-7
        $weeklySales['Week 1'] = Order::whereBetween('order_created_at', [
            $startOfMonth->copy(), 
            $startOfMonth->copy()->addDays(7)->subSecond()
        ])->where('order_payment_status', 1)->sum('order_paid_amt');

        // Week 2: Days 8-14
        $weeklySales['Week 2'] = Order::whereBetween('order_created_at', [
            $startOfMonth->copy()->addDays(7), 
            $startOfMonth->copy()->addDays(14)->subSecond()
        ])->where('order_payment_status', 1)->sum('order_paid_amt');

        // Week 3: Days 15-21
        $weeklySales['Week 3'] = Order::whereBetween('order_created_at', [
            $startOfMonth->copy()->addDays(14), 
            $startOfMonth->copy()->addDays(21)->subSecond()
        ])->where('order_payment_status', 1)->sum('order_paid_amt');

        // Week 4: Days 22-End of Month
        $weeklySales['Week 4'] = Order::whereBetween('order_created_at', [
            $startOfMonth->copy()->addDays(21), 
            $startOfMonth->copy()->endOfMonth()
        ])->where('order_payment_status', 1)->sum('order_paid_amt');

        /* =======================
         * MONTHLY ORDERS (YEAR)
         * ======================= */
        $monthlyOrdersRaw = Order::selectRaw('MONTH(order_created_at) as month, COUNT(*) as total')
            ->whereYear('order_created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyOrders = collect(range(1, 12))->map(function ($month) use ($monthlyOrdersRaw) {
            $record = $monthlyOrdersRaw->firstWhere('month', $month);
            return [
                'label' => Carbon::create()->month($month)->format('M'),
                'value' => $record ? $record->total : 0
            ];
        });

        return view('management.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'totalCustomers',
            'totalRevenue',
            'currentMonthSales',
            'weeklySales',
            'monthlyOrders',
            'recentOrders',
            'newOrdersToday',
            'todayOrders',
            'todayPending',
            'todayConfirmed',
            'todayDelivered',
            'todaySales',
            'todayNewCustomers',
            'todayPrebookings',
            'todayPaymentSummary'
        ));
    }
}
