<section class="shipping bg-black mt-20">
        <div class="container-lg">
            <div class="row gy-4">
                <div class="col-xxl-3 col-sm-6 aos-init aos-animate" data-aos="zoom-in" data-aos-duration="400">
                    <div
                        class="shipping-item flex-align gap-16 rounded-16 border border-white-13 border-dashed  transition-2">
                        <span
                            class="w-56 h-56 flex-center rounded-circle bg-main-800 text-dark text-32 flex-shrink-0"><i
                                class="ph-fill ph-truck"></i></span>
                        <div class="">
                            <h6 class="mb-0 text-xl fw-semibold text-white">Free Delivery On School Kits</h6>
                            <span class="text-sm text-neutral-300 d-block mt-10">Doorstep delivery available for selected locations and schools.</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6 aos-init aos-animate" data-aos="zoom-in" data-aos-duration="600">
                    <div
                        class="shipping-item flex-align gap-16 rounded-16 border border-white-13 border-dashed  transition-2">
                        <span
                            class="w-56 h-56 flex-center rounded-circle bg-main-800 text-dark text-32 flex-shrink-0"><i
                                class="ph-fill ph-hand-heart"></i></span>
                        <div class="">
                            <h6 class="mb-0 text-xl fw-semibold text-white"> 100% Accuracy Promise</h6>
                            <span class="text-sm text-neutral-300 d-block mt-10">School-verified kits. No extras. No misses.</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6 aos-init aos-animate" data-aos="zoom-in" data-aos-duration="800">
                    <div
                        class="shipping-item flex-align gap-16 rounded-16 border border-white-13 border-dashed  transition-2">
                        <span
                            class="w-56 h-56 flex-center rounded-circle bg-main-800 text-dark text-32 flex-shrink-0"><i
                                class="ph-fill ph-credit-card"></i></span>
                        <div class="">
                            <h6 class="mb-0 text-xl fw-semibold text-white"> Secure Payments</h6>
                            <span class="text-sm text-neutral-300 d-block mt-10">Safe & Trusted Payment Options UPI, Cards, Net Banking & more.</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-sm-6 aos-init aos-animate" data-aos="zoom-in" data-aos-duration="1000">
                    <div
                        class="shipping-item flex-align gap-16 rounded-16 border border-white-13 border-dashed transition-2">
                        <span
                            class="w-56 h-56 flex-center rounded-circle bg-main-800 text-dark text-32 flex-shrink-0"><i
                                class="ph-fill ph-chats"></i></span>
                        <div class="">
                            <h6 class="mb-0 text-xl fw-semibold text-white"> Dedicated Support</h6>
                            <span class="text-sm text-neutral-300 d-block mt-10">Dedicated parent support from order to delivery.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- ==================== Footer Start Here ==================== -->
