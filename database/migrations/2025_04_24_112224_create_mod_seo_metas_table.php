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
        Schema::create('mod_seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->index();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical')->nullable();
            $table->string('image')->nullable();
            $table->json('extra_meta')->nullable();
            $table->text('keywords')->nullable();
            $table->boolean('prevent_override')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mod_seo_metas');
    }
};
