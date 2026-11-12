<?php

namespace Tests\Feature;

use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\User;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentResubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EmailTemplateSeeder::class);
    }

    public function test_admin_can_request_document_resubmit_for_eligible_applicant(): void
    {
        Mail::fake();
        $admin = User::factory()->superAdmin()->create();
        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'organisation_type' => Applicant::TYPE_SECTION_8,
            'is_registered' => true,
            'certificate_80g_path' => 'applicants/certificates/x.pdf',
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.applicants.resubmit-documents', $applicant))
            ->assertRedirect();

        Mail::assertSent(TemplatedMail::class);
        $fresh = $applicant->fresh();
        $this->assertNotNull($fresh->document_resubmit_token);
        $this->assertNotNull($fresh->document_resubmit_expires_at);
        $this->assertTrue(
            $fresh->document_resubmit_expires_at->between(
                now()->addDays((int) config('koita.document_resubmit_days'))->subMinute(),
                now()->addDays((int) config('koita.document_resubmit_days'))->addMinute(),
            )
        );
    }

    public function test_ineligible_applicant_cannot_be_asked_to_resubmit(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => false,
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.applicants.show', $applicant))
            ->post(route('admin.applicants.resubmit-documents', $applicant))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_applicant_can_resubmit_documents_and_update_details(): void
    {
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);

        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'organisation_type' => Applicant::TYPE_SECTION_8,
            'is_registered' => true,
            'certificate_80g_path' => 'applicants/certificates/old.pdf',
            'access_code' => 'KF-ABCDEF1234',
            'document_resubmit_token' => 'token-abc',
            'document_resubmit_expires_at' => now()->addDay(),
            'application_status' => Applicant::STATUS_DOCS_RESUBMIT,
        ]);

        $this->get(route('documents.resubmit', 'token-abc'))
            ->assertOk()
            ->assertSee('Resubmit Registration Documents')
            ->assertSee($applicant->organisation_name);

        $pdf = storage_path('framework/testing/80g-resubmit.pdf');
        if (! is_dir(dirname($pdf))) {
            mkdir(dirname($pdf), 0755, true);
        }
        file_put_contents($pdf, '%PDF-1.4 test');

        $this->post(route('documents.resubmit.update', 'token-abc'), [
            'organisation_name' => 'Org Updated',
            'website' => 'https://updated.org',
            'contact_person_name' => 'Jane Updated',
            'designation' => 'CEO',
            'mobile' => '+919876543210',
            'organisation_type' => Applicant::TYPE_SECTION_8,
            'certificate_12a' => new UploadedFile($pdf, '12a.pdf', 'application/pdf', null, true),
        ])->assertRedirect(route('thanks'));

        $fresh = $applicant->fresh();
        $this->assertSame('Org Updated', $fresh->organisation_name);
        $this->assertSame('jane@example.org', $fresh->email);
        $this->assertNotNull($fresh->certificate_12a_path);
        $this->assertNull($fresh->document_resubmit_token);
        $this->assertNotNull($fresh->documents_resubmitted_at);
        Storage::disk($disk)->assertExists($fresh->certificate_12a_path);
    }

    public function test_expired_resubmit_token_is_rejected(): void
    {
        Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1234',
            'document_resubmit_token' => 'token-expired',
            'document_resubmit_expires_at' => now()->subMinute(),
            'application_status' => Applicant::STATUS_DOCS_RESUBMIT,
        ]);

        $this->get(route('documents.resubmit', 'token-expired'))
            ->assertRedirect(route('home'));
    }
}
