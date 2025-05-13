<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    protected $table = 'tujuans';
    protected $fillable = ['nama_tujuan', 'alamat', 'kota', 'provinsi', 'kode_pos', 'no_telp'];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_tujuan');
    }
}
