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


if (!function_exists('getSavedLanguages')) {
    function getSavedLanguages($params = ['*'], $refresh = false): array
    {
        if ($refresh) {
            Cache::forget('saved_languages');
        }

        return Cache::rememberForever('saved_languages', function () use ($params) {
            return MultiLanguagesModel::get($params)
                ->keyBy('language')
                ->toArray();
        });
    }
}


if (!function_exists('getDefaultLanguage')) {
    function getDefaultLanguage($refresh = false): string
    {
        if ($refresh) {
            Cache::forget('default_language');
        }

        return Cache::rememberForever('default_language', function () {
            return MultiLanguagesModel::firstWhere('is_default', true)->language ?? config('app.locale');
        });
    }
}


if (!function_exists('getOrSetCachedLocale')) {
    function getOrSetCachedLocale(?string $localeLang = null, $refresh = false): string
    {
        $cacheKey = 'app_locale';

        if ($refresh) {
            Cache::forget($cacheKey);
        }

        if ($localeLang) {
            Cache::forever($cacheKey, $localeLang);
            return $localeLang;
        }

        return Cache::rememberForever($cacheKey, fn() => getDefaultLanguage($refresh));
    }
}
