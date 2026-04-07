<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function Login()
    {
        if (Auth::check() && Auth::user()->roles === 'admin') {
            return redirect()->route('admin.home');
        }
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        if (request()->has('redirect')) {
            session(['url.intended' => request('redirect')]);
        }

        return view('auth.google-login');
    }

    public function checkLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->roles !== 'admin') {
                Auth::logout();
                return redirect()->back()->with('error', 'Akses di tolak!!');
            }

            return redirect()->intended(route('admin.home'));
        }

        return redirect()->back()->with('error', 'Email atau password salah!!');
    }
}
