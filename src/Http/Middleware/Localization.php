<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Localization
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
        $headerLocale = $request->header('Accept-Language');
        $availableLocales = Config::get('app.locales') ?: [
            Config::get('app.locale'),
            Config::get('app.fallback_locale'),
        ];

        if ($headerLocale && in_array($headerLocale, $availableLocales)) {
            App::setLocale($headerLocale);
        }

        return $next($request);
    }
}
