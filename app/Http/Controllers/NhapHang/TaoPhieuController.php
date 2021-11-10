<?php

namespace App\Http\Controllers\NhapHang;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\NhaCungCap;
use App\Models\DanhMuc\NhanVien;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaoPhieuController extends Controller
{
    public function index() {
        $nhacungcaps = NhaCungCap::all(['id','ma','ten','ten as text']);

        return view('quanly.nhaphang.taophieu.index', [
            'nhacungcaps' => $nhacungcaps
        ]);
    }

    public function tim_kiem(Request $request) {
        $q = Funcs::convertToSlug($request->q);

        $options = ['id','ma','ten','donvitinh','nhom','quycach'];

        $models = HangHoa::whereRaw("is_quydoi = 0 and (slug like '%$q%' or ma like '%$q%')")
            ->limit(20)->get($options);

        foreach($models as $model) {
            $model->text = $model->ma.' - '.$model->ten;
        }

        return [
            'results' => $models
        ];
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id','ten']);

        $sophieu = Funcs::getSoPhieu('NH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'NH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'NH';
        $phieu->ghichu = $data->ghichu;
        $phieu->sophieu = Funcs::getSTTPhieu('NH',$nhanvien->chinhanh_id);
        $phieu->status = 0;
        $phieu->tongthanhtien = 0;
        $phieu->tienthanhtoan = 0;
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

        foreach ($data->dshanghoa as $value) {
            if (!$continue) {
                break;
            }
            $hanghoa = HangHoa::find($value->hanghoa->id,['gianhap']);
            $thanhtien = $hanghoa->gianhap * $value->soluong;
            $phieu->tongthanhtien += $thanhtien;
            $phieu->tienthanhtoan += $thanhtien;

            $chitiet = new PhieuChiTiet();
            $chitiet->phieu_id = $phieu->id;
            $chitiet->maphieu = $phieu->maphieu;
            $chitiet->loaiphieu = 'NH';
            $chitiet->hanghoa_id = $value->hanghoa->id;
            $chitiet->hanghoa_ma = $value->hanghoa->ma;
            $chitiet->dongia = $hanghoa->gianhap;
            $chitiet->soluong = $value->soluong;
            $chitiet->thanhtien = $thanhtien;
            $chitiet->is_tangkho = 1;
            $chitiet->sophieu = $phieu->sophieu;
            $chitiet->chinhanh_id = $phieu->chinhanh_id;
            $chitiet->hansudung = $value->hansudung;
            $chitiet->ngay = date('Y-m-d');
            $chitiet->gio = date('H:i:s');

            try {
                $continue = $chitiet->save();
            }
            catch (QueryException $exception) {
                $message = $exception->getMessage();
                $continue = false;
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
            $phieu->update();
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Lưu phiếu thành công.',
                'data' => [
                    'maphieu' => $phieu->maphieu,
                    'thongbaos' => [
                        [
                            'topic' => NhanVien::find('1000000000','remember_token')->remember_token,
                            'title' => 'Phiếu nhập hàng mới',
                            'noidung' => ChiNhanh::withTrashed()->find($nhanvien->chinhanh_id,'ten')->ten."\n".'Người tạo: '
                                .$nhanvien->ten
                        ]
                    ]
                ]
            ];
        }
    }
}
