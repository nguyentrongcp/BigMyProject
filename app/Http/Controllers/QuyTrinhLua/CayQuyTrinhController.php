<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\GiaiDoanPhanHoi;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\NongDan;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\SanPham;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Http\Request;

class CayQuyTrinhController extends Controller
{
    public function index() {
        $muavus = MuaVu::where('status',1)->get(['id','ten as text']);

        return view('quanly.quytrinhlua.cayquytrinh.index', [
            'muavus' => $muavus
        ]);
    }

    public function danh_sach(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        if ($muavu_id == null) {
            return [];
        }
        $nongdan_ids = NongDan::pluck('id');
        $sothuaruong = ThuaRuong::where('muavu_id',$muavu_id)->whereIn('nongdan_id',$nongdan_ids)->count();
        $sonongdan = ThuaRuong::where('muavu_id',$muavu_id)->whereIn('nongdan_id',$nongdan_ids)
            ->groupBy('nongdan_id')->get('id');
        $models = QuyTrinh::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')->orderBy('den')->get();

        $sanphams = [];
        foreach(SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']) as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }

        $dacheck = QuyTrinhThuaRuong::where([
            'muavu_id' => $muavu_id,
            'status' => 1
        ])->selectRaw('count(thuaruong_id) as soluong, quytrinh_id')
            ->groupBy('quytrinh_id')->pluck('soluong','quytrinh_id');

        $giaidoans = [];
        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
            $model->dacheck = $dacheck[$model->id] ?? 0;
            if (!isset($giaidoans[$model->giaidoan_id])) {
                $giaidoans[$model->giaidoan_id] = [];
            }
            $giaidoans[$model->giaidoan_id][] = $model;
        }

        $results = [];
        foreach(GiaiDoan::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')
                    ->orderBy('den')->get(['id','muavu_id','ten','tu','den','phanloai']) as $item) {
            $quytrinhs = QuyTrinh::where('giaidoan_id',$item->id)->pluck('id');
            $sohoanthanh = QuyTrinhThuaRuong::whereIn('quytrinh_id',$quytrinhs)->where('status',1)
                ->selectRaw('count(quytrinh_id) as soluong')->groupBy('thuaruong_id')->pluck('soluong');
            $item->sohoanthanh = 0;
            foreach($sohoanthanh as $sl) {
                if ($sl == count($quytrinhs)) {
                    $item->sohoanthanh++;
                }
            }
            $item->quytrinhs = $giaidoans[$item->id] ?? [];
            $max_phanhois = GiaiDoanPhanHoi::where('giaidoan_id',$item->id)->selectRaw('max(id) as max_id')
                ->groupBy('thuaruong_id')->pluck('max_id');
            $item->tongso_phanhoi = GiaiDoanPhanHoi::where('giaidoan_id',$item->id)->whereNotNull('nongdan_id')->count();
            $item->phanhoi_moi = GiaiDoanPhanHoi::whereIn('id',$max_phanhois)->whereNotNull('nongdan_id')->count();
            $results[] = $item;
        }

        $_toados = ThuaRuong::where('muavu_id',$muavu_id)->whereNotNull('toado')
            ->whereIn('nongdan_id',$nongdan_ids)->get('toado','nongdan_id');
        $nongdan_ids = [];
        foreach($_toados as $toado) {
            $nongdan_ids[] = $toado->nongdan_id;
        }

        $nongdans = NongDan::whereIn('id',$nongdan_ids)->pluck('ten','id');
        $toados = [];
        foreach($_toados as $toado) {
            $vitri = explode(',',$toado->toado);
            $toados[] = [
                ['lat' => (float) $vitri[0], 'lng' => (float) $vitri[1]], $nongdans[$toado->nongdan_id] ?? 'Chưa xác định'
            ];
        }

        return [
            'data' => [
                'toados' => $toados,
                'sothuaruong' => $sothuaruong,
                'sonongdan' => count($sonongdan),
                'muavu_id' => $muavu_id,
                'danhsach' => $results
            ]
        ];
    }

    public function danhsach_thuaruong(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        $muavu = MuaVu::find($muavu_id,'ten');
        if ($muavu == null) {
            return [];
        }

        $quytrinh_ids = QuyTrinh::whereIn('giaidoan_id',GiaiDoan::where('muavu_id',$muavu_id)->pluck('id'))->pluck('id');
        $tongquytrinh = count($quytrinh_ids);
        $tinhtrang_hoanthanhs = QuyTrinhThuaRuong::whereIn('quytrinh_id',$quytrinh_ids)->where('status',1)
            ->selectRaw('count(status) as soluong, thuaruong_id')->groupBy('thuaruong_id')->pluck('soluong','thuaruong_id');

        $thuaruongs = ThuaRuong::where('muavu_id',$muavu_id)
            ->whereIn('nongdan_id',NongDan::pluck('id'))->get();
        $nongdan_ids = [];
        foreach($thuaruongs as $thuaruong) {
            $nongdan_ids[] = $thuaruong->nongdan_id;
        }

        $_nongdans = NongDan::whereIn('id',$nongdan_ids)->get(['id','ten','dienthoai']);
        $nongdans = [];
        foreach($_nongdans as $nongdan) {
            $nongdans[$nongdan->id] = $nongdan;
        }

        foreach($thuaruongs as $thuaruong) {
            $thuaruong->tennongdan = $nongdans[$thuaruong->nongdan_id]->ten ?? 'Không xác định';
            $thuaruong->dienthoai = $nongdans[$thuaruong->nongdan_id]->dienthoai ?? 'Không xác định';
            $thuaruong->tinhtrang_hoanthanh = $tinhtrang_hoanthanhs[$thuaruong->id] ?? 0;
            $thuaruong->tongquytrinh = $tongquytrinh;
        }

        return $thuaruongs;
    }

