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
            $table->string('no_surat_jalan')->unique();
            $table->date('tanggal');
            $table->foreignId('id_departemen')->nullable()->constrained('departemen')->onDelete('cascade');
            $table->time('jam_berangkat');
            $table->time('jam_berangkat_aktual')->nullable();
            $table->string('status_jam_berangkat_aktual')->nullable();
            $table->time('jam_kembali')->nullable();
            $table->time('jam_kembali_aktual')->nullable();
            $table->string('status_jam_kembali_aktual')->nullable();
            $table->foreignId('id_driver')->constrained('drivers')->onDelete('cascade');
            $table->boolean('status_approve')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->nullable();
            $table->string('PIC');
            $table->enum('jenis_pemesanan', ['Karyawan', 'Driver Only']);
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
