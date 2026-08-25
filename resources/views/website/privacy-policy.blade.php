@extends('website.layouts.app')

@section('title', 'Privacy Policy')

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Privacy Policy</h6>
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
                <li class="text-sm text-main-600"> Privacy Policy </li>
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
            STUDYNEST EDUCATIONAL ("we", "our", "us") operates the website studynested.com. 
            We are committed to protecting your privacy and ensuring that your personal information 
            is handled in a safe and responsible manner.
        </p>

        <h6 style="padding-top:20px">1. Information We Collect</h6>
        <p>We may collect the following information from users:</p>
        <ul>
            <li>Name</li>
            <li>Phone Number</li>
            <li>Email Address</li>
            <li>Shipping Address</li>
            <li>Payment Information</li>
        </ul>

        <h6 style="padding-top:20px">2. How We Use Your Information</h6>
        <p>We use your information to:</p>
        <ul>
            <li>Process and deliver orders (books, uniforms, school kits)</li>
            <li>Provide customer support</li>
            <li>Improve our services and user experience</li>
            <li>Send order updates and important notifications</li>
        </ul>

        <h6 style="padding-top:20px">3. Payment Security</h6>
        <p>
            All payments are processed securely through trusted third-party payment gateways.
            We do not store any card details, CVV, or net banking credentials on our servers.
        </p>

        <h6 style="padding-top:20px">4. Data Protection</h6>
        <p>
            We implement appropriate security measures to protect your personal data from 
            unauthorized access, misuse, or disclosure.
        </p>

        <h6 style="padding-top:20px">5. Cookies</h6>
        <p>
            Our website may use cookies to enhance user experience and improve website functionality.
        </p>

        <h6 style="padding-top:20px">6. Sharing of Information</h6>
        <p>We do not sell, rent, or trade your personal information.</p>
        <p>Information may be shared only with:</p>
        <ul>
            <li>Delivery partners for order fulfillment</li>
            <li>Payment gateways for secure transactions</li>
        </ul>

        <h6 style="padding-top:20px">7. User Consent</h6>
        <p>
            By using our website, you consent to the collection and use of your information 
            as outlined in this policy.
        </p>

        <h6 style="padding-top:20px">8. Contact Us</h6>
        <p>If you have any questions regarding this Privacy Policy, you can contact us:</p>
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
