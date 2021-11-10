<?php

namespace App\Models;

use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\NhaCungCap;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phieu extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'phieu';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
        });
    }

    public function khachHang() {
        return $this->belongsTo('App\Models\DanhMuc\KhachHang','doituong_id');
    }

    public function getKhachHang($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai','diachi'],$option);
        return $this->khachHang()->withTrashed()->first($option);
    }

    public function doiTuong() {
        return $this->belongsTo('App\Models\DanhMuc\DoiTuong','doituong_id');
    }

    public function getDoiTuongTC($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai','diachi'],$option);
        return $this->doiTuong()->withTrashed()->first($option);
    }

    public function nhaCungCap() {
        return $this->belongsTo('App\Models\DanhMuc\NhaCungCap','doituong_id');
    }

    public function chiNhanhNhan() {
        return $this->belongsTo('App\Models\DanhMuc\ChiNhanh','doituong_id');
    }

    public function getNhaCungCap($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai','diachi'],$option);
        return $this->nhaCungCap()->withTrashed()->first($option);
    }

    public function chiNhanh() {
        return $this->belongsTo('App\Models\DanhMuc\ChiNhanh','chinhanh_id');
    }

    public function getChiNhanh($option = []) {
        $option = array_merge(['id','ten','dienthoai','dienthoai2','diachi'],$option);
        return $this->chiNhanh()->withTrashed()->first($option);
    }

    public function khoanMuc() {
        return $this->belongsTo('App\Models\DanhMuc\KhoanMuc','khoanmuc_id');
    }

    public function getKhoanMuc() {
        $result = $this->khoanMuc()->withTrashed()->first('ten');
        return $result == null ? 'KHOẢN MỤC KHÔNG TỒN TẠI' : $result->ten;
    }

    public function getChiNhanhNhan($option = []) {
        $option = array_merge(['id','ten','dienthoai','dienthoai2','diachi'],$option);
        return $this->chiNhanhNhan()->withTrashed()->first($option);
    }

    public function phieuXuatKho() {
        return $this->belongsTo('App\Models\Phieu','doituong_id');
    }

    public function getChiNhanhXuat() {
        return $this->phieuXuatKho()->withTrashed()->first(['chinhanh_id'])->getChiNhanh();
    }

    public function getPhieuXuatKho($option = []) {
        $option = array_merge(['id','maphieu','chinhanh_id'],$option);
        return $this->phieuXuatKho()->withTrashed()->first($option);
    }

    public function nhanVien() {
        return $this->belongsTo('App\Models\DanhMuc\NhanVien','nhanvien_id');
    }

    public function getNhanVien($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai'],$option);
        return $this->nhanVien()->withTrashed()->first($option);
    }

    public function nhanVienTuVan() {
        return $this->belongsTo('App\Models\DanhMuc\NhanVien','nhanvien_tuvan_id');
    }

    public function getNhanVienTuVan($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai'],$option);
        return $this->nhanVienTuVan()->withTrashed()->first($option);
    }

    public function nhanVienDuyet() {
        return $this->belongsTo('App\Models\DanhMuc\NhanVien','nguoiduyet_id');
    }

    public function getNhanVienDuyet($option = []) {
        $option = array_merge(['id','ma','ten','dienthoai'],$option);
        return $this->nhanVienDuyet()->withTrashed()->first($option);
    }

    public function getDoiTuong($loaiphieu) {
        switch ($loaiphieu) {
            case 'BH':
            case 'KTH':
            case 'DCCNKH':
            case 'TCNKH':
                return $this->getKhachHang();
            case 'NH':
            case 'THNCC':
            case 'CCNNCC':
            case 'DCCNNCC':
                return $this->getNhaCungCap();
            case 'PT':
            case 'PC':
                return $this->getDoiTuongTC();
        }
    }

    public function chiTiets() {
        return $this->hasMany('App\Models\PhieuChiTiet','phieu_id');
    }

    public function getChiTiets($type, $_option = []) {
        switch ($type) {
            case 'NH':
                $option = ['id','chinhanh_id','hanghoa_id','soluong','hansudung','dongia','thanhtien'];
                break;
            default:
                $option = ['id','chinhanh_id','hanghoa_id','soluong','dongia','giamgia','thanhtien','quydoi','id_quydoi'];
                break;
        }
        $option = array_merge($option,$_option);
        return $this->chiTiets()->withTrashed()->get($option);
    }

    public function capNhatKhachHang($is_update = true) {
        if ($this->doituong_id == '1000000000') {
            return true;
        }
        $khachhang = KhachHang::withTrashed()->find($this->doituong_id);

        if ($khachhang != null) {
            if ($is_update) {
                $lancuoi_muahang = Phieu::where([
                    'loaiphieu' => 'BH',
                    'chinhanh_id' => $this->chinhanh_id
                ])->max('created_at');
                $khachhang->lancuoi_muahang = $lancuoi_muahang;
                $khachhang->update();
            }
            $khachhang->capNhatCongNo();
        }
        return true;
    }

    public function capNhatNhaCungCap() {
        $nhacungcap = NhaCungCap::withTrashed()->find($this->doituong_id);
        if ($nhacungcap != null) {
            $nhacungcap->capNhatCongNo();
        }
        return true;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
