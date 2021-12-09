<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DanhmucNhanvien extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_nhanvien', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('ma',20);
            $table->string('ten');
            $table->string('slug')->nullable();
            $table->string('taikhoan',20);
            $table->string('dienthoai',11);
            $table->string('chinhanh_id',10)->nullable();
            $table->string('matkhau')->default(Hash::make('Myproject2021'));
            $table->date('ngaysinh');
            $table->text('avatar')->nullable();
            $table->string('email')->nullable();
            $table->string('diachi')->nullable();
            $table->string('cmnd',12)->nullable();
            $table->date('ngaycap')->nullable();
            $table->string('noicap')->nullable();
            $table->text('cmnd_mattruoc')->nullable();
            $table->text('cmnd_matsau')->nullable();
            $table->integer('chucvu')->default(0);
            $table->tinyInteger('is_thuviec')->default(0);
            $table->tinyInteger('is_parttime')->default(0);
            $table->integer('loai')->default(1);
            $table->text('ghichu')->nullable();
            $table->text('phanquyen')->nullable();
            $table->text('quyendacbiet')->nullable();
            $table->text('quyenloaibo')->nullable();
            $table->dateTime('xacthuc_lancuoi')->nullable();

            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->rememberToken()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('danhmuc_nhanvien');
    }
}
