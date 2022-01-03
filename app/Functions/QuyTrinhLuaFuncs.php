<?php

namespace App\Functions;

use App\Models\QuyTrinhLua\NongDan;
use Illuminate\Support\Facades\DB;

class QuyTrinhLuaFuncs
{
    public static function getNongDanByToken($token, $columns=null) {
        return $columns == null ? NongDan::where('remember_token',$token)->first() : NongDan::where('remember_token',$token)->first($columns);
    }

    public static function checkRememberToken($token) {
        return NongDan::where('remember_token',$token)->count() > 0;
    }

    public static function getNongDanIDByToken($token) {
        $nongdan = self::getNongDanByToken($token,['id']);
        return $nongdan == null ? null : $nongdan->id;
    }

    public static function getMaNongDan() {
        $maxMaND = DB::select("select max(ma) as ma from quytrinhlua_nongdan")[0];
        if ($maxMaND->ma == null) {
            return 'ND000001';
        }
        else {
            $maxMaND = (int) substr($maxMaND->ma,2);
            $maxMaND++;
            while (strlen((string) $maxMaND) < 6) {
                $maxMaND = '0'.$maxMaND;
            }
            return 'ND'.$maxMaND;
        }
    }

    public static function getMaSanPham() {
        $maxMaSP= DB::select("select max(ma) as ma from quytrinhlua_sanpham where ma like 'SP%'")[0];
        if ($maxMaSP->ma == null) {
            return 'SP000001';
        }
        else {
            $maxMaSP = (int) substr($maxMaSP->ma,2);
            $maxMaSP++;
            while (strlen((string) $maxMaSP) < 6) {
                $maxMaSP = '0'.$maxMaSP;
            }
           return 'SP'.$maxMaSP;
        }
    }
}
