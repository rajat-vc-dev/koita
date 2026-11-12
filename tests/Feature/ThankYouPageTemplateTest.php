<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\PageTemplate;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\PageTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThankYouPageTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EmailTemplateSeeder::class);
        $this->seed(PageTemplateSeeder::class);
    }

    public function test_interest_with_documents_shows_yes_thank_you_template(): void
    {
        Mail::fake();
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);

        $pdf = storage_path('framework/testing/80g-thanks.pdf');
        if (! is_dir(dirname($pdf))) {
            mkdir(dirname($pdf), 0755, true);
        }
        file_put_contents($pdf, '%PDF-1.4 test');

        PageTemplate::query()->where('key', PageTemplate::INTEREST_YES)->update([
            'html' => '<section><h1>YES_THANKS_MARKER</h1><p>{{access_code}}</p></section>',
        ]);

        $this->post(route('interest.store'), [
            'organisation_name' => 'Test Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane Doe',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+91 98765 43210',
            'organisation_type' => Applicant::TYPE_SECTION_8,
            'certificate_80g' => new UploadedFile($pdf, '80g.pdf', 'application/pdf', null, true),
        ])->assertRedirect(route('thanks'));

        $applicant = Applicant::query()->firstOrFail();

        $this->get(route('thanks'))
            ->assertOk()
            ->assertSee('YES_THANKS_MARKER', false)
            ->assertSee($applicant->access_code, false);
    }

    public function test_interest_without_documents_is_rejected(): void
    {
        Mail::fake();

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

    public function test_application_submitted_uses_application_thank_you_template(): void
    {
        PageTemplate::query()->where('key', PageTemplate::APPLICATION_SUBMIT)->update([
            'html' => '<section><h1>APP_THANKS_MARKER</h1><p>{{contact_person_name}}</p></section>',
        ]);

        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF9999',
            'application_status' => Applicant::STATUS_SUBMITTED,
            'application_submitted_at' => now(),
        ]);

        $this->withSession(['applicant_id' => $applicant->id])
            ->get(route('apply.application'))
            ->assertOk()
            ->assertSee('APP_THANKS_MARKER', false)
            ->assertSee('Jane', false)
            ->assertSee('Sign out', false);
    }
}
