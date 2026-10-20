<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentSetting extends Model
{
    protected $table = 'recruitment_settings';

    protected $fillable = [
        'hero_title', 'hero_title_fr', 'hero_title_en',
        'hero_paragraph', 'hero_paragraph_fr', 'hero_paragraph_en',
        'hero_lead', 'hero_lead_fr', 'hero_lead_en',
        'hero_cta', 'hero_cta_fr', 'hero_cta_en',
        'jobs_title_first', 'jobs_title_first_fr', 'jobs_title_first_en',
        'jobs_title_highlight', 'jobs_title_highlight_fr', 'jobs_title_highlight_en',
        'talents_title_first', 'talents_title_first_fr', 'talents_title_first_en',
        'talents_title_highlight', 'talents_title_highlight_fr', 'talents_title_highlight_en',
        'talents_box_title', 'talents_box_title_fr', 'talents_box_title_en',
        'talents_box_text', 'talents_box_text_fr', 'talents_box_text_en',
        'talents_side_text', 'talents_side_text_fr', 'talents_side_text_en',
        'why_title_first', 'why_title_first_fr', 'why_title_first_en',
        'why_title_highlight', 'why_title_highlight_fr', 'why_title_highlight_en',
        'cta_title', 'cta_title_fr', 'cta_title_en',
        'cta_text', 'cta_text_fr', 'cta_text_en',
        'cta_button', 'cta_button_fr', 'cta_button_en',
    ];

    /** Singleton row — created blank on first read if missing. */
    public static function instance(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
