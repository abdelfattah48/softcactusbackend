<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuiSommesNousSetting extends Model
{
    protected $fillable = [
        'description',
        'description_fr',
        'description_en',
        'team_image_url',
    ];

    /**
     * Always returns the single settings row, creating it if it doesn't exist.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], [
            'description' => '',
            'description_fr' => '',
            'description_en' => '',
            'team_image_url' => null,
        ]);
    }
}