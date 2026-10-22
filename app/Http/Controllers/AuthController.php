<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function show(Request $request)
    {
        if ($request->session()->get('kb_login')) {
            return redirect()->route('home');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $okUser = hash_equals((string) config('kb.user'), (string) $request->input('username'));
        $okPass = hash_equals((string) config('kb.pass'), (string) $request->input('password'));

        if ($okUser && $okPass) {
            $request->session()->regenerate();
            $request->session()->put('kb_login', true);
            return redirect()->route('home');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
