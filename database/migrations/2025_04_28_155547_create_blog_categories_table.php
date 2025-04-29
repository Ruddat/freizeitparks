<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

                // Direkt ein paar Testkategorien einfügen
                DB::table('blog_categories')->insert([
                    [
                        'name' => 'Park Neuigkeiten',
                        'slug' => 'park-neuigkeiten',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Saisonstarts',
                        'slug' => 'saisonstarts',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Freizeitpark Angebote',
                        'slug' => 'freizeitpark-angebote',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Attraktionen',
                        'slug' => 'attraktionen',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'name' => 'Gutscheine & Coupons',
                        'slug' => 'gutscheine-coupons',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
