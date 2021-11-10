<?php

namespace App\Http\Controllers\NhapHang;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\HangHoa;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DanhSachController extends Controller
{
    public function index() {
        return view('quanly.nhaphang.danhsach.index');
    }

    public function danh_sach() {
        $phieus = Phieu::where([
            'loaiphieu' => 'NH',
            'status' => 0
        ])->get(['id','maphieu','nhanvien_id','chinhanh_id','doituong_id','ghichu','created_at']);

        foreach($phieus as $phieu) {
            $phieu->chinhanh = $phieu->getChiNhanh();
            $phieu->doituong = $phieu->getNhaCungCap(['dienthoai','dienthoai2','diachi']);
            $phieu->nhanvien = $phieu->getNhanVien();
            unset($phieu->chinhanh_id);
            unset($phieu->doituong_id);
            unset($phieu->nhanvien_id);
            $chitiets = $phieu->getChiTiets('NH',['dongia','thanhtien']);
            foreach($chitiets as $chitiet) {
                $chitiet->hanghoa = $chitiet->getHangHoa();
                unset($chitiet->hanghoa_id);
            }
            $phieu->chitiets = $chitiets;
        }

        return $phieus;
    }

    public function duyet_phieu(Request $request) {
        $data = json_decode($request->phieu);

        $phieu = Phieu::find($data->id,
            ['id','giamgia','doituong_id','phuthu','tongthanhtien','tienthanhtoan','status','nguoiduyet_id']);

        if ($phieu == null) {
            return [
                'succ' => 0,
                'noti' => 'Phiếu không tồn tại hoặc đã bị xóa!'
            ];
        }

        $phieu->giamgia = $data->giamgia;
        $phieu->phuthu = $data->phuthu;
        $phieu->tongthanhtien = $data->tongthanhtien;
        $phieu->tienthanhtoan = $data->tienthanhtoan;
        $phieu->status = 1;
        $phieu->nguoiduyet_id = Funcs::getNhanVienIDByToken($request->cookie('token'));

        DB::beginTransaction();

        $continue = $phieu->update();
        foreach($data->dshanghoa as $value) {
            $chitiet = PhieuChiTiet::find($value->id,['id','dongia','thanhtien','hanghoa_id','chinhanh_id']);
            $continue = $chitiet != null;
            if (!$continue) {
                break;
            }
            $chitiet->dongia = $value->dongia;
            $chitiet->thanhtien = $value->thanhtien;
            $continue = $chitiet->update();

            $chitiet->capNhatTonKho();
            $hanghoa = HangHoa::find($chitiet->hanghoa_id,['id','gianhap']);
            if ($hanghoa == null) {
                DB::rollBack();
                return [
                    'succ' => 0,
                    'noti' => 'Có hàng hóa đã bị xóa. Phiếu không thể duyệt. Vui lòng hủy phiếu!'
                ];
            }
            $hanghoa->gianhap = $chitiet->dongia;
            $continue = !$continue ? false : $hanghoa->update();
            if (!$continue) {
                break;
            }
        }

        $continue = $continue ? $phieu->capNhatNhaCungCap() : false;

        if (!$continue) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Duyệt phiếu thất bại. Vui lòng thử lại sau!',
            ];
        }
        else {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Duyệt phiếu thành công.',
                'data' => [
                    'maphieu' => $data->maphieu
                ]
            ];
        }
    }

    public function so_phieunhap() {
        return [
            'succ' => 1,
            'data' => [
                'sophieu' => Phieu::where([
                    'loaiphieu' => 'NH',
                    'status' => 0
                ])->count()
            ]
        ];
    }

    public function huy_phieu(Request $request) {
        $maphieu = $request->maphieu;
        $phieu = Phieu::where('maphieu',$maphieu)->first(['id']);
        if ($phieu == null) {
            return [
                'succ' => 0,
                'noti' => 'Phiếu không tồn tại hoặc đã bị xóa!'
            ];
        }
        $chitiets = $phieu->getChiTiets('NH');

        DB::beginTransaction();

        $continue = $phieu->forceDelete();
        foreach($chitiets as $chitiet) {
            $continue = $continue ? $chitiet->forceDelete() : false;
        }

        if (!$continue) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Hủy phiếu thất bại. Vui lòng thử lại sau!',
            ];
        }
        else {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Hủy phiếu thành công.'
            ];
        }
    }
}
