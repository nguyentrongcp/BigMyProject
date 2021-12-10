<?php

namespace App\Models\DanhMuc;

use App\Models\Phieu;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class KhachHang extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_khachhang';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            if ($model->is_nongdan) {
                $maxMaKH = DB::select("select max(ma) as ma from danhmuc_khachhang where is_nongdan=1")[0];
            }
            else {
                $maxMaKH = DB::select("select max(ma) as ma from danhmuc_khachhang where is_nongdan=0")[0];
            }
            if ($maxMaKH->ma == null) {
                $model->ma = $model->is_nongdan ? 'ND000001' : 'KH000001';
            }
            else {
                $maxMaKH = (int) substr($maxMaKH->ma,2);
                $maxMaKH++;
                while (strlen((string) $maxMaKH) < 6) {
                    $maxMaKH = '0'.$maxMaKH;
                }
                $model->ma = $model->is_nongdan ? 'ND'.$maxMaKH : 'KH'.$maxMaKH;
            }
        });
    }

    public function capNhatCongNo() {
        $congno = Phieu::where([
            'doituong_id' => $this->id,
            'loaiphieu' => 'BH'
        ])->where('tienthua','<',0)->sum('tienthua');
        $dieuchinh = Phieu::where([
            'doituong_id' => $this->id,
            'loaiphieu' => 'DCCNKH'
        ])->sum('tienthanhtoan');
        $dathu = Phieu::where([
            'doituong_id' => $this->id,
            'loaiphieu' => 'TCNKH'
        ])->sum('tienthanhtoan');

        $this->congno = $dieuchinh - $congno - $dathu;

        return $this->update();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
