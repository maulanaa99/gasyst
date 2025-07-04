<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_detail';

    protected $fillable = [
        'id_surat_jalan',
        'id_departemen',
        'id_karyawan',
        'id_lokasi',
        'id_approver',
        'approval_type',
        'status',
        'keterangan',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'id_surat_jalan');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'id_approver');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}
