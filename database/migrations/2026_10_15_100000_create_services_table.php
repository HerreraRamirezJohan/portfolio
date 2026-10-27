<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('slug')->unique();
            // The career path (config/portfolio.php) that shows this service's work.
            $table->string('path', 32)->nullable();
            // Proper nouns (Laravel, OData): not translated.
            $table->json('tech_stack')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // Translatable
            $table->json('title')->nullable();
            $table->json('summary')->nullable();
            $table->json('timeline')->nullable();
            $table->json('deliverables')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