<footer class="footer py-40 overflow-hidden">
    <div class="container">
        <div class="justify-content-between d-flex align-items-start flex-wrap">
            <div class="footer-item max-w-275 aos-init aos-animate" data-aos="fade-up" data-aos-duration="200">
                <div class="footer-item__logo">
                    <a href="{{ url('/') }}"> <img src="{{ asset('customer-web-assets/images/logo/logo.png') }}"
                            alt=""></a>
                </div>
                <p class="mb-24"><strong>Studynest simplifies school shopping for parents.</strong> <br/>
                We provide school-verified books, stationery kits, uniforms, and essential
                supplies delivered accurately and on time, so families can prepare for school without
                stress.</p>
                
            </div>

            <div class="footer-item aos-init aos-animate" data-aos="fade-up" data-aos-duration="400">
                <h6 class="footer-item__title">Quick Link</h6>
                <ul class="footer-menu">
                    <li class="mb-16">
                        <a href="{{ route('website.about-us') }}" class="text-gray-600 hover-text-main-600">About Us</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.blogs') }}" class="text-gray-600 hover-text-main-600">Blogs</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.contact-us') }}" class="text-gray-600 hover-text-main-600">Contact Us</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.terms-conditions') }}" class="text-gray-600 hover-text-main-600">Terms &
                            Conditions</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.privacy-policy') }}" class="text-gray-600 hover-text-main-600">Privacy Policy</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.refund-policy') }}" class="text-gray-600 hover-text-main-600">Refund Policy</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.cancellation-policy') }}" class="text-gray-600 hover-text-main-600">Cancellation Policy</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.disclaimer') }}" class="text-gray-600 hover-text-main-600">Disclaimer</a>
                    </li>
                     <li class="mb-16">
                        <a href="{{ route('website.prebooking') }}" class="text-gray-600 hover-text-main-600">PreBooking </a>
                    </li>

                </ul>
            </div>

            <div class="footer-item aos-init aos-animate" data-aos="fade-up" data-aos-duration="600">
                <h6 class="footer-item__title">Quick Shop</h6>
                @php
                    $footerCategories = \App\Models\ProductCategory::where('cat_status', 1)->get();
                    $booksCat = $footerCategories->firstWhere('cat_name', 'Books');
                    $uniformsCat = $footerCategories->firstWhere('cat_name', 'Uniforms');
                    
                    $otherCat = $footerCategories->firstWhere('cat_name', 'Accessories');
                @endphp
                <ul class="footer-menu">
                    <li class="mb-16">
                        <a href="{{ route('website.shop', ['category' => $booksCat?->cat_id ?? -1]) }}" class="text-gray-600 hover-text-main-600">Books</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.shop', ['category' => $uniformsCat?->cat_id ?? -1]) }}" class="text-gray-600 hover-text-main-600">Uniforms</a>
                    </li>
                    <li class="mb-16">
                        <a href="{{ route('website.shop', ['category' => $otherCat?->cat_id ?? -1]) }}" class="text-gray-600 hover-text-main-600">Other Materials</a>
                    </li>

                </ul>
            </div>


            <div class="footer-item aos-init aos-animate" data-aos="fade-up" data-aos-duration="800">
                <h6 class="footer-item__title">Contact Info</h6>
                
                <div class="flex-align gap-16 mb-16">
                    <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0"><i class="ph-fill ph-phone-call"></i></span>
                    <a href="tel:{{ $contactInfo->mobile ?? '+91 7389942021' }}" class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->mobile ?? '+91 73899 42021' }}</a>
                </div>

                <div class="flex-align gap-16 mb-16">
                    <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0"><i class="ph-fill ph-whatsapp-logo"></i></span>
                    <a href="https://wa.me/{{ $contactInfo->whatsapp ?? '917389087022' }}" class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->whatsapp ?? '+91 73890 87022' }}</a>
                </div>
                
                <div class="flex-align gap-16 mb-16">
                    <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0"><i class="ph-fill ph-envelope"></i></span>
                    <a href="mailto:{{ $contactInfo->email ?? 'colloborate@studynested.com' }}" class="text-md text-gray-900 hover-text-main-600">{{ $contactInfo->email ?? 'colloborate@studynested.com' }}</a>
                </div>

                <div class="flex-start gap-16 mb-16 d-flex align-items-start">
                    <span class="w-32 h-32 flex-center rounded-circle border border-gray-100 text-main-two-600 text-md flex-shrink-0 mt-4"><i class="ph-fill ph-map-pin"></i></span>
                    @php
                        $rawAddr = $contactInfo->address ?? 'F.No-206, Block No. B, Sagar Golden Palms, Katara Hills Barai Road, Near St. Francis Co-Ed School, Bhopal, Madhya Pradesh - 462043';
                    @endphp
                    <a href="https://maps.google.com/?q={{ urlencode($rawAddr) }}" target="_blank" class="text-md text-gray-900 hover-text-main-600 lh-base">
                        F.No-206, Block No. B, Sagar Golden Palms,<br>
                        Katara Hills Barai Road, Near St. Francis Co-Ed School,<br>
                        Bhopal, Madhya Pradesh – 462043
                    </a>
                </div>

                <ul class="flex-align gap-16 mt-20">
                    @if(isset($contactInfo->facebook_url) && $contactInfo->facebook_url)
                    <li>
                        <a href="{{ $contactInfo->facebook_url }}" target="_blank"
                            class="w-44 h-44 flex-center bg-main-two-50 text-main-two-600 text-xl rounded-8 hover-bg-main-two-600 hover-text-white">
                            <i class="ph-fill ph-facebook-logo"></i>
                        </a>
                    </li>
                    @endif
                    @if(isset($contactInfo->twitter_url) && $contactInfo->twitter_url)
                    <li>
                        <a href="{{ $contactInfo->twitter_url }}" target="_blank"
                            class="w-44 h-44 flex-center bg-main-two-50 text-main-two-600 text-xl rounded-8 hover-bg-main-two-600 hover-text-white">
                            <i class="ph-fill ph-twitter-logo"></i>
                        </a>
                    </li>
                    @endif
                    @if(isset($contactInfo->instagram_url) && $contactInfo->instagram_url)
                    <li>
                        <a href="{{ $contactInfo->instagram_url }}" target="_blank"
                            class="w-44 h-44 flex-center bg-main-two-50 text-main-two-600 text-xl rounded-8 hover-bg-main-two-600 hover-text-white">
                            <i class="ph-fill ph-instagram-logo"></i>
                        </a>
                    </li>
                    @endif
                    @if(isset($contactInfo->youtube_url) && $contactInfo->youtube_url)
                    <li>
                        <a href="{{ $contactInfo->youtube_url }}" target="_blank"
                            class="w-44 h-44 flex-center bg-main-two-50 text-main-two-600 text-xl rounded-8 hover-bg-main-two-600 hover-text-white">
                            <i class="ph-fill ph-youtube-logo"></i>
                        </a>
                    </li>
                    @endif
                </ul>

            </div>
        </div>
        <div class="row mt-40">
            <div class="col-12 text-center border-top border-gray-100 pt-24 pb-24">
                <p class="text-sm text-gray-500">© 2026 STUDYNEST EDUCATIONAL. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>
<!-- ==================== Footer End Here ==================== -->
