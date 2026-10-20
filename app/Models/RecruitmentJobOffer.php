<?php

namespace App\Models;

use App\Models\Concerns\LocalizedFields;
use Illuminate\Database\Eloquent\Model;

class RecruitmentJobOffer extends Model
{
    use LocalizedFields;

    protected $table = 'recruitment_job_offers';

    protected $fillable = [
        'num',
        'title', 'title_fr', 'title_en',
        'description', 'description_fr', 'description_en',
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

    public function getLocalizedDescription($locale = null): string
    {
        return $this->localized('description', $locale);
    }
}
