<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'alamat'
    ];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_lokasi');
    }
}
