<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicesSectionSetting extends Model
{
    protected $table = 'services_section_settings';

    protected $fillable = [
        'intro_prefix', 'intro_prefix_fr', 'intro_prefix_en',
        'intro_highlight', 'intro_highlight_fr', 'intro_highlight_en',
        'lead', 'lead_fr', 'lead_en',
        'approach_title', 'approach_title_fr', 'approach_title_en',
        'button_label', 'button_label_fr', 'button_label_en',
    ];

    /**
     * Always returns the single settings row, creating it if it doesn't exist.
     */
    public static function instance(): static
    {
        return static::firstOrCreate([], []);
    }
}
