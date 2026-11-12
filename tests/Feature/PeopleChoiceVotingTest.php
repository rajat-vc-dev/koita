<?php

namespace Tests\Feature;

use App\Mail\TemplatedMail;
use App\Models\Applicant;
use App\Models\EmailTemplate;
use App\Models\PeopleChoiceVoter;
use App\Models\ShortlistEntry;
use App\Models\User;
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

        $mail = Mail::sent(TemplatedMail::class)->sole();
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
        $this->get($page)->assertOk()
            ->assertSee('Thank you for voting!')
            ->assertSee('A confirmation email was sent to')
            ->assertSeeInOrder([$third->title, $first->title, $second->title]);

        $confirmation = Mail::sent(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->template->key === EmailTemplate::USER_PEOPLE_CHOICE_CONFIRMATION)->sole();
        $notification = Mail::sent(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->template->key === EmailTemplate::ADMIN_PEOPLE_CHOICE_BALLOT)->sole();
        $this->assertTrue($confirmation->hasTo('voter@example.org'));
        $this->assertTrue($notification->hasTo(config('koita.admin_email')));
        $choices = "1st choice: Third applicant\n2nd choice: First applicant\n3rd choice: Second applicant";
        $this->assertSame($choices, $confirmation->data['ranked_choices']);
        $this->assertSame($choices, $notification->data['ranked_choices']);

        $this->flushSession();
        $this->post(route('people-choice.register'), ['name' => 'Test Voter', 'email' => 'VOTER@example.org'])->assertRedirect($page);
        $otp = Mail::sent(TemplatedMail::class, fn (TemplatedMail $mail) => $mail->template->key === EmailTemplate::USER_PEOPLE_CHOICE_OTP)->last();
        $this->post(route('people-choice.verify'), ['code' => $otp->data['otp_code']])->assertRedirect($page);
        $this->get($page)->assertOk()->assertSee('You’ve already voted')->assertDontSee('Submit Votes');
        $this->assertDatabaseCount('people_choice_voters', 1);
        $this->post($submit, ['choices' => [$first->id, $second->id, $third->id]])->assertRedirect($page);
        $this->assertDatabaseCount('people_choice_votes', 3);
        Mail::assertSent(TemplatedMail::class, 4);

        $first->delete();
        $this->assertDatabaseCount('people_choice_votes', 3);
    }

    public function test_registration_uses_the_configured_captcha_widget_and_requires_a_response(): void
    {
        Mail::fake();
        config([
            'koita.recaptcha.site_key' => 'shared-site-key',
            'koita.recaptcha.secret_key' => 'shared-secret',
        ]);
        $page = route('people-choice.show');

        $this->get($page)->assertOk()->assertSee('data-sitekey="shared-site-key"', false);
        $this->from($page)->post(route('people-choice.register'), [
            'name' => 'Voter',
            'email' => 'voter@example.org',
        ])->assertSessionHasErrors('g-recaptcha-response');

        $this->assertDatabaseCount('people_choice_voters', 0);
        Mail::assertNothingSent();
    }

    public function test_verified_voter_can_log_in_again_before_submitting(): void
    {
        Mail::fake();
        $page = route('people-choice.show');
        $registration = ['name' => 'Voter', 'email' => 'voter@example.org'];

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->flushSession();
            $this->post(route('people-choice.register'), $registration)->assertRedirect($page);
            $otp = Mail::sent(TemplatedMail::class)->last();
            $this->post(route('people-choice.verify'), ['code' => $otp->data['otp_code']])->assertRedirect($page);
            $this->get($page)->assertOk()->assertSee('id="poll-ballot"', false)->assertDontSee('You’ve already voted');
        }

        $this->assertDatabaseCount('people_choice_voters', 1);
        $this->assertNull(PeopleChoiceVoter::query()->sole()->ballot_submitted_at);
    }

    public function test_an_expired_code_cannot_verify_a_voter(): void
    {
        Mail::fake();
        $page = route('people-choice.show');
        $this->post(route('people-choice.register'), ['name' => 'Voter', 'email' => 'voter@example.org']);
        preg_match('/\b([0-9]{6})\b/', Mail::sent(TemplatedMail::class)->sole()->render(), $match);

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

    public function test_admin_display_order_is_used_on_the_demo_ballot(): void
    {
        config(['koita.shortlist_enabled' => true]);
        $first = $this->entry('Alpha applicant');
        $second = $this->entry('Bravo applicant');
        $third = $this->entry('Charlie applicant');

        $this->actingAs(User::factory()->superAdmin()->create())
            ->patch(route('admin.shortlist.move', $third), ['direction' => 'up'])
            ->assertRedirect(route('admin.shortlist.index'));
        $this->get(route('admin.shortlist.index'))->assertSeeInOrder([$first->title, $third->title, $second->title]);

        $voter = PeopleChoiceVoter::query()->create([
            'name' => 'Demo voter',
            'email' => 'demo@example.org',
            'verified_at' => now(),
        ]);

        $this->get('/peoples-choice-awards/')->assertNotFound();
        $this->withSession(['people_choice.voter_id' => $voter->id])
            ->get('/peoples-choice-awards-demo/')
            ->assertOk()
            ->assertSee('id="navlinks"', false)
            ->assertSee(rtrim(route('home'), '/').'/#categories', false)
            ->assertSee('js/site-navigation.js', false)
            ->assertSee('class="site"', false)
            ->assertSeeInOrder([$first->title, $third->title, $second->title]);
    }

    public function test_scoreboard_totals_submitted_choices_and_breaks_score_ties_by_rank(): void
    {
        config(['koita.shortlist_enabled' => true]);

        $first = $this->entry('First organization');
        $second = $this->entry('Second organization');
        $third = $this->entry('Third organization');
        $fourth = $this->entry('Fourth organization');
        $unvoted = $this->entry('Unvoted organization');
        $ballots = [
            [$first, $second, $third, $fourth],
            [$third, $second, $first, $fourth],
            [$unvoted, $first, $second],
        ];

        foreach ($ballots as $index => $choices) {
            $voter = PeopleChoiceVoter::query()->create([
                'name' => 'Voter',
                'email' => 'voter'.$index.'@example.org',
                'ballot_submitted_at' => $index < 2 ? now() : null,
                'marketing_consented_at' => $index === 0 ? now() : null,
            ]);

            foreach ($choices as $rank => $entry) {
                DB::table('people_choice_votes')->insert([
                    'voter_id' => $voter->id,
                    'applicant_id' => $entry->applicant_id,
                    'profile_title' => $entry->title,
                    'rank' => $rank + 1,
                ]);
            }
        }

        $fourth->delete();
        $url = route('admin.scoreboard');
        $this->get($url)->assertNotFound();

        $response = $this->actingAs(User::factory()->superAdmin()->create())->get($url)
            ->assertOk()
            ->assertSee('2 submitted ballots')
            ->assertSee('voter0@example.org')
            ->assertSee('voter1@example.org')
            ->assertDontSee('voter2@example.org')
            ->assertSee('Checked')
            ->assertSee('Unchecked')
            ->assertSeeInOrder([$first->title, $third->title, $second->title, $fourth->title, $unvoted->title]);
        $results = $response->viewData('results');
        $choices = $response->viewData('rankedChoices')->get($response->viewData('ballots')->first()->id);
        $this->assertSame([$third->title, $second->title, $first->title, $fourth->title], $choices->pluck('profile_title')->all());

        $this->assertSame([8, 8, 8, 4, 0], array_column($results, 'score'));
        $this->assertSame([1, 1, 3, 4, 5], array_column($results, 'rank'));
        $this->assertSame([1 => 1, 2 => 0, 3 => 1, 4 => 0, 5 => 0], $results[0]['counts']);
        $this->assertSame([1 => 0, 2 => 2, 3 => 0, 4 => 0, 5 => 0], $results[2]['counts']);
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
