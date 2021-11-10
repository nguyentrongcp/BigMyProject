<?php

namespace App\Http\Controllers\DanhMuc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiaChiController extends Controller
{
    public function danhmuc_tinh (Request $request) {
        $q = $request->q;
        $dmtinhs = DB::select("
            select name_with_type as id,
                   name_with_type as text,
                   code
            from danhmuc_tinh
            where name like '%$q%'
        ");

        return [
            'results' => $dmtinhs
        ];
    }

    public function danhmuc_huyen (Request $request) {
        $parent_code = $request->parent_code;
        $q = $request->q;

        $dmhuyens = DB::select("
            select name_with_type as id,
                   name_with_type as text,
                   code, path_with_type
            from danhmuc_huyen
            where parent_code = '$parent_code'
            and name like '%$q%'
        ");

        return [
            'results' => $dmhuyens
        ];
    }

    public function danhmuc_xa (Request $request) {
        $parent_code = $request->parent_code;
        $q = $request->q;

        $dmxas = DB::select("
            select name_with_type as id,
                   name_with_type as text,
                   path_with_type
            from danhmuc_xa
            where parent_code = '$parent_code'
            and name like '%$q%'
        ");

        return [
            'results' => $dmxas
        ];
    }
}
