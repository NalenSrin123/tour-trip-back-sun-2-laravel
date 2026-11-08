<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        // 1. Ensure user is logged in
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }


        // 2. Parse the roles passed to middleware (supports "admin|tour_manager|....")
        $roleList = explode('|', $roles);

        // 3. Check if user has any of the required roles
        $hasRole = $user->roles->contains(function ($role) use ($roleList) {
            return in_array($role->name, $roleList);
        });

        if (!$hasRole) {
            abort(403, 'Unauthorized access.');
        }
        return $next($request);
    }
}
