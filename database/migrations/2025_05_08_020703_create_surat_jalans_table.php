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
            $table->foreignId('id_karyawan')->constrained('karyawans')->onDelete('cascade');
            $table->string('tujuan');
            $table->date('jam_berangkat');
            $table->foreignId('id_driver')->constrained('drivers')->onDelete('cascade');
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
        Schema::dropIfExists('surat_jalans');
    }
};
