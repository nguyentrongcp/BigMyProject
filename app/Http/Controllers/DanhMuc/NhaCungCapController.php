<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\NhaCungCap;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class NhaCungCapController extends Controller
{
    public function index() {
        return view('quanly.danhmuc.nhacungcap.index');
    }

    public function danh_sach(Request $request) {
        if(Funcs::isPhanQuyenByToken('danh-muc.nha-cung-cap.action',$request->cookie('token'))) {
            return NhaCungCap::withTrashed()->orderBy('deleted_at')->orderBy('updated_at','desc')->get();
        }
        else {
            return NhaCungCap::orderBy('deleted_at')->orderBy('updated_at','desc')->get();
        }
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dienthoai = $request->dienthoai;
        $dienthoai2 = $request->dienthoai2 ?? null;
        $sotaikhoan = $request->sotaikhoan ?? null;
        $sotaikhoan2 = $request->sotaikhoan2 ?? null;
        $nguoidaidien = $request->nguoidaidien ?? null;
        $chucvu = $request->chucvu ?? null;
        $diachi = $request->diachi ?? null;
        $ghichu = $request->ghichu ?? null;

        if (in_array('',['ten','dienthoai']) !== false) {
            return [
                'succ' => 0,
                'type' => $ten == '' ? 'ten' : 'dienthoai'
            ];
        }

        if (NhaCungCap::whereRaw("binary lower(ten) = '".mb_strtolower($ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên nhà cung cấp đã tồn tại!'
            ];
        }

        $model = new NhaCungCap();
        $model->ten = $ten;
        $model->slug = Funcs::convertToSlug($ten);
        $model->dienthoai = $dienthoai;
        $model->dienthoai2 = $dienthoai2;
        $model->sotaikhoan = $sotaikhoan;
        $model->sotaikhoan2 = $sotaikhoan2;
        $model->nguoidaidien = $nguoidaidien;
        $model->chucvu = $chucvu;
        $model->diachi = $diachi;
        $model->ghichu = $ghichu;
        $model->deleted_at = null;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm nhà cung cấp mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm nhà cung cấp mới thành công.',
            'data' => [
                'model' => $model
            ]
        ];
    }

    public function cap_nhat(Request $request) {
        $id = $request->id ?? null;
        $field = $request->field ?? null;
        $value = $request->value ?? '';

        if ($id === null || $field === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if ($field == 'ten') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Tên cửa hàng không được bỏ trống!'
                ];
            }
            else {
                if (NhaCungCap::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên nhà cung cấp đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'dienthoai' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Số điện thoại không được bỏ trống!'
            ];
        }

        $model = NhaCungCap::withTrashed()->find($id);
        $model->$field = $value;
        if ($field == 'ten') {
            $model->slug = Funcs::convertToSlug($value);
        }

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin nhà cung cấp thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin nhà cung cấp thất bại. Vui lòng thử lại sau!'
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

        $model = NhaCungCap::find($id);

        try {
            $model->delete();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin nhà cung cấp thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Xóa thông tin nhà cung cấp thành công.',
            'data' => [
                'deleted_at' => $model->deleted_at
            ]
        ];
    }

    public function phuc_hoi(Request $request) {
        $id = $request->id ?? null;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }

        $model = NhaCungCap::withTrashed()->find($id);

        if (NhaCungCap::whereRaw("binary lower(ten) = '".mb_strtolower($model->ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Tên nhà cung cấp đã tồn tại. Vui lòng đổi tên trước khi phục hồi!'
            ];
        }

        try {
            $model->restore();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin cửa hàng thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Phục hồi thông tin nhà cung cấp thành công.',
        ];
    }

    public function tim_kiem(Request $request) {
        $q = $request->q;
        $selectAll = $request->selectAll ?? 0;
        $selectAll = $selectAll == 0;
        $options = ['id','ma','ten','ten as text'];

        if ($selectAll) {
            $options = array_merge($options,['congno','dienthoai','dienthoai2','diachi']);
        }

        $models = NhaCungCap::where('ten','like',"%$q%")->orWhere('dienthoai','like',"%$q%")
            ->orWhere('dienthoai2','like',"%$q%")->limit(20)->get($options);

        return [
            'results' => $models
        ];
    }
}
