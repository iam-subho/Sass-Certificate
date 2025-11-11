<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterSchoolEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'venue',
        'max_participants',
        'event_category',
        'created_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Get the user who created this event (super admin).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the schools invited/participating in this event.
     */
    public function schools()
    {
        return $this->belongsToMany(School::class, 'inter_school_event_school')
                    ->withPivot([
                        'status',
                        'can_students_join',
                        'allowed_classes',
                        'manual_approval_required',
                        'responded_by',
                        'joined_at',
                        'rejected_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get schools that have joined the event.
     */
    public function joinedSchools()
    {
        return $this->schools()->wherePivot('status', 'joined');
    }

    /**
     * Get schools with pending status.
     */
    public function pendingSchools()
    {
        return $this->schools()->wherePivot('status', 'pending');
    }

    /**
     * Get the students participating in this event.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'inter_school_event_student')
                    ->withPivot([
                        'status',
                        'school_id',
                        'approved_by_school',
                        'joined_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get certificates issued for this event.
     */
    public function certificates()
    {
        return $this->morphMany(Certificate::class, 'certifiable');
    }

    /**
     * Scope for published events.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
                     ->where('status', 'published');
    }

    /**
     * Scope for ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing')
                     ->orWhere(function ($q) {
                         $q->where('status', 'published')
                           ->where('start_date', '<=', now())
                           ->where('end_date', '>=', now());
                     });
    }

    /**
     * Scope for completed events.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if event is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if event has started.
     */
    public function hasStarted(): bool
    {
        return $this->start_date <= now();
    }

    /**
     * Check if event has ended.
     */
    public function hasEnded(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if a school has joined this event.
     */
    public function hasSchoolJoined(int $schoolId): bool
    {
        return $this->schools()
                    ->wherePivot('school_id', $schoolId)
                    ->wherePivot('status', 'joined')
                    ->exists();
    }

    /**
     * Check if a student has joined this event.
     */
    public function hasStudentJoined(int $studentId): bool
    {
        return $this->students()
                    ->wherePivot('student_id', $studentId)
                    ->exists();
    }

    /**
     * Get total participating schools count.
     */
    public function getParticipatingSchoolsCountAttribute(): int
    {
        return $this->schools()->wherePivot('status', 'joined')->count();
    }

    /**
     * Get total participating students count.
     */
    public function getParticipatingStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }
}
