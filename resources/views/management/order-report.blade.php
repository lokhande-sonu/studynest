@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Order Report')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                        @include('management.layout.flash-messages')
                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Orders Report</h4>
                                    <p class="mt-2">Filter and Export Product Orders</p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card">
                            <div class="sa-chart-toolbar p-4">
                                    <div class="mb-4">
                                            <input type="text" placeholder="Search Orders by Order ID, Customer Name, Customer Mobile Number"
                                            class="form-control form-control--search" id="table-search" />
                                        </div>
                                    <form method="GET" action="{{ route('management.order-report.index') }}" class="sa-chart-toolbar__body">

                                        <div class="sa-chart-toolbar__item me-auto">
                                            <label class="sa-chart-toolbar__item-label">Order Date</label>
                                            <div class="d-flex">
                                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                                                <span class="mx-2">-</span>
                                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                                            </div>
                                        </div>
                                    
                                        <div class="sa-chart-toolbar__item">
                                            <label class="sa-chart-toolbar__item-label">Customer</label>
                                            <select name="customer_id" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->cust_id }}"
                                                        {{ request('customer_id') == $customer->cust_id ? 'selected' : '' }}>
                                                        {{ $customer->cust_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    
                                        <div class="sa-chart-toolbar__item">
                                            <label class="sa-chart-toolbar__item-label">Order Status</label>
                                            {{-- START: imohitmehto | 2026-08-25 | FIX: Filter labels now match OrderStatus enum --}}
                                            {{-- 0=Cancelled, 1=Pending, 2=Confirmed, 3=Delivered --}}
                                            <select name="order_status" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="1" {{ request('order_status')=='1'?'selected':'' }}>Pending</option>
                                                <option value="2" {{ request('order_status')=='2'?'selected':'' }}>Confirmed</option>
                                                <option value="3" {{ request('order_status')=='3'?'selected':'' }}>Delivered</option>
                                                <option value="0" {{ request('order_status')=='0'?'selected':'' }}>Cancelled</option>
                                            </select>
                                            {{-- END: imohitmehto | FIX: Filter label mapping --}}
                                        </div>
                                    
                                        <div class="sa-chart-toolbar__item">
                                            <button class="btn btn-primary btn-sm me-2">Filter</button>
                                    
                                            <a href="{{ route('management.order-report.export', request()->query()) }}"
                                               class="btn btn-success btn-sm me-2">
                                               Export Excel
                                            </a>
                                    
                                            <a href="{{ route('management.order-report.export-pdf', request()->query()) }}"
                                               class="btn btn-danger btn-sm">
                                               Export PDF
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            <div class="sa-divider"></div>
                            <table class="sa-datatables-init" data-order="[[ 1, &quot;asc&quot; ]]"
                                data-sa-search-input="#table-search">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer Details</th>
                                        <th>Items Qty</th>
                                        <th>Total Amount (₹)</th>
                                        <th>Order Date Time</th>
                                        <th>Order Status</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->order_id }}</td>
                                        
                                        <td>
                                            @if($order->customer)
                                            <div class="d-flex align-items-center">
                                                <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                                    @if($order->customer->cust_profile_photo)
                                                        <img src="{{ asset('uploads/customer-profile-photo/' . $order->customer->cust_profile_photo) }}" width="40" height="40" alt="">
                                                    @else
                                                        <div class="sa-symbol__text">{{ substr($order->customer->cust_name, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                <div>{{ $order->customer->cust_name }}
                                                    <div class="text-muted mt-n1">{{ $order->customer->cust_mobile }} | {{ $order->customer->cust_email }}</div>
                                                </div>
                                            </div>
                                            @else
                                                <div class="text-muted">User Deleted</div>
                                            @endif
                                          
                                        </td>
                                        <td>{{ $order->order_items_qty }}</td>
                                        <td>{{ number_format($order->order_total_amt, 2) }}</td>
                                        <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
                                        @php
                                            // START: imohitmehto | 2026-08-25 | FIX: Status labels now match OrderStatus enum values
                                            $orderStatusMap = [
                                                0 => ['Cancelled',  'danger'],
                                                1 => ['Pending',    'warning'],
                                                2 => ['Confirmed',  'primary'],
                                                3 => ['Delivered',  'success'],
                                            ];
                                        
                                            [$orderLabel, $orderBadge] = $orderStatusMap[$order->order_status]
                                                ?? ['Unknown', 'secondary'];
                                            // END: imohitmehto | FIX: Status label mapping
                                        @endphp
                                        
                                        <td>
                                            <div class="badge badge-sa-{{ $orderBadge }}">
                                                {{ $orderLabel }}
                                            </div>
                                        </td>
                                        
                                        @php
                                            // START: imohitmehto | 2026-08-25 | FIX: Payment status labels now match actual DB values
                                            // 0=Failed, 1=Paid, 2=Pending
                                            $paymentStatusMap = [
                                                1 => ['Paid',    'success'],
                                                2 => ['Pending', 'warning'],
                                            ];
                                        
                                            [$payLabel, $payBadge] = $paymentStatusMap[$order->order_payment_status]
                                                ?? ['Unknown', 'secondary'];
                                            // END: imohitmehto | FIX: Payment status label mapping
                                        @endphp
                                        
                                        <td>
                                            <div class="badge badge-sa-{{ $payBadge }}">
                                                {{ $payLabel }}
                                            </div>
                                        </td>
                                       
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
    @endsection