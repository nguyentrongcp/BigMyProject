<?php

namespace App\Http\Middleware;

use App\Functions\QuyTrinhLuaFuncs;
use Closure;
use Illuminate\Http\Request;

class RoleNongDan
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
            return redirect(route('nong-dan.dang-nhap'));
        }

        if (!QuyTrinhLuaFuncs::checkRememberToken($token)) {
            return redirect(route('nong-dan.dang-nhap'));
        }

        return $next($request);
    }
}
