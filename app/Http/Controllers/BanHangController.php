<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\CayTrong;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\QuyDoi;
use App\Models\HangHoaChiTiet;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BanHangController extends Controller
{
    public function index() {
        $caytrongs = CayTrong::all('ten as text');
        $nhanviens = NhanVien::all(['id','ma','ten','dienthoai']);

        $chinhanh_id = Funcs::getChiNhanhByToken($_COOKIE['token']);
        $dongias = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('dongia','hanghoa_ma');
        $tonkhos = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('tonkho','hanghoa_ma');

        $models = HangHoa::all(['id','ma','slug','ten','donvitinh','nhom','quycach','ghichu','hoatchat','congdung','dang']);

        foreach($models as $model) {
            $model->tonkho = $tonkhos[$model->ma] ?? 0;
            $model->dongia = $dongias[$model->ma] ?? 'Chưa có';
        }

        return view('quanly.banhang.index', [
            'caytrongs' => $caytrongs,
            'nhanviens' => $nhanviens,
            'hanghoas' => $models
        ]);
    }

    public function danhsach_khachhang() {
        return KhachHang::all(['id','ma','ten','dienthoai','diachi','congno']);
    }

    public function tim_kiem(Request $request) {
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $dongias = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('dongia','hanghoa_ma');
        $tonkhos = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('tonkho','hanghoa_ma');

        $q = Funcs::convertToSlug($request->q);

        $models = HangHoa::where('slug','like',"%$q%")->orWhere('ma','like',"%$q%")
            ->limit(20)->get(['id','ma','ten','donvitinh','nhom','quycach','ghichu','hoatchat','congdung','dang']);

        foreach($models as $model) {
            $model->tonkho = $tonkhos[$model->ma] ?? 0;
            $model->dongia = $dongias[$model->ma] ?? 'Chưa có';
        }

        return [
            'results' => $models
        ];
    }

    public function danh_sach(Request $request) {
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $dongias = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('dongia','hanghoa_ma');
        $tonkhos = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->pluck('tonkho','hanghoa_ma');

        $models = HangHoa::all(['id','ma','slug','ten','donvitinh','nhom','quycach','ghichu','hoatchat','congdung','dang']);

        foreach($models as $model) {
            $model->tonkho = $tonkhos[$model->ma] ?? 0;
            $model->dongia = $dongias[$model->ma] ?? 'Chưa có';
        }

        return $models;
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id']);

        if (Funcs::isKetSo($nhanvien->chinhanh_id)) {
            return [
                'succ' => 0,
                'noti' => 'Bạn đã kết sổ ngày hôm nay. Không thể lập thêm phiếu!'
            ];
        }

        $sophieu = Funcs::getSoPhieu('BH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong_id;
        $phieu->maphieu = 'BH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'BH';
        $phieu->tongthanhtien = $data->tongthanhtien;
        $phieu->phuthu = $data->phuthu;
        $phieu->giamgia = $data->giamgia;
        $phieu->tienthanhtoan = $data->tienthanhtoan;
        $phieu->tienkhachdua = $data->tienkhachdua;
        $phieu->tienthua = $data->tienthua;
        $phieu->ghichu = $data->ghichu ?? null;
        $phieu->sophieu = Funcs::getSTTPhieu('BH',$nhanvien->chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
        $phieu->nhanvien_tuvan_id = $data->nhanvien_tuvan->id;
        $phieu->chinhanh_id = $nhanvien->chinhanh_id;
        $phieu->ngay = date('Y-m-d');
        $phieu->gio = date('H:i:s');

        $message = '';

        try {
            $continue = $phieu->save();
        }
        catch (QueryException $exception) {
            $continue = false;
            $message = $exception->getMessage();
        }

        foreach ($data->dshanghoa as $value) {
            if (!$continue) {
                break;
            }

            $quydoi = QuyDoi::where('id_con',$value->hanghoa->id)->first(['id_cha','soluong']);
            $hanghoa_id = $value->hanghoa->id;
            $hanghoa_ma = $value->hanghoa->ma;
            $id_quydoi = null;
            if ($quydoi != null) {
                $hanghoa_id = $quydoi->id_cha;
                $hanghoa_ma = HangHoa::find($quydoi->id_cha,['ma'])->ma;
                $id_quydoi = $value->hanghoa->id;
                $quydoi = $quydoi->soluong;
            }

            $chitiet = new PhieuChiTiet();
            $chitiet->phieu_id = $phieu->id;
            $chitiet->maphieu = $phieu->maphieu;
            $chitiet->loaiphieu = 'BH';
            $chitiet->hanghoa_id = $hanghoa_id;
            $chitiet->hanghoa_ma = $hanghoa_ma;
            $chitiet->gianhap = HangHoa::find($hanghoa_id,['gianhap'])->gianhap;
            $chitiet->dongia = $value->dongia;
            $chitiet->soluong = $value->soluong;
            $chitiet->giamgia = $value->giamgia;
            $chitiet->thanhtien = $value->thanhtien;
            $chitiet->id_quydoi = $id_quydoi;
            $chitiet->quydoi = $quydoi ?? 1;
            $chitiet->is_tangkho = -1;
            $chitiet->chinhanh_id = $phieu->chinhanh_id;
            $chitiet->sophieu = $phieu->sophieu;
            $chitiet->ngay = date('Y-m-d');
            $chitiet->gio = date('H:i:s');

            try {
                $continue = $chitiet->save();
            }
            catch (QueryException $exception) {
                $message = $exception->getMessage();
                $continue = false;
            }

            $continue = $continue ? $chitiet->capNhatTonKho() : false;
        }

        $continue = $continue ? $phieu->capNhatKhachHang() : false;

        if (!$continue) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Lưu phiếu thất bại. Vui lòng thử lại lần nữa!',
                'mess' => $message
            ];
        }
        else {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Lưu phiếu thành công.',
                'data' => [
                    'maphieu' => $phieu->maphieu
                ]
            ];
        }
    }
}
