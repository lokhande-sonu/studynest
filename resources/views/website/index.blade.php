@extends('website.layouts.app')

@section('title', 'Study Nest - Pre-Book School Essentials')

@section('content')

<!-- ============================ Banner Section start =============================== -->
    <div class="banner">
        <div class="container">
            <div class="banner-item rounded-24 overflow-hidden position-relative arrow-center">
                <!-- <a href="#featureSection"
                    class="scroll-down w-60 h-60 text-center flex-center bg-main-600 rounded-circle border border-5 text-white border-white position-absolute start-50 translate-middle-x bottom-0 hover-bg-main-800">
                    <span class="icon line-height-0"><i class="ph ph-caret-double-down"></i></span>
                </a> -->
                <img src="assets/images/bg/banner-bg.png" alt=""
                    class="banner-img position-absolute inset-block-start-0 inset-inline-start-0 w-100 h-100 z-n1 object-fit-cover rounded-24">

                <div class="flex-align">
                    <button type="button" id="banner-prev"
                        class="slick-prev slick-arrow flex-center rounded-circle box-shadow-4xl bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                        <i class="ph ph-caret-left"></i>
                    </button>
                    <button type="button" id="banner-next"
                        class="slick-next slick-arrow flex-center rounded-circle box-shadow-4xl bg-white text-xl hover-bg-main-600 hover-text-white transition-1">
                        <i class="ph ph-caret-right"></i>
                    </button>
                </div>

                <div class="banner-slider">
                    @foreach($sliders as $slider)
                        <div class="banner-slider__item">
                            <img src="{{ asset('uploads/app-sliders/' . $slider->slider_photo_url) }}" style="width: 100%;" alt="Slider Image">
                        </div>
                    @endforeach
                    @if($sliders->isEmpty())
                        <div class="banner-slider__item">
                            <img src="{{ asset('customer-web-assets/images/bg/s1.jpg') }}" style="width: 100%;" alt="Default Banner 1">
                        </div>
                        <div class="banner-slider__item">
                            <img src="{{ asset('customer-web-assets/images/bg/s2.jpg') }}" style="width: 100%;" alt="Default Banner 2">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- ============================ Banner Section End =============================== -->

    <div class="product pt-20">
        <div class="container">
            <div class="section-heading">
                <div class="text-center">

                    <h5 class="mb-10 wow fadeInLeft">
                        Start Your School Shopping With StudyNest!
                    </h5>
                    <p class="mt-4 mb-20">Get all your school essentials in one place!</p>

                    <!-- Form Wrapper -->
                    <div class="d-flex justify-content-center mt-4">
                        <form action="{{ route('website.school-shop') }}" method="GET" class="form-location-wrapper max-w-840 w-100 text-center">

                            <div class="search-category select-style-one d-flex 
                                    select-border-end-0 search-form 
                                    text-heading-two text-sm w-100">

                                <select class="js-example-basic-single border border-neutral-40 border-end-0"
                                    name="school">
                                    <option value="" selected disabled>Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->sch_id }}">{{ $school->sch_name }}</option>
                                    @endforeach
                                </select>

                                <select class="js-example-basic-single" name="class">
                                    <option value="" selected disabled>Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->class_id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>

                                <select class="js-example-basic-single" name="category">
                                    <option value="" selected disabled>Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->cat_id }}">{{ $category->cat_name }}</option>
                                    @endforeach
                                </select>

                                <div class="search-form__wrapper position-relative border-half-start flex-grow-1">
                                    <input type="text" class="common-input border-neutral-40 py-18 ps-16 pe-76
                                              rounded-0 rounded-end pe-44
                                              placeholder-italic placeholder-text-sm border-start-0"
                                        name="search"
                                        placeholder="What would you like to buy today?">

                                    <button type="submit" class="w-64 h-44 bg-main-600 hover-bg-main-800 rounded-4
                                               flex-center text-xl text-white position-absolute
                                               top-50 translate-middle-y inset-inline-end-0 me-6">
                                        <i class="ph ph-magnifying-glass"></i>
                                    </button>
                                </div>

                            </div>

                            <!-- Mobile duplicates removed: desktop selects use d-sm-flex/d-none for responsive visibility -->

                        </form>
                    </div>
                    <!-- End Form Wrapper -->

                </div>
            </div>
        </div>
    </div>

    <div class="product pt-20 container">
        <div class="container">
            <div class="section-heading">
                <div class="flex-between flex-wrap gap-8">
                    <h5 class="mb-0 wow fadeInLeft">Schools Trusted Our Platform</h5>

                    <div class="flex-align gap-16 wow fadeInRight">
                        <!-- <a href="shop.html"
                            class="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline">View
                            All Schools</a> -->
                        <!-- <div class="flex-align gap-8">
                            <button type="button" id="school-slider-prev"
                                class="swiper-button-prev slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-left"></i>
                            </button>
                            <button type="button" id="school-slider-next"
                                class="swiper-button-next slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-right"></i>
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

            <style>
        .school-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        @media (min-width: 768px) {
            .school-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (min-width: 992px) {
            .school-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        
        /* Mobile search form styling - override main.css */
        .form-location-wrapper {
            position: relative;
        }
        
        .d-sm-none .search-form__wrapper {
            display: block !important;
            width: 100% !important;
        }
        
        .d-sm-none .search-form {
            width: 100% !important;
        }
        
        .d-sm-none .select2-container {
            width: 100% !important;
            margin-bottom: 10px;
        }
        .select2-container > .selection {
            width: 100% !important;
        }
        
        .d-sm-none .select2-container--default .select2-selection--single {
            width: 100% !important;
            border-radius: 4px !important;
        }
        
        /* Fix dropdown positioning */
        .d-sm-none .select2-dropdown {
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
        }
    </style>

            <div class="school-grid">
                @foreach($schools as $school)
                <div class="school-grid-item d-flex">
                    <div class="product-card p-12 border border-gray-100 bg-white hover-border-main-600 rounded-16 position-relative transition-2 group-item d-flex flex-column h-100 w-100" style="min-height: 320px;">
                        @if($school->is_verified == 1)
                        <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Verified</span>
                        @endif
                        <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}" class="product-card__thumb flex-center overflow-hidden" style="height: auto;">
                            <img src="{{ asset('uploads/school-logo/' . $school->sch_logo) }}" alt="{{ $school->sch_name }}" style="max-height: 150px; object-fit: contain;">
                        </a>
                        <div class="product-card__content p-sm-2 w-100 d-flex flex-column flex-grow-1">
                            <h6 class="title text-lg fw-semibold my-12 text-center">
                                <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}" class="link text-line-2">{{ $school->sch_name }}</a>
                            </h6>
                            <div class="gap-4 text-center">
                                <span class="text-gray-500 text-sm">{{ $school->sch_address }}</span>
                            </div>

                            <div class="product-card__content mt-auto pt-12">
                                <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}"
                                    class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 w-100 justify-content-center">
                                    Buy Products<i class="ph ph-shopping-cart"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
    <!-- ============================ Feature Section start =============================== -->
    <div class="product pt-20">
        <div class="container">
            <div class="section-heading">
                <div class="flex-between flex-wrap gap-8">
                    <h5 class="mb-0 wow fadeInLeft">Popular Study Material Categories</h5>

                    <div class="flex-align gap-16 wow fadeInRight">
                        <a href="{{ route('website.shop') }}"
                            class="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline">View
                            All Products</a>
                        <!-- <div class="flex-align gap-8">
                            <button type="button" id="product-one-prev"
                                class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-left"></i>
                            </button>
                            <button type="button" id="product-one-next"
                                class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-right"></i>
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>

           <div class="cat-one-slider g-12">
                @foreach($categories as $category)
                <div class="col-xxl-4 col-lg-4 col-sm-4 col-6" data-aos="fade-up" data-aos-duration="400">
                    
                    <a href="{{ route('website.shop', ['category' => $category->cat_id]) }}" class="d-block">
                        <div class="product-card p-12 border border-gray-100 bg-white hover-border-main-600 rounded-16 position-relative transition-2 group-item">
            
                            <div class="w-100 h-100 flex-center">
                                <img src="{{ asset('uploads/product-category-photo/' . $category->cat_photo) }}" 
                                     alt="{{ $category->cat_name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
            
                            <div class="product-card__content p-sm-2 w-100 text-center">
                                <h6 class="text-lg mb-8 text-inherit">
                                    {{ $category->cat_name }}
                                </h6>
            
                                <div class="gap-4 text-center">
                                    <span class="text-sm text-gray-400">
                                        {{ $category->products_count ?? '0' }} Products
                                    </span>
                                </div>
                            </div>
            
                        </div>
                    </a>
            
                </div>
                @endforeach
            </div>

        </div>
    </div>
    
    
    
    <!--<div class="feature pt-60" id="featureSection">-->
    <!--    <div class="container">-->
    <!--        <div class="section-heading">-->
    <!--            <div class="flex-between flex-wrap gap-8">-->
    <!--                <h5 class="mb-0 wow fadeInLeft">Popular Study Material Categories</h5>-->
    <!--                <div class="flex-align gap-16 wow fadeInRight">-->
    <!--                    <a href="{{ route('website.shop') }}"-->
    <!--                        class="text-sm fw-medium text-gray-700 hover-text-main-600 hover-text-decoration-underline">View-->
    <!--                        All Products</a>-->
    <!--                    <div class="flex-align gap-8">-->
    <!--                        <button type="button" id="product-one-prev"-->
    <!--                            class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">-->
    <!--                            <i class="ph ph-caret-left"></i>-->
    <!--                        </button>-->
    <!--                        <button type="button" id="product-one-next"-->
    <!--                            class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">-->
    <!--                            <i class="ph ph-caret-right"></i>-->
    <!--                        </button>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="position-relative arrow-center gradient-shadow">-->
    <!--            <div class="flex-align">-->
    <!--                <button type="button" id="feature-item-wrapper-prev"-->
    <!--                    class="slick-prev slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">-->
    <!--                    <i class="ph ph-caret-left"></i>-->
    <!--                </button>-->
    <!--                <button type="button" id="feature-item-wrapper-next"-->
    <!--                    class="slick-next slick-arrow flex-center rounded-circle bg-white text-xl hover-bg-main-600 hover-text-white transition-1">-->
    <!--                    <i class="ph ph-caret-right"></i>-->
    <!--                </button>-->
    <!--            </div>-->
    <!--            <div class="feature-item-wrapper">-->
    <!--                @foreach($categories as $category)-->
    <!--                <div class="feature-item text-center wow bounceIn" data-aos="fade-up" data-aos-duration="400">-->
    <!--                    <div class="feature-item__thumb rounded-circle">-->
    <!--                        <a href="{{ route('website.school-shop', ['category' => $category->cat_id]) }}" class="w-100 h-100 flex-center">-->
    <!--                            <img src="{{ asset('uploads/product-category-photo/' . $category->cat_photo) }}" alt="{{ $category->cat_name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">-->
    <!--                        </a>-->
    <!--                    </div>-->
    <!--                    <div class="feature-item__content mt-16">-->
    <!--                        <h6 class="text-lg mb-8"><a href="{{ route('website.shop', ['category' => $category->cat_id]) }}" class="text-inherit">{{ $category->cat_name }}</a></h6>-->
    <!--                        <span class="text-sm text-gray-400">{{ $category->products_count ?? '0' }} Products</span>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                @endforeach-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    <!-- ============================ Feature Section End =============================== -->

    <!-- ========================= New Arrivals Start ================================ -->
    <!--<section class="new-arrivals pt-80 overflow-hidden">-->
    <!--    <div class="container">-->
    <!--        <div class="section-heading text-center">-->
    <!--            <h5 class="mb-0 wow fadeInLeft">New Arrivals</h5>-->
    <!--        </div>-->
    <!--        <div class="row gy-4">-->
    <!--            @foreach($newArrivals as $product)-->
    <!--            <div class="col-xxl-3 col-lg-4 col-sm-6 col-12" data-aos="fade-up" data-aos-duration="600">-->
    <!--                <div class="product-card h-100 p-12 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">-->
    <!--                    <a href="{{ route('website.product-details', $product->p_id) }}" class="product-card__thumb flex-center overflow-hidden">-->
    <!--                        <img src="{{ asset('uploads/product-photo/' . $product->p_photo) }}" alt="{{ $product->p_name }}" style="max-height: 200px; object-fit: contain;">-->
    <!--                    </a>-->
    <!--                    <div class="product-card__content p-sm-2 w-100">-->
    <!--                        <h6 class="title text-lg fw-semibold my-12 text-center">-->
    <!--                            <a href="{{ route('website.product-details', $product->p_id) }}" class="link text-line-2">{{ $product->p_name }}</a>-->
    <!--                        </h6>-->
    <!--                        <div class="gap-4 text-center">-->
    <!--                            <span class="text-main-600 fw-bold">₹{{ $product->p_price }}</span>-->
    <!--                        </div>-->
    <!--                        <div class="product-card__content mt-12">-->
    <!--                            <a href="{{ route('website.product-details', $product->p_id) }}"-->
    <!--                                class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-11 px-24 rounded-pill flex-align gap-8 mt-24 w-100 justify-content-center">-->
    <!--                                View Details <i class="ph ph-arrow-right"></i>-->
    <!--                            </a>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            @endforeach-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    <!-- ========================= New Arrivals End ================================ -->

    <!-- ========================= Testimonials Slider Start ================================ -->
    <section class="testimonials-section py-60">
        <div class="container">
            <div class="section-heading text-center">
                <div class="">
                    <h5 class="mb-0 wow fadeInLeft">What Our Customers Say</h5>
                </div>
            </div>

            <div class="testimonial-slider">
                <!-- Slide 1 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Honestly kaafi relief mila, school shopping ka stress kam hua aur saare essentials ek hi box mein mil gaye.</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t1.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Rohan Mehta</h6>
                                <small class="text-gray-500">Father</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">ime kaafi bach gaya, alag alag shops jaane ki zarurat nahi padi aur sab stationery ek jagah mil gayi.</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t2.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Ishaan Chawla</h6>
                                <small class="text-gray-500">Father</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">School ki list ke hisaab se saare items aaye, koi cheez miss nahi hui aur process kaafi easy raha</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t3.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Ankit Shrivastava</h6>
                                <small class="text-gray-500">Father</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 4 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Parents ke liye kaafi helpful solution hai, last minute shopping aur unnecessary confusion bilkul khatam ho gaya.</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t4.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Rakesh Gupta</h6>
                                <small class="text-gray-500">Father</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 5 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Busy parents ke liye perfect option hai, school shopping simple ho gayi aur kaafi time save hua.</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t5.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Sunil Patel</h6>
                                <small class="text-gray-500">Father</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 6 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Bachche ke liye sab items achhe se packed the, quality bhi achhi lagi aur delivery smooth rahi</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t6.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Rekha Mishra</h6>
                                <small class="text-gray-500">Mother</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 7 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">School start hone se pehle hi sab ready ho gaya, market ke unnecessary chakkar nahi lagane pade</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t7.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Suman Tiwari</h6>
                                <small class="text-gray-500">Mother</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 8 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Preparation kaafi easy ho gayi, ek hi order mein complete school essentials mil gaye easily</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t8.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Tanvi Joshi</h6>
                                <small class="text-gray-500">Mother</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 9 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Overall experience kaafi acha raha, time bhi bacha aur school shopping bilkul tension free ho gayi</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t9.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Aarti Malhotra</h6>
                                <small class="text-gray-500">Mother</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 10 -->
                <div class="testimonial-card card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="quote-circle flex-center mb-16"><i class="ph-fill ph-quotes"></i></div>
                        <p class="testimonial-text mb-16">Parents ke liye stress free solution hai, main definitely isko dusre parents ko recommend karungi</p>
                        <div class="d-flex align-items-center mt-12">
                            <img class="avatar rounded-circle me-12" src="{{ asset('customer-web-assets/images/thumbs/t10.jpeg') }}"
                                alt="">
                            <div>
                                <h6 class="mb-0">Ritu Agrawal</h6>
                                <small class="text-gray-500">Mother</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================= Testimonials Slider End ================================ -->
    
    @endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 on mobile dropdowns with proper dropdown parent
    $('.d-sm-none .js-example-basic-single').select2({
        width: '100%',
        dropdownParent: $('.form-location-wrapper')
    });
});
</script>
@endpush
