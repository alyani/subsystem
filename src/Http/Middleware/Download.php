<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Alyani\Subsystem\Models\Storage;

class Download
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('sanctum')->user();
        $isAdmin = optional($user)->getTable() === 'managers';

        $isExtrenalWebService = false;
        $token = $request->bearerToken();
        if (!$user && $token) {
            $isExtrenalWebService = hash_equals(
                config('subsystem.storage.extrenalServiceToken'),
                $token
            );

            if (!$isExtrenalWebService) {
                return view('subsystem::errors.404')->withErrors('Error 101');
            }
        }

        $request->attributes->set('isAdmin', $isAdmin);
        $request->attributes->set('isExtrenalWebService', $isExtrenalWebService);

        $SID = pathinfo($request->route('SID') ?? '', PATHINFO_FILENAME);
        $storage = Storage::findBySID($SID);

        if (!$storage) {
            return response()->view('subsystem::errors.404', ['error' => "Error 102"], 404);
        }
        $request->attributes->set('storage', $storage);

        $response = $next($request);


        if ($storage->isPublic) {
            $response->headers->set('Cache-Control', 'max-age=2592000, public, immutable');
            $response->headers->set('CDN-Cache-Control', 'max-age=2592000');
        } else {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Vary', 'Authorization');
        }

        return $response;
    }
}
