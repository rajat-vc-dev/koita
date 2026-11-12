<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('organisation_type')->nullable()->after('mobile');
            $table->string('certificate_12a_path')->nullable()->after('certificate_80g_path');
            $table->string('coi_path')->nullable()->after('section_8_certificate_path');
            $table->string('pan_path')->nullable()->after('coi_path');
            $table->string('gst_path')->nullable()->after('pan_path');
            $table->timestamp('rejected_at')->nullable()->after('application_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'organisation_type',
                'certificate_12a_path',
                'coi_path',
                'pan_path',
                'gst_path',
                'rejected_at',
            ]);
        });
    }
};
