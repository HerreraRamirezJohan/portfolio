<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Case-study cards show the stack as chips and the year as metadata.
            // Stack entries are proper nouns (Laravel, PostgreSQL) -- not translated.
            $table->json('tech_stack')->nullable()->after('image_path');

            // Free text rather than a date: "2024", "2024 - 2026", "Ongoing".
            $table->string('year', 32)->nullable()->after('tech_stack');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['tech_stack', 'year']);
        });
    }
};
