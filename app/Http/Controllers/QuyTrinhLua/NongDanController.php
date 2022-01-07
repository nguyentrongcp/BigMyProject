<?php

namespace App\Http\Controllers\QuyTrinhLua;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Functions\QuyTrinhLuaFuncs;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\NhanVien;
use App\Models\QuyTrinhLua\MuaVu;
use App\Models\QuyTrinhLua\NongDan;
use App\Models\QuyTrinhLua\ThuaRuong;
use Illuminate\Database\QueryException;
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
            $results = NongDan::withTrashed()->orderBy('deleted_at')->get();
        }
        else {
            $results = NongDan::all();
        }

        $muavu_ketthuc = ThuaRuong::whereIn('muavu_id',MuaVu::where('status',0)->pluck('id'))
            ->groupBy('nongdan_id')->selectRaw('count(nongdan_id) as soluong, nongdan_id')->pluck('soluong','nongdan_id');
        $muavu_hoatdong = ThuaRuong::whereIn('muavu_id',MuaVu::where('status',1)->pluck('id'))
            ->groupBy('nongdan_id')->selectRaw('count(nongdan_id) as soluong, nongdan_id')->pluck('soluong','nongdan_id');

        foreach($results as $result) {
            $result->muavu_ketthuc = $muavu_ketthuc[$result->id] ?? 0;
            $result->muavu_hoatdong = $muavu_hoatdong[$result->id] ?? 0;
        }

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
        $muavus = json_decode($request->muavus);

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

        $dsmuavus = [];
        $tenmuavu = '';
        $count = 1;
        foreach($muavus as $muavu) {
            if ($muavu->muavu_id == '' || $muavu->muavu_id == null) {
                return [
                    'succ' => 0,
                    'noti' => 'Mùa vụ không hợp lệ!'
                ];
            }
            if ($muavu->ngaysa == '' || $muavu->ngaysa == null) {
                return [
                    'succ' => 0,
                    'noti' => 'Ngày sạ không được bỏ trống!'
                ];
            }

            if ($tenmuavu != $muavu->muavu_id) {
                $count = 1;
                $tenmuavu = $muavu->muavu_id;
            }
            $dsmuavus[] = (object) [
                'muavu_id' => $muavu->muavu_id,
                'ngaysa' => $muavu->ngaysa,
                'dientich' => $muavu->dientich,
                'ghichu' => $muavu->ghichu,
                'ten' => $muavu->ten.' ('.$count++.')'
            ];
        }

        DB::beginTransaction();
        $model = new NongDan();

        do {
            $model->id = rand(1000000000,9999999999);
        }
        while (NhanVien::find($model->id,'id') != null);

        $model->ma = QuyTrinhLuaFuncs::getMaNongDan();
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

        try {
            $model->save();
            $dataTable = [];
            $nhanvien_id = Funcs::getNhanVienIDByToken($request->cookie('token'));
            foreach($dsmuavus as $muavu) {
                $dataTable[] = [
                    'id' => rand(1000000000,9999999999),
                    'muavu_id' => $muavu->muavu_id,
                    'ngaysa' => $muavu->ngaysa,
                    'dientich' => $muavu->dientich,
                    'nongdan_id' => $model->id,
                    'nhanvien_id' => $nhanvien_id,
                    'ten' => $muavu->ten,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
            if (count($dataTable) > 0) {
                DB::table('quytrinhlua_thuaruong')->insert($dataTable);
            }
            $model->muavu_hoatdong = count($dataTable);
            DB::commit();
            return [
                'succ' => 1,
                'noti' => 'Thêm nông dân mới thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        catch (QueryException $exception) {
            DB::rollBack();
            return [
                'succ' => 0,
                'noti' => 'Thêm nông dân mới thất bại. Vui lòng thử lại!'
            ];
        }
    }

    public function import(Request $request) {
        $data = json_decode($request->data);
        $nhanvien_id = Funcs::getNhanVienIDByToken($request->cookie('token'));
        $import_id = $request->import_id;
        $listener = 'progress-import-'.$import_id;
        $time = time();
        $length = count($data);
        $phantram_hientai = -1;
        $errors = [];
        $ds_dienthoai = NongDan::pluck('dienthoai')->toArray();
        $muavu = MuaVu::where('status',1)->orderByDesc('ngaytao')->first(['id','ten']);

        DB::beginTransaction();

        foreach($data as $stt => $item) {
            $errorText = '';
            $spring = time() - $time;
            $tongthoigian = (int) ($spring * $length / ($stt + 1));
            $thoigian = $tongthoigian - $spring;
            if ($thoigian > 60) {
                $thoigian = ((int) ($thoigian/60)).' phút';
            }
            else {
                $thoigian .= ' giây';
            }
            $percent = (int) (($stt + 1)/$length*100);
            if ($percent != $phantram_hientai && $spring != 0) {
                event(new Pusher($listener,['percent' => $percent, 'thoigian' => $thoigian]));
                $phantram_hientai = $percent;
            }

            $checked = true;
            if ($item->ten == '') {
                $checked = false;
                $errorText = 'Tên bị bỏ trống!';
            }

            if ($item->dienthoai == '') {
                $checked = false;
                $errorText = 'Số điện thoại bị bỏ trống!';
            }

            if (in_array($item->dienthoai,$ds_dienthoai) !== false) {
                $checked = false;
                $errorText = 'Số điện thoại đã tồn tại!';
            }

            if ($checked) {
                $ngaysa1 = $item->ngaysa1 ?? null;
                $ngaysa2 = $item->ngaysa2 ?? null;
                $ngaysa3 = $item->ngaysa3 ?? null;

                $diachi = '';
                $diachi .= ($item->diachi != '' ? $item->diachi.', ' : '').($item->xa != '' ? $item->xa.', ' : '')
                    .($item->huyen != '' ? $item->huyen.', ' : '').($item->tinh ?? '');

                $model = new NongDan();
                $model->ma = QuyTrinhLuaFuncs::getMaNongDan();
                $model->ten = $item->ten;
                $model->slug = Funcs::convertToSlug($item->ten);
                $model->danhxung = $item->danhxung;
                $model->dienthoai = $item->dienthoai;
                $model->dienthoai2 = $item->dienthoai2;
                $model->diachi = $diachi ?? null;
                $model->_diachi = $item->diachi ?? null;
                $model->xa = $item->xa ?? null;
                $model->huyen = $item->huyen ?? null;
                $model->tinh = $item->tinh ?? null;
                $model->ghichu = $item->ghichu ?? null;

                try {
                    $model->save();
                    if ($muavu != null) {
                        if (Funcs::validateDate($ngaysa1,'d-m-Y')) {
                            try {
                                DB::table('quytrinhlua_thuaruong')->insert([
                                    'dientich' => $item->dientich1 ?? null,
                                    'nongdan_id' => $model->id,
                                    'ten' => $muavu->ten.' (1)',
                                    'muavu_id' => $muavu->id,
                                    'ngaysa' => date('Y-m-d',strtotime($ngaysa1)),
                                    'ghichu' => $item->ghichu1 ?? null,
                                    'nhanvien_id' => $nhanvien_id,
                                    'created_at' => $model->created_at,
                                    'updated_at' => $model->updated_at
                                ]);
                            }
                            catch (QueryException $exception) {
                                $errorText .= $errorText == '' ? '' : '; ';
                                $errorText .= 'Lỗi thêm thửa ruộng 1';
                            }
                        }
                        elseif (isset($item->ngaysa1)) {
                            $errorText .= $errorText == '' ? '' : '; ';
                            $errorText .= 'Lỗi định dạng thửa ruộng 1';
                        }
                        if (Funcs::validateDate($ngaysa2,'d-m-Y')) {
                            try {
                                DB::table('quytrinhlua_thuaruong')->insert([
                                    'dientich' => $item->dientich2 ?? null,
                                    'nongdan_id' => $model->id,
                                    'ten' => $muavu->ten.' (2)',
                                    'muavu_id' => $muavu->id,
                                    'ngaysa' => date('Y-m-d',strtotime($ngaysa2)),
                                    'ghichu' => $item->ghichu2 ?? null,
                                    'nhanvien_id' => $nhanvien_id,
                                    'created_at' => $model->created_at,
                                    'updated_at' => $model->updated_at
                                ]);
                            }
                            catch (QueryException $exception) {
                                $errorText .= $errorText == '' ? '' : '; ';
                                $errorText .= 'Lỗi thêm thửa ruộng 2';
                            }
                        }
                        elseif (isset($item->ngaysa2)) {
                            $errorText .= $errorText == '' ? '' : '; ';
                            $errorText .= 'Lỗi định dạng thửa ruộng 2';
                        }
                        if (Funcs::validateDate($ngaysa3,'d-m-Y')) {
                            try {
                                DB::table('quytrinhlua_thuaruong')->insert([
                                    'dientich' => $item->dientich3 ?? null,
                                    'nongdan_id' => $model->id,
                                    'ten' => $muavu->ten.' (3)',
                                    'muavu_id' => $muavu->id,
                                    'ngaysa' => date('Y-m-d',strtotime($ngaysa3)),
                                    'ghichu' => $item->ghichu3 ?? null,
                                    'nhanvien_id' => $nhanvien_id,
                                    'created_at' => $model->created_at,
                                    'updated_at' => $model->updated_at
                                ]);
                            }
                            catch (QueryException $exception) {
                                $errorText .= $errorText == '' ? '' : '; ';
                                $errorText .= 'Lỗi thêm thửa ruộng 3';
                            }
                        }
                        elseif (isset($item->ngaysa3)) {
                            $errorText .= $errorText == '' ? '' : '; ';
                            $errorText .= 'Lỗi định dạng thửa ruộng 3';
                        }
                    }
                }
                catch (QueryException $exception) {
                    $errorText .= $errorText == '' ? '' : '; ';
                    $errorText .= 'Lỗi thêm thông tin nông dân';
                }
            }
            if ($errorText != '') {
                $item->error = $errorText;
                $errors[] = $item;
            }
        }
        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Kết nhập dữ liệu thành công.',
            'data' => [
                'errors' => $errors
            ]
        ];
    }

    public function import2(Request $request) {
        $data = json_decode($request->data);
        $nhanvien_id = Funcs::getNhanVienIDByToken($request->cookie('token'));
        $import_id = $request->import_id;
        $listener = 'progress-import-'.$import_id;
        $time = time();
        $length = count($data);
        $phantram_hientai = -1;
        $errors = [];
        $ds_dienthoai = NongDan::pluck('dienthoai')->toArray();
        DB::beginTransaction();
        $table_nongdan = [];
        $table_thuaruong = [];
        $muavu = MuaVu::where('status',1)->orderByDesc('ngaytao')->first(['id','ten']);
        foreach($data as $stt => $item) {
            $errorText = '';
            $spring = time() - $time;
            $tongthoigian = (int) ($spring * $length / ($stt + 1));
            $thoigian = $tongthoigian - $spring;
            if ($thoigian > 60) {
                $thoigian = ((int) ($thoigian/60)).' phút';
            }
            else {
                $thoigian .= ' giây';
            }
            $percent = (int) (($stt + 1)/$length*100);
            if ($percent != $phantram_hientai && $spring != 0) {
                event(new Pusher($listener,['percent' => $percent, 'thoigian' => $thoigian]));
                $phantram_hientai = $percent;
            }

            $checked = true;
            if ($item->ten == '') {
                $checked = false;
                $errorText = 'Tên bị bỏ trống!';
            }

            if ($item->dienthoai == '') {
                $checked = false;
                $errorText = 'Số điện thoại bị bỏ trống!';
            }

            if (in_array($item->dienthoai,$ds_dienthoai) !== false) {
                $checked = false;
                $errorText = 'Số điện thoại đã tồn tại!';
            }

            if ($checked) {
                $ngaysa1 = $item->ngaysa1 ?? null;
                $ngaysa2 = $item->ngaysa2 ?? null;
                $ngaysa3 = $item->ngaysa3 ?? null;
                $ds_dienthoai[] = $item->dienthoai;

                $id = rand(1000000000,9999999999);
                $diachi = '';
                $diachi .= ($item->diachi != '' ? $item->diachi.', ' : '').($item->xa != '' ? $item->xa.', ' : '')
                    .($item->huyen != '' ? $item->huyen.', ' : '').($item->tinh ?? '');

                $table_nongdan[] = [
                    'id' => $id,
                    'ten' => $item->ten,
                    'slug' => Funcs::convertToSlug($item->ten),
                    'danhxung' => $item->danhxung,
                    'dienthoai' => $item->dienthoai,
                    'dienthoai2' => $item->dienthoai2,
                    'diachi' => $diachi == '' ? null : $diachi,
                    '_diachi' => $item->diachi ?? null,
                    'xa' => $item->xa ?? null,
                    'huyen' => $item->huyen ?? null,
                    'tinh' => $item->tinh ?? null,
                    'ghichu' => $item->ghichu ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if ($muavu != null) {
                    if (Funcs::validateDate($ngaysa1,'d-m-Y')) {
                        $table_thuaruong[] = [
                            'id' => rand(1000000000,9999999999),
                            'dientich' => $item->dientich1 ?? null,
                            'nongdan_id' => $id,
                            'ten' => $muavu->ten.' 1',
                            'muavu_id' => $muavu->id,
                            'ngaysa' => date('Y-m-d',strtotime($ngaysa1)),
                            'ghichu' => $item->ghichu1 ?? null,
                            'nhanvien_id' => $nhanvien_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    elseif (isset($item->ngaysa1)) {
                        $errorText .= $errorText == '' ? '' : '; ';
                        $errorText .= 'Lỗi định dạng thửa ruộng 1';
                    }
                    if (Funcs::validateDate($ngaysa2,'d-m-Y')) {
                        $table_thuaruong[] = [
                            'id' => rand(1000000000,9999999999),
                            'dientich' => $item->dientich2 ?? null,
                            'nongdan_id' => $id,
                            'ten' => $muavu->ten.' 2',
                            'muavu_id' => $muavu->id,
                            'ngaysa' => date('Y-m-d',strtotime($ngaysa2)),
                            'ghichu' => $item->ghichu2 ?? null,
                            'nhanvien_id' => $nhanvien_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    elseif (isset($item->ngaysa2)) {
                        $errorText .= $errorText == '' ? '' : '; ';
                        $errorText .= 'Lỗi định dạng thửa ruộng 2';
                    }
                    if (Funcs::validateDate($ngaysa3,'d-m-Y')) {
                        $table_thuaruong[] = [
                            'id' => rand(1000000000,9999999999),
                            'dientich' => $item->dientich3 ?? null,
                            'nongdan_id' => $id,
                            'ten' => $muavu->ten.' 3',
                            'muavu_id' => $muavu->id,
                            'ngaysa' => date('Y-m-d',strtotime($ngaysa3)),
                            'ghichu' => $item->ghichu3 ?? null,
                            'nhanvien_id' => $nhanvien_id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    elseif (isset($item->ngaysa3)) {
                        $errorText .= $errorText == '' ? '' : '; ';
                        $errorText .= 'Lỗi định dạng thửa ruộng 3';
                    }
                }
            }
            if ($errorText != '') {
                $item->error = $errorText;
                $errors[] = $item;
            }
        }
        DB::table('quytrinhlua_nongdan')->insert($table_nongdan);
        DB::table('quytrinhlua_thuaruong')->insert($table_thuaruong);
//        DB::commit();
        return [
            'succ' => 1,
            'noti' => 'Kết nhập dữ liệu thành công.',
            'data' => [
                'errors' => $errors
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
