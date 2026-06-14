<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $key = 'login.'.Str::lower($request->input('username')).'.'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['login' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."]);
        }

        // Laravel uses 'name' or 'email' by default; we use 'username'
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            $request->user()->forceFill([
                'active_session_id' => $request->session()->getId(),
            ])->save();

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors(['login' => 'Username atau password salah!']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->active_session_id === $request->session()->getId()) {
            $user->forceFill(['active_session_id' => null])->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showPasswordForm()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();
        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password lama tidak sesuai.',
            ])->onlyInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLogger::log('change_password', $user, "Mengganti password akun {$user->username}");

        return redirect()->route('admin.password.edit')->with('success', 'Password berhasil diperbarui.');
    }
}
