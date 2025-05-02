<?php

use H1ch4m\MultiLang\models\MultiLanguagesModel;
use Illuminate\Support\Facades\Cache;

if (!function_exists('getActiveLanguages')) {
    function getActiveLanguages(): array
    {
        $languages = config('h1ch4m_languages');

        // Filter active languages
        $activeLanguages = array_filter($languages, function ($lang) {
            return $lang['is_active'];
        });

        // Sort languages by default status and then by name ascending
        uasort($activeLanguages, function ($a, $b) {
            if ($a['is_default'] && !$b['is_default']) {
                return -1; // $a is default, so it comes first
            } elseif (!$a['is_default'] && $b['is_default']) {
                return 1; // $b is default, so it comes first
            } else {
                return strcmp($a['name'], $b['name']); // Sort by name ascending
            }
        });

        // Set the keys of the filtered and sorted array
        $activeLanguages = array_replace($languages, $activeLanguages);

        // Filter out disabled languages
        $activeLanguages = array_filter($activeLanguages, function ($lang) {
            return $lang['is_active'];
        });

        return $activeLanguages;
    }
}


if (!function_exists('getDefaultLanguage')) {
    function getDefaultLanguage(): string
    {
        $default_language = Cache::get('app_locale') ?? MultiLanguagesModel::firstWhere('is_default', true)->language ?? config('app.locale');

        return $default_language;
    }
}


if (!function_exists('getOrSetCachedLocale')) {
    function getOrSetCachedLocale($localeLang = null): string
    {
        $locale = Cache::get('app_locale');

        if (!$locale || $localeLang) {
            $locale =  $localeLang ?? $locale ?? getDefaultLanguage();
            Cache::forever('app_locale', $locale);
        }

        return $locale;
    }
}
