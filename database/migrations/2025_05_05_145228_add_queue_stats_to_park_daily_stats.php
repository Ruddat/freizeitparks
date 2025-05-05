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
        Schema::table('park_daily_stats', function (Blueprint $table) {
            $table->float('avg_wait_time')->nullable()->after('crowd_sample_count');
            $table->integer('max_wait_time')->nullable()->after('avg_wait_time');
            $table->float('rides_open_ratio')->nullable()->after('max_wait_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_daily_stats', function (Blueprint $table) {
            $table->dropColumn([
                'avg_wait_time',
                'max_wait_time',
                'rides_open_ratio',
            ]);
        });
    }
};
