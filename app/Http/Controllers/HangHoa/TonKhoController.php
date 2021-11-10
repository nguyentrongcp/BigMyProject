<?php

namespace App\Http\Controllers\HangHoa;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\HangHoaChiTiet;
use Illuminate\Http\Request;

class TonKhoController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if ($info->id == '1000000000') {
            $chinhanhs = ChiNhanh::whereIn('loai',['cuahang','khohanghong'])->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('quanly.hanghoa.tonkho.index', [
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function danh_sach(Request $request) {
        $chinhanh_id = $request->chinhanh_id;
        $is_tonkho = isset($request->is_tonkho);
        $hanghoas = json_decode($request->hanghoas);
        $chinhanhs = ChiNhanh::whereIn('loai',['cuahang','khohanghong'])->pluck('ten','id');
        if (count($hanghoas) > 0) {
            $orderBy = 'hanghoa_id';
        }
        else {
            $orderBy = 'chinhanh_id';
        }

        if ($chinhanh_id != 'all') {
            $thongtins = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id);
        }
        else {
            $thongtins = HangHoaChiTiet::whereIn('chinhanh_id',ChiNhanh::whereIn('loai',['cuahang','khohanghong'])->pluck('id'));
        }
        if (count($hanghoas) > 0) {
            $thongtins = $thongtins->whereIn('hanghoa_id',$hanghoas);
        }
        $thongtins = $thongtins->orderBy($orderBy)->orderByDesc('tonkho')->get(['hanghoa_id','tonkho','chinhanh_id']);
//        foreach($thongtins as $key => $thongtin) {
//            $thongtins[$thongtin->hanghoa_id] = $thongtin;
//            unset($thongtins[$key]);
//        }

        $danhsachs = HangHoa::where('is_quydoi',0);
//        $results = [];
        if (count($hanghoas) > 0) {
            $danhsachs = $danhsachs->whereIn('id',$hanghoas);
        }
        $danhsachs = $danhsachs->get(['id','ma','ten','donvitinh','quycach']);
        foreach($danhsachs as $key => $danhsach) {
            $danhsachs[$danhsach->id] = $danhsach;
            unset($danhsachs[$key]);
        }
        foreach($thongtins as $key => $thongtin) {
            $thongtin->chinhanh = $chinhanhs[$thongtin->chinhanh_id] ?? 'Chưa rõ';
            $hanghoa = $danhsachs[$thongtin->hanghoa_id];
            $thongtin->ma = $hanghoa->ma;
            $thongtin->ten = $hanghoa->ten;
            $thongtin->donvitinh = $hanghoa->donvitinh;
            $thongtin->quycach = $hanghoa->quycach;
        }
        if ($is_tonkho) {
            $results = [];
            foreach($thongtins as $thongtin) {
                if ($thongtin->tonkho != 0) {
                    $results[] = $thongtin;
                }
            }
            $thongtins = $results;
        }
//        usort($results, function($a, $b) {
//            return $a->tonkho > $b->tonkho ? -1 : ($a->tonkho == $b->tonkho ? 0 : 1);
//        });

        return $thongtins;
    }

    public function lay_thong_tin(Request $request) {
        $hanghoa_ma = $request->hanghoa_ma;
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));

        $hanghoa = HangHoa::where('ma',$hanghoa_ma)->first('ten');

        if ($hanghoa == null) {
            return [
                'succ' => 0,
                'noti' => 'Hàng hóa không tồn tại hoặc đã bị xóa!'
            ];
        }

        $chitiets = HangHoaChiTiet::where('hanghoa_ma',$hanghoa_ma)
            ->whereIn('chinhanh_id',ChiNhanh::whereIn('loai',['cuahang'])->pluck('id'))
            ->get();

        $results = [
            'ten' => $hanghoa->ten,
            'dongia' => '',
            'tonkho' => '',
            'danhsach' => []
        ];
        foreach($chitiets as $chitiet) {
            if ($chitiet->chinhanh_id == $chinhanh_id) {
                $results['dongia'] = $chitiet->dongia;
                $results['tonkho'] = $chitiet->tonkho;
            }
            else {
                $results['danhsach'][] = [
                    'ten' => $chitiet->getTenChiNhanh(),
                    'tonkho' => $chitiet->tonkho
                ];
            }
        }

        return [
            'succ' => 1,
            'data' => $results
        ];
    }
}
