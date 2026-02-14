<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Kalau user belum login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Kalau role user tidak sama dengan yang diminta
        if (auth()->user()->role !== strtolower($role)) {
            abort(403, 'Akses Ditolak.');
        }
        
        return $next($request);
    }
}
