<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

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
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
        };
    }

    /**
     * Get badge color for status
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PAID => 'green',
            self::OVERDUE => 'red',
        };
    }
}
