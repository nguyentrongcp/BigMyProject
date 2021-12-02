<?php

namespace App\Http\Controllers\DanhMuc;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc\PhanQuyen;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    public function index() {
        return view('quanly.danhmuc.phanquyen.index');
    }

    public function danh_sach() {
        return PhanQuyen::orderBy('stt')->get();
    }

    public function them_moi(Request $request) {
        $stt = $request->stt ?? '';
        $ma = $request->ma ?? '';
        $ten = $request->ten ?? '';
        $chucnang = $request->chucnang ?? '';
        $loai = $request->loai;
        $url = $request->url;
        $ghichu = $request->ghichu ?? null;

        if ($stt == '') {
            return [
                'succ' => 0,
                'type' => 'stt',
                'erro' => 'Số thứ tự không được bỏ trống!'
            ];
        }
        if ($ma == '') {
            return [
                'succ' => 0,
                'type' => 'ma',
                'erro' => 'Mã quyền không được bỏ trống!'
            ];
        }
        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên quyền không được bỏ trống!'
            ];
        }
        if ($chucnang == '') {
            return [
                'succ' => 0,
                'type' => 'chucnang',
                'erro' => 'Tên chức năng không được bỏ trống!'
            ];
        }

        if (PhanQuyen::where('stt',$stt)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'stt',
                'erro' => 'Số thứ tự đã tồn tại!'
            ];
        }

        if (PhanQuyen::where('ma',$ma)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'stt',
                'erro' => 'Mã quyền đã tồn tại!'
            ];
        }

        if (PhanQuyen::where('ten',$ten)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên quyền đã tồn tại!'
            ];
        }

        $model = new PhanQuyen();
        $model->stt = $stt;
        $model->ma = $ma;
        $model->ten = mb_strtoupper($ten);
        $model->loai = $loai;
        $model->chucnang = mb_strtoupper($chucnang);
        $model->url = mb_strtolower($url);
        $model->ghichu = $ghichu;
        $model->deleted_at = null;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm phân quyền mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm phân quyền mới thành công.',
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

        if ($field == 'stt') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Số thứ tụ không được bỏ trống!'
                ];
            }
            else {
                if (PhanQuyen::where('id','!=',$id)->whereRaw("stt = $value")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Số thứ tự đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'ma') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Mã quyền không được bỏ trống!'
                ];
            }
            else {
                if (PhanQuyen::where('id','!=',$id)->whereRaw("binary lower(ma) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Mã quyền đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'ten') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Tên quyền không được bỏ trống!'
                ];
            }
            else {
                if (PhanQuyen::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên quyền đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'chucnang' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Tên chức năng không được bỏ trống!'
            ];
        }

        $model = PhanQuyen::withTrashed()->find($id,['id',$field]);
        if ($field == 'ten' || $field == 'chucnang') {
            $value = mb_strtoupper($value);
        }
        if ($field == 'url') {
            $value = mb_strtolower($value);
        }
        $model->$field = $value == '' ? null : $value;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin phân quyền thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin phân quyền thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function xoa(Request $request) {
        $id = $request->id;
        $model = PhanQuyen::find($id);

        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if ($model->forceDelete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin phân quyền thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin phân quyền thất bại!'
            ];
        }
    }
}
