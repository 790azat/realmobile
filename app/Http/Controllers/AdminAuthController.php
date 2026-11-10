<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['login' => 'required|string', 'password' => 'required|string']);
        $key = 'admin-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 8)) {
            return back()->withErrors(['login' => 'Слишком много попыток, подождите минуту.']);
        }

        $expected = (string) config('shop.admin_password');
        if ($expected !== '' && hash_equals((string) config('shop.admin_login'), $data['login']) && hash_equals($expected, $data['password'])) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 60);

        return back()->withInput(['login' => $data['login']])->withErrors(['login' => 'Неверный логин или пароль']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }
}
