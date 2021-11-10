<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhmucNhacungcap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_nhacungcap', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('ma',8);
            $table->string('ten');
            $table->string('slug');
            $table->string('dienthoai');
            $table->string('dienthoai2')->nullable();
            $table->string('sotaikhoan')->nullable();
            $table->string('sotaikhoan2')->nullable();
            $table->string('nguoidaidien')->nullable();
            $table->string('chucvu')->nullable();
            $table->string('diachi')->nullable();
            $table->double('congno',12,0)->default(0);
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
        Schema::dropIfExists('danhmuc_nhacungcap');
    }
}
