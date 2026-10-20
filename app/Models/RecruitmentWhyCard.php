<?php

namespace App\Models;

use App\Models\Concerns\LocalizedFields;
use Illuminate\Database\Eloquent\Model;

class RecruitmentWhyCard extends Model
{
    use LocalizedFields;

    protected $table = 'recruitment_why_cards';

    protected $fillable = [
        'num',
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
        return $this->localized('title', $locale);
    }

    public function getLocalizedText($locale = null): string
    {
        return $this->localized('text', $locale);
    }
}
