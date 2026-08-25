@extends('website.layouts.app')

@section('title', 'Disclaimer')

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="pt-2" class="mb-0">Disclaimer</h6 class="pt-2">
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
                <li class="text-sm text-main-600"> Disclaimer </li>
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
            The information provided by STUDYNEST EDUCATIONAL on studynested.com is for general informational and educational purposes only.
        </p>

        <h6 style="padding-top:20px">1. Product Accuracy</h6>
        <p>
            We strive to ensure that all product details (books, uniforms, school kits) are accurate and updated as per school requirements. However, we do not guarantee complete accuracy or completeness at all times.
        </p>

        <h6 style="padding-top:20px">2. No Guarantee of Results</h6>
        <p>
            We do not guarantee any specific academic results or outcomes from the use of our products.
        </p>

        <h6 style="padding-top:20px">3. Third-Party Services</h6>
        <p>
            Our website may use third-party services such as payment gateways and delivery partners.<br>
            We are not responsible for any issues caused by these third-party services.
        </p>

        <h6 style="padding-top:20px">4. Limitation of Liability</h6>
        <p>
            STUDYNEST EDUCATIONAL shall not be held liable for any direct, indirect, or incidental damages arising from the use of our website, products, or services.
        </p>

        <h6 style="padding-top:20px">5. User Responsibility</h6>
        <p>
            Users are responsible for verifying product details and ensuring correct order placement before making a purchase.
        </p>

        <h6 style="padding-top:20px">6. Changes to Disclaimer</h6>
        <p>
            We reserve the right to update or change this Disclaimer at any time without prior notice.
        </p>

        <h6 style="padding-top:20px">7. Contact Information</h6>
        <p>For any queries, contact us:</p>
        <br>
        <p><strong>STUDYNEST EDUCATIONAL</strong></p>
        <p><strong>Proprietor:</strong> Arti Jha</p>
        <p><strong>Address:</strong> F.No-206, Block No. B Sagar Golden Palms, Katara Hills Barai Road, Near St. Francis Co-Ed School, Bhopal, Madhya Pradesh – 462043</p>
        <p><strong>Email:</strong> colloborate@studynested.com</p>
        <p><strong>Phone:</strong> +91 7389942021</p>

        <p style="padding-top:20px">© 2026 STUDYNEST EDUCATIONAL. All Rights Reserved.</p>
    </div>
</div>

@endsection
