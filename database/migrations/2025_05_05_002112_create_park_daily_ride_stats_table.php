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
        Schema::create('park_daily_ride_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('park_id');
            $table->unsignedBigInteger('ride_id')->nullable();
            $table->string('ride_name');
            $table->date('date');
            $table->float('avg_wait_time')->default(0);
            $table->timestamps();

            $table->unique(['park_id', 'ride_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_daily_ride_stats');
    }
};
