<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') | ILSS–KCADT AI Awards</title>
    <link rel="icon" href="{{ asset('assets/koita/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/people-choice.css') }}">
    @stack('head')
</head>
<body class="@yield('body_class', 'poll-auth')">
    @yield('content')
    @stack('scripts')
</body>
</html>
