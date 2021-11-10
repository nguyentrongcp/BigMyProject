<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PhieuChitiet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phieu_chitiet', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('phieu_id',10);
            $table->string('maphieu');
            $table->string('loaiphieu');
            $table->string('hanghoa_id',10);
            $table->string('hanghoa_ma',8);
            $table->double('gianhap',12,0)->nullable();
            $table->double('dongia',12,0)->nullable();
            $table->decimal('soluong',8,2);
            $table->double('giamgia',12,0)->nullable();
            $table->double('thanhtien',12,0)->nullable();
            $table->integer('quydoi')->default(1);
            $table->string('id_quydoi',10)->nullable();
            $table->integer('sophieu');
            $table->tinyInteger('is_tangkho')->default(0)->comment('1 là tăng kho, -1 là giảm kho');
            $table->tinyInteger('status')->default(1);
            $table->string('chinhanh_id',10);
            $table->date('hansudung')->nullable();
            $table->integer('soluong_trahang')->default(0);
            $table->string('doituong_id',10)->nullable();
            $table->date('ngay')->nullable();
            $table->time('gio')->nullable();
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
        Schema::dropIfExists('phieu_chitiet');
    }
}
