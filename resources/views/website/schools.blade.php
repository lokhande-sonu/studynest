@extends('website.layouts.app')

@section('title', 'Schools - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">All Partnered & Available Schools</h6>
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
                    <li class="text-sm text-main-600"> Schools </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- =============================== Schools Section Start ======================================== -->
    <section class="shop py-80">
        <div class="container">
            <div class="row gy-4">
                @forelse($schools as $school)
                <div class="col-xxl-3 col-lg-4 col-sm-6" data-aos="fade-up" data-aos-duration="400">
                    <div class="product-card p-16 border border-gray-100 bg-white hover-border-main-600 rounded-16 position-relative transition-2 group-item d-flex flex-column h-100">
                        @if($school->is_verified == 1)
                        <span class="product-card__badge bg-danger-600 px-8 py-4 text-sm text-white">Verified</span>
                        @endif
                        
                        <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}" class="product-card__thumb flex-center overflow-hidden" style="height: 200px;">
                            <img src="{{ asset('uploads/school-logo/' . $school->sch_logo) }}" alt="{{ $school->sch_name }}" style="max-height: 180px; object-fit: contain;">
                        </a>
                        
                        <div class="product-card__content p-sm-2 w-100 d-flex flex-column flex-grow-1">
                            <h6 class="title text-lg fw-semibold my-16 text-center">
                                <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}" class="link text-line-2">{{ $school->sch_name }}</a>
                            </h6>
                            
                            <div class="gap-4 text-center mb-16">
                                <span class="text-gray-500 text-sm">
                                    <i class="ph ph-map-pin me-4"></i>{{ $school->sch_address }}, {{ $school->sch_city }}
                                </span>
                            </div>

                            <div class="product-card__content mt-auto pt-16">
                                <a href="{{ route('website.school-shop', ['school' => $school->sch_id]) }}"
                                    class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-12 px-24 rounded-pill flex-align gap-8 w-100 justify-content-center fw-medium">
                                    Browse Products <i class="ph ph-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-80">
                    <div class="mb-24">
                        <i class="ph ph-buildings text-64 text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-700">No schools found at the moment.</h5>
                    <p class="text-gray-500">Please check back later or contact us for assistance.</p>
                    <a href="{{ route('website.index') }}" class="btn btn-main rounded-pill px-32 mt-24">Go Back Home</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination Start -->
            @if($schools->hasPages())
            <div>
                <!-- <span class="text-gray-900">Showing {{ $schools->firstItem() }}-{{ $schools->lastItem() }} of {{ $schools->total() }} Schools</span> -->
                {{ $schools->links('pagination::bootstrap-5') }}
            </div>
            @endif
            <!-- Pagination End -->
        </div>
    </section>
    <!-- =============================== Schools Section End ======================================== -->

@endsection
