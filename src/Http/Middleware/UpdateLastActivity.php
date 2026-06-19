<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return $next($request);
        }
        if ($authUser->last_activity->lt(now()->subMinutes(5))) {
            $authUser->forceFill([
                'last_activity' => now(),
            ])->save();
        }
        return $next($request);
    }
}
