<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contact page — messages received from the public "fill-in-the-blanks" form
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nom et prénom
            $table->string('company')->nullable();       // Nom de la société
            $table->string('project')->nullable();       // Nom du projet
            $table->string('email');
            $table->string('phone', 50)->nullable();     // Numéro de téléphone
            $table->text('message')->nullable();         // Reconstructed sentence
            $table->timestamp('read_at')->nullable();    // null = unread (bell notification)
            $table->timestamps();
        });

        // Contact page — "L'univers de notre agence" gallery (CMS-managed images)
        Schema::create('contact_universe_images', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();         // /storage URL (uploaded by the backoffice)
            $table->boolean('enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_universe_images');
        Schema::dropIfExists('contact_messages');
    }
};
