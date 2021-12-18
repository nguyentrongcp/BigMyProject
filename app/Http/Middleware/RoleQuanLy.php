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
            $info = Funcs::getNhanVienByToken($token,['chinhanh_id','phanquyen']);
            $urls = Funcs::getUrlPhanQuyenByIDPhanQuyen($info->phanquyen);
            $currentUrl = str_replace(url()->to('/').'/','',url()->current());
            if (strpos($currentUrl,'quan-ly/xem-phieu') !== false) {
                $currentUrl = 'quan-ly/xem-phieu';
            }
            if (strpos($currentUrl,'quan-ly/hang-hoa/in-qrcode') !== false) {
                $currentUrl = 'quan-ly/hang-hoa/qrcode';
            }
            if (strpos($currentUrl,'quan-ly/quy-trinh-lua/viewer/cay-quy-trinh') !== false) {
                $currentUrl = 'quan-ly/quy-trinh-lua/viewer/cay-quy-trinh';
            }
            if (in_array($currentUrl,$urls) === false && in_array($currentUrl,['quan-ly/quy-trinh-lua/viewer/cay-quy-trinh']) === false) {
                abort(404);
            }

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
