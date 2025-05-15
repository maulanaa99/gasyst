<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasis';
    protected $fillable = ['nama_tujuan', 'alamat', 'kota', 'provinsi', 'kode_pos', 'no_telp'];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_lokasi');
    }
}
