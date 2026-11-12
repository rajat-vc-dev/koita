<?php

namespace Tests\Feature;

use App\Mail\PeopleChoiceOtp;
use App\Models\Applicant;
use App\Models\PeopleChoiceVoter;
use App\Models\ShortlistEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PeopleChoiceVotingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['koita.people_choice_voting_enabled' => true]);
    }

    public function test_email_verification_opens_the_ballot_and_a_ranked_vote_is_saved_once(): void
    {
        Mail::fake();
        $first = $this->entry('First applicant');
        $second = $this->entry('Second applicant');
        $third = $this->entry('Third applicant');
        $disabled = $this->entry('Disabled applicant', enabled: false);
        $this->entry('Rejected applicant', status: Applicant::STATUS_REJECTED);
        $page = route('people-choice.show');

        $this->get($page)->assertOk()->assertSee('Audience Poll')->assertDontSee($first->title);
        $this->post(route('people-choice.vote'), ['choices' => [$first->id, $second->id, $third->id]])
            ->assertRedirect($page);

        $this->from($page)->post(route('people-choice.register'), [
            'name' => 'Test Voter',
            'email' => 'Voter@Example.org',
            'marketing_consent' => '1',
        ])->assertRedirect($page);
        $this->get($page)->assertOk()->assertSee('Verify your email')->assertDontSee('Resend');

        $mail = Mail::sent(PeopleChoiceOtp::class)->sole();
        $this->assertTrue($mail->hasTo('voter@example.org'));
        $this->assertSame(1, preg_match('/\b([0-9]{6})\b/', $mail->render(), $match));
        $code = $match[1];
        $voter = PeopleChoiceVoter::query()->sole();
        $this->assertTrue(Hash::check($code, $voter->otp_hash));
        $this->assertNull($voter->verified_at);
        $this->get($page)->assertDontSee($code);

        $this->from($page)->post(route('people-choice.verify'), ['code' => $code === '000000' ? '000001' : '000000'])
            ->assertSessionHasErrors('code');
        $this->assertSame(1, $voter->refresh()->otp_attempts);

        $this->post(route('people-choice.verify'), ['code' => $code])->assertRedirect($page);
        $this->assertNull($voter->refresh()->otp_hash);
        $this->assertNotNull($voter->marketing_consented_at);
        $this->get($page)->assertOk()
            ->assertSee($first->title)->assertSee($second->title)->assertSee($third->title)
            ->assertDontSee($disabled->title)->assertDontSee('Rejected applicant');

        $submit = route('people-choice.vote');
        $this->from($page)->post($submit, ['choices' => [$first->id, $second->id]])->assertSessionHasErrors('choices');
        $this->from($page)->post($submit, ['choices' => array_fill(0, 6, $first->id)])->assertSessionHasErrors('choices');
        $this->from($page)->post($submit, ['choices' => [$first->id, $first->id, $third->id]])->assertSessionHasErrors('choices.0');
        $this->from($page)->post($submit, ['choices' => [$first->id, $second->id, $disabled->id]])->assertSessionHasErrors('choices');
        $this->assertDatabaseCount('people_choice_votes', 0);

        $this->post($submit, ['choices' => [$third->id, $first->id, $second->id]])->assertRedirect($page);
        $this->assertSame(
            [$third->applicant_id, $first->applicant_id, $second->applicant_id],
            DB::table('people_choice_votes')->orderBy('rank')->pluck('applicant_id')->all(),
        );
        $this->get($page)->assertOk()->assertSee('Thank you for voting!');
        $this->post($submit, ['choices' => [$first->id, $second->id, $third->id]])->assertRedirect($page);
        $this->assertDatabaseCount('people_choice_votes', 3);

        $first->delete();
        $this->assertDatabaseCount('people_choice_votes', 3);
    }

    public function test_an_expired_code_cannot_verify_a_voter(): void
    {
        Mail::fake();
        $page = route('people-choice.show');
        $this->post(route('people-choice.register'), ['name' => 'Voter', 'email' => 'voter@example.org']);
        preg_match('/\b([0-9]{6})\b/', Mail::sent(PeopleChoiceOtp::class)->sole()->render(), $match);

        $this->travel(11)->minutes();

        $this->from($page)->post(route('people-choice.verify'), ['code' => $match[1]])->assertSessionHasErrors('code');
        $this->assertNull(PeopleChoiceVoter::query()->sole()->verified_at);
    }

    public function test_the_feature_flag_blocks_public_access(): void
    {
        config(['koita.people_choice_voting_enabled' => false]);

        $this->get(route('people-choice.show'))->assertNotFound();
        $this->post(route('people-choice.register'))->assertNotFound();
        $this->post(route('people-choice.vote'))->assertNotFound();
    }

    private function entry(string $title, bool $enabled = true, string $status = Applicant::STATUS_SUBMITTED): ShortlistEntry
    {
        $applicant = Applicant::query()->create([
            'organisation_name' => $title,
            'website' => 'https://example.org',
            'contact_person_name' => 'Contact Person',
            'designation' => 'Director',
            'email' => str_replace(' ', '.', strtolower($title)).'@example.org',
            'mobile' => '+911234567890',
            'application_status' => $status,
        ]);

        return ShortlistEntry::query()->create([
            'applicant_id' => $applicant->id,
            'title' => $title,
            'is_enabled' => $enabled,
        ]);
    }
}
