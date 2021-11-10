<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThongbaoGia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thongbao_gia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chinhanh_id',10);
            $table->string('nhanvien_id',10);
            $table->string('hanghoa_id',10);
            $table->string('hanghoa_ma',8);
            $table->double('giacu',12,0);
            $table->double('giamoi',12,0);

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
        Schema::dropIfExists('thongbao');
    }
}
