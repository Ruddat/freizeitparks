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
        // Optional: alte Werte in neue Status-Werte mappen
        DB::table('parks')->where('status', 'open')->update(['status' => 'active']);
        DB::table('parks')->where('status', 'closed')->update(['status' => 'revive']);
        DB::table('parks')->where('status', 'unknown')->update(['status' => 'pending']);

        // Enum-Änderung (Achtung: ENUM direkt ist migrationsbedingt eingeschränkt)
        Schema::table('parks', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'revive'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        // Reverse-Mapping bei Rollback
        DB::table('parks')->where('status', 'active')->update(['status' => 'open']);
        DB::table('parks')->where('status', 'revive')->update(['status' => 'closed']);
        DB::table('parks')->where('status', 'pending')->update(['status' => 'unknown']);

        Schema::table('parks', function (Blueprint $table) {
            $table->string('status')->default('unknown')->change();
        });
    }
};
