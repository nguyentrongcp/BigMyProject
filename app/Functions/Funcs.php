<?php

namespace App\Functions;

use App\Models\DanhMuc\NhanVien;
use App\Models\DanhMuc\PhanQuyen;
use App\Models\DanhMuc\QuyDoi;
use App\Models\HangHoaChiTiet;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use App\Models\QuyTrinhLua\NongDan;
use DateTime;
use Kreait\Firebase\Messaging\CloudMessage;

class Funcs
{
    public static function getTenPhieu($loaiphieu) {
        $tenphieus = [
            'TCNKH' => 'THU CÔNG NỢ KHÁCH HÀNG',
            'DCCNKH' => 'ĐIỀU CHỈNH CÔNG NỢ KHÁCH HÀNG',
            'CCNNCC' => 'THU CÔNG NỢ NHÀ CUNG CẤP',
            'BH' => 'BÁN HÀNG',
            'BHM' => 'BÁN HÀNG (TIỀN MẶT)',
            'BHN' => 'BÁN HÀNG (TIỀN NỢ)',
            'NH' => 'NHẬP HÀNG',
            'XKNB' => 'XUẤT KHO NỘI BỘ',
            'NKNB' => 'NHẬP KHO NỘI BỘ',
            'DKHH' => 'ĐẦU KỲ HÀNG HÓA',
            'KTH' => 'KHÁCH TRẢ HÀNG',
            'PT' => 'THU',
            'PC' => 'CHI'
        ];

        return $tenphieus[$loaiphieu];
    }

    public static function getSoPhieu($loaiphieu) {
        $sophieu = Phieu::withTrashed()->whereDate('created_at', date('Y-m-d'))
            ->where(['loaiphieu' => $loaiphieu])->count('sophieu');
        $sophieu = $sophieu == null ? 1 : ($sophieu + 1);

        return $sophieu < 10 ? '000'.$sophieu : ($sophieu < 100 ? '00'.$sophieu : ($sophieu < 1000 ? '0'.$sophieu : $sophieu));
    }

    public static function getSTTPhieu($loaiphieu,$chinhanh_id) {
        $sophieu = Phieu::withTrashed()->whereDate('created_at', date('Y-m-d'))
            ->where(['loaiphieu' => $loaiphieu, 'chinhanh_id' => $chinhanh_id])->count('sophieu');
        return $sophieu == null ? 1 : ($sophieu + 1);
    }

    public static function isKetSo($chinhanh_id = null) {
        if ($chinhanh_id == null) {
            $chinhanh_id = self::getChiNhanhByToken($_COOKIE['token']);
        }
        $ketso = Phieu::where([
            'loaiphieu' => 'KSCN',
            'chinhanh_id' => $chinhanh_id,
            'ngay' => date('Y-m-d')
        ])->count();

        return $ketso > 0;
    }

