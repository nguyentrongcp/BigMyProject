<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class XemPhieuController extends Controller
{
    public function xem_phieu(Request $request, $maphieu) {
        if (strpos($maphieu, 'REVIEW') !== false) {
            if (!Storage::exists('public/phieutam/'.$maphieu.'.txt')) {
                abort(404);
            }

            $phieu = Storage::get('public/phieutam/'.$maphieu.'.txt');
            Storage::delete('public/phieutam/'.$maphieu.'.txt');
            $phieu = json_decode(str_replace('\n','',$phieu));
            switch ($phieu->loaiphieu) {
                case 'BH':
                    $view = 'quanly.xemphieu.ban-hang'; break;
                case 'KTH':
                    $view = 'quanly.xemphieu.khach-tra-hang'; break;
                case 'NH':
                    $view = 'quanly.xemphieu.nhap-hang'; break;
                case 'XKNB':
                    $view = 'quanly.xemphieu.xuatkho-noibo'; break;
                case 'NKNB':
                    $view = 'quanly.xemphieu.nhapkho-noibo'; break;
                default:
                    $view = 'quanly.xemphieu.thu-chi'; break;
            }

            return view($view,[
                'phieu' => $phieu, 'controls' => (object) ['printable' => false, 'deletable' => false], 'auto_print' => false
            ]);
        }
        else {
            $controls = (object) [
                'printable' => !isset($request->printable) || $request->printable == 0,
                'deletable' => isset($request->deletable) || $request->deletable == 1
            ];
            $auto_print = isset($request->autoprint) || $request->autoprint == 1;
            $loaiphieu = Phieu::withTrashed()->where('maphieu',$maphieu)->first('loaiphieu');
            if ($loaiphieu == null) {
                abort(404);
            }
            else {
                $loaiphieu = $loaiphieu->loaiphieu;
            }
            switch ($loaiphieu) {
                case 'BH':
                    $phieu = $this->getBH($maphieu);
                    $view = 'quanly.xemphieu.ban-hang';
                    break;
                case 'KTH':
                    $phieu = $this->getKTH($maphieu);
                    $view = 'quanly.xemphieu.khach-tra-hang';
                    break;
                case 'NH':
                    $phieu = $this->getNH($maphieu);
                    $view = 'quanly.xemphieu.nhap-hang';
                    break;
                case 'XKNB':
                    $phieu = $this->getXKNB($maphieu);
                    $view = 'quanly.xemphieu.xuatkho-noibo';
                    break;
                case 'NKNB':
                    $phieu = $this->getNKNB($maphieu);
                    $view = 'quanly.xemphieu.nhapkho-noibo';
                    break;
                case 'DKHH':
                    $phieu = $this->getDKHH($maphieu);
                    $view = 'quanly.xemphieu.dauky-hanghoa';
                    break;
                case 'KSCN':
                    $phieu = $this->getKSCN($maphieu);
                    $view = 'quanly.xemphieu.ketso-cuoingay';
                    break;
                default:
                    $phieu = $this->getThuChi($maphieu);
                    $view = 'quanly.xemphieu.thu-chi';
                    break;
            }

            if ($controls->deletable) {
                if (!Funcs::isPhanQuyenByToken('role.phieu.action',$_COOKIE['token'])) {
                    $nhanvien = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
                    if ($nhanvien->id != $phieu->nhanvien_id && $phieu->chinhanh_id != $nhanvien->chinhanh_id) {
                        $controls->deletable = false;
                    }
                    elseif ($phieu->loaiphieu == 'NH') {
                        if ($phieu->status == 1) {
                            $controls->deletable = false;
                        }
                    }
                }
                elseif (in_array($phieu->loaiphieu,['BH','KTH','TCNKH','CCNNCC','PT','PC','KSCN']) !== false && $phieu->ngay < date('Y-m-d')) {
                    $controls->deletable = false;
                }
            }
            return view($view, [
                'phieu' => $phieu, 'controls' => $controls, 'auto_print' => $auto_print
            ]);
        }
    }

    public function tao_phieu(Request $request, $loaiphieu) {
        switch ($loaiphieu) {
            case 'NH':
                $phieu = $this->taoNH($request);
                break;
            case 'BH':
                $phieu = $this->taoBH($request);
                break;
            case 'KTH':
                $phieu = $this->taoKTH($request);
                break;
            case 'XKNB':
                $phieu = $this->taoXKNB($request);
                break;
            case 'NKNB':
                $phieu = $this->taoNKNB($request);
                break;
            default:
                $phieu = $this->taoThuChi($request,$loaiphieu);
                break;
        }

        $phieu->deleted_at = null;
        $phieu->loaiphieu = $loaiphieu;
        if (!isset($phieu->chinhanh)) {
            $phieu->chinhanh = ChiNhanh::withTrashed()
                ->find(Funcs::getChiNhanhByToken($request->cookie('token')), ['id','ten','diachi','dienthoai','dienthoai2']);
        }
        if (!isset($phieu->sophieu)) {
            $phieu->sophieu = Funcs::getSTTPhieu($loaiphieu,$phieu->chinhanh->id);
        }
        if (!isset($phieu->nhanvien)) {
            $phieu->nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['ten']);
        }

        if (Storage::put('public/phieutam/'.$phieu->maphieu.'.txt', json_encode($phieu))) {
            return [
                'succ' => 1,
                'data' => [
                    'maphieu' => $phieu->maphieu
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'data' => [
                    'maphieu' => $phieu->maphieu
                ]
            ];
        }
    }

    private function taoBH($request) {
        $phieu = json_decode($request->phieu);
        $time = time();

        $phieu->doituong = KhachHang::withTrashed()->find($phieu->doituong_id,['id','ma','ten','dienthoai','diachi','congno']);
        unset($phieu->doituong_id);

        $phieu->created_at = date('Y-m-d H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;

        return $phieu;
    }

    private function taoKTH($request) {
        $phieu = json_decode($request->phieu);
        $time = time();

        $phieu->created_at = date('Y-m-d H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;

        return $phieu;
    }

    private function taoNH($request) {
        $phieu = json_decode($request->phieu);
        $time = time();

        $phieu->created_at = $phieu->created_at ?? date('d-m-Y H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;
        $phieu->status = 1;

        return $phieu;
    }

    private function taoXKNB($request) {
        $phieu = json_decode($request->phieu);
        $time = time();

        $phieu->created_at = $phieu->created_at ?? date('d-m-Y H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;
        $phieu->status = 1;

        return $phieu;
    }

    private function taoNKNB($request) {
        $phieu = json_decode($request->phieu);
        $time = time();

        $phieu->created_at = $phieu->created_at ?? date('d-m-Y H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;
        $phieu->status = 1;

        return $phieu;
    }

    private function taoThuChi($request, $loaiphieu) {
        $phieu = json_decode($request->phieu);
        $time = time();
        $phieu->tenphieu = Funcs::getTenPhieu($loaiphieu);

        $phieu->created_at = $phieu->created_at ?? date('d-m-Y H:i:s');
        $phieu->maphieu = 'REVIEW'.$time;
        $phieu->status = 1;

        return $phieu;
    }

    private function getBH($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getKhachHang();
        unset($phieu->doituong_id);
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien_tuvan = $phieu->getNhanVienTuVan();
        unset($phieu->nhanvien_tuvan_id);
        $phieu->nhanvien = $phieu->getNhanVien();

        $chitiets = $phieu->getChiTiets('BH');
        foreach ($chitiets as $chitiet) {
            if ($chitiet->id_quydoi != null) {
                $chitiet->hanghoa = $chitiet->getHangHoaQuyDoi();
                if ($chitiet->hanghoa == null) {
                    $chitiet->soluong = $chitiet->soluong / $chitiet->quydoi;
                    $chitiet->dongia = $chitiet->dongia * $chitiet->quydoi;
                    $chitiet->giamgia = $chitiet->giamgia * $chitiet->quydoi;
                }
            }
            $chitiet->hanghoa = $chitiet->hanghoa ?? $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getKTH($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getKhachHang();
        unset($phieu->doituong_id);
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();

        $chitiets = $phieu->getChiTiets('KTH');
        foreach ($chitiets as $chitiet) {
            if ($chitiet->id_quydoi != null) {
                $chitiet->hanghoa = $chitiet->getHangHoaQuyDoi();
                if ($chitiet->hanghoa == null) {
                    $chitiet->soluong = $chitiet->soluong / $chitiet->quydoi;
                    $chitiet->dongia = $chitiet->dongia * $chitiet->quydoi;
                    $chitiet->giamgia = $chitiet->giamgia * $chitiet->quydoi;
                }
            }
            $chitiet->hanghoa = $chitiet->hanghoa ?? $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getNH($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getNhaCungCap();
        unset($phieu->doituong_id);
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();

        $chitiets = $phieu->getChiTiets('NH');
        foreach ($chitiets as $chitiet) {
            $chitiet->hanghoa = $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getXKNB($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getChiNhanhNhan();
        unset($phieu->doituong_id);
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();
        $phieu->nhanvien_soanhang = $phieu->getNhanVienDuyet();
        unset($phieu->nguoiduyet_id);

        $chitiets = $phieu->getChiTiets('XKNB',['status']);
        foreach ($chitiets as $chitiet) {
            $chitiet->hanghoa = $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getNKNB($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getChiNhanhXuat();
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();
        $phieu->nhanvien_nhanhang = $phieu->getNhanVienDuyet();
        unset($phieu->nguoiduyet_id);
        $phieu->phieuxuat = $phieu->getPhieuXuatKho()->maphieu;
        unset($phieu->doituong_id);

        $chitiets = $phieu->getChiTiets('XKNB',['status']);
        foreach ($chitiets as $chitiet) {
            $chitiet->hanghoa = $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getDKHH($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->nhanvien = $phieu->getNhanVien();

        $chitiets = $phieu->getChiTiets('DKHH');
        foreach ($chitiets as $chitiet) {
            $chitiet->hanghoa = $chitiet->getHangHoa();
        }
        $phieu->dshanghoa = $chitiets;

        return $phieu;
    }

    private function getThuChi($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->doituong = $phieu->getDoiTuong($phieu->loaiphieu);
        unset($phieu->doituong_id);
        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();
        $phieu->tenphieu = Funcs::getTenPhieu($phieu->loaiphieu);
        if ($phieu->loaiphieu == 'PT' || $phieu->loaiphieu == 'PC') {
            $phieu->khoanmuc = $phieu->getKhoanMuc();
            unset($phieu->khoanmuc_id);
        }

        return $phieu;
    }

    private function getKSCN($maphieu) {
        $phieu = Phieu::withTrashed()->where('maphieu', $maphieu)->first();

        if ($phieu == null) {
            abort(404);
        }

        $phieu->chinhanh = $phieu->getChiNhanh();
        $phieu->nhanvien = $phieu->getNhanVien();

        return $phieu;
    }
}
