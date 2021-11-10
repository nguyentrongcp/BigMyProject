<?php

namespace App\Http\Controllers\ChuyenKhoNoiBo;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NhapKhoController extends Controller
{
    public function index() {
        $nhanvien_id = Funcs::getNhanVienIDByToken($_COOKIE['token']);
        $nhanviens = NhanVien::where('id','!=',$nhanvien_id)->get(['id','ma','ten','dienthoai']);
        foreach($nhanviens as $nhanvien) {
            $nhanvien->text = $nhanvien->dienthoai.' - '.$nhanvien->ten;
        }
        return view('quanly.chuyenkho-noibo.nhapkho.index', [
            'nhanviens' => $nhanviens
        ]);
    }

    public function danhsach_phieuxuat(Request $request) {
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $chitiets = PhieuChiTiet::whereIn('phieu_id',Phieu::where([
            'doituong_id' => $chinhanh_id,
            'loaiphieu' => 'XKNB'
        ])->pluck('id'))->where('status','0')->get(['id','maphieu','hanghoa_id','phieu_id','soluong','created_at']);

        foreach($chitiets as $chitiet) {
            $phieu = $chitiet->getPhieu(['doituong_id','nguoiduyet_id','ghichu','chinhanh_id']);
            $chitiet->nhanvien = $phieu->getNhanVienDuyet()->ten;
            $chitiet->hanghoa = $chitiet->getHangHoa();
            $chitiet->hanghoa_ten = $chitiet->hanghoa->ten;
            $chitiet->chinhanh_chuyen = $phieu->getChiNhanh()->ten;
        }

        return $chitiets;
    }

    public function so_phieuxuat(Request $request) {
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $sophieu = PhieuChiTiet::whereIn('phieu_id',Phieu::where([
            'doituong_id' => $chinhanh_id,
            'loaiphieu' => 'XKNB'
        ])->pluck('id'))->where('status',0)->groupBy('phieu_id')->selectRaw('count(*) as sophieu')->get()->count();

        return [
            'succ' => 1,
            'data' => [
                'sophieu' => $sophieu
            ]
        ];
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id']);

        $sophieu = Funcs::getSoPhieu('NKNB');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'NKNB'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'NKNB';
        $phieu->ghichu = $data->ghichu;
        $phieu->sophieu = Funcs::getSTTPhieu('NKNB',$nhanvien->chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
        $phieu->nguoiduyet_id = $data->nhanvien_nhanhang->id;
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

            $chitiet = new PhieuChiTiet();
            $chitiet->phieu_id = $phieu->id;
            $chitiet->maphieu = $phieu->maphieu;
            $chitiet->loaiphieu = 'NKNB';
            $chitiet->hanghoa_id = $value->hanghoa->id;
            $chitiet->hanghoa_ma = $value->hanghoa->ma;
            $chitiet->soluong = $value->soluong;
            $chitiet->is_tangkho = 1;
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

            if ($continue) {
                PhieuChiTiet::where('id',$value->id)->update([
                    'status' => 1
                ]);
            }
        }

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

    public function huy_phieu(Request $request) {
        $chitiets = json_decode($request->chitiets);

        DB::beginTransaction();
        $continue = true;
        foreach($chitiets as $id) {
            $chitiet = PhieuChiTiet::find($id, ['id','status','chinhanh_id','is_tangkho','hanghoa_id']);
            $chitiet->status = -1;
            $chitiet->is_tangkho = 0;
            $continue = !$continue ? false : $chitiet->update();
            $continue = !$continue ? false : $chitiet->capNhatTonKho();
        }

        if (!$continue) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Trả lại hàng thất bại. Vui lòng thử lại lần nữa!',
            ];
        }
        else {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Trả lại hàng thành công.'
            ];
        }
    }
}
