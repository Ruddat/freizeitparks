<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Bestehende UNIQUE-Einschränkung entfernen
        Schema::table('parks', function (Blueprint $table) {
            $table->dropUnique('parks_external_id_unique');
        });

        // Spalte ändern: nullable erlauben und neue UNIQUE-Einschränkung hinzufügen
        Schema::table('parks', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->change();
        });

        // Leere Strings in NULL umwandeln
        DB::table('parks')
            ->where('external_id', '')
            ->update(['external_id' => null]);
    }

    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            // Rückgängig machen: UNIQUE-Einschränkung entfernen
            $table->dropUnique('parks_external_id_unique');
        });

        Schema::table('parks', function (Blueprint $table) {
            // Spalte wieder auf NOT NULL setzen
            $table->string('external_id')->unique()->change();

            // NULL-Werte zurück in leere Strings umwandeln
            DB::table('parks')
                ->whereNull('external_id')
                ->update(['external_id' => '']);
        });
    }
};
