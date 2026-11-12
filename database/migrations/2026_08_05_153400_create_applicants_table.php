<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('organisation_name');
            $table->string('website');
            $table->string('contact_person_name');
            $table->string('designation');
            $table->string('email');
            $table->string('mobile');
            $table->boolean('is_registered')->default(false);
            $table->string('certificate_80g_path')->nullable();
            $table->string('section_8_certificate_path')->nullable();
            $table->string('access_code')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
