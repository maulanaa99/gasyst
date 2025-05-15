<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalans';
    protected $fillable = [
        'tanggal',
        'id_karyawan',
        'id_lokasi',
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
        'id_departemen'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'id_driver');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}

