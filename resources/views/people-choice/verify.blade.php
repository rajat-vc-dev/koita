@extends('people-choice.layout')

@section('title', 'Verify your email')

@section('content')
    <main class="poll-panel">
        <h1>Verify your email</h1>
        <p class="poll-notice">A verification code was sent to your email.</p>
        <p class="poll-intro">Enter the six-digit code sent to <strong>{{ $email }}</strong> to continue.</p>

        <form method="POST" action="{{ route('people-choice.verify') }}">
            @csrf
            <div class="poll-field">
                <label for="poll-code">Verification code</label>
                <input class="poll-code" id="poll-code" name="code" inputmode="numeric" autocomplete="one-time-code"
                       pattern="[0-9]{6}" minlength="6" maxlength="6" placeholder="000000" autofocus required>
                @error('code') <p class="poll-error" role="alert">{{ $message }}</p> @enderror
            </div>
            <button class="poll-button poll-wide" type="submit">Verify &amp; Continue</button>
        </form>

        <form method="POST" action="{{ route('people-choice.back') }}" class="poll-back">
            @csrf
            <button type="submit">Entered the wrong email? Go back</button>
        </form>
    </main>
@endsection
