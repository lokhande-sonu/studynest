@extends('website.layouts.app')

@section('title', $product->p_name)

@section('content')

    <!-- ========================= Breadcrumb Start =============================== -->
    <div class="breadcrumb mb-0 py-26 bg-main-two-50">
        <div class="container container-lg">
            <div class="breadcrumb-wrapper flex-between flex-wrap gap-16">
                <h6 class="mb-0">Product Details</h6>
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
                        <a href="{{ route('website.shop') }}" class="text-gray-900 hover-text-main-600">Shop</a>
                    </li>
                    <li class="flex-align">
                        <i class="ph ph-caret-right"></i>
                    </li>
                    <li class="text-sm text-main-600"> {{ $product->p_name }} </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ========================= Breadcrumb End =============================== -->

    <!-- ========================== Product Details Two Start =========================== -->
    <section class="product-details py-40">
        <div class="container container-lg">
            <div class="row gy-4">
                <div class="col-xl-9">
                    <div class="row gy-4">
                        <div class="col-xl-6">
                            <div class="product-details__left">

                                <div class="product-details__thumb-slider border border-gray-100 rounded-16 bg-white">
                                    @forelse($product->images as $image)
                                    <div class="">
                                        <div class="product-details__thumb flex-center h-100 p-20">
                                            <img src="{{ asset('uploads/product-photo/' . $image->img_path) }}" alt="{{ $product->p_name }}" style="max-height: 400px; object-fit: contain;">
                                        </div>
                                    </div>
                                    @empty
                                    <div class="">
                                        <div class="product-details__thumb flex-center h-100 p-20">
                                            <img src="{{ asset('uploads/product-photo/' . $product->p_photo) }}" alt="{{ $product->p_name }}" style="max-height: 400px; object-fit: contain;">
                                        </div>
                                    </div>
                                    @endforelse
                                </div>

                                <div class="mt-24">
                                    <div class="product-details__images-slider">
                                        @forelse($product->images as $image)
                                        <div>
                                            <div
                                                class="max-w-120 max-h-120 h-100 flex-center border border-gray-100 rounded-16 p-8 bg-white cursor-pointer">
                                                <img src="{{ asset('uploads/product-photo/' . $image->img_path) }}" alt="{{ $product->p_name }}" style="max-height: 100px; object-fit: contain;">
                                            </div>
                                        </div>
                                        @empty
                                        <div>
                                            <div
                                                class="max-w-120 max-h-120 h-100 flex-center border border-gray-100 rounded-16 p-8 bg-white cursor-pointer">
                                                <img src="{{ asset('uploads/product-photo/' . $product->p_photo) }}" alt="{{ $product->p_name }}" style="max-height: 100px; object-fit: contain;">
                                            </div>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="product-details__content">
                                <h5 class="mb-12">{{ $product->p_name }}</h5>
                                <div class="flex-align flex-wrap gap-12 text-sm text-gray-600">
                                    @if($product->schools->count() > 0)
                                    <span><span class="fw-medium text-gray-900">School:</span> @foreach($product->schools as $school) {{ $school->sch_name }}@if(!$loop->last), @endif @endforeach</span>
                                    <span class="text-gray-200">|</span>
                                    @endif
                                    @if($product->classes->count() > 0)
                                    <span><span class="fw-medium text-gray-900">Class:</span> @foreach($product->classes as $class) {{ $class->class_name }}@if(!$loop->last), @endif @endforeach</span>
                                    <span class="text-gray-200">|</span>
                                    @endif
                                    <span><span class="fw-medium text-gray-900">Category:</span> {{ $product->category->cat_name ?? 'N/A' }}</span>
                                    
                                    @if($product->p_gender && $product->p_gender !== 'Not Applicable')
                                    <span class="text-gray-200">|</span>
                                    <span><span class="fw-medium text-gray-900">Gender:</span> {{ $product->p_gender }}</span>
                                    @endif

                                    <span class="text-gray-200">|</span>
                                    <span><span class="fw-medium text-gray-900">SKU:</span> {{ $product->stockInventories->first()->prod_sku ?? 'N/A' }} </span>
                                </div>

                                <div class="mt-24 pt-24 border-top border-gray-100">
                                    <p class="text-gray-700 leading-relaxed">{{ $product->p_short_desc }}</p>
                                </div>

                                <div class="my-32 flex-align gap-16 flex-wrap">
                                    <div class="flex-align gap-8">
                                        <h4 class="mb-0 text-main-600" id="main-price">₹{{ number_format($product->discounted_price ?? $product->p_price, 0) }}</h4>
                                    </div>
                                    @if($product->discounted_price && $product->discounted_price < $product->p_price)
                                    <div class="flex-align gap-8" id="regular-price-container">
                                        <span class="text-gray-500 text-sm">Regular Price</span>
                                        <h5 class="text-gray-400 mb-0 fw-medium text-decoration-line-through" id="main-regular-price">₹{{ number_format($product->p_price, 0) }}</h5>
                                    </div>
                                    @else
                                    <div class="flex-align gap-8 d-none" id="regular-price-container">
                                        <span class="text-gray-500 text-sm">Regular Price</span>
                                        <h5 class="text-gray-400 mb-0 fw-medium text-decoration-line-through" id="main-regular-price"></h5>
                                    </div>
                                    @endif
                                </div>

                                <div class="mt-32 pt-32 border-top border-gray-100">
                                    <span class="fw-medium text-gray-900 d-block mb-12">100% Guarantee Safe Checkout</span>
                                    <img src="{{ asset('customer-web-assets/images/thumbs/gateway-img.png') }}" alt="Payment Gateways" class="max-w-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-40">
                        <div class="product-dContent border border-gray-100 rounded-24 p-32 bg-white">
                            <h6 class="mb-24 border-bottom border-gray-100 pb-16">Product Description</h6>
                            <div class="text-gray-700 leading-relaxed">
                                {!! $product->p_full_desc !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="product-details__sidebar py-32 px-24 border border-gray-100 rounded-16 bg-white sticky-sidebar">
                        <form id="addToCartForm" action="{{ route('website.cart.add', $product->p_id) }}" method="POST">
                            @csrf
                            <div class="mb-24">
                                @if($product->variants->count() > 1)
                                <label for="variant" class="text-md mb-8 text-heading fw-semibold d-block">Select Variant</label>
                                <select class="form-select border-gray-200 mb-16" name="variant_id" id="variant" required>
                                    @foreach($product->variants as $variant)
                                        @php
                                            $inventory = $product->stockInventories->where('prod_variant_id', $variant->prod_variant_id)->first();
                                            $vPrice = $inventory ? $inventory->unit_price : 0;
                                            $vDiscount = $inventory ? $inventory->discounted_unit_price : null;
                                            $vStock = $inventory ? $inventory->available_stock : 0;
                                        @endphp
                                        <option value="{{ $variant->prod_variant_id }}" 
                                                data-price="{{ $vPrice }}" 
                                                data-discount="{{ $vDiscount }}" 
                                                data-stock="{{ $vStock }}"
                                                @if($loop->first) selected @endif>
                                            {{ $variant->prod_variant }}
                                        </option>
                                    @endforeach
                                </select>
                                @elseif($product->variants->count() == 1)
                                    <input type="hidden" name="variant_id" value="{{ $product->variants->first()->prod_variant_id }}">
                                @endif

                                @if(($product->category && $product->category->cat_name === 'Premium'))
                                <label class="text-md mb-8 text-heading fw-semibold d-block">Customizations</label>
                                <select class="form-select border-gray-200 mb-12" name="customization_option" id="customization_option">
                                    <option value="normal" data-price="0">Normal / Basic</option>
                                    <option value="basic" data-price="999">Basic - Name Sticker (₹999)</option>
                                    <option value="premium" data-price="1199">Premium - Laser Name Print (₹1199)</option>
                                    <option value="platinum" data-price="1499">Platinum - Laser Name Print + GPS Tracking (₹1499)</option>
                                </select>
                                <div id="customization_text_wrapper" class="mb-12 d-none">
                                    <label class="text-sm mb-6 text-heading d-block">Enter Text to Print</label>
                                    <textarea class="form-control border-gray-200" name="customization_text" id="customization_text" rows="2" placeholder="Enter name or text to print"></textarea>
                                </div>
                                @endif

                                <label for="stock" class="text-md mb-8 text-heading fw-semibold d-block">Quantity</label>
                                <div class="d-flex rounded-8 overflow-hidden border border-gray-200">
                                    <button type="button"
                                        class="custom-qty-btn-minus flex-shrink-0 h-44 w-44 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white transition-1">
                                        <i class="ph ph-minus"></i>
                                    </button>
                                    @php 
                                        $initialStock = $product->variants->count() > 0 
                                            ? ($product->stockInventories->where('prod_variant_id', $product->variants->first()->prod_variant_id)->first()->available_stock ?? 0)
                                            : ($product->stockInventories->first()->available_stock ?? 0);
                                        $maxQty = max(0, (int)$initialStock);
                                    @endphp
                                    <input type="number"
                                        class="quantity__input flex-grow-1 border-0 text-center w-32 px-16 fw-medium"
                                        name="quantity" id="stock" value="{{ $maxQty > 0 ? 1 : 0 }}" 
                                        min="{{ $maxQty > 0 ? 1 : 0 }}" 
                                        max="{{ $maxQty }}" 
                                        step="1">
                                    <button type="button"
                                        class="custom-qty-btn-plus flex-shrink-0 h-44 w-44 text-neutral-600 bg-gray-50 flex-center hover-bg-main-600 hover-text-white transition-1">
                                        <i class="ph ph-plus"></i>
                                    </button>
                                </div>
                                <small class="text-gray-500 mt-8 d-block" id="stock-availability">
                                    @if($initialStock > 0)
                                        In Stock: {{ $initialStock }}
                                    @else
                                        <span class="text-danger fw-medium">Out of Stock</span>
                                    @endif
                                </small>
                            </div>

                            <div class="mb-24 pt-16 border-top border-gray-100">
                                <div class="flex-between flex-wrap gap-8">
                                    <span class="text-gray-600 fw-medium">Subtotal</span>
                                    <h5 class="mb-0 text-main-600" id="sidebar-price">₹{{ number_format($product->discounted_price ?? $product->p_price, 0) }}</h5>
                                </div>
                            </div>

                            <button type="submit" id="addToCartBtn" class="btn btn-main flex-center gap-8 rounded-pill py-14 fw-semibold w-100 transition-2" {{ $initialStock <= 0 ? 'disabled' : '' }}>
                                <i class="ph ph-shopping-cart-simple text-xl"></i>
                                {{ $initialStock > 0 ? 'Add To Cart' : 'Out of Stock' }}
                            </button>
                        </form>

                        <div class="mt-32 pt-24 border-top border-gray-100">
                            <div class="flex-align gap-12 mb-16">
                                <span class="w-40 h-40 bg-main-50 text-main-600 rounded-circle flex-center text-xl flex-shrink-0">
                                    <i class="ph ph-truck"></i>
                                </span>
                                <div>
                                    <span class="text-xs text-gray-500 d-block">Delivery by</span>
                                    <span class="text-sm fw-semibold text-heading">StudyNest Logistics</span>
                                </div>
                            </div>
                            <div class="flex-align gap-12">
                                <span class="w-40 h-40 bg-main-50 text-main-600 rounded-circle flex-center text-xl flex-shrink-0">
                                    <i class="ph ph-shield-check"></i>
                                </span>
                                <div>
                                    <span class="text-xs text-gray-500 d-block">Authentic Product</span>
                                    <span class="text-sm fw-semibold text-heading">100% Original</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ========================== Product Details Two End =========================== -->

    <!-- ========================== Similar Product Start ============================= -->
    <section class="new-arrival pb-80">
        <div class="container container-lg">
            <div class="section-heading mb-32">
                <div class="flex-between flex-wrap gap-16">
                    <h5 class="mb-0">You Might Also Need</h5>
                    <div class="flex-align gap-16">
                        <a href="{{ route('website.shop') }}"
                            class="text-sm fw-bold text-main-600 hover-text-decoration-underline">View All Products</a>
                        <div class="flex-align gap-8">
                            <button type="button" id="new-arrival-prev"
                                class="slick-prev slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-left"></i>
                            </button>
                            <button type="button" id="new-arrival-next"
                                class="slick-next slick-arrow flex-center rounded-circle border border-gray-100 hover-border-main-600 text-xl hover-bg-main-600 hover-text-white transition-1">
                                <i class="ph ph-caret-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="new-arrival__slider arrow-style-two">
                @foreach($relatedProducts as $related)
                <div>
                    @php
                        $relInStock = false;
                        $relPrice = $related->p_price;
                        $relDiscount = $related->discounted_price;
                        
                        if($related->variants->count() > 0) {
                            $firstV = $related->variants->first();
                            $relInv = $related->stockInventories->where('prod_variant_id', $firstV->prod_variant_id)->first();
                            if($relInv && $relInv->available_stock > 0) {
                                $relInStock = true;
                                $relPrice = $relInv->unit_price;
                                $relDiscount = $relInv->discounted_unit_price;
                            }
                        } else {
                             $relInv = $related->stockInventories->whereNull('prod_variant_id')->first();
                             if($relInv && $relInv->available_stock > 0) {
                                $relInStock = true;
                                $relPrice = $relInv->unit_price;
                                $relDiscount = $relInv->discounted_unit_price;
                             }
                        }
                    @endphp
                    <div class="product-card h-100 p-16 border border-gray-100 hover-border-main-600 rounded-16 position-relative transition-2 group-item bg-gray">
                        <a href="{{ route('website.product-details', $related->p_id) }}"
                            class="product-card__thumb flex-center rounded-8 bg-white position-relative overflow-hidden">
                            <img src="{{ asset('uploads/product-photo/' . $related->p_photo) }}" alt="{{ $related->p_name }}" class="w-auto transition-2" style="max-height: 200px; object-fit: contain;">
                            @if(!$relInStock)
                                <span class="position-absolute top-50 start-50 translate-middle badge bg-danger rounded-pill px-12 py-6 z-1">OUT OF STOCK</span>
                            @endif
                        </a>
                        <div class="product-card__content mt-16 text-center w-100">
                            <h6 class="title text-md fw-semibold mb-8">
                                <a href="{{ route('website.product-details', $related->p_id) }}" class="link text-line-2">{{ $related->p_name }}</a>
                            </h6>

                            <div class="product-card__price mb-16">
                                @if($relDiscount && $relDiscount < $relPrice)
                                    <span class="text-gray-400 text-sm fw-medium text-decoration-line-through">₹{{ number_format($relPrice, 0) }}</span>
                                    <span class="text-main-600 text-md fw-bold ms-4">₹{{ number_format($relDiscount, 0) }}</span>
                                @else
                                    <span class="text-heading text-md fw-bold">₹{{ number_format($relPrice, 0) }}</span>
                                @endif
                            </div>

                            <a href="{{ route('website.product-details', $related->p_id) }}"
                                class="product-card__cart btn bg-main-50 text-main-600 hover-bg-main-600 hover-text-white py-10 px-20 rounded-pill flex-center gap-8 fw-semibold transition-2 w-100">
                                View Product <i class="ph ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- ========================== Similar Product End ============================= -->

    @push('styles')
    <style>
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 10;
        }
        .product-details__thumb img {
            transition: transform 0.3s ease;
        }
        .product-details__thumb:hover img {
            transform: scale(1.05);
        }
        .nav-submenu {
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
    </style>
    @endpush

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== Meta Pixel: ViewContent Event =====
            if (typeof fbq !== 'undefined') {
                fbq('track', 'ViewContent', {
                    content_name: @json($product->p_name),
                    content_ids: ['{{ $product->p_id }}'],
                    content_type: 'product',
                    value: {{ $product->discounted_price ?? $product->p_price }},
                    currency: 'INR'
                });
            }

            const variantSelect = document.getElementById('variant');
            const quantityInput = document.getElementById('stock');
            const mainPrice = document.getElementById('main-price');
            const mainRegularPrice = document.getElementById('main-regular-price');
            const regularPriceContainer = document.getElementById('regular-price-container');
            const sidebarPrice = document.getElementById('sidebar-price');
            const plusBtn = document.querySelector('.custom-qty-btn-plus');
            const minusBtn = document.querySelector('.custom-qty-btn-minus');
            const customizationSelect = document.getElementById('customization_option');
            const customizationTextWrapper = document.getElementById('customization_text_wrapper');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const stockAvailability = document.getElementById('stock-availability');

            let currentUnitPrice = {{ $product->discounted_price ?? $product->p_price }};
            let customizationSurcharge = 0;

            function updatePricing() {
                let qty = parseInt(quantityInput.value) || 0;
                let totalPrice = (currentUnitPrice + customizationSurcharge) * qty;
                
                if(sidebarPrice) {
                    sidebarPrice.textContent = '₹' + Math.round(totalPrice).toLocaleString('en-IN');
                }
            }

            if (variantSelect) {
                variantSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                    const discount = parseFloat(selectedOption.getAttribute('data-discount')); 
                    const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

                    let effectivePrice = price;
                    if (!isNaN(discount) && discount > 0 && discount < price) {
                        effectivePrice = discount;
                        if(mainPrice) mainPrice.textContent = '₹' + Math.round(discount + customizationSurcharge).toLocaleString('en-IN');
                        if(regularPriceContainer && mainRegularPrice) {
                            mainRegularPrice.textContent = '₹' + Math.round(price).toLocaleString('en-IN');
                            regularPriceContainer.classList.remove('d-none');
                        }
                    } else {
                        if(mainPrice) mainPrice.textContent = '₹' + Math.round(price + customizationSurcharge).toLocaleString('en-IN');
                        if(regularPriceContainer) {
                            regularPriceContainer.classList.add('d-none');
                        }
                    }
                    
                    currentUnitPrice = effectivePrice;

                    // Update Max to actual available stock
                    const allowedMax = stock;
                    quantityInput.setAttribute('max', allowedMax);
                    
                    // Enable/Disable quantity buttons based on stock
                    if (plusBtn) plusBtn.disabled = (stock <= 0);
                    if (minusBtn) minusBtn.disabled = (stock <= 0);
                    
                    // Update Stock Label
                    if (stockAvailability) {
                        if (stock > 0) {
                            stockAvailability.innerHTML = `In Stock: ${stock}`;
                            addToCartBtn.disabled = false;
                            addToCartBtn.innerHTML = '<i class="ph ph-shopping-cart-simple text-xl"></i> Add To Cart';
                            if (parseInt(quantityInput.value) === 0) quantityInput.value = 1;
                        } else {
                            stockAvailability.innerHTML = '<span class="text-danger fw-medium">Out of Stock</span>';
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Out of Stock';
                            quantityInput.value = 0;
                        }
                    }

                    // Validate Quantity
                    let currentQty = parseInt(quantityInput.value) || 0;
                    if (currentQty > allowedMax) {
                        quantityInput.value = allowedMax; 
                    }
                    if (stock > 0 && currentQty < 1) {
                        quantityInput.value = 1;
                    }

                    updatePricing();
                });
                
                // Initial trigger
                variantSelect.dispatchEvent(new Event('change'));
            }

            if (customizationSelect) {
                customizationSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const surcharge = parseFloat(selected.getAttribute('data-price')) || 0;
                    customizationSurcharge = surcharge;
                    
                    if (this.value !== 'normal') {
                        customizationTextWrapper && customizationTextWrapper.classList.remove('d-none');
                    } else {
                        customizationTextWrapper && customizationTextWrapper.classList.add('d-none');
                    }

                    // Update main price display
                    const selectedOption = variantSelect ? variantSelect.options[variantSelect.selectedIndex] : null;
                    const price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price')) || 0 : ({{ $product->p_price ?? 0 }});
                    const discount = selectedOption ? parseFloat(selectedOption.getAttribute('data-discount')) : ({{ $product->discounted_price ?? 0 }});
                    
                    let effective = price;
                    if (!isNaN(discount) && discount > 0 && discount < price) {
                        effective = discount;
                    }
                    
                    if(mainPrice) mainPrice.textContent = '₹' + Math.round(effective + customizationSurcharge).toLocaleString('en-IN');
                    updatePricing();
                });
            }

            // Custom AJAX submission for Add to Cart
            document.getElementById('addToCartForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const btn = addToCartBtn;
                const originalContent = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-8"></span> Adding...';

                const fd = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                })
                .then(r => r.json())
                .then(data => {
                    if (data.login_required) {
                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                        loginModal.show();
                        return;
                    }
                    if (data.success) {
                        window.snToast('success', data.message || 'Product added to cart');
                        const badge = document.getElementById('header-cart-count');
                        if (badge && typeof data.cart_count !== 'undefined') {
                            badge.textContent = data.cart_count;
                        }

                        // ===== Meta Pixel: AddToCart Event =====
                        if (typeof fbq !== 'undefined') {
                            let qty = parseInt(quantityInput.value) || 1;
                            fbq('track', 'AddToCart', {
                                content_name: @json($product->p_name),
                                content_ids: ['{{ $product->p_id }}'],
                                content_type: 'product',
                                value: currentUnitPrice * qty,
                                currency: 'INR',
                                num_items: qty
                            });
                        }
                    } else {
                        window.snToast('error', data.message || 'Unable to add product');
                    }
                })
                .catch(() => window.snToast('error', 'Network error. Please try again.'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                });
            });

            // Quantity buttons - prevent double firing with proper debounce
            let plusDebounceTimer = null;
            let minusDebounceTimer = null;
            
            if(plusBtn) {
                plusBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if(this.disabled) return;
                    
                    // Clear any pending debounce
                    if(plusDebounceTimer) clearTimeout(plusDebounceTimer);
                    
                    let max = parseInt(quantityInput.getAttribute('max')) || 0;
                    let val = parseInt(quantityInput.value) || 0;
                    
                    // Strict check - don't go beyond max
                    if(val >= max) {
                        window.snToast('warning', 'Only ' + max + ' units available in stock.');
                        return;
                    }
                    
                    // Increment and update
                    let newVal = Math.min(val + 1, max);
                    quantityInput.value = newVal;
                    updatePricing();
                    
                    // Debounce for 300ms
                    this.disabled = true;
                    plusDebounceTimer = setTimeout(() => {
                        if(max > newVal) this.disabled = false;
                    }, 300);
                });
            }
            
            if(minusBtn) {
                minusBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if(this.disabled) return;
                    
                    // Clear any pending debounce
                    if(minusDebounceTimer) clearTimeout(minusDebounceTimer);
                    
                    let min = parseInt(quantityInput.getAttribute('min')) || 1;
                    let val = parseInt(quantityInput.value) || 0;
                    
                    // Strict check - don't go below min
                    if(val <= min) return;
                    
                    // Decrement and update
                    let newVal = Math.max(val - 1, min);
                    quantityInput.value = newVal;
                    updatePricing();
                    
                    // Debounce for 300ms
                    this.disabled = true;
                    minusDebounceTimer = setTimeout(() => {
                        if(newVal > min) this.disabled = false;
                    }, 300);
                });
            }
            
            // Allow manual input with validation
            quantityInput.addEventListener('change', function() {
                let val = parseInt(this.value) || 0;
                let min = parseInt(this.getAttribute('min')) || 1;
                let max = parseInt(this.getAttribute('max')) || 5;
                
                if(val < min) val = min;
                if(val > max) val = max;
                
                this.value = val;
                updatePricing();
            });
        });
    </script>
@endsection
