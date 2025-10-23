<?php

namespace App\Enums;

enum SchoolStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display label for status
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Get badge color for status
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::SUSPENDED => 'gray',
        };
    }
}
