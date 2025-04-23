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
        Schema::table('parks', function (Blueprint $table) {
            $table->string('street')->nullable()->after('location');
            $table->string('zip')->nullable()->after('street');
            $table->string('city')->nullable()->after('zip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn(['street', 'zip', 'city']);
        });
    }
};
