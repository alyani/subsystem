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
        $authUser = Auth::guard('sanctum')->user();

        $isAdmin = optional($authUser)->getTable() === 'managers';

        $isExtrenalWebService = false;
        $token = $request->bearerToken();

        if (!$authUser && $token) {
            $externalToken = config('subsystem.storage.extrenalServiceToken');

            $isExtrenalWebService = $externalToken
                ? hash_equals($externalToken, $token)
                : false;

            if (!$isExtrenalWebService) {
                abort(403, 'Error 101');
            }
        }

        $SID = pathinfo($request->route('SID') ?? '', PATHINFO_FILENAME);

        $storage = Storage::findBySID($SID);

        if (!$storage) {
            abort(404, 'Error 102');
        }

        if (!$storage->morphable_type) {
            abort(404, 'Error 103');
        }

        if (!$storage->isUsed) {
            abort(404, 'Error 104');
        }

        $isPublic = (bool) $storage->isPublic;
        $isSigned = $request->hasValidSignature();

        /**
         * اجازه دسترسی اگر یکی از این‌ها برقرار باشد:
         * 1. فایل public باشد
         * 2. لینک signed معتبر باشد
         * 3. external web service معتبر باشد
         * 4. admin باشد
         * 5. کاربر عادی permission داشته باشد
         */
        $hasAccess =
            $isPublic ||
            $isSigned ||
            $isExtrenalWebService ||
            $isAdmin ||
            $this->hasStorageCheckPermission($storage, $authUser);

        if (!$hasAccess) {
            abort(403, 'Error 105');
        }

        $request->attributes->set('storage', $storage);

        return $next($request);
    }

    protected function hasStorageCheckPermission($storage, $authUser): bool
    {
        if (is_null($authUser)) {
            return false;
        }

        $storagable = $storage->morphable_type;

        if (method_exists($storagable, 'storageCheck')) {
            return $storagable::storageCheck($storage, $authUser);
        }

        return true;
    }
}
