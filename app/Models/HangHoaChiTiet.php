<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HangHoaChiTiet extends Model
{
    use HasFactory;
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'hanghoa_chitiet';

    public function chiNhanh() {
        return $this->belongsTo('App\Models\DanhMuc\ChiNhanh','chinhanh_id');
    }

    public function getChiNhanh($option = []) {
        $option = array_merge(['id','ten','dienthoai','dienthoai2','diachi'],$option);
        return $this->chiNhanh()->withTrashed()->first($option);
    }

    public function getTenChiNhanh() {
        $chinhanh = $this->chiNhanh()->withTrashed()->first('ten');
        return $chinhanh == null ? 'Chưa rõ' : $chinhanh->ten;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
