<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaSanpham extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_sanpham', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ma');
            $table->string('ten');
            $table->string('slug');
            $table->string('donvitinh');
            $table->string('nhom');
            $table->string('dang')->nullable();
            $table->string('xuatxu')->nullable();
            $table->text('thanhphan')->nullable();
            $table->text('congdung')->nullable();
            $table->text('hinhanh')->nullable();
            $table->double('dongia',12,0);
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
        Schema::dropIfExists('quytrinhlua_sanpham');
    }
}
