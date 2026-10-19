<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Services section — singleton intro / approach / button text
        Schema::create('services_section_settings', function (Blueprint $table) {
            $table->id();

            // Intro title is split in two editable parts, rendered as:
            //   <prefix> <highlight boxed> .
            $table->string('intro_prefix')->nullable();
            $table->string('intro_prefix_fr')->nullable();
            $table->string('intro_prefix_en')->nullable();
            $table->string('intro_highlight')->nullable();
            $table->string('intro_highlight_fr')->nullable();
            $table->string('intro_highlight_en')->nullable();

            // Intro lead paragraph
            $table->text('lead')->nullable();
            $table->text('lead_fr')->nullable();
            $table->text('lead_en')->nullable();

            // Approach heading ("Une approche. Quatre expertises.")
            $table->string('approach_title')->nullable();
            $table->string('approach_title_fr')->nullable();
            $table->string('approach_title_en')->nullable();

            // Card button label ("Demandez un audit stratégique")
            $table->string('button_label')->nullable();
            $table->string('button_label_fr')->nullable();
            $table->string('button_label_en')->nullable();

            $table->timestamps();
        });

        // Services section — the 4 approach diagram nodes
        Schema::create('service_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->text('text')->nullable();
            $table->text('text_fr')->nullable();
            $table->text('text_en')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Services section — the service rows (01 / 02 / 03 ...)
        Schema::create('service_cards', function (Blueprint $table) {
            $table->id();
            $table->string('num')->nullable();          // "01", "02", "03"
            $table->string('title')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->string('sub')->nullable();          // short tagline
            $table->string('sub_fr')->nullable();
            $table->string('sub_en')->nullable();
            $table->text('text')->nullable();           // long description
            $table->text('text_fr')->nullable();
            $table->text('text_en')->nullable();
            $table->boolean('short')->default(false);   // shorter card layout variant
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_cards');
        Schema::dropIfExists('service_steps');
        Schema::dropIfExists('services_section_settings');
    }
};
