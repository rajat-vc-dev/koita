<?php

namespace Tests\Feature;

use App\Mail\TemplatedMail;
use App\Models\Applicant;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\PageTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplyGateAndApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EmailTemplateSeeder::class);
        $this->seed(PageTemplateSeeder::class);
    }

    public function test_gate_rejects_invalid_credentials(): void
    {
        $this->from(route('apply'))
            ->post(route('apply.gate'), [
                'email' => 'nope@example.org',
                'access_code' => 'BAD',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_gate_authenticates_and_opens_application(): void
    {
        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF2345',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->post(route('apply.gate'), [
            'email' => 'jane@example.org',
            'access_code' => 'KF-ABCDEF2345',
        ])->assertRedirect(route('apply.application'));

        $this->assertEquals($applicant->id, session('applicant_id'));

        $this->get(route('apply.application'))
            ->assertOk()
            ->assertSee('Organization Details');
    }

    public function test_gate_blocks_rejected_applicants(): void
    {
        Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF2345',
            'application_status' => Applicant::STATUS_REJECTED,
            'rejected_at' => now(),
        ]);

        $this->from(route('apply'))
            ->post(route('apply.gate'), [
                'email' => 'jane@example.org',
                'access_code' => 'KF-ABCDEF2345',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_three_steps_can_be_saved(): void
    {
        Mail::fake();
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);

        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1111',
            'application_status' => Applicant::STATUS_INVITED,
        ]);

        $this->withSession(['applicant_id' => $applicant->id])
            ->post(route('apply.application.step1'), [
                'organization_name' => 'Org Updated',
                'primary_areas' => ['Education', 'Other'],
                'primary_area_other' => 'Disability inclusion',
                'mission_overview' => 'We work across India.',
            ])->assertRedirect(route('apply.application', ['step' => 1]));

        $this->assertDatabaseHas('applicant_organization_details', [
            'applicant_id' => $applicant->id,
            'primary_area_other' => 'Disability inclusion',
        ]);

        $this->withSession(['applicant_id' => $applicant->id])
            ->post(route('apply.application.step2'), [
                'solution_name' => 'AI Helper',
                'social_problem' => 'Access gaps',
                'solution_description' => 'Uses NLP',
                'users_of_solution' => ['Beneficiaries directly'],
                'technical_details' => 'Python stack',
                'demo_video_url' => 'https://drive.google.com/demo',
                'testimonial_video_url' => 'https://drive.google.com/testimonial',
            ])->assertRedirect(route('apply.application', ['step' => 2]));

        $pdf = storage_path('framework/testing/deck.pdf');
        if (! is_dir(dirname($pdf))) {
            mkdir(dirname($pdf), 0755, true);
        }
        file_put_contents($pdf, '%PDF-1.4 test');

        $this->withSession(['applicant_id' => $applicant->id])
            ->post(route('apply.application.step3'), [
                'adoption_stage' => 'dev',
                'success_measurement' => 'Surveys',
                'outcomes_impact' => 'Improved outcomes',
                'deck' => new UploadedFile($pdf, 'deck.pdf', 'application/pdf', null, true),
                'consent' => '1',
            ])->assertRedirect(route('apply.application'));

        $fresh = $applicant->fresh();
        $this->assertEquals(Applicant::STATUS_SUBMITTED, $fresh->application_status);
        $this->assertDatabaseHas('applicant_impact_details', [
            'applicant_id' => $applicant->id,
            'adoption_stage' => 'dev',
        ]);
        Storage::disk($disk)->assertExists($fresh->impactDetail->deck_path);

        Mail::assertSent(TemplatedMail::class, 2);
        Mail::assertSent(TemplatedMail::class, function (TemplatedMail $mail) use ($applicant) {
            return $mail->hasTo($applicant->email);
        });
        Mail::assertSent(TemplatedMail::class, function (TemplatedMail $mail) {
            return $mail->hasTo(config('koita.admin_email'));
        });
    }

    public function test_submitted_application_rejects_further_step_posts(): void
    {
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
            ->post(route('apply.application.step1'), [
                'organization_name' => 'Should Fail',
                'primary_areas' => ['Education'],
                'mission_overview' => 'Nope',
            ])->assertForbidden();
    }
}
