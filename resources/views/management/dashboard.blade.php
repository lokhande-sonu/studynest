@extends('management.layout.app')
@section('title', 'Dashboard')

@section('content')
<div id="top" class="sa-app__body px-2 px-lg-4">
    <div class="container pb-6">
        @include('management.layout.flash-messages')

        <div class="py-5">
            <div class="row g-4 align-items-center">
                <div class="col">
                    <h4 class="h4 m-0">Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- TODAY'S BUSINESS SUMMARY -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <h5 class="mb-3 text-primary">Today's Business Summary</h5>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.order-report.index', ['from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #0d6efd;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Today's Orders</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value text-primary">{{ $todayOrders }}</div>
                            <div class="saw-indicator__caption">Total orders placed today</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.order-report.index', ['order_status' => 1, 'from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #ffc107;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Today's Pending</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value text-warning">{{ $todayPending }}</div>
                            <div class="saw-indicator__caption">Orders pending today</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.order-report.index', ['order_status' => 3, 'from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #198754;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Today's Completed</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value text-success">{{ $todayDelivered }}</div>
                            <div class="saw-indicator__caption">Orders delivered today</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #198754;">
                    <div class="sa-widget-header saw-indicator__header">
                        <h2 class="sa-widget-header__title">Today's Sales</h2>
                    </div>
                    <div class="saw-indicator__body">
                        <div class="saw-indicator__value text-success">₹ {{ number_format($todaySales, 0) }}</div>
                        <div class="saw-indicator__caption">Paid revenue today</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.customers.index') }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #0dcaf0;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">New Customers</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value text-info">{{ $todayNewCustomers }}</div>
                            <div class="saw-indicator__caption">New customers today</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.prebooking.index') }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #6f42c1;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Pre-Bookings</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value text-purple" style="color: #6f42c1;">{{ $todayPrebookings }}</div>
                            <div class="saw-indicator__caption">Pre-bookings today</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1 h-100" style="border-left: 4px solid #198754;">
                    <div class="sa-widget-header saw-indicator__header">
                        <h2 class="sa-widget-header__title">Payment Summary</h2>
                    </div>
                    <div class="saw-indicator__body">
                        <div class="saw-indicator__value text-success">₹ {{ number_format($todayPaymentSummary, 0) }}</div>
                        <div class="saw-indicator__caption">Payments received today</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OVERALL CUMULATIVE CARDS (CLICKABLE) -->
        <div class="row g-4 g-xl-5 mb-4">
            <div class="col-12">
                <h5 class="mb-3 text-muted">Overall Summary (All Time)</h5>
            </div>

            <!-- TOTAL ORDERS (CLICKABLE) -->
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.order-report.index') }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1" style="cursor: pointer;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Total Orders</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value">{{ $totalOrders }}</div>
                            <div class="saw-indicator__delta saw-indicator__delta--fall">
                                <div class="saw-indicator__delta-value">
                                    {{ now()->format('h:i A') }}
                                </div>
                            </div>
                            <div class="saw-indicator__caption">
                                Calculated Till {{ now()->format('d/m/Y') }}
                                @if($newOrdersToday > 0)
                                    <span class="badge badge-sa-success ms-2">{{ $newOrdersToday }} new today</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- PENDING ORDERS (CLICKABLE) -->
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.order-report.index', ['order_status' => 2]) }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1" style="cursor: pointer;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Pending Orders</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value">{{ $pendingOrders }}</div>
                            <div class="saw-indicator__delta saw-indicator__delta--fall">
                                <div class="saw-indicator__delta-value">
                                    {{ now()->format('h:i A') }}
                                </div>
                            </div>
                            <div class="saw-indicator__caption">
                                Calculated Till {{ now()->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- TOTAL SALES (CLICKABLE) -->
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.transaction-report.index') }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1" style="cursor: pointer;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Total Sales</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value">
                                ₹ {{ number_format($totalRevenue, 2) }}
                            </div>
                            <div class="saw-indicator__delta saw-indicator__delta--fall">
                                <div class="saw-indicator__delta-value">
                                    {{ now()->format('h:i A') }}
                                </div>
                            </div>
                            <div class="saw-indicator__caption">
                                Calculated Till {{ now()->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- TOTAL CUSTOMERS (CLICKABLE) -->
            <div class="col-12 col-md-3 d-flex">
                <a href="{{ route('management.customers.index') }}" class="text-decoration-none text-reset flex-grow-1">
                    <div class="card saw-indicator flex-grow-1" style="cursor: pointer;">
                        <div class="sa-widget-header saw-indicator__header">
                            <h2 class="sa-widget-header__title">Total Active Customers</h2>
                        </div>
                        <div class="saw-indicator__body">
                            <div class="saw-indicator__value">{{ $totalCustomers }}</div>
                            <div class="saw-indicator__delta saw-indicator__delta--fall">
                                <div class="saw-indicator__delta-value">
                                    {{ now()->format('h:i A') }}
                                </div>
                            </div>
                            <div class="saw-indicator__caption">
                                Calculated Till {{ now()->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- THIS MONTH SALES -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-4 col-xxl-3 d-flex">
                <div class="card flex-grow-1 saw-pulse">
                    <div class="sa-widget-header saw-pulse__header">
                        <h2 class="sa-widget-header__title">This Month Sales Statistics</h2>
                    </div>

                    <div class="saw-pulse__counter">
                        ₹ {{ number_format($currentMonthSales, 2) }}
                    </div>

                    <div class="sa-widget-table saw-pulse__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>This Month Weeks</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weeklySales as $week => $amount)
                                <tr>
                                    <td><a href="#" class="text-reset">{{ $week }}</a></td>
                                    <td class="text-end">₹ {{ number_format($amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MONTHLY ORDERS CHART -->
            <div class="col-12 col-lg-8 col-xxl-9 d-flex">
                <div class="card flex-grow-1 saw-chart"
                     data-sa-data='@json($monthlyOrders)'>
                    <div class="sa-widget-header saw-chart__header">
                        <h2 class="sa-widget-header__title">Orders Monthly Statistics</h2>
                    </div>
                    <div class="saw-chart__body">
                        <div class="saw-chart__container">
                            <canvas></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT ORDERS -->
        <div class="row g-4 mt-1">
            <div class="col-12 d-flex">
                <div class="card flex-grow-1">
                    <div class="sa-widget-header">
                        <h2 class="sa-widget-header__title">Recent Orders</h2>
                        <a href="{{ route('management.order.index') }}" class="btn btn-sm btn-secondary">View All Orders</a>
                    </div>
                    <div class="table-responsive">
                        <table class="sa-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items Qty</th>
                                    <th class="text-end">Total Amount</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                    <th>Order Date Time</th>
                                    <th class="w-min" data-orderable="false">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr>
                                    <td><a href="{{ route('management.order.details', $order->order_id) }}" class="text-reset">#{{ $order->order_id }}</a></td>
                                    <td>
                                        @if($order->customer)
                                            {{ $order->customer->cust_name }}
                                            <div class="text-muted mt-n1">{{ $order->customer->cust_mobile }} | {{ $order->customer->cust_email }}</div>
                                        @else
                                            <div class="text-muted">User Deleted</div>
                                        @endif
                                    </td>
                                    <td>{{ $order->order_items_qty }}</td>
                                    <td class="text-end">
                                        <div class="sa-price"><span class="sa-price__symbol">₹ </span><span class="sa-price__integer">{{ number_format(round($order->order_total_amt), 0) }}</span></div>
                                    </td>
                                    <td>
                                        @php
                                            $dashboardPaymentStatusMap = [
                                                1 => ['Paid', 'success'],
                                                2 => ['Pending', 'warning'],
                                                0 => ['Failed', 'danger'],
                                            ];
                                            [$dashboardPayLabel, $dashboardPayBadge] = $dashboardPaymentStatusMap[$order->order_payment_status] ?? ['Unknown', 'secondary'];
                                        @endphp
                                        <span class="badge badge-sa-{{ $dashboardPayBadge }}">{{ $dashboardPayLabel }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-sa-{{ \App\Enums\OrderStatus::badge($order->order_status) }}">{{ \App\Enums\OrderStatus::label($order->order_status) }}</span>
                                    </td>
                                    <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
                                    <td>
                                        <a class="btn btn-sa-muted btn-sm" href="{{ route('management.order.details', $order->order_id) }}" target="_blank">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No orders yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection