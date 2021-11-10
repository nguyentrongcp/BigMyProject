<?php

namespace App\Http\Controllers\HangHoa;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\NhanVien;
use App\Models\HangHoaChiTiet;
use App\Models\ThongBaoGia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiaBanController extends Controller
{
    public function index() {
        return view('quanly.hanghoa.giaban.index');
    }

    public function danh_sach(Request $request) {
        $hanghoa_id = $request->hanghoa_id;

        $results = HangHoaChiTiet::where('hanghoa_id',$hanghoa_id)->get(['id','chinhanh_id','tonkho','dongia']);
        foreach($results as $result) {
            $result->ten = $result->getChiNhanh()->ten;
        }

        return $results;
    }

    public function cap_nhat(Request $request) {
        $id = $request->id;
        $dongia = $request->dongia;

        if ($dongia < 0) {
            return [
                'succ' => 0,
                'erro' => 'Đơn giá không hợp lệ!'
            ];
        }

        $model = HangHoaChiTiet::find($id,['id','dongia','chinhanh_id','hanghoa_id']);
        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Không tìm thấy dữ liệu cập nhật!'
            ];
        }

        $giacu = $model->dongia;
        $model->dongia = $dongia;
        $giamoi = $dongia;
        $chenhlech = $giamoi - $giacu;
        $data_socket = [];
        if ($model->update()) {
            if ($chenhlech != 0) {
                $thongbao = new ThongBaoGia();
                $hanghoa = HangHoa::withTrashed()->find($model->hanghoa_id,['ma','ten']);
                $thongbao->chinhanh_id = $model->chinhanh_id;
                $thongbao->nhanvien_id = Funcs::getNhanVienIDByToken($request->cookie('token'));
                $thongbao->hanghoa_id = $model->hanghoa_id;
                $thongbao->hanghoa_ma = $hanghoa->ma;
                $thongbao->giacu = $giacu;
                $thongbao->giamoi = $giamoi;
                $thongbao->save();
                $tokens = NhanVien::where('chinhanh_id',$model->chinhanh_id)->pluck('remember_token');
                foreach($tokens as $token) {
                    $data_socket[] = [
                        'topic' => $token,
                        'title' => 'Thay Đổi Giá Bán',
                        'noidung' => "$hanghoa->ten\nGiá mới: ".number_format($giamoi),
                        'cuahang' => ChiNhanh::withTrashed()->find($model->chinhanh_id,'ten')->ten
                    ];
                }
//                $data_socket[] = [
//                    'topic' => $model->chinhanh_id,
//                    'title' => 'Thay Đổi Giá Bán',
//                    'noidung' => "$hanghoa->ten\nGiá mới: ".number_format($giamoi)
//                ];
                event(new Pusher('thongbaogia',''));
                event(new Pusher('reload-danhsach-hanghoa',''));
            }
            return [
                'succ' => 1,
                'noti' => 'Cập nhật đơn giá mới thành công.',
                'data' => [
                    'data_socket' => $data_socket
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật đơn giá mới thất bại!'
            ];
        }
    }

    public function dong_bo(Request $request) {
        $hanghoa_id = $request->hanghoa_id;
        $dongia = $request->dongia;

        if ($dongia < 0) {
            return [
                'succ' => 0,
                'erro' => 'Đơn giá không hợp lệ!'
            ];
        }
        $chitiets = HangHoaChiTiet::where('hanghoa_id',$hanghoa_id)->get(['id','dongia','chinhanh_id']);
        $nhanvien_id = Funcs::getNhanVienIDByToken($request->cookie('token'));

        $hanghoa = HangHoa::withTrashed()->find($hanghoa_id,['ma','ten']);
        $data_socket = [];
        DB::beginTransaction();
        foreach($chitiets as $chitiet) {
            $giacu = $chitiet->dongia;
            $giamoi = $dongia;
            $chenhlech = $giamoi - $giacu;
            $chitiet->dongia = $dongia;
            $chitiet->update();

            if ($chenhlech != 0) {
                $thongbao = new ThongBaoGia();
                $thongbao->chinhanh_id = $chitiet->chinhanh_id;
                $thongbao->nhanvien_id = $nhanvien_id;
                $thongbao->hanghoa_id = $hanghoa_id;
                $thongbao->hanghoa_ma = $hanghoa->ma;
                $thongbao->giacu = $giacu;
                $thongbao->giamoi = $giamoi;
                $thongbao->save();
//                $data_socket[] = [
//                    'topic' => $chitiet->chinhanh_id,
//                    'title' => 'Thay Đổi Giá Bán',
//                    'noidung' => "$hanghoa->ten\nGiá mới: ".number_format($giamoi)
//                ];
                $tokens = NhanVien::where('chinhanh_id',$chitiet->chinhanh_id)->pluck('remember_token');
                foreach($tokens as $token) {
                    $data_socket[] = [
                        'topic' => $token,
                        'title' => 'Thay Đổi Giá Bán',
                        'noidung' => "$hanghoa->ten\nGiá mới: ".number_format($giamoi),
                        'cuahang' => 'Toàn hệ thống'
                    ];
                }
            }
        }
        event(new Pusher('thongbaogia',''));
        event(new Pusher('reload-danhsach-hanghoa',''));

        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Đồng bộ giá bán thành công.',
            'data' => [
                'data_socket' => $data_socket
            ]
        ];
    }

    public function thong_bao_gia(Request $request) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));

        $thongbaos = ThongBaoGia::where('chinhanh_id',$chinhanh_id)
            ->whereRaw('giacu != giamoi')
            ->where('created_at','>',date('Y-m-d'))->orderByDesc('created_at')->get();

        foreach($thongbaos as $thongbao) {
            $thongbao->hanghoa = $thongbao->getTenHangHoa();
        }

        return $thongbaos;
    }
}
