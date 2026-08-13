<!-- ======================= Middle Top Start ========================= -->
<div class="header-top bg-main-600 flex-between p-4">
    <div class="container">
        <div class="flex-between flex-wrap gap-8">

            <div class="d-flex align-items-center gap-10">
                <span class="text-sm fw-medium text-white"><span
                        class="text-yellow"> School Essentials Made Easy&nbsp</span> — Accurate Kits Delivered to Your Doorstep</span>

            </div>

            <ul class="header-top__right flex-align flex-wrap gap-16 w-auto d-none d-md-flex">
                <li class="pe-12">
                    <a href="javascript:void(0)"
                        class="text-white text-sm d-flex align-items-center gap-4 hover-text-decoration-underline"
                        onclick="openTrackModal()">
                        <img src="{{ asset('customer-web-assets/images/icon/track-icon.png') }}" alt="Track Icon">
                        <span class="">Track Your Order</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- ======================= Middle Top End ========================= -->

<!-- ======================= Middle Header Start ========================= -->
<header class="header-middle border-bottom border-gray-100">
    <div class="container">
        <nav class="header-inner flex-between gap-8">
            <!-- Logo Start -->
            <div class="logo">
                <a href="{{ url('/') }}" class="link">
                    <img src="{{ asset('customer-web-assets/images/logo/logo.png') }}" alt="Logo">
                </a>
            </div>
            <!-- Logo End  -->

            <!-- form location Start -->
            <!-- <form action="{{ route('website.shop') }}" method="GET" class="flex-align flex-wrap form-location-wrapper max-w-840 w-100">
                <div class="search-category select-style-one d-flex select-border-end-0 search-form d-sm-flex d-none text-heading-two text-sm w-100">
                    <select class="js-example-basic-single border border-neutral-40 border-end-0" name="category">
                        @php
                            $headerCategories = \App\Models\ProductCategory::where('cat_status', 1)->get();
                        @endphp
                        <option value="" selected>All Categories</option>
                        @foreach($headerCategories as $category)
                            <option value="{{ $category->cat_id }}">{{ $category->cat_name }}</option>
                        @endforeach
                    </select>

                    <div class="search-form__wrapper position-relative border-half-start flex-grow-1">
                        <input type="text"
                            class="common-input border-neutral-40 py-18 ps-16 pe-76 rounded-0 rounded-end pe-44 placeholder-italic placeholder-text-sm border-start-0"
                            name="search" id="header-search-input"
                            placeholder="What would you like to buy today?">
                        <div id="header-search-suggestions" class="position-absolute w-100 bg-white border border-gray-100 rounded-8 mt-2 d-none" style="z-index: 1050;">
                        </div>
                        <button type="submit"
                            class="w-64 h-44 bg-main-600 hover-bg-main-800 rounded-4 flex-center text-xl text-white position-absolute top-50 translate-middle-y inset-inline-end-0 me-6"><i
                                class="ph ph-magnifying-glass"></i></button>
                    </div>
                </div>
            </form> -->
            <!-- form location start -->

            <!-- Header Middle Right start -->
            <div class="header-right flex-align flex-shrink-0">
                <div class="flex-align gap-20">
                    <!--<button type="button" class="search-icon flex-align d-lg-none d-flex gap-4 item-hover">-->
                    <!--    <span class="text-2xl text-gray-700 d-flex position-relative item-hover__text">-->
                    <!--        <i class="ph ph-magnifying-glass"></i>-->
                    <!--    </span>-->
                    <!--</button>-->

                    @auth
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="flex-align gap-4 item-hover" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="text-xl text-gray-700 d-flex position-relative item-hover__text">
                                <i class="ph ph-user"></i>
                            </span>
                            <span class="text-md text-heading-three item-hover__text d-none d-lg-flex">{{ Auth::user()->cust_name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('website.orders.index') }}">My Orders</a></li>
                            <li><a class="dropdown-item" href="{{ route('website.logout') }}">Logout</a></li>
                        </ul>
                    </div>
                    @else
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#loginModal"
                        class="flex-align gap-4 item-hover">
                        <span class="text-xl text-gray-700 d-flex position-relative item-hover__text">
                            <i class="ph ph-user"></i>
                        </span>
                        <span class="text-md text-heading-three item-hover__text d-none d-lg-flex">Account</span>
                    </a>
                    @endauth

                    <a href="{{ route('website.cart') }}" class="flex-align gap-4 item-hover">
                        <span class="text-xl text-gray-700 d-flex position-relative me-6 mt-6 item-hover__text">
                            <i class="ph ph-shopping-cart-simple"></i>
                            <span
                                id="header-cart-count"
                                class="w-16 h-16 flex-center rounded-circle bg-main-800 text-xs position-absolute top-n6 end-n4">
                                @auth
                                    {{ \App\Models\CartItem::where('cust_id', Auth::id())->count() }}
                                @else
                                    0
                                @endauth
                            </span>
                        </span>
                        <span class="text-md text-heading-three item-hover__text d-none d-lg-flex">Cart</span>
                    </a>
                    <a href="tel:{{ $contactInfo->mobile ?? '+917389942021' }}" class="d-sm-flex align-items-center gap-16 d-none ms-10">
                        <span class="d-flex text-32">
                            <img src="{{ asset('customer-web-assets/images/icon/mobile.png') }}" alt="Mobile Icon">
                        </span>
                        <span class="">
                            <span class="d-block text-heading fw-medium">Need any Help! Call Us</span>
                            <span class="d-block fw-bold hover-text-decoration-underline"
                                style="color: #007442;">{{ $contactInfo->mobile ?? '+91 7389942021' }}</span>
                        </span>
                    </a>
                    <button type="button" class="toggle-mobileMenu d-lg-none ms-3n text-gray-800 text-4xl d-flex"> <i
                        class="ph ph-list"></i> </button>
                </div>
            </div>
            <!-- Header Middle Right End  -->
        </nav>
    </div>
