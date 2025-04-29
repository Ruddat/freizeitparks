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
        // 🛠 Zuerst alte NULL-Kommentare auf Default-Wert setzen
        DB::table('park_crowd_reports')
            ->whereNull('comment')
            ->update(['comment' => 'Keine Bewertung vorhanden.']);

        // 🔥 Danach Spalte auf TEXT ändern (nicht nullable!)
        Schema::table('park_crowd_reports', function (Blueprint $table) {
            $table->text('comment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_crowd_reports', function (Blueprint $table) {
            $table->string('comment', 255)->nullable()->change();
        });
    }
};
