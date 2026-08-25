
<div class="sa-toolbar sa-toolbar--search-hidden sa-app__toolbar">
                <div class="sa-toolbar__body">
                    <div class="sa-toolbar__item"><button class="sa-toolbar__button" type="button" aria-label="Menu"
                            data-sa-toggle-sidebar=""><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M1,11V9h18v2H1z M1,3h18v2H1V3z M15,17H1v-2h14V17z"></path>
                            </svg></button></div>

                    <h5 class="mt-4 ps-5">StudyNest Management Portal</h5>

                    <div class="mx-auto"></div>


                    <div class="dropdown sa-toolbar__item"><button class="sa-toolbar-user" type="button"
                            id="dropdownMenuButton" data-bs-toggle="dropdown" data-bs-offset="0,1"
                            aria-expanded="false"><span
                                class="sa-toolbar-user__avatar sa-symbol sa-symbol--shape--rounded"><img
                                    src="{{ auth('management')->user()->profile_photo
                                            ? asset(env('MANAGEMENT_PROFILE_PHOTO') . '/' . auth('management')->user()->profile_photo)
                                            : asset('management-assets/images/customers/customer-4-64x64.jpg') }}"                                   
                                    alt="" /></span><span class="sa-toolbar-user__info"><span
                                    class="sa-toolbar-user__title">{{ auth('management')->user()->name ?? 'Admin' }}</span><span
                                    class="sa-toolbar-user__subtitle">{{ auth('management')->user()->email ?? 'Admin' }}</span></span></button>
                        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="{{ route('management.profile.index') }}">My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('management.profile.index') }}">Change Password</a></li>

                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item text-danger" href="{{ route('management.logout') }}">LogOut</a></li>
                        </ul>
                    </div>
                </div>
                <div class="sa-toolbar__shadow"></div>
            </div>