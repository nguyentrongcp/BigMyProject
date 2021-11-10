<?php

namespace App\Http\Middleware;

use App\Functions\Funcs;
use Closure;
use Illuminate\Http\Request;

class RoleQuanLy
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

        if (Funcs::checkRememberToken($token)) {
            $info = Funcs::getNhanVienByToken($token,['chinhanh_id']);

            if (!$info->isChiNhanhAvailable()) {
                abort(401);
            }
        }
        else {
            return redirect(route('dang-nhap'));
        }

        return $next($request);
    }
}
