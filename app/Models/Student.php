<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'father_name',
        'mother_name',
        'mobile',
        'email',
        'username',
        'password',
        'school_id',
        'class_id',
        'section',
        'bio',
        'profile_picture',
        'headline',
        'location',
        'website_url',
        'linkedin_url',
        'twitter_url',
        'github_url',
        'profile_public',
        'is_active',
        'last_login_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'profile_public' => 'boolean',
            'is_active' => 'boolean',
            'password' => 'hashed',
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
    public function class()
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
     * Get the inter-school events this student is participating in.
     */
    public function interSchoolEvents()
    {
        return $this->belongsToMany(InterSchoolEvent::class, 'inter_school_event_student')
                    ->withPivot([
                        'status',
                        'school_id',
                        'approved_by_school',
                        'joined_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get inter-school events this student has joined.
     */
    public function joinedInterSchoolEvents()
    {
        return $this->interSchoolEvents()->wherePivot('status', 'joined');
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get visible certificates for public profile
     */
    public function visibleCertificates()
    {
        return $this->certificates()
            ->where('visible_on_profile', true)
            ->where('status', 'approved')
            ->where('is_valid', true)
            ->latest('issued_at');
    }

    /**
     * Get the public profile URL
     */
    public function getPublicProfileUrlAttribute(): string
    {
        return url("/profile/{$this->username}");
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute(): ?string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        // Default avatar using first letter of name
        return "https://ui-avatars.com/api/?name=" . urlencode($this->full_name) . "&size=200&background=random";
    }

    /**
     * Check if username is available
     */
    public static function isUsernameAvailable(string $username, ?int $excludeId = null): bool
    {
        $query = static::where('username', $username);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    /**
     * Generate a unique username from name
     */
    public static function generateUsername(string $firstName, string $lastName): string
    {
        $baseUsername = Str::slug($firstName . '-' . $lastName);
        $username = $baseUsername;
        $counter = 1;

        while (!static::isUsernameAvailable($username)) {
            $username = $baseUsername . '-' . $counter;
            $counter++;
        }

        return $username;
    }
}
