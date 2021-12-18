<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\ThuaRuong;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\SanPham;
use Illuminate\Http\Request;

class ThuaRuongController extends Controller
{
    public function index($thuaruong_id) {
        if ($thuaruong_id == null) {
            abort(404);
        }

        return view('quanly.quytrinhlua.thuaruong.index', [
            'thuaruong_id' => $thuaruong_id
        ]);
    }

    public function cay_quy_trinh(Request $request) {
        $thuaruong_id = $request->thuaruong_id;

        if ($thuaruong_id == null) {
            return [];
        }

        $thuaruong = ThuaRuong::find($thuaruong_id,['muavu_id']);
        if ($thuaruong == null) {
            return [];
        }
        $muavu_id = $thuaruong->muavu_id;
        $models = QuyTrinh::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')->orderBy('den')->get();

        $sanphams = SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']);
        foreach($sanphams as $key => $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
            unset($sanphams[$key]);
        }

        $quytrinh_thuaruong = QuyTrinhThuaRuong::where('thuaruong_id',$thuaruong_id)->get();

        foreach($quytrinh_thuaruong as $key => $item) {
            $quytrinh_thuaruong[$item->quytrinh_id] = $item;
            unset($quytrinh_thuaruong[$key]);
        }

        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
            $model->quytrinh_thuaruong = $quytrinh_thuaruong[$model->id] ?? null;
        }

        return $models;
    }

    public function danh_sach(Request $request) {
        $nongdan_id = $request->nongdan_id;
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.nong-dan.action',$request->cookie('token'))) {
            $results = ThuaRuong::withTrashed()->where('nongdan_id',$nongdan_id)
            ->orderByDesc('deleted_at')->orderByDesc('ngaysa')->get();
        }
        else {
            $results = ThuaRuong::where('nongdan_id',$nongdan_id)->orderByDesc('ngaysa')->get();
        }

        $muavus = MuaVu::withTrashed()->get(['id','ten','status']);
        $quytrinh = QuyTrinh::groupBy('muavu_id')->selectRaw('count(id) as soluong, muavu_id')->pluck('soluong','muavu_id');
        foreach($muavus as $key => $muavu) {
            $muavus[$muavu->id] = $muavu;
            unset($muavus[$key]);
        }

        foreach($results as $result) {
            $result->muavu = $muavus[$result->muavu_id]->ten ?? 'Chưa rõ';
            $result->status = $muavus[$result->muavu_id]->status ?? 'Chưa rõ';
            $result->tinhtrang_hoanthanh = 0;
            $result->tongquytrinh = $quytrinh[$result->muavu_id] ?? 0;
        }

        return $results;
    }
}
