<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\DoiTuong;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\KhoanMuc;
use App\Models\DanhMuc\NhaCungCap;
use App\Models\DanhMuc\NhanVien;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhieuController extends Controller
{
    public function danh_sach(Request $request) {
        $begin = $request->begin ?? '2020-01-01';
        $end = $request->end ?? date('Y-m-d');
        $end = date('Y-m-d',strtotime('+1 days',strtotime($end)));

        switch ($request->loaiphieu) {
            case 'KTH':
            case 'BH':
                $phieus = $this->getBH($begin,$end,$request);
                break;
            case 'THNCC':
            case 'NH':
                $phieus = $this->getNH($request,$begin,$end);
                break;
            case 'XKNB':
                $phieus = $this->getXKNB($request,$begin,$end);
                break;
            case 'NKNB':
                $phieus = $this->getNKNB($request,$begin,$end);
                break;
            case 'DKHH':
                $phieus = $this->getDKHH($request,$begin,$end);
                break;
            case 'KSCN':
                $phieus = $this->getKSCN($request,$begin,$end);
                break;
            default:
                $phieus = $this->getThuChi($request,$begin,$end);
                break;
        }

        return $phieus;
    }

    public function xoa(Request $request) {
        $maphieu = $request->maphieu;

        $phieu = Phieu::where('maphieu',$maphieu)
            ->first(['id','loaiphieu','deleted_at']);
        if ($phieu == null) {
            return [
                'succ' => 0,
                'noti' => 'Phiếu không tồn tại hoặc đã bị xóa!'
            ];
        }

        if (!$phieu->delete()) {
            return [
                'succ' => 0,
                'noti' => 'Xóa phiếu thất bại. Vui lòng thử lại sau!'
            ];
        }

        switch ($phieu->loaiphieu) {
            case 'KTH':
            case 'BH':
                $continue = $this->actionBH($maphieu);
                break;
            case 'THNCC':
            case 'NH':
                $continue = $this->actionNH($maphieu);
                break;
            case 'XKNB':
                $continue = $this->actionXKNB($maphieu);
                break;
            case 'NKNB':
                $continue = $this->actionNKNB($maphieu);
                break;
            case 'DKHH':
                $continue = $this->actionDKHH($maphieu);
                break;
            default:
                $continue = $this->actionThuChi($maphieu);
        }

        if ($continue) {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Xóa phiếu thành công.',
                'data' => [
                    'deleted_at' => $phieu->deleted_at
                ]
            ];
        }
        else {
            DB::rollBack();
            return [
                'succ' => 1,
                'noti' => 'Xóa phiếu thất bại!'
            ];
        }
    }

    public function phuc_hoi(Request $request) {
        $maphieu = $request->maphieu;

        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','loaiphieu','deleted_at']);
        if (!$phieu->trashed()) {
            return [
                'succ' => 0,
                'noti' => 'Phiếu không bị xóa. Phục hồi thất bại!'
            ];
        }

        if (!$phieu->restore()) {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi phiếu thất bại. Vui lòng thử lại sau!'
            ];
        }

        $continue = true;
        switch ($phieu->loaiphieu) {
            case 'KTH':
            case 'BH':
                $continue = $this->actionBH($maphieu,false);
                break;
            case 'THNCC':
            case 'NH':
                $continue = $this->actionNH($maphieu,false);
                break;
            case 'XKNB':
                $continue = $this->actionXKNB($maphieu,false);
                break;
            case 'NKNB':
                $continue = $this->actionNKNB($maphieu,false);
                break;
            case 'DKHH':
                $continue = $this->actionDKHH($maphieu,false);
                break;
            default:
                $continue = $this->actionThuChi($maphieu);
        }

        if ($continue) {
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Phục hồi phiếu thành công.'
            ];
        }
        else {
            DB::rollBack();
            return [
                'succ' => 1,
                'noti' => 'Phục hồi phiếu thất bại!'
            ];
        }
    }

    private function getBH($begin, $end, $request) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $phieus = Phieu::withTrashed()->where([
            'loaiphieu' => $request->loaiphieu,
            'chinhanh_id' => $chinhanh_id
        ])
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get(['id','maphieu','doituong_id','giamgia','phuthu','tongthanhtien','nhanvien_id',
                'tienthanhtoan','sophieu','created_at','deleted_at']);

        $khachhang_ids = [];
        $nhanvien_ids = [];
        foreach ($phieus as $phieu) {
            $khachhang_ids[] = $phieu->doituong_id;
            $nhanvien_ids[] = $phieu->nhanvien_id;
        }
        $khachhangs = KhachHang::withTrashed()->whereIn('id',$khachhang_ids)->pluck('ten','id');
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanvien_ids)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->doituong = $khachhangs[$phieu->doituong_id] ?? 'Chưa rõ';
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getNH($request, $begin, $end) {
        $wheres = ['loaiphieu' => $request->loaiphieu];
        $options = ['id','chinhanh_id','maphieu','doituong_id','nhanvien_id','sophieu','created_at','deleted_at','status'];
        $info = Funcs::getNhanVienByToken($request->cookie('token'),['chinhanh_id','id']);
        $chinhanh_id = $request->chinhanh_id ?? null;
        if ($info->id == '1000000000') {
            $options[] = 'tienthanhtoan';
            if ($chinhanh_id != null) {
                $wheres['chinhanh_id'] = $chinhanh_id;
            }
        }
//        else {
//            $wheres['chinhanh_id'] = $chinhanh_id ?? $info->chinhanh_id;
//        }
        $phieus = Phieu::withTrashed()->where($wheres)
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get($options);

        $nhacungcaps = [];
        $nhanviens = [];
        $chinhanhs = [];
        foreach ($phieus as $phieu) {
            $nhacungcaps[] = $phieu->doituong_id;
            $nhanviens[] = $phieu->nhanvien_id;
            $chinhanhs[] = $phieu->chinhanh_id;
        }
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanviens)->pluck('ten','id');
        $chinhanhs = ChiNhanh::withTrashed()->whereIn('id',$chinhanhs)->pluck('ten','id');
        $nhacungcaps = NhaCungCap::withTrashed()->whereIn('id',$nhacungcaps)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->doituong = $nhacungcaps[$phieu->doituong_id] ?? 'Chưa rõ';
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
            $phieu->chinhanh = $chinhanhs[$phieu->chinhanh_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getXKNB($request, $begin, $end) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $phieus = Phieu::withTrashed()->where([
            'loaiphieu' => $request->loaiphieu,
            'chinhanh_id' => $chinhanh_id
        ])
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get(['id','maphieu','doituong_id','nhanvien_id','ghichu','sophieu','created_at','deleted_at']);

        $nhanviens = [];
        $chinhanhs = [];
        foreach ($phieus as $phieu) {
            $nhanviens[] = $phieu->nhanvien_id;
            $chinhanhs[] = $phieu->doituong_id;
        }
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanviens)->pluck('ten','id');
        $chinhanhs = ChiNhanh::withTrashed()->whereIn('id',$chinhanhs)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->doituong = $chinhanhs[$phieu->doituong_id] ?? 'Chưa rõ';
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getNKNB($request, $begin, $end) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $phieus = Phieu::withTrashed()->where([
            'loaiphieu' => $request->loaiphieu,
            'chinhanh_id' => $chinhanh_id
        ])
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get(['id','maphieu','doituong_id','nhanvien_id','ghichu','sophieu','created_at','deleted_at']);

        $nhanviens = [];

        foreach ($phieus as $phieu) {
            $nhanviens[] = $phieu->nhanvien_id;
            $phieuxuat = $phieu->getPhieuXuatKho();
            $phieu->doituong = $phieuxuat->getChiNhanh();
            $phieu->doituong = $phieu->doituong == null ? 'Chưa rõ' : $phieu->doituong->ten;
            unset($phieu->doituong_id);
            $phieu->phieuxuat = $phieuxuat->maphieu;
        }
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanviens)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getDKHH($request, $begin, $end) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $phieus = Phieu::withTrashed()->where([
            'loaiphieu' => 'DKHH',
            'chinhanh_id' => $chinhanh_id
        ])
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get(['id','maphieu','nhanvien_id','ghichu','sophieu','created_at','deleted_at']);

        $nhanviens = [];

        foreach ($phieus as $phieu) {
            $nhanviens[] = $phieu->nhanvien_id;
        }
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanviens)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getThuChi($request, $begin, $end) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $whereLoaiPhieu = $request->loaiphieu == 'TC' ?
            "(loaiphieu = 'PT' or loaiphieu = 'PC')" : "loaiphieu = '$request->loaiphieu'";
        $phieus = Phieu::withTrashed()
            ->whereRaw("$whereLoaiPhieu and chinhanh_id='$chinhanh_id'
            and (created_at between '$begin' and '$end')")
            ->orderByDesc('created_at')
            ->get(['id','maphieu','doituong_id','khoanmuc_id','noidung','tienthanhtoan','loaiphieu','nhanvien_id','ghichu','sophieu','created_at','deleted_at']);

        $doituongs = [];
        $nhanviens = [];
        $khoanmucs = [];
        foreach ($phieus as $phieu) {
            $doituongs[] = $phieu->doituong_id;
            $nhanviens[] = $phieu->nhanvien_id;
            $khoanmucs[] = $phieu->khoanmuc_id;
        }
        $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanviens)->pluck('ten','id');
        $doituongs = DoiTuong::withTrashed()->whereIn('id',$doituongs)->pluck('ten','id');
        $khoanmucs = KhoanMuc::withTrashed()->whereIn('id',$khoanmucs)->pluck('ten','id');
        $khachhangs = KhachHang::withTrashed()->whereIn('id',$doituongs)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->doituong = ($phieu->loaiphieu == 'TCNKH' || $phieu->loaiphieu == 'DCCNKH') ? ($khachhangs[$phieu->doituong_id] ?? 'Chưa rõ') : ($doituongs[$phieu->doituong_id] ?? 'Chưa rõ');
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
            $phieu->khoanmuc = $khoanmucs[$phieu->khoanmuc_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function getKSCN($request, $begin, $end) {
        $chinhanh_id = $request->chinhanh_id ?? Funcs::getChiNhanhByToken($request->cookie('token'));
        $phieus = Phieu::withTrashed()->where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id
        ])
            ->whereBetween('created_at',[$begin,$end])->orderByDesc('created_at')
            ->get(['id','maphieu','nhanvien_id','tongthanhtien','phuthu','giamgia','tienthanhtoan','ghichu','sophieu','created_at','deleted_at']);

        $nhanviens = [];

        foreach ($phieus as $phieu) {
            $nhanviens[] = $phieu->nhanvien_id;
        }
        $nhanviens = NhanVien::whereIn('id',$nhanviens)->pluck('ten','id');
        foreach ($phieus as $phieu) {
            $phieu->nhanvien = $nhanviens[$phieu->nhanvien_id] ?? 'Chưa rõ';
        }

        return $phieus;
    }

    private function actionBH($maphieu, $is_xoa = true) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','chinhanh_id','loaiphieu','deleted_at','doituong_id']);

        $chitiets = $phieu->getChiTiets('BH');

        $continue = true;
        foreach ($chitiets as $chitiet) {
            if (!$continue) {
                break;
            }
            $continue = $is_xoa ? $chitiet->delete() : $chitiet->restore();

            $continue = $continue == false ? $continue : $chitiet->capNhatTonKho();
        }

        if ($phieu->loaiphieu == 'BH') {
            $continue = $continue == false ? $continue : $phieu->capNhatKhachHang();
        }

        return $continue;
    }

    private function actionNH($maphieu, $is_xoa = true) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','chinhanh_id','loaiphieu','deleted_at','doituong_id','status']);

        if ($phieu->status == 0) {
            return false;
        }

        $chitiets = $phieu->getChiTiets('NH');

        $continue = true;
        foreach ($chitiets as $chitiet) {
            if (!$continue) {
                break;
            }
            $continue = $is_xoa ? $chitiet->delete() : $chitiet->restore();

            $continue = $continue == false ? $continue : $chitiet->capNhatTonKho();
        }

        return $continue == false ? $continue : $phieu->capNhatNhaCungCap();
    }

    private function actionXKNB($maphieu, $is_xoa = true) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','chinhanh_id','loaiphieu','deleted_at']);

        $chitiets = $phieu->getChiTiets('XKNB');

        $continue = true;
        foreach ($chitiets as $chitiet) {
            if (!$continue) {
                break;
            }
            $continue = $is_xoa ? $chitiet->delete() : $chitiet->restore();

            $continue = $continue == false ? $continue : $chitiet->capNhatTonKho();
        }

        return $continue;
    }

    private function actionNKNB($maphieu, $is_xoa = true) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','chinhanh_id','doituong_id','loaiphieu','deleted_at']);

        $chitiets = $phieu->getChiTiets('NKNB');

        $continue = true;
        foreach ($chitiets as $chitiet) {
            if (!$continue) {
                break;
            }
            $continue = $is_xoa ? $chitiet->delete() : $chitiet->restore();
            $continue = $continue == false ? $continue : $chitiet->capNhatTonKho();
            $_chitiet = PhieuChiTiet::where([
                'phieu_id' => $phieu->doituong_id,
                'hanghoa_id' => $chitiet->hanghoa_id
            ])->first(['id','status','chinhanh_id','hanghoa_id']);
            $_chitiet->status = $is_xoa ? 0 : 1;
            $_chitiet->update();
            $continue = $continue == false ? $continue : $_chitiet->capNhatTonKho();
        }

        return $continue;
    }

    private function actionDKHH($maphieu, $is_xoa = true) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','deleted_at']);

        $chitiets = $phieu->getChiTiets('DKHH');

        $continue = true;
        foreach ($chitiets as $chitiet) {
            if (!$continue) {
                break;
            }
            $continue = $is_xoa ? $chitiet->delete() : $chitiet->restore();
            $continue = $continue == false ? $continue : $chitiet->capNhatTonKho();
        }

        return $continue;
    }

    private function actionThuChi($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu',$maphieu)
            ->first(['id','doituong_id','chinhanh_id','loaiphieu','deleted_at','tienthanhtoan']);

        if (in_array($phieu->loaiphieu,['TCNKH','DCCNKH']) !== false) {
            return $phieu->capNhatKhachHang();
        }
        elseif (in_array($phieu->loaiphieu,['CCNNCC','DCCNNCC']) !== false) {
            return $phieu->capNhatNhaCungCap();
        }
        else {
            return true;
        }
    }
}
