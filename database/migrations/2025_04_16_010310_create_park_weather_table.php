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
            $table->string('temperature')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('wind_speed')->nullable();
            $table->string('humidity')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
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
