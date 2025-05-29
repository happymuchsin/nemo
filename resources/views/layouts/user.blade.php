<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>

    @include('includes.script')
</head>

@if ($sidebar)

    <body class="hold-transition sidebar-mini layout-navbar-fixed layout-fixed text-sm">
        <div id="app">
            <div class="wrapper">
                @include('includes.user.navbar')
                @include('includes.user.sidebar')
                <div id="main" class="content-wrapper">
                    @yield('page-content')
                    @include('includes.user.footer')
                </div>
            </div>
        </div>
    </body>
@else

    <body class="hold-transition layout-navbar-fixed layout-fixed text-sm sidebar-collapse">
        <div id="app">
            <div class="wrapper">
                @include('includes.user.navbar')
                <div id="main" class="content-wrapper">
                    @yield('page-content')
                    @include('includes.user.footer')
                </div>
            </div>
        </div>
    </body>
@endif

</html>
