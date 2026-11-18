@extends('people-choice.layout')

@section('title', $justSubmitted ? 'Thank you for voting' : 'Already voted')

@section('poll_content')
    <main class="poll-auth poll-receipt">
        <div class="poll-panel">
            <div class="poll-receipt-check" aria-hidden="true">✓</div>
            <h1>{{ $justSubmitted ? 'Thank you for voting!' : 'You’ve already voted' }}</h1>
            <p class="poll-intro">
                Your ballot for this year’s People’s Choice Awards has been recorded.
                @if ($justSubmitted && $confirmationSent)
                    A confirmation email was sent to <strong>{{ $voter->email }}</strong>.
                @endif
            </p>

            <ol class="poll-receipt-choices" aria-label="Your submitted choices">
                @foreach ($choices as $choice)
                    <li><span>{{ $ordinals[$choice->rank] }}</span> {{ $choice->profile_title }}</li>
                @endforeach
            </ol>
        </div>
    </main>
@endsection
