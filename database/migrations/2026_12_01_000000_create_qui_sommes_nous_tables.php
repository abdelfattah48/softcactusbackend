<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Qui Sommes-Nous settings (singleton table for main description)
        Schema::create('qui_sommes_nous_settings', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->timestamps();
        });

        // Qui Sommes-Nous services
        Schema::create('qui_sommes_nous_services', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->text('text')->nullable();
            $table->text('text_fr')->nullable();
            $table->text('text_en')->nullable();
            $table->string('icon_url')->nullable(); // for the service icon image
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qui_sommes_nous_services');
        Schema::dropIfExists('qui_sommes_nous_settings');
    }
};