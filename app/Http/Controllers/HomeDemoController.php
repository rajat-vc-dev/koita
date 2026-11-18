<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ShortlistEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeDemoController extends Controller
{
    public function index(): View
    {
        return view('home', ['shortlistEntries' => $this->entries()->inDisplayOrder()->get()]);
    }

    public function logo(int $entry): StreamedResponse
    {
        $profile = $this->entries()->findOrFail($entry);
        $disk = Storage::disk((string) config('koita.uploads_disk', 'local'));
        abort_unless($profile->logo_path && $disk->exists($profile->logo_path), 404);

        return $disk->response($profile->logo_path, headers: ['X-Content-Type-Options' => 'nosniff']);
    }

    private function entries(): Builder
    {
        return ShortlistEntry::query()->where('is_enabled', true)
            ->whereHas('applicant', fn (Builder $query) => $query->where('application_status', Applicant::STATUS_SUBMITTED));
    }
}
