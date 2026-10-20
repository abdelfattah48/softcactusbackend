<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recruitment page — singleton settings (hero + section titles + CTAs)
        Schema::create('recruitment_settings', function (Blueprint $table) {
            $table->id();

            // Hero / intro block
            $table->text('hero_title')->nullable();
            $table->text('hero_title_fr')->nullable();
            $table->text('hero_title_en')->nullable();
            $table->text('hero_paragraph')->nullable();
            $table->text('hero_paragraph_fr')->nullable();
            $table->text('hero_paragraph_en')->nullable();
            $table->string('hero_lead')->nullable();
            $table->string('hero_lead_fr')->nullable();
            $table->string('hero_lead_en')->nullable();
            $table->text('hero_cta')->nullable();
            $table->text('hero_cta_fr')->nullable();
            $table->text('hero_cta_en')->nullable();

            // "Nos opportunités" section title
            $table->string('jobs_title_first')->nullable();
            $table->string('jobs_title_first_fr')->nullable();
            $table->string('jobs_title_first_en')->nullable();
            $table->string('jobs_title_highlight')->nullable();
            $table->string('jobs_title_highlight_fr')->nullable();
            $table->string('jobs_title_highlight_en')->nullable();

            // "Nos stagiaires" section title (split around the highlighted box)
            $table->string('talents_title_first')->nullable();
            $table->string('talents_title_first_fr')->nullable();
            $table->string('talents_title_first_en')->nullable();
            $table->string('talents_title_highlight')->nullable();
            $table->string('talents_title_highlight_fr')->nullable();
            $table->string('talents_title_highlight_en')->nullable();
            $table->string('talents_box_title')->nullable();
            $table->string('talents_box_title_fr')->nullable();
            $table->string('talents_box_title_en')->nullable();
            $table->text('talents_box_text')->nullable();
            $table->text('talents_box_text_fr')->nullable();
            $table->text('talents_box_text_en')->nullable();
            $table->text('talents_side_text')->nullable();
            $table->text('talents_side_text_fr')->nullable();
            $table->text('talents_side_text_en')->nullable();

            // "Pourquoi nous rejoindre" section title
            $table->string('why_title_first')->nullable();
            $table->string('why_title_first_fr')->nullable();
            $table->string('why_title_first_en')->nullable();
            $table->string('why_title_highlight')->nullable();
            $table->string('why_title_highlight_fr')->nullable();
            $table->string('why_title_highlight_en')->nullable();

            // Final CTA card
            $table->string('cta_title')->nullable();
            $table->string('cta_title_fr')->nullable();
            $table->string('cta_title_en')->nullable();
            $table->text('cta_text')->nullable();
            $table->text('cta_text_fr')->nullable();
            $table->text('cta_text_en')->nullable();
            $table->string('cta_button')->nullable();
            $table->string('cta_button_fr')->nullable();
            $table->string('cta_button_en')->nullable();

            $table->timestamps();
        });

        // Recruitment page — job offers accordion ("Nos opportunités")
        Schema::create('recruitment_job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('num')->nullable();          // "01", "02", "03"
            $table->string('title')->nullable();
            $table->string('title_fr')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_fr')->nullable();
            $table->text('description_en')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Recruitment page — stagiaires / talents carousel
        Schema::create('recruitment_talents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('role')->nullable();
            $table->string('role_fr')->nullable();
            $table->string('role_en')->nullable();
            $table->string('image')->nullable();       // /storage URL or /assets path
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Recruitment page — "Pourquoi nous rejoindre" cards
        Schema::create('recruitment_why_cards', function (Blueprint $table) {
            $table->id();
            $table->string('num')->nullable();          // "01", "02", "03"
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

        // Recruiting — applications received from the public recruitment page
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->string('poste')->nullable();        // job title (or "Candidature spontanée")
            $table->string('nom');
            $table->string('prenom');
            $table->string('email');
            $table->string('tel');
            $table->text('message')->nullable();
            $table->string('cv_path')->nullable();      // storage/candidatures/...
            $table->string('cv_original_name')->nullable();
            $table->string('status')->default('new');   // new | reviewing | shortlisted | hired | rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures');
        Schema::dropIfExists('recruitment_why_cards');
        Schema::dropIfExists('recruitment_talents');
        Schema::dropIfExists('recruitment_job_offers');
        Schema::dropIfExists('recruitment_settings');
    }
};
