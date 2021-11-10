<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\CayTrong;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\QuyDoi;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhachTraHangController extends Controller
{
    public function index() {
        $caytrongs = CayTrong::all('ten as text');
        return view('quanly.khachtrahang.index', [
            'caytrongs' => $caytrongs
        ]);
    }

    public function lichsu_muahang(Request $request) {
        $khachhang_id = $request->khachhang_id;
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $date = date('Y-m-d',strtotime('-3 months',time()));

        $chitiets = PhieuChiTiet::whereIn('phieu_id',Phieu::where([
            'loaiphieu' => 'BH',
            'chinhanh_id' => $chinhanh_id,
            'doituong_id' => $khachhang_id
        ])->where('created_at', '>=', $date)->pluck('id'))->orderByDesc('created_at')
            ->get(['id','maphieu','hanghoa_id','phieu_id','dongia','soluong','giamgia',
                'thanhtien','quydoi','id_quydoi','soluong_trahang','created_at']);

        $hanghoas = [];
        $phieus = [];
        foreach($chitiets as $chitiet) {
            $phieus[] = $chitiet->phieu_id;
            $hanghoas[] = $chitiet->hanghoa_id;
        }
        $hanghoas = HangHoa::whereIn('id',$hanghoas)->get(['id','ma','ten','donvitinh']);
        $phieus = Phieu::whereIn('id',$phieus)->get(['id','nhanvien_tuvan_id','giamgia','phuthu','tienthanhtoan']);
        $nhanviens = [];
        foreach($phieus as $key => $phieu) {
            $phieus[$phieu->id] = $phieu;
            unset($phieus[$key]);
            $nhanviens[] = $phieu->nhanvien_tuvan_id;
        }
        foreach($hanghoas as $key => $hanghoa) {
            $hanghoas[$hanghoa->id] = $hanghoa;
            unset($hanghoas[$key]);
        }
        $nhanviens = NhanVien::whereIn('id',$nhanviens)->pluck('ten','id');

        foreach($chitiets as $chitiet) {
            $phieu = $phieus[$chitiet->phieu_id];
            $chitiet->nhanvien = $nhanviens[$phieu->nhanvien_tuvan_id] ?? 'Chưa rõ';
            $hanghoa = null;
            if ($chitiet->id_quydoi != null) {
                $hanghoa = $chitiet->getHangHoaQuyDoi();
            }
            $chitiet->hanghoa = $hanghoa ?? $hanghoas[$chitiet->hanghoa_id];
            $chitiet->hanghoa_ten = $chitiet->hanghoa->ten;
            $chitiet->__giamgia = $phieu->giamgia;
            $chitiet->__phuthu = $phieu->phuthu;
            $chitiet->tienthanhtoan = $phieu->tienthanhtoan;
        }

        return $chitiets;
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id']);

        $sophieu = Funcs::getSoPhieu('KTH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = 'KTH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'KTH';
        $phieu->tongthanhtien = $data->tongthanhtien;
        $phieu->phuthu = $data->phuthu;
        $phieu->giamgia = $data->giamgia;
        $phieu->tienthanhtoan = $data->tienthanhtoan;
        $phieu->ghichu = $data->ghichu;
        $phieu->sophieu = Funcs::getSTTPhieu('KTH',$nhanvien->chinhanh_id);
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
            $chitiet->loaiphieu = 'KTH';
            $chitiet->hanghoa_id = $hanghoa_id;
            $chitiet->hanghoa_ma = $hanghoa_ma;
            $chitiet->dongia = $value->dongia;
            $chitiet->soluong = $value->soluong;
            $chitiet->giamgia = $value->giamgia;
            $chitiet->thanhtien = $value->thanhtien;
            $chitiet->id_quydoi = $id_quydoi;
            $chitiet->quydoi = $quydoi ?? 1;
            $chitiet->is_tangkho = 1;
            $chitiet->chinhanh_id = $phieu->chinhanh_id;
            $chitiet->sophieu = $phieu->sophieu;
            $chitiet->ngay = date('Y-m-d');
            $chitiet->gio = date('H:i:s');
            $chitiet->doituong_id = $value->id;

            try {
                $continue = $chitiet->save();
            }
            catch (QueryException $exception) {
                $message = $exception->getMessage();
                $continue = false;
            }

            $continue = $continue ? $chitiet->capNhatTonKho() : false;
            if ($continue) {
                PhieuChiTiet::where('id',$value->id)->increment('soluong_trahang',$value->soluong);
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
}
