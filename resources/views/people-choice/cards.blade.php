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
                                <img src="{{ route(($preview ?? false) ? 'home-demo.logo' : 'people-choice.logo', $entry->id) }}" alt="" loading="lazy">
                            @else
                                <span aria-hidden="true">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="poll-card-body">
                            @if ($entry->sector)
                                <p class="poll-sector">{{ $entry->sector }}</p>
                            @endif
                            <h2>{{ $entry->title }}</h2>
                            @if ($entry->solution_name)
                                <p class="poll-solution"><span>AI Solution</span>{{ $entry->solution_name }}</p>
                            @endif
                            @if ($summary)
                                <p class="poll-summary">{{ Str::limit($summary, 140) }}</p>
                            @endif

                            <div class="poll-actions">
                                <button type="button" class="poll-button poll-secondary" data-dialog="profile-{{ $entry->id }}" hidden>Read More</button>
                                @if ($preview ?? false)
<a class="poll-button" href="{{ route('people-choice.show') }}">Vote</a>
@else
<label class="poll-choice">
                                    <input type="checkbox" name="choices[]" value="{{ $entry->id }}" @checked($rank !== false)
                                           data-rank="{{ $rank === false ? '' : $rank + 1 }}" data-title="{{ $entry->title }}" aria-label="Vote for {{ $entry->title }}">
                                    <span class="poll-button">Vote</span>
                                </label>
@endif
                            </div>
                        </div>
                    </article>
                @endforeach
