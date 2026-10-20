<?php

namespace App\Models\Concerns;

/**
 * Locale fallback helper for the *_fr / *_en column pairs used across
 * the recruitment CMS models (same convention as the services section).
 */
trait LocalizedFields
{
    /**
     * Resolve a localized value for a base field, e.g. localized('title', $locale)
     * reads title_en / title_fr / title depending on the locale, with fallbacks.
     */
    protected function localized(string $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $fr = $this->{$field . '_fr'} ?: $this->{$field};
        $en = $this->{$field . '_en'} ?: $this->{$field};

        return $locale === 'en'
            ? ($en ?: ($fr ?: ''))
            : ($fr ?: ($en ?: ''));
    }
}
