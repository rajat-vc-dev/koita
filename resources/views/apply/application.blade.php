@extends('layouts.site')

@section('title', 'Application Form — AI for Impact Awards')
@section('description', 'Complete your AI for Impact Awards application.')

@section('nav')
@include('partials.nav-simple')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endpush

@section('content')
@php
  $org = $applicant->organizationDetail;
  $sol = $applicant->aiSolution;
  $areas = old('primary_areas', $org->primary_areas ?? []);
  $users = old('users_of_solution', $sol->users_of_solution ?? []);
  $inlineErrorKeys = [
      'primary_areas', 'primary_area_other', 'mission_overview',
      'solution_name', 'social_problem', 'solution_description', 'users_of_solution', 'users_other',
      'technical_details', 'demo_video_url', 'testimonial_video_url',
      'adoption_stage', 'user_count', 'success_measurement', 'outcomes_impact', 'deck', 'brand_asset_url', 'consent',
  ];
  $firstErrorKey = \Illuminate\Support\Arr::first($errors->keys());
  $showBannerError = $errors->any()
      && $firstErrorKey
      && ! in_array($firstErrorKey, $inlineErrorKeys, true)
      && ! str_starts_with((string) $firstErrorKey, 'primary_areas.')
      && ! str_starts_with((string) $firstErrorKey, 'users_of_solution.');
@endphp

<section class="pagehero" id="formTop">
  <div class="wrap">
    <h1 style="margin-top:12px">Application Form</h1>
    <p>Signed in as <strong>{{ $applicant->email }}</strong> ({{ $applicant->organisation_name }}).</p>
    <form method="POST" action="{{ route('apply.logout') }}" style="margin-top:12px">
      @csrf
      <button type="submit" class="btn btn-outline btn-sm">Sign out</button>
    </form>
  </div>
</section>

