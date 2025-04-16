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
        Schema::create('park_monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained('parks')->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->float('avg_crowd_level')->nullable();
            $table->timestamps();

            $table->unique(['park_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_monthly_stats');
    }
};
