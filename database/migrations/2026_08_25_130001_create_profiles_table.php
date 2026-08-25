<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Contact details are genuinely 1:1 -- one phone, one LinkedIn.
            $table->string('phone')->nullable();
            $table->string('public_email')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('photo_path')->nullable();

            // Translatable (spatie): {"es": "...", "en": "..."}
            $table->json('headline')->nullable();
            $table->json('location')->nullable();
            $table->json('summary')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
