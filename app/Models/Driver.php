<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'driver';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_driver',
        'outsourching',
        'id_mobil',
        'id_karyawan',
        'driver_image',
        'status'
    ];

    /**
     * Get the mobil that owns the driver.
     */
    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class, 'id_mobil');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'id_driver');
    }
}
