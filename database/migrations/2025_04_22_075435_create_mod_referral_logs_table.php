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
        Schema::create('mod_referral_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('referer_url')->nullable();
            $table->string('source')->nullable();
            $table->string('keyword')->nullable();
            $table->string('landing_page');
            $table->string('ip_address')->nullable();
            $table->unsignedInteger('visit_count')->default(1);
            $table->timestamp('visited_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // Neue Felder für Bot-Tracking
            $table->boolean('is_bot')->default(false);
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('asn')->nullable();
            $table->string('isp')->nullable();

            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('os')->nullable();          // z. B. Windows, iOS
            $table->string('browser')->nullable();     // z. B. Chrome, Firefox
            // Weitere Felder für Bot-Tracking
            // $table->string('bot_name')->nullable(); // z. B. Googlebot, Bingbot
            // $table->string('bot_version')->nullable(); // z. B. 2.1
            // $table->string('bot_os')->nullable(); // z. B. Linux, Windows
            // $table->string('bot_browser')->nullable(); // z. B. Chrome, Firefox
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_referral_logs');
    }
};
