<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Sign In') — {{ config('app.name', 'Koita') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/admin/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/metisMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/slicknav.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/default-css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/responsive.css') }}">
    <script src="{{ asset('assets/admin/js/vendor/modernizr-2.8.3.min.js') }}"></script>
</head>
<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    @yield('content')

    <script src="{{ asset('assets/admin/js/vendor/jquery-2.2.4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.slicknav.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/admin/js/scripts.js') }}"></script>
    @stack('scripts')
</body>
</html>
