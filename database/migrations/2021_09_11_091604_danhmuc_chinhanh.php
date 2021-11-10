<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhmucChinhanh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_chinhanh', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('ten');
            $table->string('dienthoai');
            $table->string('dienthoai2')->nullable();
            $table->string('diachi');
            $table->string('ghichu')->nullable();
            $table->string('loai')->default('cuahang');

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
        Schema::dropIfExists('danhmuc_chinhanh');
    }
}
