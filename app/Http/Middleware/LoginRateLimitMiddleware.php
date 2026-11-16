<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimitMiddleware
{
    /**
     * Handle an incoming request.
     * Cek apakah user telah gagal login 4 kali atau lebih dan sedang dalam periode lockout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->routeIs('login.post')) {
            $key = 'login_lockout_'.sha1($request->ip().'_'.strtolower((string) $request->input('email')));

            if (Cache::has($key)) {
                $lockoutUntil = Cache::get($key);
                $secondsLeft = max(1, $lockoutUntil - time());

                return redirect()->route('login')
                    ->withInput($request->only('email'))
                    ->with('login_locked', true)
                    ->with('lockout_seconds', $secondsLeft)
                    ->withErrors([
                        'email' => "Terlalu banyak percobaan login gagal (4 kali). Demi keamanan, akun Anda dikunci sementara selama {$secondsLeft} detik. Silakan tunggu sebelum mencoba kembali.",
                    ]);
            }
        }

        return $next($request);
    }
}
