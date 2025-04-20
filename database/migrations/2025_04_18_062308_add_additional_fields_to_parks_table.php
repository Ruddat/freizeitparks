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
            $table->string('url')->nullable()->after('longitude');
            $table->text('video_embed_code')->nullable()->after('url');
            $table->string('video_url')->nullable()->after('video_embed_code');
            $table->string('logo')->nullable()->after('video_url');
            $table->text('description')->nullable()->after('logo');
            $table->text('opening_hours')->nullable()->after('description');
            $table->string('type')->nullable()->after('opening_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn([
                'url',
                'video_embed_code',
                'video_url',
                'logo',
                'description',
                'opening_hours',
                'type'
            ]);
        });
    }
};
