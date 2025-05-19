<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Lokasi;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalans';
    protected $fillable = [
        'tanggal',
        'id_departemen',
        'jam_berangkat',
        'jam_berangkat_aktual',
        'status_jam_berangkat_aktual',
        'jam_kembali',
        'jam_kembali_aktual',
        'status_jam_kembali_aktual',
        'id_driver',
        'status',
        'PIC',
        'keterangan',
        'status_approve',
        'approved_by',
        'approved_at',
        'no_surat_jalan',
        'pemesanan_mobil_id',
        'jenis_pemesanan'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'surat_jalan_detail', 'surat_jalan_id', 'karyawan_id')
            ->withTimestamps();
    }

    public function lokasis()
    {
        return $this->belongsToMany(Lokasi::class, 'surat_jalan_detail', 'surat_jalan_id', 'lokasi_id')
            ->withTimestamps();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'id_driver');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }



    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

