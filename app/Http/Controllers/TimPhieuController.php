<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use Illuminate\Http\Request;

class TimPhieuController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if (Funcs::isPhanQuyenByToken('role.chi-nhanh.tat-ca',$_COOKIE['token'])) {
            $chinhanhs = ChiNhanh::whereIn('loai',['cuahang','congty'])->orWhere('id',$info->chinhanh_id)->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('quanly.timphieu.index', [
            'chinhanhs' => $chinhanhs,
        ]);
    }
}
