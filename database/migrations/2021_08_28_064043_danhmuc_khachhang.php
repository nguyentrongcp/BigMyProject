<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhmucKhachhang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_khachhang', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('ma',8);
            $table->string('ten');
            $table->string('slug');
            $table->string('danhxung');
            $table->string('dienthoai');
            $table->string('dienthoai2')->nullable();
            $table->string('diachi')->nullable();
            $table->string('_diachi')->nullable();
            $table->string('xa')->nullable();
            $table->string('huyen')->nullable();
            $table->string('tinh')->nullable();
            $table->string('caytrong')->nullable();
            $table->string('dientich')->nullable();
            $table->string('chinhanh_id',10);
            $table->double('congno',12,0)->default(0);
            $table->dateTime('lancuoi_muahang')->nullable();
            $table->string('nhanvien_id',10)->nullable();
            $table->tinyInteger('is_nongdan')->default(0);
            $table->text('ghichu')->nullable();

            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('danhmuc_khachhang');
    }
}
