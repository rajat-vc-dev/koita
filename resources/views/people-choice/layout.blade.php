@extends('layouts.site')

@section('nav')
    @include('partials.nav-home')
@endsection

@push('styles')
    <meta name="robots" content="noindex">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/people-choice.css') }}">
@endpush

@section('content')
    <div class="poll-page">
        @yield('poll_content')
    </div>
@endsection
