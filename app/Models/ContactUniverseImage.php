<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUniverseImage extends Model
{
    protected $table = 'contact_universe_images';

    protected $fillable = [
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
}
