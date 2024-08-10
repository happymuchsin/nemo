<aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4 text-sm">
    <!-- Brand Logo -->
    <a href="{{ route('user.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/img/logo.png') }}" alt="AdminLTE Logo" class="brand-image">
        <span class="brand-text font-weight-strong text-warning"> {{ env('APP_NAME') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- SidebarSearch Form -->
        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fa fa-search fa-fw text-success"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-compact" data-widget="treeview"
                role="menu" data-accordion="false">
                @if ($page == 'user_dashboard')
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_daily"
                            onclick="setSidebar('dashboard_daily')">
                            <i class="nav-icon fa fa-d text-warning"></i>
                            <p>aily</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_weekly"
                            onclick="setSidebar('dashboard_weekly')">
                            <i class="nav-icon fa fa-w text-warning"></i>
                            <p>eekly</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_monthly"
                            onclick="setSidebar('dashboard_monthly')">
                            <i class="nav-icon fa fa-m text-warning"></i>
                            <p>onthly</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_quarterly"
                            onclick="setSidebar('dashboard_quarterly')">
                            <i class="nav-icon fa fa-q text-warning"></i>
                            <p>uarterly</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_half"
                            onclick="setSidebar('dashboard_half')">
                            <i class="nav-icon fa fa-h text-warning"></i>
                            <p>alf Yearly</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_dashboard" href="#" id="dashboard_yearly"
                            onclick="setSidebar('dashboard_yearly')">
                            <i class="nav-icon fa fa-y text-warning"></i>
                            <p>early</p>
                        </a>
                    </li>
                @elseif ($page == 'user_needle_report')
                    <li class="nav-item">
                        <a class="nav-link user_needle_report" href="#" id="needle_report_exchange"
                            onclick="setSidebar('needle_report_exchange')">
                            <i class="nav-icon fa fa-hand-holding-box text-warning"></i>
                            <p>Exchange</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link user_needle_report" href="#" id="needle_report_counter"
                            onclick="setSidebar('needle_report_counter')">
                            <i class="nav-icon fa fa-boxes-stacked text-warning"></i>
                            <p>Counter</p>
                        </a>
                    </li>
                @endif
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
<script>
    function homeClick(stat) {
        document.location = "{{ route('user.dashboard') }}";
    }
</script>
