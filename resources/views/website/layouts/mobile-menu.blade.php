<div class="mobile-menu scroll-sm d-lg-none d-block">
    <button type="button" class="close-button"> <i class="ph ph-x"></i> </button>
    <div class="mobile-menu__inner">
        <a href="{{ url('/') }}" class="mobile-menu__logo">
            <img src="{{ asset('customer-web-assets/images/logo/logo.png') }}" alt="Logo">
        </a>
        <div class="mobile-menu__menu">
            @php
                $mobileCategories = \App\Models\ProductCategory::where('cat_status', 1)->get();
                $mBooksCat = $mobileCategories->firstWhere('cat_name', 'Books');
                $mUniformsCat = $mobileCategories->firstWhere('cat_name', 'Uniforms');
                $mAccessoriesCat = $mobileCategories->firstWhere('cat_name', 'Accessories');
            @endphp
            <!-- Nav Menu Start -->
            <ul class="nav-menu flex-align nav-menu--mobile">
                <li class="on-hover-item nav-menu__item">
                    <a href="{{ route('website.index') }}" class="nav-menu__link text-heading-two">Home</a>
                </li>
                <li class="on-hover-item nav-menu__item">
                    <a href="{{ route('website.schools') }}" class="nav-menu__link text-heading-two">Schools</a>
                </li>
                <li class="on-hover-item nav-menu__item has-submenu">
                    <a href="javascript:void(0)" class="nav-menu__link text-heading-two">Shop</a>
                    <ul class="on-hover-dropdown common-dropdown nav-submenu scroll-sm">
                        <li class="common-dropdown__item nav-submenu__item">
                            <a href="{{ route('website.shop', ['category' => $mBooksCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Books</a>
                        </li>
                        <li class="common-dropdown__item nav-submenu__item">
                            <a href="{{ route('website.shop', ['category' => $mUniformsCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Uniforms</a>
                        </li>
                        <li class="common-dropdown__item nav-submenu__item">
                            <a href="{{ route('website.shop', ['category' => $mAccessoriesCat?->cat_id ?? -1]) }}" class="common-dropdown__link nav-submenu__link text-heading-two hover-bg-neutral-100"> Accessories</a>
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
    </div>
</div>
