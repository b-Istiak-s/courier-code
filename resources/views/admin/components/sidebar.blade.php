<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="{{ !empty(Auth()->user()->image) ? asset(Auth()->user()->image) : asset('no_image.jpg') }}"
                class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"></h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        @if (Auth::user()->status == 1)
            <li class="menu-label">Dashboard</li>
            <li>
                <a href="{{ route('dashboard') }}">
                    <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('admin.assign.courier.services.page') }}">
                    <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                    <div class="menu-title">Assign Courier Services</div>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('admin.booking.page') }}">
                    <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                    <div class="menu-title">Booking Order</div>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('admin.bulk.upload.index') }}">
                    <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                    <div class="menu-title">Bulk Order</div>
                </a>
            </li> --}}
            {{-- <li>
                <a href="{{ route('admin.booking.operator.page') }}">
                    <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                    <div class="menu-title">Booking Operator</div>
                </a>
            </li> --}}



            @if (Auth::user()->can('booking.menu'))
                <li class="menu-label">BOOKING SETUP</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon">
                            <i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Manage Booking</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('admin.booking.operator.page') }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Booking Operator</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.bulk.upload.index') }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Bulk Order</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.booking.page') }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Booking Order</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.assign.courier.services.page') }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Assign Courier Services</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->can('store.menu'))
                <li class="menu-label">STORE SETUP</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon">
                            <i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Manage Store</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('admin.store.admin.index', Auth::user()->id) }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Create Store Admin</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.store.index', Auth::user()->id) }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">Create Store</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.store.manage.index') }}">
                                <div class="parent-icon"><i class='bx bx-cookie'></i></div>
                                <div class="menu-title">My Store</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->can('hub.menu'))
                <li class="menu-label">HUB SETUP</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon">
                            <i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Manage Hub</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('admin.hub.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Create Hub
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.hub.inchage.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Create Hub Incharge
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.store.inchage.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Create Store Incharge
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.dispatch.incharge.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Create Dispatch Incharge
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->can('product.menu'))
                <li class="menu-label">SETUP</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Manage Product</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('admin.product.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Add Product
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->can('setting.menu'))
                <li class="menu-label">SETTING</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Settings</div>
                    </a>
                    <ul>
                        <li>
                            <a href="{{ route('admin.product.type.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Add Product Type
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.delivery.type.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Add Delivery Type
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.setup.charges.index') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                Setup Charges
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (Auth::user()->can('role.menu'))
                <li class="menu-label">Roles And Permission</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i>
                        </div>
                        <div class="menu-title">Role & Permission</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('all.permission') }}"><i class="bx bx-right-arrow-alt"></i>All
                                Permission</a>
                        </li>
                        <li> <a href="{{ route('all.roles') }}"><i class="bx bx-right-arrow-alt"></i>All Roles</a></li>
                        <li> <a href="{{ route('add.roles.permission') }}"><i class="bx bx-right-arrow-alt"></i>Roles
                                in
                                Permission</a>
                        </li>
                        <li> <a href="{{ route('all.roles.permission') }}"><i class="bx bx-right-arrow-alt"></i>All
                                Roles
                                in
                                Permission</a>
                        </li>
                    </ul>
                </li>
            @endif
        @endif
    </ul>
    <!--end navigation-->
</div>
