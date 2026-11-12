<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentResubmitRequest;
use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\EmailTemplate;
use App\Models\PageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentResubmitController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $applicant = $this->findValidApplicant($token);

        if (! $applicant) {
            return redirect()
                ->route('home')
                ->withErrors(['documents' => 'This document resubmit link is invalid or has expired.']);
        }

        return view('documents.resubmit', compact('applicant', 'token'));
    }

    public function file(string $token, string $type): StreamedResponse|RedirectResponse
    {
        $applicant = $this->findValidApplicant($token);

        if (! $applicant) {
            abort(404);
        }

        $disk = (string) config('koita.uploads_disk', 'local');
        $path = $applicant->filePathForType($type);

        abort_unless($path && Storage::disk($disk)->exists($path), 404);

        return Storage::disk($disk)->download($path);
    }

    public function update(StoreDocumentResubmitRequest $request, string $token): RedirectResponse
    {
        $applicant = $this->findValidApplicant($token);

        if (! $applicant) {
            return redirect()
                ->route('home')
                ->withErrors(['documents' => 'This document resubmit link is invalid or has expired.']);
        }

        $disk = (string) config('koita.uploads_disk', 'local');
        $docMap = Applicant::documentsByType()[$applicant->organisation_type] ?? [];
        $paths = [];

        foreach ($applicant->requestedResubmitFields() as $input) {
            $column = $docMap[$input] ?? null;
            if (! $column || ! $request->hasFile($input)) {
                continue;
            }

            $old = $applicant->{$column};
            if ($old) {
                Storage::disk($disk)->delete($old);
            }
            $paths[$column] = $request->file($input)->store('applicants/certificates', $disk);
        }

        $applicant->update([
            'website' => $request->string('website')->toString(),
            'contact_person_name' => $request->string('contact_person_name')->toString(),
            'designation' => $request->string('designation')->toString(),
            'mobile' => $request->string('mobile')->toString(),
            'is_registered' => true,
            ...$paths,
            'documents_resubmitted_at' => now(),
            'document_resubmit_token' => null,
            'document_resubmit_expires_at' => null,
            'document_resubmit_fields' => null,
        ]);

        $applicant->refresh();
        $this->sendResubmittedNotifications($applicant);

        return redirect()
            ->route('thanks')
            ->with('thank_you', [
                'key' => PageTemplate::DOCUMENT_RESUBMIT,
                'data' => [
                    'organisation_name' => $applicant->organisation_name,
                    'contact_person_name' => $applicant->contact_person_name,
                    'email' => $applicant->email,
                ],
            ]);
    }

    private function sendResubmittedNotifications(Applicant $applicant): void
    {
        $data = [
            'organisation_name' => $applicant->organisation_name,
            'organisation_type' => $applicant->organisationTypeLabel(),
            'website' => $applicant->website,
            'contact_person_name' => $applicant->contact_person_name,
            'designation' => $applicant->designation,
            'email' => $applicant->email,
            'mobile' => $applicant->mobile,
            'resubmitted_at' => $applicant->documents_resubmitted_at?->format('Y-m-d H:i') ?: now()->format('Y-m-d H:i'),
            'documents_list' => $applicant->documentsListForEmail(),
            'section_8_certificate_url' => $applicant->section8CertificateUrl() ?: 'NA',
            'certificate_12a_url' => $applicant->certificate12aUrl() ?: 'NA',
            'certificate_80g_url' => $applicant->certificate80gUrl() ?: 'NA',
            'coi_url' => $applicant->coiUrl() ?: 'NA',
            'pan_url' => $applicant->panUrl() ?: 'NA',
            'gst_url' => $applicant->gstUrl() ?: 'NA',
            'admin_applicant_url' => route('admin.applicants.show', $applicant),
        ];

        $userTemplate = EmailTemplate::query()
            ->where('key', EmailTemplate::USER_DOCUMENTS_RESUBMITTED)
            ->first();
        $adminTemplate = EmailTemplate::query()
            ->where('key', EmailTemplate::ADMIN_DOCUMENTS_RESUBMITTED)
            ->first();

        if ($userTemplate) {
            Mail::to($applicant->email)->send(new TemplatedMail($userTemplate, $data));
        }

        if ($adminTemplate) {
            Mail::to(config('koita.admin_email'))->send(new TemplatedMail($adminTemplate, $data));
        }
    }

    private function findValidApplicant(string $token): ?Applicant
    {
        return Applicant::query()
            ->where('document_resubmit_token', $token)
            ->where('is_registered', true)
            ->where('application_status', '!=', Applicant::STATUS_REJECTED)
            ->whereNotNull('document_resubmit_expires_at')
            ->where('document_resubmit_expires_at', '>', now())
            ->first();
    }
}
