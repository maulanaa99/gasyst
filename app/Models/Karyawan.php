<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';
    protected $fillable = ['nik', 'nama', 'departemen', 'jabatan'];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_karyawan');
    }
}
