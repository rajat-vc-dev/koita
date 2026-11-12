<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_ai_solutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('solution_name');
            $table->text('social_problem');
            $table->text('solution_description');
            $table->json('users_of_solution');
            $table->string('users_other')->nullable();
            $table->text('technical_details');
            $table->string('demo_video_url');
            $table->string('testimonial_video_url')->nullable();
            $table->timestamps();

            $table->unique('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_ai_solutions');
    }
};
