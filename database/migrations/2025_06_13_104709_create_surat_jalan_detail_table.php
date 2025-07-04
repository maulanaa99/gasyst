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
        Schema::create('surat_jalan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_surat_jalan')->constrained('surat_jalan')->onDelete('cascade');
            $table->foreignId('id_departemen')->constrained('departemen')->onDelete('cascade');
            $table->foreignId('id_karyawan')->constrained('karyawan')->onDelete('cascade');
            $table->foreignId('id_lokasi')->constrained('lokasi')->onDelete('cascade');
            $table->foreignId('id_approver')->constrained('users')->onDelete('cascade');
            $table->enum('approval_type', ['admin', 'manager', 'hrga', 'security'])->default('admin');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('keterangan')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_detail');
    }
};
