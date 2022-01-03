<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Functions\QuyTrinhLuaFuncs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\DonViTinh;
use App\Models\DanhMuc\HangHoaNhom;
use App\Models\QuyTrinhLua\SanPham;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index() {
        $donvitinhs = DonViTinh::all('ten as text');
        $nhoms = HanghoaNhom::all('ten as text');

        return view('quanly.quytrinhlua.sanpham.index', [
            'donvitinhs' => $donvitinhs,
            'nhoms' => $nhoms
        ]);
    }

    public function danh_sach(Request $request) {
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.san-pham.action',$request->cookie('token'))) {
            $models = SanPham::withTrashed()->orderBy('deleted_at')->orderBy('updated_at','desc')->get();
        }
        else {
            $models = SanPham::orderBy('updated_at','desc')->get();
        }

        return $models;
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dongia = $request->dongia;
        $donvitinh = $request->donvitinh;
        $nhom = $request->nhom;
        $ghichu = $request->ghichu ?? null;
        $dang = $request->dang ?? null;


        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên sản phẩm không được bỏ trống!'
            ];
        }
        if ($dongia < 0 || $dongia == '') {
            return [
                'succ' => 0,
                'type' => 'dongia'
            ];
        }

        if (SanPham::whereRaw("binary lower(ten) = '".mb_strtolower($ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên sản phẩm đã tồn tại!'
            ];
        }

        $model = new SanPham();
        $model->ma = QuyTrinhLuaFuncs::getMaSanPham();
        $model->ten = $ten;
        $model->slug = Funcs::convertToSlug($ten);
        $model->donvitinh = $donvitinh;
        $model->dongia = $dongia;
        $model->ghichu = $ghichu;
        $model->nhom = $nhom;
        $model->dang = $dang;
        $model->deleted_at = null;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm sản phẩm mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm sản phẩm mới thành công.',
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
                    'erro' => 'Tên sản phẩm không được bỏ trống!'
                ];
            }
            else {
                if (SanPham::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")
                        ->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên sản phẩm đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'dongia' && ($value < 0 || $value == '')) {
            return [
                'succ' => 0,
                'erro' => 'Đơn giá không hợp lệ!'
            ];
        }

        $model = SanPham::withTrashed()->find($id);
        $model->$field = $value;
        if ($field == 'ten') {
            $model->slug = Funcs::convertToSlug($value);
        }

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin sản phẩm thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin sản phẩm thất bại. Vui lòng thử lại sau!'
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

        $model = SanPham::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin sản phẩm thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at,
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin sản phẩm thất bại. Vui lòng thử lại sau!',
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

        $model = SanPham::withTrashed()->find($id);

        if (SanPham::whereRaw("binary lower(ten) = '".mb_strtolower($model->ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Tên sản phẩm đã tồn tại. Vui lòng đổi tên trước khi phục hồi!'
            ];
        }

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin sản phẩm thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin sản phẩm thất bại. Vui lòng thử lại sau!',
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
}
