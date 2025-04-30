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
        Schema::table('park_weather', function (Blueprint $table) {
            $table->float('wind_speed')->nullable()->after('icon');
            $table->float('uv_index')->nullable()->after('wind_speed');
            $table->integer('rain_chance')->nullable()->after('uv_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_weather', function (Blueprint $table) {
            $table->dropColumn(['wind_speed', 'uv_index', 'rain_chance']);
        });
    }
};
