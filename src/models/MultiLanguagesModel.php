<?php

namespace H1ch4m\MultiLang\models;

use Illuminate\Database\Eloquent\Model;

class MultiLanguagesModel extends Model
{

    protected $table = 'multi_languages';

    protected $guarded = [];


    protected static function booted()
    {
        static::saved(function () {
            getSavedLanguages(refresh: true);
            getOrSetCachedLocale(refresh: true);
            getDefaultLanguage(refresh: true);
        });

        static::deleted(function () {
            getSavedLanguages(refresh: true);
            getOrSetCachedLocale(refresh: true);
            getDefaultLanguage(refresh: true);
        });
    }
}
