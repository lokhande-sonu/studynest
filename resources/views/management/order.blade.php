@extends('management.layout.app') {{-- your layout file --}}
@section('title', 'Customer Orders')

@section('content')

<div id="top" class="sa-app__body">
                <div class="mx-sm-2 px-2 px-sm-3 px-xxl-4 pb-6">
                    <div class="container">
                        @include('management.layout.flash-messages')

                        <div class="py-5">
                            <div class="row g-4 align-items-center">
                                <div class="col">

                                    <h4 class="h4 m-0">Orders Management</h4>
                                    <p class="mt-2">Manage list of all the orders placed from mobile app</p>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card">
                            <div class="p-4"><input type="text" placeholder="Search Orders by Order ID, Customer Name, Customer Mobile Number"
                                    class="form-control form-control--search mx-auto" id="table-search" /></div>
                            <div class="sa-divider"></div>
                            <table class="sa-datatables-init" data-order="[[ 1, &quot;asc&quot; ]]"
                                data-sa-search-input="#table-search">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Details</th>
                                        <th>Items Qty</th>
                                        <th>Total Amount (₹)</th>
                                        <th>Order Date Time</th>
                                        <th>Order Status</th>
                                        <th>Payment Status</th>
                                        <th class="w-min" data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td><a href="#" class="text-reset">#{{ $order->order_id }}</a></td>
                                        
                                        <td>
                                            @if($order->customer)
                                            <div class="d-flex align-items-center">
                                                <div class="sa-symbol sa-symbol--shape--rounded sa-symbol--size--lg me-4">
                                                    @if($order->customer->cust_profile_photo)
                                                         <img src="{{ asset(env('CUSTOMER_PROFILE_PHOTO') . '/' . $order->customer->cust_profile_photo) }}" width="40" height="40" alt="">
                                                    @else
                                                        <div class="sa-symbol__text">{{ substr($order->customer->cust_name, 0, 1) }}</div>
                                                    @endif
                                                </div>
                                                <div>{{ $order->customer->cust_name }}
                                                    <div class="text-muted mt-n1">{{ $order->customer->cust_mobile }} | {{ $order->customer->cust_email }}</div>
                                                    @if($order->order_gst_number)
                                                    <div class="mt-1"><span class="badge badge-sa-info">GST: {{ $order->order_gst_number }}</span></div>
                                                    @endif
                                                </div>
                                            </div>
                                            @else
                                                <div class="text-muted">User Deleted</div>
                                            @endif
                                        </td>
                                        <td>{{ $order->order_items_qty }}</td>
                                        <td>{{ number_format($order->order_total_amt, 2) }}</td>
                                        <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
                                        
                                        <td>
                                            <div class="badge badge-sa-{{ \App\Enums\OrderStatus::badge($order->order_status) }}">
                                                {{ \App\Enums\OrderStatus::label($order->order_status) }}
                                            </div>
                                        </td>
                                        
                                        @php
                                            $paymentStatusMap = [
                                                1 => ['Paid',    'success'],
                                                2 => ['Pending', 'warning'],
                                            ];
                                        
                                            [$payLabel, $payBadge] = $paymentStatusMap[$order->order_payment_status]
                                                ?? ['Unknown', 'secondary'];
                                        @endphp
                                        
                                        <td>
                                            <div class="badge badge-sa-{{ $payBadge }}">
                                                {{ $payLabel }}
                                            </div>
                                        </td>
                                        
                                        
                                     
                                        <td>
                                            <div class="dropdown"><button class="btn btn-sa-muted btn-sm" type="button"
                                                    id="order-context-menu-{{ $order->order_id }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false" aria-label="More"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="3" height="13"
                                                        fill="currentColor">
                                                        <path
                                                            d="M1.5,8C0.7,8,0,7.3,0,6.5S0.7,5,1.5,5S3,5.7,3,6.5S2.3,8,1.5,8z M1.5,3C0.7,3,0,2.3,0,1.5S0.7,0,1.5,0 S3,0.7,3,1.5S2.3,3,1.5,3z M1.5,10C2.3,10,3,10.7,3,11.5S2.3,13,1.5,13S0,12.3,0,11.5S0.7,10,1.5,10z">
                                                        </path>
                                                    </svg></button>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="order-context-menu-{{ $order->order_id }}">
                                                    <!-- Link to Order Details Route -->
                                                    <li><a class="dropdown-item" href="{{ route('management.order.details', $order->order_id) }}" target="_blank">View Order</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('management.order.generate-invoice', $order->order_id) }}" target="_blank">Generate Invoice</a></li>
                                                </ul>
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