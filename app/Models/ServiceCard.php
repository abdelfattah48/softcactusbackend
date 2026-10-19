<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCard extends Model
{
    protected $table = 'service_cards';

    protected $fillable = [
        'num',
        'title', 'title_fr', 'title_en',
        'sub', 'sub_fr', 'sub_en',
        'text', 'text_fr', 'text_en',
        'short', 'enabled', 'sort_order',
    ];

    protected $casts = [
        'short' => 'boolean',
        'enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getLocalizedTitle($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en'    => $this->title_en ?: $this->title_fr ?: $this->title ?: '',
            default => $this->title_fr ?: $this->title ?: $this->title_en ?: '',
        };
    }

    public function getLocalizedSub($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en'    => $this->sub_en ?: $this->sub_fr ?: $this->sub ?: '',
            default => $this->sub_fr ?: $this->sub ?: $this->sub_en ?: '',
        };
    }

    public function getLocalizedText($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en'    => $this->text_en ?: $this->text_fr ?: $this->text ?: '',
            default => $this->text_fr ?: $this->text ?: $this->text_en ?: '',
        };
    }
}
