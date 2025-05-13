<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';
    protected $fillable = ['nama_departemen'];

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_departemen');
    }
}
