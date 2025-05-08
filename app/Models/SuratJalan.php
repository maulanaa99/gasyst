<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalans';
    protected $fillable = [
        'tanggal',
        'id_karyawan',
        'tujuan',
        'jam_berangkat',
        'id_driver',
        'status',
        'keterangan'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'id_driver');
    }
}

