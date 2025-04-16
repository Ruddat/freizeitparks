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
        Schema::create('parks', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->integer('queue_times_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->string('name');
            $table->string('group_name')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->string('continent')->nullable();
            $table->string('timezone')->nullable();
            $table->string('status')->default('unknown');
            $table->string('image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parks');
    }
};
