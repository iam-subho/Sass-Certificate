<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'event_date',
        'event_type',
        'certificate_template_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the school that owns the event.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the certificate template for this event.
     */
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Get certificates issued for this event.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Scope for active events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
