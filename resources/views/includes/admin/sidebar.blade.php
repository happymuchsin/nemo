<aside class="main-sidebar sidebar-dark-primary elevation-4 modlogin/proses/-fixed text-sm">
    <!-- Brand Logo -->
    <a href="{{ route('user.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/img/logo.png') }}" alt="AdminLTE Logo" class="brand-image">
        <span class="brand-text font-weight-strong text-warning"> {{ env('APP_NAME') }}</span>
    </a>
    <div class="sidebar">
        <div class="form-inline mt-3">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fa fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <nav class="mt-2 text-sm">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-compact" data-widget="treeview"
                role="menu" data-accordion="false">
                {{-- @can('admin-dashboard') --}}
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ isset($admin_dashboard) ? $admin_dashboard : '' }}">
                        <i class="nav-icon fa fa-chart-mixed text-warning"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li> --}}
                {{-- @endcan --}}

                {{-- @can('admin-master') --}}
                <li class="nav-item {{ isset($admin_master) ? $admin_master : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa-sharp fa fa-database text-warning"></i>
                        <p>
                            Master
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        {{-- @can('admin-master-division') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.division') }}"
                                class="nav-link {{ $page == 'admin_master_division' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Division</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-position') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.position') }}"
                                class="nav-link {{ $page == 'admin_master_position' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Position</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-tools-user') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.tools.user') }}"
                                class="nav-link {{ $page == 'admin_tools_user' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Data Users</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-approval') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.approval') }}"
                                class="nav-link {{ $page == 'admin_master_approval' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Approval</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-area') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.area') }}"
                                class="nav-link {{ $page == 'admin_master_area' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Area</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-line') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.line') }}"
                                class="nav-link {{ $page == 'admin_master_line' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Line</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-counter') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.counter') }}"
                                class="nav-link {{ $page == 'admin_master_counter' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Counter</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-box') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.box') }}"
                                class="nav-link {{ $page == 'admin_master_box' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Box</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-placement') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.placement') }}"
                                class="nav-link {{ $page == 'admin_master_placement' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Placement</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-status') --}}
                        {{-- <li class="nav-item">
                            <a href="{{ route('admin.master.status') }}"
                                class="nav-link {{ $page == 'admin_master_status' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Status</p>
                            </a>
                        </li> --}}
                        {{-- @endcan --}}
                        {{-- @can('admin-master-needle') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.needle') }}"
                                class="nav-link {{ $page == 'admin_master_needle' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Needle</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-buyer') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.buyer') }}"
                                class="nav-link {{ $page == 'admin_master_buyer' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Buyer</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-category') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.category') }}"
                                class="nav-link {{ $page == 'admin_master_category' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Category</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-sample') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.sample') }}"
                                class="nav-link {{ $page == 'admin_master_sample' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Sample</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-fabric') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.fabric') }}"
                                class="nav-link {{ $page == 'admin_master_fabric' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Fabric</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-master-style') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.master.style') }}"
                                class="nav-link {{ $page == 'admin_master_style' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Style</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
                {{-- @endcan --}}

                {{-- @can('admin-tools') --}}
                <li class="nav-item {{ isset($admin_tools) ? $admin_tools : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-gears text-warning"></i>
                        <p>
                            Tools
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        {{-- @can('admin-tools-role') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.tools.role') }}"
                                class="nav-link {{ $page == 'admin_tools_role' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Role</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-tools-permission') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.tools.permission') }}"
                                class="nav-link {{ $page == 'admin_tools_permission' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Permission</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                        {{-- @can('admin-tools-profile') --}}
                        <li class="nav-item">
                            <a href="{{ route('admin.tools.profile') }}"
                                class="nav-link {{ $page == 'admin_tools_profile' ? 'active' : '' }}">
                                <i class="fa fa-circle nav-icon text-warning"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        {{-- @endcan --}}
                    </ul>
                </li>
                {{-- @endcan --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<div class="preloader d-flex justify-content-center align-items-center">
    <img class="animation__shake mr-2" src="{{ asset('assets/img/logo.png') }}" alt="" height="60"
        width="60">
    <b class="animation__shake mr-5"> {{ env('APP_NAME') }}</b>
</div>
<style>
    .dt-buttons {
        float: left;
    }
</style>
