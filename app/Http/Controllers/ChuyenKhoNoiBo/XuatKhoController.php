<?php

namespace App\Http\Controllers\ChuyenKhoNoiBo;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\QuyDoi;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XuatKhoController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['chinhanh_id','id']);
        $chinhanhs = ChiNhanh::where('id','!=',$info->chinhanh_id)->orderBy('loai')->get(['id','ten','ten as text']);
        $nhanviens = NhanVien::where('id','!=',$info->id)->get(['id','ma','ten','dienthoai']);
        return view('quanly.chuyenkho-noibo.xuatkho.index', [
            'chinhanhs' => $chinhanhs,
            'nhanviens' => $nhanviens
        ]);
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id']);

        DB::beginTransaction();

        $sophieu = Funcs::getSoPhieu('XKNB');

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'XKNB'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'XKNB';
        $phieu->ghichu = $data->ghichu;
        $phieu->sophieu = Funcs::getSTTPhieu('XKNB',$nhanvien->chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
        $phieu->nguoiduyet_id = $data->nhanvien_soanhang->id;
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
            $chitiet->loaiphieu = 'XKNB';
            $chitiet->hanghoa_id = $value->hanghoa->id;
            $chitiet->hanghoa_ma = $value->hanghoa->ma;
            $chitiet->soluong = $value->soluong;
            $chitiet->is_tangkho = -1;
            $chitiet->chinhanh_id = $phieu->chinhanh_id;
            $chitiet->sophieu = $phieu->sophieu;
            $chitiet->status = 0;
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
