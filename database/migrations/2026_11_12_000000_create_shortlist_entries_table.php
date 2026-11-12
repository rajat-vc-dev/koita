<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shortlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('solution_name')->nullable();
            $table->string('sector')->nullable();
            $table->string('tagline')->nullable();
            $table->text('problem')->nullable();
            $table->text('solution')->nullable();
            $table->text('impact')->nullable();
            $table->text('demo_url')->nullable();
            $table->text('testimonial_url')->nullable();
            $table->text('pdf_url')->nullable();
            $table->string('cta_label')->nullable();
            $table->text('cta_url')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shortlist_entries');
    }
};
