<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\DoiTuong;
use Illuminate\Http\Request;

class DoiTuongController extends Controller
{
    public function index() {
        return view('quanly.danhmuc.doituong.index');
    }

    public function danh_sach(Request $request) {
        $info = Funcs::getNhanVienByToken($request->cookie('token'),['id','chinhanh_id']);
        if ($info->id != '1000000000') {
            $results = DoiTuong::withTrashed()->where('chinhanh_id',$info->chinhanh_id)
                ->orderBy('deleted_at')
                ->orderBy('updated_at','desc')->get();
        }
        else {
            $results = DoiTuong::withTrashed()->orderBy('deleted_at')
                ->orderBy('updated_at','desc')->get();
        }

        return $results;
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dienthoai = $request->dienthoai;
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

        if (DoiTuong::where('dienthoai',$dienthoai)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'dienthoai',
                'erro' => 'Số điện thoại đối tượng đã tồn tại!'
            ];
        }

        $model = new DoiTUong();
        $model->ten = $ten;
        $model->slug = Funcs::convertToSlug($ten);
        $model->chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
        $model->dienthoai = $dienthoai;
        $model->diachi = $diachi ?? null;
        $model->_diachi = $_diachi ?? null;
        $model->ghichu = $ghichu ?? null;
        $model->deleted_at = null;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Thêm đối tượng mới thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Thêm đối tượng mới thất bại. Vui lòng thử lại!'
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
                'erro' => 'Tên đối tượng không được bỏ trống!'
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
                if (DoiTuong::where('id','!=',$id)->where('dienthoai',$value)->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Số điện thoại đối tượng đã tồn tại!'
                    ];
                }
            }
        }

        $model = DoiTuong::find($id);

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
        }
        else {
            $model->$field = $value;
        }

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin đối tượng thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin đối tượng thất bại. Vui lòng thử lại!'
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

        $model = DoiTuong::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin đối tượng thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin đối tượng thất bại. Vui lòng thử lại sau!'
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

        $model = DoiTuong::withTrashed()->find($id);

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin đối tượng thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin đối tượng thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function tim_kiem(Request $request) {
        $q = $request->q;

        $models = DoiTuong::where('slug','like',"%$q%")->orWhere('dienthoai','like',"%$q%")->limit(20)->get();

        foreach($models as $model) {
            $model->text = $model->dienthoai.' - '.$model->ten;
        }

        return [
            'results' => $models
        ];
    }
}
