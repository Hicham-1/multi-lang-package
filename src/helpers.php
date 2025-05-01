<?php

if (!function_exists('getActiveLanguages')) {
    function getActiveLanguages()
    {
        $languages = config('languages');

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
