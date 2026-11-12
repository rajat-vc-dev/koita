@extends('layouts.site')

@section('title', 'Resubmit Documents — AI for Impact Awards')
@section('description', 'Update your registration documents.')

@section('nav')
@include('partials.nav-simple')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<style>
.radio-group{display:flex;gap:20px;margin-top:10px;flex-wrap:wrap}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:700px){.detail-grid{grid-template-columns:1fr}}
.readonly-field input{background:#f3eee6;color:#6d5a64;cursor:not-allowed}
</style>
@endpush

@section('content')
@php
  $requestedFields = $applicant->requestedResubmitFields();
  $docMap = \App\Models\Applicant::documentsByType()[$applicant->organisation_type] ?? [];
  $fileTypes = \App\Models\Applicant::formFieldToFileType();
  $inlineErrorKeys = [
      'website', 'contact_person_name', 'designation', 'mobile', 'documents',
      'section_8_certificate', 'certificate_12a', 'certificate_80g', 'coi', 'pan', 'gst',
      'g-recaptcha-response',
  ];
  $firstErrorKey = \Illuminate\Support\Arr::first($errors->keys());
  $showBannerError = $errors->any()
      && $firstErrorKey
      && ! in_array($firstErrorKey, $inlineErrorKeys, true);
@endphp
<section class="section form-section">
  <div class="wrap" style="max-width:860px">
    <div class="form-card">
      <div class="form-body">
        <form id="docResubmitForm" method="POST" action="{{ route('documents.resubmit.update', $token) }}" enctype="multipart/form-data" novalidate>
          @csrf
          <div id="err" style="color:#c0392b;font-weight:700;margin-bottom:14px;min-height:1.2em;">
            @if ($showBannerError) {{ $errors->first() }} @endif
          </div>

          <h1 class="h-md" style="color:var(--plum-ink);margin:0 0 12px;">Resubmit Your Documents</h1>
			<p style="text-align: center;">Kindly review and update your details.<br/> Organisation name and email address cannot be changed.</p>
          <div class="detail-grid">
            <div class="field readonly-field">
              <label>Organisation Name</label>
              <input type="text" value="{{ $applicant->organisation_name }}" readonly disabled>
            </div>
            <div class="field{{ $errors->has('website') ? ' bad' : '' }}">
              <label for="website">Website / Public Profile Link <span class="req">*</span></label>
              <input type="text" id="website" name="website" value="{{ old('website', $applicant->website) }}" class="{{ $errors->has('website') ? 'err' : '' }}">
              <div class="err-msg">@error('website'){{ $message }}@enderror</div>
            </div>
            <div class="field{{ $errors->has('contact_person_name') ? ' bad' : '' }}">
              <label for="contact_person_name">Name of Contact Person <span class="req">*</span></label>
              <input type="text" id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name', $applicant->contact_person_name) }}" class="{{ $errors->has('contact_person_name') ? 'err' : '' }}">
              <div class="err-msg">@error('contact_person_name'){{ $message }}@enderror</div>
            </div>
            <div class="field{{ $errors->has('designation') ? ' bad' : '' }}">
              <label for="designation">Designation <span class="req">*</span></label>
              <input type="text" id="designation" name="designation" value="{{ old('designation', $applicant->designation) }}" class="{{ $errors->has('designation') ? 'err' : '' }}">
              <div class="err-msg">@error('designation'){{ $message }}@enderror</div>
            </div>
            <div class="field readonly-field">
              <label>Email Address</label>
              <input type="email" value="{{ $applicant->email }}" readonly disabled>
            </div>
            <div class="field{{ $errors->has('mobile') ? ' bad' : '' }}">
              <label for="mobile">Mobile Number <span class="req">*</span></label>
              <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $applicant->mobile) }}" class="{{ $errors->has('mobile') ? 'err' : '' }}">
              <div class="err-msg">@error('mobile'){{ $message }}@enderror</div>
            </div>
          </div>

          <div class="field full readonly-field" style="margin-top:15px;">
            <label>Organisation type</label>
            <input type="text" value="{{ $applicant->organisationTypeLabel() }}" readonly disabled>
          </div>

          <h2 class="h-md" style="color:var(--plum-ink);margin:28px 0 12px; border-top: #ddd solid 1px; padding-top: 30px;">Upload documents</h2>
          <p class="muted" style="margin-bottom:16px;">Upload each requested document. PDF or image (jpg, jpeg, webp, png), max 5MB.</p>
          <div class="err-msg" id="documentsErr">@error('documents'){{ $message }}@enderror</div>

          <div id="requestedUploads">
            @forelse ($requestedFields as $name)
              @php
                $column = $docMap[$name] ?? null;
                $fileType = $fileTypes[$name] ?? $name;
                $label = \App\Models\Applicant::resubmitDocumentLabel($applicant->organisation_type, $name);
                $hasExisting = $column && filled($applicant->{$column});
              @endphp
              <div class="field{{ $errors->has($name) ? ' bad' : '' }}" style="margin-top:25px;">
                <label for="{{ $name }}">Upload {{ $label }} <span class="req">*</span></label>
                @if ($hasExisting)
                  <p class="muted ap_line">Current file: <a style="color: var(--plum-deep); text-decoration: underline;" href="{{ route('documents.resubmit.file', ['token' => $token, 'type' => $fileType]) }}">View</a></p>
                @endif
                <input type="file" id="{{ $name }}" name="{{ $name }}" accept=".pdf,.jpg,.jpeg,.webp,.png" data-label="{{ $label }}" class="{{ $errors->has($name) ? 'err' : '' }}" required>
                <div class="err-msg">@error($name){{ $message }}@enderror</div>
              </div>
            @empty
              <p class="muted">No documents were requested on this link.</p>
            @endforelse
          </div>

          @include('partials.honeypot')
          @include('partials.recaptcha')

          <button type="submit" class="btn btn-primary btn-block" id="resubmitBtn" style="margin-top:24px;">
            Submit Updated Documents <span class="arw">&rarr;</span>
          </button>
          <p id="msg" style="display:none;margin-top:12px;font-weight:600;color:var(--plum);">Please wait while your details are being submitted…</p>
        </form>
      </div>
    </div>
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
        $('#docResubmitForm .field.bad').each(function () {
            clearFieldError(this);
        });
        $('#err').text('');
    }

    $('#docResubmitForm').on('input change', 'input, select, textarea', function () {
        clearFieldError(this);
    });

    function allowedFile(input) {
        if (!input.files || !input.files.length) return true;
        var ext = input.files[0].name.split('.').pop().toLowerCase();
        if ($.inArray(ext, ['pdf','jpg','jpeg','webp','png']) === -1) return false;
        if (input.files[0].size > 5 * 1024 * 1024) return false;
        return true;
    }

    function selectedFileState(input) {
        if (!input || !input.files || !input.files.length) {
            return false;
        }
        return allowedFile(input) ? true : 'invalid';
    }

    var fieldMax = {{ (int) \App\Models\Applicant::FIELD_MAX }};
    var mobileMax = {{ (int) \App\Models\Applicant::MOBILE_MAX }};
    var fileTypeMsg = 'Documents must be PDF or image (jpg, jpeg, webp, png) and under 5MB';

    $('#docResubmitForm').on('submit', function (e) {
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

        var website = $.trim($('#website').val());
        if (website === '') {
            fail('#website', 'Please enter website');
        } else if (website.length > fieldMax) {
            fail('#website', 'Website / public profile link may not be greater than ' + fieldMax + ' characters.');
        }

        var contactPersonName = $.trim($('#contact_person_name').val());
        if (contactPersonName === '') {
            fail('#contact_person_name', 'Please enter contact person');
        } else if (contactPersonName.length > fieldMax) {
            fail('#contact_person_name', 'Contact person name may not be greater than ' + fieldMax + ' characters.');
        }

        var designation = $.trim($('#designation').val());
        if (designation === '') {
            fail('#designation', 'Please enter designation');
        } else if (designation.length > fieldMax) {
            fail('#designation', 'Designation may not be greater than ' + fieldMax + ' characters.');
        }

        var mobile = $.trim($('#mobile').val());
        if (mobile === '') {
            fail('#mobile', 'Please enter mobile');
        } else if (mobile.length > mobileMax) {
            fail('#mobile', 'Mobile number may not be greater than ' + mobileMax + ' characters.');
        }

        $('#requestedUploads input[type="file"]').each(function () {
            var result = selectedFileState(this);
            var label = $(this).data('label') || 'document';
            if (result === 'invalid') {
                fail(this, fileTypeMsg);
            } else if (!result) {
                fail(this, 'Please upload ' + label);
            }
        });

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

        $('#resubmitBtn').prop('disabled', true).hide();
        $('#msg').show();
        return true;
    });
});
</script>
@endpush
