<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('why_us_settings', function (Blueprint $table) {
            $table->text('description_bold')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('why_us_settings', function (Blueprint $table) {
            $table->dropColumn('description_bold');
        });
    }
};
