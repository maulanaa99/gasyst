<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalans';
    protected $fillable = [
        'tanggal',
        'id_karyawan',
        'id_tujuan',
        'jam_berangkat',
        'id_driver',
        'status',
        'PIC',
        'keterangan',
        'jam_berangkat_aktual',
        'status_jam_berangkat_aktual',
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

    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class, 'id_tujuan');
    }
}

