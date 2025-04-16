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
        Schema::create('park_crowd_forecas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained('parks')->onDelete('cascade');
            $table->date('date');
            $table->tinyInteger('crowd_level')->nullable();
            $table->string('status')->nullable();
            $table->string('opening_hours')->nullable();
            $table->timestamps();

            $table->unique(['park_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_crowd_forecas');
    }
};
