<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>
            <div class="search-bar flex-grow-1">
                <div class="position-relative search-bar-box"></div>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="" aria-expanded="false">
                        </a>
                        <div class="">
                            <div class="header-notifications-list"></div>
                        </div>
                    </li>
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        </a>
                        <div class="">
                            <div class="header-message-list"></div>
                        </div>
                    </li>
                </ul>
            </div>
            {{-- asset('no_image.jpg') --}}
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ !empty(Auth()->user()->image) ? asset(Auth()->user()->image) : asset('no_image.jpg') }}"
                        class="user-img" alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0">{{ Auth()->user()->name }}</p>
                        <p class="designattion mb-0">{{ Auth()->user()->email }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.profile.page') }}">
                            <i class="bx bx-user"></i><span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.kyc.verification.page') }}">
                            <i class="bx bx-user"></i><span>KYC Verification</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.password.change.page') }}">
                            <i class="bx bx-cog"></i><span>Change Password</span>
                        </a>
                    </li>


                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>

                    @if (Auth::user()->can('merchant.manage'))
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.register.merchant.page') }}">
                                <i class="bx bx-user"></i><span>Manage Merchant</span>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->can('admin.create'))
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.create.page') }}">
                                <i class="bx bx-user"></i><span>Create Admin</span>
                            </a>
                        </li>
                    @endif
                    {{-- <li>
                        <a class="dropdown-item" href="{{ route('admin.hub.index') }}">
                            <i class="bx bx-user"></i><span>Manage Hub</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.hub.inchage.index') }}">
                            <i class="bx bx-user"></i><span>Store Incharge</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.dispatch.incharge.index') }}">
                            <i class="bx bx-user"></i><span>Dispatch Incharge</span>
                        </a>
                    </li> --}}
                    {{-- @endif --}}

                    {{-- <li>
                        <a class="dropdown-item" href="{{ route('admin.booking.operator.page') }}">
                            <i class="bx bx-user"></i><span>Booking Operator</span>
                        </a>
                    </li> --}}

                    @if (Auth()->user()->role == 'Admin')
                        {{-- <li>
                            <a class="dropdown-item" href="{{ route('admin.shop.index') }}">
                                <i class="bx bx-user"></i><span>Create Store</span>
                            </a>
                        </li> --}}

                        {{-- <li>
                            <a class="dropdown-item" href="{{ route('admin.store.admin.index') }}">
                                <i class="bx bx-user"></i><span>Register Store Admin</span>
                            </a>
                        </li> --}}
                        {{-- <li>
                            <a class="dropdown-item" href="{{ route('admin.register.page') }}">
                                <i class="bx bx-user"></i><span>Register Dispatch Admin</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.register.page') }}">
                                <i class="bx bx-user"></i><span>Register Operation Admin</span>
                            </a>
                        </li> --}}
                    @endif

                    @if (Auth()->user()->role == 'Admin' || Auth()->user()->role == 'owner')
                        {{-- <li>
                            <a class="dropdown-item" href="{{ route('admin.register.page') }}">
                                <i class="bx bx-user"></i><span>Register New Member</span>
                            </a>
                        </li> --}}
                    @endif

                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger dropdown-item"><i
                                    class='bx bx-log-out-circle'></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
