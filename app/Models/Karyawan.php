<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawans';
    protected $fillable = ['nik', 'nama_karyawan', 'id_departemen', 'jabatan'];

    public function suratJalan()
    {
        return $this->belongsToMany(SuratJalan::class, 'surat_jalan_detail_karyawan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}
