<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'price_small', 'price_medium', 'price_large',
                'size_small', 'size_medium', 'size_large',
                'timeline', 'tech_stack',
            ]);
        });

        Schema::table('services', function (Blueprint $table) {
            // Hourly rate in US dollars. The Spanish page shows it converted
            // to pesos (App\Support\HourlyRates).
            $table->unsignedInteger('rate_usd')->nullable()->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('rate_usd');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->json('tech_stack')->nullable()->after('path');
            $table->unsignedInteger('price_small')->nullable()->after('tech_stack');
            $table->unsignedInteger('price_medium')->nullable()->after('price_small');
            $table->unsignedInteger('price_large')->nullable()->after('price_medium');
            $table->json('timeline')->nullable()->after('summary');
            $table->json('size_small')->nullable()->after('deliverables');
            $table->json('size_medium')->nullable()->after('size_small');
            $table->json('size_large')->nullable()->after('size_medium');
        });
    }
};
