<div class="sa-app__sidebar">
    <div class="sa-sidebar">
        <div class="sa-sidebar__header">
            <a class="sa-sidebar__logo" href="{{ route('management.dashboard') }}">
                <div class="sa-sidebar-logo">
                    <img src="{{ asset('management-assets/images/logo.png') }}" width="145">
                    <!--<div class="sa-sidebar-logo__caption">StudyNest</div>-->
                </div>
            </a>
            <button class="sa-sidebar__close d-xl-none" type="button" aria-label="Close" data-sa-close-sidebar="">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M11.414,10l4.293-4.293c0.391-0.391,0.391-1.023,0-1.414s-1.023-0.391-1.414,0L10,8.586L5.707,4.293 c-0.391-0.391-1.023-0.391-1.414,0s-0.391,1.023,0,1.414L8.586,10l-4.293,4.293c-0.391,0.391-0.391,1.023,0,1.414 c0.195,0.195,0.451,0.293,0.707,0.293s0.512-0.098,0.707-0.293L10,11.414l4.293,4.293c0.195,0.195,0.451,0.293,0.707,0.293 s0.512-0.098,0.707-0.293c0.391-0.391,0.391-1.023,0-1.414L11.414,10z"></path>
                </svg>
            </button>
        </div>

        <div class="sa-sidebar__body" data-simplebar>
            <ul class="sa-nav sa-nav--sidebar" data-sa-collapse="">
                        <li class="sa-nav__section">
                            <ul class="sa-nav__menu sa-nav__menu--root">
                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a href="{{ route('management.dashboard') }}"
                                        class="sa-nav__link"><span class="sa-nav__icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M8,13.1c-4.4,0-8,3.4-8-3C0,5.6,3.6,2,8,2s8,3.6,8,8.1C16,16.5,12.4,13.1,8,13.1zM8,4c-3.3,0-6,2.7-6,6c0,4,2.4,0.9,5,0.2C7,9.9,7.1,9.5,7.4,9.2l3-2.3c0.4-0.3,1-0.2,1.3,0.3c0.3,0.5,0.2,1.1-0.2,1.4l-2.2,1.7c2.5,0.9,4.8,3.6,4.8-0.2C14,6.7,11.3,4,8,4z">
                                                </path>
                                            </svg></span><span class="sa-nav__title">Dashboard</span></a></li>


                                <div class="sa-nav__section-title"><span>Management</span></div>
                                
                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                        href="{{ route('management.blogs.index') }}" class="sa-nav__link"><span class="sa-nav__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M2 3h12v10H2V3zm1 1v8h10V4H3zm1 2h6v1H4V6zm0 2h6v1H4V8zm0 2h4v1H4v-1z">
                                                </path>
                                            </svg>
                                        </span><span class="sa-nav__title">Blogs Management</span></a>
                                </li>

                                 <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                        href="{{ route('management.schools.index') }}" class="sa-nav__link"><span class="sa-nav__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V1Zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3V1Z">
                                                </path>
                                            </svg>
                                        </span><span class="sa-nav__title">Schools Management</span></a>
                                </li>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                    href="{{ route('management.classes.index') }}" class="sa-nav__link"><span class="sa-nav__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                            viewBox="0 0 16 16" fill="currentColor">
                                            <path
                                                d="M8 0a.5.5 0 0 1 .473.337L9.046 2H14a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1.85l1.323 3.837a.5.5 0 1 1-.946.326L11.092 11H8.5v3a.5.5 0 0 1-1 0v-3H4.908l-1.435 4.163a.5.5 0 1 1-.946-.326L3.85 11H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h4.954L7.527.337A.5.5 0 0 1 8 0zM2 3v7h12V3H2z">
                                            </path>
                                        </svg>
                                    </span><span class="sa-nav__title">Classes Management</span></a>
                                </li>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                    href="{{ route('management.subjects.index') }}" class="sa-nav__link"><span class="sa-nav__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                            viewBox="0 0 16 16" fill="currentColor">
                                            <path
                                                d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.353-.454c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z">
                                            </path>
                                        </svg>
                                    </span><span class="sa-nav__title">Subjects Management</span></a>
                                </li>
                                
                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"
                                    data-sa-collapse-item="sa-nav__menu-item--open"><a href="#" class="sa-nav__link"
                                        data-sa-collapse-trigger=""><span class="sa-nav__icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M8,6C4.7,6,2,4.7,2,3s2.7-3,6-3s6,1.3,6,3S11.3,6,8,6z M2,5L2,5L2,5C2,5,2,5,2,5z M8,8c3.3,0,6-1.3,6-3v3c0,1.7-2.7,3-6,3S2,9.7,2,8V5C2,6.7,4.7,8,8,8z M14,5L14,5C14,5,14,5,14,5L14,5z M2,10L2,10L2,10C2,10,2,10,2,10z M8,13c3.3,0,6-1.3,6-3v3c0,1.7-2.7,3-6,3s-6-1.3-6-3v-3C2,11.7,4.7,13,8,13z M14,10L14,10C14,10,14,10,14,10L14,10z">
                                                </path>
                                            </svg></span><span class="sa-nav__title">Products Management</span><span
                                            class="sa-nav__arrow"><svg xmlns="http://www.w3.org/2000/svg" width="6"
                                                height="9" viewBox="0 0 6 9" fill="currentColor">
                                                <path
                                                    d="M5.605,0.213 C6.007,0.613 6.107,1.212 5.706,1.612 L2.696,4.511 L5.706,7.409 C6.107,7.809 6.107,8.509 5.605,8.808 C5.204,9.108 4.702,9.108 4.301,8.709 L-0.013,4.511 L4.401,0.313 C4.702,-0.087 5.304,-0.087 5.605,0.213 Z">
                                                </path>
                                            </svg></span></a>
                                    <ul class="sa-nav__menu sa-nav__menu--sub" data-sa-collapse-content="">
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.categories.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Manage Categories</span></a></li>
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.products.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Manage Products</span></a></li>
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.bundles.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Manage Bundles</span></a></li>

                                    </ul>
                                </li>
                                
                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                        href="{{ route('management.customers.index') }}" class="sa-nav__link"><span class="sa-nav__icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M8,10c-3.3,0-6,2.7-6,6H0c0-3.2,1.9-6,4.7-7.3C3.7,7.8,3,6.5,3,5c0-2.8,2.2-5,5-5s5,2.2,5,5c0,1.5-0.7,2.8-1.7,3.7c2.8,1.3,4.7,4,4.7,7.3h-2C14,12.7,11.3,10,8,10z M8,2C6.3,2,5,3.3,5,5s1.3,3,3,3s3-1.3,3-3S9.7,2,8,2z">
                                                </path>
                                            </svg>
                                        </span><span class="sa-nav__title">Customers</span></a>
                                </li>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                        href="{{ route('management.order.index') }}" class="sa-nav__link"><span
                                            class="sa-nav__icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                                height="1em" viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M14.2,10.3c-0.1,0.4-0.5,0.7-0.9,0.7H4.8c-0.5,0-0.9-0.3-1-0.8L2.2,4C2.1,3.4,1.6,3,1,3H0.4C0.2,3,0,2.8,0,2.6V1.4C0,1.2,0.2,1,0.4,1h1.4c1,0,1.9,0.7,2.1,1.7l1.5,6.1C5.5,8.9,5.7,9,5.8,9h6.5c0.1,0,0.2-0.1,0.3-0.2l1.1-3.4C13.8,5.2,13.7,5,13.5,5H7.4C7.2,5,7,4.8,7,4.6V3.4C7,3.2,7.2,3,7.4,3H15c0.6,0,1,0.4,1,1v1L14.2,10.3z M4.5,13C5.3,13,6,13.7,6,14.5C6,15.3,5.3,16,4.5,16S3,15.3,3,14.5C3,13.7,3.7,13,4.5,13z M11.5,13c0.8,0,1.5,0.7,1.5,1.5c0,0.8-0.7,1.5-1.5,1.5S10,15.3,10,14.5C10,13.7,10.7,13,11.5,13z">
                                                </path>
                                            </svg></span><span class="sa-nav__title">Orders</span></a>
                                </li>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"><a
                                    href="{{ route('management.prebooking.index') }}" class="sa-nav__link"><span
                                        class="sa-nav__icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                                            height="1em" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M12,0H4C1.8,0,0,1.8,0,4v8c0,2.2,1.8,4,4,4h8c2.2,0,4-1.8,4-4V4C16,1.8,14,0,12,0z M12,14H4c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h8c1.1,0,2,0.9,2,2v8C14,13.1,13.1,14,12,14z M8,4c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S8.6,4,8,4z M8,8c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S8.6,8,8,8z M8,12c-0.6,0-1,0.4-1,1s0.4,1,1,1s1-0.4,1-1S8.6,12,8,12z"/>
                                        </svg></span><span class="sa-nav__title">Pre-Bookings</span></a>
                                </li>

                                <div class="sa-nav__section-title"><span>Reports</span></div>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"
                                    data-sa-collapse-item="sa-nav__menu-item--open"><a href="#" class="sa-nav__link"
                                        data-sa-collapse-trigger=""><span class="sa-nav__icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M14.5,15h-1c-0.8,0-1.5-0.7-1.5-1.5v-8C12,4.7,12.7,4,13.5,4h1C15.3,4,16,4.7,16,5.5v8C16,14.3,15.3,15,14.5,15z M8.5,15h-1C6.7,15,6,14.3,6,13.5v-11C6,1.7,6.7,1,7.5,1h1C9.3,1,10,1.7,10,2.5v11C10,14.3,9.3,15,8.5,15z M2.5,15h-1C0.7,15,0,14.3,0,13.5v-5C0,7.7,0.7,7,1.5,7h1C3.3,7,4,7.7,4,8.5v5C4,14.3,3.3,15,2.5,15z">
                                                </path>
                                            </svg></span><span class="sa-nav__title">Report & Analytics</span><span
                                            class="sa-nav__arrow"><svg xmlns="http://www.w3.org/2000/svg" width="6"
                                                height="9" viewBox="0 0 6 9" fill="currentColor">
                                                <path
                                                    d="M5.605,0.213 C6.007,0.613 6.107,1.212 5.706,1.612 L2.696,4.511 L5.706,7.409 C6.107,7.809 6.107,8.509 5.605,8.808 C5.204,9.108 4.702,9.108 4.301,8.709 L-0.013,4.511 L4.401,0.313 C4.702,-0.087 5.304,-0.087 5.605,0.213 Z">
                                                </path>
                                            </svg></span></a>
                                    <ul class="sa-nav__menu sa-nav__menu--sub" data-sa-collapse-content="">
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.order-report.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Order Report</span></a></li>
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.transaction-report.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Transaction Report</span></a></li>
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.customer-report.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Customer Report</span></a></li>
                                    </ul>
                                </li>

                                <div class="sa-nav__section-title"><span>More Options</span></div>

                                <li class="sa-nav__menu-item sa-nav__menu-item--has-icon"
                                    data-sa-collapse-item="sa-nav__menu-item--open"><a href="#" class="sa-nav__link"
                                        data-sa-collapse-trigger=""><span class="sa-nav__icon"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                                                viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M14,6.8l-0.2,0.1C14,7.3,14,7.6,14,8c0,0.4,0,0.7-0.1,1.1L14,9.2c1,0.6,1.4,1.9,0.8,3c-0.5,0.9-1.6,1.2-2.5,0.7l-0.5-0.3c-0.6,0.5-1.2,0.8-1.9,1.1v0.8c0,0.9-0.7,1.6-1.6,1.6H7.6C6.7,16,6,15.3,6,14.4v-0.8c-0.7-0.2-1.3-0.6-1.9-1.1l-0.5,0.3c-0.9,0.5-2,0.2-2.5-0.7c-0.6-1-0.3-2.4,0.8-3l0.2-0.1C2,8.7,2,8.4,2,8c0-0.4,0-0.7,0.1-1.1L2,6.8c-1.1-0.6-1.4-2-0.8-3C1.7,3,2.8,2.7,3.6,3.2l0.5,0.3C4.7,3,5.3,2.6,6,2.4V1.6C6,0.7,6.7,0,7.6,0h0.8C9.3,0,10,0.7,10,1.6v0.8c0.7,0.2,1.3,0.6,1.9,1.1l0.5-0.3c0.9-0.5,2-0.2,2.5,0.7C15.4,4.9,15.1,6.2,14,6.8z M8,5.5C6.6,5.5,5.5,6.6,5.5,8s1.1,2.5,2.5,2.5s2.5-1.1,2.5-2.5S9.4,5.5,8,5.5z">
                                                </path>
                                            </svg></span><span class="sa-nav__title">Settings</span><span
                                            class="sa-nav__arrow"><svg xmlns="http://www.w3.org/2000/svg" width="6"
                                                height="9" viewBox="0 0 6 9" fill="currentColor">
                                                <path
                                                    d="M5.605,0.213 C6.007,0.613 6.107,1.212 5.706,1.612 L2.696,4.511 L5.706,7.409 C6.107,7.809 6.107,8.509 5.605,8.808 C5.204,9.108 4.702,9.108 4.301,8.709 L-0.013,4.511 L4.401,0.313 C4.702,-0.087 5.304,-0.087 5.605,0.213 Z">
                                                </path>
                                            </svg></span></a>
                                    <ul class="sa-nav__menu sa-nav__menu--sub" data-sa-collapse-content="">
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.charges.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">Manage Charges</span></a></li>
                                        <li class="sa-nav__menu-item"><a href="{{ route('management.app-slider.index') }}"
                                                class="sa-nav__link"><span
                                                    class="sa-nav__menu-item-padding"></span><span
                                                    class="sa-nav__title">App Slider</span></a></li>

                                    </ul>
                                </li>
                            </ul>
                        </li>

                    </ul>
        </div>
    </div>
</div>
