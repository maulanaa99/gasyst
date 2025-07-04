<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanTimestamp extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_timestamp';

    protected $fillable = [
        'id_surat_jalan',
        'timestamp_type',
        'checked_by',
        'keterangan'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'id_surat_jalan');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
