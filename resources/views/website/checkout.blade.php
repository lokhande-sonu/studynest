@extends('website.layouts.app')

@section('title', 'Study Nest - Checkout')

@section('content')
    <div class="breadcrumb py-26 bg-main-two-50">
        <div class="container container-lg">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Checkout</h6>
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
                    <li class="text-sm text-main-600"> Checkout </li>
                </ul>
            </div>
        </div>
    </div>

    <section class="checkout py-80">
        <div class="container container-lg">
            @if(session('error'))
                <div class="alert alert-danger mb-24">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-24">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('website.checkout.place-order') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xl-9 col-lg-8 order-lg-1">
                        <div class="pe-xl-5">
                            <div class="border border-gray-100 rounded-8 px-40 py-40 mb-24">
                                <h5 class="mb-24">Billing Details</h5>
                                <div class="row gy-4">
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">First Name <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200" placeholder="First Name" name="first_name" value="{{ old('first_name') }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">Last Name <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-heading-two text-sm fw-semibold">Email Address <span class="text-danger-600">*</span></label>
                                        <input type="email" class="common-input border-gray-200" placeholder="Email Address" name="email" value="{{ old('email', auth()->user()->cust_email ?? '') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-heading-two text-sm fw-semibold">Phone Number <span class="text-danger-600">*</span></label>
                                        <input type="number" class="common-input border-gray-200" placeholder="Phone Number" name="phone" value="{{ old('phone', auth()->user()->cust_mobile ?? '') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-heading-two text-sm fw-semibold">Address <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200 mb-16" placeholder="House number and street name" name="address" value="{{ old('address') }}" required>
                                        <input type="text" class="common-input border-gray-200" placeholder="Apartment, suite, unit etc. (optional)" name="apartment" value="{{ old('apartment') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">City <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200" placeholder="City" name="city" value="{{ old('city') }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">State <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200" placeholder="State" name="state" value="{{ old('state') }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">Pincode <span class="text-danger-600">*</span></label>
                                        <input type="text" class="common-input border-gray-200" placeholder="Pincode" name="pincode" value="{{ old('pincode') }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-heading-two text-sm fw-semibold">GST Number (Optional)</label>
                                        <input type="text" class="common-input border-gray-200" placeholder="Enter GSTIN (e.g., 27AAPFU0939F1ZV)" name="gst_number" value="{{ old('gst_number') }}" maxlength="15">
                                        <small class="text-gray-500">Required for GST invoice</small>
                                    </div>
                                </div>
                            </div>

                            <div class="border border-gray-100 rounded-8 px-40 py-40">
                                <h5 class="mb-24">Payment Method</h5>
                                <div class="row gy-4">
                                    <div class="col-12">
                                        <div class="form-check common-check py-16 px-20 border border-gray-100 rounded-8 mb-16">
                                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-heading-two" for="cod">
                                                Cash on Delivery (COD)
                                            </label>
                                        </div>
                                        <div class="form-check common-check py-16 px-20 border border-gray-100 rounded-8">
                                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online" {{ old('payment_method') == 'online' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-heading-two" for="online">
                                                Online Payment (UPI, Cards, Net Banking)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 order-lg-2">
                        <div class="checkout-sidebar border border-gray-100 rounded-8 px-24 py-40">
                            <h6 class="mb-24">Your Order</h6>
                            <div class="bg-color-three rounded-8 p-24 mb-24">
                                <div class="mb-32 flex-between gap-8">
                                    <span class="text-gray-900 font-heading-two">Product</span>
                                    <span class="text-gray-900 font-heading-two">Subtotal</span>
                                </div>
                                
                                @foreach($cartItems as $item)
                                    <div class="mb-16 flex-between gap-8">
                                        <span class="text-gray-500 text-sm">
                                            {{ $item->product->p_name }}
                                            @if($item->variant)
                                                ({{ $item->variant->prod_variant }})
                                            @endif
                                            x {{ $item->quantity }}
                                        </span>
                                        <span class="text-gray-900 fw-semibold text-sm">₹{{ number_format($item->line_total, 2) }}</span>
                                    </div>
                                @endforeach

                                <div class="border-top border-gray-100 my-24"></div>
                                <div class="mb-32 flex-between gap-8">
                                    <span class="text-gray-900 font-heading-two">Subtotal (incl. GST)</span>
                                    <span class="text-gray-900 fw-semibold">₹{{ number_format($subTotal, 2) }}</span>
                                </div>

                                @foreach($calculatedCharges as $charge)
                                    @if(!empty($charge->charge_name))
                                        <div class="mb-16 flex-between gap-8">
                                            <span class="text-gray-500 text-sm">
                                                {{ $charge->charge_name }}
                                                @if($charge->gst_rate > 0)
                                                    (incl. {{ number_format($charge->gst_rate, 0) }}% GST)
                                                @endif
                                            </span>
                                            <span class="text-gray-900 fw-semibold text-sm">
                                                @if(isset($charge->applied) && !$charge->applied)
                                                    Free
                                                @else
                                                    ₹{{ number_format($charge->calculated_amount, 2) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="border-top border-gray-100 my-24"></div>
                                <div class="flex-between gap-8">
                                    <span class="text-gray-900 font-heading-two text-lg fw-bold">Total</span>
                                    <span class="text-gray-900 fw-bold text-lg">₹{{ number_format($grandTotal, 2) }}</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-main w-100 rounded-8 py-18 fw-normal">Place Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Meta Pixel: InitiateCheckout Event -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof fbq !== 'undefined') {
                fbq('track', 'InitiateCheckout', {
                    content_ids: [
                        @foreach($cartItems as $item)
                            '{{ $item->product_id }}'{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    content_type: 'product',
                    num_items: {{ $cartItems->sum('quantity') }},
                    value: {{ $grandTotal }},
                    currency: 'INR'
                });
            }
        });
    </script>
@endsection
