<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'logo',
        'package_id',
        'certificate_left_logo',
        'certificate_right_logo',
        'signature_left',
        'signature_middle',
        'signature_right',
        'signature_left_title',
        'signature_middle_title',
        'signature_right_title',
        'is_active',
        'status',
        'plan_type',
        'plan_start_date',
        'plan_expiry_date',
        'monthly_certificate_limit',
        'certificates_issued_this_month',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'plan_start_date' => 'date',
            'plan_expiry_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the certificate templates for the school (many-to-many).
     */
    public function certificateTemplates()
    {
        return $this->belongsToMany(CertificateTemplate::class, 'certificate_template_school')
            ->withTimestamps();
    }

    /**
     * Helper method: Get the first certificate template.
     * Use this as a helper, not as a relationship.
     */
    public function getFirstTemplateAttribute()
    {
        return $this->certificateTemplates->first();
    }

    /**
     * Get the package for the school.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the students for the school.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the certificates for the school.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the school admins.
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'school_admin');
    }

    /**
     * Get the issuers (teachers/staff) for the school.
     */
    public function issuers()
    {
        return $this->hasMany(User::class)->where('role', 'issuer');
    }

    /**
     * Get the events/competitions for the school.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the classes for the school.
     */
    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Get the invoices for the school.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the user who approved this school.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if school is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if school is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if school is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if school has exceeded certificate limit.
     */
    public function hasExceededLimit(): bool
    {
        return $this->certificates_issued_this_month >= $this->monthly_certificate_limit;
    }

    /**
     * Check if plan is expired.
     */
    public function isPlanExpired(): bool
    {
        return $this->plan_expiry_date && $this->plan_expiry_date < now();
    }

    /**
     * Make school status pending
     */
    public function makePending()
    {
        $this->status = 'pending';
        $this->save();
    }

}
