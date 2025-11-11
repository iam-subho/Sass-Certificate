<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'download_token',
        'student_id',
        'school_id',
        'certificate_template_id',
        'event_id',
        'certifiable_type',
        'certifiable_id',
        'issuer_id',
        'issued_at',
        'is_valid',
        'status',
        'rank',
        'sent_via_email',
        'sent_via_whatsapp',
        'approved_at',
        'approved_by',
        'visible_on_profile'
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'approved_at' => 'datetime',
            'is_valid' => 'boolean',
            'sent_via_email' => 'boolean',
            'sent_via_whatsapp' => 'boolean',
        ];
    }

    /**
     * Boot method to auto-generate certificate_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_id)) {
                $certificate->certificate_id = 'CERT-' . strtoupper(Str::random(10));
            }
            if (empty($certificate->download_token)) {
                // Generate a unique, secure token for downloading
                $certificate->download_token = Str::random(64);
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }

    /**
     * Get the student that owns the certificate.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the school that owns the certificate.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the template used for the certificate.
     */
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    /**
     * Get the event/competition for this certificate (legacy).
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the parent certifiable model (Event or InterSchoolEvent).
     */
    public function certifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the event (works for both Event and InterSchoolEvent).
     * This helper ensures backward compatibility.
     */
    public function getEvent()
    {
        return $this->certifiable ?? $this->event;
    }

    /**
     * Get the issuer (teacher/staff) who issued this certificate.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issuer_id');
    }

    /**
     * Get the approver (school admin) who approved this certificate.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get certificate logs (email/WhatsApp).
     */
    public function logs()
    {
        return $this->hasMany(CertificateLog::class);
    }

    /**
     * Get the verification URL for the certificate.
     */
    public function getVerificationUrlAttribute(): string
    {
        return url("/verify/{$this->certificate_id}");
    }

    /**
     * Get the secure download URL for the certificate.
     */
    public function getDownloadUrlAttribute(): string
    {
        return url("/certificate/download/{$this->download_token}");
    }

    /**
     * Check if certificate is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if certificate is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Scope for pending certificates.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved certificates.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
