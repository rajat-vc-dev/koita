<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ShortlistEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ShortlistController extends Controller
{
    public function index(): View
    {
        $entries = ShortlistEntry::query()->with('applicant:id,organisation_name')->inDisplayOrder()->get();
        $applicants = Applicant::query()
            ->where('application_status', Applicant::STATUS_SUBMITTED)
            ->whereNotIn('id', ShortlistEntry::query()->select('applicant_id'))
            ->orderBy('organisation_name')
            ->get(['id', 'organisation_name', 'email']);

        return view('admin.shortlist.index', compact('entries', 'applicants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['applicant_id' => ['required', 'integer']]);

        $entry = DB::transaction(function () use ($data) {
            $applicant = Applicant::query()->lockForUpdate()->find($data['applicant_id']);

            if ($applicant?->application_status !== Applicant::STATUS_SUBMITTED) {
                throw ValidationException::withMessages([
                    'applicant_id' => 'Select a registration with Submitted status.',
                ]);
            }

            $applicant->load(['aiSolution', 'impactDetail']);
            $solution = $applicant->aiSolution;

            return ShortlistEntry::query()->firstOrCreate(['applicant_id' => $applicant->id], [
                'title' => $applicant->organisation_name,
                'solution_name' => $solution?->solution_name,
                'problem' => $solution?->social_problem,
                'solution' => $solution?->solution_description,
                'impact' => $applicant->impactDetail?->outcomes_impact,
                'demo_url' => $solution?->demo_video_url,
                'testimonial_url' => $solution?->testimonial_video_url,
            ]);
        });

        return redirect()->route('admin.shortlist.index', ['edit' => $entry->id])
            ->with('status', 'Registration added to the shortlist.');
    }

    public function update(Request $request, ShortlistEntry $entry): RedirectResponse
    {
        $data = $request->validateWithBag('shortlist', [
            'title' => ['required', 'string', 'max:255'],
            'solution_name' => ['nullable', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'problem' => ['nullable', 'string', 'max:10000'],
            'solution' => ['nullable', 'string', 'max:10000'],
            'impact' => ['nullable', 'string', 'max:10000'],
            'demo_url' => ['nullable', 'url:http,https', 'max:2048'],
            'testimonial_url' => ['nullable', 'url:http,https', 'max:2048'],
            'pdf_url' => ['nullable', 'url:http,https', 'max:2048'],
            'cta_label' => ['nullable', 'required_with:cta_url', 'string', 'max:255'],
            'cta_url' => ['nullable', 'required_with:cta_label', 'url:http,https', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        unset($data['logo']);
        $newLogo = null;

        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('shortlist/logos', $this->uploadsDisk());

            if (! $newLogo) {
                return back()->withInput()->withErrors(['logo' => 'The logo could not be saved. Please try again.'], 'shortlist');
            }

            $data['logo_path'] = $newLogo;
        }

        try {
            $oldLogo = DB::transaction(function () use ($entry, $data) {
                $current = ShortlistEntry::query()->lockForUpdate()->findOrFail($entry->id);
                $oldLogo = $current->logo_path;
                $current->update($data);

                return $oldLogo;
            });
        } catch (Throwable $exception) {
            $this->deleteLogo($newLogo);

            throw $exception;
        }

        if ($newLogo) {
            $this->deleteLogo($oldLogo);
        }

        return redirect()->route('admin.shortlist.index', ['edit' => $entry->id])
            ->with('status', 'Shortlist profile saved.');
    }

    public function move(Request $request, ShortlistEntry $entry): RedirectResponse
    {
        $data = $request->validate(['direction' => ['required', 'in:up,down']]);

        $moved = DB::transaction(function () use ($entry, $data): bool {
            $entries = ShortlistEntry::query()->inDisplayOrder()->lockForUpdate()->get();
            $index = $entries->search(fn (ShortlistEntry $candidate) => $candidate->is($entry));
            abort_if($index === false, 404);

            $target = $index + ($data['direction'] === 'up' ? -1 : 1);

            if (! $entries->has($target)) {
                return false;
            }

            $profile = $entries[$index];
            $entries[$index] = $entries[$target];
            $entries[$target] = $profile;

            foreach ($entries as $position => $profile) {
                $profile->display_order = $position + 1;
                $profile->save();
            }

            return true;
        }, attempts: 3);

        return to_route('admin.shortlist.index')->with(
            'status',
            $moved ? 'Shortlist display order saved.' : 'The entry is already at the end of the list in that direction.',
        );
    }

    public function status(Request $request, ShortlistEntry $entry): RedirectResponse
    {
        $data = $request->validate(['is_enabled' => ['required', 'boolean']]);
        $entry->update($data);

        return redirect()->route('admin.shortlist.index')
            ->with('status', $entry->is_enabled ? 'Shortlist entry enabled.' : 'Shortlist entry disabled.');
    }

    public function destroy(ShortlistEntry $entry): RedirectResponse
    {
        $logo = DB::transaction(function () use ($entry) {
            $current = ShortlistEntry::query()->lockForUpdate()->findOrFail($entry->id);
            $current->delete();

            return $current->logo_path;
        });

        $this->deleteLogo($logo);

        return redirect()->route('admin.shortlist.index')->with('status', 'Registration removed from the shortlist.');
    }

    public function logo(ShortlistEntry $entry): StreamedResponse
    {
        $disk = Storage::disk($this->uploadsDisk());
        abort_unless($entry->logo_path && $disk->exists($entry->logo_path), 404);

        return $disk->response($entry->logo_path, headers: ['X-Content-Type-Options' => 'nosniff']);
    }

    private function uploadsDisk(): string
    {
        return (string) config('koita.uploads_disk', 'local');
    }

    private function deleteLogo(?string $path): void
    {
        if ($path) {
            Storage::disk($this->uploadsDisk())->delete($path);
        }
    }
}
