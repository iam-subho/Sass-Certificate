<?php

namespace App\Models;

use App\Enums\AttributeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'school_id',
        'min_value',
        'max_value',
        'is_active',
        'created_by',
        'display_order',
        'numeric_value',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'type' => AttributeType::class,
            'min_value' => 'decimal:2',
            'max_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the school that owns this attribute (null for global attributes).
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who created this attribute.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the options for this attribute (for enum type).
     */
    public function options()
    {
        return $this->hasMany(AttributeOption::class)->orderBy('display_order');
    }

    /**
     * Get all student attribute assignments.
     */
    public function studentAttributes()
    {
        return $this->hasMany(StudentAttribute::class);
    }

    /**
     * Check if this is a global (super admin) attribute.
     */
    public function isGlobal(): bool
    {
        return is_null($this->school_id);
    }

    /**
     * Check if this is a school-specific attribute.
     */
    public function isSchoolSpecific(): bool
    {
        return !is_null($this->school_id);
    }

    /**
     * Check if this attribute is enum type.
     */
    public function isEnumType(): bool
    {
        return $this->type === AttributeType::ENUM;
    }

    /**
     * Check if this attribute is numeric type.
     */
    public function isNumericType(): bool
    {
        return $this->type === AttributeType::NUMERIC;
    }

    /**
     * Scope for active attributes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for global (super admin) attributes.
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('school_id');
    }

    /**
     * Scope for school-specific attributes.
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope for attributes available to a school (global + school-specific).
     */
    public function scopeAvailableForSchool($query, $schoolId)
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->whereNull('school_id')
              ->orWhere('school_id', $schoolId);
        });
    }

    /**
     * Scope for enum type attributes.
     */
    public function scopeEnumType($query)
    {
        return $query->where('type', AttributeType::ENUM->value);
    }

    /**
     * Scope for numeric type attributes.
     */
    public function scopeNumericType($query)
    {
        return $query->where('type', AttributeType::NUMERIC->value);
    }
}
