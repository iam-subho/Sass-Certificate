<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'html_content',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the schools using this template.
     */
    public function schools()
    {
        return $this->hasMany(School::class);
    }

    /**
     * Get the certificates using this template.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Replace template placeholders with actual data.
     */
    public function render(array $data): string
    {
        $html = $this->html_content;

        foreach ($data as $key => $value) {
            $html = str_replace("{{" . $key . "}}", $value, $html);
        }

        return $html;
    }
}
