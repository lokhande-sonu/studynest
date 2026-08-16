<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function dashboard()
    {
        /* =======================
         * BASIC COUNTS
         * ======================= */
        $totalOrders = Order::count();

        // Pending orders awaiting confirmation
        $pendingOrders = Order::where('order_status', \App\Enums\OrderStatus::PENDING)->count();

        $totalCustomers = Customer::where('cust_status', 1)->count();

        $totalRevenue = Order::where('order_payment_status', 1)->sum('order_paid_amt');

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
            'monthlyOrders'
        ));
    }
}
