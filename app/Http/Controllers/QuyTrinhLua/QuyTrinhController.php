<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\NongDan;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\SanPham;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuyTrinhController extends Controller
{
    public function index() {
        $muavus = MuaVu::where('status',1)->get(['id','ten as text']);

        return view('quanly.quytrinhlua.quytrinh-sudung.index', [
            'muavus' => $muavus
        ]);
    }

    public function danh_sach(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        $phanloai = $request->phanloai ?? null;
        if ($muavu_id == null || $phanloai == null) {
            return [];
        }
        $phanloai = $phanloai == 'phan' ? 'Phân bón' : 'Thuốc';
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.quy-trinh.action',$request->cookie('token'))) {
            $models = QuyTrinh::withTrashed()->where([
                    'muavu_id' => $muavu_id,
                    'phanloai' => $phanloai
                ])->orderBy('deleted_at')
                ->orderBy('tu')->orderBy('den')->get();
        }
        else {
            $models = QuyTrinh::where([
                'muavu_id' => $muavu_id,
                'phanloai' => $phanloai
            ])->orderBy('tu')->orderBy('den')->get();
        }

        $sanphams = [];
        foreach(SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']) as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }

        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
        }

        return $models;
    }

    public function them_moi(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        if ($muavu_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Bạn chưa chọn mùa vụ!'
            ];
        }
        $giaidoan_id = $request->giaidoan_id ?? null;
        if ($giaidoan_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Bạn chưa chọn giai đoạn!'
            ];
        }
        $giaidoan = $request->giaidoan ?? '';
        $tu = $request->tu ?? '';
        $den = $request->den ?? '';
        $phanloai = $request->phanloai ?? '';
        $sanpham_id = $request->sanpham_id ?? null;
        $congdung = $request->congdung ?? '';
        $soluong = $request->soluong ?? '';
        $ghichu = $request->ghichu ?? null;

        if ($giaidoan == '') {
            return [
                'succ' => 0,
                'noti' => 'Giai đoạn không được bỏ trống!'
            ];
        }
        if ($tu == '') {
            return [
                'succ' => 0,
                'noti' => 'Số ngày không hợp lệ!'
            ];
        }
        if ($den == '') {
            return [
                'succ' => 0,
                'noti' => 'Số ngày không hợp lệ!'
            ];
        }
        if ($phanloai == '') {
            return [
                'succ' => 0,
                'noti' => 'Phân loại không được bỏ trống!'
            ];
        }
        if ($sanpham_id == null) {
            return [
                'succ' => 0,
                'type' => 'sanpham_id',
                'erro' => 'Bạn chưa chọn sản phẩm'
            ];
        }
        $sanpham = SanPham::withTrashed()->find($sanpham_id,['ten','donvitinh','dongia']);
        if ($sanpham == null) {
            return [
                'succ' => 0,
                'type' => 'sanpham_id',
                'erro' => 'Sản phẩm không tồn tại!'
            ];
        }
        if ($congdung == '') {
            return [
                'succ' => 0,
                'type' => 'giaidoan'
            ];
        }
        if ($soluong == '') {
            return [
                'succ' => 0,
                'type' => 'soluong'
            ];
        }

        $model = new QuyTrinh();
        $model->giaidoan = $giaidoan;
        $model->giaidoan_id = $giaidoan_id;
        $model->tu = $tu;
        $model->den = $den;
        $model->phanloai = $phanloai;
        $model->sanpham_id = $sanpham_id;
        $model->congdung = $congdung;
        $model->soluong = $soluong;
        $model->ghichu = $ghichu;
        $model->muavu_id = $muavu_id;
        $model->deleted_at = null;

        try {
            $model->save();
            $model->sanpham = $sanpham->ten;
            $model->donvitinh = $sanpham->donvitinh;
            $model->dongia = $sanpham->dongia;
            $model->thanhtien = (float) $sanpham->dongia * (float) $soluong;
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm quy trình mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm quy trình mới thành công.',
            'data' => [
                'model' => $model
            ]
        ];
    }

    public function cap_nhat(Request $request) {
        $id = $request->id ?? null;
        $field = $request->field ?? null;
        $value = $request->value ?? '';

        if ($id === null || $field === null || $value === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        if ($value == '') {
            $errors = [
                'giaidoan_id' => 'Bạn chưa chọn giai đoạn!',
                'tu' => 'Số ngày không hợp lệ!',
                'den' => 'Số ngày không hợp lệ!',
                'sanpham_id' => 'Bạn chưa chọn sản phẩm!',
                'soluong' => 'Số lượng không hợp lệ!',
                'congdung' => 'Công dụng không được bỏ trống!'
            ];

            return [
                'succ' => 0,
                'erro' => $errors[$field]
            ];
        }

        DB::beginTransaction();
        $model = QuyTrinh::withTrashed()->find($id);
        $model->$field = $value;
        if ($field == 'giaidoan_id') {
            $giaidoan = GiaiDoan::find($value);
            $model->giaidoan = $giaidoan->ten;
            $model->tu = $giaidoan->tu;
            $model->den = $giaidoan->den;
            $model->phanloai = $giaidoan->phanloai;
        }

        if ($model->save()) {
            if ($field == 'sanpham_id' || $field == 'soluong') {
                $sanpham = SanPham::withTrashed()->find($field == 'sanpham_id' ? $value : $model->sanpham_id,['ten','donvitinh','dongia']);
                if ($field == 'sanpham_id') {
                    if ($sanpham == null) {
                        return [
                            'succ' => 0,
                            'erro' => 'Sản phẩm không tồn tại!'
                        ];
                    }
                    $model->sanpham = $sanpham->ten;
                    $model->donvitinh = $sanpham->donvitinh;
                    $model->dongia = $sanpham->dongia;
                }
                $model->thanhtien = (float) $sanpham->dongia * (float) $model->soluong;
            }
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin quy trình thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin quy trình thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function xoa(Request $request) {
        $id = $request->id ?? null;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $model = QuyTrinh::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin quy trình thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at,
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin quy trình thất bại. Vui lòng thử lại sau!',
            ];
        }
    }

    public function phuc_hoi(Request $request) {
        $id = $request->id ?? null;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $model = QuyTrinh::withTrashed()->find($id);

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin quy trình thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin quy trình thất bại. Vui lòng thử lại sau!',
            ];
        }
    }

    public function tim_kiem(Request $request) {
        $q = Funcs::convertToSlug($request->q);

        $models = SanPham::whereRaw("(slug like '%$q%' or ma like '%$q%')");

        $models = $models->limit(20)->get();

        foreach($models as $model) {
            $model->text = $model->ma.' - '.$model->ten;
        }

        return [
            'results' => $models
        ];
    }

    public function quytrinh_homnay2() {
        $results = [];
        $muavu_ids = MuaVu::where('status',1)->pluck('id');
        $quytrinh_ids = QuyTrinh::whereIn('giaidoan_id',GiaiDoan::whereIn('muavu_id',$muavu_ids)->pluck('id'))->pluck('id');
        $_sanphams = SanPham::withTrashed()->whereIn('id',QuyTrinh::whereIn('id',$quytrinh_ids)->pluck('sanpham_id'))->get(['id','ten','donvitinh']);
        foreach($_sanphams as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }
        $_giaidoans = GiaiDoan::whereIn('muavu_id',$muavu_ids)->get(['id','tu','den','ten'])->toArray();
        foreach($_giaidoans as $giaidoan) {
            $giaidoans[$giaidoan['id']] = (object) $giaidoan;
        }
        foreach(QuyTrinh::whereIn('giaidoan_id',GiaiDoan::whereIn('muavu_id',$muavu_ids)->pluck('id'))
                    ->get(['id','sanpham_id','soluong','congdung','tu','den','giaidoan','giaidoan_id']) as $quytrinh) {
            if (isset($giaidoans[$quytrinh->giaidoan_id])) {
                $giaidoans[$quytrinh->giaidoan_id]->quytrinhs = [];
            }
            $giaidoans[$quytrinh->giaidoan_id]->quytrinhs[] = $quytrinh;
        }
        $thuaruongs = ThuaRuong::whereIn('muavu_id',$muavu_ids)
            ->get(['id','ten','nongdan_id','ngaysa']);
        foreach($thuaruongs as $thuaruong) {
            $songay = (strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa))/86400;
            $trangthais = QuyTrinhThuaRuong::whereIn('quytrinh_id',$quytrinh_ids)->where('thuaruong_id',$thuaruong->id)
                ->pluck('status','quytrinh_id');
            foreach($giaidoans as $giaidoan) {
                if ($songay >= $giaidoan->tu && $songay <= $giaidoan->den) {
                    foreach($giaidoan->quytrinhs as $quytrinh) {
                        $trangthai = $trangthais[$quytrinh->id] ?? 0;
                        if (!$trangthai) {
                            $quytrinh->sanpham = $sanphams[$quytrinh->sanpham_id]->ten;
                            $quytrinh->donvitinh = $sanphams[$quytrinh->sanpham_id]->donvitinh;
                            $results[] = [
                                'topic' => $thuaruong->nongdan_id,
                                'tieude' => 'Quy trình hôm nay',
                                'noidung' => $quytrinh->sanpham."\nCông dụng: ".$quytrinh->congdung."\nSố lượng/ha: ".((float)$quytrinh->soluong)
                            ];
                        }
                    }
                }
            }
        }

        return $results;
    }

    public function quytrinh_homnay() {
        $results = [];
        $muavu_ids = MuaVu::where('status',1)->pluck('id');
        $giaidoans = GiaiDoan::whereIn('muavu_id',$muavu_ids)->get(['id','tu','den','ten']);
        $thuaruongs = ThuaRuong::whereIn('muavu_id',$muavu_ids);
        $whereRaw = '';
        $giaidoan_ids = [];
        foreach($giaidoans as $giaidoan) {
            $tungay = date('Y-m-d',strtotime(-$giaidoan->tu.' days',strtotime(date('Y-m-d'))));
            $denngay = date('Y-m-d',strtotime(-$giaidoan->den.' days',strtotime(date('Y-m-d'))));
            if ($whereRaw != '') {
                $whereRaw .= ' or ';
            }
            $whereRaw .= "('$denngay' <= ngaysa and '$tungay' >= ngaysa)";
            $giaidoan->quytrinhs = QuyTrinh::where('giaidoan_id',$giaidoan->id)->get(['id','sanpham_id','soluong','congdung','tu','den','giaidoan','giaidoan_id']);
            $giaidoan_ids[] = $giaidoan->id;
        }
        $sanphams = [];
        $_sanphams = SanPham::withTrashed()->whereIn('id',QuyTrinh::whereIn('giaidoan_id',$giaidoan_ids)->pluck('sanpham_id'))
            ->get(['id','ten','donvitinh']);
        foreach($_sanphams as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }
        $whereRaw = '('.$whereRaw.')';
        $thuaruongs = $thuaruongs->whereRaw($whereRaw)->get(['id','ten','nongdan_id','ngaysa']);
        $thuaruong_ids = [];
        $nongdan_ids = [];
        foreach($thuaruongs as $thuaruong) {
            $thuaruong_ids[] = $thuaruong->id;
            $nongdan_ids[] = $thuaruong->nongdan_id;
        }
        $_trangthais = QuyTrinhThuaRuong::whereIn('thuaruong_id',$thuaruong_ids)->get(['quytrinh_id','thuaruong_id','status']);
        $trangthais = [];
        foreach($_trangthais as $trangthai) {
            if (!isset($trangthais[$trangthai->thuaruong_id])) {
                $trangthais[$trangthai->thuaruong_id] = [];
            }
            $trangthais[$trangthai->thuaruong_id][$trangthai->quytrinh_id] = $trangthai->status;
        }
        $nongdan_hoatdongs = NongDan::whereIn('id',$nongdan_ids)->whereNotNull('remember_token')->pluck('id')->toArray();
        foreach($thuaruongs as $thuaruong) {
            $songay = (strtotime(date('Y-m-d')) - strtotime($thuaruong->ngaysa))/86400;
            foreach($giaidoans as $giaidoan) {
                if ($songay >= $giaidoan->tu && $songay <= $giaidoan->den) {
                    foreach($giaidoan->quytrinhs as $quytrinh) {
                        $trangthai = $trangthais[$thuaruong->id][$quytrinh->id] ?? 0;
                        if (!$trangthai && in_array($thuaruong->nongdan_id,$nongdan_hoatdongs) !== false) {
                            $quytrinh->sanpham = $sanphams[$quytrinh->sanpham_id]->ten;
                            $quytrinh->donvitinh = $sanphams[$quytrinh->sanpham_id]->donvitinh;
                            $results[] = [
                                'topic' => $thuaruong->nongdan_id,
                                'tieude' => 'Hôm nay cần làm',
                                'noidung' => $quytrinh->sanpham."\nCông dụng: ".$quytrinh->congdung."\nSố lượng/ha: ".((float)$quytrinh->soluong)
                            ];
                        }
                    }
                }
            }
        }

        return $results;
    }
}
