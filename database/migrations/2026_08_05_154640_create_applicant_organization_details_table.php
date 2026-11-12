<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_organization_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('organization_name');
            $table->json('primary_areas');
            $table->string('primary_area_other')->nullable();
            $table->text('mission_overview');
            $table->timestamps();

            $table->unique('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_organization_details');
    }
};
