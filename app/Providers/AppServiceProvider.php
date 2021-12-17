<?php

namespace App\Providers;

use App\Functions\Funcs;
use App\Functions\QuyTrinhLuaFuncs;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (isset($_COOKIE['token'])) {
            $token = $_COOKIE['token'];
            $type = Funcs::checkToken($token);
            if ($type === 'nhanvien') {
                $info = Funcs::getNhanVienByToken($token);
                if ($info != null) {
                    $info->setTenChiNhanh();
                    $info->setChucVu();
                    $info->urls = Funcs::getUrlPhanQuyenByIDPhanQuyen($info->phanquyen);
                    $info->phanquyen = Funcs::getPhanQuyenByIDPhanQuyen($info->phanquyen);
                    unset($info->quyendacbiet);
                    unset($info->quyenloaibo);
                    $so_phieunhap = Phieu::where([
                        'loaiphieu' => 'NH',
                        'status' => 0
                    ])->count();
                    $so_phieuxuat = PhieuChiTiet::whereIn('phieu_id',Phieu::where([
                        'doituong_id' => $info->chinhanh_id,
                        'loaiphieu' => 'XKNB'
                    ])->pluck('id'))->where('status',0)->groupBy('phieu_id')->selectRaw('count(*) as sophieu')->get()->count();
                    View::share([
                        'info' => $info,
                        'so_phieunhap' => $so_phieunhap,
                        'so_phieuxuat' => $so_phieuxuat
                    ]);
                }
            }
            else {
                $info = QuyTrinhLuaFuncs::getNongDanByToken($token);
                if ($info != null) {
                    View::share([
                        'info' => $info
                    ]);
                }
            }
        }
    }
}