</header>
<!-- ======================= Middle Header End ========================= -->

<!-- ==================== Header Start Here ==================== -->
<header class="header bg-white border-bottom-0 box-shadow-3xl z-2">
    <div class="container container-lg">
        <nav class="header-inner d-flex justify-content-center gap-8">
            <div class="flex-align menu-category-wrapper position-relative">
                <!-- Menu Start  -->
                <div class="header-menu d-lg-block d-none">
                    <!-- Nav Menu Start -->
                    <ul class="nav-menu flex-align ">
                        @php
                            $booksCat = $headerCategories->firstWhere('cat_name', 'Books');
                            $uniformsCat = $headerCategories->firstWhere('cat_name', 'Uniforms');
                            $accessoriesCat = $headerCategories->firstWhere('cat_name', 'Accessories');
                        @endphp

                        <li class="nav-menu__item">
                            <a href="{{ route('website.index') }}" class="nav-menu__link text-heading-two">Home</a>
                        </li>
                        <li class="nav-menu__item">
                            <a href="{{ route('website.schools') }}" class="nav-menu__link text-heading-two">Schools</a>
                        </li>
                        <!--<li class="nav-menu__item">-->
                        <!--    <a href="{{ route('website.blogs') }}" class="nav-menu__link text-heading-two">Blogs</a>-->
                        <!--</li>-->
                        <li class="on-hover-item nav-menu__item has-submenu">
                            <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Shop</a>
                            <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                                <li class="common-dropdown__item nav-submenu__item">
                                    <a href="{{ route('website.bundles') }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100 fw-semibold"> Bundles</a>
                                </li>
                                <li class="common-dropdown__item nav-submenu__item">
                                    <a href="{{ route('website.shop', ['category' => $booksCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Books</a>
                                </li>
                                <li class="common-dropdown__item nav-submenu__item">
                                    <a href="{{ route('website.shop', ['category' => $uniformsCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Uniforms</a>
                                </li>
                                <li class="common-dropdown__item nav-submenu__item">
                                    <a href="{{ route('website.shop', ['category' => $accessoriesCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Accessories</a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- <li class="nav-menu__item">
                            <a href="javascript:void(0)" class="nav-menu__link text-heading-two">How it Works</a>
                        </li> -->
                        <li class="nav-menu__item">
                            <a href="{{ route('website.about-us') }}" class="nav-menu__link text-heading-two">About Us</a>
                        </li>
                        <li class="nav-menu__item">
                            <a href="{{ route('website.contact-us') }}" class="nav-menu__link text-heading-two">Contact Us</a>
                        </li>
                        
                    </ul>
                    <!-- Nav Menu End -->
                </div>
                <!-- Menu End  -->
            </div>

            <!-- Header Right start -->
            <!--<div class="header-right flex-align gap-20">-->

            <!--    <button type="button" class="toggle-mobileMenu d-lg-none ms-3n text-gray-800 text-4xl d-flex"> <i-->
            <!--            class="ph ph-list"></i> </button>-->
            <!--</div>-->
            <!-- Header Right End  -->
        </nav>
    </div>
