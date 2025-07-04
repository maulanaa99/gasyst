<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mobil extends Model
{
    use HasFactory;

    protected $table = 'mobil';

    protected $fillable = [
        'nama_mobil',
        'plat_no',
        'car_image',
        'status'
    ];

    /**
     * Get the drivers for the mobil.
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class, 'id_mobil');
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_mobil');
    }
}
