<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\DoiTuong;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhoanMuc;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuChiController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if ($info->id == '1000000000') {
            $chinhanhs = ChiNhanh::where('loai','cuahang')->orWhere('id',$info->chinhanh_id)->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }
        $khoanmucs = KhoanMuc::all(['id','ten as text','is_khoanthu']);
        $doituongs = DoiTuong::all(['id','ten','dienthoai','diachi','slug']);

        return view('quanly.thuchi.index', [
            'chinhanhs' => $chinhanhs,
            'khoanmucs' => $khoanmucs,
            'doituongs' => $doituongs
        ]);
    }

    public function mobile_index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if ($info->id == '1000000000') {
            $chinhanhs = ChiNhanh::where('loai','cuahang')->orWhere('id',$info->chinhanh_id)->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('mobile.thuchi.index', [
            'chinhanhs' => $chinhanhs,
        ]);
    }

    public function luu_phieu(Request $request) {
        $data = json_decode($request->phieu);
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);

        if (Funcs::isKetSo($nhanvien->chinhanh_id)) {
            return [
                'succ' => 0,
                'noti' => 'Bạn đã kết sổ ngày hôm nay. Không thể lập thêm phiếu!'
            ];
        }

        $sophieu = Funcs::getSoPhieu($data->loaiphieu);

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->doituong_id = $data->doituong->id;
        $phieu->maphieu = $data->loaiphieu.date('Ymd').'-'.$sophieu;
        $phieu->loaiphieu = $data->loaiphieu;
        $phieu->khoanmuc_id = $data->khoanmuc_id;
        $phieu->tienthanhtoan = $data->tienthanhtoan;
        $phieu->noidung = $data->noidung;
        $phieu->ghichu = $data->ghichu ?? null;
        $phieu->sophieu = Funcs::getSTTPhieu($data->loaiphieu,$nhanvien->chinhanh_id);
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
                ]
            ];
        }
    }

    public function tra_cuu(Request $request) {
        $ngay = $request->ngay ?? null;
        $chinhanh_id = $request->chinhanh_id ?? null;
        if ($ngay == null || $chinhanh_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $loaiphieus = ['BH','KTH','PT','PC','TCNKH','CCNNCC'];
        $dsphieus = Phieu::where([
            'ngay' => $ngay,
            'chinhanh_id' => $chinhanh_id
        ])->whereIn('loaiphieu',$loaiphieus)
            ->get(['id','maphieu','loaiphieu','khoanmuc_id','doituong_id','tienthanhtoan',
                'noidung','ghichu','tienthua','created_at','sophieu','nhanvien_id']);
        $data = [];
        foreach($dsphieus as $phieu) {
            if ($phieu->loaiphieu == 'BH') {
                $phieu->_loaiphieu = $phieu->tienthua < 0 ? 'BHN' : 'BHM';
            }
            elseif ($phieu->loaiphieu == 'PT' || $phieu->loaiphieu == 'PC') {
                $phieu->_loaiphieu = $phieu->khoanmuc_id;
            }
            else {
                $phieu->_loaiphieu = $phieu->loaiphieu;
            }
            if (!isset($data[$phieu->_loaiphieu])) {
                $data[$phieu->_loaiphieu] = [];
            }
            $data[$phieu->_loaiphieu][] = $phieu;
        }

        $dauky = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id
        ])->where('ngay','<',$ngay)->orderByDesc('created_at')->limit(1)->get('tienthanhtoan')->sum('tienthanhtoan');
        $tongcuoi = $dauky;
        $results = [];
        $_tongthu = 0;
        $_tongchi = 0;
        foreach($data as $key => $phieus) {
            $tongthu = 0;
            $tongchi = 0;
            $congno = 0;
            $danhsach = [];
            $tenphieu = (int) $key > 0 ? $phieus[0]->getKhoanMuc() : Funcs::getTenPhieu($key);
            foreach($phieus as $phieu) {
                if ((int) $key == 0) {
                    if ($key == 'TCNKH' || $key == 'BHM') {
                        $tongthu += $phieu->tienthanhtoan;
                    }
                    elseif ($key == 'BHN') {
                        $tongthu += $phieu->tienthanhtoan + $phieu->tienthua;
                        $congno -= $phieu->tienthua;
                    }
                    else {
                        $tongchi += $phieu->tienthanhtoan;
                    }
                }
                else {
                    if ($phieu->loaiphieu == 'PT') {
                        $tongthu += $phieu->tienthanhtoan;
                    }
                    else {
                        $tongchi += $phieu->tienthanhtoan;
                    }
                }
                $phieu->doituong = $phieu->getDoiTuong($phieu->loaiphieu)->ten;
                $phieu->nhanvien = $phieu->getNhanVien()->ten;
                $danhsach[] = $phieu;
            }
            $tongcuoi = $tongcuoi + $tongthu - $tongchi;
            if ($key == 'BHM' || $key == 'BHN') {
//                array_unshift($results, [
//                    'tenphieu' => $tenphieu,
//                    'loaiphieu' => $key,
//                    'congno' => $congno,
//                    'tongthu' => $tongthu,
//                    'tongchi' => $tongchi,
//                    'tongcuoi' => $tongcuoi,
//                    'dsphieu' => $danhsach
//                ]);
                $results[] = [
                    'tenphieu' => $tenphieu,
                    'loaiphieu' => $key,
                    'congno' => $congno,
                    'tongthu' => $tongthu,
                    'tongchi' => $tongchi,
                    'tongcuoi' => $tongcuoi,
                    'dsphieu' => $danhsach
                ];
            }
            else {
                $results[] = [
                    'tenphieu' => $tenphieu,
                    'loaiphieu' => $key,
                    'congno' => $congno,
                    'tongthu' => $tongthu,
                    'tongchi' => $tongchi,
                    'tongcuoi' => $tongcuoi,
                    'dsphieu' => $danhsach
                ];
            }
            $_tongthu += $tongthu;
            $_tongchi += $tongchi;
        }
        array_unshift($results,
            [
            'tenphieu' => 'ĐẦU KỲ',
            'tongcuoi' => $dauky
        ]);

        $is_ketso = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id
        ])->where('ngay',$ngay)->count();

        return [
            'is_ketso' => $is_ketso,
            'dauky' => $dauky,
            'cuoiky' => $tongcuoi,
            'tongthu' => $_tongthu,
            'tongchi' => $_tongchi,
            'data' => $results
        ];
    }

    public function ket_so(Request $request) {
        $ngay = $request->ngay ?? null;
        $chinhanh_id = $request->chinhanh_id ?? null;
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);

        if ($ngay == null || $chinhanh_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $ketso = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id,
            'ngay' => $ngay
        ])->count();

        if ($ketso) {
            return [
                'succ' => 0,
                'noti' => 'Đã tồn tại phiếu kết sổ cuối ngày. Kết sổ cuối ngày thất bại!'
            ];
        }

        $sophieu = Phieu::withTrashed()->where('ngay', $ngay)
            ->where(['loaiphieu' => 'KSCN'])->max('sophieu');
        $sophieu = $sophieu == null ? 1 : ($sophieu + 1);

        $sophieu = $sophieu < 10 ? '000'.$sophieu : ($sophieu < 100 ? '00'.$sophieu : ($sophieu < 1000 ? '0'.$sophieu : $sophieu));

        $dauky = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id
        ])->where('ngay','<',$ngay)->orderByDesc('created_at')
            ->limit(1)->get('tienthanhtoan')->sum('tienthanhtoan');

        $tongthu = Phieu::where([
            'ngay' => $ngay,
            'chinhanh_id' => $chinhanh_id
        ])->whereIn('loaiphieu',['BH','PT','TCNKH'])
            ->selectRaw('tienthanhtoan + if(tienthua < 0,tienthua,0) as tongcuoi')
            ->get()->sum('tongcuoi');

        $tongchi = Phieu::where([
            'ngay' => $ngay,
            'chinhanh_id' => $chinhanh_id
        ])->whereIn('loaiphieu',['KTH','CNNNCC','PC'])->sum('tienthanhtoan');

        DB::beginTransaction();

        $phieu = new Phieu();
        $phieu->maphieu = 'KSCN'.date('Ymd',strtotime($ngay)).'-'.$sophieu;
        $phieu->loaiphieu = 'KSCN';
        $phieu->tongthanhtien = $dauky;
        $phieu->phuthu = $tongthu;
        $phieu->giamgia = $tongchi;
        $phieu->tienthanhtoan = $dauky + $tongthu - $tongchi;
        $phieu->ghichu = $request->ghichu ?? null;
        $phieu->sophieu = (int) $sophieu;
        $phieu->nhanvien_id = $nhanvien->id;
        $phieu->chinhanh_id = $chinhanh_id;
        $phieu->ngay = $ngay;
        $phieu->gio = date('H:i:s');
        $phieu->created_at = $ngay.' '.date('H:i:s');

        $message = '';

        try {
            $continue = $phieu->save();
        }
        catch (QueryException $exception) {
            $continue = false;
            $message = $exception->getMessage();
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
                    'maphieu' => $phieu->maphieu,
                ]
            ];
        }
    }

    public function mo_so(Request $request) {
        $chinhanh_id = $request->chinhanh_id ?? null;
        if ($chinhanh_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }
        $phieu = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id,
            'ngay' => date('Y-m-d')
        ])->first(['id','deleted_at']);

        if ($phieu == null) {
            return [
                'succ' => 0,
                'noti' => 'Không tìm thấy phiếu kết sổ ngày hôm nay. Mở sổ thất bại!'
            ];
        }

        if ($phieu->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Mở sổ thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Mở sổ thất bại.'
            ];
        }
    }
}
