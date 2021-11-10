<?php

namespace App\Http\Middleware;

use App\Functions\Funcs;
use Closure;
use Illuminate\Http\Request;

class RoleApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $_COOKIE['token'] ?? null;
        if ($token == null) {
            return redirect(route('dang-nhap'));
        }

        return Funcs::checkRememberToken($token) ? $next($request) : ['sess' => 0];
    }
}
