<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Functions\QuyTrinhLuaFuncs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\GiaiDoanPhanHoi;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\NongDan;
use App\Models\QuyTrinhLua\QuyTrinhThuaRuong;
use App\Models\QuyTrinhLua\ThuaRuong;
use App\Models\QuyTrinhLua\QuyTrinh;
use App\Models\QuyTrinhLua\SanPham;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuaRuongController extends Controller
{
    public function index($thuaruong_id) {
        if ($thuaruong_id == null) {
            abort(404);
        }

        $thuaruong = ThuaRuong::find($thuaruong_id);
        if ($thuaruong == null) {
            abort(404);
        }
        $nongdan = NongDan::find($thuaruong->nongdan_id);
        $muavu = MuaVu::find($thuaruong->muavu_id);

        return view('quanly.quytrinhlua.thuaruong.index', [
            'thuaruong' => $thuaruong,
            'nongdan' => $nongdan,
            'muavu' => $muavu
        ]);
    }

    public function mobile_index() {
        $muavus = MuaVu::where('status',1)->get(['id','ten','ten as text']);

        return view('nongdan.thuaruong.index', [
            'muavus' => $muavus
        ]);
    }

    public function cay_quy_trinh(Request $request) {
        $thuaruong_id = $request->thuaruong_id;

        if ($thuaruong_id == null) {
            return [
                'data' => [
                    'danhsach' => []
                ]
            ];
        }

        $thuaruong = ThuaRuong::find($thuaruong_id,['muavu_id']);
        if ($thuaruong == null) {
            return [
                'data' => [
                    'danhsach' => []
                ]
            ];
        }
        $muavu_id = $thuaruong->muavu_id;
        $models = QuyTrinh::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')->orderBy('den')->get();

        $sanphams = [];
        foreach(SanPham::withTrashed()->get(['id','ten','donvitinh','dongia']) as $sanpham) {
            $sanphams[$sanpham->id] = $sanpham;
        }

        $quytrinh_thuaruong = [];
        foreach(QuyTrinhThuaRuong::where('thuaruong_id',$thuaruong_id)->get() as $item) {
            $quytrinh_thuaruong[$item->quytrinh_id] = $item;
        }

        $giaidoans = [];
        foreach($models as $model) {
            $model->sanpham = $sanphams[$model->sanpham_id]->ten;
            $model->donvitinh = $sanphams[$model->sanpham_id]->donvitinh;
            $model->dongia = $sanphams[$model->sanpham_id]->dongia;
            $model->thanhtien = (float) $model->dongia * (float) $model->soluong;
            $model->quytrinh_thuaruong = $quytrinh_thuaruong[$model->id] ?? [];
            if (!isset($giaidoans[$model->giaidoan_id])) {
                $giaidoans[$model->giaidoan_id] = [];
            }
            $giaidoans[$model->giaidoan_id][] = $model;
        }

        $results = [];
        foreach(GiaiDoan::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')
                    ->orderBy('den')->get(['id','muavu_id','ten','tu','den','phanloai']) as $item) {
            $item->quytrinhs = $giaidoans[$item->id] ?? [];
            $item->phanhois = GiaiDoanPhanHoi::where([
                'giaidoan_id' => $item->id,
                'thuaruong_id' => $thuaruong_id
            ])->orderBy('created_at')->get();
            $nhanvien_ids = GiaiDoanPhanHoi::where([
                'giaidoan_id' => $item->id,
                'thuaruong_id' => $thuaruong_id
            ])->pluck('nhanvien_id');
            $nhanviens = NhanVien::withTrashed()->whereIn('id',$nhanvien_ids)->pluck('ten','id');
            foreach($item->phanhois as $phanhoi) {
                if ($phanhoi->nhanvien_id != null) {
                    $phanhoi->nhanvien = $nhanviens[$phanhoi->nhanvien_id] ?? 'Chưa xác định';
                }
            }
            $results[] = $item;
        }

        return [
            'data' => [
                'danhsach' => $results
            ]
        ];
    }

    public function danh_sach(Request $request) {
        $nongdan_id = $request->nongdan_id;
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.nong-dan.action',$request->cookie('token'))) {
            $results = ThuaRuong::withTrashed()->where('nongdan_id',$nongdan_id)
            ->orderByDesc('deleted_at')->orderByDesc('ngaysa')->get();
        }
        else {
            $results = ThuaRuong::where('nongdan_id',$nongdan_id)->orderByDesc('ngaysa')->get();
        }

        $muavus = MuaVu::withTrashed()->get(['id','ten','status']);
        $quytrinh = QuyTrinh::groupBy('muavu_id')->selectRaw('count(id) as soluong, muavu_id')->pluck('soluong','muavu_id');
        foreach($muavus as $key => $muavu) {
            $muavus[$muavu->id] = $muavu;
            unset($muavus[$key]);
        }

        foreach($results as $result) {
            $result->muavu = $muavus[$result->muavu_id]->ten ?? 'Chưa rõ';
            $result->status = $muavus[$result->muavu_id]->status ?? 'Chưa rõ';
            $result->tinhtrang_hoanthanh = 0;
            $result->tongquytrinh = $quytrinh[$result->muavu_id] ?? 0;
        }

        return $results;
    }

    public function danh_sach_mobile(Request $request) {
        $nongdan_id = QuyTrinhLuaFuncs::getNongDanIDByToken($request->cookie('token'));
        $results = ThuaRuong::where('nongdan_id',$nongdan_id)->orderByDesc('ngaysa')->get();

        $muavus = MuaVu::withTrashed()->get(['id','ten','status']);
        $quytrinh = QuyTrinh::groupBy('muavu_id')->selectRaw('count(id) as soluong, muavu_id')->pluck('soluong','muavu_id');
        foreach($muavus as $key => $muavu) {
            $muavus[$muavu->id] = $muavu;
            unset($muavus[$key]);
        }

        foreach($results as $result) {
            $result->muavu = $muavus[$result->muavu_id]->ten ?? 'Chưa rõ';
            $result->status = $muavus[$result->muavu_id]->status ?? 'Chưa rõ';
            $result->tinhtrang_hoanthanh = 0;
            $result->tongquytrinh = $quytrinh[$result->muavu_id] ?? 0;
        }

        return $results;
    }

    public function them_moi(Request $request) {
        $nongdan_id = $request->nongdan_id;
        $muavu_id = $request->muavu_id;
        if ($nongdan_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }
        if ($muavu_id == null) {
            return [
                'succ' => 0,
                'type' => 'muavu_id'
            ];
        }
        $dientich = $request->dientich ?? '';
        $ngaysa = $request->ngaysa ?? '';
        $ghichu = $request->ghichu ?? null;
        $ten = $request->ten ?? null;
        if ($dientich == null) {
            return [
                'succ' => 0,
                'type' => 'dientich'
            ];
        }
        if ($ngaysa == null) {
            return [
                'succ' => 0,
                'type' => 'ngaysa'
            ];
        }
        $toado = $request->toado ?? null;

        $model = new ThuaRuong();
        $model->ten = $ten;
        $model->nongdan_id = $nongdan_id;
        $model->muavu_id = $muavu_id;
        $model->dientich = $dientich;
        $model->ngaysa = $ngaysa;
        $model->ghichu = $ghichu;
        $model->toado = $toado;

        try {
            $muavu = MuaVu::withTrashed()->find($muavu_id,['id','ten','status']);
            $tongquytrinh = QuyTrinh::where('muavu_id',$muavu_id)->count();
            if ($model->ten == null) {
                $tongmuavu = ThuaRuong::where([
                    'nongdan_id' => $nongdan_id,
                    'muavu_id' => $muavu_id
                ])->count();
                $model->ten = $muavu->ten.' ('.($tongmuavu + 1).')';
            }
            $model->save();
            $model->muavu = $muavu->ten;
            $model->status = $muavu->status;
            $model->tinhtrang_hoanthanh = 0;
            $model->tongquytrinh = $tongquytrinh;
            $model->deleted_at = null;

            return [
                'succ' => 1,
                'noti' => 'Thêm thửa ruộng mới thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm thửa ruộng mới thất bại. Vui lòng thử lại!',
                'mess' => $exception->getMessage()
            ];
        }
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

        if ($field == 'dientich' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Diện tích không hợp lệ!'
            ];
        }

        if ($field == 'ngaysa' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Ngày sạ không được bỏ trống!'
            ];
        }

        if ($field == 'muavu_id' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Bạn chưa chọn mùa vụ!'
            ];
        }

        $model = ThuaRuong::find($id);

        $model->$field = $value;

        if ($model->save()) {
            if ($field == 'muavu_id') {
                $model->muavu = MuaVu::find($value,'ten')->ten;
                $tongquytrinh = QuyTrinh::where('muavu_id',$value)->count();
                $model->tongquytrinh = $tongquytrinh;
                $model->tinhtrang_hoanthanh = 0;
            }
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin thửa ruộng thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin thửa ruộng thất bại. Vui lòng thử lại!'
            ];
        }
    }

    public function xoa(Request $request) {
        $id = $request->id ?? null;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $model = ThuaRuong::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin thửa ruộng thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin thửa ruộng thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function phuc_hoi(Request $request) {
        $id = $request->id ?? null;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $model = ThuaRuong::withTrashed()->find($id);

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin thửa ruộng thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin thửa ruộng thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function traloi_phanhoi(Request $request) {
        $giaidoan_id = $request->giaidoan_id ?? null;
        $thuaruong_id = $request->thuaruong_id ?? null;
        $noidung = $request->noidung ?? '';
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','ten']);
        $nongdan_id = ThuaRuong::find($thuaruong_id,'nongdan_id')->nongdan_id;

        if ($giaidoan_id == null || $thuaruong_id == null || $noidung == '') {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $model = new GiaiDoanPhanHoi();
        $model->giaidoan_id = $giaidoan_id;
        $model->thuaruong_id = $thuaruong_id;
        $model->nhanvien_id = $nhanvien->id;
        $model->noidung = $noidung;

        try {
            $model->save();
            $tieude = $nhanvien->ten.' đã trả lời phản hồi của bạn';
            DB::table('quytrinhlua_thongbao')->insert([
                'nongdan_id' => $nongdan_id,
                'nhanvien_id' => $nhanvien->id,
                'giaidoan_id' => $giaidoan_id,
                'phanhoi_id' => $model->id,
                'tieude' => 'đã trả lời phản hồi của bạn',
                'noidung' => $noidung,
                'loai' => 'phanhoi',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $thongbao = [
                [
                    'topic' => $nongdan_id,
                    'tieude' => $tieude,
                    'noidung' => $noidung
                ]
            ];
            return [
                'succ' => 1,
                'noti' => 'Trả lời phản hồi thành công.',
                'data' => [
                    'model' => $model,
                    'thongbao' => $thongbao
                ]
            ];
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Trả lời phản hồi thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }
    }

    public function xoa_phan_hoi(Request $request) {
        $phanhoi_id = $request->phanhoi_id ?? null;
        if ($phanhoi_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $phanhoi = GiaiDoanPhanHoi::find($phanhoi_id);

        if ($phanhoi->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa phản hồi thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa phản hồi thất bại. Vui lòng thử lại sau!'
            ];
        }
    }
}
