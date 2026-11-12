<?php

namespace App\Http\Controllers\Apply;

use App\Http\Controllers\Controller;
use App\Http\Requests\Apply\StoreAiSolutionRequest;
use App\Http\Requests\Apply\StoreImpactDetailRequest;
use App\Http\Requests\Apply\StoreOrganizationDetailRequest;
use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\ApplicantAiSolution;
use App\Models\ApplicantImpactDetail;
use App\Models\ApplicantOrganizationDetail;
use App\Models\EmailTemplate;
use App\Models\PageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        /** @var Applicant $applicant */
        $applicant = $request->attributes->get('applicant');

        if ($applicant->application_status === Applicant::STATUS_SUBMITTED) {
            return $this->thankYouView($applicant);
        }

        $applicant->load(['organizationDetail', 'aiSolution', 'impactDetail']);

        $step = (int) $request->query('step', $this->suggestedStep($applicant));

        return view('apply.application', [
            'applicant' => $applicant,
            'step' => max(0, min(2, $step)),
        ]);
    }

    public function storeStep1(StoreOrganizationDetailRequest $request): RedirectResponse
    {
        /** @var Applicant $applicant */
        $applicant = $request->attributes->get('applicant');
        $this->ensureNotSubmitted($applicant);

        ApplicantOrganizationDetail::query()->updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'organization_name' => $applicant->organisation_name,
                'primary_areas' => $request->input('primary_areas', []),
                'primary_area_other' => $request->input('primary_area_other'),
                'mission_overview' => $request->string('mission_overview')->toString(),
            ],
        );

        $this->markInProgress($applicant);

        return redirect()->route('apply.application', ['step' => 1]);
    }

    public function storeStep2(StoreAiSolutionRequest $request): RedirectResponse
    {
        /** @var Applicant $applicant */
        $applicant = $request->attributes->get('applicant');
        $this->ensureNotSubmitted($applicant);

        ApplicantAiSolution::query()->updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'solution_name' => $request->string('solution_name')->toString(),
                'social_problem' => $request->string('social_problem')->toString(),
                'solution_description' => $request->string('solution_description')->toString(),
                'users_of_solution' => $request->input('users_of_solution', []),
                'users_other' => $request->input('users_other'),
                'technical_details' => $request->string('technical_details')->toString(),
                'demo_video_url' => $request->string('demo_video_url')->toString(),
                'testimonial_video_url' => $request->string('testimonial_video_url')->toString(),
            ],
        );

        $this->markInProgress($applicant);

        return redirect()->route('apply.application', ['step' => 2]);
    }

    public function storeStep3(StoreImpactDetailRequest $request): RedirectResponse
    {
        /** @var Applicant $applicant */
        $applicant = $request->attributes->get('applicant');
        $this->ensureNotSubmitted($applicant);

        $disk = (string) config('koita.uploads_disk', 'local');
        $deckPath = $request->file('deck')->store('applicants/decks', $disk);

        ApplicantImpactDetail::query()->updateOrCreate(
            ['applicant_id' => $applicant->id],
            [
                'adoption_stage' => $request->string('adoption_stage')->toString(),
                'user_count' => $request->input('user_count'),
                'success_measurement' => $request->string('success_measurement')->toString(),
                'outcomes_impact' => $request->string('outcomes_impact')->toString(),
                'deck_path' => $deckPath,
                'brand_asset_url' => $request->string('brand_asset_url')->toString(),
                'consent' => true,
            ],
        );

        $applicant->update([
            'application_status' => Applicant::STATUS_SUBMITTED,
            'application_submitted_at' => now(),
        ]);

        $applicant->refresh()->load('aiSolution');
        $this->sendApplicationSubmittedNotifications($applicant);

        return redirect()->route('apply.application')->with('submitted', true);
    }

    private function sendApplicationSubmittedNotifications(Applicant $applicant): void
    {
        $data = [
            'contact_person_name' => $applicant->contact_person_name,
            'organisation_name' => $applicant->organisation_name,
            'website' => $applicant->website,
            'designation' => $applicant->designation,
            'email' => $applicant->email,
            'mobile' => $applicant->mobile,
            'solution_name' => $applicant->aiSolution?->solution_name,
            'submitted_at' => $applicant->application_submitted_at?->format('Y-m-d H:i') ?? now()->format('Y-m-d H:i'),
            'admin_applicant_url' => route('admin.applicants.show', $applicant),
        ];

        $userTemplate = EmailTemplate::query()
            ->where('key', EmailTemplate::USER_APPLICATION_SUBMIT)
            ->first();
        $adminTemplate = EmailTemplate::query()
            ->where('key', EmailTemplate::ADMIN_APPLICATION_SUBMIT)
            ->first();

        if ($userTemplate) {
            Mail::to($applicant->email)->send(new TemplatedMail($userTemplate, $data));
        }

        if ($adminTemplate) {
            Mail::to(config('koita.admin_email'))->send(new TemplatedMail($adminTemplate, $data));
        }
    }

    private function ensureNotSubmitted(Applicant $applicant): void
    {
        abort_if(
            $applicant->application_status === Applicant::STATUS_SUBMITTED,
            403,
            'This application has already been submitted.',
        );
    }

    private function suggestedStep(Applicant $applicant): int
    {
        if (! $applicant->organizationDetail) {
            return 0;
        }
        if (! $applicant->aiSolution) {
            return 1;
        }
        if (! $applicant->impactDetail) {
            return 2;
        }

        return 2;
    }

    private function markInProgress(Applicant $applicant): void
    {
        if ($applicant->application_status !== Applicant::STATUS_SUBMITTED) {
            $applicant->update(['application_status' => Applicant::STATUS_IN_PROGRESS]);
        }
    }

    private function thankYouView(Applicant $applicant): View
    {
        $template = PageTemplate::query()
            ->where('key', PageTemplate::APPLICATION_SUBMIT)
            ->firstOrFail();

        $rendered = $template->render([
            'contact_person_name' => $applicant->contact_person_name,
            'organisation_name' => $applicant->organisation_name,
            'email' => $applicant->email,
        ]);

        return view('thanks', [
            'pageTitle' => $rendered['page_title'],
            'html' => $rendered['html'],
            'showSignOut' => true,
        ]);
    }
}
