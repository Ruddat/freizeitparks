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
        Schema::create('park_weather', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Vorhersage-Datum
            $table->float('temp_day');
            $table->float('temp_night');
            $table->integer('weather_code')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamp('fetched_at')->nullable(); // wann wurde es abgefragt
            $table->timestamps();

            $table->unique(['park_id', 'date']); // ein Eintrag pro Park & Tag
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_weather');
    }
};
