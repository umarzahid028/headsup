<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // During development, allow access to vendor routes
        if (config('app.env') === 'local') {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user->role || !($user->role instanceof Role) || !$user->role->isVendor()) {
            abort(403, 'Unauthorized. Vendor access only.');
        }

        return $next($request);
    }
} 