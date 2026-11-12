@extends('admin.layouts.app')

@section('title', 'Shortlist')
@section('page_title', 'Shortlist')
@section('breadcrumb', 'Shortlist')

@section('content')
    <div class="card mt-4">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.shortlist.store') }}" id="shortlist-add-form">
                @csrf
                <div class="form-group">
                    <label for="shortlist-applicant">Add a submitted registration</label>
                    <select class="form-control" id="shortlist-applicant" name="applicant_id" required @disabled($applicants->isEmpty())>
                        <option value="">{{ $applicants->isEmpty() ? 'No submitted registrations available' : 'Select a registration' }}</option>
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
        <div class="card mt-3" id="entry-{{ $entry->id }}">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center mb-2">
                        @if ($entry->logo_path)
                            <img src="{{ route('admin.shortlist.logo', $entry) }}" alt="" width="64" class="mr-3">
                        @endif
                        <div>
                            <h5>{{ $loop->iteration }}. {{ $entry->title }}</h5>
                            <div class="text-muted">{{ $entry->applicant->organisation_name }}</div>
                            <span class="badge {{ $entry->is_enabled ? 'badge-success' : 'badge-secondary' }}">
                                {{ $entry->is_enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center">
                        <form method="POST" action="{{ route('admin.shortlist.move', $entry) }}" class="mr-2 mb-2">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-outline-secondary" type="submit" name="direction" value="up"
                                    aria-label="Move {{ $entry->title }} up" @disabled($loop->first)>Move up</button>
                            <button class="btn btn-sm btn-outline-secondary" type="submit" name="direction" value="down"
                                    aria-label="Move {{ $entry->title }} down" @disabled($loop->last)>Move down</button>
                        </form>
                        <a class="btn btn-sm btn-primary mr-2 mb-2" href="{{ route('admin.shortlist.index', ['edit' => $entry->id]) }}#entry-{{ $entry->id }}">Edit</a>
                        <form method="POST" action="{{ route('admin.shortlist.status', $entry) }}" class="mr-2 mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_enabled" value="{{ $entry->is_enabled ? 0 : 1 }}">
                            <button class="btn btn-sm btn-outline-secondary" type="submit">{{ $entry->is_enabled ? 'Disable' : 'Enable' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.shortlist.destroy', $entry) }}" class="js-shortlist-remove mb-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                        </form>
                    </div>
                </div>

                @if (request()->query('edit') === (string) $entry->id)
                    @include('admin.shortlist.form', ['entry' => $entry])
                @endif
            </div>
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
