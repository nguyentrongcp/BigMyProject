<?php

namespace App\Http\Controllers\HangHoa;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\QuyDoi;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhatSinhTonController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if (Funcs::isPhanQuyenByToken('role.chi-nhanh.tat-ca',$_COOKIE['token'])) {
            $chinhanhs = ChiNhanh::whereIn('loai',['cuahang','khohanghong'])->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('quanly.hanghoa.phatsinhton.index', [
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function danh_sach(Request $request) {
        $begin = $request->begin ?? '2020-01-01';
        $end = $request->end ?? date('Y-m-d');
        $end = date('Y-m-d',strtotime('+1 days',strtotime($end)));
        $chinhanh_id = $request->chinhanh_id;
        $hanghoa_id = $request->hanghoa_id;

        $dauky = PhieuChiTiet::selectRaw('(soluong * is_tangkho)/quydoi as soluong')
            ->where('created_at','<',$begin)
            ->where([
                'hanghoa_id' => $hanghoa_id
            ])
            ->whereIn('phieu_id',Phieu::where([
                'chinhanh_id' => $chinhanh_id,
                'status' => 1
            ])->pluck('id'))
            ->get()->sum('soluong');

        $dauky = round($dauky,2);

        $results = PhieuChiTiet::where('hanghoa_id',$hanghoa_id)->where('is_tangkho','!=',0)
            ->whereIn('phieu_id',Phieu::where([
                'chinhanh_id' => $chinhanh_id,
                'status' => 1
            ])->pluck('id'))
            ->whereBetween('ngay',[$begin,$end])->orderBy('created_at')
            ->get(['id','loaiphieu','created_at','maphieu','soluong','is_tangkho','quydoi']);

        $tangtk = 0;
        $giamtk = 0;
        $cuoiky = $dauky;
        foreach($results as $result) {
            $result->soluong = ((float) $result->soluong * $result->is_tangkho / $result->quydoi);
            if ($result->soluong > 0) {
                $tangtk += $result->soluong;
            }
            else {
                $giamtk += $result->soluong;
            }
            $cuoiky += $result->soluong;
            $cuoiky = round($cuoiky,2);
            $result->tonkho = $cuoiky;
            $result->soluong = $result->soluong > 0 ? '+'.$result->soluong : $result->soluong;
            $result->tenphieu = 'PHIẾU '.Funcs::getTenPhieu($result->loaiphieu);
        }
        $results = $results->toArray();
        array_unshift($results,(object) [
            'created_at' => 'ĐẦU KỲ',
            'tonkho' => $dauky
        ]);
        $tonkho = Funcs::getTonKho($hanghoa_id,$chinhanh_id);

        return [
            'results' => $results,
            'thongtin' => [
                'dauky' => $dauky,
                'tangtk' => $tangtk,
                'giamtk' => $giamtk,
                'cuoiky' => $cuoiky,
                'tonkho' => $tonkho
            ]
        ];
    }

    public function dau_ky(Request $request) {
        $hanghoa_id = $request->hanghoa_id;
        $hanghoa_ma = $request->hanghoa_ma;
        $soluong = $request->soluong;
        $ghichu = $request->ghichu ?? null;

        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id']);
        $chinhanh_id = $request->chinhanh_id ?? $nhanvien->chinhanh_id;

        $sophieu = Funcs::getSoPhieu('DKHH');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->maphieu = 'DKHH'.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = 'DKHH';
        $phieu->ghichu = $ghichu ?? null;
        $phieu->sophieu = Funcs::getSTTPhieu('DKHH',$chinhanh_id);
        $phieu->nhanvien_id = $nhanvien->id;
        $phieu->chinhanh_id = $chinhanh_id;
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

        if ($continue) {
            $chitiet = new PhieuChiTiet();
            $chitiet->phieu_id = $phieu->id;
            $chitiet->maphieu = $phieu->maphieu;
            $chitiet->loaiphieu = 'DKHH';
            $chitiet->hanghoa_id = $hanghoa_id;
            $chitiet->hanghoa_ma = $hanghoa_ma;
            $chitiet->soluong = $soluong;
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
