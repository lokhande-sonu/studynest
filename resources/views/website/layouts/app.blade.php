<!DOCTYPE html>
<html lang="en" class="">
<head>
    @include('website.layouts.head')
</head>
<body>

    <!--==================== Preloader Start ====================-->
    <div class="preloader">
        <img src="{{ asset('customer-web-assets/images/logo/logo.png') }}" alt="">
    </div>
    <!--==================== Preloader End ====================-->

    <!--==================== Overlay Start ====================-->
    <div class="overlay"></div>
    <!--==================== Overlay End ====================-->

    <!--==================== Sidebar Overlay End ====================-->
    <div class="side-overlay"></div>
    <!--==================== Sidebar Overlay End ====================-->

    <!-- ==================== Scroll to Top End Here ==================== -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- ==================== Scroll to Top End Here ==================== -->

    @include('website.layouts.search-box')
    @include('website.layouts.mobile-menu')
    @include('website.layouts.header')

    <!-- Flash notifications are rendered as toasts via scripts.blade.php -->

    @yield('content')

    @include('website.layouts.footer')
    @include('website.layouts.auth-modals')
    @include('website.layouts.scripts')

</body>
</html>
