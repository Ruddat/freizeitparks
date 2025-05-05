<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('park_weather', function (Blueprint $table) {
            $table->float('temp_mean')->nullable()->after('temp_night');
            $table->float('apparent_temp_max')->nullable()->after('temp_mean');
            $table->float('apparent_temp_min')->nullable()->after('apparent_temp_max');
            $table->float('apparent_temp_mean')->nullable()->after('apparent_temp_min');

            $table->float('precipitation')->nullable()->after('rain_chance');
            $table->float('rain_sum')->nullable()->after('precipitation');
            $table->float('showers_sum')->nullable()->after('rain_sum');
            $table->float('snowfall_sum')->nullable()->after('showers_sum');
            $table->integer('precipitation_hours')->nullable()->after('snowfall_sum');
            $table->integer('precip_prob_mean')->nullable()->after('precipitation_hours');
            $table->integer('precip_prob_min')->nullable()->after('precip_prob_mean');

            $table->timestamp('sunrise')->nullable()->after('precip_prob_min');
            $table->timestamp('sunset')->nullable()->after('sunrise');
            $table->integer('sunshine_duration')->nullable()->after('sunset');
            $table->integer('daylight_duration')->nullable()->after('sunshine_duration');

            $table->float('wind_gusts')->nullable()->after('wind_speed');
            $table->integer('wind_direction')->nullable()->after('wind_gusts');

            $table->float('radiation_sum')->nullable()->after('wind_direction');
            $table->float('evapotranspiration')->nullable()->after('radiation_sum');

            $table->float('uv_index_clear_sky')->nullable()->after('uv_index');
        });
    }

    public function down(): void
    {
        Schema::table('park_weather', function (Blueprint $table) {
            $table->dropColumn([
                'temp_mean',
                'apparent_temp_max',
                'apparent_temp_min',
                'apparent_temp_mean',
                'precipitation',
                'rain_sum',
                'showers_sum',
                'snowfall_sum',
                'precipitation_hours',
                'precip_prob_mean',
                'precip_prob_min',
                'sunrise',
                'sunset',
                'sunshine_duration',
                'daylight_duration',
                'wind_gusts',
                'wind_direction',
                'radiation_sum',
                'evapotranspiration',
                'uv_index_clear_sky',
            ]);
        });
    }
};
