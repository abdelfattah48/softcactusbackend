<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuiSommesNousService extends Model
{
    protected $fillable = [
        'title',
        'title_fr',
        'title_en',
        'text',
        'text_fr',
        'text_en',
        'icon_url',
        'enabled',
        'sort_order',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get only enabled services
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Get the localized title based on current locale or fallback
     */
    public function getLocalizedTitle($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        return match($locale) {
            'en' => $this->title_en ?: $this->title_fr ?: $this->title ?: '',
            'fr' => $this->title_fr ?: $this->title ?: $this->title_en ?: '',
            default => $this->title ?: $this->title_fr ?: $this->title_en ?: '',
        };
    }

    /**
     * Get the localized text based on current locale or fallback
     */
    public function getLocalizedText($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        return match($locale) {
            'en' => $this->text_en ?: $this->text_fr ?: $this->text ?: '',
            'fr' => $this->text_fr ?: $this->text ?: $this->text_en ?: '',
            default => $this->text ?: $this->text_fr ?: $this->text_en ?: '',
        };
    }
}