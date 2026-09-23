@extends('website.layouts.app')

@section('title', 'Shop - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container container-lg">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">StudyNest Products Shop</h6>
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
                    @php
                        $filters = [];
                        if(request('search')) $filters[] = 'Search: "'.request('search').'"';
                        if(request('school')) {
                            $s = $schools->firstWhere('sch_id', request('school'));
                            if($s) $filters[] = $s->sch_name;
                        }
                        if(request('class')) {
                            $c = $classes->firstWhere('class_id', request('class'));
                            if($c) $filters[] = $c->class_name;
                        }
                        if(request('subject')) {
                            $sub = $subjects->firstWhere('subject_id', request('subject'));
                            if($sub) $filters[] = $sub->subject_name;
                        }
                        if(request('category')) {
                            $cat = $categories->firstWhere('cat_id', request('category'));
                            if($cat) $filters[] = $cat->cat_name;
                        }
                        if(request('gender')) $filters[] = request('gender');
                    @endphp

                    @if(count($filters) > 0)
                        <li class="text-sm">
                            <a href="{{ route('website.school-shop') }}" class="text-gray-900 hover-text-main-600">Product Shop</a>
                        </li>
                        <li class="flex-align">
                            <i class="ph ph-caret-right"></i>
                        </li>
                        <li class="text-sm text-main-600">
                            {{ implode(', ', $filters) }}
                        </li>
                    @else
                        <li class="text-sm text-main-600"> Product Shop </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- =============================== Shop Section Start ======================================== -->
    <section class="shop py-20">
        <div class="container container-lg">
            <div class="row">

                <!-- Sidebar Start -->
                <div class="col-lg-3">
                    <form action="{{ route('website.school-shop') }}" method="GET" id="shopFilterForm">
                        <div class="shop-sidebar">
                            <button type="button"
                                class="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600">
                                <i class="ph ph-x"></i>
                            </button>

                            @if(request()->hasAny(['school', 'class', 'subject', 'category', 'gender', 'sort_by', 'search']))
                            <div class="mb-32">
                                <a href="{{ route('website.school-shop') }}" class="w-100 btn btn-main text-white rounded-pill py-14 px-24 flex-center gap-8">
                                    <i class="ph ph-arrow-counter-clockwise"></i> Clear All Filters
                                </a>
                            </div>
                            @endif
                            
                            <!-- Filter by School -->
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter By Schools</h6>
                                <ul class="max-h-540 overflow-y-auto scroll-sm">
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="school" id="school_all" value="" onchange="this.form.submit()" {{ request('school') == '' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="school_all">All Schools</label>
                                        </div>
                                    </li>
                                    @foreach($schools as $school)
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="school" id="school_{{ $school->sch_id }}" value="{{ $school->sch_id }}" onchange="this.form.submit()" {{ request('school') == $school->sch_id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="school_{{ $school->sch_id }}">{{ $school->sch_name }}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Filter by Class -->
                            <!--<div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">-->
                            <!--    <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">-->
                            <!--        Filter by Class-->
                            <!--    </h6>-->
                            <!--    <div class="mb-0">-->
                            <!--        <select class="common-input form-select" name="class" onchange="this.form.submit()">-->
                            <!--            <option value="">Select Class</option>-->
                            <!--            @foreach($classes as $class)-->
                            <!--                <option value="{{ $class->class_id }}" {{ request('class') == $class->class_id ? 'selected' : '' }}>{{ $class->class_name }}</option>-->
                            <!--            @endforeach-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <input type="hidden" name="class" id="filter-class" value="{{ request('class') }}"> 
                            <!--Use only if above code is commented-->


                            <!-- Filter by Subject -->
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">
                                    Filter by Subject
                                </h6>
                                <div class="mb-0">
                                    <select class="common-input form-select" name="subject" onchange="this.form.submit()">
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->subject_id }}" {{ request('subject') == $subject->subject_id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Filter by Category -->
                            <!--<div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">-->
                            <!--    <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">-->
                            <!--        Filter by Category-->
                            <!--    </h6>-->
                            <!--    <ul class="max-h-540 overflow-y-auto scroll-sm">-->
                            <!--        <li class="mb-24">-->
                            <!--            <div class="form-check common-check common-radio">-->
                            <!--                <input class="form-check-input" type="radio" name="category" id="cat_all" value="" onchange="this.form.submit()" {{ request('category') == '' ? 'checked' : '' }}>-->
                            <!--                <label class="form-check-label" for="cat_all">All Categories</label>-->
                            <!--            </div>-->
                            <!--        </li>-->
                            <!--        @foreach($categories as $category)-->
                            <!--        <li class="mb-24">-->
                            <!--            <div class="form-check common-check common-radio">-->
                            <!--                <input class="form-check-input" type="radio" name="category" id="cat_{{ $category->cat_id }}" value="{{ $category->cat_id }}" onchange="this.form.submit()" {{ request('category') == $category->cat_id ? 'checked' : '' }}>-->
                            <!--                <label class="form-check-label" for="cat_{{ $category->cat_id }}">{{ $category->cat_name }}</label>-->
                            <!--            </div>-->
                            <!--        </li>-->
                            <!--        @endforeach-->
                            <!--    </ul>-->
                            <!--</div>-->
                            <input type="hidden" name="category" id="filter-category" value="{{ request('category') }}">
                            <!--Use only if above code is commented-->

                            <!-- Filter by Gender -->
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">Filter by Gender</h6>
                                <ul class="max-h-540 overflow-y-auto scroll-sm">
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_all" value="" onchange="this.form.submit()" {{ request('gender') == '' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_all">All</label>
                                        </div>
                                    </li>
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_boys" value="Boys" onchange="this.form.submit()" {{ request('gender') == 'Boys' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_boys">Boys</label>
                                        </div>
                                    </li>
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_girls" value="Girls" onchange="this.form.submit()" {{ request('gender') == 'Girls' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_girls">Girls</label>
                                        </div>
                                    </li>
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="gender" id="gender_unisex" value="Unisex" onchange="this.form.submit()" {{ request('gender') == 'Unisex' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_unisex">Unisex</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- Sidebar End -->

                <!-- Content Start -->
                <div class="col-lg-9">
                        
                       @php
                            $selectedSchool = null;
                            if(request('school')) {
                                $selectedSchool = $schools->firstWhere('sch_id', request('school'));
                            }
                        @endphp
                        
                        @if($selectedSchool)
                        <div class="blog-sidebar border border-gray-100 rounded-8 p-32 mb-40">
                            <div class="d-flex align-items-center flex-sm-nowrap flex-wrap gap-24 mb-16">
                                <div class="w-120 h-120 rounded-4 overflow-hidden flex-shrink-0">
                                    <img src="{{ asset('uploads/school-logo/' . $selectedSchool->sch_logo) }}"
                                         alt="{{ $selectedSchool->sch_name }}"
                                         class="cover-img">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-lg mb-4">{{ $selectedSchool->sch_name }}</h6>
                                    <br/>
                                    <p class="text-md">{{ $selectedSchool->sch_desc }}</p>
                                    <br/>
                                    <div class="flex-align gap-8">
                                        <i class="ph ph-map-pin text-main-600"></i>
                                        <span class="text-lg text-black">
                                            {{ $selectedSchool->sch_address }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="blog-sidebar border border-gray-100 rounded-8 p-32 mb-40">

                        {{-- Class Selector --}}
                        <h6 class="text-lg mb-16">Select Your Class</h6>
                        <div class="d-flex flex-wrap gap-12 mb-32">
                            @foreach($classes as $class)
                                <button type="button"
                                    onclick="applyClass({{ $class->class_id }})"
                                    class="btn py-10 px-20 rounded-8 fw-medium
                                    {{ request('class') == $class->class_id
                                        ? 'bg-main-600 text-white'
                                        : 'bg-gray-50 text-heading hover-bg-main-600 hover-text-white' }}">
                                    {{ $class->class_name }}
                                </button>
                            @endforeach
                        </div>
                    
                        {{-- Category Selector --}}
                        <h6 class="text-lg mb-16">Select Category</h6>
                        <div class="d-flex flex-wrap gap-12">
                            @foreach($categories as $category)
                                <button type="button"
                                    onclick="applyCategory({{ $category->cat_id }})"
                                    class="btn py-10 px-20 rounded-8 fw-medium
                                    {{ request('category') == $category->cat_id
                                        ? 'bg-main-600 text-white'
                                        : 'bg-gray-50 text-heading hover-bg-main-600 hover-text-white' }}">
                                    {{ $category->cat_name }}
                                </button>
                            @endforeach
                        </div>
                    
                    </div>

                        
                        
                        
                        
                        
                        <!-- Top Start -->
                        <div class="flex-between gap-16 flex-wrap mb-40 ">
                            <span class="text-gray-900">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} result</span>
                            <div class="position-relative flex-align gap-16 flex-wrap">

                                <!--<div class="list-grid-btns flex-align gap-16">-->
                                <!--    <button type="submit" form="bulkAddForm"-->
                                <!--        class="w-100 h-44 flex-center border border-gray-100 rounded-6 text-2xl btn-main text-white ps-10 pe-10">-->
                                <!--        <i class="ph ph-shopping-cart"></i> <span class="text-16 ps-10">Bulk Add To Cart</span>-->
                                <!--    </button>-->
                                <!--</div>-->
                                <div class="position-relative text-gray-500 flex-align gap-4 text-14">
                                    <label for="sorting" class="text-inherit flex-shrink-0">Sort by: </label>
                                    {{-- START: imohitmehto | 2026-08-25 | FIX: Sort dropdown uses URL manipulation instead of form.submit() --}}
                                    {{-- Original sort select was outside the <form> element, so this.form.submit() failed --}}
                                    {{-- Now uses JavaScript URL parameter manipulation to preserve all existing filters --}}
                                    <select class="form-control common-input px-14 py-14 text-inherit rounded-6 w-auto"
                                        id="sorting" name="sort_by" onchange="var url = new URL(window.location); url.searchParams.set('sort_by', this.value); window.location = url.toString();">
                                        <option value="" selected disabled>Sort By</option>
                                        <option value="price_low_high" {{ request('sort_by') == 'price_low_high' ? 'selected' : '' }}>Price Low to High</option>
                                        <option value="price_high_low" {{ request('sort_by') == 'price_high_low' ? 'selected' : '' }}>Price High to Low</option>
                                    </select>
                                    {{-- END: imohitmehto | FIX: Sort dropdown JavaScript fix --}}
                                </div>
                                <button type="button"
                                    class="w-44 h-44 d-lg-none d-flex flex-center border border-gray-100 rounded-6 text-2xl sidebar-btn"><i
                                        class="ph-bold ph-funnel"></i></button>
                            </div>
                        </div>
                        <!-- Top End -->

                        <div class="row gy-4">
                            <!-- Bundles Loop -->
                            @foreach($bundles as $bundle)
                            <div class="col-lg-4 col-sm-6 bundle-item" data-bundle-id="{{ $bundle->b_id }}">
                                <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 cursor-pointer"
                                     onclick="toggleBundleDetails({{ $bundle->b_id }}, this)">
                                    <span class="product-card__badge bg-main-600 text-white text-sm fw-medium rounded-4 px-8 py-4 position-absolute inset-inline-start-0 inset-block-start-0 m-16 z-1">Bundle</span>
                                    
                                    <div class="product-card__thumb flex-center rounded-8 bg-white position-relative">
                                        <img src="{{ asset('uploads/bundle/' . $bundle->b_image) }}" alt="{{ $bundle->b_name }}" class="w-auto" style="max-height: 200px; object-fit: contain;">
                                    </div>
                                    <div class="product-card__content mt-8">
                                        <h6 class="title text-lg fw-semibold mt-12 mb-8">
                                            <span class="link text-line-2">{{ $bundle->b_name }}</span>
                                        </h6>
                                        <div class="mt-8">
                                            <span class="text-gray-900 text-sm fw-medium mt-8">
                                                {{ Str::limit($bundle->b_short_desc, 60) }}
                                            </span>
                                        </div>
                                        
                                         <button type="button"
                                            class="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100 mt-24">
                                            View Bundle <i class="ph ph-caret-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Template for Bundle Details -->
                            <div id="bundle-details-template-{{ $bundle->b_id }}" class="d-none">
                                <div class="bundle-expansion-content p-24 bg-gray-50 border border-gray-100 rounded-16 mt-16 position-relative">
                                     <button type="button" class="position-absolute top-0 end-0 m-16 w-32 h-32 flex-center border border-gray-200 rounded-circle bg-white text-gray-500 hover-bg-main-600 hover-text-white transition-1" onclick="closeBundleDetails()">
                                        <i class="ph ph-x"></i>
                                    </button>

                                    <div class="mb-24">
                                        <h6 class="mb-12">{{ $bundle->b_name }}</h6>
                                        <p class="text-gray-600 mb-16">{{ $bundle->b_short_desc }}</p>
                                        
                                        <!-- Category Filters -->
                                        @php
                                            $bundleCategories = $bundle->products->pluck('category')->unique('cat_id')->filter();
                                        @endphp
                                        @if($bundleCategories->count() > 0)
                                        <div class="flex-align gap-8 bundle-filters mb-24 flex-wrap" data-bundle-id="{{ $bundle->b_id }}">
                                            <span class="fw-medium text-gray-900 me-8">Filter:</span>
                                            <button type="button" class="btn btn-sm btn-main rounded-pill filter-btn active" onclick="filterBundleProducts({{ $bundle->b_id }}, 'all', this)">All</button>
                                            @foreach($bundleCategories as $cat)
                                                <button type="button" class="btn btn-sm btn-outline-main bg-white rounded-pill filter-btn" onclick="filterBundleProducts({{ $bundle->b_id }}, {{ $cat->cat_id }}, this)">
                                                    {{ $cat->cat_name }}
                                                </button>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>

                                    <div id="tpl_bundleForm{{ $bundle->b_id }}">
                                        <div class="row gy-4 bundle-products-grid" id="bundle-products-{{ $bundle->b_id }}">
                                            @foreach($bundle->products as $bProduct)
                                                 @php
                                                    // Stock & Price Logic
                                                    $isInStock = false;
                                                    $defaultVariantId = null;
                                                    $price = $bProduct->p_price;
                                                    $discountPrice = $bProduct->discounted_price;
                                                    
                                                    if($bProduct->variants->count() > 0) {
                                                        $firstVariant = $bProduct->variants->first();
                                                        $defaultVariantId = $firstVariant->prod_variant_id;
                                                        $inventory = $bProduct->stockInventories->where('prod_variant_id', $firstVariant->prod_variant_id)->first();
                                                        if($inventory && $inventory->available_stock > 0) {
                                                            $isInStock = true;
                                                            $price = $inventory->unit_price;
                                                            $discountPrice = $inventory->discounted_unit_price;
                                                        }
                                                    } else {
                                                         $inventory = $bProduct->stockInventories->whereNull('prod_variant_id')->first();
                                                         if($inventory && $inventory->available_stock > 0) {
                                                            $isInStock = true;
                                                            $price = $inventory->unit_price;
                                                            $discountPrice = $inventory->discounted_unit_price;
                                                         } elseif($bProduct->p_stock > 0) {
                                                             $isInStock = true; // Fallback
                                                         }
                                                    }
                                                @endphp
                                                
                                                <div class="col-lg-3 col-md-4 col-sm-6 bundle-product-item" data-category="{{ $bProduct->category->cat_id ?? 'other' }}">
                                                    <div class="product-card h-100 p-16 border border-gray-100 bg-white rounded-16 position-relative {{ !$isInStock ? 'opacity-50' : '' }}">
                                                        <!-- Checkbox for Selection -->
                                                        <div class="position-absolute top-0 start-0 m-16 z-2">
                                                            <div class="form-check common-check">
                                                                <input class="form-check-input" type="checkbox" name="product_ids[]" value="{{ $bProduct->p_id }}" 
                                                                    {{ $isInStock ? 'checked' : 'disabled' }} 
                                                                    id="tpl_bp_{{ $bundle->b_id }}_{{ $bProduct->p_id }}">
                                                                <label class="form-check-label" for="tpl_bp_{{ $bundle->b_id }}_{{ $bProduct->p_id }}"></label>
                                                            </div>
                                                        </div>
                                                        <br/>
                                                        
                                                        <div class="product-card__thumb flex-center rounded-8 bg-white position-relative mb-16 {{ !$isInStock ? 'opacity-75' : '' }}">
                                                            <img src="{{ asset('uploads/product-photo/' . $bProduct->p_photo) }}" alt="" class="w-auto max-w-unset" style="max-height: 150px; object-fit: contain;">
                                                            @unless($isInStock)
                                                            <span class="position-absolute top-50 start-50 translate-middle badge bg-danger rounded-pill px-16 py-8">OUT OF STOCK</span>
                                                            @endunless
                                                        </div>
                                                        
                                                        <div class="product-card__content">
                                                            <h6 class="title text-md fw-semibold mb-8">{{ $bProduct->p_name }}</h6>
                                                            <div class="mt-8 mb-8">
                                                                <span class="text-gray-900 text-sm fw-medium mt-8">
                                                                    Category: {{ $bProduct->category->cat_name ?? 'N/A' }}
                                                                    @if($bProduct->p_gender && $bProduct->p_gender !== 'Not Applicable')
                                                                        | Gender: {{ $bProduct->p_gender }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            @if($isInStock && $bProduct->variants->count() > 1)
                                                            <div class="mb-12">
                                                                <select class="form-select form-select-sm border-gray-200" name="variants[{{ $bProduct->p_id }}]">
                                                                    @foreach($bProduct->variants as $variant)
                                                                        @php
                                                                            $vInv = $bProduct->stockInventories->where('prod_variant_id', $variant->prod_variant_id)->first();
                                                                        @endphp
                                                                        <option value="{{ $variant->prod_variant_id }}" data-stock="{{ $vInv ? (int) $vInv->available_stock : 0 }}" data-price="{{ $vInv ? $vInv->unit_price : 0 }}" data-discount="{{ $vInv ? $vInv->discounted_unit_price : '' }}" {{ $defaultVariantId == $variant->prod_variant_id ? 'selected' : '' }}>
                                                                            {{ $variant->prod_variant }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @elseif($isInStock && $defaultVariantId)
                                                                <input type="hidden" name="variants[{{ $bProduct->p_id }}]" value="{{ $defaultVariantId }}">
                                                            @endif
                                                            @if($isInStock)
                                                            <div class="d-flex align-items-center gap-8 mb-12">
                                                                <button type="button" class=" btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white" onclick="changeBundleQty({{ $bundle->b_id }}, {{ $bProduct->p_id }}, -1)">
                                                                    <i class="ph ph-minus"></i>
                                                                </button>
                                                                @php $maxStock = isset($inventory) && $inventory ? (int) $inventory->available_stock : 0; @endphp
                                                                <input type="number" min="1" value="1" max="{{ $maxStock }}" class="form-control form-control-sm w-70 text-center" id="tpl_qty_{{ $bundle->b_id }}_{{ $bProduct->p_id }}">
                                                                <button type="button" class=" btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white" onclick="changeBundleQty({{ $bundle->b_id }}, {{ $bProduct->p_id }}, 1)">
                                                                    <i class="ph ph-plus"></i>
                                                                </button>
                                                            </div>
                                                            @endif
                                                            
                                                            <div class="product-card__price" id="tpl_item_price_{{ $bundle->b_id }}_{{ $bProduct->p_id }}" data-unit-price="{{ $price }}" data-unit-discount="{{ $discountPrice ?? '' }}">
                                                                @if($isInStock)
                                                                    @php $effB = ($discountPrice && $discountPrice < $price) ? $discountPrice : $price; @endphp
                                                                    <span class="fw-semibold">₹{{ number_format($effB, 0) }}</span>
                                                                    <br/>
                                                                    <span><small class="text-gray-500 ms-2">(1 × ₹{{ number_format($effB, 0) }})</small></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="mt-32 pt-24 border-top border-gray-200 flex-between flex-wrap gap-16">
                                            <div class="text-gray-600">
                                                <i class="ph ph-info"></i> Selected products will be added to your cart.
                                            </div>
                                            <div class="text-gray-900 fw-semibold">
                                                Total: ₹<span id="act_bundle_total_{{ $bundle->b_id }}">0</span>
                                            </div>
                                            <button type="button" onclick="submitBundleToCart({{ $bundle->b_id }})" class="btn btn-main rounded-pill px-32 py-12">
                                                Add Selected to Cart <i class="ph ph-shopping-cart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            <!-- End Bundles Loop -->

                            <!-- Container for active expansion -->
                            <div class="col-12 d-none" id="active-bundle-container"></div>
                            @forelse($products as $product)
                            <div class="col-lg-4 col-sm-6">
                                <div
                                    class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                    @php
                                        $totalAvailable = (int) $product->stockInventories->sum('available_stock');
                                        $isOutOfStock = $totalAvailable <= 0;
                                    @endphp
                                    <div class="product-card__thumb flex-center rounded-8 bg-white position-relative {{ $isOutOfStock ? 'opacity-75' : '' }}" @if(!$isOutOfStock) onclick="window.location.href='{{ route('website.product-details', $product->p_id) }}'" style="cursor:pointer" @endif>
                                        <img src="{{ asset('uploads/product-photo/' . $product->p_photo) }}" alt="{{ $product->p_name }}" class="w-auto max-w-unset" style="max-height: 200px; object-fit: contain;">
                                        @if($isOutOfStock)
                                        <span class="position-absolute top-50 start-50 translate-middle badge bg-danger rounded-pill px-16 py-8">OUT OF STOCK</span>
                                        @endif
                                    </div>
                                    <div class="product-card__content mt-16 w-100">
                                        <h6 class="title text-lg fw-semibold mt-12 mb-8">
                                            <a href="{{ route('website.product-details', $product->p_id) }}" class="link text-line-2" tabindex="0">{{ $product->p_name }}</a>
                                        </h6>

                                        <div class="mt-8">
                                            <span class="text-gray-900 text-sm fw-medium mt-8">
                                                Category: {{ $product->category->cat_name ?? 'N/A' }}
                                                @if($product->p_gender && $product->p_gender !== 'Not Applicable')
                                                    | Gender: {{ $product->p_gender }}
                                                @endif
                                            </span>
                                        </div>

                                        @php
                                            $initialPrice = $product->p_price ?? 0;
                                            $initialDiscount = $product->discounted_price ?? null;
                                            $initialVariantId = '';
                                            $initialStock = 0;
                                            $anyInv = optional($product->stockInventories)->sortBy('unit_price')->first();
                                            
                                            if($product->variants->count() > 0) {
                                                $firstVariant = $product->variants->first();
                                                $initialVariantId = $firstVariant->prod_variant_id;
                                                $inventory = $product->stockInventories->where('prod_variant_id', $firstVariant->prod_variant_id)->first();
                                                if(!$inventory){
                                                    $inventory = $product->stockInventories->whereNull('prod_variant_id')->first() ?: $anyInv;
                                                }
                                                if($inventory) {
                                                    $initialPrice = $inventory->unit_price ?: $initialPrice;
                                                    $initialDiscount = $inventory->discounted_unit_price ?? $initialDiscount;
                                                    $initialStock = (int) $inventory->available_stock;
                                                }
                                            } else {
                                                $inventory = $product->stockInventories->whereNull('prod_variant_id')->first() ?: $anyInv;
                                                if($inventory) {
                                                    $initialPrice = $inventory->unit_price ?: $initialPrice;
                                                    $initialDiscount = $inventory->discounted_unit_price ?? $initialDiscount;
                                                    $initialStock = (int) $inventory->available_stock;
                                                }
                                            }
                                            $maxQty = $initialStock > 0 ? $initialStock : 0;
                                        @endphp

                                        <div class="mt-12 mb-12">
                                            @if(!$isOutOfStock && $product->variants->count() > 1)
                                                <select class="form-select form-select-sm border-gray-200 variant-selector w-100 mb-12" 
                                                        data-product-id="{{ $product->p_id }}"
                                                        >
                                                    @foreach($product->variants as $variant)
                                                        @php
                                                            $inventory = $product->stockInventories->where('prod_variant_id', $variant->prod_variant_id)->first();
                                                            if(!$inventory){
                                                                $inventory = $product->stockInventories->whereNull('prod_variant_id')->first() ?: $anyInv;
                                                            }
                                                            $vPrice = $inventory ? ($inventory->unit_price ?: 0) : 0;
                                                            $vDiscount = $inventory ? ($inventory->discounted_unit_price ?? 0) : 0;
                                                        @endphp
                                                        <option value="{{ $variant->prod_variant_id }}" 
                                                                data-price="{{ $vPrice }}" 
                                                                data-discount="{{ $vDiscount }}"
                                                                data-stock="{{ $inventory ? (int) $inventory->available_stock : 0 }}"
                                                                @if($loop->first) selected @endif>
                                                            {{ \Illuminate\Support\Str::limit($variant->prod_variant, 30) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($product->variants->count() == 1)
                                                <input type="hidden" id="hidden-variant-{{ $product->p_id }}" value="{{ $product->variants->first()->prod_variant_id }}">
                                            @endif
                                            @if(!$isOutOfStock)
                                            <div class="row align-items-center g-2">
                                                <div class="col-12 col-md-6">
                                                    <div class="d-flex align-items-center gap-8">
                                                        <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeQty({{ $product->p_id }}, -1)">
                                                            <i class="ph ph-minus"></i>
                                                        </button>
                                                        <input type="number" min="1" value="1" max="{{ $maxQty }}" class="form-control form-control-sm w-70 text-center" id="qty-{{ $product->p_id }}">
                                                        <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeQty({{ $product->p_id }}, 1)">
                                                            <i class="ph ph-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="product-card__price my-20 text-md-end" id="price-container-{{ $product->p_id }}" data-unit-price="{{ $initialPrice }}" data-unit-discount="{{ $initialDiscount ?? '' }}">
                                                        @php $eff = ($initialDiscount && $initialDiscount < $initialPrice) ? $initialDiscount : $initialPrice; @endphp
                                                        <span class="text-heading text-md fw-semibold ">₹{{ number_format($eff, 0) }}</span>
                                                        <small class="text-gray-500 ms-6">(1 × ₹{{ number_format($eff, 0) }})</small>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                        

                                        <!-- Individual Add to Cart Button (Form handled via JS to avoid nesting) -->
                                        <input type="hidden" id="hidden-variant-{{ $product->p_id }}" value="{{ $initialVariantId }}">
                                        @if(!$isOutOfStock)
                                        <button type="button"
                                            class="product-card__cart btn bg-gray-50 text-heading hover-bg-main-600 hover-text-white py-11 px-24 rounded-8 flex-center gap-8 fw-medium w-100"
                                            tabindex="0"
                                            onclick="addToCart({{ $product->p_id }})">
                                            Add To Cart <i class="ph ph-shopping-cart"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-warning">No products found matching your criteria.</div>
                            </div>
                            @endforelse
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-40">
                            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </section>

    <!-- Hidden Bulk Add Form -->
    <form action="{{ route('website.cart.bulk-add') }}" method="POST" id="bulkAddForm">
        @csrf
        @foreach($products as $product)
        <input type="hidden" name="product_ids[]" value="{{ $product->p_id }}">
        <input type="hidden" name="variants[{{ $product->p_id }}]" id="bulk-variant-{{ $product->p_id }}" 
               value="{{ $product->variants->first()->prod_variant_id ?? '' }}">
        @endforeach
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.variant-selector').forEach(select => {
                select.addEventListener('change', function() {
                    const productId = this.dataset.productId;
                    const selectedOption = this.options[this.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price || 0);
                    const discount = parseFloat(selectedOption.dataset.discount || 0);
                    const variantId = this.value;
                    const stock = parseInt(selectedOption.dataset.stock || '0', 10);

                    const container = document.getElementById(`price-container-${productId}`);
                    if (container) {
                        container.dataset.unitPrice = isNaN(price) ? '0' : String(price);
                        container.dataset.unitDiscount = (isNaN(discount) ? '' : String(discount));
                        updateProductCardPrice(productId);
                    }

                    const hiddenInput = document.getElementById(`hidden-variant-${productId}`);
                    if(hiddenInput) hiddenInput.value = variantId;

                    const bulkInput = document.getElementById(`bulk-variant-${productId}`);
                    if(bulkInput) bulkInput.value = variantId;

                    const qtyInput = document.getElementById(`qty-${productId}`);
                    if (qtyInput) {
                        const cap = stock > 0 ? stock : 0;
                        qtyInput.max = cap;
                        let val = parseInt(qtyInput.value || '1', 10);
                        if (isNaN(val) || val < 1) val = 1;
                        if (val > cap) val = cap;
                        qtyInput.value = val;
                    }
                });
            });
            document.querySelectorAll('[id^="price-container-"]').forEach(function(el){
                const id = el.id.replace('price-container-','');
                updateProductCardPrice(id);
            });
        });

        function updateProductCardPrice(productId) {
            const container = document.getElementById(`price-container-${productId}`);
            if (!container) return;
            const up = parseFloat(container.dataset.unitPrice || '0');
            const ud = parseFloat(container.dataset.unitDiscount || '0');
            const qtyInput = document.getElementById(`qty-${productId}`);
            const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value || '1', 10)) : 1;
            const eff = (!isNaN(ud) && ud > 0 && ud < up) ? ud : up;
            const total = eff * qty;
            let html = '';
            if (!isNaN(ud) && ud > 0 && ud < up) {
                html = `<span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹${Math.round(up)}</span>
                        <span class="text-heading text-md fw-semibold ms-6">₹${Math.round(total)}</span>
                        <small class="text-gray-500 ms-6">(${qty} × ₹${Math.round(eff)})</small>`;
            } else {
                html = `<span class="text-heading text-md fw-semibold ">₹${Math.round(total)}</span>
                        <small class="text-gray-500 ms-6">(${qty} × ₹${Math.round(up)})</small>`;
            }
            container.innerHTML = html;
        }

        function addToCart(productId) {
            var variantInput = document.getElementById('hidden-variant-' + productId);
            var variantId = variantInput ? variantInput.value : '';
            var qtyInput = document.getElementById('qty-' + productId);
            var quantity = qtyInput ? parseInt(qtyInput.value || '1', 10) : 1;
            if (isNaN(quantity) || quantity < 1) quantity = 1;

            var baseUrl = "{{ route('website.cart.add', ['id' => 'PLACEHOLDER']) }}";
            var url = baseUrl.replace('PLACEHOLDER', productId);

            var btn = event && event.target ? event.target.closest('button') : null;
            var originalText = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Adding...';
            }

            var fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            if (variantId) fd.append('variant_id', variantId);
            fd.append('quantity', String(quantity));

            fetch(url, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: fd
            }).then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.login_required) {
                    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    return;
                }
                if (data && data.success) {
                    showMiniToast('Item added to cart');
                    var badge = document.getElementById('header-cart-count');
                    if (badge && typeof data.cart_count !== 'undefined') {
                        badge.textContent = String(data.cart_count);
                    }
                } else {
                    showMiniToast(data.message || 'Unable to add to cart', true);
                }
            }).catch(function() {
                showMiniToast('Network error. Try again.', true);
            }).finally(function() {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText || 'Add To Cart';
                }
            });
        }

        function showMiniToast(text, isError) {
            var toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 m-3 px-3 py-2 rounded-8 text-white shadow';
            toast.style.zIndex = '2000';
            toast.style.background = isError ? '#dc3545' : '#198754';
            toast.textContent = text;
            document.body.appendChild(toast);
            setTimeout(function() { toast.remove(); }, 1800);
        }

        let activeBundleId = null;

        function toggleBundleDetails(bundleId, element) {
            const container = document.getElementById('active-bundle-container');
            
            // If clicking the same bundle, close it
            if (activeBundleId === bundleId) {
                closeBundleDetails();
                return;
            }

            activeBundleId = bundleId;
            
            // 1. Find the last element in the current row
            const bundleItem = element.closest('.bundle-item');
            const allItems = Array.from(document.querySelectorAll('.bundle-item'));
            const itemTop = bundleItem.offsetTop;
            
            // Filter items that are in the same row (allow small tolerance for pixel diffs)
            const rowItems = allItems.filter(item => Math.abs(item.offsetTop - itemTop) < 5);
            const lastItemInRow = rowItems[rowItems.length - 1];
            
            // 2. Move container after the last item
            // Check if container is already in the DOM, if so move it.
            // If lastItemInRow is the last child, nextSibling is null, insertBefore appends.
            lastItemInRow.parentNode.insertBefore(container, lastItemInRow.nextSibling);
            
            // 3. Inject content
            const template = document.getElementById(`bundle-details-template-${bundleId}`);
            let html = template.innerHTML;
            
            // Replace template IDs with active IDs to prevent duplicates and ensure proper labeling
            html = html.replace(/id="tpl_/g, 'id="act_');
            html = html.replace(/for="tpl_/g, 'for="act_');
            
            container.innerHTML = html;
            container.classList.remove('d-none');
            
            // Scroll to container slightly
            setTimeout(() => {
                const yOffset = -100; 
                const y = container.getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({top: y, behavior: 'smooth'});
            }, 100);

            initBundleHandlers(bundleId);
            updateBundleTotal(bundleId);
        }

        function closeBundleDetails() {
            const container = document.getElementById('active-bundle-container');
            container.classList.add('d-none');
            container.innerHTML = '';
            activeBundleId = null;
        }

        function filterBundleProducts(bundleId, categoryId, btn) {
            // Update active button state
            const btnContainer = btn.closest('.bundle-filters');
            btnContainer.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active', 'btn-main', 'text-white');
                b.classList.add('btn-outline-main', 'bg-white');
            });
            btn.classList.remove('btn-outline-main', 'bg-white');
            btn.classList.add('active', 'btn-main', 'text-white');

            // Filter items
            const container = document.getElementById(`active-bundle-container`); 
            const items = container.querySelectorAll('.bundle-product-item');
            
            items.forEach(item => {
                if (categoryId === 'all' || item.dataset.category == categoryId) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        }

        function submitBundleToCart(bundleId) {
            const container = document.getElementById(`act_bundleForm${bundleId}`);
            if (!container) {
                console.error('Bundle container not found');
                return;
            }

            const checkboxes = container.querySelectorAll('input[name="product_ids[]"]:checked');
            
            if (checkboxes.length === 0) {
                window.snToast('warning', 'Please select at least one product to add to cart.');
                return;
            }

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            checkboxes.forEach((checkbox) => {
                const productId = checkbox.value;
                
                fd.append('product_ids[]', productId);
                
                // Variant
                const variantSelect = container.querySelector(`select[name="variants[${productId}]"]`);
                const variantInput = container.querySelector(`input[name="variants[${productId}]"]`);
                
                let variantId = null;
                if (variantSelect) {
                    variantId = variantSelect.value;
                } else if (variantInput) {
                    variantId = variantInput.value;
                }
                
                if (variantId) {
                    fd.append(`variants[${productId}]`, variantId);
                }

                // Quantity
                const qtyInput = document.getElementById(`act_qty_${bundleId}_${productId}`);
                let qtyValue = 1;
                if (qtyInput) {
                    const parsed = parseInt(qtyInput.value || '1', 10);
                    qtyValue = isNaN(parsed) || parsed < 1 ? 1 : parsed;
                    const max = parseInt(qtyInput.max || '0', 10);
                    if (max > 0 && qtyValue > max) qtyValue = max;
                }
                fd.append(`quantities[${productId}]`, qtyValue);
            });

            const btn = event && event.target ? event.target.closest('button') : null;
            const original = btn ? btn.innerHTML : null;
            if (btn) { btn.disabled = true; btn.innerHTML = 'Adding...'; }
            fetch("{{ route('website.cart.bulk-add') }}", {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.login_required) {
                    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    return;
                }
                if (data && data.success) {
                    showMiniToast('Selected items added to cart');
                    const badge = document.getElementById('header-cart-count');
                    if (badge && typeof data.cart_count !== 'undefined') {
                        badge.textContent = String(data.cart_count);
                    }
                } else {
                    showMiniToast(data.message || 'Unable to add items', true);
                }
            })
            .catch(() => showMiniToast('Network error. Try again.', true))
            .finally(() => {
                if (btn) { btn.disabled = false; btn.innerHTML = original || 'Add Selected to Cart'; }
            });
        }

        function changeQty(productId, delta) {
            const input = document.getElementById('qty-' + productId);
            if (!input) return;
            let val = parseInt(input.value || '1', 10);
            if (isNaN(val)) val = 1;
            val += delta;
            if (val < 1) val = 1;
            const max = parseInt(input.max || '0', 10);
            if (max > 0 && val > max) val = max;
            input.value = val;
            updateProductCardPrice(productId);
        }

        function changeBundleQty(bundleId, productId, delta) {
            const input = document.getElementById(`act_qty_${bundleId}_${productId}`) || document.getElementById(`tpl_qty_${bundleId}_${productId}`);
            if (!input) return;
            let val = parseInt(input.value || '1', 10);
            if (isNaN(val)) val = 1;
            val += delta;
            if (val < 1) val = 1;
            const max = parseInt(input.max || '0', 10);
            if (max > 0 && val > max) val = max;
            input.value = val;
            updateBundleItemPrice(bundleId, productId);
            updateBundleTotal(bundleId);
        }

        function updateBundleItemPrice(bundleId, productId) {
            // handles both template (tpl_) and active (act_) IDs
            const priceEl = document.getElementById(`act_item_price_${bundleId}_${productId}`) || document.getElementById(`tpl_item_price_${bundleId}_${productId}`);
            if (!priceEl) return;
            const up = parseFloat(priceEl.dataset.unitPrice || '0');
            const ud = parseFloat(priceEl.dataset.unitDiscount || '0');
            const qtyInput = document.getElementById(`act_qty_${bundleId}_${productId}`) || document.getElementById(`tpl_qty_${bundleId}_${productId}`);
            const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value || '1', 10)) : 1;
            const eff = (!isNaN(ud) && ud > 0 && ud < up) ? ud : up;
            const total = eff * qty;
            let html = '';
            if (!isNaN(ud) && ud > 0 && ud < up) {
                html = `<span class="text-gray-400 text-sm fw-semibold text-decoration-line-through">₹${Math.round(up)}</span>
                        <span class="fw-semibold text-main-600 ms-2">₹${Math.round(total)}</span>
                        <small class="text-gray-500 ms-2">(${qty} × ₹${Math.round(eff)})</small>`;
            } else {
                html = `<span class="fw-semibold">₹${Math.round(total)}</span>
                        <small class="text-gray-500 ms-2">(${qty} × ₹${Math.round(up)})</small>`;
            }
            priceEl.innerHTML = html;
        }
        function initActiveBundleEvents(bundleId) {
            const container = document.getElementById('active-bundle-container');
            if (!container) return;
            container.querySelectorAll('select.form-select').forEach(function(sel){
                sel.addEventListener('change', function(){
                    const name = this.getAttribute('name') || '';
                    const match = name.match(/variants\[(\d+)\]/);
                    if (!match) return;
                    const pid = match[1];
                    const opt = this.options[this.selectedIndex];
                    const priceEl = document.getElementById(`act_item_price_${bundleId}_${pid}`);
                    if (priceEl) {
                        priceEl.dataset.unitPrice = opt.dataset.price || '0';
                        priceEl.dataset.unitDiscount = opt.dataset.discount || '';
                        updateBundleItemPrice(bundleId, pid);
                        updateBundleTotal(bundleId);
                    }
                });
            });
            // Checkboxes update total
            container.querySelectorAll('input[name="product_ids[]"]').forEach(function(cb){
                cb.addEventListener('change', function(){
                    updateBundleTotal(bundleId);
                });
            });
        }
        function updateBundleTotal(bundleId) {
            const container = document.getElementById(`active-bundle-container`);
            if (!container) return;
            const form = container.querySelector(`#act_bundleForm${bundleId}`);
            const totalEl = container.querySelector(`#act_bundle_total_${bundleId}`);
            let total = 0;
            const items = container.querySelectorAll('.bundle-product-item');
            items.forEach(item => {
                const checkbox = item.querySelector('input[name="product_ids[]"]');
                if (!checkbox || !checkbox.checked) return;
                const productId = checkbox.value;
                const qtyInput = container.querySelector(`#act_qty_${bundleId}_${productId}`) || container.querySelector(`#tpl_qty_${bundleId}_${productId}`);
                let qty = 1;
                if (qtyInput) {
                    const parsed = parseInt(qtyInput.value || '1', 10);
                    qty = isNaN(parsed) || parsed < 1 ? 1 : parsed;
                }
                let unit = 0;
                const variantSelect = form ? form.querySelector(`select[name="variants[${productId}]"]`) : null;
                if (variantSelect) {
                    const opt = variantSelect.options[variantSelect.selectedIndex];
                    const d = parseFloat(opt.getAttribute('data-discount') || '0');
                    const p = parseFloat(opt.getAttribute('data-price') || '0');
                    unit = d && d > 0 ? d : p;
                } else {
                    const priceTextEl = item.querySelector('.product-card__price');
                    if (priceTextEl) {
                        const text = priceTextEl.innerText.replace(/[^0-9.]/g, ' ').trim();
                        const nums = text.split(/\s+/).map(parseFloat).filter(n => !isNaN(n) && n > 0);
                        if (nums.length > 0) unit = nums[nums.length - 1];
                    }
                }
                total += unit * qty;
            });
            if (totalEl) {
                totalEl.textContent = Math.round(total);
            }
        }

        function initBundleHandlers(bundleId) {
            const container = document.getElementById(`active-bundle-container`);
            if (!container) return;
            const form = container.querySelector(`#act_bundleForm${bundleId}`);
            if (!form) return;
            form.querySelectorAll('input[name="product_ids[]"]').forEach(cb => {
                cb.addEventListener('change', () => updateBundleTotal(bundleId));
            });
            form.querySelectorAll('input[type="number"]').forEach(inp => {
                inp.addEventListener('input', () => updateBundleTotal(bundleId));
                inp.addEventListener('change', () => updateBundleTotal(bundleId));
            });
            form.querySelectorAll('select').forEach(sel => {
                sel.addEventListener('change', () => {
                    // Identify product id from select name
                    const name = sel.getAttribute('name') || '';
                    const m = name.match(/variants\[(\d+)\]/);
                    if (m) {
                        const pid = m[1];
                        const opt = sel.options[sel.selectedIndex];
                        const priceEl = container.querySelector(`#act_item_price_${bundleId}_${pid}`);
                        if (priceEl) {
                            priceEl.dataset.unitPrice = opt.getAttribute('data-price') || '0';
                            priceEl.dataset.unitDiscount = opt.getAttribute('data-discount') || '';
                            updateBundleItemPrice(bundleId, pid);
                        }
                    }
                    updateBundleTotal(bundleId);
                });
            });
        }
    </script>
    
    <script>
        function applyClass(classId) {
            document.getElementById('filter-class').value = classId;
            document.getElementById('shopFilterForm').submit();
        }
    
        function applyCategory(categoryId) {
            document.getElementById('filter-category').value = categoryId;
            document.getElementById('shopFilterForm').submit();
        }
    </script>


@endsection
