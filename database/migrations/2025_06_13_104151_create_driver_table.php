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
        Schema::create('driver', function (Blueprint $table) {
            $table->id();
            $table->string('nama_driver');
            $table->string('outsourching')->nullable();
            $table->foreignId('id_mobil')->nullable()->constrained('mobil')->onDelete('set null');
            $table->foreignId('id_karyawan')->nullable()->constrained('karyawan')->onDelete('set null');
            $table->string('driver_image')->nullable();
            $table->enum('status', ['Tersedia', 'Dipesan', 'Dalam Perjalanan', 'Servis'])->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver');
    }
};
