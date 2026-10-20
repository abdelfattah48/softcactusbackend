<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // read_at powers the back-office notification bell: a candidature is an
        // "unread" notification while read_at is null.
        Schema::table('candidatures', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
