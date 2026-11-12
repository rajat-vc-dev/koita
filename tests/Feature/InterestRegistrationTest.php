<?php

namespace Tests\Feature;

use App\Mail\TemplatedMail;
use App\Models\Applicant;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InterestRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EmailTemplateSeeder::class);
    }

    public function test_section_8_registration_creates_access_code_and_sends_mail(): void
    {
        Mail::fake();
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);

        $pdf = storage_path('framework/testing/80g.pdf');
        if (! is_dir(dirname($pdf))) {
            mkdir(dirname($pdf), 0755, true);
        }
        file_put_contents($pdf, '%PDF-1.4 test');

        $response = $this->post(route('interest.store'), [
            'organisation_name' => 'Test Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane Doe',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+91 98765 43210',
            'organisation_type' => Applicant::TYPE_SECTION_8,
            'certificate_80g' => new UploadedFile($pdf, '80g.pdf', 'application/pdf', null, true),
        ]);

        $response->assertRedirect(route('thanks'));

        $applicant = Applicant::query()->first();
        $this->assertNotNull($applicant);
        $this->assertTrue($applicant->is_registered);
        $this->assertSame(Applicant::TYPE_SECTION_8, $applicant->organisation_type);
        $this->assertNotNull($applicant->access_code);
        $this->assertStringStartsWith(config('koita.access_code_prefix'), $applicant->access_code);
        Storage::disk($disk)->assertExists($applicant->certificate_80g_path);

        Mail::assertSent(TemplatedMail::class, 2);
    }

    public function test_pvt_registration_accepts_coi(): void
    {
        Mail::fake();
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);

        $pdf = storage_path('framework/testing/coi.pdf');
        if (! is_dir(dirname($pdf))) {
            mkdir(dirname($pdf), 0755, true);
        }
        file_put_contents($pdf, '%PDF-1.4 test');

        $this->post(route('interest.store'), [
            'organisation_name' => 'Test Pvt',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane Doe',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+91 98765 43210',
            'organisation_type' => Applicant::TYPE_PVT_LLP,
            'coi' => new UploadedFile($pdf, 'coi.pdf', 'application/pdf', null, true),
        ])->assertRedirect(route('thanks'));

        $applicant = Applicant::query()->first();
        $this->assertSame(Applicant::TYPE_PVT_LLP, $applicant->organisation_type);
        $this->assertNotNull($applicant->coi_path);
        $this->assertNotNull($applicant->access_code);
    }

    public function test_organisation_type_requires_at_least_one_document(): void
    {
        $this->from(route('home'))
            ->post(route('interest.store'), [
                'organisation_name' => 'Test Org',
                'website' => 'https://example.org',
                'contact_person_name' => 'Jane Doe',
                'designation' => 'Director',
                'email' => 'jane@example.org',
                'mobile' => '+91 98765 43210',
                'organisation_type' => Applicant::TYPE_TRUST,
            ])
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('applicants', 0);
    }
}
