<?php

namespace App\Models\QuyTrinhLua;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NongDan extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'quytrinhlua_nongdan';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $maxMaND = DB::select("select max(ma) as ma from quytrinhlua_nongdan")[0];
            if ($maxMaND->ma == null) {
                $model->ma = 'ND000001';
            }
            else {
                $maxMaND = (int) substr($maxMaND->ma,2);
                $maxMaND++;
                while (strlen((string) $maxMaND) < 6) {
                    $maxMaND = '0'.$maxMaND;
                }
                $model->ma = 'ND'.$maxMaND;
            }
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
