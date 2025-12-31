<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'label',
        'numeric_value',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
            'display_order' => 'integer',
        ];
    }

    /**
     * Get the attribute that owns this option.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get all student attribute assignments using this option.
     */
    public function studentAttributes()
    {
        return $this->hasMany(StudentAttribute::class);
    }

    /**
     * Scope to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
