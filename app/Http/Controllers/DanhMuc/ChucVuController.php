<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Pusher;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChucVu;
use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChucVuController extends Controller
{
    public function index() {
        return view('quanly.danhmuc.chucvu.index');
    }

    public function danh_sach() {
        return ChucVu::all(['id','loai','ten']);
    }

    public function danhsach_phanquyen(Request $request) {
        $id = $request->id;
        $results = PhanQuyen::orderBy('stt')->get(['id','stt','ten','chucnang','loai','ghichu']);
        $phanquyens = ChucVu::find($id,'phanquyen');
        if ($phanquyens == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }
        $phanquyens = json_decode($phanquyens->phanquyen) ?? [];
        foreach($results as $result) {
            if (in_array($result->id,$phanquyens) !== false) {
                $result->checked = true;
            }
        }

        return [
            'succ' => 1,
            'data' => $results
        ];
    }

    public function phan_quyen(Request $request) {
        $id = $request->id;
        $phanquyens = json_decode($request->phanquyens);
        $chucvu = ChucVu::find($id,['id','phanquyen','loai']);
        if ($chucvu == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }
        $chucvu->phanquyen = json_encode($phanquyens);

        DB::beginTransaction();
        if ($chucvu->update()) {
            $nhanviens = NhanVien::where('chucvu',$chucvu->loai)->get(['id','phanquyen','quyendacbiet','quyenloaibo']);
            foreach($nhanviens as $nhanvien) {
                $quyendacbiet = json_decode($nhanvien->quyendacbiet) ?? [];
                $quyenloaibo = json_decode($nhanvien->quyenloaibo) ?? [];
                $nhanvien->phanquyen = array_merge(array_diff($phanquyens,$quyenloaibo),array_diff($quyendacbiet,$phanquyens));
                $nhanvien->phanquyen = json_encode($nhanvien->phanquyen);
                $nhanvien->update();
            }
            event(new Pusher('reload-chucvu-'.$chucvu->loai,''));

            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Cập nhật phân quyền chức vụ thành công.'
            ];
        }
        else {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Cập nhật phân quyền chức vụ thất bại!'
            ];
        }
    }

    public function cap_nhat(Request $request) {
        $id = $request->id;
        $field = $request->field;
        $value = $request->value;
        $model = ChucVu::find($id);

        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }
        if ($field == 'ten') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Tên chức vụ không được bỏ trống!'
                ];
            }
            else {
                if (ChucVu::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên chức vụ đã tồn tại!'
                    ];
                }
            }
        }

        $model->$field = $value;

        if ($model->update()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin chức vụ thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin chức vụ thành công.'
            ];
        }
    }
}
