<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThongbaoNhaphang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thongbao_nhaphang', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chinhanh_id',10);
            $table->string('nhanvien_id',10);
            $table->string('nhacungcap_id',10);
            $table->string('maphieu');
            $table->text('noidung');

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
        Schema::dropIfExists('thongbao_nhaphang');
    }
}
