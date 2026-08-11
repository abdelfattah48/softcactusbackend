<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhyUsSetting extends Model
{
    protected $fillable = [
        'description',
        'description_fr',
        'description_en',
        'description_bold',
        'description_bold_fr',
        'description_bold_en',
    ];

    /**
     * Always returns the single settings row, creating it if it doesn't exist.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], ['description' => '']);
    }
}
