<?php

namespace H1ch4m\MultiLang\controllers;

use App\Http\Controllers\Controller;
use H1ch4m\MultiLang\models\MultiLanguagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Facades\Validator;


class MultiLanguageController extends Controller
{
    public function records(Request $request, $model_name)
    {
        $class_name = 'App\\Models\\' . $model_name;
        if (!class_exists($class_name)) {
            return redirect()->back()->withErrors(['error' => 'The model does not exist.']);
        }

        $default_language = getDefaultLanguage();
        $valid_languages = MultiLanguagesModel::where('is_default', false)->get(['language'])->pluck('language')->toArray();


        $model_instance = new $class_name();
        $data = null;


        if ($model_instance->parent_method) {
            $parent_method = $model_instance->parent_method;
            $data = $model_instance::latest()
                ->whereHas($parent_method)
                ->with([$parent_method])
                ->get()
                ->groupBy(function ($item) use ($default_language, $parent_method) {
                    return $item->$parent_method->getTranslation($item->$parent_method->default_title, $default_language);
                });
        } else {
            $data = $model_instance::latest()->get();
        }

        return view('h1ch4m::multi-language.records', compact('data', 'default_language', 'valid_languages', 'model_instance', 'model_name'));
    }

    //
    public function edit(Request $request)
    {
        $model_name = $request->input('model');
        $language = $request->input('language');
        $id = $request->input('id');
        $class_name = 'App\\Models\\' . $model_name;
        if (!class_exists($class_name)) {
            return redirect()->back()->withErrors(['error' => 'The model does not exist.']);
        }

        $validLanguages = MultiLanguagesModel::get(['language'])->pluck('language')->toArray();
        if (!in_array($language, $validLanguages)) {
            return redirect()->back()->withErrors(['error' => 'The selected language is not valid.']);
        }
        $modelInstance = new $class_name();
        $translatableInputs = $modelInstance->translatableInputs;

        $item = $modelInstance::where('id', $id)->first();

        $default_language = getDefaultLanguage();
        $full_language = getActiveLanguages()[$language]['name'];
        $full_default_language = getActiveLanguages()[$default_language]['name'];


        $valid_languages = MultiLanguagesModel::where('is_default', false)
            ->where('language', '!=', $language)
            ->get(['language'])
            ->pluck('language')
            ->toArray();

        return view('h1ch4m::multi-language.edit', compact('translatableInputs', 'item', 'model_name', 'language', 'full_default_language', 'full_language', 'default_language', 'valid_languages'));
    }

    public function store(Request $request)
    {
        $data = $request->input('data');

        $model_name = $request->input('model');
        $language = $request->input('language');
        $id = $request->input('id');

        $class_name = 'App\\Models\\' . $model_name;
        if (!class_exists($class_name)) {
            return redirect()->back()->withErrors(['error' => 'The model does not exist.']);
        }

        $validLanguages =  MultiLanguagesModel::get(['language'])->pluck('language')->toArray();
        if (!in_array($language, $validLanguages)) {
            return redirect()->back()->withErrors(['error' => 'The selected language is not valid.']);
        }
        $model_instance = new $class_name();

        foreach ($data as $column => $values) {
            foreach ($values as $id => $value) {
                $model = $model_instance->find($id);

                $value = is_array($value) ? array_values($value) : $value;

                if ($model) {
                    $model->setTranslation($column, $language, $value);
                    $model->save();
                }
            }
        }


        return redirect()->back()->with('success', 'Translation has been saved successfully');
    }


    public function models()
    {
        $modelsPath = app_path('Models');
        $models = [];


        foreach (File::allFiles($modelsPath) as $file) {
            $model_name = Str::before($file->getFilename(), '.php');
            $class_name = 'App\\Models\\' . $model_name;
            if (in_array(HasTranslations::class, class_uses($class_name))) {
                $model_instance = new $class_name();
                $models[$model_name] = $model_instance->custom_name ?? $model_name;
            }
        }

        $saved_languages = MultiLanguagesModel::get(['language', 'is_default']);

        return view('h1ch4m::multi-language.models', compact('models', 'saved_languages'));
    }

    public function setting()
    {
        $languages = getActiveLanguages();
        $saved_data = MultiLanguagesModel::get(['language', 'is_default']);

        return view('h1ch4m::multi-language.setting', compact('languages', 'saved_data'));
    }

    public function store_setting(Request $request)
    {
        $data = $request->input();
        $data['languages'] = array_unique($data['languages'] ?? []);
        $languages = getActiveLanguages();

        $validator = Validator::make($data, [
            'default' => ['required', 'in:' . implode(',', array_keys($languages))],
            'languages' => ['required', 'array', 'min:1', 'in:' . implode(',', array_keys($languages))],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        MultiLanguagesModel::truncate();
        foreach ($data['languages'] as $language) {
            $is_default = ($language === $data['default']) ? true : false;

            MultiLanguagesModel::updateOrCreate(
                ['language' => $language],
                ['is_default' => $is_default]
            );
        }

        return redirect()->back();
    }
}
