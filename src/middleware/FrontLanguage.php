<?php

namespace H1ch4m\MultiLang\middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use H1ch4m\MultiLang\models\MultiLanguagesModel;
use Illuminate\Support\Facades\Cache;

class FrontLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        App::setFallbackLocale(config('app.fallback_locale'));

        $lang = $request->route('lang') ?? getOrSetCachedLocale() ?? config('app.locale');

        if (!array_key_exists($lang, getActiveLanguages())) {
            $lang = config('app.locale');
        }

        App::setlocale($lang);
        $request->route()->forgetParameter('lang');
        URL::defaults(['lang' => $lang]);

        getOrSetCachedLocale($lang);

        return $next($request);
    }
}
