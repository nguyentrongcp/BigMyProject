<?php

namespace App\Functions;

use App\Models\QuyTrinhLua\NongDan;

class QuyTrinhLuaFuncs
{
    public static function getNongDanByToken($token, $columns=null) {
        return $columns == null ? NongDan::where('remember_token',$token)->first() : NongDan::where('remember_token',$token)->first($columns);
    }

    public static function getNongDanIDByToken($token) {
        $nongdan = self::getNongDanByToken($token,['id']);
        return $nongdan == null ? null : $nongdan->id;
    }
}
