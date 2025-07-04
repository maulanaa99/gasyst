<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\SuratJalanDetail;
use App\Models\Driver;
use App\Models\Departemen;
use App\Models\Mobil;
use App\Models\SuratJalanTimestamp;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_surat_jalan',
        'tanggal',
        'id_driver',
        'id_mobil',
        'jam_berangkat',
        'jam_kembali',
        'keterangan',
        'status',
        'jenis_pemesanan',
        'issued_by',
        'issued_at',
        'approve_by',
        'approve_at',
        'acknowledged_by',
        'acknowledged_at',
        'checked_by',
        'checked_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'issued_at' => 'datetime',
        'approve_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'checked_at' => 'datetime'
    ];

    protected function jamBerangkat(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? date('H:i', strtotime($value)) : null,
            set: fn ($value) => $value
        );
    }

    protected function jamKembali(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? date('H:i', strtotime($value)) : null,
            set: fn ($value) => $value
        );
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'id_driver');
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class, 'id_mobil');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function suratJalanDetail()
    {
        return $this->hasMany(SuratJalanDetail::class, 'id_surat_jalan');
    }

    public function suratJalanTimestamp()
    {
        return $this->hasMany(SuratJalanTimestamp::class, 'id_surat_jalan');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'surat_jalan_detail', 'id_surat_jalan', 'id_karyawan')->distinct();
    }

    public function lokasis()
    {
        return $this->belongsToMany(Lokasi::class, 'surat_jalan_detail', 'id_surat_jalan', 'id_lokasi')->distinct();
    }

    public function approveBy()
    {
        return $this->belongsTo(User::class, 'approve_by');
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function issuedAt()
    {
        return $this->belongsTo(User::class, 'issued_at');
    }
    public function approveAt()
    {
        return $this->belongsTo(User::class, 'approve_at');
    }
}

