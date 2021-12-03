<?php

namespace App\Http\Controllers\HangHoa;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\PhieuChiTiet;
use Illuminate\Http\Request;

class SoLuongBanController extends Controller
{
    public function index() {
        $info = Funcs::getNhanVienByToken($_COOKIE['token'],['id','chinhanh_id']);
        if (Funcs::isPhanQuyenByToken('role.chi-nhanh.tat-ca',$_COOKIE['token'])) {
            $chinhanhs = ChiNhanh::whereIn('loai',['cuahang'])->get(['id','ten as text']);
        }
        else {
            $chinhanhs = ChiNhanh::where('id',$info->chinhanh_id)->get(['id','ten as text']);
        }

        return view('quanly.hanghoa.soluongban.index', [
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function danh_sach(Request $request) {
        $begin = $request->begin ?? '2020-01-01';
        $end = $request->end ?? date('Y-m-d');
        $end = date('Y-m-d',strtotime('+1 days',strtotime($end)));
        $chinhanh_id = $request->chinhanh_id;
        $hanghoas = json_decode($request->hanghoas);

        $danhsachs = PhieuChiTiet::where('loaiphieu','BH')->whereBetween('created_at',[$begin,$end]);
        if (count($hanghoas) > 0) {
            $danhsachs = $danhsachs->whereIn('hanghoa_id',$hanghoas);
        }
        if ($chinhanh_id != 'all') {
            $danhsachs = $danhsachs->where('chinhanh_id',$chinhanh_id);
        }
        $danhsachs = $danhsachs->groupBy('hanghoa_id')->selectRaw('sum(soluong) as soluong, hanghoa_id')->get();
        $hanghoa_ids = [];
        foreach($danhsachs as $key => $danhsach) {
            if (in_array($danhsach->hanghoa_id,$hanghoa_ids) === false) {
                $hanghoa_ids[] = $danhsach->hanghoa_id;
            }
            $danhsachs[$danhsach->hanghoa_id] = (float)$danhsach->soluong;
            unset($danhsachs[$key]);
        }

        $trahangs = PhieuChiTiet::where('loaiphieu','KTH')->whereBetween('created_at',[$begin,$end]);
        if (count($hanghoas) > 0) {
            $trahangs = $trahangs->whereIn('hanghoa_id',$hanghoas);
        }
        if ($chinhanh_id != 'all') {
            $trahangs = $trahangs->where('chinhanh_id',$chinhanh_id);
        }
        $trahangs = $trahangs->groupBy('hanghoa_id')->selectRaw('count(soluong) as soluong, hanghoa_id')->get();
        foreach($trahangs as $key => $trahang) {
            if (in_array($trahang->hanghoa_id,$hanghoa_ids) === false) {
                $hanghoa_ids[] = $trahang->hanghoa_id;
            }
            $trahangs[$trahang->hanghoa_id] = (float)$trahang->soluong;
            unset($trahangs[$key]);
        }

        $results = HangHoa::whereIn('id',$hanghoa_ids)->get(['id','ten','ma','donvitinh','quycach']);
        foreach($results as $result) {
            $result->slban = $danhsachs[$result->id] ?? 0;
            $result->sltra = $trahangs[$result->id] ?? 0;
            $result->slbanthuc = $result->slban - $result->sltra;
        }
        $results = $results->toArray();

        usort($results, function($a, $b) {
            return $a['slbanthuc'] > $b['slbanthuc'] ? -1 : ($a['slbanthuc'] == $b['slbanthuc'] ? 0 : 1);
        });

        return $results;
    }
}
