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
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('id_karyawan')->nullable()->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('id_lokasi')->nullable()->constrained('lokasis')->onDelete('cascade');
            $table->foreignId('id_departemen')->nullable()->constrained('departemens')->onDelete('cascade');
            $table->time('jam_berangkat');
            $table->time('jam_berangkat_aktual')->nullable();
            $table->string('status_jam_berangkat_aktual')->nullable();
            $table->time('jam_kembali')->nullable();
            $table->foreignId('id_driver')->constrained('drivers')->onDelete('cascade');
            $table->string('status');
            $table->string('PIC');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
    }
};
