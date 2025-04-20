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
        Schema::create('park_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Der jeweilige Tag
            $table->float('avg_temp_day')->nullable();
            $table->float('avg_temp_night')->nullable();
            $table->tinyInteger('avg_crowd_level')->nullable();
            $table->integer('weather_code')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['park_id', 'date']); // Nur 1 Eintrag pro Tag und Park
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_daily_stats');
    }
};