    public function danhsach_nongdan(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        $muavu = MuaVu::find($muavu_id,'ten');
        if ($muavu == null) {
            return [];
        }

        $nongdan_ids = ThuaRuong::where('muavu_id',$muavu_id)->pluck('nongdan_id');

        $nongdans = NongDan::whereIn('id',$nongdan_ids)->get(['id','ma','ten','danhxung','dienthoai','dienthoai2','diachi','ghichu']);

        return $nongdans;
    }

    public function danhsach_hoanthanh(Request $request) {
        $giaidoan_id = $request->giaidoan_id ?? null;
        $muavu_id = $request->muavu_id ?? null;
        if ($giaidoan_id == null || $muavu_id == null) {
            return [];
        }

        $quytrinh_ids = QuyTrinh::whereIn('giaidoan_id',GiaiDoan::where('muavu_id',$muavu_id)->pluck('id'))->pluck('id');
        $tongquytrinh = count($quytrinh_ids);
        $tinhtrang_hoanthanhs = QuyTrinhThuaRuong::whereIn('quytrinh_id',$quytrinh_ids)->where('status',1)
            ->selectRaw('count(status) as soluong, thuaruong_id')->groupBy('thuaruong_id')->pluck('soluong','thuaruong_id');

        $quytrinh_ids = QuyTrinh::where('giaidoan_id',$giaidoan_id)->pluck('id');
        $thuaruongs = QuyTrinhThuaRuong::whereIn('quytrinh_id',$quytrinh_ids)->where('status',1)
            ->selectRaw('count(quytrinh_id) as soluong, thuaruong_id')->groupBy('thuaruong_id')->get();
        $thuaruong_ids = [];
        foreach($thuaruongs as $thuaruong) {
            if ($thuaruong->soluong == count($quytrinh_ids)) {
                $thuaruong_ids[] = $thuaruong->thuaruong_id;
            }
        }

        $thuaruongs = ThuaRuong::whereIn('id',$thuaruong_ids)->get();
        $_nongdans = NongDan::whereIn('id',ThuaRuong::whereIn('id',$thuaruong_ids)->pluck('nongdan_id'))->get(['id','ten','dienthoai']);
        $nongdans = [];
        foreach($_nongdans as $nongdan) {
            $nongdans[$nongdan->id] = $nongdan;
        }

        foreach($thuaruongs as $thuaruong) {
            $thuaruong->tennongdan = $nongdans[$thuaruong->nongdan_id]->ten ?? 'Không xác định';
            $thuaruong->dienthoai = $nongdans[$thuaruong->nongdan_id]->dienthoai ?? 'Không xác định';
            $thuaruong->tinhtrang_hoanthanh = $tinhtrang_hoanthanhs[$thuaruong->id] ?? 0;
            $thuaruong->tongquytrinh = $tongquytrinh;
        }

        return $thuaruongs;
    }

    public function danhsach_phanhoimoi(Request $request) {
        $giaidoan_id = $request->giaidoan_id ?? null;
        if ($giaidoan_id == null) {
            return [];
        }
        $max_phanhois = GiaiDoanPhanHoi::where('giaidoan_id',$giaidoan_id)->selectRaw('max(id) as max_id')
            ->groupBy('thuaruong_id')->pluck('max_id');
        $thuaruong_ids = GiaiDoanPhanHoi::whereIn('id',$max_phanhois)->whereNotNull('nongdan_id')->pluck('thuaruong_id');
        $results = [];
        foreach($thuaruong_ids as $thuaruong_id) {
            $thuaruong = ThuaRuong::find($thuaruong_id,['id','nongdan_id','ten']);
            $nongdan = NongDan::find($thuaruong->nongdan_id,['id','ten','dienthoai']);
            $phanhois = GiaiDoanPhanHoi::where([
                'giaidoan_id' => $giaidoan_id,
                'thuaruong_id' => $thuaruong_id
            ])->get();
            $nhanvien_ids = [];
            foreach($phanhois as $phanhoi) {
                if ($phanhoi->nhanvien_id != null) {
                    $nhanvien_ids[] = $phanhoi->nhanvien_id;
                }
            }
            $nhanvien = NhanVien::withTrashed()->whereIn('id',$nhanvien_ids)->pluck('ten','id');
            foreach($phanhois as $phanhoi) {
                if ($phanhoi->nhanvien_id != null) {
                    $phanhoi->nhanvien = $nhanvien[$phanhoi->nhanvien_id] ?? 'Chưa xác định';
                }
            }
            $result = (object) [
                'tennongdan' => $nongdan == null ? 'Chưa xác định' : $nongdan->ten,
                'dienthoai' => $nongdan == null ? 'Chưa xác định' : $nongdan->dienthoai,
                'ten' => $thuaruong == null ? 'Chưa xác định' : $thuaruong->ten,
                'id' => $thuaruong_id,
                'phanhois' => $phanhois
            ];
            $results[] = $result;
        }

        return $results;
    }
}
