<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')->insertOrIgnore([
            [
                'key' => 'user_people_choice_confirmation',
                'name' => 'User — People’s Choice ballot confirmation',
                'subject' => 'Thanks for voting — The ILSS–KCADT AI Awards',
                'body' => "Hi {{voter_name}},\n\nThank you for taking part in this year’s People’s Choice Awards. Your vote has been recorded in this order:\n\n{{ranked_choices}}\n\nWinners will be announced on our website and social channels. Thank you for helping recognise impactful, community-led AI work.",
                'placeholders' => 'voter_name, ranked_choices',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'admin_people_choice_ballot',
                'name' => 'Admin — People’s Choice ballot submitted',
                'subject' => 'New vote submitted — The ILSS–KCADT AI Awards',
                'body' => "New People’s Choice ballot submitted.\n\nVoter name: {{voter_name}}\nEmail: {{email}}\nSubmitted at: {{submitted_at}}\nMarketing consent: {{marketing_consent}}\nExclude from marketing: {{exclude_from_marketing}}\n\nRanked choices:\n{{ranked_choices}}\n\nView the admin scoreboard: {{scoreboard_url}}",
                'placeholders' => 'voter_name, email, submitted_at, marketing_consent, exclude_from_marketing, ranked_choices, scoreboard_url',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')->whereIn('key', [
            'user_people_choice_confirmation',
            'admin_people_choice_ballot',
        ])->delete();
    }
};
