<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUsFeature extends Model
{
    protected $fillable = [
        'title',
        'description',
        'description_fr',
        'description_en',
        'icon',
        'enabled',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
