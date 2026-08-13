@extends('website.layouts.app')

@section('title', 'Blogs - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Our Latest Blogs & Insights</h6>
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
                    <li class="text-sm text-main-600"> Blogs </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- =============================== Blogs Section Start ======================================== -->
    <section class="shop py-80">
        <div class="container">
            <div class="row gy-4">
                @forelse($blogs as $blog)
                <div class="col-xxl-4 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="400">
                    <div class="product-card p-16 border border-gray-100 bg-white hover-border-main-600 rounded-16 position-relative transition-2 group-item d-flex flex-column h-100">
                        
                        <a href="{{ route('website.blog-details', $blog->b_slug) }}" class="product-card__thumb flex-center overflow-hidden rounded-12" style="height: 240px;">
                            @if($blog->b_image)
                            <img src="{{ asset('uploads/blog-photos/' . $blog->b_image) }}" alt="{{ $blog->b_title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                            <img src="{{ asset('management-assets/images/no-image.png') }}" alt="{{ $blog->b_title }}" style="max-height: 180px; object-fit: contain;">
                            @endif
                        </a>
                        
                        <div class="product-card__content p-sm-2 w-100 d-flex flex-column flex-grow-1 mt-16">
                            <div class="flex-align gap-12 mb-12">
                                <span class="text-gray-500 text-xs flex-align gap-4">
                                    <i class="ph ph-calendar-blank"></i> {{ $blog->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-gray-500 text-xs flex-align gap-4">
                                    <i class="ph ph-user"></i> Admin
                                </span>
                            </div>

                            <h6 class="title text-lg fw-semibold mb-16">
                                <a href="{{ route('website.blog-details', $blog->b_slug) }}" class="link text-line-2">{{ $blog->b_title }}</a>
                            </h6>
                            
                            <p class="text-gray-500 text-sm mb-16 text-line-3">
                                {{ Str::limit(strip_tags($blog->b_content), 120) }}
                            </p>

                            <div class="product-card__content mt-auto pt-16 border-top border-gray-100">
                                <a href="{{ route('website.blog-details', $blog->b_slug) }}"
                                    class="text-main-600 hover-text-main-700 flex-align gap-8 fw-medium">
                                    Read More <i class="ph ph-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-80">
                    <div class="mb-24">
                        <i class="ph ph-article text-64 text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-700">No blogs found at the moment.</h5>
                    <p class="text-gray-500">Please check back later for exciting news and articles.</p>
                    <a href="{{ route('website.index') }}" class="btn btn-main rounded-pill px-32 mt-24">Go Back Home</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination Start -->
            @if($blogs->hasPages())
            <div class="mt-40">
                {{ $blogs->links('pagination::bootstrap-5') }}
            </div>
            @endif
            <!-- Pagination End -->
        </div>
    </section>
    <!-- =============================== Blogs Section End ======================================== -->

@endsection
