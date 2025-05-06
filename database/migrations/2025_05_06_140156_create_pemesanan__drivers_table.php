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
        Schema::create('pemesanan__drivers', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('id_karyawan');
            $table->string('tujuan');
            $table->date('jam_berangkat');
            $table->integer('id_driver');
            $table->string('status');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan__drivers');
    }
};
