@extends('website.layouts.app')

@section('title', 'Study Nest - Thank You')

@section('content')
    <section class="thank-you py-80">
        <div class="container container-lg">
            <div class="text-center mb-40">
                <div class="mb-32">
                    <span class="w-80 h-80 bg-main-50 text-main-600 rounded-circle flex-center text-4xl mx-auto">
                        <i class="ph ph-check"></i>
                    </span>
                </div>
                <h3 class="mb-16">Thank You for Your Order!</h3>
                @if(isset($order))
                    @php $statusLabel = \App\Enums\OrderStatus::label($order->order_status); @endphp
                    @if($order->order_status == \App\Enums\OrderStatus::CONFIRMED)
                        <p class="text-gray-500 mb-32">Your order has been confirmed. We will dispatch it soon.</p>
                    @elseif($order->order_status == \App\Enums\OrderStatus::CANCELLED)
                        <p class="text-danger mb-32">Your order has been cancelled.</p>
                    @elseif($order->order_status == \App\Enums\OrderStatus::DELIVERED)
                        <p class="text-gray-500 mb-32">Your order has been delivered. Thank you for shopping with us!</p>
                    @else
                        <p class="text-gray-500 mb-32">Your order has been placed successfully. We will contact you shortly for confirmation.</p>
                    @endif
                @else
                    <p class="text-gray-500 mb-32">Your order has been placed successfully. We will contact you shortly for confirmation.</p>
                @endif
                
                @if(isset($order))
                    <div class="d-inline-block text-start border border-gray-100 rounded-8 p-24 w-100 max-w-600 mx-auto">
                        <h5 class="mb-24 border-bottom border-gray-100 pb-16">Order Summary</h5>
                        
                        <div class="flex-between gap-16 mb-16">
                            <span class="text-gray-500">Order Number</span>
                            <span class="text-gray-900 fw-semibold">#{{ $order->order_id }}</span>
                        </div>
                        <div class="flex-between gap-16 mb-16">
                            <span class="text-gray-500">Date</span>
                            <span class="text-gray-900 fw-semibold">{{ \Carbon\Carbon::parse($order->order_date_time)->format('M d, Y h:i A') }}</span>
                        </div>
                        @php
                            $orderCharges = is_array($order->order_charges) ? $order->order_charges : json_decode($order->order_charges, true);
                            $deliveryCharge = 10.00;
                            if (is_array($orderCharges)) {
                                foreach ($orderCharges as $charge) {
                                    if (strtolower($charge['charge_name'] ?? '') === 'delivery charge') {
                                        $deliveryCharge = (float)($charge['calculated_amount'] ?? $charge['charge_value'] ?? 10);
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <div class="flex-between gap-16 mb-16">
                            <span class="text-gray-500">Delivery Charge</span>
                            <span class="text-gray-900 fw-semibold">₹{{ number_format($deliveryCharge, 2) }}</span>
                        </div>
                        <div class="flex-between gap-16 mb-16">
                            <span class="text-gray-500">Total Amount</span>
                            <span class="text-gray-900 fw-semibold text-xl">₹{{ number_format($order->order_total_amt, 2) }}</span>
                        </div>
                        <div class="flex-between gap-16 mb-16">
                            <span class="text-gray-500">Payment Method</span>
                            <span class="text-gray-900 fw-semibold">
                                {{ $order->order_payment_mode == 1 ? 'Cash on Delivery' : 'Online Payment' }}
                            </span>
                        </div>

                        @php
                            $charges = is_array($order->order_charges) ? $order->order_charges : json_decode($order->order_charges, true);
                            $totalOtherCharges = 0;
                            $cgstAmount = 0;
                            $sgstAmount = 0;
                            if(is_array($charges)) {
                                foreach($charges as $charge) {
                                    $chargeName = strtolower($charge['charge_name'] ?? '');
                                    $amount = (float)($charge['calculated_amount'] ?? 0);
                                    if($chargeName === 'cgst') {
                                        $cgstAmount = $amount;
                                    } elseif($chargeName === 'sgst') {
                                        $sgstAmount = $amount;
                                    } else {
                                        $totalOtherCharges += $amount;
                                    }
                                }
                            }
                            $subTotal = $order->order_total_amt - $cgstAmount - $sgstAmount - $totalOtherCharges;
                            $hasGST = $cgstAmount > 0 || $sgstAmount > 0;
                        @endphp

                        @if($hasGST)
                        <div class="mt-16 pt-16 border-top border-gray-100">
                            <h6 class="mb-12">Price Breakdown</h6>
                            <div class="flex-between gap-16 mb-8">
                                <span class="text-gray-500 text-sm">Subtotal</span>
                                <span class="text-gray-900 fw-medium text-sm">₹{{ number_format($subTotal, 2) }}</span>
                            </div>
                            <div class="flex-between gap-16 mb-8">
                                <span class="text-gray-500 text-sm">CGST</span>
                                <span class="text-gray-900 fw-medium text-sm">₹{{ number_format($cgstAmount, 2) }}</span>
                            </div>
                            <div class="flex-between gap-16 mb-8">
                                <span class="text-gray-500 text-sm">SGST</span>
                                <span class="text-gray-900 fw-medium text-sm">₹{{ number_format($sgstAmount, 2) }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="mt-32">
                            <h6 class="mb-16">Items Ordered</h6>
                            @if(is_array($order->order_items))
                                @foreach($order->order_items as $item)
                                    <div class="flex-between gap-16 mb-12 pb-12 border-bottom border-gray-100 last-no-border">
                                        <div class="d-flex align-items-center gap-12">
                                            <div>
                                                <p class="text-gray-900 fw-medium line-clamp-1">{{ $item['product_name'] }}</p>
                                                @if(isset($item['variant_name']) && $item['variant_name'])
                                                    <span class="text-xs text-gray-500">Variant: {{ $item['variant_name'] }}</span>
                                                @endif
                                                <span class="text-xs text-gray-500 d-block">Qty: {{ $item['product_qty'] }}</span>
                                            </div>
                                        </div>
                                        <span class="text-gray-900 fw-medium">₹{{ number_format($item['price'] ?? $item['product_total'] ?? 0, 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-gray-500 text-sm">Item details not available.</p>
                            @endif
                        </div>

                        <div class="mt-32">
                            <h6 class="mb-16">Delivery Address</h6>
                            @php
                                $delivery = is_array($order->order_delivery_details) ? $order->order_delivery_details : json_decode($order->order_delivery_details, true);
                            @endphp
                            @if($delivery)
                                <p class="text-gray-500 text-sm mb-4">{{ $delivery['first_name'] }} {{ $delivery['last_name'] }}</p>
                                <p class="text-gray-500 text-sm mb-4">{{ $delivery['address'] }}</p>
                                @if(isset($delivery['apartment']) && $delivery['apartment'])
                                    <p class="text-gray-500 text-sm mb-4">{{ $delivery['apartment'] }}</p>
                                @endif
                                <p class="text-gray-500 text-sm mb-4">{{ $delivery['city'] }}, {{ $delivery['state'] }} - {{ $delivery['pincode'] ?? $delivery['zip'] ?? '' }}</p>
                                <p class="text-gray-500 text-sm mb-4">Phone: {{ $delivery['phone'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="mt-40">
                    <a href="{{ route('website.index') }}" class="btn btn-main rounded-8 py-16 px-32 fw-normal">Back to Home</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Meta Pixel: Purchase Event -->
    @if(isset($order))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof fbq !== 'undefined') {
                @php
                    $orderItems = is_array($order->order_items) ? $order->order_items : json_decode($order->order_items, true);
                    $contentIds = [];
                    $numItems = 0;
                    if (is_array($orderItems)) {
                        foreach ($orderItems as $oItem) {
                            $contentIds[] = (string)($oItem['product_id'] ?? '');
                            $numItems += (int)($oItem['product_qty'] ?? 1);
                        }
                    }
                @endphp
                fbq('track', 'Purchase', {
                    content_ids: @json($contentIds),
                    content_type: 'product',
                    value: {{ $order->order_total_amt }},
                    currency: 'INR',
                    num_items: {{ $numItems }}
                });
            }
        });
    </script>
    @endif
@endsection