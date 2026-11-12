@extends('layouts.site')

@section('title', $pageTitle)
@section('description', 'Thank you for your submission.')

@section('nav')
@include('partials.nav-simple')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endpush

@section('content')
{!! $html !!}

@if (! empty($showSignOut))
<div class="wrap" style="margin-top:18px;margin-bottom:40px">
  <form method="POST" action="{{ route('apply.logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline">Sign out</button>
  </form>
</div>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/site.js') }}"></script>
@endpush
