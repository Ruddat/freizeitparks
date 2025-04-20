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
        Schema::table('park_crowd_reports', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->unsignedTinyInteger('theming')->nullable();
            $table->unsignedTinyInteger('cleanliness')->nullable();
            $table->unsignedTinyInteger('gastronomy')->nullable();
            $table->unsignedTinyInteger('service')->nullable();
            $table->unsignedTinyInteger('attractiveness')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_crowd_reports', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'latitude', 'longitude']);
            $table->dropColumn(['theming', 'cleanliness', 'gastronomy', 'service', 'attractiveness']);
        });
    }
};
