@extends('website.layouts.app')

@section('title', 'Order Details #' . $order->order_id . ' - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Order Details</h6>
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
                    <li class="text-sm">
                        <a href="{{ route('website.orders.index') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                            My Orders
                        </a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm text-main-600"> #{{ $order->order_id }} </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <section class="py-24">
        <div class="container">

            @if($order->order_status == \App\Enums\OrderStatus::CANCELLED)
                <div class="alert alert-danger d-flex align-items-center gap-12 mb-24 rounded-8" role="alert">
                    <i class="ph ph-x-circle text-2xl"></i>
                    <div>
                        <strong>Order Cancelled</strong> — This order has been cancelled. If you have any questions, please contact our support team.
                    </div>
                </div>
            @elseif($order->order_status == \App\Enums\OrderStatus::CONFIRMED)
                <div class="alert alert-primary d-flex align-items-center gap-12 mb-24 rounded-8" role="alert">
                    <i class="ph ph-check-circle text-2xl"></i>
                    <div>
                        <strong>Order Confirmed</strong> — Your order has been confirmed and is being processed.
                    </div>
                </div>
            @endif

            <div class="flex-between flex-wrap gap-16 mb-24">
                <h5 class="mb-0">Order #{{ $order->order_id }}</h5>
                <div class="flex-align gap-16">
                    <span class="badge bg-{{ \App\Enums\OrderStatus::badge($order->order_status) }} text-white px-16 py-8 rounded-pill text-sm">{{ \App\Enums\OrderStatus::label($order->order_status) }}</span>

                    <button type="button" class="btn btn-outline-primary rounded-pill py-10 px-24 text-sm flex-align gap-8" onclick="trackOrder({{ $order->order_id }})">
                        <i class="ph ph-map-pin"></i> Track Order
                    </button>

                    @if(in_array($order->order_status, [\App\Enums\OrderStatus::PENDING, \App\Enums\OrderStatus::CONFIRMED]))
                        <form action="{{ route('website.orders.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                            @csrf
                            <button type="submit" class="btn btn-danger rounded-pill py-10 px-24 text-sm flex-align gap-8">
                                <i class="ph ph-x-circle"></i> Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-lg-8">
                    <!-- Items -->
                    <div class="border border-gray-100 rounded-8 px-24 py-24 bg-white mb-24">
                        <h6 class="mb-24 border-bottom border-gray-100 pb-16">Order Items</h6>
                        @foreach($order->order_items as $item)
                            <div class="d-flex align-items-center gap-16 mb-24 last-mb-0">
                                <div class="flex-shrink-0">
                                    <img src="{{ $item['product_image'] ?? asset('assets/images/placeholder.png') }}" alt="{{ $item['product_name'] }}" class="rounded-8 border border-gray-100" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-md fw-semibold mb-4">{{ $item['product_name'] }}</h6>
                                    @if(!empty($item['variant_name']))
                                        <p class="text-sm text-gray-500 mb-4">Variant: {{ $item['variant_name'] }}</p>
                                    @endif
                                    @if(!empty($item['customization_option']) && $item['customization_option'] !== 'normal')
                                        <div class="mb-8">
                                            <span class="badge bg-info text-dark text-xs">Customization</span>
                                            <span class="text-sm text-gray-700 ms-8">
                                                {{ ucfirst($item['customization_option']) }} — ₹{{ number_format($item['customization_price'] ?? 0, 2) }}
                                            </span>
                                            @if(!empty($item['customization_text']))
                                                <div class="text-xs text-gray-600 mt-4">Text: {{ $item['customization_text'] }}</div>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="flex-between">
                                        <p class="text-sm text-gray-600">
                                            Qty: <span class="fw-semibold text-heading">{{ $item['product_qty'] }}</span> x ₹{{ number_format($item['product_rate'] ?? 0, 2) }}
                                        </p>
                                        <p class="text-md fw-semibold text-main-600">₹{{ number_format($item['price'] ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Payment & Delivery Info -->
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="border border-gray-100 rounded-8 px-24 py-24 bg-white h-100">
                                <h6 class="mb-16 border-bottom border-gray-100 pb-12">Delivery Address</h6>
                                @php
                                    $details = is_string($order->order_delivery_details) ? json_decode($order->order_delivery_details, true) : $order->order_delivery_details;
                                @endphp
                                <p class="text-heading fw-semibold mb-4">{{ $details['first_name'] ?? '' }} {{ $details['last_name'] ?? '' }}</p>
                                <p class="text-gray-600 text-sm mb-4">{{ $details['address'] ?? '' }}</p>
                                <p class="text-gray-600 text-sm mb-4">{{ $details['city'] ?? '' }}, {{ $details['state'] ?? '' }} - {{ $details['pincode'] ?? ($details['zip'] ?? '') }}</p>
                                <p class="text-gray-600 text-sm mb-0">Phone: {{ $details['phone'] ?? '' }}</p>
                                @if(!empty($order->order_gst_number))
                                <div class="mt-16 pt-12 border-top border-gray-100">
                                    <p class="text-sm text-heading fw-medium mb-4">GST Number:</p>
                                    <p class="text-main-600 text-sm fw-semibold">{{ $order->order_gst_number }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="border border-gray-100 rounded-8 px-24 py-24 bg-white h-100">
                                <h6 class="mb-16 border-bottom border-gray-100 pb-12">Payment Info</h6>
                                <div class="flex-between mb-8">
                                    <span class="text-gray-600">Payment Method:</span>
                                    <span class="text-heading fw-medium">{{ $order->order_payment_mode == 1 ? 'COD' : 'Online' }}</span>
                                </div>
                                <div class="flex-between mb-8">
                                    <span class="text-gray-600">Payment Status:</span>
                                    @php
                                        // START: imohitmehto | 2026-08-25 | FIX: Payment status labels now match actual DB values
                                        // 0=Failed, 1=Paid, 2=Pending
                                        $paymentStatusMap = [
                                            1 => ['Paid', 'success'],
                                            2 => ['Pending', 'warning'],
                                            0 => ['Failed', 'danger'],
                                        ];
                                        [$payLabel, $payBadge] = $paymentStatusMap[$order->order_payment_status] ?? ['Unknown', 'secondary'];
                                        // END: imohitmehto | FIX: Payment status label mapping
                                    @endphp
                                    <span class="badge bg-{{ $payBadge }} text-white px-10 py-4 rounded-pill text-xs">{{ $payLabel }}</span>
                                </div>
                                <div class="flex-between mb-0">
                                    <span class="text-gray-600">Order Date:</span>
                                    <span class="text-heading fw-medium">{{ date('d M Y, h:i A', strtotime($order->order_date_time)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Order Summary -->
                     <div class="border border-gray-100 rounded-8 px-24 py-24 bg-white">
                        <h6 class="mb-24 border-bottom border-gray-100 pb-16">Order Summary</h6>
                        
                        @php
                            // START: imohitmehto | 2026-08-25 | FIX: Subtotal calculated from stored item data (product_rate * qty)
                            // Previously used $item['price'] which was the GST-inclusive total, not the base subtotal
                            $subTotal = collect($order->order_items)->sum(function ($item) {
                                return $item['product_rate'] * $item['product_qty'];
                            });
                            // END: imohitmehto | FIX: Customer order subtotal accuracy
                        @endphp

                        <div class="flex-between mb-12">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-heading fw-medium">₹{{ number_format($subTotal, 2) }}</span>
                        </div>

                        @if(!empty($order->order_charges))
                            @foreach($order->order_charges as $charge)
                                <div class="flex-between mb-12">
                                    <span class="text-gray-600">{{ $charge['charge_name'] }}</span>
                                    <span class="text-heading fw-medium">+ ₹{{ number_format($charge['calculated_amount'] ?? 0, 2) }}</span>
                                </div>
                            @endforeach
                        @endif

                        <div class="border-top border-gray-100 pt-16 mt-16 flex-between">
                            <span class="text-lg fw-semibold text-heading">Grand Total</span>
                            <span class="text-lg fw-semibold text-main-600">₹{{ number_format($order->order_total_amt, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script>
        function trackOrder(orderId) {
            fetch('{{ route("website.order.track") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const info = data.data;
                    alert(`Order #${info.order_id}\n\nStatus: ${info.order_status}\nPayment: ${info.payment_status}\nOrdered: ${info.order_date}\nLast Updated: ${info.updated_at}\nItems: ${info.items_qty}`);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Failed to track order. Please try again.');
                console.error('Error:', error);
            });
        }
    </script>

@endsection
