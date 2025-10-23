<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'father_name',
        'mother_name',
        'mobile',
        'email',
        'school_id',
        'class_id',
        'section',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
        ];
    }

    /**
     * Get the school that owns the student.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the class that the student belongs to.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Get the certificates for the student.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
