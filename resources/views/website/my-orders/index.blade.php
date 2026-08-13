@extends('website.layouts.app')

@section('title', 'My Orders - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">My Orders</h6>
                <ul class="flex-align gap-8 flex-wrap">
                    <li class="text-sm">
                        <a href="{{ route('website.index') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                            <i class="ph ph-house"></i>
                            Home
                        </a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm text-main-600"> My Orders </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <section class="py-24">
        <div class="container">
            <div class="border border-gray-100 rounded-8 px-24 py-24 bg-white">
                <h5 class="mb-24">Order History</h5>
                
                @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover border border-gray-100 rounded-8 overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-16 px-24 text-gray-500 fw-medium">Order ID</th>
                                <th class="py-16 px-24 text-gray-500 fw-medium">Date</th>
                                <th class="py-16 px-24 text-gray-500 fw-medium">Total Amount</th>
                                <th class="py-16 px-24 text-gray-500 fw-medium">Payment Status</th>
                                <th class="py-16 px-24 text-gray-500 fw-medium">Order Status</th>
                                <th class="py-16 px-24 text-gray-500 fw-medium text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $paymentStatusMap = [
                                        1 => ['Paid', 'success'],
                                        2 => ['Pending', 'warning'],
                                        0 => ['Failed', 'danger'],
                                    ];
                                    [$payLabel, $payBadge] = $paymentStatusMap[$order->order_payment_status] ?? ['Unknown', 'secondary'];
                                @endphp
                                <tr>
                                    <td class="py-16 px-24 fw-medium text-heading">#{{ $order->order_id }}</td>
                                    <td class="py-16 px-24 text-gray-600">{{ date('d M Y', strtotime($order->order_date_time)) }}</td>
                                    <td class="py-16 px-24 text-heading fw-semibold">₹{{ number_format($order->order_total_amt, 2) }}</td>
                                    <td class="py-16 px-24">
                                        <span class="badge bg-{{ $payBadge }} text-white px-12 py-6 rounded-pill text-xs">{{ $payLabel }}</span>
                                    </td>
                                    <td class="py-16 px-24">
                                        <span class="badge bg-{{ \App\Enums\OrderStatus::badge($order->order_status) }} text-white px-12 py-6 rounded-pill text-xs">{{ \App\Enums\OrderStatus::label($order->order_status) }}</span>
                                    </td>
                                    <td class="py-16 px-24 text-end">
                                        <a href="{{ route('website.orders.show', $order->order_id) }}" class="btn btn-outline-main text-sm py-8 px-16 rounded-pill hover-bg-main-600 hover-text-white transition-2">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="text-center py-40">
                        <div class="mb-24">
                            <i class="ph ph-shopping-bag text-6xl text-gray-300"></i>
                        </div>
                        <h5 class="text-heading mb-16">No orders yet</h5>
                        <p class="text-gray-500 mb-24">You haven't placed any orders yet. Start shopping now!</p>
                        <a href="{{ route('website.shop') }}" class="btn btn-main rounded-pill py-12 px-32">
                            Go to Shop
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection