<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'id_departemen',
        'is_active',
        'phone_number',
        'address',
        'profile_image',
        'signature'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Helper methods untuk cek role
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isHRGA(): bool
    {
        return $this->role === 'hrga';
    }

    public function isDeptAdmin(): bool
    {
        return $this->role === 'dept_admin';
    }

    public function isDeptManager(): bool
    {
        return $this->role === 'dept_manager';
    }

    public function isSecurity(): bool
    {
        return $this->role === 'security';
    }

    // Helper method untuk cek akses
    public function canAccessDepartemen(int $departemenId): bool
    {
        if ($this->isSuperAdmin() || $this->isHRGA()) {
            return true;
        }

        return $this->id_departemen === $departemenId;
    }

    // Helper method untuk cek approval
    public function canApproveSuratJalan(): bool
    {
        return $this->isSuperAdmin() ||
               $this->isHRGA() ||
               $this->isDeptManager();
    }

    // Helper method untuk cek akses security
    public function canRecordCheckpoint(): bool
    {
        return $this->isSuperAdmin() ||
               $this->isSecurity();
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'id_user');
    }

    public function suratJalan()
    {
        return $this->hasMany(SuratJalan::class, 'created_by');
    }

    public function suratJalanDetail()
    {
        return $this->hasMany(SuratJalanDetail::class, 'id_approver');
    }

    public function suratJalanTimestamp()
    {
        return $this->hasMany(SuratJalanTimestamp::class, 'checked_by');
    }
}
