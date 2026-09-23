@extends('website.layouts.app')

@section('title', $bundle->b_name . ' - Bundle - StudyNest')

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Bundle Details</h6>
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
                    <li class="text-sm">
                        <a href="{{ route('website.bundles') }}" class="text-gray-900 flex-align gap-8 hover-text-main-600">
                            Bundles
                        </a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm text-main-600">{{ Str::limit($bundle->b_name, 30) }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <section class="py-24">
        <div class="container">
            <!-- Bundle Header -->
            <div class="row gy-4 mb-32">
                <div class="col-lg-5">
                    <div class="border border-gray-100 rounded-16 p-24 bg-white">
                        <div class="position-relative">
                            <img src="{{ asset('uploads/bundle/' . $bundle->b_image) }}" alt="{{ $bundle->b_name }}" class="w-100 rounded-12" style="max-height: 400px; object-fit: contain;">
                            <span class="badge bg-main-600 text-white position-absolute top-0 start-0 m-16 px-12 py-8 rounded-4">
                                {{ $bundle->products->count() }} Products
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="ps-lg-24">
                        <h2 class="fw-bold mb-16">{{ $bundle->b_name }}</h2>
                        <p class="text-gray-600 text-lg mb-24">{{ $bundle->b_short_desc }}</p>

                        <!-- School/Class/Subject Info -->
                        <div class="d-flex flex-wrap gap-16 mb-24">
                            @if($bundle->schools->count() > 0)
                                <div class="d-flex align-items-center gap-8 text-gray-600">
                                    <i class="ph ph-school text-main-600"></i>
                                    <span>{{ $bundle->schools->pluck('sch_name')->implode(', ') }}</span>
                                </div>
                            @endif
                            @if($bundle->classes->count() > 0)
                                <div class="d-flex align-items-center gap-8 text-gray-600">
                                    <i class="ph ph-student text-main-600"></i>
                                    <span>{{ $bundle->classes->pluck('class_name')->implode(', ') }}</span>
                                </div>
                            @endif
                            @if($bundle->subjects->count() > 0)
                                <div class="d-flex align-items-center gap-8 text-gray-600">
                                    <i class="ph ph-book text-main-600"></i>
                                    <span>{{ $bundle->subjects->pluck('subject_name')->implode(', ') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Total Price Preview -->
                        @php
                            $totalPrice = 0;
                            foreach($bundle->products as $bProduct) {
                                $price = $bProduct->p_price;
                                $discountPrice = $bProduct->discounted_price;
                                if($bProduct->variants->count() > 0) {
                                    $firstVariant = $bProduct->variants->first();
                                    $inventory = $bProduct->stockInventories->where('prod_variant_id', $firstVariant->prod_variant_id)->first();
                                    if($inventory) {
                                        $price = $inventory->unit_price;
                                        $discountPrice = $inventory->discounted_unit_price;
                                    }
                                } else {
                                    $inventory = $bProduct->stockInventories->whereNull('prod_variant_id')->first();
                                    if($inventory) {
                                        $price = $inventory->unit_price;
                                        $discountPrice = $inventory->discounted_unit_price;
                                    }
                                }
                                $totalPrice += ($discountPrice && $discountPrice < $price) ? $discountPrice : $price;
                            }
                        @endphp

                        <div class="border-top border-gray-100 pt-24 mb-24">
                            <div class="d-flex align-items-baseline gap-16 mb-16">
                                <span class="text-gray-600">Bundle Value:</span>
                                <span class="text-2xl fw-bold text-main-600">₹{{ number_format($totalPrice, 0) }}</span>
                            </div>
                            <p class="text-gray-500 text-sm">
                                <i class="ph ph-info me-4"></i>
                                Select products below to customize your bundle
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-16 align-items-center justify-content-between mb-24">
                        <h4 class="fw-bold mb-0">Products in this Bundle</h4>

                        <!-- Category Filters -->
                        @php
                            $bundleCategories = $bundle->products->pluck('category')->unique('cat_id')->filter();
                        @endphp
                        @if($bundleCategories->count() > 0)
                            <div class="d-flex flex-wrap gap-8 align-items-center">
                                <span class="text-gray-600 text-sm">Filter:</span>
                                <button type="button" class="btn btn-sm btn-main rounded-pill filter-btn active" onclick="filterProducts('all', this)">All</button>
                                @foreach($bundleCategories as $cat)
                                    <button type="button" class="btn btn-sm btn-outline-main bg-white rounded-pill filter-btn" onclick="filterProducts({{ $cat->cat_id }}, this)">
                                        {{ $cat->cat_name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Add to Cart Form -->
                    <form id="bundleForm" action="{{ route('website.cart.bulk-add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bundle_id" value="{{ $bundle->b_id }}">

                        <div class="row gy-4 products-grid">
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
                                            $isInStock = true;
                                        }
                                    }
                                    $effectivePrice = ($discountPrice && $discountPrice < $price) ? $discountPrice : $price;
                                @endphp

                                <div class="col-lg-3 col-md-4 col-sm-6 product-item" data-category="{{ $bProduct->category->cat_id ?? 'other' }}">
                                    <div class="border border-gray-100 rounded-16 p-16 h-100 bg-white {{ !$isInStock ? 'opacity-60' : '' }}">
                                        <!-- Checkbox -->
                                        <div class="d-flex align-items-start justify-content-between mb-12">
                                            <div class="form-check">
                                                <input class="form-check-input product-checkbox" type="checkbox" name="product_ids[]" value="{{ $bProduct->p_id }}"
                                                    {{ $isInStock ? 'checked' : 'disabled' }}
                                                    id="bp_{{ $bProduct->p_id }}"
                                                    data-price="{{ $effectivePrice }}"
                                                    onchange="updateTotal()">
                                            </div>
                                            @if(!$isInStock)
                                                <span class="badge bg-danger rounded-pill px-8 py-4 text-xs">Out of Stock</span>
                                            @endif
                                        </div>

                                        <!-- Product Image -->
                                        <a href="{{ route('website.product-details', $bProduct->p_id) }}" class="d-block mb-16">
                                            <div class="flex-center rounded-8 bg-gray-50" style="height: 150px;">
                                                <img src="{{ asset('uploads/product-photo/' . $bProduct->p_photo) }}" alt="{{ $bProduct->p_name }}" class="rounded-8" style="max-height: 130px; object-fit: contain;">
                                            </div>
                                        </a>

                                        <!-- Product Info -->
                                        <div class="text-center mb-12">
                                            <h6 class="fw-medium text-sm mb-4">
                                                <a href="{{ route('website.product-details', $bProduct->p_id) }}" class="text-heading hover-text-main-600">{{ Str::limit($bProduct->p_name, 35) }}</a>
                                            </h6>
                                            <p class="text-gray-500 text-xs">{{ $bProduct->category->cat_name ?? 'N/A' }}</p>
                                        </div>

                                        @if($isInStock)
                                            <!-- Variant Selection -->
                                            @if($bProduct->variants->count() > 1)
                                                <select class="form-select form-select-sm border-gray-200 mb-12" name="variants[{{ $bProduct->p_id }}]" onchange="updateVariantPrice(this, {{ $bProduct->p_id }})">
                                                    @foreach($bProduct->variants as $variant)
                                                        @php
                                                            $vInv = $bProduct->stockInventories->where('prod_variant_id', $variant->prod_variant_id)->first();
                                                            $vPrice = $vInv ? ($vInv->discounted_unit_price ?? $vInv->unit_price) : 0;
                                                        @endphp
                                                        <option value="{{ $variant->prod_variant_id }}" data-price="{{ $vPrice }}" {{ $defaultVariantId == $variant->prod_variant_id ? 'selected' : '' }}>
                                                            {{ $variant->prod_variant }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($defaultVariantId)
                                                <input type="hidden" name="variants[{{ $bProduct->p_id }}]" value="{{ $defaultVariantId }}">
                                            @endif

                                            <!-- Quantity -->
                                            <div class="d-flex justify-content-center align-items-center gap-8 mb-12">
                                                <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeQty({{ $bProduct->p_id }}, -1)">
                                                    <i class="ph ph-minus text-sm"></i>
                                                </button>
                                                @php $maxStock = isset($inventory) && $inventory ? (int) $inventory->available_stock : 0; @endphp
                                                <input type="number" name="quantities[{{ $bProduct->p_id }}]" min="1" value="1" max="{{ $maxStock }}" class="form-control form-control-sm w-60 text-center px-4" id="qty_{{ $bProduct->p_id }}" onchange="updateTotal()">
                                                <button type="button" class="btn btn-sm border border-gray-200 w-32 h-32 flex-center rounded-6 text-neutral-600 bg-gray-50 hover-bg-main-600 hover-text-white" onclick="changeQty({{ $bProduct->p_id }}, 1)">
                                                    <i class="ph ph-plus text-sm"></i>
                                                </button>
                                            </div>
                                        @endif

                                        <!-- Price -->
                                        <div class="text-center">
                                            @if($isInStock)
                                                <span class="text-main-600 fw-semibold">₹<span id="price_{{ $bProduct->p_id }}" data-unit-price="{{ $effectivePrice }}">{{ number_format($effectivePrice, 0) }}</span></span>
                                                <span class="text-gray-500 text-xs">/unit</span>
                                            @else
                                                <span class="text-danger text-sm">Out of Stock</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Sticky Bottom Bar -->
                        <div class="position-sticky bottom-0 bg-white border-top border-gray-100 p-16 mt-32 rounded-12 shadow-sm" style="z-index: 100;">
                            <div class="container">
                                <div class="flex-between flex-wrap gap-16">
                                    <div class="text-gray-600">
                                        <i class="ph ph-info me-4"></i>
                                        <span id="selectedCount">{{ $bundle->products->count() }}</span> products selected
                                    </div>
                                    <div class="d-flex align-items-center gap-24">
                                        <div class="text-gray-900 fw-bold">
                                            Total: <span class="text-main-600 text-xl">₹<span id="totalPrice">{{ number_format($totalPrice, 0) }}</span></span>
                                        </div>
                                        <button type="submit" class="btn btn-main rounded-pill px-40 py-14">
                                            Add Selected to Cart <i class="ph ph-shopping-cart ms-8"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Related Bundles -->
            @if($relatedBundles->count() > 0)
                <div class="mt-64 pt-32 border-top border-gray-100">
                    <h4 class="fw-bold mb-24">Related Bundles</h4>
                    <div class="row gy-4">
                        @foreach($relatedBundles as $relatedBundle)
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2">
                                    <span class="product-card__badge bg-main-600 text-white text-sm fw-medium rounded-4 px-8 py-4 position-absolute inset-inline-start-0 inset-block-start-0 m-16 z-1">
                                        {{ $relatedBundle->products->count() }} Products
                                    </span>
                                    <a href="{{ route('website.bundle-details', $relatedBundle->b_id) }}" class="d-block">
                                        <div class="product-card__thumb flex-center rounded-8 bg-white mb-16">
                                            <img src="{{ asset('uploads/bundle/' . $relatedBundle->b_image) }}" alt="{{ $relatedBundle->b_name }}" class="w-auto" style="max-height: 150px; object-fit: contain;">
                                        </div>
                                        <div class="product-card__content">
                                            <h6 class="title text-md fw-semibold mb-8 text-heading hover-text-main-600">{{ Str::limit($relatedBundle->b_name, 40) }}</h6>
                                            <p class="text-gray-500 text-xs">{{ Str::limit($relatedBundle->b_short_desc, 50) }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('scripts')
<script>
    function filterProducts(categoryId, btn) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'btn-main');
            b.classList.add('btn-outline-main', 'bg-white');
        });
        btn.classList.remove('btn-outline-main', 'bg-white');
        btn.classList.add('active', 'btn-main');

        // Filter products
        const products = document.querySelectorAll('.product-item');
        products.forEach(product => {
            if (categoryId === 'all' || product.dataset.category == categoryId) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }

    function changeQty(productId, change) {
        const input = document.getElementById('qty_' + productId);
        const currentVal = parseInt(input.value) || 1;
        const maxVal = parseInt(input.max) || 5;
        let newVal = currentVal + change;

        if (newVal < 1) newVal = 1;
        if (newVal > maxVal) newVal = maxVal;

        input.value = newVal;
        updateTotal();
    }

    function updateVariantPrice(select, productId) {
        const newPrice = parseFloat(select.options[select.selectedIndex].dataset.price) || 0;
        const priceEl = document.getElementById('price_' + productId);
        priceEl.textContent = Math.round(newPrice);
        priceEl.dataset.unitPrice = newPrice;
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        let selectedCount = 0;
        const products = document.querySelectorAll('.product-item');

        products.forEach(product => {
            const checkbox = product.querySelector('.product-checkbox');
            if (checkbox && checkbox.checked && !checkbox.disabled) {
                const productId = checkbox.value;
                const qtyInput = document.getElementById('qty_' + productId);
                const priceEl = document.getElementById('price_' + productId);

                if (qtyInput && priceEl) {
                    const qty = parseInt(qtyInput.value) || 1;
                    const price = parseFloat(priceEl.dataset.unitPrice) || 0;
                    total += qty * price;
                    selectedCount++;
                }
            }
        });

        document.getElementById('totalPrice').textContent = Math.round(total);
        document.getElementById('selectedCount').textContent = selectedCount;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotal();
    });
</script>
@endsection
