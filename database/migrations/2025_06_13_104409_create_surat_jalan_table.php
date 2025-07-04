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
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal');
            $table->foreignId('id_driver')->nullable()->constrained('driver')->onDelete('set null');
            $table->foreignId('id_mobil')->nullable()->constrained('mobil')->onDelete('set null');
            $table->time('jam_berangkat');
            $table->time('jam_kembali');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Dipesan', 'Dalam Perjalanan', 'Selesai', 'Dibatalkan'])->default('Dipesan');
            $table->enum('jenis_pemesanan', ['Driver Only', 'Karyawan']);
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('approve_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approve_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
