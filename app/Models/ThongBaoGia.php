<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThongBaoGia extends Model
{
    use HasFactory;
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'thongbao_gia';

    public function hangHoa() {
        return $this->belongsTo('App\Models\DanhMuc\HangHoa','hanghoa_id');
    }

    public function getHangHoa($_option = []) {
        $option = array_merge(['id','ma','ten','donvitinh','nhom','quycach'],$_option);
        return $this->hangHoa()->withTrashed()->first($option);
    }

    public function getTenHangHoa() {
        $hanghoa = $this->hangHoa()->withTrashed()->first('ten');
        return $hanghoa == null ? 'Chưa rõ' : $hanghoa->ten;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
