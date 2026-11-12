@extends('layouts.site')

@section('title', 'Submit your Application — AI for Impact Awards')
@section('description', 'Submit your application to the AI for Impact Awards 2026 cycle.')

@section('nav')
@include('partials.nav-simple')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endpush

@section('content')
<section class="pagehero" id="formTop">
  <div class="wrap">
    <h1 style="margin-top:2.5rem;">Applications for the AI Awards 2026 Are Now Closed</h1>
    <p>Thank you for your interest in the AI Awards by ILSS and Koita Foundation.</p>
    <p>The application window for the 2026 edition has now closed.</p>
    <p style="margin-bottom:2rem;">Stay connected with us! Follow the Koita Centre for Digital Transformation (KCADT) on LinkedIn for updates, insights, and stories on how AI is shaping the future of social impact.</p>
  </div>
</section>

@endsection

@push('scripts')
@if (filled(config('koita.recaptcha.site_key')))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script>
$(document).ready(function () {
    function fieldFrom(selectorOrEl) {
        var $el = $(selectorOrEl);
        if ($el.hasClass('field')) {
            return $el;
        }
        return $el.closest('.field');
    }

    function setFieldError(selectorOrEl, message) {
        var $field = fieldFrom(selectorOrEl);
        if (!$field.length) {
            $('#err').text(message);
            return false;
        }
        var $msg = $field.children('.err-msg');
        if (!$msg.length) {
            $msg = $('<div class="err-msg"></div>').appendTo($field);
        }
        $msg.text(message);
        $field.addClass('bad');
        $field.find('input:not([type=radio]):not([type=checkbox]), select, textarea').addClass('err');
        return false;
    }

    function clearFieldError(selectorOrEl) {
        var $field = fieldFrom(selectorOrEl);
        if (!$field.length) {
            return;
        }
        $field.removeClass('bad');
        $field.find('input, select, textarea').removeClass('err');
        $field.children('.err-msg').text('');
    }

    function clearAllFieldErrors() {
        $('#gateAuthForm .field.bad').each(function () {
            clearFieldError(this);
        });
        $('#err').text('');
    }

    $('#gateAuthForm').on('input change', 'input, select, textarea', function () {
        clearFieldError(this);
    });

    $('#gateAuthForm').on('submit', function (e) {
        clearAllFieldErrors();

        var ok = true;
        var firstFocus = null;

        function fail(selector, message) {
            setFieldError(selector, message);
            if (!firstFocus) {
                var $target = $(selector);
                firstFocus = $target.filter('input, select, textarea')[0]
                    || $target.find('input, select, textarea').get(0)
                    || null;
            }
            ok = false;
        }

        var email = $.trim($('#email').val());
        if (email === '') {
            fail('#email', 'Please enter your email');
        } else if (!email.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/)) {
            fail('#email', 'Please enter a valid email');
        }

        if ($.trim($('#access_code').val()) === '') {
            fail('#access_code', 'Please enter your access code');
        }

        if ($('.g-recaptcha').length && (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse())) {
            fail($('.g-recaptcha').closest('.field'), 'Please complete the reCAPTCHA');
        }

        if (!ok) {
            e.preventDefault();
            if (firstFocus) {
                firstFocus.focus();
            }
            return false;
        }

        $('#gateBtn').prop('disabled', true).text('Checking…');
        return true;
    });
});
</script>
@endpush