<section class="section">
  <div class="wrap" style="max-width:860px">
    <div id="appWrap" class="form-card">
      <div class="stepper" id="stepper">
        <div class="stp {{ $step === 0 ? 'active' : ($step > 0 ? 'done' : '') }}" data-s="0"><span class="dot">1</span> Organization Details</div><span class="bar"></span>
        <div class="stp {{ $step === 1 ? 'active' : ($step > 1 ? 'done' : '') }}" data-s="1"><span class="dot">2</span> AI Solution Overview</div><span class="bar"></span>
        <div class="stp {{ $step === 2 ? 'active' : '' }}" data-s="2"><span class="dot">3</span> Impact, Measurement & Adoption</div>
      </div>

      <div class="form-body">
        <div id="err" style="color:#c0392b;font-weight:700;margin-bottom:14px;min-height:1.2em;">
          @if ($showBannerError) {{ $errors->first() }} @endif
        </div>

        {{-- STEP 1 --}}
        <form id="step1Form" class="fstep {{ $step === 0 ? 'active' : '' }}" method="POST" action="{{ route('apply.application.step1') }}" style="{{ $step === 0 ? '' : 'display:none' }}" novalidate>
          @csrf
          <div class="field" style="opacity:1">
            <label>Organization Name</label>
            <input type="text" value="{{ $applicant->organisation_name }}" readonly disabled>
          </div>
          <div class="field work{{ $errors->has('primary_areas') ? ' bad' : '' }}">
            <label>Organisation's Primary Area of Work <span class="req">*</span></label>
            <span class="hint">Check all that apply</span>
            <div class="checks" id="primaryAreasGroup">
              @foreach (['Education','Livelihood','Healthcare','Environment','Climate','Other'] as $area)
                <label class="check"><input type="checkbox" name="primary_areas[]" value="{{ $area }}" class="primary-area" @checked(in_array($area, $areas, true))> {{ $area }}</label>
              @endforeach
            </div>
            <div class="field{{ $errors->has('primary_area_other') ? ' bad' : '' }}" id="primaryAreaOtherWrap" style="display:none;margin-top:10px">
              <label for="primary_area_other">Please specify other area <span class="req">*</span></label>
              <input type="text" id="primary_area_other" name="primary_area_other" value="{{ old('primary_area_other', $org->primary_area_other ?? '') }}" class="{{ $errors->has('primary_area_other') ? 'err' : '' }}">
              <div class="err-msg">@error('primary_area_other'){{ $message }}@enderror</div>
            </div>
            <div class="err-msg">@error('primary_areas'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('mission_overview') ? ' bad' : '' }}">
            <label for="mission_overview">Brief overview of your organization’s mission and work <span class="req">*</span></label>
            <span class="hint">200 words max</span>
            <textarea id="mission_overview" name="mission_overview" data-words="200" class="{{ $errors->has('mission_overview') ? 'err' : '' }}">{{ old('mission_overview', $org->mission_overview ?? '') }}</textarea>
            <div class="err-msg">@error('mission_overview'){{ $message }}@enderror</div>
          </div>
          <div class="form-nav">
            <span></span>
            <button class="btn btn-primary" type="submit" id="step1Btn">Continue <span class="arw">&rarr;</span></button>
          </div>
        </form>

        {{-- STEP 2 --}}
        <form id="step2Form" class="fstep {{ $step === 1 ? 'active' : '' }}" method="POST" action="{{ route('apply.application.step2') }}" style="{{ $step === 1 ? '' : 'display:none' }}" novalidate>
          @csrf
          <div class="field{{ $errors->has('solution_name') ? ' bad' : '' }}">
            <label for="solution_name">Solution Name <span class="req">*</span></label>
            <input type="text" id="solution_name" name="solution_name" value="{{ old('solution_name', $sol->solution_name ?? '') }}" class="{{ $errors->has('solution_name') ? 'err' : '' }}">
            <div class="err-msg">@error('solution_name'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('social_problem') ? ' bad' : '' }}">
            <label for="social_problem">What Social Problem Are You Solving Using AI? <span class="req">*</span></label>
            <span class="hint">250 words max</span>
            <textarea id="social_problem" name="social_problem" data-words="250" class="{{ $errors->has('social_problem') ? 'err' : '' }}">{{ old('social_problem', $sol->social_problem ?? '') }}</textarea>
            <div class="err-msg">@error('social_problem'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('solution_description') ? ' bad' : '' }}">
            <label for="solution_description">Describe Your AI Solution <span class="req">*</span></label>
            <span class="hint">250 words max</span>
            <textarea id="solution_description" name="solution_description" data-words="250" class="{{ $errors->has('solution_description') ? 'err' : '' }}">{{ old('solution_description', $sol->solution_description ?? '') }}</textarea>
            <div class="err-msg">@error('solution_description'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('users_of_solution') ? ' bad' : '' }}">
            <label>Users of Solution <span class="req">*</span></label>
            <span class="hint">Check all that apply</span>
            <div class="checks users" id="usersGroup">
              @foreach (['Beneficiaries directly','Field workers/intermediaries','Government workers','Other'] as $user)
                <label class="check"><input type="checkbox" name="users_of_solution[]" value="{{ $user }}" class="users-of" @checked(in_array($user, $users, true))> {{ $user }}</label>
              @endforeach
            </div>
            <div class="field{{ $errors->has('users_other') ? ' bad' : '' }}" id="usersOtherWrap" style="display:none;margin-top:10px">
              <label for="users_other">Please specify other users <span class="req">*</span></label>
              <input type="text" id="users_other" name="users_other" value="{{ old('users_other', $sol->users_other ?? '') }}" class="{{ $errors->has('users_other') ? 'err' : '' }}">
              <div class="err-msg">@error('users_other'){{ $message }}@enderror</div>
            </div>
            <div class="err-msg">@error('users_of_solution'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('technical_details') ? ' bad' : '' }}">
            <label for="technical_details">Share The Technical Details of Your Solution <span class="req">*</span></label>
            <span class="hint">200 words max</span>
            <textarea id="technical_details" name="technical_details" data-words="200" class="{{ $errors->has('technical_details') ? 'err' : '' }}">{{ old('technical_details', $sol->technical_details ?? '') }}</textarea>
            <div class="err-msg">@error('technical_details'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('demo_video_url') ? ' bad' : '' }}">
            <label for="demo_video_url">Upload a Video Demonstration of Your Solution With a Voice-Over Explanation <span class="req">*</span></label>
            <span class="hint">Please upload the video to your Google Drive and share the open-access link here.</span>
            <input type="url" id="demo_video_url" name="demo_video_url" value="{{ old('demo_video_url', $sol->demo_video_url ?? '') }}" placeholder="https://drive.google.com/..." class="{{ $errors->has('demo_video_url') ? 'err' : '' }}">
            <div class="err-msg">@error('demo_video_url'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('testimonial_video_url') ? ' bad' : '' }}">
            <label for="testimonial_video_url">Upload a User Testimonial Video Where the User Shares Their Experience With Your Solution <span class="req">*</span></label>
            <span class="hint">Please upload the video to your Google Drive and share the open-access link here.</span>
            <input type="url" id="testimonial_video_url" name="testimonial_video_url" value="{{ old('testimonial_video_url', $sol->testimonial_video_url ?? '') }}" placeholder="https://drive.google.com/..." class="{{ $errors->has('testimonial_video_url') ? 'err' : '' }}">
            <div class="err-msg">@error('testimonial_video_url'){{ $message }}@enderror</div>
          </div>
          <div class="form-nav">
            <a class="btn btn-outline" href="{{ route('apply.application', ['step' => 0]) }}">&larr; Back</a>
            <button class="btn btn-primary" type="submit" id="step2Btn">Continue <span class="arw">&rarr;</span></button>
          </div>
        </form>

        {{-- STEP 3 --}}
        <form id="step3Form" class="fstep {{ $step === 2 ? 'active' : '' }}" method="POST" action="{{ route('apply.application.step3') }}" enctype="multipart/form-data" style="{{ $step === 2 ? '' : 'display:none' }}" novalidate>
          @csrf
          <div class="field{{ ($errors->has('adoption_stage') || $errors->has('user_count')) ? ' bad' : '' }}">
            <label>Which Stage of Adoption Best Describes Your Solution? <span class="req">*</span></label>
            <div class="checks" id="adoptGroup">
              <label class="check"><input type="radio" name="adoption_stage" value="dev" @checked(old('adoption_stage', $applicant->impactDetail->adoption_stage ?? '') === 'dev')> Still Under Development / Testing</label>
              <label class="check"><input type="radio" name="adoption_stage" value="pilot" @checked(old('adoption_stage', $applicant->impactDetail->adoption_stage ?? '') === 'pilot')> Piloted With Target Users</label>
              <label class="check"><input type="radio" name="adoption_stage" value="scale" @checked(old('adoption_stage', $applicant->impactDetail->adoption_stage ?? '') === 'scale')> Currently Deployed at Scale</label>
            </div>
            <input type="number" id="user_count" name="user_count" placeholder="Number of users" value="{{ old('user_count', $applicant->impactDetail->user_count ?? '') }}" style="margin-top:8px;display:none" class="{{ $errors->has('user_count') ? 'err' : '' }}">
            <div class="err-msg">@error('adoption_stage'){{ $message }}@enderror @error('user_count'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('success_measurement') ? ' bad' : '' }}">
            <label for="success_measurement">How Do You Measure Success of The AI Intervention? <span class="req">*</span></label>
            <span class="hint">250 words max</span>
            <textarea id="success_measurement" name="success_measurement" data-words="250" class="{{ $errors->has('success_measurement') ? 'err' : '' }}">{{ old('success_measurement', $applicant->impactDetail->success_measurement ?? '') }}</textarea>
            <div class="err-msg">@error('success_measurement'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('outcomes_impact') ? ' bad' : '' }}">
            <label for="outcomes_impact">What Are The Outcomes or Impact Achieved so Far? <span class="req">*</span></label>
            <span class="hint">250 words max</span>
            <textarea id="outcomes_impact" name="outcomes_impact" data-words="250" class="{{ $errors->has('outcomes_impact') ? 'err' : '' }}">{{ old('outcomes_impact', $applicant->impactDetail->outcomes_impact ?? '') }}</textarea>
            <div class="err-msg">@error('outcomes_impact'){{ $message }}@enderror</div>
          </div>
          <div class="field files{{ $errors->has('deck') ? ' bad' : '' }}">
            <label for="deck">Upload a PPT Explaining Your Solution and Work <span class="req">*</span></label>
            <span class="hint">Only PDF, PPT, or PPTX — max 20MB</span>
            <label class="file-drop"><div class="fic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg></div><span class="fl" id="deckLabel">Click to upload PPT / PDF</span><input type="file" id="deck" name="deck" accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" hidden></label>
            <div class="err-msg">@error('deck'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('brand_asset_url') ? ' bad' : '' }}">
            <label for="brand_asset_url">Upload Brand Assets <span class="req">*</span></label>
            <span class="hint">Paste a URL to your brand assets (Drive, Dropbox, website, etc.)</span>
            <input type="url" id="brand_asset_url" name="brand_asset_url" value="{{ old('brand_asset_url', $applicant->impactDetail->brand_asset_url ?? '') }}" placeholder="https://" class="{{ $errors->has('brand_asset_url') ? 'err' : '' }}">
            <div class="err-msg">@error('brand_asset_url'){{ $message }}@enderror</div>
          </div>
          <div class="field{{ $errors->has('consent') ? ' bad' : '' }}">
            <label class="consent"><input type="checkbox" name="consent" value="1" id="consent" @checked(old('consent'))> I confirm the information provided is accurate and understand that uploaded files may be shared outside our organisation for the purpose of the awards.</label>
            <div class="err-msg">@error('consent'){{ $message }}@enderror</div>
          </div>
          <div class="form-nav">
            <a class="btn btn-outline" href="{{ route('apply.application', ['step' => 1]) }}">&larr; Back</a>
            <button class="btn btn-primary" type="submit" id="step3Btn">Submit Application <span class="arw">&rarr;</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script>
