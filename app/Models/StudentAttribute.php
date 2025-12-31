<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attribute_id',
        'attribute_option_id',
        'numeric_value',
        'assigned_by',
        'assigned_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
            'assigned_at' => 'date',
        ];
    }

    /**
     * Get the student that owns this attribute assignment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the selected option (for enum type).
     */
    public function option()
    {
        return $this->belongsTo(AttributeOption::class, 'attribute_option_id');
    }

    /**
     * Get the user who assigned this value.
     */
    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the display value (either option label or numeric value).
     */
    public function getDisplayValueAttribute(): string
    {
        if ($this->attribute_option_id && $this->option) {
            return $this->option->label;
        }

        return (string) $this->numeric_value;
    }

    /**
     * Get the numeric score (either option's numeric_value or direct numeric_value).
     */
    public function getNumericScoreAttribute(): ?float
    {
        if ($this->attribute_option_id && $this->option) {
            return (float) $this->option->numeric_value;
        }

        return $this->numeric_value ? (float) $this->numeric_value : null;
    }

    /**
     * Scope for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope for a specific attribute.
     */
    public function scopeForAttribute($query, $attributeId)
    {
        return $query->where('attribute_id', $attributeId);
    }

    /**
     * Scope for a specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('assigned_at', $date);
    }

    /**
     * Scope for a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('assigned_at', [$startDate, $endDate]);
    }

    /**
     * Scope ordered by date (newest first).
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('assigned_at', 'desc');
    }

    /**
     * Scope ordered by date (oldest first).
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('assigned_at', 'asc');
    }
}
