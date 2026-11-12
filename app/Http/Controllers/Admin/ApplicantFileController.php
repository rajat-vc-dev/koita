<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantFileController extends Controller
{
    public function show(Applicant $applicant, string $type): StreamedResponse
    {
        [$disk, $path, $downloadName] = $this->resolve($applicant, $type);

        abort_unless($path && Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path, $downloadName);
    }

    /**
     * @return array{0:string,1:?string,2:string}
     */
    private function resolve(Applicant $applicant, string $type): array
    {
        $disk = (string) config('koita.uploads_disk', 'local');

      if ($type === 'deck') {
    $path = $applicant->impactDetail?->deck_path;

    return [
        $disk,
        $path,
        $path ? 'application-' . \Illuminate\Support\Str::slug($applicant->organisation_name) . '.' . pathinfo($path, PATHINFO_EXTENSION) : 'application-deck'
    ];
}

        $column = Applicant::fileTypeColumns()[$type] ?? null;
        abort_unless($column, 404);

      $path = $applicant->{$column};

return [$disk, $path, str_replace('_', '-', $type) . '-' . \Illuminate\Support\Str::slug($applicant->organisation_name) . '.' . pathinfo((string) $path, PATHINFO_EXTENSION)];
    }
}
