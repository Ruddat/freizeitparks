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
        Schema::create('park_queue_time_averages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('park_id');
            $table->integer('ride_id');
            $table->string('ride_name');
            $table->string('land_name');
            $table->float('average_wait_time')->default(0); // Durchschnittliche Wartezeit
            $table->integer('fetch_count')->default(0); // Anzahl der Abfragen für den Durchschnitt
            $table->timestamps();

            $table->unique(['park_id', 'ride_id']); // Eindeutig pro Park und Fahrt
            $table->foreign('park_id')->references('id')->on('parks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_queue_time_averages');
    }
};
