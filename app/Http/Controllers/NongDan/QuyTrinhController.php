<?php

namespace App\Http\Controllers\NongDan;

use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\SanPham;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Http\Request;

class QuyTrinhController extends Controller
{
    public function index(Request $request) {
        $thuaruongs = ThuaRuong::orderByDesc('ngaysa')->get();
        $thuaruong_id = $request->thuaruong_id ?? null;
        $thuaruong = $thuaruong_id == null ? (count($thuaruongs) > 0 ? $thuaruongs[0] : null) : ThuaRuong::find($thuaruong_id);
        if ($thuaruong != null) {
            $thuaruong->songay = strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa);
            $thuaruong->songay = $thuaruong->songay/3600/24;
        }

        return view('nongdan.quytrinh.index', [
            'thuaruongs' => $thuaruongs,
            'thuaruong' => $thuaruong
        ]);
    }

    public function danh_sach(Request $request) {
        $thuaruong_id = $request->thuaruong_id ?? null;
        if ($thuaruong_id == null) {
            return [];
        }
        $thuaruong = ThuaRuong::find($thuaruong_id);
        if ($thuaruong == null) {
            return [];
        }
        $models = QuyTrinh::where('muavu_id',$thuaruong->muavu_id)->orderBy('tu')->orderBy('den')->orderBy('phanloai')->get();

        $quytrinh_ids = [];
        foreach($models as $model) {
            $quytrinh_ids[] = $model->id;
        }

        $quytrinh_thuaruong = QuyTrinhThuaRuong::where('thuaruong_id',$thuaruong_id)
            ->whereIn('quytrinh_id',$quytrinh_ids)->get();
        foreach($quytrinh_thuaruong as $key => $item) {
            $quytrinh_thuaruong[$item->quytrinh_id] = $item;
            unset($quytrinh_thuaruong[$key]);
        }

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
            $model->trangthai = isset($quytrinh_thuaruong[$model->id]) ? $quytrinh_thuaruong[$model->id]->status : 0;
            $model->nongdan_ghichu = isset($quytrinh_thuaruong[$model->id]) ? $quytrinh_thuaruong[$model->id]->ghichu : '';
        }

        return $models;
    }

    public function hoan_thanh(Request $request) {
        $quytrinh_id = $request->quytrinh_id ?? null;
        $thuaruong_id = $request->thuaruong_id ?? null;

        if ($quytrinh_id == null || $thuaruong_id == null ||
            QuyTrinhThuaRuong::where(['quytrinh_id'=>$quytrinh_id,'thuaruong_id'=>$thuaruong_id])->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $quytrinh_thuaruong = new QuyTrinhThuaRuong();
        $quytrinh_thuaruong->quytrinh_id = $quytrinh_id;
        $quytrinh_thuaruong->thuaruong_id = $thuaruong_id;
        $quytrinh_thuaruong->ghichu = $request->ghichu ?? null;

        if ($quytrinh_thuaruong->save()) {
            return [
                'succ' => 1,
                'noti' => 'Xác nhận hoàn thành quy trình thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xác nhận hoàn thành quy trình thất bại.'
            ];
        }
    }

    public function huy(Request $request) {
        $quytrinh_id = $request->quytrinh_id ?? null;
        $thuaruong_id = $request->thuaruong_id ?? null;

        $quytrinh_thuaruong = QuyTrinhThuaRuong::where(['quytrinh_id'=>$quytrinh_id,'thuaruong_id'=>$thuaruong_id])->first();
        if ($quytrinh_thuaruong == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }
        if ($quytrinh_thuaruong->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Hủy hoàn thành quy trình thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Hủy hoàn thành quy trình thành công.'
            ];
        }
    }
}
