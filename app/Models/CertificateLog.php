<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'type',
        'recipient',
        'status',
        'response',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Get the certificate that owns the log.
     */
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }

    /**
     * Check if log is for email.
     */
    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    /**
     * Check if log is for WhatsApp.
     */
    public function isWhatsApp(): bool
    {
        return $this->type === 'whatsapp';
    }

    /**
     * Check if sending was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if sending failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
