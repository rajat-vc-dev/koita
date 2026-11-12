<?php

namespace Tests\Feature\Admin;

use App\Models\Applicant;
use App\Models\ShortlistEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShortlistAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['koita.shortlist_enabled' => true]);
    }

    public function test_disabled_shortlist_hides_navigation_and_blocks_routes(): void
    {
        config(['koita.shortlist_enabled' => false]);
        $applicant = $this->applicant(Applicant::STATUS_SUBMITTED);
        $entry = ShortlistEntry::query()->create([
            'applicant_id' => $applicant->id,
            'title' => $applicant->organisation_name,
        ]);
        $this->actingAs(User::factory()->superAdmin()->create());

        $indexUrl = route('admin.shortlist.index');
        $scoreboardUrl = route('admin.scoreboard');
        $this->get(route('admin.dashboard'))->assertOk()
            ->assertDontSee($indexUrl, false)
            ->assertDontSee($scoreboardUrl, false);
        $this->get($scoreboardUrl)->assertNotFound();
        $this->get($indexUrl)->assertNotFound();
        $this->post(route('admin.shortlist.store'))->assertNotFound();
        $this->put(route('admin.shortlist.update', $entry))->assertNotFound();
        $this->patch(route('admin.shortlist.status', $entry))->assertNotFound();
        $this->get(route('admin.shortlist.logo', $entry))->assertNotFound();
        $this->delete(route('admin.shortlist.destroy', $entry))->assertNotFound();
        $this->assertModelExists($entry);
    }

    public function test_only_submitted_registrations_can_be_added_once(): void
    {
        $submitted = $this->applicant(Applicant::STATUS_SUBMITTED);
        $draft = $this->applicant(Applicant::STATUS_IN_PROGRESS);
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->get(route('admin.shortlist.index'))
            ->assertOk()
            ->assertSee($submitted->email)
            ->assertDontSee($draft->email);

        $this->post(route('admin.shortlist.store'), ['applicant_id' => $draft->id])
            ->assertSessionHasErrors('applicant_id');

        $this->post(route('admin.shortlist.store'), ['applicant_id' => $submitted->id])->assertRedirect();
        $this->post(route('admin.shortlist.store'), ['applicant_id' => $submitted->id])->assertRedirect();
        $this->assertDatabaseCount('shortlist_entries', 1);
        $this->assertDatabaseHas('shortlist_entries', [
            'applicant_id' => $submitted->id,
            'title' => $submitted->organisation_name,
        ]);
    }

    public function test_admin_can_edit_disable_enable_and_remove_a_shortlist_profile(): void
    {
        $disk = Storage::fake('local');
        $applicant = $this->applicant(Applicant::STATUS_SUBMITTED);
        $entry = ShortlistEntry::query()->create([
            'applicant_id' => $applicant->id,
            'title' => $applicant->organisation_name,
        ]);
        $this->actingAs(User::factory()->superAdmin()->create());

        $editUrl = route('admin.shortlist.index', ['edit' => $entry->id]);
        $this->get($editUrl)->assertOk()->assertSee('Save profile');
        $this->put(route('admin.shortlist.update', $entry), [
            'title' => 'Edited profile',
            'problem' => 'Problem statement',
            'pdf_url' => 'https://example.org/details.pdf',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect($editUrl)->assertSessionHasNoErrors();

        $entry->refresh();
        $this->assertSame('Edited profile', $entry->title);
        $disk->assertExists($entry->logo_path);
        $this->get(route('admin.shortlist.logo', $entry))->assertOk();

        $this->patch(route('admin.shortlist.status', $entry), ['is_enabled' => 0])->assertRedirect();
        $this->assertFalse($entry->refresh()->is_enabled);
        $this->patch(route('admin.shortlist.status', $entry), ['is_enabled' => 1])->assertRedirect();
        $this->assertTrue($entry->refresh()->is_enabled);

        $this->delete(route('admin.shortlist.destroy', $entry))->assertRedirect();
        $this->assertModelMissing($entry);
        $disk->assertMissing($entry->logo_path);
        $this->assertSame(Applicant::STATUS_SUBMITTED, $applicant->refresh()->application_status);
        $this->get(route('admin.shortlist.index'))->assertSee($applicant->email);
    }

    private function applicant(string $status): Applicant
    {
        return Applicant::query()->create([
            'organisation_name' => 'Organization '.$status,
            'website' => 'https://example.org',
            'contact_person_name' => 'Contact Person',
            'designation' => 'Director',
            'email' => $status.'@example.org',
            'mobile' => '+911234567890',
            'application_status' => $status,
        ]);
    }
}
