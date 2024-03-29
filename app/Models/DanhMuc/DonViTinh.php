<?php

namespace App\Models\DanhMuc;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonViTinh extends Model
{
    use HasFactory;
    use softDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'danhmuc_donvitinh';

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
