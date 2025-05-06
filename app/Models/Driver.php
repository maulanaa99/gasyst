<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'id_driver';
    protected $fillable = ['nama_driver', 'outsourching', 'id_mobil', 'image', 'user', 'image', 'rute', 'status'];

    /**
     * Get the mobil that owns the driver.
     */
    public function mobils(): BelongsTo
    {
        return $this->belongsTo(Mobil::class, 'id_mobil');
    }
}
