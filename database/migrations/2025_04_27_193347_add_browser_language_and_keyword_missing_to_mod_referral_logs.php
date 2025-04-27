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
        Schema::table('mod_referral_logs', function (Blueprint $table) {
            $table->string('browser_language', 10)->nullable()->after('browser');
            $table->boolean('keyword_missing')->default(false)->after('keyword');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mod_referral_logs', function (Blueprint $table) {
            $table->dropColumn('browser_language');
            $table->dropColumn('keyword_missing');
        });
    }
};
