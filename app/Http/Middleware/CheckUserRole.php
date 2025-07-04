<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Superadmin bisa akses semua
        if ($user->isSuperadmin()) {
            return $next($request);
        }

        // Cek role spesifik
        switch ($role) {
            case 'hrga':
                if (!$user->isHrga()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            case 'admin':
                if (!$user->isAdmin()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            case 'manager':
                if (!$user->isManager()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            case 'security':
                if (!$user->isSecurity()) {
                    abort(403, 'Unauthorized action.');
                }
                break;
            default:
                abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
