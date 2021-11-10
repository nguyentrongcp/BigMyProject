<?php

namespace App\Models\DanhMuc;

use App\Models\Phieu;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NhaCungCap extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_nhacungcap';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
            $maxMaNCC = DB::select("select max(ma) as ma from danhmuc_nhacungcap")[0];
            if ($maxMaNCC->ma == null) {
                $model->ma = 'NCC00001';
            }
            else {
                $maxMaNCC = (int) substr($maxMaNCC->ma,3);
                $maxMaNCC++;
                while (strlen((string) $maxMaNCC) < 5) {
                    $maxMaNCC = '0'.$maxMaNCC;
                }
                $model->ma = 'NCC'.$maxMaNCC;
            }
        });
    }

    public function capNhatCongNo() {
        $congno = Phieu::where([
            'doituong_id' => $this->id,
            'loaiphieu' => 'NH'
        ])->where('status',1)->sum('tienthanhtoan');
        $dachi = Phieu::whereRaw("doituong_id = '$this->id' and (loaiphieu = 'THNCC' || loaiphieu = 'TTCNNCC')")
            ->sum('tienthanhtoan');

        $this->congno = $congno - $dachi;

        return $this->update();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
