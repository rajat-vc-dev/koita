<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('document_resubmit_token')->nullable()->unique()->after('application_submitted_at');
            $table->timestamp('document_resubmit_requested_at')->nullable()->after('document_resubmit_token');
            $table->timestamp('document_resubmit_expires_at')->nullable()->after('document_resubmit_requested_at');
            $table->timestamp('documents_resubmitted_at')->nullable()->after('document_resubmit_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn([
                'document_resubmit_token',
                'document_resubmit_requested_at',
                'document_resubmit_expires_at',
                'documents_resubmitted_at',
            ]);
        });
    }
};
