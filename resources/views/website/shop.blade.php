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
                            <a href="{{ route('website.shop') }}" class="text-gray-900 hover-text-main-600">Product Shop</a>
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
            <form action="{{ route('website.shop') }}" method="GET" id="shopFilterForm">
                <div class="row">

                    <!-- Sidebar Start -->
                    <div class="col-lg-3">
                        <div class="shop-sidebar">
                            <button type="button"
                                class="shop-sidebar__close d-lg-none d-flex w-32 h-32 flex-center border border-gray-100 rounded-circle hover-bg-main-600 position-absolute inset-inline-end-0 me-10 mt-8 hover-text-white hover-border-main-600">
                                <i class="ph ph-x"></i>
                            </button>

                            @if(request()->hasAny(['school', 'class', 'subject', 'category', 'gender', 'sort_by', 'search']))
                            <div class="mb-32">
                                <a href="{{ route('website.shop') }}" class="w-100 btn btn-main text-white rounded-pill py-14 px-24 flex-center gap-8">
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
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">
                                    Filter by Class
                                </h6>
                                <div class="mb-0">
                                    <select class="common-input form-select" name="class" onchange="this.form.submit()">
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->class_id }}" {{ request('class') == $class->class_id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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
                            <div class="shop-sidebar__box border border-gray-100 rounded-8 p-32 mb-32">
                                <h6 class="text-xl border-bottom border-gray-100 pb-24 mb-24">
                                    Filter by Category
                                </h6>
                                <ul class="max-h-540 overflow-y-auto scroll-sm">
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="category" id="cat_all" value="" onchange="this.form.submit()" {{ request('category') == '' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_all">All Categories</label>
                                        </div>
                                    </li>
                                    @foreach($categories as $category)
                                    <li class="mb-24">
                                        <div class="form-check common-check common-radio">
                                            <input class="form-check-input" type="radio" name="category" id="cat_{{ $category->cat_id }}" value="{{ $category->cat_id }}" onchange="this.form.submit()" {{ request('category') == $category->cat_id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="cat_{{ $category->cat_id }}">{{ $category->cat_name }}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

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
                    </div>
                    <!-- Sidebar End -->

                    <!-- Content Start -->
                    <div class="col-lg-9">
                        <!-- Top Start -->
                        <div class="flex-between gap-16 flex-wrap mb-40 ">
                            <span class="text-gray-900">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} result</span>
                            <div class="position-relative flex-align gap-16 flex-wrap">

                                <div class="list-grid-btns flex-align gap-16">
                                    <button type="submit" form="bulkAddForm"
                                        class="w-100 h-44 flex-center border border-gray-100 rounded-6 text-2xl btn-main text-white ps-10 pe-10">
                                        <i class="ph ph-shopping-cart"></i> <span class="text-16 ps-10">Bulk Add To Cart</span>
                                    </button>
                                </div>
                                <div class="position-relative text-gray-500 flex-align gap-4 text-14">
                                    <label for="sorting" class="text-inherit flex-shrink-0">Sort by: </label>
                                    <select class="form-control common-input px-14 py-14 text-inherit rounded-6 w-auto"
                                        id="sorting" name="sort_by" onchange="this.form.submit()">
                                        <option value="" selected disabled>Sort By</option>
                                        <option value="price_low_high" {{ request('sort_by') == 'price_low_high' ? 'selected' : '' }}>Price Low to High</option>
                                        <option value="price_high_low" {{ request('sort_by') == 'price_high_low' ? 'selected' : '' }}>Price High to Low</option>
                                    </select>
                                </div>
                                <button type="button"
                                    class="w-44 h-44 d-lg-none d-flex flex-center border border-gray-100 rounded-6 text-2xl sidebar-btn"><i
                                        class="ph-bold ph-funnel"></i></button>
                            </div>
                        </div>
                        <!-- Top End -->

                        <div class="row gy-4">
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
                                                if(!$inventory) {
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
                                            $maxQty = $initialStock > 0 ? min($initialStock, 5) : 5;
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
                                        <div class="product-card__price my-20 text-md-end" id="price-container-{{ $product->p_id }}" data-unit-price="{{ $initialPrice }}" data-unit-discount="{{ $initialDiscount ?? 0 }}">
                                            @php
                                                $eff = ($initialDiscount && $initialDiscount < $initialPrice) ? $initialDiscount : $initialPrice;
                                            @endphp
                                            <span class="text-heading text-md fw-semibold ">₹{{ number_format($eff, 2) }}</span>
                                            <br/>
                                            <small class="text-gray-500 ms-6">(1 × ₹{{ number_format($eff, 2) }})</small>
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
            </form>
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
                html = `<span class="text-gray-400 text-md fw-semibold text-decoration-line-through">₹${up.toFixed(2)}</span>
                        <span class="text-heading text-md fw-semibold ms-6">₹${total.toFixed(2)}</span>
                        <small class="text-gray-500 ms-6">(${qty} × ₹${eff.toFixed(2)})</small>`;
            } else {
                html = `<span class="text-heading text-md fw-semibold ">₹${total.toFixed(2)}</span>
                        <small class="text-gray-500 ms-6">(${qty} × ₹${up.toFixed(2)})</small>`;
            }
            container.innerHTML = html;
        }

        function addToCart(productId) {
            const variantInput = document.getElementById('hidden-variant-' + productId);
            const variantId = variantInput ? variantInput.value : '';
            const qtyInput = document.getElementById('qty-' + productId);
            let quantity = qtyInput ? parseInt(qtyInput.value || '1', 10) : 1;
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            const baseUrl = "{{ route('website.cart.add', ['id' => 'PLACEHOLDER']) }}";
            const url = baseUrl.replace('PLACEHOLDER', productId);

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            if (variantId) fd.append('variant_id', variantId);
            fd.append('quantity', String(quantity));
            const originalText = event && event.target && event.target.closest('button') ? event.target.closest('button').innerHTML : null;
            const btn = event && event.target && event.target.closest('button') ? event.target.closest('button') : null;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = 'Adding...';
            }
            fetch(url, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: fd
            }).then(r => r.json())
            .then(data => {
                if (data && data.login_required) {
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    return;
                }
                if (data && data.success) {
                    showMiniToast('Item added to cart');
                    const badge = document.getElementById('header-cart-count');
                    if (badge && typeof data.cart_count !== 'undefined') {
                        badge.textContent = String(data.cart_count);
                    }
                } else {
                    showMiniToast(data.message || 'Unable to add to cart', true);
                }
            }).catch(() => {
                showMiniToast('Network error. Try again.', true);
            }).finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText || 'Add To Cart';
                }
            });
        }

        function showMiniToast(text, isError) {
            let toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 m-3 px-3 py-2 rounded-8 text-white shadow';
            toast.style.zIndex = '2000';
            toast.style.background = isError ? '#dc3545' : '#198754';
            toast.textContent = text;
            document.body.appendChild(toast);
            setTimeout(() => { toast.remove(); }, 1800);
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
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
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
                html = `<span class="text-gray-400 text-sm fw-semibold text-decoration-line-through">₹${up.toFixed(2)}</span>
                        <span class="fw-semibold text-main-600 ms-2">₹${total.toFixed(2)}</span>
                        <small class="text-gray-500 ms-2">(${qty} × ₹${eff.toFixed(2)})</small>`;
            } else {
                html = `<span class="fw-semibold">₹${total.toFixed(2)}</span>
                        <small class="text-gray-500 ms-2">(${qty} × ₹${up.toFixed(2)})</small>`;
            }
            priceEl.innerHTML = html;
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
        // Init totals on load
        document.addEventListener('DOMContentLoaded', function(){
            // ===== Meta Pixel: Search Event (fires only when search query is present) =====
            @if(request('search'))
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Search', {
                    search_string: @json(request('search')),
                    content_category: @json(request('category') ? ($categories->firstWhere('cat_id', request('category'))->cat_name ?? '') : 'All'),
                    content_ids: [
                        @foreach($products->take(10) as $p)
                            '{{ $p->p_id }}'{{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    content_type: 'product'
                });
            }
            @endif

            document.querySelectorAll('[id^="price-container-"]').forEach(function(el){
                const id = el.id.replace('price-container-','');
                updateProductCardPrice(id);
            });
            // Update on variant change to recalc totals instead of replacing HTML
            document.querySelectorAll('.variant-selector').forEach(function(select){
                select.addEventListener('change', function(){
                    const productId = this.dataset.productId;
                    const opt = this.options[this.selectedIndex];
                    const price = parseFloat(opt.dataset.price || '0');
                    const discount = parseFloat(opt.dataset.discount || '0');
                    const container = document.getElementById(`price-container-${productId}`);
                    if (container) {
                        container.dataset.unitPrice = isNaN(price) ? '0' : String(price);
                        container.dataset.unitDiscount = (isNaN(discount) ? '' : String(discount));
                        updateProductCardPrice(productId);
                    }
                    const hiddenInput = document.getElementById(`hidden-variant-${productId}`);
                    if (hiddenInput) hiddenInput.value = this.value;
                });
            });
        });
    </script>

@endsection
