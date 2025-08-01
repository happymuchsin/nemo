<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="position: fixed;">
    <!-- Left navbar links -->
    <ul class="navbar-nav mr-auto">
        <li class="nav-item px-0">
            <a class="nav-link" data-widget="pushmenu" id="collSidebar" href="#" role="button"><i class="fa fa-bars"></i></a>
        </li>
        @can('user-dashboard')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_dashboard' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.dashboard') }}">
                    <b class="text-center text-black"> Dashboard</b>
                </a>
            </li>
        @endcan
        @can('user-report')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_report' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report') }}">
                    <b class="text-center text-black"> Report</b>
                </a>
            </li>
            <li id=""
                class="nav-item text-center dropdown dropdown-hover {{ in_array($page, [
                    'user_report_summary_stock',
                    'user_report_usage_needle',
                    'user_report_daily_stock',
                    'user_report_timing_log',
                    'user_report_track_by_operator',
                    'user_report_track_by_needle',
                    'user_report_wip_needle',
                    'user_report_summary_wip',
                    'user_report_high_user',
                    'user_report_interval_user',
                    'user_report_daily_broken_needle',
                ])
                    ? 'rounded-lg bg-warning'
                    : '' }}">
                <a id="dropdownSubMenuReport" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                    <b class="text-center text-black"> Report</b>
                </a>
                <ul aria-labelledby="dropdownSubMenuReport" class="dropdown-menu border-0 shadow">
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_summary_stock' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.summary-stock') }}">
                            Summary Stock
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_usage_needle' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.usage-needle') }}">
                            Usage Needle All Operator
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_daily_stock' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.daily-stock') }}">
                            Needle Stock
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_timing_log' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.timing-log') }}">
                            Timing Log All Operator
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_track_by_operator' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.track-by-operator') }}">
                            Tracking by Operator
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_track_by_needle' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.track-by-needle') }}">
                            Tracking by Needle Type
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_wip_needle' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.wip-needle') }}">
                            WIP Needle
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_summary_wip' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.summary-wip') }}">
                            Summary WIP (Date)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_high_user' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.high-user') }}">
                            High User
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_interval_user' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.report.interval-user') }}">
                            Interval User
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_report_daily_broken_needle' ? 'rounded-lg bg-warning' : '' }}"
                            href="{{ route('user.report.daily-broken-needle') }}">
                            Daily Broken Needle
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
        @can('user-needle-report')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_needle_report' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.needle-report') }}">
                    <b class="text-center text-black"></i> Needle Report</b>
                </a>
            </li>
        @endcan
        {{-- @can('user-stock')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_stock' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.stock') }}">
                    <b class="text-center text-black"></i> Stock</b>
                </a>
            </li>
        @endcan --}}
        @can('user-warehouse')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_warehouse' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.warehouse') }}">
                    <b class="text-center text-black"></i> Warehouse</b>
                </a>
            </li>
        @endcan
        @can('user-dead-stock')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_dead_stock' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.dead-stock') }}">
                    <b class="text-center text-black"></i> Dead Stock</b>
                </a>
            </li>
        @endcan
        @can('user-adjustment')
            <li id="" class="nav-item text-center">
                <a class="nav-link {{ $page == 'user_adjustment' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.adjustment') }}">
                    <b class="text-center text-black"></i> Adjustment</b>
                </a>
            </li>
        @endcan
        @can('user-approval')
            <li id="" class="nav-item text-center dropdown dropdown-hover {{ in_array($page, ['user_approval_missing_fragment', 'user_approval_adjustment']) ? 'rounded-lg bg-warning' : '' }}">
                <a id="dropdownSubMenuApproval" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                    <b class="text-center text-black"></i> Approval</b>
                </a>
                <ul aria-labelledby="dropdownSubMenuApproval" class="dropdown-menu border-0 shadow">
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_approval_missing_fragment' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.approval.missing-fragment') }}">
                            Missing Fragment
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item custom-dropdown-item {{ $page == 'user_approval_adjustment' ? 'rounded-lg bg-warning' : '' }}" href="{{ route('user.approval.adjustment') }}">
                            Adjustment
                        </a>
                    </li>
                </ul>
            </li>
        @endcan
    </ul>

    @if (env('APP_ENV') == 'production')
        <img class="mr-auto ml-auto" src="{{ asset('assets/img/anggun.png') }}" alt="" width="180">
    @endif

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fa fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="bellCountNotif"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header" id="countNotif"></span>
                <div class="dropdown-divider"></div>
                <div id="divNotif"></div>
                {{-- <a href="#" class="dropdown-item">
                    <i class="fa fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fa fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fa fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a> --}}
            </div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <b class="text-center"><i class="fa fa-gear text-info"></i> {{ auth()->user()->name }}</b>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header"><b>Tools</b></span>
                <div class="dropdown-divider"></div>
                @can('admin-dashboard')
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        <i class="fa fa-users-gear text-info mr-2"></i> Admin
                    </a>
                @endcan
                <a href="{{ route('admin.profile') }}" class="dropdown-item">
                    <i class="fa fa-user-gear text-info mr-2"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer"><i class="fa fa-power-off text-danger"></i> Log Out</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fa fa-maximize"></i>
            </a>
        </li>
    </ul>
</nav>
