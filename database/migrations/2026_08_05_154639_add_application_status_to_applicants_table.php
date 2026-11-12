<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('application_status')->default('interest_registered')->after('access_code');
            $table->timestamp('invite_sent_at')->nullable()->after('application_status');
            $table->timestamp('application_submitted_at')->nullable()->after('invite_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['application_status', 'invite_sent_at', 'application_submitted_at']);
        });
    }
};
