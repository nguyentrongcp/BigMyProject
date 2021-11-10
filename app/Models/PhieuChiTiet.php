<?php

namespace App\Models;

use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\QuyDoi;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PhieuChiTiet extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'phieu_chitiet';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
        });
    }

    public function hangHoa() {
        return $this->belongsTo('App\Models\DanhMuc\HangHoa','hanghoa_id');
    }

    public function hangHoaQuyDoi() {
        return $this->belongsTo('App\Models\DanhMuc\HangHoa','id_quydoi');
    }

    public function Phieu() {
        return $this->belongsTo('App\Models\Phieu','phieu_id');
    }

    public function getHangHoa($_option = []) {
        $option = array_merge(['id','ma','mamoi','ten','donvitinh','nhom','quycach'],$_option);
        return $this->hangHoa()->withTrashed()->first($option);
    }

    public function getHangHoaQuyDoi($_option = []) {
        $option = array_merge(['id','ma','ten','donvitinh','nhom','quycach'],$_option);
        return $this->hangHoaQuyDoi()->withTrashed()->first($option);
    }

    public function getPhieu($_option = []) {
        $option = array_merge(['id','doituong_id','giamgia','phuthu','tienthanhtoan','nhanvien_id'],$_option);
        return $this->Phieu()->withTrashed()->first($option);
    }

    public function capNhatTonKho() {
        $tonkho = PhieuChiTiet::selectRaw('(soluong * is_tangkho)/quydoi as soluong')
            ->where('created_at','<=',date('Y-m-d H:i:s'))
            ->where([
                'hanghoa_id' => $this->hanghoa_id
            ])
            ->whereIn('phieu_id',Phieu::where([
                'chinhanh_id' => $this->chinhanh_id,
                'status' => 1
            ])->pluck('id'))
            ->get()->sum('soluong');

        $tonkho = round($tonkho,2);

        $quydois = QuyDoi::where('id_cha',$this->hanghoa_id)->get(['id_con','soluong']);

        foreach($quydois as $quydoi) {
            HangHoaChiTiet::where([
                'hanghoa_id' => $quydoi->id_con,
                'chinhanh_id' => $this->chinhanh_id
            ])->update([
                'tonkho' => $tonkho * $quydoi->soluong
            ]);
        }

        return HangHoaChiTiet::where([
            'hanghoa_id' => $this->hanghoa_id,
            'chinhanh_id' => $this->chinhanh_id
        ])->update([
            'tonkho' => $tonkho
        ]);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
