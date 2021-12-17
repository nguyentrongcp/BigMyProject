<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Http\Controllers\Controller;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\MuaVuThuaRuong;
use App\Models\QuyTrinhLua\QuyTrinhSuDung;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MuaVuController extends Controller
{
    public function index() {
        $muavus = MuaVu::all(['id','ten as text']);

        return view('quanly.quytrinhlua.muavu.index', [
            'muavus' => $muavus
        ]);
    }

    public function danh_sach(Request $request) {
        if (Funcs::isPhanQuyenByToken('quy-trinh-lua.mua-vu.action',$request->cookie('token'))) {
            $results = MuaVu::withTrashed()->orderBy('deleted_at')->orderByDesc('status')->get();
        }
        else {
            $results = MuaVu::orderByDesc('status')->get();
        }

        foreach($results as $result) {
            $thuaruongs = MuaVuThuaRuong::where('muavu_id',$result->id)->pluck('thuaruong_id');
            $result->soluong_nongdan = ThuaRuong::whereIn('id',$thuaruongs)->groupBy('nongdan_id')->count();
            $result->soluong_thuaruong = count($thuaruongs);
        }

        return $results;
    }

    public function them_moi(Request $request) {
        $ma = $request->ma;
        $ten = $request->ten;
        $muavu_id = $request->muavu_id ?? null;
        $ghichu = $request->ghichu ?? null;

        if ($ma == '') {
            return [
                'succ' => 0,
                'type' => 'ma',
                'erro' => 'Mã mùa vụ không được bỏ trống!'
            ];
        }

        if ($ten == '') {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên mùa vụ không được bỏ trống!'
            ];
        }

        if (MuaVu::where('ma',$ma)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ma',
                'erro' => 'Mã mùa vụ đã tồn tại!'
            ];
        }

        if (MuaVu::where('ten',$ten)->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'ten',
                'erro' => 'Tên mùa vụ đã tồn tại!'
            ];
        }

        $model = new MuaVu();
        $model->ma = $ma;
        $model->ten = $ten;
        $model->ghichu = $ghichu ?? null;
        $model->status = 1;
        $model->ngaytao = date('Y-m-d');
        $model->deleted_at = null;

        DB::beginTransaction();
        if ($model->save()) {
            $model->soluong_nongdan = 0;
            $model->soluong_thuaruong = 0;
            if ($muavu_id != null) {
                $quytrinhs = QuyTrinhSuDung::where('muavu_id',$muavu_id)->get(['giaidoan','tu','den','sanpham_id','congdung','soluong','ghichu']);
                if (count($quytrinhs) > 0) {
                    $dataTable = [];
                    foreach($quytrinhs as $quytrinh) {
                        $dataTable[] = [
                            'id' => rand(1000000000,9999999999),
                            'giaidoan' => $quytrinh->giaidoan,
                            'tu' => $quytrinh->tu,
                            'den' => $quytrinh->den,
                            'sanpham_id' => $quytrinh->sanpham_id,
                            'congdung' => $quytrinh->congdung,
                            'soluong' => $quytrinh->soluong,
                            'ghichu' => $quytrinh->ghichu,
                            'muavu_id' => $model->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    try {
                        DB::table('quytrinhlua_quytrinh_sudung')->insert($dataTable);
                    }
                    catch (QueryException $exception) {
                        DB::commit();
                        return [
                            'succ' => 0,
                            'noti' => 'Sao chép quy trình mới thất bại. Vui lòng thử lại sau!',
                            'mess' => $exception->getMessage()
                        ];
                    }
                }
            }
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Thêm mùa vụ mới thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Thêm mùa vụ mới thất bại. Vui lòng thử lại!'
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

        if ($field == 'ma') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Mã mùa vụ không được bỏ trống!'
                ];
            }
            else {
                if (MuaVu::where('id','!=',$id)->whereRaw("binary lower(ma) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Mã mùa vụ đã tồn tại!'
                    ];
                }
            }
        }

        if ($field == 'ten') {
            if ($value === '') {
                return [
                    'succ' => 0,
                    'erro' => 'Tên mùa vụ không được bỏ trống!'
                ];
            }
            else {
                if (MuaVu::where('id','!=',$id)->whereRaw("binary lower(ten) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Tên mùa vụ đã tồn tại!'
                    ];
                }
            }
        }

        $model = MuaVu::find($id);

        $model->$field = $value;

        if ($model->save()) {
            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin mùa vụ thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin mùa vụ thất bại. Vui lòng thử lại!'
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

        $model = MuaVu::find($id);

        if ($model->delete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa thông tin mùa vụ thành công.',
                'data' => [
                    'deleted_at' => $model->deleted_at
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin mùa vụ thất bại. Vui lòng thử lại sau!'
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

        $model = MuaVu::withTrashed()->find($id);

        if ($model->restore()) {
            return [
                'succ' => 1,
                'noti' => 'Phục hồi thông tin mùa vụ thành công.',
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin mùa vụ thất bại. Vui lòng thử lại sau!'
            ];
        }
    }
}
