<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class DetectLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = $request->header('App-Language', 'fa');
        App::setLocale($language);

        if ($language) {
            $request->merge(['language' => $language]);
        }

        return $next($request);
    }
}
