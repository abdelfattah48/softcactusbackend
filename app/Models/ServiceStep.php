<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStep extends Model
{
    protected $table = 'service_steps';

    protected $fillable = [
        'title', 'title_fr', 'title_en',
        'text', 'text_fr', 'text_en',
        'enabled', 'sort_order',
    ];

    protected $casts = [
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

    public function getLocalizedText($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($locale) {
            'en'    => $this->text_en ?: $this->text_fr ?: $this->text ?: '',
            default => $this->text_fr ?: $this->text ?: $this->text_en ?: '',
        };
    }
}
