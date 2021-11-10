<?php

namespace App\Models\DanhMuc;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class HangHoa extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_hanghoa';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $ma = $model->is_quydoi ? 'QD' : 'HH';
            $maxMaHH= DB::select("select max(ma) as ma from danhmuc_hanghoa where ma like '$ma%'")[0];
            if ($maxMaHH->ma == null) {
                $model->ma = $ma.'000001';
            }
            else {
                $maxMaHH = (int) substr($maxMaHH->ma,2);
                $maxMaHH++;
                while (strlen((string) $maxMaHH) < 6) {
                    $maxMaHH = '0'.$maxMaHH;
                }
                $model->ma = $ma.$maxMaHH;
            }
        });
    }

    public function chiTiets() {
        return $this->hasMany('App\Models\HangHoaChiTiet', 'hanghoa_id');
    }

    public function chiTiet($chinhanh_id, $options = null) {
        $chitiet = $this->chiTiets()->where('chinhanh_id',$chinhanh_id);
        return $options == null ? $chitiet->first() : $chitiet->first($options);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
