<?php

namespace App\Models;

use App\Models\Concerns\LocalizedFields;
use Illuminate\Database\Eloquent\Model;

class RecruitmentTalent extends Model
{
    use LocalizedFields;

    protected $table = 'recruitment_talents';

    protected $fillable = [
        'first_name', 'last_name',
        'role', 'role_fr', 'role_en',
        'image', 'enabled', 'sort_order',
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

    public function getLocalizedRole($locale = null): string
    {
        return $this->localized('role', $locale);
    }
}
