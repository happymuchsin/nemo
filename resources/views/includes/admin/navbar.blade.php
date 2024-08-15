<nav class="main-header navbar navbar-expand navbar-white navbar-light" style="height: 55pt;">
    <!-- Left navbar links -->
    <ul class="navbar-nav mr-auto">
        <li class="nav-item px-0">
            <a class="nav-link" data-widget="pushmenu" id="collSidebar" href="#" role="button"><i
                    class="fa fa-bars"></i></a>
        </li>
    </ul>

    @if (env('APP_ENV') == 'production')
        <img class="mr-auto ml-auto" src="{{ asset('assets/img/anggun.png') }}" alt="" width="180">
    @endif

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <b class="text-center">{{ auth()->user()->name }} <i class="fa fa-users text-info"></i></b>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="{{ route('user.dashboard') }}" class="dropdown-item">
                    <i class="fa fa-users-gear text-info mr-2"></i> {{ env('APP_NAME') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item dropdown-footer"><i
                        class="fa fa-power-off text-danger"></i> Log Out</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fa fas fa-maximize"></i>
            </a>
        </li>
    </ul>
</nav>
