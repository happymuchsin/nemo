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

<body class="hold-transition sidebar-mini layout-fixed text-sm">
    <div class="wrapper">
        @include('includes.admin.navbar')
        @include('includes.admin.sidebar')
        <div class="content-wrapper">
            @yield('page-content')
        </div>
        @include('includes.admin.footer')
    </div>
</body>

</html>
