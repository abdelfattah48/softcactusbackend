<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notre_histoire', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique()->comment('The year (e.g. 2018, 2021)');
            $table->text('description_fr')->nullable()->comment('French description');
            $table->text('description_en')->nullable()->comment('English description');
            $table->text('description')->nullable()->comment('Default description (fallback)');
            $table->boolean('enabled')->default(true)->comment('Whether this year is visible');
            $table->integer('sort_order')->default(0)->comment('Order for display');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notre_histoire');
    }
};