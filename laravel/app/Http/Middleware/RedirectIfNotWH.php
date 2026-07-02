<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotWH
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'wh_officer') {
                if ($request->routeIs('dashboard') || $request->routeIs('dashboard.detections') || $request->routeIs('dashboard.reports')) {
                    return $next($request);
                }

                return redirect()->route('dashboard');
            }

            if ($user->role === 'admin') {
                return $next($request);
            }
        }
        abort(403, 'Akses tidak sah.');
    }
}