    public static function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }

    public static function checkToken($token) {
        $checked = NongDan::where('remember_token',$token)->count();
        return $checked ? 'nongdan' : (NhanVien::where('remember_token',$token)->count() > 0 ? 'nhanvien' : null);
    }

    public static function getNhanVienByToken($token, $columns=null) {
        return $columns == null ? NhanVien::where('remember_token',$token)->first() : NhanVien::where('remember_token',$token)->first($columns);
    }

    public static function getNhanVienIDByToken($token) {
        $nhanvien = self::getNhanVienByToken($token,['id']);
        return $nhanvien == null ? null : $nhanvien->id;
    }

    public static function getChiNhanhByToken($token) {
        $chinhanh = self::getNhanVienByToken($token,['chinhanh_id']);

        return $chinhanh == null ? null : $chinhanh->chinhanh_id;
    }

    public static function isPhanQuyenByToken($phanquyen,$token) {
        $id_phanquyen = PhanQuyen::where('ma',$phanquyen)->first('id');
        return $id_phanquyen == null ? false : (NhanVien::where('remember_token',$token)->where('phanquyen','like',"%$id_phanquyen->id%")->count() > 0);
    }

    public static function getPhanQuyenByIDPhanQuyen($ids) {
        if (!is_array($ids)) {
            $ids = json_decode($ids) ?? [];
        }
        return PhanQuyen::whereIn('id',$ids)->orderBy('stt')->pluck('ma')->toArray();
    }

    public static function getUrlPhanQuyenByIDPhanQuyen($ids) {
        if (!is_array($ids)) {
            $ids = json_decode($ids) ?? [];
        }
        return PhanQuyen::whereIn('id',$ids)->whereNotNull('url')->pluck('url')->toArray();
    }

    public static function getPhanQuyenByID($id) {
        $phanquyens = NhanVien::find($id,'phanquyen');

        return self::getPhanQuyenByIDPhanQuyen($phanquyens->phanquyen);
    }

    public static function getPhanQuyenByToken($token) {
        $phanquyens = self::getNhanVienByToken($token,['phanquyen']);

        return self::getPhanQuyenByIDPhanQuyen($phanquyens->phanquyen);
    }

    public static function checkRememberToken($token) {
        return NhanVien::where('remember_token',$token)->count() > 0;
    }

    public static function getTonKho($hanghoa_id, $chinhanh_id) {
        $tonkho = PhieuChiTiet::selectRaw('(soluong * is_tangkho)/quydoi as soluong')
            ->where('created_at','<=',date('Y-m-d H:i:s'))
            ->where([
                'hanghoa_id' => $hanghoa_id
            ])
            ->whereIn('phieu_id',Phieu::where([
                'chinhanh_id' => $chinhanh_id,
                'status' => 1
            ])->pluck('id'))
            ->get()->sum('soluong');
        return round($tonkho,2);
    }

    public static function capNhatTonKho($hanghoa_id, $chinhanh_id) {
        $tonkho = self::getTonKho($hanghoa_id,$chinhanh_id);
        $tonkho = round($tonkho,2);

        $quydois = QuyDoi::where('id_cha',$hanghoa_id)->get(['id_con','soluong']);

        foreach($quydois as $quydoi) {
            HangHoaChiTiet::where([
                'hanghoa_id' => $quydoi->id_con,
                'chinhanh_id' => $chinhanh_id
            ])->update([
                'tonkho' => $tonkho * $quydoi->soluong
            ]);
        }

        return HangHoaChiTiet::where([
            'hanghoa_id' => $hanghoa_id,
            'chinhanh_id' => $chinhanh_id
        ])->update([
            'tonkho' => $tonkho
        ]);
    }

    public static function getCongNoKhachHang($khachhang_id) {
        $congno = Phieu::where([
            'doituong_id' => $khachhang_id,
            'loaiphieu' => 'BH'
        ])->where('tienthua','<',0)->sum('tienthua');
        $dieuchinh = Phieu::where([
            'doituong_id' => $khachhang_id,
            'loaiphieu' => 'DCCNKH'
        ])->sum('tienthanhtoan');
        $dathu = Phieu::where([
            'doituong_id' => $khachhang_id,
            'loaiphieu' => 'TCNKH'
        ])->sum('tienthanhtoan');

        return $dieuchinh - $congno - $dathu;
    }

    /**
     * @param $text
     */
    public static function convertToSlug($text) {
        $slug = mb_strtolower($text);
        $slug = str_replace(['á','à','ả','ạ','ã','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ'],'a',$slug);
        $slug = str_replace(explode('|','é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ'),'e',$slug);
        $slug = str_replace(explode('|','i|í|ì|ỉ|ĩ|ị'),'i',$slug);
        $slug = str_replace(explode('|','ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ'),'o',$slug);
        $slug = str_replace(explode('|','ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự'),'u',$slug);
        $slug = str_replace(explode('|','ý|ỳ|ỷ|ỹ|ỵ'),'y',$slug);
        $slug = str_replace(explode('|','đ'),'d',$slug);
        $slug = str_replace(['[',']','`','~','!','@','#','|','$','%','^','&','*','(',')','+','=',',','.','/','?','>','<',"'",'"',':',';','_'],'',$slug);
        $slug = str_replace('-----','',$slug);
        $slug = str_replace('----','',$slug);
        $slug = str_replace('---','',$slug);
        $slug = str_replace('--','',$slug);
        $slug = str_replace('-','',$slug);
        $slug = '@'.$slug.'@';
        $slug = str_replace(['@-','-@','@',' '],'',$slug);

        return $slug;
    }

    public static function guiThongBaoMobile($title, $noidung, $token) {
        $url = "https://fcm.googleapis.com/fcm/send";
        $data_json = json_encode([
            "notification" => [
                "title" => $title,
                "body" => $noidung,
                    "sound" => "default",
                    "click_action" => "FCM_PLUGIN_ACTIVITY",
                    "icon" => "fcm_push_icon"
                ],
            "data" => [
                "title" => $title,
                "body" => $noidung,
                "sound" => "default",
                "click_action" => "FCM_PLUGIN_ACTIVITY",
                "icon" => "fcm_push_icon"
            ],
            "to" => '/topics/test',
            "priority" => "high",
            "restricted_package_name" => ""
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "authorization: key=AIzaSyBaR9RRRjI2vryqvExanefBdFfBF09siSw"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}
