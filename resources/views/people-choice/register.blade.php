@extends('people-choice.layout')

@section('title', 'Audience Poll')

@if (config('koita.recaptcha.site_key'))
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endif

@section('poll_content')
    <main class="poll-auth">
        <div class="poll-panel">
            <h1>Audience Poll</h1>
            <p class="poll-intro">To participate, please enter your name and email.</p>

            <form method="POST" action="{{ route('people-choice.register') }}">
                @csrf
                <div class="poll-field">
                    <label for="poll-name">Name</label>
                    <input id="poll-name" name="name" value="{{ old('name') }}" autocomplete="name"
                           placeholder="Enter your full name" maxlength="255" required>
                    @error('name') <p class="poll-error" role="alert">{{ $message }}</p> @enderror
                </div>
                <div class="poll-field">
                    <label for="poll-email">Email address</label>
                    <input id="poll-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                           placeholder="you@example.com" maxlength="255" aria-describedby="poll-email-hint" required>
                    <p id="poll-email-hint" class="poll-hint">A verification code will be sent to this email address.</p>
                    @error('email') <p class="poll-error" role="alert">{{ $message }}</p> @enderror
                </div>

                @if (config('koita.recaptcha.site_key'))
                    <div class="g-recaptcha" data-sitekey="{{ config('koita.recaptcha.site_key') }}"></div>
                @endif
                @error('g-recaptcha-response') <p class="poll-error" role="alert">{{ $message }}</p> @enderror

                <p class="poll-hint">We use your name and email to verify and record your ballot.</p>
                <label class="poll-consent">
                    <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent'))>
                    <span>I agree to receive marketing communications, campaigns, and relevant updates from ILSS (optional).</span>
                </label>
                @error('marketing_consent') <p class="poll-error" role="alert">{{ $message }}</p> @enderror

                <button class="poll-button poll-wide" type="submit">Submit</button>
            </form>
        </div>
    </main>
@endsection
