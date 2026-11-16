<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userRole = strtolower(Auth::user()->role ?? '');

        // Map Indonesian alias
        if ($userRole === 'kurir') {
            $userRole = 'courier';
        }

        $normalizedRoles = array_map(function (string $role): string {
            $r = strtolower(trim($role));

            return $r === 'kurir' ? 'courier' : $r;
        }, $roles);

        if (! in_array($userRole, $normalizedRoles, true)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
