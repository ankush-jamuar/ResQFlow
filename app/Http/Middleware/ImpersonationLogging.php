<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ImpersonationLogging
{
    public function handle(Request $request, Closure $next)
    {
        $adminId = session('admin_impersonator_id');

        if ($adminId && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $admin = User::find($adminId);
            $target = auth()->user();

            Log::warning('[Security Audit] Impersonated Action Detected', [
                'admin_id' => $adminId,
                'admin_name' => $admin->name ?? 'Unknown',
                'target_user_id' => $target->id ?? 'Unknown',
                'target_user_name' => $target->name ?? 'Unknown',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'payload' => $request->except(['password', '_token', 'password_confirmation']),
                'ip' => $request->ip(),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        return $next($request);
    }
}
