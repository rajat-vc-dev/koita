@extends('people-choice.layout')

@section('title', 'Verify your email')

@section('poll_content')
<style>
.top-marquee {
  display: none;
}
header.nav {
  top: 0px;
}
.poll-panel {
  padding: clamp(22px, 5vw, 22px);
  margin-top: 30px;
}
.poll-intro {
	font-size: 14px;
  margin-bottom: 10px;
}
.poll-field input {
  min-height: 40px;
}
.poll-button.poll-wide {
  background: var(--gold);
  color: var(--ink);
  border: 1.5px solid var(--gold);
  font-size: 1.2rem;
  font-family: var(--font);
  font-weight: 700;
  transition: transform .3s var(--ease),box-shadow .3s var(--ease),background .3s,color .3s;
}
.poll-panel h1 {
  font-size: clamp(1.50rem, 5vw, 1.45rem);
}
.poll-button.poll-wide:hover {
  transform: translateY(-2px);
}
.poll-consent {
  font-size: 11px;
}
.poll-consent input {
  margin: -2px 0 0;
}
.poll-hint {
  font-size: 14px;
}
.poll-error {
  font-size: 10px;
}
</style>
    <main class="poll-auth">
        <div class="poll-panel">
            <h1>Verify your email</h1>
            <p class="poll-notice">A verification code was sent to your email address.</p>
            <p class="poll-intro">Enter the six-digit code sent to <strong>{{ $email }}</strong> to continue.</p>

            <form id="poll-verify" method="POST" action="{{ route('people-choice.verify') }}">
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
                <button type="submit">Entered the wrong email address? Go back</button>
            </form>
        </div>
    </main>
@endsection
