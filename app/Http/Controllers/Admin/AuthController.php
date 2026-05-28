<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $key = 'login.' . Str::lower($request->input('username')) . '.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['login' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."]);
        }

        // Laravel uses 'name' or 'email' by default; we use 'username'
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 60);
        return back()->withErrors(['login' => 'Username atau password salah!']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