$(document).ready(function () {
    function wordCount(text) {
        text = $.trim(text || '');
        return text ? text.split(/\s+/).length : 0;
    }

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
        $field.find('input:not([type=radio]):not([type=checkbox]), select, textarea').first().addClass('err');
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

    function clearFormFieldErrors($form) {
        $form.find('.field.bad').each(function () {
            clearFieldError(this);
        });
        $('#err').text('');
    }

    function beginValidation($form) {
        clearFormFieldErrors($form);
        return {
            ok: true,
            firstFocus: null,
            fail: function (selector, message) {
                setFieldError(selector, message);
                if (!this.firstFocus) {
                    var $target = $(selector);
                    this.firstFocus = $target.filter('input, select, textarea')[0]
                        || $target.find('input, select, textarea').get(0)
                        || null;
                }
                this.ok = false;
            }
        };
    }

    function finishValidation(e, state) {
        if (!state.ok) {
            e.preventDefault();
            if (state.firstFocus) {
                state.firstFocus.focus();
                $('html, body').animate({ scrollTop: $(state.firstFocus).offset().top - 80 }, 200);
            }
            return false;
        }
        return true;
    }

    function isValidHttpUrl(value) {
        var urlToCheck = $.trim(value || '');
        if (!urlToCheck) {
            return false;
        }
        if (!/^https?:\/\//i.test(urlToCheck)) {
            urlToCheck = 'https://' + urlToCheck;
        }
        try {
            var parsed = new URL(urlToCheck);
            return (parsed.protocol === 'http:' || parsed.protocol === 'https:')
                && parsed.hostname
                && parsed.hostname.indexOf('.') !== -1;
        } catch (err) {
            return false;
        }
    }

    var deckMaxBytes = 20 * 1024 * 1024;
    var deckAllowedExt = ['pdf', 'ppt', 'pptx'];

    function deckExtension(file) {
        if (!file || !file.name) {
            return '';
        }
        var parts = file.name.split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    }

    function deckErrorMessage(file) {
        if (!file) {
            return 'Please upload your PPT / PDF';
        }
        var ext = deckExtension(file);
        if ($.inArray(ext, deckAllowedExt) === -1) {
            return 'Only PDF, PPT, or PPTX files are allowed';
        }
        if (file.size > deckMaxBytes) {
            return 'File size must not exceed 20 MB';
        }
        return null;
    }

    function toggleOther(checkboxSelector, wrapSelector) {
        var checked = false;
        $(checkboxSelector + ':checked').each(function () {
            if ($(this).val() === 'Other') checked = true;
        });
        $(wrapSelector).toggle(checked);
        if (!checked) {
            $(wrapSelector).find('input').val('');
            clearFieldError(wrapSelector);
        }
    }

    $('.primary-area').on('change', function () {
        toggleOther('.primary-area', '#primaryAreaOtherWrap');
        clearFieldError('.field.work');
    });
    $('.users-of').on('change', function () {
        toggleOther('.users-of', '#usersOtherWrap');
        clearFieldError($('#usersGroup').closest('.field'));
    });
    toggleOther('.primary-area', '#primaryAreaOtherWrap');
    toggleOther('.users-of', '#usersOtherWrap');

    function toggleUserCount() {
        var v = $('input[name="adoption_stage"]:checked').val();
        $('#user_count').toggle(v === 'pilot' || v === 'scale');
        if (v !== 'pilot' && v !== 'scale') {
            $('#user_count').val('');
        }
        clearFieldError('#user_count');
    }
    $('input[name="adoption_stage"]').on('change', toggleUserCount);
    toggleUserCount();

    $('textarea[data-words]').each(function () {
        var $t = $(this);
        var max = +$t.data('words');
        var $hint = $t.prev('.hint');
        var base = $hint.length ? $hint.text() : '';
        $t.on('input', function () {
            if ($hint.length) $hint.text(base + ' (' + wordCount($t.val()) + '/' + max + ')');
        }).trigger('input');
    });

    $('#step1Form, #step2Form, #step3Form').on('input change', 'input, select, textarea', function () {
        clearFieldError(this);
    });

    $('#deck').on('change', function () {
        var f = this.files[0];
        clearFieldError('#deck');
        if (!f) {
            $('#deckLabel').text('Click to upload PPT / PDF');
            return;
        }
        var msg = deckErrorMessage(f);
        if (msg) {
            $('#deck').val('');
            $('#deckLabel').text('Click to upload PPT / PDF');
            setFieldError('#deck', msg);
            return;
        }
        $('#deckLabel').text(f.name);
    });

    $('#step1Form').on('submit', function (e) {
        var state = beginValidation($(this));

        if (!$('.primary-area:checked').length) {
            state.fail('.field.work', 'Please select at least one primary area of work');
        }
        if ($('.primary-area[value="Other"]').is(':checked') && $.trim($('#primary_area_other').val()) === '') {
            state.fail('#primary_area_other', 'Please specify the other area of work');
        }
        var mission = $('#mission_overview').val();
        if ($.trim(mission) === '') {
            state.fail('#mission_overview', 'Please provide a brief overview of your organization’s mission and work');
        } else if (wordCount(mission) > 200) {
            state.fail('#mission_overview', 'Mission overview must be 200 words or less');
        }

        if (!finishValidation(e, state)) {
            return false;
        }
        $('#step1Btn').prop('disabled', true);
        return true;
    });

    $('#step2Form').on('submit', function (e) {
        var state = beginValidation($(this));

        if ($.trim($('#solution_name').val()) === '') {
            state.fail('#solution_name', 'Please enter solution name');
        }

        var social = $('#social_problem').val();
        if ($.trim(social) === '') {
            state.fail('#social_problem', 'Please describe the social problem');
        } else if (wordCount(social) > 250) {
            state.fail('#social_problem', 'Social problem must be 250 words or less');
        }

        var solutionDesc = $('#solution_description').val();
        if ($.trim(solutionDesc) === '') {
            state.fail('#solution_description', 'Please describe your AI solution');
        } else if (wordCount(solutionDesc) > 250) {
            state.fail('#solution_description', 'Solution description must be 250 words or less');
        }

        if (!$('.users-of:checked').length) {
            state.fail($('#usersGroup').closest('.field'), 'Please select users of the solution');
        }
        if ($('.users-of[value="Other"]').is(':checked') && $.trim($('#users_other').val()) === '') {
            state.fail('#users_other', 'Please specify other users');
        }

        var technical = $('#technical_details').val();
        if ($.trim(technical) === '') {
            state.fail('#technical_details', 'Please share technical details');
        } else if (wordCount(technical) > 200) {
            state.fail('#technical_details', 'Technical details must be 200 words or less');
        }

        var demo = $.trim($('#demo_video_url').val());
        if (demo === '') {
            state.fail('#demo_video_url', 'Please add demo video link');
        } else if (!isValidHttpUrl(demo)) {
            state.fail('#demo_video_url', 'Please enter a valid demo video link');
        }

        var testimonial = $.trim($('#testimonial_video_url').val());
        if (testimonial === '') {
            state.fail('#testimonial_video_url', 'Please add testimonial video link');
        } else if (!isValidHttpUrl(testimonial)) {
            state.fail('#testimonial_video_url', 'Please enter a valid testimonial video link');
        }

        if (!finishValidation(e, state)) {
            return false;
        }
        $('#step2Btn').prop('disabled', true);
        return true;
    });

    $('#step3Form').on('submit', function (e) {
        var state = beginValidation($(this));

        if (!$('input[name="adoption_stage"]:checked').length) {
            state.fail('input[name="adoption_stage"]:first', 'Please select adoption stage');
        } else {
            var stage = $('input[name="adoption_stage"]:checked').val();
            if ((stage === 'pilot' || stage === 'scale') && $.trim($('#user_count').val()) === '') {
                state.fail('#user_count', 'Please enter number of users');
            }
        }

        var success = $('#success_measurement').val();
        if ($.trim(success) === '') {
            state.fail('#success_measurement', 'Please describe how you measure success');
        } else if (wordCount(success) > 250) {
            state.fail('#success_measurement', 'Success measurement must be 250 words or less');
        }

        var outcomes = $('#outcomes_impact').val();
        if ($.trim(outcomes) === '') {
            state.fail('#outcomes_impact', 'Please describe the outcomes / impact achieved');
        } else if (wordCount(outcomes) > 250) {
            state.fail('#outcomes_impact', 'Outcomes must be 250 words or less');
        }

        var deckMsg = deckErrorMessage($('#deck')[0].files[0]);
        if (deckMsg) {
            state.fail('#deck', deckMsg);
        }

        var brand = $.trim($('#brand_asset_url').val());
        if (brand === '') {
            state.fail('#brand_asset_url', 'Please enter the brand assets URL');
        } else if (!isValidHttpUrl(brand)) {
            state.fail('#brand_asset_url', 'Please enter a valid brand asset URL');
        }

        if (!$('#consent').is(':checked')) {
            state.fail('#consent', 'Please confirm the consent checkbox');
        }

        if (!finishValidation(e, state)) {
            return false;
        }
        $('#step3Btn').prop('disabled', true);
        return true;
    });
});
</script>
@endpush
