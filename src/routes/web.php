<?php

use Illuminate\Support\Facades\Route;
use H1ch4m\MultiLang\controllers\MultiLanguageController;

Route::group(['as' => 'languages.'], function () {
    Route::get('/multi-language-models',         [MultiLanguageController::class, 'models'])->name('models');
    Route::get('/multi-language-records/{model}',         [MultiLanguageController::class, 'records'])->name('records');
    Route::get('/multi-language',         [MultiLanguageController::class, 'edit'])->name('edit');
    Route::post('/multi-language',         [MultiLanguageController::class, 'store'])->name('store');

    Route::get('/multi-language-settings',         [MultiLanguageController::class, 'setting'])->name('setting');
    Route::post('/multi-language-settings',         [MultiLanguageController::class, 'store_setting'])->name('store_setting');
});
