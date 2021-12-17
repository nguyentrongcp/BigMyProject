<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\NongDan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NongDanController extends Controller
{
    public function index() {
        $muavus = MuaVu::where('status',1)->get();

        return view('quanly.quytrinhlua.nongdan.index', [
            'muavus' => $muavus
        ]);
    }

    public function danh_sach(Request $request) {
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.nong-dan.action',$request->cookie('token'))) {
            $results = NongDan::withTrashed()->where('id','!=','1000000000');
        }
        else {
            $results = NongDan::where('id','!=','1000000000');
        }
        $results = $results->orderBy('deleted_at')->get();

        return $results;
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dienthoai = $request->dienthoai;
        $dienthoai2 = $request->dienthoai2 ?? null;
        $danhxung = $request->danhxung;
        $_diachi = $request->_diachi;
        $xa = $request->xa ?? '';
        $huyen = $request->huyen ?? '';
        $tinh = $request->tinh ?? '';
        $diachi = '';
        $diachi .= ($_diachi != '' ? $_diachi.', ' : '').($xa != '' ? $xa.', ' : '')
            .($huyen != '' ? $huyen.', ' : '').($tinh ?? '');
        $ghichu = $request->ghichu ?? null;

        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten'
            ];
        }

        if ($dienthoai == '') {
            return [
                'succ' => 0,
                'type' => 'dienthoai'
            ];
        }

        if (NongDan::where('dienthoai',$dienthoai)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'dienthoai',
                'erro' => 'Số điện thoại nông dân đã tồn tại!'
            ];
        }

        $model = new NongDan();

        do {
            $model->id = rand(1000000000,9999999999);
        }
        while (NhanVien::find($model->id,'id') != null);

        $model->ten = $ten;
        $model->slug = Funcs::convertToSlug($ten);
        $model->danhxung = $danhxung;
        $model->dienthoai = $dienthoai;
        $model->dienthoai2 = $dienthoai2;
        $model->diachi = $diachi ?? null;
        $model->_diachi = $_diachi ?? null;
        $model->xa = $xa ?? null;
        $model->huyen = $huyen ?? null;
        $model->tinh = $tinh ?? null;
        $model->ghichu = $ghichu ?? null;
        $model->deleted_at = null;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Thêm nông dân mới thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Thêm nông dân mới thất bại. Vui lòng thử lại!'
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

        if ($field == 'ten' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Tên nông dân không được bỏ trống!'
            ];
        }

        if ($field == 'dienthoai') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Số điện thoại không được bỏ trống!'
                ];
            }
            else {
                if (NongDan::where('id','!=',$id)->where('dienthoai',$value)->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Số điện thoại nông dân đã tồn tại!'
                    ];
                }
            }
        }

        $model = NongDan::find($id);

        if ($field == 'diachi') {
            $value = json_decode($value);
            $_diachi = $value->_diachi;
            $xa = $value->xa;
            $huyen = $value->huyen;
            $tinh = $value->tinh;
            $diachi = '';
            $diachi .= ($_diachi != '' ? $_diachi.', ' : '').($xa != '' ? $xa.', ' : '')
                .($huyen != '' ? $huyen.', ' : '').($tinh ?? '');
            $model->diachi = $diachi ?? null;
            $model->_diachi = $_diachi ?? null;
            $model->xa = $xa ?? null;
            $model->huyen = $huyen ?? null;
            $model->tinh = $tinh ?? null;
        }
        else {
            $model->$field = $value;
        }

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin nông dân thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin nông dân thất bại. Vui lòng thử lại!'
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

        $model = NongDan::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin nông dân thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin nông dân thất bại. Vui lòng thử lại sau!'
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

        $model = NongDan::withTrashed()->find($id);

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin nông dân thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin nông dân thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function thong_tin(Request $request) {
        $id = $request->id;
        $khachhang = NongDan::find($id);

        if ($khachhang == null) {
            return [
                'succ' => 0,
                'noti' => 'Nông dân không tồn tại hoặc đã bị xóa!'
            ];
        }
        else {
            return [
                'succ' => 1,
                'data' => $khachhang
            ];
        }
    }

    public function tim_kiem(Request $request) {
        $q = Funcs::convertToSlug($request->q);

        $models = DB::select("
            select * from danhmuc_khachhang
            where (slug like '%$q%' or dienthoai like '%$q%') and is_nongdan = 1 and deleted_at is null
            limit 20
        ");

        foreach($models as $model) {
            $model->text = $model->dienthoai.' - '.$model->ten;
        }

        return [
            'results' => $models
        ];
    }
}
