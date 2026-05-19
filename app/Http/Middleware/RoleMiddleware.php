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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== $role) {
            $userRole = auth()->user()->role;
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Unauthorized access.');
            } elseif ($userRole === 'hospital') {
                return redirect()->route('hospital.dashboard')->with('error', 'Unauthorized access.');
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        return $next($request);
    }
}
