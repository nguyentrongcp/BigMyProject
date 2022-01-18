<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\QuyTrinhLuaFuncs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\GiaiDoanPhanHoi;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\SanPham;
use App\Models\QuyTrinhLua\ThongBao;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Http\Request;

class ThongBaoController extends Controller
{
    public function danh_sach(Request $request) {
        $nongdan_id = QuyTrinhLuaFuncs::getNongDanIDByToken($request->cookie('token'));

        $thongbaos = [];
        foreach(ThongBao::where([
            'nongdan_id' => $nongdan_id,
            'is_viewed' => 0,
            'loai' => 'phanhoi'
        ])->get() as $item) {
            $item->nhanvien = NhanVien::withTrashed()->find($item->nhanvien_id,'ten')->ten;
            $item->thuaruong_id = GiaiDoanPhanHoi::find($item->phanhoi_id,['thuaruong_id'])->thuaruong_id;
            $item->thuaruong_ten = ThuaRuong::withTrashed()->find($item->thuaruong_id,'ten')->ten;
            $thongbaos[] = $item;
        }

        $quytrinhs = [];
        $muavu_ids = MuaVu::where('status',1)->pluck('id');
        $thuaruongs = ThuaRuong::where('nongdan_id',$nongdan_id)
            ->whereIn('muavu_id',$muavu_ids)->get(['muavu_id','ngaysa','id','ten']);
        foreach($thuaruongs as $thuaruong) {
            $songay = (strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa))/86400;
            $giaidoans = GiaiDoan::where('muavu_id',$thuaruong->muavu_id)->where('tu','<=',$songay)
                ->where('den','>=',$songay)->orderBy('tu')->orderBy('den')->get();
            foreach($giaidoans as $giaidoan) {
                $quytrinh_ids = QuyTrinh::where([
                    'muavu_id' => $thuaruong->muavu_id,
                    'giaidoan_id' => $giaidoan->id
                ])->pluck('id');
                $_quytrinhs = QuyTrinh::where([
                    'muavu_id' => $thuaruong->muavu_id,
                    'giaidoan_id' => $giaidoan->id
                ])->whereNotIn('id',QuyTrinhThuaRuong::where([
                    'status' => 1,
                    'thuaruong_id' => $thuaruong->id
                ])->whereIn('quytrinh_id',$quytrinh_ids)->pluck('quytrinh_id'))->get();
                foreach($_quytrinhs as $quytrinh) {
                    $sanpham = SanPham::withTrashed()->find($quytrinh->sanpham_id);
                    $quytrinh->sanpham = $sanpham->ten;
                    $quytrinh->donvitinh = $sanpham->donvitinh;
                }
                if (count($_quytrinhs) > 0) {
                    $quytrinhs[] = [
                        'thuaruong_id' => $thuaruong->id,
                        'thuaruong_ten' => $thuaruong->ten,
                        'giaidoan_id' => $giaidoan->id,
                        'danhsach' => $_quytrinhs
                    ];
                }
            }
        }

        return [
            'data' => [
                'quytrinhs' => $quytrinhs,
                'thongbaos' => $thongbaos
            ]
        ];
    }

    public function xem(Request $request) {
        $id = $request->id;
        if ($id !== null) {
            $thongbao = ThongBao::find($id);
            $thongbao->is_viewed = 1;
            $thongbao->update();
        }

        return [];
    }
}
