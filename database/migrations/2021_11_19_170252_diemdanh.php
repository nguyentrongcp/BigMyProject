<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Diemdanh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diemdanh', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chinhanh_batdau',10)->nullable();
            $table->string('chinhanh_ketthuc',10)->nullable();
            $table->string('nhanvien_id',10);
            $table->float('ngaycong')->default(0);
            $table->date('ngay');
            $table->time('tg_batdau')->nullable();
            $table->time('tg_ketthuc')->nullable();
            $table->string('toado_batdau')->nullable();
            $table->string('toado_ketthuc')->nullable();
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
        Schema::dropIfExists('diemdanh');
    }
}
