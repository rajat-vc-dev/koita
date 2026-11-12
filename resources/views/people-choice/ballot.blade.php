@extends('people-choice.layout')

@section('title', 'People’s Choice Awards')

@section('poll_content')
    <main class="poll-shell">
        <div class="poll-heading">
            <h1>People’s Choice Awards</h1>
            @if ($entries->count() >= $minVotes)
                <p>Vote for {{ $minVotes }} to {{ $maxVotes }} shortlisted organizations in order of preference.</p>
            @else
                <p>Voting will be available once at least {{ $minVotes }} organizations are on the ballot.</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="poll-error poll-notice" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('people-choice.vote') }}" id="poll-ballot"
              data-min="{{ $minVotes }}" data-max="{{ $maxVotes }}">
            @csrf
            <div class="poll-grid">
                @php
                    $previousChoices = array_filter((array) old('choices', []), 'is_scalar');
                    $previousChoices = array_map('strval', array_values($previousChoices));
                @endphp
                @foreach ($entries as $entry)
                    @php
                        $rank = array_search((string) $entry->id, $previousChoices, true);
                        $summary = $entry->tagline ?: ($entry->solution ?: $entry->problem);
                        $initials = collect(preg_split('/\s+/u', trim($entry->title)))->take(2)
                            ->map(fn ($word) => mb_substr($word, 0, 1))->implode('');
                    @endphp
                    <article class="poll-card">
                        <div class="poll-card-banner">
                            <span class="poll-card-rank" aria-hidden="true" hidden></span>
                            @if ($entry->logo_path)
                                <img src="{{ route('people-choice.logo', $entry->id) }}" alt="" loading="lazy">
                            @else
                                <span aria-hidden="true">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="poll-card-body">
                            @if ($entry->sector)
                                <p class="poll-sector">{{ $entry->sector }}</p>
                            @endif
                            <h2>{{ $entry->title }}</h2>
                            @if ($summary)
                                <p class="poll-summary">{{ Str::limit($summary, 140) }}</p>
                            @endif

                            <div class="poll-actions">
                                <button type="button" class="poll-button poll-secondary" data-dialog="profile-{{ $entry->id }}" hidden>Read More</button>
                                <label class="poll-choice">
                                    <input type="checkbox" name="choices[]" value="{{ $entry->id }}" @checked($rank !== false)
                                           data-rank="{{ $rank === false ? '' : $rank + 1 }}" data-title="{{ $entry->title }}" aria-label="Vote for {{ $entry->title }}">
                                    <span class="poll-button">Vote</span>
                                </label>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if ($entries->count() >= $minVotes)
                <footer class="poll-tray">
                    <div>
                        <p id="poll-count" aria-live="polite">Choose {{ $minVotes }} to {{ $maxVotes }} organizations.</p>
                        <ol class="poll-ranked-choices" id="poll-ranked-choices" aria-label="Your ranked choices" hidden></ol>
                        <p class="poll-tray-hint">Your first selection is your first choice.</p>
                    </div>
                    <button class="poll-button" id="poll-submit" type="submit">Submit Votes</button>
                </footer>
            @endif
        </form>

        @foreach ($entries as $entry)
            <dialog class="poll-profile" id="profile-{{ $entry->id }}" aria-labelledby="profile-title-{{ $entry->id }}">
                <form method="dialog" class="poll-profile-close">
                    <button type="submit" aria-label="Close profile">×</button>
                </form>
                <h2 id="profile-title-{{ $entry->id }}">{{ $entry->title }}</h2>
                @if ($entry->solution_name)
                    <p><strong>AI solution</strong> {{ $entry->solution_name }}</p>
                @endif
                @if ($entry->sector)
                    <p><strong>Sector</strong> {{ $entry->sector }}</p>
                @endif
                @if ($entry->tagline)
                    <p class="poll-hint">{{ $entry->tagline }}</p>
                @endif
                @foreach (['problem' => 'Problem', 'solution' => 'Solution', 'impact' => 'Impact'] as $field => $label)
                    @if ($entry->{$field})
                        <h3>{{ $label }}</h3>
                        <p class="poll-profile-text">{{ $entry->{$field} }}</p>
                    @endif
                @endforeach
                <div class="poll-profile-links">
                    @foreach (['demo_url' => 'Solution Demo', 'testimonial_url' => 'User Testimonial', 'pdf_url' => 'View PDF', 'cta_url' => $entry->cta_label] as $field => $label)
                        @if ($label && preg_match('~^https?://~i', $entry->{$field} ?? ''))
                            <a href="{{ $entry->{$field} }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </dialog>
        @endforeach
    </main>
@endsection
