<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // "From" prices in MXN before IVA, one per size (S, M, L). The
            // home page shows them; the numbers come from the price lists in
            // the Cotizaciones folder.
            $table->unsignedInteger('price_small')->nullable()->after('tech_stack');
            $table->unsignedInteger('price_medium')->nullable()->after('price_small');
            $table->unsignedInteger('price_large')->nullable()->after('price_medium');

            // Translatable: what a typical job of that size is.
            $table->json('size_small')->nullable()->after('deliverables');
            $table->json('size_medium')->nullable()->after('size_small');
            $table->json('size_large')->nullable()->after('size_medium');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_small', 'price_medium', 'price_large', 'size_small', 'size_medium', 'size_large']);
        });
    }
};
