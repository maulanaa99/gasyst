<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'NIK',
        'nama_karyawan',
        'id_departemen',
        'jabatan',
        'karyawan_image'
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'id_karyawan');
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_karyawan');
    }
}
