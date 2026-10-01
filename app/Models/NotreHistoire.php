<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotreHistoire extends Model
{
    use HasFactory;

    protected $table = 'notre_histoire';

    protected $fillable = [
        'year',
        'description_fr',
        'description_en', 
        'description',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get localized description
     */
    public function getLocalizedDescription(string $locale = 'fr'): string
    {
        return match($locale) {
            'en' => $this->description_en ?: $this->description_fr ?: $this->description ?: '',
            'fr' => $this->description_fr ?: $this->description ?: $this->description_en ?: '',
            default => $this->description ?: $this->description_fr ?: $this->description_en ?: '',
        };
    }

    /**
     * Scope for enabled items
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('year', 'asc');
    }
}