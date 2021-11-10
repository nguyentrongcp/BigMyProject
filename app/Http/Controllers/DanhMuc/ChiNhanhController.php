<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\HangHoaChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChiNhanhController extends Controller
{
    public function index() {
        $chinhanhs = ChiNhanh::all(['id','ten']);

        return view('quanly.danhmuc.chinhanh.index', [
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function mobile_index() {
        $chinhanhs = ChiNhanh::where('loai','cuahang')->get(['id','ten','dienthoai','dienthoai2','diachi']);

        return view('mobile.danhmuc.chinhanh.index', [
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function danh_sach() {
        return ChiNhanh::withTrashed()->orderBy('deleted_at')->orderBy('updated_at','desc')->get();
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dienthoai = $request->dienthoai;
        $dienthoai2 = $request->dienthoai2 ?? null;
        $diachi = $request->diachi;
        $loai = $request->loai;
        $chinhanh_id = $request->chinhanh_id;
        $ghichu = $request->ghichu ?? null;

        if (in_array('',['ten','dienthoai','diachi']) !== false) {
            return [
                'succ' => 0,
                'type' => $ten == '' ? 'ten' : ($dienthoai == '' ? 'dienthoai' : 'dienthoai2')
            ];
        }

        if (ChiNhanh::whereRaw("binary lower(ten) = '".mb_strtolower($ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên cửa hàng đã tồn tại!'
            ];
        }

        DB::beginTransaction();

        $model = new ChiNhanh();
        $model->id = rand(1000000000,9999999999);
        $model->ten = $ten;
        $model->dienthoai = $dienthoai;
        $model->dienthoai2 = $dienthoai2;
        $model->diachi = $diachi;
        $model->ghichu = $ghichu;
        $model->loai = $loai;
        $model->deleted_at = null;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Thêm cửa hàng mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        if ($chinhanh_id != 'none') {
            if (ChiNhanh::find($chinhanh_id) == null) {
                DB::rollBack();
                return [
                    'succ' => 0,
                    'type' => 'chinhanh_id'
                ];
            }

            $_chitiets = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->get(['hanghoa_id','hanghoa_ma','dongia']);
            $chitiets = [];
            foreach($_chitiets as $chitiet) {
                $chitiets[] = [
                    'hanghoa_ma' => $chitiet->hanghoa_ma,
                    'hanghoa_id' => $chitiet->hanghoa_id,
                    'chinhanh_id' => $model->id,
                    'dongia' => $chitiet->dongia,
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
                    'noti' => 'Sao chép giá bán thất bại. Vui lòng thử lại!',
                    'mess' => $exception->getMessage()
                ];
            }
        }

        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Thêm cửa hàng mới thành công.',
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
                if (ChiNhanh::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên cửa hàng đã tồn tại!'
                    ];
                }
            }
        }
        if ($field == 'dienthoai' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Số điện thoại cửa hàng không được bỏ trống!'
            ];
        }

        if ($field == 'diachi' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Địa chỉ cửa hàng không được bỏ trống!'
            ];
        }

        $model = ChiNhanh::withTrashed()->find($id,['id',$field]);
        $model->$field = $value;

        if ($model->save()) {
            if ($field == 'ten') {
                event(new Pusher('doiten-chinhanh', $value));
            }

            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin cửa hàng thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin cửa hàng thất bại. Vui lòng thử lại sau!'
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

        $model = ChiNhanh::find($id);

        try {
            $model->delete();
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin cửa hàng thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        try {
            $model->chiTiets()->forceDelete();
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin cửa hàng thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at
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
        $chinhanh_id = $request->chinhanh_id;

        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu không hợp lệ!'
            ];
        }


        DB::beginTransaction();

        $model = ChiNhanh::withTrashed()->find($id);

        if (ChiNhanh::whereRaw("binary lower(ten) = '".mb_strtolower($model->ten)."'")->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Tên cửa hàng đã tồn tại. Vui lòng đổi tên trước khi phục hồi!'
            ];
        }

        try {
            $model->restore();
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin cửa hàng thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        if ($chinhanh_id != 'none') {
            if (ChiNhanh::find($chinhanh_id) == null) {
                return [
                    'succ' => 0,
                    'erro' => 'Cửa hàng này không tồn tại hoặc đã bị xóa!'
                ];
            }

            $_chitiets = HangHoaChiTiet::where('chinhanh_id',$chinhanh_id)->get(['hanghoa_id','hanghoa_ma','dongia']);
            $chitiets = [];
            foreach($_chitiets as $chitiet) {
                $chitiets[] = [
                    'hanghoa_ma' => $chitiet->hanghoa_ma,
                    'hanghoa_id' => $chitiet->hanghoa_id,
                    'chinhanh_id' => $model->id,
                    'dongia' => $chitiet->dongia,
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
                    'noti' => 'Thêm giá bán thất bại. Vui lòng thử lại sau!',
                    'mess' => $exception->getMessage()
                ];
            }
        }

        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Phục hồi thông tin cửa hàng thành công.',
        ];
    }

    public function tim_kiem(Request $request) {
        $selectAll = $request->selectAll ?? true;
        $options = ['id','ten as text','dienthoai','dienthoai2','diachi'];

        if (isset($request->type)) {
            $models = ChiNhanh::where('loai',$request->type);
        }
        else {
            $models = ChiNhanh::whereRaw('loai is not null');
        }

        if ($selectAll !== true) {
            $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));
            $models->where('id','!=',$chinhanh_id);
        }

        $models = $models->limit(20)->get($options);

        return [
            'results' => $models
        ];
    }
}
