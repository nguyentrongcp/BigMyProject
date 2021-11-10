<?php

namespace App\Http\Controllers;

use App\Functions\Pusher;
use App\Models\DanhMuc\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function dang_nhap(Request $request) {
        $dienthoai = $request->dienthoai;
        $matkhau = $request->matkhau;
        $is_dangxuat = $request->is_dangxuat;

        $user = NhanVien::withTrashed()->where('dienthoai', $dienthoai)->orWhere('taikhoan',$dienthoai)->get();
        if (count($user) == 0) {
            return [
                'succ' => 0,
                'type' => 'dienthoai',
                'noti' => 'Tài khoản hoặc số điện thoại không tồn tại!'
            ];
        }
        $user = $user->whereNull('deleted_at')->first();
        if ($user == null) {
            return [
                'succ' => 0,
                'type' => 'dienthoai',
                'noti' => 'Tài khoản đã ngừng hoạt động!'
            ];
        }
        if (!Hash::check($matkhau, $user->matkhau)) {
            return [
                'succ' => 0,
                'type' => 'matkhau',
                'noti' => 'Mật khẩu không chính xác!'
            ];
        }
        if ($is_dangxuat || !isset($user->remember_token)) {
            $token = md5($user->dienthoai.time());
            $user->remember_token = $token;
        }

        $user->xacthuc_lancuoi = date('Y-m-d H:i:s');
        $user->save();

        if ($is_dangxuat) {
            event(new Pusher('change-token-'.$user->id,''));
        }

        return [
            'succ' => 1,
            'noti' => 'Đăng nhập thành công.',
            'data' => [
                'token' => $user->remember_token,
                'chinhanh_id' => $user->chinhanh_id
            ]
        ];
    }
}
