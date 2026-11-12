<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterestRegistrationRequest;
use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\EmailTemplate;
use App\Models\PageTemplate;
use App\Services\AccessCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class InterestRegistrationController extends Controller
{
    public function store(
        StoreInterestRegistrationRequest $request,
        AccessCodeGenerator $accessCodes,
    ): RedirectResponse {
        $disk = (string) config('koita.uploads_disk', 'local');
        $type = $request->string('organisation_type')->toString();
        $docMap = Applicant::documentsByType()[$type];

        $paths = [
            'certificate_80g_path' => null,
            'certificate_12a_path' => null,
            'section_8_certificate_path' => null,
            'coi_path' => null,
            'pan_path' => null,
            'gst_path' => null,
        ];

        foreach ($docMap as $input => $column) {
            if ($request->hasFile($input)) {
                $paths[$column] = $request->file($input)->store('applicants/certificates', $disk);
            }
        }

        $isRegistered = collect($paths)->contains(fn ($path) => filled($path));
        $accessCode = $isRegistered ? $accessCodes->generate() : null;

        $applicant = Applicant::query()->create([
            'organisation_name' => $request->string('organisation_name')->toString(),
            'website' => $request->string('website')->toString(),
            'contact_person_name' => $request->string('contact_person_name')->toString(),
            'designation' => $request->string('designation')->toString(),
            'email' => $request->string('email')->toString(),
            'mobile' => $request->string('mobile')->toString(),
            'organisation_type' => $type,
            'terms_accepted_at' => now(),
            'is_registered' => $isRegistered,
            ...$paths,
            'access_code' => $accessCode,
        ]);

        $this->sendNotifications($applicant);

        $thankYouKey = $applicant->is_registered
            ? PageTemplate::INTEREST_YES
            : PageTemplate::INTEREST_NO;

        return redirect()
            ->route('thanks')
            ->with('thank_you', [
                'key' => $thankYouKey,
                'data' => [
                    'organisation_name' => $applicant->organisation_name,
                    'website' => $applicant->website,
                    'contact_person_name' => $applicant->contact_person_name,
                    'designation' => $applicant->designation,
                    'email' => $applicant->email,
                    'mobile' => $applicant->mobile,
                    'organisation_type' => $applicant->organisationTypeLabel(),
                    'access_code' => $applicant->access_code,
                ],
            ]);
    }

    private function sendNotifications(Applicant $applicant): void
    {
        $userKey = $applicant->is_registered ? EmailTemplate::USER_YES : EmailTemplate::USER_NO;
        $adminKey = $applicant->is_registered ? EmailTemplate::ADMIN_YES : EmailTemplate::ADMIN_NO;

        $data = [
            'organisation_name' => $applicant->organisation_name,
            'website' => $applicant->website,
            'contact_person_name' => $applicant->contact_person_name,
            'designation' => $applicant->designation,
            'email' => $applicant->email,
            'mobile' => $applicant->mobile,
            'organisation_type' => $applicant->organisationTypeLabel(),
            'access_code' => $applicant->access_code,
            'documents_list' => $applicant->documentsListForEmail(),
            'section_8_certificate_url' => $applicant->section8CertificateUrl() ?: 'NA',
            'certificate_12a_url' => $applicant->certificate12aUrl() ?: 'NA',
            'certificate_80g_url' => $applicant->certificate80gUrl() ?: 'NA',
            'coi_url' => $applicant->coiUrl() ?: 'NA',
            'pan_url' => $applicant->panUrl() ?: 'NA',
            'gst_url' => $applicant->gstUrl() ?: 'NA',
        ];

        $userTemplate = EmailTemplate::query()->where('key', $userKey)->first();
        $adminTemplate = EmailTemplate::query()->where('key', $adminKey)->first();

        if ($userTemplate) {
            Mail::to($applicant->email)->send(new TemplatedMail($userTemplate, $data));
        }

        if ($adminTemplate) {
            Mail::to(config('koita.admin_email'))->send(new TemplatedMail($adminTemplate, $data));
        }
    }
}