</header>
<!-- ==================== Header End Here ==================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('header-search-input');
    const suggestBox = document.getElementById('header-search-suggestions');
    const categorySelect = document.querySelector('form.form-location-wrapper select[name="category"]') || document.querySelector('form[action="{{ route('website.shop') }}"] select[name="category"]');
    if (!input || !suggestBox) return;
    let timer = null;
    function hideSuggest() {
        suggestBox.classList.add('d-none');
        suggestBox.innerHTML = '';
    }
    function renderSuggestions(items) {
        if (!items || items.length === 0) {
            hideSuggest();
            return;
        }
        const html = items.map(it => `
            <a href="${it.url}" class="d-block px-12 py-10 text-heading hover-bg-main-50 text-decoration-none">
                <i class="ph ph-magnifying-glass me-8 text-gray-500"></i>${it.name}
            </a>
        `).join('');
        suggestBox.innerHTML = html;
        suggestBox.classList.remove('d-none');
    }
    async function fetchSuggestions(q) {
        const params = new URLSearchParams();
        params.set('q', q);
        if (categorySelect && categorySelect.value) {
            params.set('category', categorySelect.value);
        }
        const url = "{{ route('website.search.suggestions') }}" + '?' + params.toString();
        try {
            const resp = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
            const data = await resp.json();
            if (data && data.success) {
                renderSuggestions(data.data);
            } else {
                hideSuggest();
            }
        } catch (e) {
            hideSuggest();
        }
    }
    input.addEventListener('input', function () {
        const q = input.value.trim();
        clearTimeout(timer);
        if (q.length < 2) {
            hideSuggest();
            return;
        }
        timer = setTimeout(() => fetchSuggestions(q), 250);
    });
    input.addEventListener('focus', function () {
        if (input.value.trim().length >= 2 && suggestBox.innerHTML.trim() !== '') {
            suggestBox.classList.remove('d-none');
        }
    });
    document.addEventListener('click', function (e) {
        if (!suggestBox.contains(e.target) && e.target !== input) {
            hideSuggest();
        }
    });
});
</script>
<div id="trackOrderModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Track Your Order</h6>
                <button type="button" class="btn-close" onclick="closeTrackModal()"></button>
            </div>
            <div class="modal-body">
                <p class="text-gray-700 mb-12">Enter your Order ID to view current status, payment state and order date.</p>
                <form id="trackOrderForm" class="mb-16">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="mb-12">
                        <label class="text-sm fw-medium mb-6">Order ID</label>
                        <input type="text" name="order_id" class="form-control" placeholder="e.g. 1024" required>
                        <small class="text-gray-500 d-block mt-6">You can find your Order ID on the order confirmation and in My Orders.</small>
                    </div>
                    <button type="submit" class="btn btn-main rounded-8 py-10 px-24">Check Status</button>
                </form>
                <div id="trackOrderResult" class="border border-gray-100 rounded-12 p-16 bg-white d-none">
                    <div class="row g-12">
                        <div class="col-6">
                            <div class="border border-gray-100 rounded-8 p-12 bg-gray-50 h-100">
                                <div class="d-flex align-items-center gap-10 mb-6">
                                    <i class="ph ph-hash text-main-600"></i>
                                    <span class="text-sm text-gray-700">Order ID</span>
                                </div>
                                <div id="trk_order_id" class="fw-semibold"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-gray-100 rounded-8 p-12 bg-gray-50 h-100">
                                <div class="d-flex align-items-center gap-10 mb-6">
                                    <i class="ph ph-check-circle text-main-600"></i>
                                    <span class="text-sm text-gray-700">Status</span>
                                </div>
                                <div id="trk_order_status" class="fw-semibold"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-gray-100 rounded-8 p-12 bg-gray-50 h-100">
                                <div class="d-flex align-items-center gap-10 mb-6">
                                    <i class="ph ph-credit-card text-main-600"></i>
                                    <span class="text-sm text-gray-700">Payment</span>
                                </div>
                                <div id="trk_payment_status" class="fw-semibold"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-gray-100 rounded-8 p-12 bg-gray-50 h-100">
                                <div class="d-flex align-items-center gap-10 mb-6">
                                    <i class="ph ph-calendar text-main-600"></i>
                                    <span class="text-sm text-gray-700">Placed On</span>
                                </div>
                                <div id="trk_order_date" class="fw-semibold"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border border-gray-100 rounded-8 p-12 bg-gray-50">
                                <div class="d-flex align-items-center gap-10 mb-6">
                                    <i class="ph ph-clock text-main-600"></i>
                                    <span class="text-sm text-gray-700">Last Updated</span>
                                </div>
                                <div id="trk_updated_at" class="fw-semibold"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="trackOrderError" class="alert alert-danger d-none mt-12"></div>
            </div>
        </div>
    </div>
</div>
<script>
    function openTrackModal() {
        const modal = document.getElementById('trackOrderModal');
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'trackBackdrop';
        document.body.appendChild(backdrop);
    }
    function closeTrackModal() {
        const modal = document.getElementById('trackOrderModal');
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.getElementById('trackBackdrop');
        if (backdrop) backdrop.remove();
    }
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('trackOrderForm');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const resultBox = document.getElementById('trackOrderResult');
            const errorBox = document.getElementById('trackOrderError');
            resultBox.classList.add('d-none');
            errorBox.classList.add('d-none');
            const fd = new FormData(form);
            try {
                const resp = await fetch("{{ route('website.order.track') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd
                });
                const data = await resp.json();
                if (!data.success) {
                    errorBox.textContent = data.message || 'Unable to fetch order status.';
                    errorBox.classList.remove('d-none');
                    return;
                }
                document.getElementById('trk_order_id').textContent = '#' + (data.data.order_id ?? '');
                document.getElementById('trk_order_status').textContent = data.data.order_status ?? '';
                document.getElementById('trk_payment_status').textContent = data.data.payment_status ?? '';
                document.getElementById('trk_order_date').textContent = data.data.order_date ?? '';
                document.getElementById('trk_updated_at').textContent = data.data.updated_at ?? '';
                resultBox.classList.remove('d-none');
            } catch (err) {
                errorBox.textContent = 'Network error. Please try again.';
                errorBox.classList.remove('d-none');
            }
        });
    });
</script>
