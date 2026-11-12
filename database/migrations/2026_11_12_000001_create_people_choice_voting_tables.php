<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people_choice_voters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('otp_challenge', 64)->nullable();
            $table->string('otp_hash')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->unsignedTinyInteger('otp_attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('marketing_consented_at')->nullable();
            $table->timestamp('ballot_submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('people_choice_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voter_id')->constrained('people_choice_voters')->cascadeOnDelete();
            $table->foreignId('applicant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('profile_title');
            $table->unsignedTinyInteger('rank');
            $table->unique(['voter_id', 'rank']);
            $table->unique(['voter_id', 'applicant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people_choice_votes');
        Schema::dropIfExists('people_choice_voters');
    }
};
