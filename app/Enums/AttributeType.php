<?php

namespace App\Enums;

enum AttributeType: string
{
    case ENUM = 'enum';
    case NUMERIC = 'numeric';

    /**
     * Get all type values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display label for type
     */
    public function label(): string
    {
        return match($this) {
            self::ENUM => 'Enum/Remark',
            self::NUMERIC => 'Numeric',
        };
    }

    /**
     * Get description for type
     */
    public function description(): string
    {
        return match($this) {
            self::ENUM => 'Predefined options like Excellent, Good, Average',
            self::NUMERIC => 'Numeric values like marks, scores, percentages',
        };
    }

    /**
     * Check if this is an enum type
     */
    public function isEnum(): bool
    {
        return $this === self::ENUM;
    }

    /**
     * Check if this is a numeric type
     */
    public function isNumeric(): bool
    {
        return $this === self::NUMERIC;
    }
}
