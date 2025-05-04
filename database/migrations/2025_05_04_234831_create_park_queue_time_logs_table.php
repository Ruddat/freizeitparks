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
        Schema::create('park_queue_time_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('park_id');
            $table->unsignedBigInteger('ride_id');
            $table->string('ride_name');
            $table->string('land_name')->nullable();
            $table->integer('wait_time')->default(0);
            $table->boolean('is_open')->default(false);
            $table->timestamp('fetched_at')->index();
            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('park_queue_time_logs');
    }
};
