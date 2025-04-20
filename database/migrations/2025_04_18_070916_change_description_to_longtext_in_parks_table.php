<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schritt 1: NULL-Werte bereinigen
        DB::table('parks')
            ->whereNull('description')
            ->update(['description' => '']);

        // Schritt 2: Spalte auf longtext ändern, nullable
        Schema::table('parks', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });

        // Schritt 3: Optional NOT NULL setzen, falls gewünscht
        Schema::table('parks', function (Blueprint $table) {
            $table->longText('description')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }
};
