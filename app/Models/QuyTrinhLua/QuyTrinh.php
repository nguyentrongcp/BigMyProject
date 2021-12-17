<?php

namespace App\Models\QuyTrinhLua;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuyTrinh extends Model
{
    use HasFactory;
    use softDeletes;
    public $incrementing = false;
    protected $dates = ['deleted_at'];
    protected $table = 'quytrinhlua_quytrinh';

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = rand(1000000000,9999999999);
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
