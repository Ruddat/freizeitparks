<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('park_daily_stats', function (Blueprint $table) {
            $table->integer('avg_precip_prob')->nullable()->after('avg_temp_night');
            $table->float('precipitation_sum')->nullable()->after('avg_precip_prob');
            $table->integer('sunshine_duration')->nullable()->after('precipitation_sum');
            $table->float('wind_speed_max')->nullable()->after('sunshine_duration');
            $table->float('wind_gusts_max')->nullable()->after('wind_speed_max');
            $table->float('uv_index_max')->nullable()->after('wind_gusts_max');
            $table->integer('crowd_sample_count')->nullable()->after('avg_crowd_level'); // Optional
        });
    }

    public function down(): void
    {
        Schema::table('park_daily_stats', function (Blueprint $table) {
            $table->dropColumn([
                'avg_precip_prob',
                'precipitation_sum',
                'sunshine_duration',
                'wind_speed_max',
                'wind_gusts_max',
                'uv_index_max',
                'crowd_sample_count',
            ]);
        });
    }
};
