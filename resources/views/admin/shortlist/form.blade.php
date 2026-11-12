<form method="POST" action="{{ route('admin.shortlist.update', $entry) }}" enctype="multipart/form-data" class="mt-3 pt-3 border-top">
    @csrf
    @method('PUT')

    <div class="row">
        @foreach (['title' => 'Title', 'solution_name' => 'AI solution name', 'sector' => 'Sector', 'tagline' => 'Tagline'] as $field => $label)
            <div class="form-group col-md-6">
                <label for="shortlist-{{ $field }}">{{ $label }}</label>
                <input class="form-control" type="text" id="shortlist-{{ $field }}" name="{{ $field }}"
                       value="{{ old($field, $entry->{$field}) }}" maxlength="255" @required($field === 'title')>
                @error($field, 'shortlist')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>

    @foreach (['problem' => 'Problem', 'solution' => 'Solution', 'impact' => 'Impact'] as $field => $label)
        <div class="form-group">
            <label for="shortlist-{{ $field }}">{{ $label }}</label>
            <textarea class="form-control" id="shortlist-{{ $field }}" name="{{ $field }}" rows="4" maxlength="10000">{{ old($field, $entry->{$field}) }}</textarea>
            @error($field, 'shortlist')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    @endforeach

    <div class="row">
        @foreach (['demo_url' => 'Solution demo link', 'testimonial_url' => 'User testimonial link', 'pdf_url' => 'PDF link', 'cta_url' => 'CTA link'] as $field => $label)
            <div class="form-group col-md-6">
                <label for="shortlist-{{ $field }}">{{ $label }}</label>
                <input class="form-control" type="url" id="shortlist-{{ $field }}" name="{{ $field }}"
                       value="{{ old($field, $entry->{$field}) }}" maxlength="2048">
                @error($field, 'shortlist')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
        <div class="form-group col-md-6">
            <label for="shortlist-cta-label">CTA text</label>
            <input class="form-control" type="text" id="shortlist-cta-label" name="cta_label"
                   value="{{ old('cta_label', $entry->cta_label) }}" maxlength="255">
            @error('cta_label', 'shortlist')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-6">
            <label for="shortlist-logo">{{ $entry->logo_path ? 'Replace logo' : 'Upload logo' }}</label>
            <input class="form-control-file" type="file" id="shortlist-logo" name="logo"
                   accept="image/jpeg,image/png,image/webp" aria-describedby="shortlist-logo-hint">
            <small class="form-text text-muted" id="shortlist-logo-hint">JPG, PNG or WebP, up to 5 MB.</small>
            @error('logo', 'shortlist')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <button class="btn btn-primary" type="submit">Save profile</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.shortlist.index') }}">Close</a>
</form>
