<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('email_templates')->insertOrIgnore([
            'key' => 'user_people_choice_otp',
            'name' => 'User — People’s Choice verification code',
            'subject' => 'Your People’s Choice Awards verification code',
            'body' => "Your People’s Choice Awards verification code is {{otp_code}}.\n\nEnter this code within 10 minutes to continue to the ballot.\n\nIf you did not request this code, you can ignore this email.",
            'placeholders' => 'otp_code',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('email_templates')->where('key', 'user_people_choice_otp')->delete();
    }
};
