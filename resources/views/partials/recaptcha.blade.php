@php
  $recaptchaSiteKey = (string) config('koita.recaptcha.site_key');
@endphp
@if ($recaptchaSiteKey !== '')
  <div class="field full" style="margin:18px 0;">
    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
    @error('g-recaptcha-response')
      <p class="recaptcha-error" style="color:#c0392b;font-weight:700;margin-top:8px;">{{ $message }}</p>
    @enderror
  </div>
@endif
