<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaThongbao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_thongbao', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nongdan_id');
            $table->string('nhanvien_id',10)->nullable();
            $table->integer('giaidoan_id')->nullable();
            $table->integer('quytrinh_id')->nullable();
            $table->integer('muavu_id')->nullable();
            $table->integer('sanpham_id')->nullable();
            $table->integer('phanhoi_id')->nullable();
            $table->string('loai');
            $table->string('tieude');
            $table->tinyInteger('is_viewed')->default(0);
            $table->text('noidung')->nullable();

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
        Schema::dropIfExists('quytrinhlua_thongbao');
    }
}
