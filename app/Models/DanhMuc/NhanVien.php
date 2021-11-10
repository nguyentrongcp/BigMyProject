<?php

namespace App\Models\DanhMuc;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NhanVien extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_nhanvien';
    protected $guarded = [];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $maxMaNV = DB::select("select max(ma) as ma from danhmuc_nhanvien")[0];
            if ($maxMaNV->ma == null) {
                $model->ma = 'NV000001';
            }
            else {
                $maxMaNV = (int) substr($maxMaNV->ma,2);
                $maxMaNV++;
                while (strlen((string) $maxMaNV) < 6) {
                    $maxMaNV = '0'.$maxMaNV;
                }
                $model->ma = 'NV'.$maxMaNV;
            }
        });
    }

    protected $hidden = [
        'matkhau',
    ];

    public function chiNhanh() {
        return $this->belongsTo('App\Models\DanhMuc\ChiNhanh','chinhanh_id');
    }

    public function setTenChiNhanh($isDelete = false) {
        $this->chinhanh_ten = 'Không có';
        if (isset($this->chinhanh_id)) {
            $chinhanh_ten = $this->chiNhanh()->first('ten');
            if ($chinhanh_ten != null) {
                $this->chinhanh_ten = $chinhanh_ten->ten;
            }
        }
        if ($isDelete) {
            unset($this->chinhanh_id);
        }
    }

    public function setChucVu() {
        $this->chucvu_ten = 'Không có';
        if (isset($this->chucvu)) {
            $chucvu_ten = ChucVu::where('loai',$this->chucvu)->first('ten');
            if ($chucvu_ten != null) {
                $this->chucvu_ten = $chucvu_ten->ten;
            }
        }
    }

    public function getTenChiNhanh() {
        $chinhanh_ten = 'Không có';
        if (isset($this->chinhanh_id)) {
            $chinhanh_ten = $this->chiNhanh()->first('ten');
            if ($chinhanh_ten != null) {
                $chinhanh_ten = $chinhanh_ten->ten;
            }
        }

        return $chinhanh_ten;
    }

    public function isChiNhanhAvailable() {
        if (isset($this->chinhanh_id)) {
            $chinhanh_ten = $this->chiNhanh()->first('ten');
            if ($chinhanh_ten != null) {
                return true;
            }
        }

        return false;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
