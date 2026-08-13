@extends('website.layouts.app')

@section('title', 'Refund Policy')

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Refund Policy</h6>
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
                <li class="text-sm text-main-600"> Refund Policy </li>
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
            At STUDYNEST EDUCATIONAL, we strive to provide high-quality school products including books, uniforms, and educational kits. Please read our refund policy carefully before making a purchase.
        </p>

        <h6 style="padding-top:20px">1. Eligibility for Refund</h6>
        <p>Refunds are applicable only under the following conditions:</p>
        <ul>
            <li>Duplicate payment made by the customer</li>
            <li>Order not delivered within the committed time (due to our fault)</li>
            <li>Damaged or defective product received</li>
            <li>Incorrect product delivered</li>
        </ul>

        <h6 style="padding-top:20px">2. Non-Refundable Conditions</h6>
        <p>Refunds will NOT be applicable in the following cases:</p>
        <ul>
            <li>If the product has been used or damaged by the customer</li>
            <li>If the correct product was delivered as per order</li>
            <li>Change of mind after purchase</li>
            <li>Delay caused by courier partners or incorrect address provided</li>
        </ul>

        <h6 style="padding-top:20px">3. Replacement Policy</h6>
        <ul>
            <li>In case of damaged or wrong product, we will offer replacement instead of refund (if applicable)</li>
            <li>Customer must report the issue within 48 hours of delivery with proof (images/video)</li>
        </ul>

        <h6 style="padding-top:20px">4. Refund Process</h6>
        <ul>
            <li>Approved refunds will be processed within 7–10 working days</li>
            <li>Refund will be credited to the original payment method</li>
        </ul>

        <h6 style="padding-top:20px">5. Cancellation Link</h6>
        <p>
            Refunds related to cancellations will be processed as per our Cancellation Policy.
        </p>

        <h6 style="padding-top:20px">6. Contact for Refund</h6>
        <p>To request a refund, contact us with your order details:</p>
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
