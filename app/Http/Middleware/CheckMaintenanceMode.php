<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Bypass maintenance mode for:
     *  - /login  (so admin can log in)
     *  - /logout
     *  - /dashboard/* (already behind auth+role, let the role middleware decide)
     *  - /api/*  (so the dashboard JS still works for logged-in admin)
     *  - /maintenance (the page itself)
     */
    private const BYPASS_PREFIXES = [
        'login',
        'logout',
        'dashboard',
        'api',
        'maintenance',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Only engage when the setting is explicitly '1'
        if (Setting::get('maintenance_mode', '0') !== '1') {
            return $next($request);
        }

        // Admins / editors can still access the site
        $user = $request->user();
        if ($user && $user->roles->whereIn('name', ['admin', 'editor'])->isNotEmpty()) {
            return $next($request);
        }

        // Allow bypass paths
        $path = ltrim($request->path(), '/');
        foreach (self::BYPASS_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        // Inertia requests get a JSON redirect signal
        if ($request->header('X-Inertia')) {
            return response()->json(['url' => '/maintenance'], 409)
                ->header('X-Inertia-Location', url('/maintenance'));
        }

        return redirect()->route('maintenance');
    }
}
