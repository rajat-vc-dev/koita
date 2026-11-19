        @foreach ($entries as $entry)
            <dialog class="poll-profile" id="profile-{{ $entry->id }}" aria-labelledby="profile-title-{{ $entry->id }}">
                <form method="dialog" class="poll-profile-close">
                    <button type="submit" aria-label="Close profile">×</button>
                </form>
                <div class="poll-profile-logo">
                    @if ($entry->logo_path)
                        <img src="{{ route(($preview ?? false) ? 'home-demo.logo' : 'people-choice.logo', $entry->id) }}" alt="{{ $entry->title }} logo" loading="lazy">
                    @else
                        <span aria-hidden="true">{{ collect(preg_split('/\s+/u', trim($entry->title)))->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('') }}</span>
                    @endif
                </div>
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
