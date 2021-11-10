<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use Illuminate\Http\Request;

class TimPhieuController extends Controller
{
    public function index(Request $request) {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if ($info->id == '1000000000') {
            $chinhanhs = ChiNhanh::whereIn('loai',['cuahang','congty'])->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('quanly.timphieu.index', [
            'chinhanhs' => $chinhanhs,
        ]);
    }
}
