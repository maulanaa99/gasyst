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
        // Kemudian buat tabel drivers dengan foreign key
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_driver');
            $table->string('outsourching');
            $table->foreignId('id_mobil')->constrained('mobils')->onDelete('cascade');
            $table->foreignId('id_karyawan')->constrained('karyawans')->onDelete('cascade');
            $table->string('driver_image')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel drivers dulu karena memiliki foreign key ke mobils
        Schema::dropIfExists('drivers');
        // Kemudian hapus tabel mobils
        Schema::dropIfExists('mobils');
    }
};
