@extends('website.layouts.app')

@section('title', 'Study Nest - Pre-Book School Essentials')

@section('content')

    <!-- ============================== Banner Inner Start ================================ -->
    <section class="banner-inner z-1 position-relative py-12 bg-img bg-start"
        data-background-image="{{ asset('customer-web-assets/images/thumbs/banner-inner-bg1.png') }}">
        <div class="container">


            <div class="banner-inner__content text-center">
                <!-- Logo Start -->
                <div class="logo">
                    <a href="{{ route('website.index') }}" class="link">
                        <img src="{{ asset('customer-web-assets/images/logo/logo.png') }}" alt="Logo">
                    </a>
                </div>
                <!-- Logo End  -->
                <h4 class="mb-20 fw-semibold">Pre-Book School Essentials</h4>
                <p class="my-20">Secure your child’s school books, uniforms, and accessories in advance with
                    a <strong>₹99 token amount</strong><br>and enjoy <strong>exclusive pre-booking discounts.</strong>
                </p>
            </div>
        </div>
    </section>
    <!-- ============================== Banner Inner End ================================ -->

    <!-- ============================= Intro Section start ================================= -->
    <section class="step py-20">
        <div class="position-relative z-1">
            <img src="{{ asset('customer-web-assets/images/shape/curve-line-shape.png') }}" alt=""
                class="position-absolute top-0 inset-inline-end-0 z-n1 me-60 d-lg-block d-none">

            <div class="container">
                <div class="row gy-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="step-content">
                            <div class="section-heading ms-auto text-start">
                                <h5 class="">Free Pre-Booking for First 1000 Parents</h5>
                                <p class="text-gray-600">The first 1000 parents can pre-book their child’s school essentials
                                    absolutely free. No ₹99 token amount will be required for the first 1000 registrations.
                                </p>
                                <h6 class="mt-24 mb-16">What You Get?</h6>
                                <ul class="list-unstyled">
                                    <li class="flex-align gap-12 mb-16">
                                        <span
                                            class="w-32 h-32 flex-center rounded-circle bg-main-50 text-main-600 text-md flex-shrink-0">
                                            <i class="ph-fill ph-check"></i>
                                        </span>
                                        <span class="text-gray-600 text-lg">Free Pre-Booking (₹99 Token Waived Off) for the
                                            first 1000 users</span>
                                    </li>
                                    <li class="flex-align gap-12 mb-16">
                                        <span
                                            class="w-32 h-32 flex-center rounded-circle bg-main-50 text-main-600 text-md flex-shrink-0">
                                            <i class="ph-fill ph-check"></i>
                                        </span>
                                        <span class="text-gray-600 text-lg">Guaranteed availability of Books, Uniforms, and
                                            Accessories</span>
                                    </li>
                                    <li class="flex-align gap-12 mb-16">
                                        <span
                                            class="w-32 h-32 flex-center rounded-circle bg-main-50 text-main-600 text-md flex-shrink-0">
                                            <i class="ph-fill ph-check"></i>
                                        </span>
                                        <span class="text-gray-600 text-lg">Exclusive early-bird discounted pricing</span>
                                    </li>
                                    <li class="flex-align gap-12 mb-16">
                                        <span
                                            class="w-32 h-32 flex-center rounded-circle bg-main-50 text-main-600 text-md flex-shrink-0">
                                            <i class="ph-fill ph-check"></i>
                                        </span>
                                        <span class="text-gray-600 text-lg">Priority delivery before the school session
                                            begins</span>
                                    </li>
                                    <li class="flex-align gap-12 mb-16">
                                        <span
                                            class="w-32 h-32 flex-center rounded-circle bg-main-50 text-main-600 text-md flex-shrink-0">
                                            <i class="ph-fill ph-check"></i>
                                        </span>
                                        <span class="text-gray-600 text-lg">School-approved kits with 100% accuracy</span>
                                    </li>
                                </ul>
                                <a href="#prebook-form" class="btn btn-main rounded-pill mt-32">Pre-Book Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <img src="{{ asset('customer-web-assets/images/thumbs/prebooking-hero1.png') }}"
                                alt="School Essentials" class="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================= Intro Section End ================================= -->


    <!-- ========================= Exclusive Benefits Section =================================== -->
    <section class="why-seller py-30 bg-neutral-50">
        <div class="container">
            <div class="section-heading text-center mx-auto">
                <h5 class="">Exclusive Benefits for Pre-Booking Parents</h5>
                <span class="text-gray-600">Enjoy these special perks when you book early.</span>
            </div>
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="why-seller-item text-center bg-white p-32 rounded-16 shadow-sm h-100">
                        <span class="text-main-two-600 text-72 d-inline-flex">
                            <i class="ph ph-percent"></i>
                        </span>
                        <h6 class="my-28">Additional Discount</h6>
                        <p class="text-gray-600 max-w-392 mx-auto">Get exclusive additional discounts compared to
                            regular prices.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="why-seller-item text-center bg-white p-32 rounded-16 shadow-sm h-100">
                        <span class="text-main-two-600 text-72 d-inline-flex">
                            <i class="ph ph-seal-check"></i>
                        </span>
                        <h6 class="my-28">Guaranteed School-Approved</h6>
                        <p class="text-gray-600 max-w-392 mx-auto">All items are 100% genuine and as per school approval
                            guidelines.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="why-seller-item text-center bg-white p-32 rounded-16 shadow-sm h-100">
                        <span class="text-main-two-600 text-72 d-inline-flex">
                            <i class="ph ph-package"></i>
                        </span>
                        <h6 class="my-28">Complete Sets</h6>
                        <p class="text-gray-600 max-w-392 mx-auto">Availability of complete sets (Books + Uniforms +
                            Accessories) in one go.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="why-seller-item text-center bg-white p-32 rounded-16 shadow-sm h-100">
                        <span class="text-main-two-600 text-72 d-inline-flex">
                            <i class="ph ph-wallet"></i>
                        </span>
                        <h6 class="my-28">Easy Balance Payment</h6>
                        <p class="text-gray-600 max-w-392 mx-auto">Pay the remaining balance comfortably after product
                            confirmation.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="why-seller-item text-center bg-white p-32 rounded-16 shadow-sm h-100">
                        <span class="text-main-two-600 text-72 d-inline-flex">
                            <i class="ph ph-file-text"></i>
                        </span>
                        <h6 class="my-28">Proper Invoice</h6>
                        <p class="text-gray-600 max-w-392 mx-auto">Get a proper invoice and full order tracking for your
                            purchase.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================= Exclusive Benefits Section End =================================== -->

    <!-- ========================= Who Should Pre-Book Section =================================== -->
    <section class="py-30 bg-main-600">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <h3 class="text-white mb-16">Who Should Pre-Book?</h3>
                    <p class="text-white text-lg opacity-75">This offer is perfect for you if you are looking for
                        convenience and savings.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row gy-4">
                        <div class="col-md-4">
                            <div class="bg-white bg-opacity-10 p-24 rounded-12 h-100 border border-white border-opacity-10">
                                <i class="ph-fill ph-check-circle text-yellow text-3xl mb-16"></i>
                                <p class="text-white text-md">Parents who want guaranteed product availability</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-white bg-opacity-10 p-24 rounded-12 h-100 border border-white border-opacity-10">
                                <i class="ph-fill ph-tag text-yellow text-3xl mb-16"></i>
                                <p class="text-white text-md">Parents who want the best possible pricing</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-white bg-opacity-10 p-24 rounded-12 h-100 border border-white border-opacity-10">
                                <i class="ph-fill ph-smiley text-yellow text-3xl mb-16"></i>
                                <p class="text-white text-md">Parents who prefer stress-free and organized school
                                    shopping</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================= Who Should Pre-Book Section End =================================== -->


    <!-- ========================== How It Works ========================== -->
    <section class="py-80" id="how-it-works">
        <div class="container">
            <div class="section-heading text-center mx-auto mb-48">
                <h5 class="">How Pre-Booking Works</h5>
                <span class="text-gray-600">Follow these simple steps.</span>
            </div>

            <div class="row gy-4 justify-content-center position-relative">
                <!-- Connect line (only on large screens) -->
                <div class="d-none d-lg-block w-100 position-absolute top-50 start-0 translate-middle-y z-n1 border-top border-dashed border-main-600 border-2"
                    style="height: 0;"></div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="text-center bg-white p-16 rounded-16 border border-gray-100 h-100">
                        <div
                            class="w-64 h-64 mx-auto flex-center rounded-circle bg-main-600 text-white text-2xl fw-bold mb-16">
                            1</div>
                        <h6 class="text-md mb-8">Select School</h6>
                        <p class="text-sm text-gray-600">Select your School and Class</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="text-center bg-white p-16 rounded-16 border border-gray-100 h-100">
                        <div
                            class="w-64 h-64 mx-auto flex-center rounded-circle bg-main-600 text-white text-2xl fw-bold mb-16">
                            2</div>
                        <h6 class="text-md mb-8">Fill Form</h6>
                        <p class="text-sm text-gray-600">Complete the pre-booking form</p>
                    </div>
                </div>
                <!-- <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="text-center bg-white p-16 rounded-16 border border-gray-100 h-100">
                            <div
                                class="w-64 h-64 mx-auto flex-center rounded-circle bg-main-600 text-white text-2xl fw-bold mb-16">
                                3</div>
                            <h6 class="text-md mb-8">Pay ₹99</h6>
                            <p class="text-sm text-gray-600">Pay the ₹99 token amount</p>
                        </div>
                    </div> -->
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="text-center bg-white p-16 rounded-16 border border-gray-100 h-100">
                        <div
                            class="w-64 h-64 mx-auto flex-center rounded-circle bg-main-600 text-white text-2xl fw-bold mb-16">
                            3</div>
                        <h6 class="text-md mb-8">Confirmed</h6>
                        <p class="text-sm text-gray-600">Your pre-booking is confirmed</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="text-center bg-white p-16 rounded-16 border border-gray-100 h-100">
                        <div
                            class="w-64 h-64 mx-auto flex-center rounded-circle bg-main-600 text-white text-2xl fw-bold mb-16">
                            4</div>
                        <h6 class="text-md mb-8">Delivery</h6>
                        <p class="text-sm text-gray-600">Final product details and discounted prices will be shared
                            before delivery</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================== How It Works End ========================== -->

    <!-- ========================== Form Section ========================== -->
    <section class="py-80 bg-main-two-50" id="prebook-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white p-48 rounded-16 shadow-lg">
                        <h3 class="mb-24 text-center">Pre-Book Now</h3>
                        <p class="text-center text-gray-600 mb-32">Reserve your child’s school essentials today with
                            just ₹99.</p>

                        @if(session('success'))
                            <div class="alert alert-success mb-3">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mb-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('website.prebooking.store') }}" method="POST" class="row gy-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold">Select School</label>
                                <select class="form-select border-gray-200" name="school_id" required>
                                    <option selected disabled value="">Choose School...</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->sch_id }}">{{ $school->sch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold">Select Class</label>
                                <select class="form-select border-gray-200" name="class" required>
                                    <option selected disabled value="">Choose Class...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold">Parent Name</label>
                                <input type="text" name="customer_name" class="form-control border-gray-200"
                                    placeholder="Your Full Name" required value="{{ old('customer_name') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold">Email Address
                                    (Optional)</label>
                                <input type="email" name="customer_email" class="form-control border-gray-200"
                                    placeholder="you@example.com" value="{{ old('customer_email') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-heading-two text-sm fw-semibold">Mobile Number</label>
                                <input type="text" name="customer_mobile" class="form-control border-gray-200"
                                    placeholder="10-digit mobile number" required value="{{ old('customer_mobile') }}">
                            </div>

                            <div class="col-12 mt-32">
                                <div class="p-16 bg-main-50 rounded-8 flex-between">
                                    <span class="fw-semibold text-gray-800">Token Amount Payable:</span>
                                    <span class="text-xl fw-bold text-main-600">₹99.00</span>
                                    <br />
                                    <i><span class="fw-semibold text-gray-800">Special Launch Offer: Free Pre-Booking for
                                            First 1000 Users</span></i>
                                </div>
                            </div>

                            <div class="col-12 mt-32 text-center">
                                <button type="submit" class="btn btn-main rounded-pill py-16 px-48 w-100 text-lg">Confirm
                                    Pre-Booking</button>
                            </div>
                            <p>First 1000 users can pre-book without paying the ₹99 token amount. After the limit is
                                reached, the regular ₹99 token will apply.</p>

                            <p>Deliveries will start from 20th March 2026, we will contact you before start delivering for
                                placing final order and material selection via email.</p>

                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!-- Token Amount Details Sidebar -->
                    <div class="bg-white p-32 rounded-16 shadow-sm border border-gray-100 mb-24">
                        <h5 class="mb-16">Token Amount Details</h5>
                        <ul class="list-unstyled">
                            <li class="flex-align gap-8 mb-12">
                                <i class="ph-fill ph-money text-main-600"></i>
                                <span class="text-sm text-gray-600">Pre-booking token amount: ₹99 only</span>
                            </li>
                            <li class="flex-align gap-8 mb-12">
                                <i class="ph-fill ph-calculator text-main-600"></i>
                                <span class="text-sm text-gray-600">Token amount will be adjusted in the final
                                    bill</span>
                            </li>
                            <li class="flex-align gap-8 mb-0">
                                <i class="ph-fill ph-warning-circle text-main-600"></i>
                                <span class="text-sm text-gray-600">Remaining amount payable after order
                                    confirmation</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Important Information Sidebar -->
                    <div class="bg-white p-32 rounded-16 shadow-sm border border-gray-100 mb-24">
                        <h5 class="mb-16">Important Information</h5>
                        <ul class="list-unstyled">
                            <li class="d-flex align-items-start gap-8 mb-12">
                                <i class="ph-fill ph-info text-main-600 flex-shrink-0 mt-4"></i>
                                <span class="text-sm text-gray-600">Pre-booking is available for a limited period
                                    only</span>
                            </li>
                            <li class="d-flex align-items-start gap-8 mb-12">
                                <i class="ph-fill ph-info text-main-600 flex-shrink-0 mt-4"></i>
                                <span class="text-sm text-gray-600">Discounts are applicable only for pre-booking
                                    customers</span>
                            </li>
                            <li class="d-flex align-items-start gap-8 mb-12">
                                <i class="ph-fill ph-info text-main-600 flex-shrink-0 mt-4"></i>
                                <span class="text-sm text-gray-600">All products will be delivered as per school
                                    approval</span>
                            </li>
                            <li class="d-flex align-items-start gap-8 mb-0">
                                <i class="ph-fill ph-info text-main-600 flex-shrink-0 mt-4"></i>
                                <span class="text-sm text-gray-600">Token amount is non-refundable but adjustable in the
                                    final order</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Assistance Sidebar -->
                    <div class="bg-white p-32 rounded-16 shadow-sm border border-gray-100">
                        <h5 class="mb-16">Need Assistance?</h5>
                        <p class="text-sm text-gray-600 mb-16">If you have any questions about pre-booking or product
                            details, our support team is here to help.</p>
                        <a href="https://wa.me/917389087022" class="btn btn-outline-main w-100 mb-12 flex-center gap-8">
                            <i class="ph-fill ph-whatsapp-logo"></i> WhatsApp Support
                        </a>
                        <a href="mailto:colloborate@studynested.com"
                            class="btn btn-outline-main w-100 mb-12 flex-center gap-8">
                            <i class="ph-fill ph-envelope"></i> Email Support
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================== Form Section End ========================== -->

@endsection