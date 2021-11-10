<?php

namespace App\Models\DanhMuc;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiNhanh extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_chinhanh';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
        });
    }

    public function nhanViens() {
        return $this->hasMany('App\Models\DanhMuc\NhanVien','chinhanh_id');
    }

    public function chiTiets() {
        return $this->hasMany('App\Models\HangHoaChiTiet', 'chinhanh_id');
    }

    public function chiTiet($hanghoa_ma, $options = null) {
        $chitiet = $this->chiTiets()->where('hanghoa_ma',$hanghoa_ma);
        return $options == null ? $chitiet->first() : $chitiet->first($options);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
