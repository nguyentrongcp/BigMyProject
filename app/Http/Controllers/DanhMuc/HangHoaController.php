<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\DonViTinh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\HangHoaNhom;
use App\Models\DanhMuc\QuyDoi;
use App\Models\HangHoaChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HangHoaController extends Controller
{
    public function index() {
        $donvitinhs = DonViTinh::all('ten as text');
        $nhoms = HanghoaNhom::all('ten as text');

        return view('quanly.danhmuc.hanghoa.index', [
            'donvitinhs' => $donvitinhs,
            'nhoms' => $nhoms
        ]);
    }

    public function mobile_index() {
        return view('mobile.danhmuc.hanghoa.index');
    }

    public function danh_sach_mobile(Request $request) {
        $q = Funcs::convertToSlug($request->q);
        $nhanvien = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);

        $options = ['id','ma','ten','donvitinh','nhom','quycach','hinhanh'];
        if ($nhanvien->id == '1000000000') {
            $options[] = 'gianhap';
        }

        $models = HangHoa::where('slug','like',"%$q%")->orWhere('ma','like',"%$q%")->limit(20)->get($options);

        $ids = [];
        foreach($models as $model) {
            $ids[] = $model->id;
        }
        $chitiets = HangHoaChiTiet::whereIn('hanghoa_id',$ids)->where('chinhanh_id',$nhanvien->chinhanh_id)->get(['hanghoa_id','dongia','tonkho']);
        foreach($chitiets as $key => $chitiet) {
            $chitiets[$chitiet->hanghoa_id] = $chitiet;
            unset($chitiets[$key]);
        }
        foreach($models as $model) {
            $model->tonkho = (float) ($chitiets[$model->id]->tonkho ?? 'Chưa có');
            $model->dongia = $chitiets[$model->id]->dongia ?? '0.00';
        }

        return $models;
    }

    public function danh_sach(Request $request) {
        if (Funcs::isPhanQuyenByToken('danh-muc.hang-hoa.action',$request->cookie('token'))) {
            $models = HangHoa::withTrashed()->orderBy('deleted_at')->orderBy('updated_at','desc')->get();
        }
        else {
            $models = HangHoa::orderBy('updated_at','desc')->get();
        }
        if (!Funcs::isPhanQuyenByToken('role.gia-nhap',$request->cookie('token'))) {
            foreach($models as $model) {
                unset($model->gianhap);
            }
        }

        return $models;
    }

    public function danhmuc_quydoi() {
        $hanghoas = [];
        foreach(HangHoa::whereIn('id',QuyDoi::pluck('id_cha'))->orWhereIn('id',QuyDoi::pluck('id_con'))
                    ->get(['id','ma','ten','donvitinh']) as $hanghoa) {
            $hanghoas[$hanghoa->id] = $hanghoa;
        }
        $models = QuyDoi::all(['id','id_cha','id_con','soluong']);
        foreach($models as $model) {
            $hanghoa_cha = $hanghoas[$model->id_cha] ?? (object) [
                    'ma' => null,
                    'ten' => 'Hàng hóa cha không tồn tại',
                    'donvitinh' => null
                ];
            $hanghoa_con = $hanghoas[$model->id_con] ?? (object) [
                    'ma' => null,
                    'ten' => 'Hàng hóa con không tồn tại',
                    'donvitinh' => null
                ];
            $model->ma = $hanghoa_cha->ma;
            $model->ten = $hanghoa_cha->ten;
            $model->donvitinh = $hanghoa_cha->donvitinh;
            $model->male = $hanghoa_con->ma;
            $model->tenle = $hanghoa_con->ten;
            $model->donvile = $hanghoa_con->donvitinh;
        }

        return $models;
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $mamoi = $request->mamoi;
        $gianhap = $request->gianhap;
        $dongia = $request->dongia;
        $donvitinh = $request->donvitinh;
        $quycach = $request->quycach;
        $nhom = $request->nhom;
        $ghichu = $request->ghichu ?? null;
        $hoatchat = $request->hoatchat ?? null;
        $congdung = $request->congdung ?? null;
        $lieuluong = $request->lieuluong ?? null;
        $dang = $request->dang ?? null;
        $id_cha = $request->id_cha ?? null;


        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten'
            ];
        }
        if (($gianhap < 0 || $gianhap == '') && $id_cha == null) {
            return [
                'succ' => 0,
                'type' => 'gianhap'
            ];
        }
        if ($id_cha != null) {
            if ($request->quydoi <= 0) {
                return [
                    'succ' => 0,
                    'type' => 'soluong_quydoi',
                ];
            }
        }
        if ($dongia < 0 || $dongia == '') {
            return [
                'succ' => 0,
                'type' => $id_cha == null ? 'dongia' : 'giaquydoi'
            ];
        }

        if (HangHoa::whereRaw("binary lower(ten) = '".mb_strtolower($ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên hàng hóa đã tồn tại!'
            ];
        }

        DB::beginTransaction();

        $model = new HangHoa();
        $model->ten = $ten;
        $model->mamoi = $mamoi ?? null;
        $model->slug = Funcs::convertToSlug($ten);
        $model->donvitinh = $donvitinh;
        $model->gianhap = $gianhap;
        $model->ghichu = $ghichu;
        $model->nhom = $nhom;
        $model->quycach = $quycach;
        $model->hoatchat = $hoatchat;
        $model->congdung = $congdung;
        $model->lieuluong = $lieuluong;
        $model->dang = $dang;
        $model->deleted_at = null;
        if ($id_cha != null) {
            $hanghoa_cha = HangHoa::find($id_cha);
            if ($hanghoa_cha == null) {
                DB::rollBack();
                return [
                    'succ' => 0,
                    'noti' => 'Hàng hóa quy đổi không tồn tại hoặc đã bị xóa!',
                ];
            }
            $model->gianhap = 0;
            $model->quycach = $hanghoa_cha->quycach;
            $model->nhom = $hanghoa_cha->nhom;
            $model->hoatchat = $hanghoa_cha->hoatchat;
            $model->congdung = $hanghoa_cha->congdung;
            $model->dang = $hanghoa_cha->dang;
            $model->is_quydoi = 1;
        }

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Thêm hàng hóa mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        $chinhanhs = ChiNhanh::all('id');
        $chitiets = [];
        foreach($chinhanhs as $chinhanh) {
            $chitiets[] = [
                'hanghoa_ma' => $model->ma,
                'hanghoa_id' => $model->id,
                'chinhanh_id' => $chinhanh->id,
                'dongia' => $dongia,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ];
        }

        try {
            DB::table('hanghoa_chitiet')->insert($chitiets);
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Thêm tồn kho và giá bán thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        if ($id_cha != null) {
            try {
                DB::table('danhmuc_quydoi')->insert([
                    'id_cha' => $id_cha,
                    'id_con' => $model->id,
                    'soluong' => $request->quydoi,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            catch (QueryException $exception) {
                DB::rollBack();
                return [
                    'succ' => 0,
                    'noti' => 'Thêm quy đổi thất bại. Vui lòng thử lại sau!',
                    'mess' => $exception->getMessage()
                ];
            }
        }

        Funcs::capNhatTonKho($id_cha,Funcs::getChiNhanhByToken($request->cookie('token')));

        DB::commit();
        return [
            'succ' => 1,
            'noti' => $id_cha != null ? 'Thêm quy đổi thành công.' : 'Thêm hàng hóa mới thành công.',
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

        if ($field == 'ten') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Tên hàng hóa không được bỏ trống!'
                ];
            }
            else {
                if (HangHoa::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")
                        ->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên hàng hóa đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'gianhap' && ($value < 0 || $value == '')) {
            return [
                'succ' => 0,
                'erro' => 'Giá nhập không hợp lệ!'
            ];
        }
        if ($field == 'dongia' && ($value < 0 || $value == '')) {
            return [
                'succ' => 0,
                'erro' => 'Giá bán không hợp lệ'
            ];
        }

        $model = HangHoa::withTrashed()->find($id);
        $model->$field = $value;
        if ($field == 'ten') {
            $model->slug = Funcs::convertToSlug($value);
        }

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin hàng hóa thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin hàng hóa thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function capnhat_quydoi(Request $request) {
        $id = $request->id ?? null;
        $value = $request->value ?? '';

        if ($id === null || $value === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if ($value <= 0) {
            return [
                'succ' => 0,
                'erro' => 'Số lượng quy đổi không hợp lệ!'
            ];
        }

        $model = QuyDoi::find($id);
        $model->soluong = $value;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin quy đổi thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin quy đổi thất bại. Vui lòng thử lại sau!'
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

        DB::beginTransaction();

        $model = HangHoa::find($id);
        $type = QuyDoi::where('id_con',$id)->count() > 0 ? 'con' : (QuyDoi::where('id_cha',$id)->count() > 0 ? 'cha' : 'none');

        try {
            if ($type == 'con') {
                $model->forceDelete();
                QuyDoi::where('id_con',$id)->forceDelete();
            }
            else {
                $model->delete();
                if ($type == 'cha') {
                    $id_cons = QuyDoi::where('id_cha',$id)->pluck('id_con');
                    HangHoa::whereIn('id',$id_cons)->forceDelete();
                    QuyDoi::where('id_cha',$id)->forceDelete();
                }
            }
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin hàng hóa thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        try {
            $model->chiTiets()->forceDelete();
            if ($type == 'cha') {
                HangHoaChiTiet::whereIn('hanghoa_id',$id_cons)->forceDelete();
            }
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin hàng hóa thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at,
                    'id_cons' => $id_cons ?? []
                ]
            ];
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin chi tiết hàng hóa thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }
    }

    public function phuc_hoi(Request $request) {
        $id = $request->id ?? null;
        $dongia = $request->dongia;

        if ($dongia < 0 || $dongia == '') {
            return [
                'succ' => 0,
                'type' => 'dongia'
            ];
        }

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }


        DB::beginTransaction();

        $model = HangHoa::withTrashed()->find($id);

        if (HangHoa::whereRaw("binary lower(ten) = '".mb_strtolower($model->ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Tên hàng hóa đã tồn tại. Vui lòng đổi tên trước khi phục hồi!'
            ];
        }

        try {
            $model->restore();
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin hàng hóa thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        $chinhanhs = ChiNhanh::all('id');
        $chitiets = [];
        foreach($chinhanhs as $chinhanh) {
            $chitiets[] = [
                'hanghoa_ma' => $model->ma,
                'hanghoa_id' => $model->id,
                'chinhanh_id' => $chinhanh->id,
                'dongia' => $dongia,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ];
        }

        try {
            DB::table('hanghoa_chitiet')->insert($chitiets);
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Thêm tồn kho và giá bán thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Phục hồi thông tin hàng hóa thành công.',
        ];
    }

    public function tim_kiem(Request $request) {
        $q = Funcs::convertToSlug($request->q);

        $is_quydoi = false;
        if (isset($request->is_quydoi)) {
            $is_quydoi = $request->is_quydoi == 1;
        }

        $options = ['id','ma','ten','donvitinh','nhom','quycach'];

        if ($is_quydoi) {
            $models = HangHoa::where('slug','like',"%$q%")->orWhere('ma','like',"%$q%");
        }
        else {
            $models = HangHoa::whereRaw("(slug like '%$q%' or ma like '%$q%') and is_quydoi = 0");
        }

        $models = $models->limit(20)->get($options);

        foreach($models as $model) {
            $model->text = $model->ma.' - '.$model->ten;
        }

        return [
            'results' => $models
        ];
    }
}
