<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $interval = config('subsystem.lastActivityUpdateInterval', 5);

        if ($user = $request->user()) {
            if (
                $interval === 0 ||
                !$user->last_activity ||
                $user->last_activity->lt(now()->subMinutes($interval))
            ) {
                $user->forceFill([
                    'last_activity' => now(),
                ])->saveQuietly();
            }
        }

        return $next($request);
    }
}
