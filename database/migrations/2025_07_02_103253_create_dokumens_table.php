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
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_diterima');
            $table->string('nama_pengirim_dokumen');
            $table->string('nama_penerima_dokumen');
            $table->string('no_resi');
            $table->string('ekspedisi');
            $table->string('keterangan');
            $table->datetime('security_received_at')->nullable();
            $table->string('security_received_by')->nullable();
            $table->datetime('hrga_received_at')->nullable();
            $table->string('hrga_received_by')->nullable();
            $table->datetime('user_received_at')->nullable();
            $table->string('user_received_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
