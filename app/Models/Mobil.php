<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mobil extends Model
{
    protected $table = 'mobils';
    protected $fillable = [
        'nama_mobil', 'plat_no', 'car_image', 'status'
    ];

    /**
     * Get the drivers for the mobil.
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class, 'id_mobil');
    }
}
