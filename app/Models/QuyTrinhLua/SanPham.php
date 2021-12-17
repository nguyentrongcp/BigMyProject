<?php

namespace App\Models\QuyTrinhLua;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SanPham extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'quytrinhlua_sanpham';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $maxMaSP= DB::select("select max(ma) as ma from quytrinhlua_sanpham where ma like 'SP%'")[0];
            if ($maxMaSP->ma == null) {
                $model->ma = 'SP000001';
            }
            else {
                $maxMaSP = (int) substr($maxMaSP->ma,2);
                $maxMaSP++;
                while (strlen((string) $maxMaSP) < 6) {
                    $maxMaSP = '0'.$maxMaSP;
                }
                $model->ma = 'SP'.$maxMaSP;
            }
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
