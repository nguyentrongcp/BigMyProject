<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\MuaVu;
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
        $sothuaruong = ThuaRuong::where('muavu_id',$muavu_id)->count('id');
        $sonongdan = ThuaRuong::where('muavu_id',$muavu_id)->groupBy('nongdan_id')->count('nongdan_id');
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
            $results[] = $item;
        }

        return [
            'data' => [
                'sothuaruong' => $sothuaruong,
                'sonongdan' => $sonongdan,
                'danhsach' => $results
            ]
        ];
    }
}
