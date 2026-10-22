<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KbAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->get('kb_login')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
