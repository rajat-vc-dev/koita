@extends('people-choice.layout')

@section('title', 'Audience Choice Awards')

@section('poll_content')
@include('people-choice.card-styles')
    <main class="poll-shell">
        <div class="poll-heading">
            <h1>Audience Choice Awards</h1>
            @if ($entries->count() >= $minVotes)
                <ul class="poll-intro">
                    <li>
                        <span class="poll-intro-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        </span>
                        <span>Review the <strong>12 shortlisted organisations</strong> and cast your vote.</span>
                    </li>
                    <li>
                        <span class="poll-intro-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><path d="m8 12.5 2.8 2.8L16.5 9.5"/></svg>
                        </span>
                        <span>Select a minimum of <strong>{{ $minVotes }}</strong> and a maximum of <strong>{{ $maxVotes }}</strong> organisations.</span>
                    </li>
                    <li>
                        <span class="poll-intro-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m4 7 8 6 8-6"/></svg>
                        </span>
                        <span>You can vote <strong>once</strong> per verified email address.</span>
                    </li>
                </ul>
            @else
                <p>Voting will be available once at least {{ $minVotes }} organizations are on the ballot.</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="poll-error poll-notice" role="alert">{{ $errors->first() }}</div>
        @endif
		
		<div class="wrap">

        <h2 class="poll-list-title">List of Finalists</h2>

        <form method="POST" action="{{ route('people-choice.vote') }}" id="poll-ballot"
              data-min="{{ $minVotes }}" data-max="{{ $maxVotes }}">
            @csrf
			
            <div class="poll-grid">
                @php
                    $previousChoices = array_filter((array) old('choices', []), 'is_scalar');
                    $previousChoices = array_map('strval', array_values($previousChoices));
                @endphp
                @include('people-choice.cards')
            </div>
			</div>

            @if ($entries->count() >= $minVotes)
				<div class="wrap">
                <footer class="poll-tray">
                    <div>
                        <p id="poll-count" aria-live="polite">Choose {{ $minVotes }} to {{ $maxVotes }} organizations.</p>
                        <ol class="poll-ranked-choices" id="poll-ranked-choices" aria-label="Your ranked choices" hidden></ol>
                        <p class="poll-tray-hint">Your first selection is your first choice.</p>
                    </div>
                    <button class="poll-button" id="poll-submit" type="submit">Submit Votes</button>
                </footer>
				</div>
            @endif
        </form>

        @include('people-choice.profiles')
    </main>

<script>
/* Show the vote tray once the user has scrolled about 8% of the page.
   (The tray also appears as soon as any vote is selected — handled in CSS.) */
(function () {
    var shell = document.querySelector('.poll-shell');
    if (!shell) return;
    function updateTray() {
        var maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        var progress = maxScroll > 0 ? window.scrollY / maxScroll : 1;
        shell.classList.toggle('is-tray-visible', progress >= 0.08);
    }
    window.addEventListener('scroll', updateTray, { passive: true });
    window.addEventListener('resize', updateTray);
    updateTray();
})();
</script>
@endsection
