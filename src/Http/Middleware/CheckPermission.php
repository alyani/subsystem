<?php

namespace Alyani\Subsystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ?string $customPermission = null)
    {
        $routeName = $request->route()->getName();
        
        // If a custom permission is explicitly passed from the route, use it.
        // Otherwise, fallback to the automatic normalization.
        $permissionToCheck = $customPermission ?: $this->normalizeRouteName($routeName);

        // Check user access using the normalized route name
        if (Gate::allows($permissionToCheck)) {
            return $next($request);
        }

        abort(403, 'Access denied');
    }

    /**
     * Normalize the route name by mapping action suffixes to form suffixes.
     * * This unifies 'store' with 'create' and 'update' with 'edit' to simplify
     * the role-permission mapping and enhance UX in the administration panel.
     *
     * @param string $routeName The original name of the route.
     * @return string The normalized route name.
     */
    protected function normalizeRouteName(string $routeName): string
    {
        // If the route ends with .store, convert it to .create
        if (str_ends_with($routeName, '.store')) {
            return str_replace('.store', '.create', $routeName);
        }


        // If the route ends with .update, convert it to .edit
        if (str_ends_with($routeName, '.update')) {
            return str_replace('.update', '.edit', $routeName);
        }

        // If the route ends with .update, convert it to .edit
        if (str_ends_with($routeName, '.show')) {
            return str_replace('.show', '.list', $routeName);
        }

        return $routeName;
    }
}

