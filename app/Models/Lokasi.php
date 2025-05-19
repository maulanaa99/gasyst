<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasis';
    protected $fillable = ['kode_lokasi', 'nama_lokasi', 'alamat', 'kota', 'provinsi', 'kode_pos', 'no_telp'];

    public function suratJalan()
    {
        return $this->belongsToMany(SuratJalan::class, 'surat_jalan_detail', 'lokasi_id', 'surat_jalan_id')
            ->withTimestamps();
    }
}
