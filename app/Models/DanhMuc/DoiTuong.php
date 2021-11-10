<?php

namespace App\Models\DanhMuc;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DoiTuong extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_doituong';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $maxMaDT = DB::select("select max(ma) as ma from danhmuc_doituong")[0];
            if ($maxMaDT->ma == null) {
                $model->ma = 'DT000001';
            }
            else {
                $maxMaDT = (int) substr($maxMaDT->ma,2);
                $maxMaDT++;
                while (strlen((string) $maxMaDT) < 6) {
                    $maxMaDT = '0'.$maxMaDT;
                }
                $model->ma = 'DT'.$maxMaDT;
            }
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
