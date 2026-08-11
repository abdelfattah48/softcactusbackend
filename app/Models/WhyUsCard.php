<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUsCard extends Model
{
    protected $fillable = [
        'name',
        'role',
        'cover_url',
        'video_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
