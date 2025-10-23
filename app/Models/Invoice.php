<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'school_id',
        'month',
        'certificates_count',
        'amount',
        'plan_type',
        'status',
        'due_date',
        'paid_date',
        'payment_method',
        'transaction_id',
        'notes',
        'package_meta',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_date' => 'date',
            'amount' => 'decimal:2',
            'package_meta' => 'array',
        ];
    }


    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the school that owns the invoice.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if invoice is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if invoice is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->isPending() && $this->due_date < now();
    }
}
