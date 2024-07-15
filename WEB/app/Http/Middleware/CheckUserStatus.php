<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->status) {
            Auth::logout();
            return redirect('/login')->with('loginError', 'Akun Anda dinonaktifkan. Mohon hubungi admin untuk mengaktifkannya.');
        }

        return $next($request);
    }
}
