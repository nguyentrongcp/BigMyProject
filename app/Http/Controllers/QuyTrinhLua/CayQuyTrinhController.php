<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinhSuDung;
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
        $phanloai = $request->phanloai ?? null;
        if ($muavu_id == null || $phanloai == null) {
            return [];
        }
        $phanloai = $phanloai == 'phan' ? 'Phân bón' : 'Thuốc';
        $models = QuyTrinhSuDung::where('muavu_id',$muavu_id)->whereIn('sanpham_id',SanPham::withTrashed()->where('phanloai',$phanloai)->pluck('id'))
            ->orderBy('tu')->orderBy('den')->get();

        $sanphams = SanPham::withTrashed()->where('phanloai',$phanloai)->get(['id','ten','donvitinh','phanloai','dongia']);
        foreach($sanphams as $key => $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
            unset($sanphams[$key]);
        }

        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->phanloai = $sanphams[$model->sanpham_id]->phanloai;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
        }

        return $models;
    }
}
