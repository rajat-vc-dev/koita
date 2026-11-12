<?php

namespace Tests\Feature\Admin;

use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\User;
use Database\Seeders\EmailTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicantAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(EmailTemplateSeeder::class);
    }

    public function test_superadmin_can_view_applicants_index(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.applicants.index'))
            ->assertOk()
            ->assertSee('Applicants registration')
            ->assertSee('Export Excel')
            ->assertSee('filter_registered', false);
    }

    public function test_datatable_hides_actions_when_registered_no(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Applicant::query()->create([
            'organisation_name' => 'Yes Org',
            'website' => 'https://yes.example.org',
            'contact_person_name' => 'Yes Person',
            'designation' => 'Director',
            'email' => 'yes@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        Applicant::query()->create([
            'organisation_name' => 'No Org',
            'website' => 'https://no.example.org',
            'contact_person_name' => 'No Person',
            'designation' => 'Director',
            'email' => 'no@example.org',
            'mobile' => '+911234567891',
            'is_registered' => false,
            'access_code' => null,
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.applicants.data', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'columns' => [
                    ['data' => 'organisation_name', 'name' => 'organisation_name', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'contact_person_name', 'name' => 'contact_person_name', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'email', 'name' => 'email', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'organisation_type_label', 'name' => 'organisation_type', 'searchable' => 'false', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'status_label', 'name' => 'application_status', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'registered_at', 'name' => 'created_at', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'actions', 'name' => 'actions', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '']],
                ],
                'order' => [['column' => 0, 'dir' => 'asc']],
                'search' => ['value' => ''],
            ]))
            ->assertOk();

        $rows = collect($response->json('data'));
        $yes = $rows->firstWhere('organisation_name', 'Yes Org');
        $no = $rows->firstWhere('organisation_name', 'No Org');

        $this->assertNotNull($yes);
        $this->assertNotNull($no);
        $this->assertStringContainsString('ti-eye', $yes['actions']);
        $this->assertStringContainsString('ti-email', $yes['actions']);
        $this->assertStringContainsString('ti-eye', $no['actions']);
        $this->assertStringNotContainsString('ti-email', $no['actions']);
        $this->assertStringNotContainsString('—', $no['actions']);
    }

    public function test_datatable_filters_by_registered_and_status(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Applicant::query()->create([
            'organisation_name' => 'Yes Org',
            'website' => 'https://yes.example.org',
            'contact_person_name' => 'Yes Person',
            'designation' => 'Director',
            'email' => 'yes@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INVITED,
        ]);

        Applicant::query()->create([
            'organisation_name' => 'No Org',
            'website' => 'https://no.example.org',
            'contact_person_name' => 'No Person',
            'designation' => 'Director',
            'email' => 'no@example.org',
            'mobile' => '+911234567891',
            'is_registered' => false,
            'access_code' => null,
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.applicants.data', [
                'draw' => 1,
                'start' => 0,
                'length' => 10,
                'registered' => 'yes',
                'status' => Applicant::STATUS_INVITED,
                'columns' => [
                    ['data' => 'organisation_name', 'name' => 'organisation_name', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                ],
                'order' => [['column' => 0, 'dir' => 'asc']],
                'search' => ['value' => ''],
            ]))
            ->assertOk();

        $this->assertSame(1, $response->json('recordsFiltered'));
        $this->assertSame('Yes Org', $response->json('data.0.organisation_name'));
    }

    public function test_export_excel_uses_filters(): void
    {
        $admin = User::factory()->superAdmin()->create();

        Applicant::query()->create([
            'organisation_name' => 'Yes Org',
            'website' => 'https://yes.example.org',
            'contact_person_name' => 'Yes Person',
            'designation' => 'Director',
            'email' => 'yes@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        Applicant::query()->create([
            'organisation_name' => 'No Org',
            'website' => 'https://no.example.org',
            'contact_person_name' => 'No Person',
            'designation' => 'Director',
            'email' => 'no@example.org',
            'mobile' => '+911234567891',
            'is_registered' => false,
            'access_code' => null,
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.applicants.export', ['registered' => 'no']))
            ->assertOk();

        $csv = $response->streamedContent();
        $this->assertStringContainsString('No Org', $csv);
        $this->assertStringNotContainsString('Yes Org', $csv);
        $this->assertStringContainsString('Organisation type', $csv);
        $this->assertStringContainsString('Has COI', $csv);
        $this->assertStringContainsString('Mission overview', $csv);
        $this->assertStringContainsString('Solution name', $csv);
        $this->assertStringContainsString('Adoption stage', $csv);
    }

    public function test_admin_can_reject_application(): void
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
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.applicants.reject', $applicant))
            ->assertRedirect();

        $fresh = $applicant->fresh();
        $this->assertSame(Applicant::STATUS_REJECTED, $fresh->application_status);
        $this->assertNotNull($fresh->rejected_at);
        Mail::assertSent(TemplatedMail::class);
    }

    public function test_invite_sends_email_when_access_code_present(): void
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
            'is_registered' => true,
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.applicants.invite', $applicant))
            ->assertRedirect();

        Mail::assertSent(TemplatedMail::class);
        $this->assertEquals(Applicant::STATUS_INVITED, $applicant->fresh()->application_status);
    }

    public function test_guest_cannot_download_applicant_files(): void
    {
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);
        Storage::disk($disk)->put('applicants/certificates/test.pdf', '%PDF-1.4 test');

        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'certificate_80g_path' => 'applicants/certificates/test.pdf',
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->get(route('admin.applicants.files.show', [
            'applicant' => $applicant,
            'type' => 'certificate_80g',
        ]))->assertRedirect(route('admin.login'));
    }

    public function test_superadmin_can_download_applicant_files(): void
    {
        $disk = (string) config('koita.uploads_disk', 'local');
        Storage::fake($disk);
        Storage::disk($disk)->put('applicants/certificates/test.pdf', '%PDF-1.4 test');

        $admin = User::factory()->superAdmin()->create();
        $applicant = Applicant::query()->create([
            'organisation_name' => 'Org',
            'website' => 'https://example.org',
            'contact_person_name' => 'Jane',
            'designation' => 'Director',
            'email' => 'jane@example.org',
            'mobile' => '+911234567890',
            'is_registered' => true,
            'certificate_80g_path' => 'applicants/certificates/test.pdf',
            'access_code' => 'KF-ABCDEF1234',
            'application_status' => Applicant::STATUS_INTEREST,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.applicants.files.show', [
                'applicant' => $applicant,
                'type' => 'certificate_80g',
            ]))
            ->assertOk();
    }
}
