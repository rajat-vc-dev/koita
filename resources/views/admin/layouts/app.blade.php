<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Koita') }}</title>
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
    @stack('styles')
    <script src="{{ asset('assets/admin/js/vendor/modernizr-2.8.3.min.js') }}"></script>
</head>
<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div class="page-container">
        @include('admin.partials.sidebar')

        <div class="main-content">
            @include('admin.partials.header')

            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">@yield('page_title', 'Dashboard')</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                @hasSection('breadcrumb')
                                    <li><span>@yield('breadcrumb')</span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="{{ asset('assets/admin/images/author/avatar.png') }}" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                                {{ auth()->user()->name }} <i class="fa fa-angle-down"></i>
                            </h4>
                            <div class="dropdown-menu">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item" style="border:0;background:transparent;width:100%;text-align:left;cursor:pointer;">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-content-inner">
                @yield('content')
            </div>
        </div>
    </div>

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
