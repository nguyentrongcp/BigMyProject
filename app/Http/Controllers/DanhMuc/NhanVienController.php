<?php

namespace App\Http\Controllers\DanhMuc;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Http\Controllers\Controller;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\ChucVu;
use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\PhanQuyen;
use App\Models\QuyTrinhLua\NongDan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
    public function index() {
        $chucvus = ChucVu::all(['loai as id','loai','ten as text']);
        $chinhanhs = ChiNhanh::all(['id', 'ten as text']);

        return view('quanly.danhmuc.nhanvien.index', [
            'chucvus' => $chucvus,
            'chinhanhs' => $chinhanhs
        ]);
    }

    public function mobile_index() {
        $chucvus = ChucVu::pluck('ten','loai');

        return view('mobile.danhmuc.nhanvien.index', [
            'chucvus' => $chucvus
        ]);
    }

    public function danh_sach(Request $request) {
        if(Funcs::isPhanQuyenByToken('danh-muc.nhan-vien.action',$request->cookie('token'))) {
            $nhanviens = NhanVien::withTrashed()->orderBy('deleted_at')
                ->orderBy('updated_at','desc')->get();
        }
        else {
            $nhanviens = NhanVien::orderBy('updated_at','desc')->get();
        }

        return $nhanviens;
    }

    public function danh_sach_mobile() {
        $nhanviens = NhanVien::orderBy('chucvu')->get(['id','ten','dienthoai','chucvu']);

        return $nhanviens;
    }

    public function danhsach_phanquyen(Request $request) {
        $id = $request->id;
        $results = PhanQuyen::orderBy('stt')->get(['id','stt','ten','chucnang','loai','ghichu']);
        $phanquyens = NhanVien::find($id,'phanquyen');
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
        $nhanvien = NhanVien::find($id,['id','phanquyen','quyendacbiet','quyenloaibo','chucvu']);
        $chucvu = ChucVu::where('loai',$nhanvien->chucvu)->first('phanquyen');
        $chucvu->phanquyen = json_decode($chucvu->phanquyen) ?? [];

        $nhanvien->phanquyen = json_encode($phanquyens);
        $nhanvien->quyenloaibo = json_encode(array_diff($chucvu->phanquyen,$phanquyens));
        $nhanvien->quyendacbiet = json_encode(array_diff($phanquyens,$chucvu->phanquyen));

        if ($nhanvien->update()) {
            event(new Pusher('reload-info-'.$id,''));
            return [
                'succ' => 1,
                'noti' => 'Cập nhật phân quyền nhân viên thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật phân quyền thất bại!'
            ];
        }
    }

    public function chon_quyen(Request $request) {
        $id = $request->id;
        $quyen = $request->quyen;
        $nhanvien = NhanVien::find($id,['id','phanquyen','quyendacbiet','quyenloaibo','chucvu']);
        $chucvu = ChucVu::where('loai',$nhanvien->chucvu)->first('phanquyen');
        $chucvu->phanquyen = json_decode($chucvu->phanquyen) ?? [];
        $phanquyen = json_decode($nhanvien->phanquyen) ?? [];
        $quyendacbiet = json_decode($nhanvien->quyendacbiet) ?? [];
        $quyenloaibo = json_decode($nhanvien->quyenloaibo) ?? [];

        if (in_array($quyen,$phanquyen) === false) {
            $phanquyen[] = $quyen;
            if (in_array($quyen,$chucvu->phanquyen) === false) {
                $quyendacbiet[] = $quyen;
            }
            else {
                $quyenloaibo_new = [];
                foreach($quyenloaibo as $value) {
                    if ($value !== $quyen) {
                        $quyenloaibo_new[] = $value;
                    }
                }
                $quyenloaibo = $quyenloaibo_new;
            }
        }
        else {
            $phanquyen_new = [];
            foreach($phanquyen as $value) {
                if ($value !== $quyen) {
                    $phanquyen_new[] = $value;
                }
            }
            $phanquyen = $phanquyen_new;
            if (in_array($quyen,$chucvu->phanquyen) !== false) {
                $quyenloaibo[] = $quyen;
            }
            else {
                $quyendacbiet_new = [];
                foreach($quyendacbiet as $value) {
                    if ($quyen !== $value) {
                        $quyendacbiet_new[] = $value;
                    }
                }
                $quyendacbiet = $quyendacbiet_new;
            }
        }

        $nhanvien->quyendacbiet = json_encode($quyendacbiet);
        $nhanvien->quyenloaibo = json_encode($quyenloaibo);
        $nhanvien->phanquyen = json_encode($phanquyen);

        if ($nhanvien->update()) {
            event(new Pusher('reload-info-'.$id,''));
            return [
                'succ' => 1,
                'noti' => 'Cập nhật phân quyền nhân viên thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật phân quyền nhân viên thất bại!'
            ];
        }
    }

    public function them_moi(Request $request) {
        $ten = $request->ten;
        $dienthoai = $request->dienthoai;
        $chucvu = $request->chucvu;
        $chinhanh_id = $request->chinhanh_id ?? null;
        $ngaysinh = $request->ngaysinh;
        $ghichu = $request->ghichu ?? null;

        if (in_array('',['ten','dienthoai','ngaysinh']) !== false) {
            return [
                'succ' => 0,
                'type' => $ten == '' ? 'ten' : ($dienthoai == '' ? 'dienthoai' : 'ngaysinh')
            ];
        }

        if (NhanVien::whereRaw("binary lower(dienthoai) = '".mb_strtolower($dienthoai)."'")->count() > 0) {
            return [
                'succ' => 0,
                'type' => 'dienthoai',
                'erro' => 'Số điện thoại này đã tồn tại!'
            ];
        }

        $taikhoan = '';
        $tens = explode(' ',$ten);
        for($i=0; $i<=count($tens) - 2; $i++) {
            $taikhoan .= substr(Funcs::convertToSlug($tens[$i]),0,1);
        }
        $taikhoan .= Funcs::convertToSlug($tens[count($tens) - 1]).substr($ngaysinh,2,2);

        $model = new NhanVien();

        do {
            $model->id = rand(1000000000,9999999999);
        }
        while (NongDan::find($model->id,'id') != null);

        $model->ten = $ten;
        $model->slug = Funcs::convertToSlug($ten);
        $model->dienthoai = $dienthoai;
        $model->taikhoan = mb_strtolower($taikhoan);
        $model->chucvu = $chucvu;
        $model->chinhanh_id = $chinhanh_id;
        $model->ghichu = $ghichu;
        $model->ngaysinh = $ngaysinh;
        $model->deleted_at = null;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Thêm nhân viên mới thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Thêm nhân viên mới thành công.',
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

        if ($field == 'ten' && $value === '') {
            return [
                'succ' => 0,
                'erro' => 'Tên nhân viên không được bỏ trống!'
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
                if (NhanVien::where('id','!=',$id)->whereRaw("binary lower(dienthoai) = '".mb_strtolower($value)."'")->count() > 0) {
                    return [
                        'succ' => 0,
                        'erro' => 'Số điện thoại đã tồn tại!'
                    ];
                }
            }
        }

        if ($field == 'ngaysinh' && $value == '') {
            return [
                'succ' => 0,
                'erro' => 'Ngày sinh không được bỏ trống!'
            ];
        }

        $model = NhanVien::withTrashed()->find($id);
        $model->$field = $value;
        if ($field == 'ten' || $field == 'ngaysinh') {
            $taikhoan = '';
            $tens = explode(' ',$model->ten);
            for($i=0; $i<=count($tens) - 2; $i++) {
                $taikhoan .= substr(Funcs::convertToSlug($tens[$i]),0,1);
            }
            $taikhoan .= Funcs::convertToSlug($tens[count($tens) - 1]).substr($model->ngaysinh,2,2);
            $model->taikhoan = $taikhoan;
            if ($field === 'ten') {
                $model->slug = Funcs::convertToSlug($value);
            }
        }

        if ($field == 'chucvu') {
            $model->quyendacbiet = null;
            $model->quyenloaibo = null;
            $phanquyen = ChucVu::where('loai',$value)->first('phanquyen');
            $model->phanquyen = $phanquyen->phanquyen ?? null;
        }

        if ($model->save()) {
            event(new Pusher('reload-info-'.$model->id,''));

            return [
                'succ' => 1,
                'noti' => 'Cập nhật thông tin nhân viên thành công.',
                'data' => [
                    'model' => $model
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Cập nhật thông tin nhân viên thất bại. Vui lòng thử lại sau!'
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

        $model = NhanVien::find($id);

        try {
            $model->delete();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Xóa thông tin nhân viên thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        event(new Pusher('reload-info-'.$model->id,''));
        return [
            'succ' => 1,
            'noti' => 'Xóa thông tin nhân viên thành công.',
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
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }


        $model = NhanVien::withTrashed()->find($id);

        if (NhanVien::whereRaw("binary lower(dienthoai) = '".mb_strtolower($model->dienthoai)."'")->count() > 0) {
            return [
                'succ' => 0,
                'noti' => 'Số điện thoại đã tồn tại. Vui lòng đổi số điện thoại trước khi phục hồi!'
            ];
        }

        try {
            $model->restore();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Phục hồi thông tin nhân viên thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Phục hồi thông tin nhân viên thành công.',
        ];
    }

    public function chuyen_cua_hang(Request $request) {
        $id = $request->id ?? null;
        if ($id === null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }
        $chinhanh_id = $request->chinhanh_id;
        if (!isset($chinhanh_id)) {
            return [
                'succ' => 0,
                'noti' => 'Bạn chưa chọn cửa hàng cần chuyển!'
            ];
        }

        if (ChiNhanh::find($chinhanh_id)->count() == 0) {
            return [
                'succ' => 0,
                'noti' => 'Cửa hàng không tồn tại hoặc đã bị xóa. Vui lòng chọn cửa hàng khác!',
            ];
        }

        $model = NhanVien::find($id,['id','chinhanh_id']);

        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'ID nhân viên không hợp lệ!',
            ];
        }
        $model->chinhanh_id = $chinhanh_id;

        try {
            $model->save();
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Chuyển cửa hàng thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1
        ];
    }

    public function doi_mat_khau(Request $request) {
        $matkhau_cu = $request->matkhau_cu ?? '';
        $matkhau_moi = $request->matkhau_moi ?? '';
        $matkhau_nhaplai = $request->matkhau_nhaplai ?? '';
        $id = $request->id;

        if ($matkhau_cu == '') {
            return [
                'succ' => 0,
                'type' => 'matkhau_cu',
                'erro' => 'Mật khẩu cũ không được bỏ trống!'
            ];
        }
        if ($matkhau_moi == '') {
            return [
                'succ' => 0,
                'type' => 'matkhau_cu',
                'erro' => 'Mật khẩu moi không được bỏ trống!'
            ];
        }
        if ($matkhau_nhaplai !== $matkhau_moi) {
            return [
                'succ' => 0,
                'type' => 'matkhau_nhaplai',
                'erro' => 'Mật khẩu nhập lại không khớp!'
            ];
        }

        $model = NhanVien::find($id,['id','matkhau']);
        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if (!Hash::check($matkhau_cu,$model->matkhau)) {
            return [
                'succ' => 0,
                'type' => 'matkhau_cu',
                'erro' => 'Mật khẩu cũ không chính xác!'
            ];
        }

        $model->matkhau = Hash::make($matkhau_moi);
        if ($model->update()) {
            return [
                'succ' => 1,
                'noti' => 'Đổi mật khẩu thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Đổi mật khẩu thất bại.'
            ];
        }
    }

    public function check_matkhau_macdinh() {
        $matkhau = Funcs::getNhanVienByToken($_COOKIE['token'],['matkhau']);
        if (Hash::check('Hailuannv2021',$matkhau->matkhau)) {
            return [
                'succ' => 1,
                'data' => 0
            ];
        }
        else {
            return [
                'succ' => 1,
                'data' => 1
            ];
        }
    }

    public function reset_mat_khau(Request $request) {
        $id = $request->id ?? null;
        $matkhau = $request->matkhau ?? '';
        $model = NhanVien::find($id,['id','matkhau']);
        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if ($matkhau == '') {
            return [
                'succ' => 0,
                'erro' => 'Mật khẩu mới không được bỏ trống!'
            ];
        }

        $model->matkhau = Hash::make($matkhau);
        if($model->update()) {
            return [
                'succ' => 1,
                'noti' => 'Reset mật khẩu thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Reset mật khẩu thất bại!'
            ];
        }
    }
}
