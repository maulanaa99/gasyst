<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';

    protected $fillable = [
        'kode_departemen',
        'nama_departemen'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_departemen');
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_departemen');
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_departemen');
    }
}
