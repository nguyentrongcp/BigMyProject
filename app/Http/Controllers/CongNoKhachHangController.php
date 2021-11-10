<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\KhachHang;
use App\Models\Phieu;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CongNoKhachHangController extends Controller
{
    public function index() {
        return view('quanly.tracuu-congno.congno-khachhang');
    }

    public function thu_congno(Request $request) {
        $data = json_decode($request->phieu);
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);

        if (Funcs::isKetSo($nhanvien->chinhanh_id)) {
            return [
                'succ' => 0,
                'noti' => 'Bạn đã kết sổ ngày hôm nay. Không thể lập thêm phiếu!'
            ];
        }

        $sophieu = Funcs::getSoPhieu('TCNKH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'TCNKH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'TCNKH';
        $phieu->tienthanhtoan = $data->tienthanhtoan;
        $phieu->ghichu = $data->ghichu ?? null;
        $phieu->sophieu = Funcs::getSTTPhieu('TCNKH',$nhanvien->chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
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

        $continue = $continue ? $phieu->capNhatKhachHang(false) : false;

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
                    'maphieu' => $phieu->maphieu,
                    'congno' => KhachHang::find($data->doituong->id,['congno'])->congno
                ]
            ];
        }
    }

    public function dieuchinh_congno(Request $request) {
        $data = json_decode($request->phieu);
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);

        $sophieu = Funcs::getSoPhieu('DCCNKH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'DCCNKH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'DCCNKH';
        $phieu->tienthanhtoan = $data->tienthanhtoan - Funcs::getCongNoKhachHang($data->doituong->id);
        $phieu->ghichu = $data->ghichu ?? null;
        $phieu->sophieu = Funcs::getSTTPhieu('DCCNKH',$nhanvien->chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
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

        $continue = $continue ? $phieu->capNhatKhachHang(false) : false;

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
                    'maphieu' => $phieu->maphieu,
                    'congno' => KhachHang::find($data->doituong->id,['congno'])->congno
                ]
            ];
        }
    }
}
