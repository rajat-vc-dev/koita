<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shortlist_entries', function (Blueprint $table) {
            $table->unsignedInteger('display_order')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('shortlist_entries', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
