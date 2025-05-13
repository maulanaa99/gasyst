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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->time('jam_berangkat_aktual')->nullable();
            $table->boolean('status_jam_berangkat_aktual')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn('jam_berangkat_aktual');
            $table->dropColumn('status_jam_berangkat_aktual');
        });
    }
};
