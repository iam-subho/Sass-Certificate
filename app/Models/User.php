<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id',
        'is_active',
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the school that the user belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is school admin.
     */
    public function isSchoolAdmin(): bool
    {
        return $this->role === 'school_admin';
    }

    /**
     * Check if user is issuer.
     */
    public function isIssuer(): bool
    {
        return $this->role === 'issuer';
    }

    /**
     * Certificates issued by this user (for issuers).
     */
    public function issuedCertificates()
    {
        return $this->hasMany(Certificate::class, 'issuer_id');
    }

    /**
     * Certificates approved by this user (for school admins).
     */
    public function approvedCertificates()
    {
        return $this->hasMany(Certificate::class, 'approved_by');
    }

    /**
     * Schools approved by this user (for super admin).
     */
    public function approvedSchools()
    {
        return $this->hasMany(School::class, 'approved_by');
    }
}
