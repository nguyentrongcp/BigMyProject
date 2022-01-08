<?php

namespace App\Http\Controllers\NongDan;

use App\Functions\Funcs;
use App\Functions\QuyTrinhLuaFuncs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\GiaiDoanPhanHoi;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\SanPham;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class QuyTrinhController extends Controller
{
    public function index(Request $request) {
        $nongdan_id = QuyTrinhLuaFuncs::getNongDanIDByToken($_COOKIE['token']);
        $thuaruongs = ThuaRuong::where('nongdan_id',$nongdan_id)->orderByDesc('ngaysa')->get();
        $thuaruong_id = $request->thuaruong_id ?? null;
        $giaidoan_id = $request->giaidoan_id ?? '';
        $thuaruong = $thuaruong_id == null ? (count($thuaruongs) > 0 ? $thuaruongs[0] : null) : ThuaRuong::find($thuaruong_id);
        if ($thuaruong != null) {
            $thuaruong->songay = strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa);
            $thuaruong->songay = $thuaruong->songay/3600/24;
        }

        return view('nongdan.quytrinh.index', [
            'thuaruongs' => $thuaruongs,
            'thuaruong' => $thuaruong,
            'giaidoan_id' => $giaidoan_id
        ]);
    }

    public function index_hientai(Request $request) {
        return view('nongdan.quytrinh-hientai.index', [
            'giaidoan_id' => $request->giaidoan_id ?? ''
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
        $thuaruong->songay = strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa);
        $thuaruong->songay = $thuaruong->songay/3600/24;
        $models = QuyTrinh::where('muavu_id',$thuaruong->muavu_id)->orderBy('tu')->orderBy('den')->orderBy('phanloai')->get();

        $quytrinh_ids = [];
        foreach($models as $model) {
            $quytrinh_ids[] = $model->id;
        }

        $quytrinh_thuaruong = [];
        foreach(QuyTrinhThuaRuong::where('thuaruong_id',$thuaruong_id)
                    ->whereIn('quytrinh_id',$quytrinh_ids)->get() as $item) {
            $quytrinh_thuaruong[$item->quytrinh_id] = $item;
        }

        $sanphams = [];
        foreach(SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']) as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }

        $giaidoans = [];
        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
            $model->trangthai = isset($quytrinh_thuaruong[$model->id]) ? $quytrinh_thuaruong[$model->id]->status : 0;
            $model->nongdan_ghichu = isset($quytrinh_thuaruong[$model->id]) ? $quytrinh_thuaruong[$model->id]->ghichu : '';
            if (!isset($giaidoans[$model->giaidoan_id])) {
                $giaidoans[$model->giaidoan_id] = [];
            }
            $giaidoans[$model->giaidoan_id][] = $model;
        }

        $results = [];
        foreach(GiaiDoan::where('muavu_id',$thuaruong->muavu_id,)->orderBy('tu')
                    ->orderBy('den')->get(['id','muavu_id','ten','tu','den','phanloai']) as $item) {
            $item->quytrinhs = $giaidoans[$item->id] ?? [];
            $item->phanhois = GiaiDoanPhanHoi::where([
                'giaidoan_id' => $item->id,
                'thuaruong_id' => $thuaruong_id
            ])->orderBy('created_at')->get();
            $nhanvien_ids = GiaiDoanPhanHoi::where([
                'giaidoan_id' => $item->id,
                'thuaruong_id' => $thuaruong_id
            ])->pluck('nhanvien_id');
            $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanvien_ids)->pluck('ten','id');
            foreach($item->phanhois as $phanhoi) {
                if ($phanhoi->nhanvien_id != null) {
                    $phanhoi->nhanvien = $nhanviens[$phanhoi->nhanvien_id] ?? 'Chưa xác định';
                }
            }
            $results[] = $item;
        }

        return [
            'data' => [
                'muavu_id' => $thuaruong->muavu_id,
                'thuaruong' => $thuaruong,
                'danhsach' => $results
            ]
        ];
    }

    public function danhsach_hientai(Request $request) {
        $muavu_ids = MuaVu::where('status',1)->pluck('id');
        $nongdan_id = QuyTrinhLuaFuncs::getNongDanIDByToken($request->cookie('token'));
        $thuaruongs = ThuaRuong::where('nongdan_id',$nongdan_id)
            ->whereIn('muavu_id',$muavu_ids)->get(['muavu_id','ngaysa','id','ten','toado']);
        $results = [];
        foreach($thuaruongs as $thuaruong) {
            $songay = (strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa))/86400;
            $giaidoans = GiaiDoan::where('muavu_id',$thuaruong->muavu_id)->where('tu','<=',$songay)
                ->where('den','>=',$songay)->orderBy('tu')->orderBy('den')->get();
            foreach($giaidoans as $giaidoan) {
                $_quytrinhs = QuyTrinh::where([
                    'muavu_id' => $thuaruong->muavu_id,
                    'giaidoan_id' => $giaidoan->id
                ])->get();
                foreach($_quytrinhs as $quytrinh) {
                    $sanpham = SanPham::withTrashed()->find($quytrinh->sanpham_id);
                    $quytrinh->sanpham = $sanpham->ten;
                    $quytrinh->donvitinh = $sanpham->donvitinh;
                    $quytrinh_thuaruong = QuyTrinhThuaRuong::where([
                        'thuaruong_id' => $thuaruong->id,
                        'quytrinh_id' => $quytrinh->id
                    ])->first(['status','ghichu']);
                    $quytrinh->trangthai = $quytrinh_thuaruong == null ? 0 : $quytrinh_thuaruong->status;
                    $quytrinh->nongdan_ghichu = $quytrinh_thuaruong == null ? '' : $quytrinh_thuaruong->ghichu;
                }
                if (count($_quytrinhs) > 0) {
                    $giaidoan->quytrinhs = $_quytrinhs;
                    $giaidoan->phanhois = GiaiDoanPhanHoi::where([
                        'giaidoan_id' => $giaidoan->id,
                        'thuaruong_id' => $thuaruong->id
                    ])->orderBy('created_at')->get();
                    $nhanvien_ids = GiaiDoanPhanHoi::where([
                        'giaidoan_id' => $giaidoan->id,
                        'thuaruong_id' => $thuaruong->id
                    ])->pluck('nhanvien_id');
                    $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanvien_ids)->pluck('ten','id');
                    foreach($giaidoan->phanhois as $phanhoi) {
                        if ($phanhoi->nhanvien_id != null) {
                            $phanhoi->nhanvien = $nhanviens[$phanhoi->nhanvien_id] ?? 'Chưa xác định';
                        }
                    }
                }
            }
            if (count($giaidoans) > 0) {
                $thuaruong->songay = $songay;
                $thuaruong->giaidoans = $giaidoans;
                $results[] = $thuaruong;
            }
        }

        return [
            'data' => [
                'danhsach' => $results
            ]
        ];
    }

    public function hoan_thanh(Request $request) {
        $quytrinh_id = $request->quytrinh_id ?? null;
        $thuaruong_id = $request->thuaruong_id ?? null;
        $muavu_id = $request->muavu_id;

        if ($quytrinh_id == null || $thuaruong_id == null || $muavu_id == null ||
            QuyTrinhThuaRuong::where(['quytrinh_id'=>$quytrinh_id,'thuaruong_id'=>$thuaruong_id])->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $quytrinh_thuaruong = new QuyTrinhThuaRuong();
        $quytrinh_thuaruong->muavu_id = $muavu_id;
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

    public function gui_phan_hoi(Request $request) {
        $giaidoan_id = $request->giaidoan_id ?? null;
        $thuaruong_id = $request->thuaruong_id ?? null;
        $noidung = $request->noidung ?? '';
        $nongdan_id = QuyTrinhLuaFuncs::getNongDanIDByToken($request->cookie('token'));

        if ($giaidoan_id == null || $thuaruong_id == null || $noidung == '') {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $model = new GiaiDoanPhanHoi();
        $model->giaidoan_id = $giaidoan_id;
        $model->thuaruong_id = $thuaruong_id;
        $model->nongdan_id = $nongdan_id;
        $model->noidung = $noidung;

        try {
            $model->save();
            return [
                'succ' => 1,
                'noti' => 'Gửi phản hồi thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Gửi phản hồi thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }
    }

    public function xoa_phan_hoi(Request $request) {
        $phanhoi_id = $request->phanhoi_id ?? null;
        if ($phanhoi_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $phanhoi = GiaiDoanPhanHoi::find($phanhoi_id);

        if ($phanhoi->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa phản hồi thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa phản hồi thất bại. Vui lòng thử lại sau!'
            ];
        }
    }
}
