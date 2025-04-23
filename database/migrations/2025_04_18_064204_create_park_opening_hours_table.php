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
        Schema::create('park_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->onDelete('cascade');
            $table->string('day')->nullable(); // z. B. monday, tuesday
            $table->time('open')->nullable();
            $table->time('close')->nullable();
            $table->timestamps();
        });

        // Entferne das opening_hours-Feld aus der parks-Tabelle
        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn('opening_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_opening_hours');

        // Füge opening_hours wieder zur parks-Tabelle hinzu
        Schema::table('parks', function (Blueprint $table) {
            $table->text('opening_hours')->nullable()->after('description');
        });
    }
};
