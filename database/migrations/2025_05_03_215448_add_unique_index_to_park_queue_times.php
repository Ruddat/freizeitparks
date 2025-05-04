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
        // 🔥 Duplikate löschen (behält den ältesten Eintrag je Kombination)
        DB::statement("
            DELETE pq1 FROM park_queue_times pq1
            JOIN park_queue_times pq2
              ON pq1.ride_id = pq2.ride_id
             AND pq1.park_id = pq2.park_id
             AND pq1.id > pq2.id
        ");

        // 🔐 Unique Index hinzufügen
        Schema::table('park_queue_times', function (Blueprint $table) {
            $table->unique(['park_id', 'ride_id'], 'unique_ride_per_park');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('park_queue_times', function (Blueprint $table) {
            $table->dropUnique('unique_ride_per_park');
        });
    }
};
