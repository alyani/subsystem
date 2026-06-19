<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class ZWJStrings extends TransformsRequest
{
    /**
     * All of the registered skip callbacks.
     *
     * @var array
     */
    protected static array $skipCallbacks = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

    /**
     * Transform the given value.
     *
     *  U+200B zero width space
     *  U+200C zero width non-joiner Unicode code point
     *  U+200D zero width joiner Unicode code point
     *  U+FEFF zero width no-break space Unicode code point
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (!empty($value) && is_string($value)) {
            return empty(preg_replace( '/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value)) ?  '' : $value;
        }

        return $value;
    }

    /**
     * Register a callback that instructs the middleware to be skipped.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function skipWhen(Closure $callback)
    {
        static::$skipCallbacks[] = $callback;
    }
}
