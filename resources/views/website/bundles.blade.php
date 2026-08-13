@extends('website.layouts.app')

@section('title', 'All Bundles - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">All Bundles</h6>
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
                    <li class="text-sm text-main-600">Bundles</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <section class="py-24">
        <div class="container">
            <!-- Filters -->
            <div class="mb-24">
                <form method="GET" action="{{ route('website.bundles') }}" class="row gy-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-gray-600 text-sm fw-medium mb-8">School</label>
                        <select name="school" class="form-select form-select-sm border-gray-200 rounded-8">
                            <option value="">All Schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->sch_id }}" {{ request('school') == $school->sch_id ? 'selected' : '' }}>
                                    {{ $school->sch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-gray-600 text-sm fw-medium mb-8">Class</label>
                        <select name="class" class="form-select form-select-sm border-gray-200 rounded-8">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}" {{ request('class') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label text-gray-600 text-sm fw-medium mb-8">Subject</label>
                        <select name="subject" class="form-select form-select-sm border-gray-200 rounded-8">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->subject_id }}" {{ request('subject') == $subject->subject_id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex gap-8">
                            <button type="submit" class="btn btn-main rounded-8 py-10 px-24 flex-grow-1">
                                <i class="ph ph-faders me-8"></i>Filter
                            </button>
                            @if(request()->hasAny(['school', 'class', 'subject', 'search']))
                                <a href="{{ route('website.bundles') }}" class="btn btn-outline-main rounded-8 py-10 px-16">
                                    <i class="ph ph-x"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Search Bar -->
            <div class="mb-24">
                <form method="GET" action="{{ route('website.bundles') }}" class="position-relative">
                    @if(request('school'))
                        <input type="hidden" name="school" value="{{ request('school') }}">
                    @endif
                    @if(request('class'))
                        <input type="hidden" name="class" value="{{ request('class') }}">
                    @endif
                    @if(request('subject'))
                        <input type="hidden" name="subject" value="{{ request('subject') }}">
                    @endif
                    <input type="text" name="search" class="form-control form-control-lg border-gray-200 rounded-12 ps-48 pe-48 py-16" placeholder="Search bundles by name or product..." value="{{ request('search') }}">
                    <button type="submit" class="position-absolute top-50 start-0 translate-middle-y ms-16 border-0 bg-transparent text-gray-500 hover-text-main-600">
                        <i class="ph ph-magnifying-glass text-xl"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('website.bundles', request()->except('search')) }}" class="position-absolute top-50 end-0 translate-middle-y me-16 text-gray-500 hover-text-main-600">
                            <i class="ph ph-x-circle text-xl"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Bundles Grid -->
            @if($bundles->count() > 0)
                <div class="row gy-4">
                    @foreach($bundles as $bundle)
                        <div class="col-lg-4 col-md-6">
                            <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                <span class="product-card__badge bg-main-600 text-white text-sm fw-medium rounded-4 px-8 py-4 position-absolute inset-inline-start-0 inset-block-start-0 m-16 z-1">
                                    {{ $bundle->products->count() }} Products
                                </span>

                                <div class="product-card__thumb flex-center rounded-8 bg-white position-relative">
                                    <img src="{{ asset('uploads/bundle/' . $bundle->b_image) }}" alt="{{ $bundle->b_name }}" class="w-auto" style="max-height: 200px; object-fit: contain;">
                                </div>

                                <div class="product-card__content mt-16">
                                    <h6 class="title text-lg fw-semibold mt-12 mb-8">
                                        <span class="link text-line-2">{{ $bundle->b_name }}</span>
                                    </h6>
                                    <div class="mt-8 mb-16">
                                        <span class="text-gray-600 text-sm">
                                            {{ Str::limit($bundle->b_short_desc, 80) }}
                                        </span>
                                    </div>

                                    <!-- Product Preview -->
                                    <div class="mb-16">
                                        <div class="d-flex flex-wrap gap-8">
                                            @foreach($bundle->products->take(4) as $bProduct)
                                                <div class="position-relative">
                                                    <img src="{{ asset('uploads/product-photo/' . $bProduct->p_photo) }}" alt="{{ $bProduct->p_name }}" class="rounded-6" style="width: 40px; height: 40px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                            @if($bundle->products->count() > 4)
                                                <div class="w-40 h-40 rounded-6 bg-gray-100 flex-center text-gray-600 text-xs fw-medium">
                                                    +{{ $bundle->products->count() - 4 }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ route('website.bundle-details', $bundle->b_id) }}" class="btn btn-main w-100 rounded-8 py-12 fw-medium">
                                        View Bundle <i class="ph ph-arrow-right ms-8"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-32">
                    {{ $bundles->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-64">
                    <div class="mb-24">
                        <i class="ph ph-package text-6xl text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-600 mb-8">No bundles found</h5>
                    <p class="text-gray-500 mb-24">Try adjusting your filters or search query</p>
                    <a href="{{ route('website.bundles') }}" class="btn btn-main rounded-8 py-12 px-32">
                        View All Bundles
                    </a>
                </div>
            @endif
        </div>
    </section>

@endsection
