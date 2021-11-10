<?php

namespace App\Providers;

use App\Functions\Funcs;
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
            $info = Funcs::getNhanVienByToken($token);
            if ($info != null) {
                $info->setTenChiNhanh();
                $info->setChucVu();
                View::share([
                    'info' => $info
                ]);
            }
        }
    }
}
