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

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/people-choice.js') }}" defer></script>
@endpush
