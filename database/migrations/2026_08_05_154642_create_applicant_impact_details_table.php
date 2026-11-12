<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_impact_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('adoption_stage');
            $table->unsignedInteger('user_count')->nullable();
            $table->text('success_measurement');
            $table->text('outcomes_impact');
            $table->string('deck_path');
            $table->boolean('consent')->default(false);
            $table->timestamps();

            $table->unique('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_impact_details');
    }
};
