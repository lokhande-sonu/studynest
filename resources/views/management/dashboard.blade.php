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

        <div class="row g-4 g-xl-5">

            <!-- TOTAL ORDERS -->
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- PENDING ORDERS -->
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1">
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
            </div>

            <!-- TOTAL SALES -->
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1">
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
            </div>

            <!-- TOTAL CUSTOMERS -->
            <div class="col-12 col-md-3 d-flex">
                <div class="card saw-indicator flex-grow-1">
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
            </div>

            <!-- THIS MONTH SALES -->
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
    </div>
</div>
@endsection
