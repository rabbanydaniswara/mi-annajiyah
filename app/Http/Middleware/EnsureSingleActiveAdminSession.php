<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleActiveAdminSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->active_session_id) {
            $currentSessionId = $request->session()->getId();

            if (! hash_equals($user->active_session_id, $currentSessionId)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()
                    ->route('admin.login')
                    ->withErrors([
                        'login' => 'Sesi akun ini sudah aktif di perangkat lain. Silakan login kembali.',
                    ]);
            }
        }

        return $next($request);
    }
}
