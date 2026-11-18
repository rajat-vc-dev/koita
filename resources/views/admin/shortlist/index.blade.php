@extends('admin.layouts.app')

@section('title', 'Shortlist')
@section('page_title', 'Shortlist Organisations')
@section('breadcrumb', 'Shortlist')

@section('content')
    <style>
        .shortlist-row {
            background: #fff;
            border: 1px solid #ece9f3;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(30, 20, 60, .04);
            padding: 14px 20px;
        }
        .shortlist-row__main {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px 20px;
        }
        .shortlist-row__info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1 1 300px;
            min-width: 0;
        }
        .shortlist-row__num {
            flex: 0 0 auto;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #efecf7;
            color: #3b3355;
            font-weight: 600;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .shortlist-row__logo {
            flex: 0 0 auto;
            width: 88px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .shortlist-row__logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .shortlist-row__avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            color: #fff;
            font-size: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            user-select: none;
        }
        .shortlist-row__text {
            min-width: 0;
            flex: 1 1 auto;
        }
        /* Keep the organisation name on a single line; truncate with "..." if space runs out */
        .shortlist-row__title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #1f1a2e;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .shortlist-row__sub {
            color: #6b6580;
            font-size: .85rem;
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* Small status pill shown below the name */
        .shortlist-row__status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            padding: 2px 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: .75rem;
        }
        .shortlist-row__status svg {
            width: 13px;
            height: 13px;
        }
        .shortlist-row__status--on {
            background: #e3f6e8;
            color: #1f9d4a;
        }
        .shortlist-row__status--off {
            background: #f0eff3;
            color: #6b6580;
        }
        /* Actions do not shrink; they wrap to the next line on narrow screens */
        .shortlist-row__actions {
            flex: 0 0 auto;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            padding-left: 20px;
            border-left: 1px solid #e4e1ec;
        }
        .shortlist-row__actions form {
            display: flex;
            gap: 8px;
            margin: 0;
        }
        .shortlist-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #e1dee8;
            background: #fafafb;
            color: #2b2540;
            font-weight: 500;
            font-size: .9rem;
            line-height: 1.2;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            cursor: pointer;
            transition: background .15s, border-color .15s, opacity .15s;
        }
        .shortlist-btn:hover {
            background: #f1eff5;
            color: #2b2540;
            text-decoration: none;
        }
        .shortlist-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }
        .shortlist-btn svg {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
        }
        .shortlist-btn--edit {
            background: #c8960c;
            border-color: #c8960c;
            color: #fff;
        }
        .shortlist-btn--edit:hover {
            background: #b38509;
            border-color: #b38509;
            color: #fff;
        }
        .shortlist-btn--danger {
            background: #fff5f6;
            border-color: #f5c6cc;
            color: #d6303f;
        }
        .shortlist-btn--danger:hover {
            background: #fde8eb;
            color: #b8202e;
        }
        @media (max-width: 991.98px) {
            .shortlist-row__actions {
                border-left: 0;
                padding-left: 0;
                width: 100%;
            }
        }
        @media (max-width: 575.98px) {
            .shortlist-row__logo {
                width: 64px;
            }
        }
    </style>

    <div class="card mt-4">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.shortlist.store') }}" id="shortlist-add-form">
                @csrf
                <div class="form-group">
                    <label for="shortlist-applicant">Please select an organisation to add to shortlist</label>
                    <select class="form-control" id="shortlist-applicant" name="applicant_id" required @disabled($applicants->isEmpty())>
                        <option value="">{{ $applicants->isEmpty() ? 'No submitted registrations available' : 'Select an Organisation' }}</option>
                        @foreach ($applicants as $applicant)
                            <option value="{{ $applicant->id }}">{{ $applicant->organisation_name }} ({{ $applicant->email }})</option>
                        @endforeach
                    </select>
                    @error('applicant_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary" type="submit" @disabled($applicants->isEmpty())>Add to shortlist</button>
            </form>
        </div>
    </div>

    @forelse ($entries as $entry)
        @php
            // Avatar fallback: first letter of the name, with a stable colour picked from the name
            $orgName = $entry->applicant->organisation_name;
            $displayName = trim((string) ($entry->title ?: $orgName));
            $avatarColors = ['#1a73e8', '#d93025', '#188038', '#e37400', '#9334e6', '#00838f', '#c2185b', '#5f6368', '#3949ab', '#6d4c41'];
            $avatarColor = $avatarColors[crc32(mb_strtolower($displayName)) % count($avatarColors)];
            $avatarLetter = mb_strtoupper(mb_substr($displayName, 0, 1)) ?: '?';
        @endphp
        <div class="shortlist-row mt-3" id="entry-{{ $entry->id }}">
            <div class="shortlist-row__main">
                <div class="shortlist-row__info">
                    <div class="shortlist-row__num">{{ $loop->iteration }}</div>

                    {{-- Show the uploaded logo; if there is none, show a Gmail-style letter avatar --}}
                    <div class="shortlist-row__logo">
                        @if ($entry->logo_path)
                            <img src="{{ route('admin.shortlist.logo', $entry) }}" alt="">
                        @else
                            <div class="shortlist-row__avatar" style="background: {{ $avatarColor }};" aria-hidden="true">{{ $avatarLetter }}</div>
                        @endif
                    </div>

                    <div class="shortlist-row__text">
                        <h5 class="shortlist-row__title" title="{{ $entry->title }}">{{ $entry->title }}</h5>
                        {{-- Show the organisation name only when it differs from the title, so it never appears twice --}}
                        @if (mb_strtolower(trim((string) $entry->title)) !== mb_strtolower(trim((string) $orgName)))
                            <div class="shortlist-row__sub" title="{{ $orgName }}">{{ $orgName }}</div>
                        @endif

                        {{-- Enabled / Disabled status, shown below the name --}}
                        <span class="shortlist-row__status {{ $entry->is_enabled ? 'shortlist-row__status--on' : 'shortlist-row__status--off' }}">
                            @if ($entry->is_enabled)
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#1f9d4a"/><path d="M7 12.5l3.2 3.2L17 9" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="11" fill="#9a94ab"/><path d="M8 12h8" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/></svg>
                            @endif
                            {{ $entry->is_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>

                <div class="shortlist-row__actions">
                    <form method="POST" action="{{ route('admin.shortlist.move', $entry) }}">
                        @csrf
                        @method('PATCH')
                        <button class="shortlist-btn" type="submit" name="direction" value="up"
                                aria-label="Move {{ $entry->title }} up" @disabled($loop->first)>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                            Move up
                        </button>
                        <button class="shortlist-btn" type="submit" name="direction" value="down"
                                aria-label="Move {{ $entry->title }} down" @disabled($loop->last)>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
                            Move down
                        </button>
                    </form>

                    <a class="shortlist-btn shortlist-btn--edit" href="{{ route('admin.shortlist.index', ['edit' => $entry->id]) }}#entry-{{ $entry->id }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('admin.shortlist.status', $entry) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_enabled" value="{{ $entry->is_enabled ? 0 : 1 }}">
                        <button class="shortlist-btn" type="submit">
                            @if ($entry->is_enabled)
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M5.6 5.6l12.8 12.8"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.7 2.7L16 10"/></svg>
                            @endif
                            {{ $entry->is_enabled ? 'Disable' : 'Enable' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.shortlist.destroy', $entry) }}" class="js-shortlist-remove">
                        @csrf
                        @method('DELETE')
                        <button class="shortlist-btn shortlist-btn--danger" type="submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>
                            Remove
                        </button>
                    </form>
                </div>
            </div>

            @if (request()->query('edit') === (string) $entry->id)
                <div class="mt-3">
                    @include('admin.shortlist.form', ['entry' => $entry])
                </div>
            @endif
        </div>
    @empty
        <p class="mt-4 text-muted">The shortlist is empty.</p>
    @endforelse
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#shortlist-applicant').on('change', function () {
                if (this.value) {
                    $('#shortlist-add-form').trigger('submit');
                }
            });

            $('.js-shortlist-remove').on('submit', function (event) {
                if (!window.confirm('Remove this shortlist profile? Its saved profile details will be deleted.')) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush
