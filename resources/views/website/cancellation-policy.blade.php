@extends('website.layouts.app')

@section('title', 'Cancellation Policy')

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Cancellation Policy</h6>
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
                <li class="text-sm text-main-600"> Cancellation Policy </li>
            </ul>
        </div>
    </div>
</div>
<!-- ========================= Breadcrumb End =============================== -->

<div class="mb-0 py-26">
    <div class="container">
        <p><strong>Effective Date: 01-04-2026</strong></p>
        <br/>
        <p>
            At STUDYNEST EDUCATIONAL, we understand that sometimes customers may need to cancel their orders. Please read our cancellation policy carefully.
        </p>

        <h6 style="padding-top:20px">1. Order Cancellation by Customer</h6>
        <ul>
            <li>Orders can be cancelled within 24 hours of placing the order.</li>
            <li>To request cancellation, customers must contact us via phone or email with order details.</li>
        </ul>

        <h6 style="padding-top:20px">2. Cancellation After Processing</h6>
        <ul>
            <li>Once the order has been processed, packed, or shipped, cancellation requests may not be accepted.</li>
            <li>In such cases, customers may refer to our Refund Policy.</li>
        </ul>

        <h6 style="padding-top:20px">3. Cancellation by STUDYNEST EDUCATIONAL</h6>
        <p>We reserve the right to cancel any order under the following circumstances:</p>
        <ul>
            <li>Product out of stock</li>
            <li>Incorrect pricing or product information</li>
            <li>Payment issues or suspected fraudulent transactions</li>
            <li>Any unforeseen operational issue</li>
        </ul>

        <h6 style="padding-top:20px">4. Refund on Cancellation</h6>
        <ul>
            <li>If cancellation is approved, the refund will be processed as per our Refund Policy.</li>
            <li>Refunds will be credited to the original payment method within 7–10 working days.</li>
        </ul>

        <h6 style="padding-top:20px">5. Contact for Cancellation</h6>
        <p>For cancellation requests, contact us:</p>
        <br>
        <p><strong>STUDYNEST EDUCATIONAL</strong></p>
        <p><strong>Proprietor:</strong> Arti Jha</p>
        <p><strong>Address:</strong> F.No-206, Block No. B, Sagar Golden Palms, Katara Hills Barai Road, Near St. Francis Co-Ed School, Bhopal, Madhya Pradesh – 462043</p>
        <p><strong>Email:</strong> colloborate@studynested.com</p>
        <p><strong>Phone:</strong> +91 7389942021</p>

        <p style="padding-top:20px">© 2026 STUDYNEST EDUCATIONAL. All Rights Reserved.</p>
    </div>
</div>

@endsection
