<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // why_us_settings: add EN/FR variants for both text fields
        Schema::table('why_us_settings', function (Blueprint $table) {
            $table->text('description_fr')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_fr');
            $table->text('description_bold_fr')->nullable()->after('description_bold');
            $table->text('description_bold_en')->nullable()->after('description_bold_fr');
        });

        // why_us_features: add EN/FR variants for description
        Schema::table('why_us_features', function (Blueprint $table) {
            $table->text('description_fr')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_fr');
        });
    }

    public function down(): void
    {
        Schema::table('why_us_settings', function (Blueprint $table) {
            $table->dropColumn(['description_fr', 'description_en', 'description_bold_fr', 'description_bold_en']);
        });

        Schema::table('why_us_features', function (Blueprint $table) {
            $table->dropColumn(['description_fr', 'description_en']);
        });
    }
};
