<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\GiaiDoan;
use App\Models\QuyTrinhLua\QuyTrinh;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class GiaiDoanController extends Controller
{
    public function danh_sach(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        $results = GiaiDoan::where('muavu_id',$muavu_id)->orderBy('phanloai')->orderBy('tu')->orderBy('den')
            ->get();

        return $results;
    }

    public function them_moi(Request $request) {
        $muavu_id = $request->muavu_id ?? null;
        if ($muavu_id == null) {
            return [
                'succ' => 0,
                'noti' => 'Bạn chưa chọn mùa vụ!'
            ];
        }
        $ten = $request->ten ?? '';
        $tu = $request->tu ?? '';
        $den = $request->den ?? '';
        $phanloai = $request->phanloai ?? '';

        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten'
            ];
        }
        if ($tu == '') {
            return [
                'succ' => 0,
                'type' => 'tu'
            ];
        }
        if ($den == '') {
            return [
                'succ' => 0,
                'type' => 'den'
            ];
        }

        $model = new GiaiDoan();
        $model->ten = $ten;
        $model->tu = $tu;
        $model->den = $den;
        $model->phanloai = $phanloai;
        $model->muavu_id = $muavu_id;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm giai đoạn mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm giai đoạn mới thành công.',
            'data' => [
                'model' => $model
            ]
        ];
    }

    public function cap_nhat(Request $request) {
        $id = $request->id ?? null;
        $ten = $request->ten ?? '';
        $tu = $request->tu ?? '';
        $den = $request->den ?? '';
        $phanloai = $request->phanloai ?? '';

        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten'
            ];
        }
        if ($tu == '') {
            return [
                'succ' => 0,
                'type' => 'tu'
            ];
        }
        if ($den == '') {
            return [
                'succ' => 0,
                'type' => 'den'
            ];
        }

        $model = GiaiDoan::find($id);
        $model->ten = $ten;
        $model->tu = $tu;
        $model->den = $den;
        $model->phanloai = $phanloai;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin giai đoạn thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin giai đoạn thất bại. Vui lòng thử lại sau!'
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

        $model = GiaiDoan::find($id);

        if ($model->forceDelete()) {
            QuyTrinh::where('giaidoan_id',$id)->forceDelete();
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin giai đoạn thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at,
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin giai đoạn thất bại. Vui lòng thử lại sau!',
            ];
        }
    }
}
