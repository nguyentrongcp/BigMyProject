<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\SanPham;
use Illuminate\Http\Request;

class CayQuyTrinhController extends Controller
{
    public function index() {
        $muavus = MuaVu::where('status',1)->get(['id','ten as text']);

        return view('quanly.quytrinhlua.cayquytrinh.index', [
            'muavus' => $muavus
        ]);
    }

    public function danh_sach(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        if ($muavu_id == null) {
            return [];
        }
        $models = QuyTrinh::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')->orderBy('den')->get();

        $sanphams = SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']);
        foreach($sanphams as $key => $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
            unset($sanphams[$key]);
        }

        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
        }

        return $models;
    }
}
