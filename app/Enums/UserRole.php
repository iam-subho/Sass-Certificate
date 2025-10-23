<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case SCHOOL_ADMIN = 'school_admin';
    case ISSUER = 'issuer';

    /**
     * Get all role values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display label for role
     */
    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::SCHOOL_ADMIN => 'School Administrator',
            self::ISSUER => 'Certificate Issuer',
        };
    }

    /**
     * Check if role is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    /**
     * Check if role is school admin
     */
    public function isSchoolAdmin(): bool
    {
        return $this === self::SCHOOL_ADMIN;
    }

    /**
     * Check if role is issuer
     */
    public function isIssuer(): bool
    {
        return $this === self::ISSUER;
    }
}
