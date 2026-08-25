@extends('website.layouts.app')

@section('title', 'Terms & Conditions')

@section('content')

<!-- ========================= Breadcrumb Start =============================== -->
<div class="breadcrumb mb-0 py-26 bg-main-two-50">
    <div class="container">
        <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
            <h6 class="mb-0">Terms & Conditions</h6>
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
                <li class="text-sm text-main-600"> Terms & Conditions </li>
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
            Welcome to STUDYNEST EDUCATIONAL. By accessing and using our website (studynested.com), you agree to comply with and be bound by the following Terms & Conditions.
        </p>

        <h6 style="padding-top:20px">1. Business Information</h6>
        <p>
            STUDYNEST EDUCATIONAL is a proprietorship business operated by Arti Jha, based in Bhopal, Madhya Pradesh, India.
        </p>

        <h6 style="padding-top:20px">2. Products & Services</h6>
        <p>
            We provide school-related products including books, uniforms, stationery kits, and other educational materials. All products are sourced and delivered as per school requirements and availability.
        </p>

        <h6 style="padding-top:20px">3. User Responsibility</h6>
        <p>
            Users must provide accurate and complete information while placing orders, including name, contact details, and delivery address.<br>
            We are not responsible for delays or issues caused by incorrect information.
        </p>

        <h6 style="padding-top:20px">4. Pricing & Payments</h6>
        <ul>
            <li>All prices are listed in INR (₹) and are inclusive/exclusive of applicable taxes (as mentioned).</li>
            <li>Payments are processed securely via trusted third-party payment gateways.</li>
            <li>We do not store any card details or payment credentials on our servers.</li>
        </ul>

        <h6 style="padding-top:20px">5. Order Confirmation</h6>
        <ul>
            <li>Once an order is placed, users will receive confirmation via email or phone.</li>
            <li>We reserve the right to accept or reject any order due to availability or other reasons.</li>
        </ul>

        <h6 style="padding-top:20px">6. Shipping & Delivery</h6>
        <ul>
            <li>Orders are delivered to the address provided by the user.</li>
            <li>Delivery timelines may vary based on location, product availability, and external factors.</li>
            <li>Delays caused by courier partners or unforeseen circumstances are not under our direct control.</li>
        </ul>

        <h6 style="padding-top:20px">7. Cancellation & Refund</h6>
        <p>
            All cancellations and refunds are governed by our separate Cancellation Policy and Refund Policy available on the website.
        </p>

        <h6 style="padding-top:20px">8. Intellectual Property</h6>
        <p>
            All content on this website (text, images, logo, design) is the property of STUDYNEST EDUCATIONAL and is protected under applicable laws.<br>
            Unauthorized use or reproduction is strictly prohibited.
        </p>

        <h6 style="padding-top:20px">9. Limitation of Liability</h6>
        <p>
            We are not liable for any indirect, incidental, or consequential damages arising from the use of our website or services.
        </p>

        <h6 style="padding-top:20px">10. Changes to Terms</h6>
        <p>
            We reserve the right to update or modify these Terms & Conditions at any time without prior notice.
        </p>

        <h6 style="padding-top:20px">11. Governing Law</h6>
        <p>
            These Terms shall be governed by and interpreted in accordance with the laws of India. Any disputes shall be subject to the jurisdiction of Bhopal, Madhya Pradesh.
        </p>

        <h6 style="padding-top:20px">12. Contact Information</h6>
        <p>For any queries, please contact us:</p>
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
