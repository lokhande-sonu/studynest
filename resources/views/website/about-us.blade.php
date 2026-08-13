@extends('website.layouts.app')

@section('content')

<!-- ============================== Banner Inner Start ================================ -->
<section class="banner-inner bg-overlay z-1 position-relative py-72 bg-img bg-start"
    data-background-image="{{ asset('customer-web-assets/images/thumbs/banner-inner-bg1.png') }}">
    <div class="container">
        <div class="banner-inner__content text-center">
            <h4 class="text-white mb-20 fw-semibold">About Us</h4>
            <p class="text-white my-20">
                Every new school year brings excitement for children—but for parents, it often comes with stress.
            </p>
        </div>
    </div>
</section>
<!-- ============================== Banner Inner End ================================ -->

<!-- ============================= Steps Section start ================================= -->
<section class="step py-80">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-6">
                <div class="section-heading text-start">
                    <h4>About StudyNest</h4>
                    <span class="text-gray-600">
                        Managing long book lists, visiting multiple stores, facing out-of-stock issues, price confusion, and last-minute rush can make school preparation overwhelming. What should be a happy beginning often turns into a tiring experience.
                    </span>
                    <br/><br/>
                    <span class="text-gray-600">
                        At STUDYNEST EDUCATIONAL, we understand this deeply—because we’ve experienced it ourselves.
                    </span>
                    <br/><br/>
                    <span class="text-gray-600">
                        That’s why we built Studynest: to make school preparation simple, accurate, and completely stress-free for parents.
                    </span>
                    <br/><br/>
                    
                    <h4>Our Story</h4>
                    <span class="text-gray-600">
                        Studynest was created with one simple belief: parents deserve peace of mind.
                    </span>
                    <br/><br/>
                    <span class="text-gray-600">
                        We saw how school shopping had become unnecessarily complicated. Parents were spending days coordinating between different vendors, schools, and shops, often under time pressure and confusion. At the same time, schools were dealing with repeated queries and overcrowded processes.
                    </span>
                    <br/><br/>
                    <span class="text-gray-600">
                        Studynest was built to solve this problem by bringing everything together on one reliable and easy-to-use platform.
                    </span>
                    <br/><br/>
                </div>
            </div>

            <div class="col-lg-6 text-center">
                <img src="{{ asset('customer-web-assets/images/thumbs/steps.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
<!-- ============================= Steps Section End ================================= -->

<section class="counter">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <h5>What We Do</h5>
            <span class="text-gray-600">
                Studynest is a school-focused educational supply platform designed around real school requirements.
                <br/>
                We collaborate with schools to create dedicated pages for each institution. <br/>On these pages, parents can find exactly what their child needs—nothing more, nothing less.
                <br/><br/>
                Through Studynest, parents can easily order:
            </span>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-11">
                <div class="row gy-4 line-wrapper">
                    <div class="col-lg-1 col-sm-6"></div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="counter-item text-center py-40">
                            <h3 class="text-main-600 fw-semibold">1</h3>
                            <p class="fw-semibold">School-prescribed book sets</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="counter-item text-center py-40">
                            <h3 class="text-main-600 fw-semibold">2</h3>
                            <p class="fw-semibold">Essential stationery kits</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="counter-item text-center py-40">
                            <h3 class="text-main-600 fw-semibold">3</h3>
                            <p class="fw-semibold">Uniforms and accessories</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="counter-item text-center py-40">
                            <h3 class="text-main-600 fw-semibold">4</h3>
                            <p class="fw-semibold">Ready-to-use school kits</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6">
                        <div class="counter-item text-center py-40">
                            <h3 class="text-main-600 fw-semibold">5</h3>
                            <p class="fw-semibold">Optional old book exchange (where available)</p>
                        </div>
                    </div>
                    <div class="col-lg-1 col-sm-6"></div>
                </div>

                <p class="text-center text-gray-600 mt-30">
                    All orders are verified and delivered directly to parents’ doorsteps before the school session begins.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="shipping bg-black mt-20">
    <div class="container-lg">
        <div class="text-center mb-40 pt-40">
            <h4 class="text-white">Built for Parents, Aligned with Schools</h4>
            <p class="text-neutral-300 mt-10">
                Studynest follows a transparent and collaborative approach.
                We do not replace vendors or charge commissions to schools. <br/>
                Instead, we provide an optional solution that reduces pressure on schools while offering parents convenience, clarity, and reliability.
                <br/><br/>
                <span class="text-white"><strong>Our belief is simple:
                <i>when parents are stress-free, students start stronger.</i></strong></span>
            </p>
            <br/><br/>
            <h4 class="text-white">Our Promise</h4>
            <p class="text-neutral-300 mt-10">
                We are committed to making school preparation: <br/><br/>
                <strong>Simple &nbsp; | &nbsp; Accurate &nbsp; | &nbsp; Reliable &nbsp; | &nbsp; Stress-free</strong>
                <br/><br/>
                <span class="text-white"><strong>Every order is carefully verified to ensure parents receive exactly what is required—no confusion, no missing items.</strong></span>
            </p>
        </div>
    </div>
</section>

<!-- ========================= Why Become Seller section start =================================== -->
<section class="why-seller py-100">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <h5>Why Parents Choose Studynest</h5>
            <span class="text-gray-600">
                Parents trust Studynest because we focus on what truly matters:
            </span>
        </div>

        <div class="row gy-4 justify-content-center">
            <div class="col-lg-2 col-sm-6 text-center">
                <i class="ph ph-check text-72 text-main-two-600"></i>
                <p class="fw-semibold mt-20">Verified, school-approved lists</p>
            </div>
            <div class="col-lg-2 col-sm-6 text-center">
                <i class="ph ph-money text-72 text-main-two-600"></i>
                <p class="fw-semibold mt-20">Clear and transparent pricing</p>
            </div>
            <div class="col-lg-2 col-sm-6 text-center">
                <i class="ph ph-truck text-72 text-main-two-600"></i>
                <p class="fw-semibold mt-20">Doorstep delivery</p>
            </div>
            <div class="col-lg-2 col-sm-6 text-center">
                <i class="ph ph-clock text-72 text-main-two-600"></i>
                <p class="fw-semibold mt-20">Reduced last-minute hassle</p>
            </div>
            <div class="col-lg-2 col-sm-6 text-center">
                <i class="ph ph-headset text-72 text-main-two-600"></i>
                <p class="fw-semibold mt-20">Dedicated customer support</p>
            </div>
        </div>
        <p class="text-center text-gray-600 mt-30">
            With Studynest, parents save time, avoid confusion, and focus on what truly matters—their child.
        </p>

        <div class="row mt-60 justify-content-center">
            <div class="col-lg-8">
                <div class="border p-24 rounded-8">
                    <h5 class="mb-16">Business Information</h5>
                    <p><strong>STUDYNEST EDUCATIONAL</strong></p>
                    <p><strong>Proprietor:</strong> Arti Jha</p>
                    <p><strong>Address:</strong> F.No-206, Block No. B, Sagar Golden Palms, Katara Hills Barai Road, Near St. Francis Co-Ed School, Bhopal, Madhya Pradesh – 462043</p>
                    <p><strong>Email:</strong> colloborate@studynested.com</p>
                    <p><strong>Phone:</strong> +91 7389942021</p>
                    <p class="mt-16 text-gray-500 text-sm">© 2026 STUDYNEST EDUCATIONAL. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ========================= Why Become Seller section End =================================== -->

@endsection
