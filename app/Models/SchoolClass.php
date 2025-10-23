<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'sections',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the school that owns the class.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get students in this class.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Get sections as array.
     */
    public function getSectionsArrayAttribute(): array
    {
        return $this->sections ? explode(',', $this->sections) : [];
    }

    /**
     * Scope for active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
