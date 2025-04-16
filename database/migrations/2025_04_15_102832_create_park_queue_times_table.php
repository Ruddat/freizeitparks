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
        Schema::create('park_queue_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->onDelete('cascade');
            $table->bigInteger('ride_id')->nullable(); // von API
            $table->string('ride_name');
            $table->boolean('is_open')->nullable();
            $table->integer('wait_time')->nullable();
            $table->string('land_name')->nullable(); // z. B. "Coasters"
            $table->timestamp('last_updated')->nullable(); // von API
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_queue_times');
    }
};
