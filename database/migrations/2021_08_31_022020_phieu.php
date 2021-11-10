<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Phieu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phieu', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('maphieu');
            $table->string('loaiphieu');
            $table->string('doituong_id',10)->nullable();
            $table->integer('khoanmuc_id')->nullable();
            $table->double('tongthanhtien', 12, 0)->default(0);
            $table->double('phuthu', 12, 0)->default(0);
            $table->double('giamgia', 12, 0)->default(0);
            $table->double('tienthanhtoan', 12, 0)->default(0);
            $table->double('tienkhachdua', 12, 0)->default(0);
            $table->double('tienthua', 12, 0)->default(0);
            $table->string('nhanvien_id',10);
            $table->string('nhanvien_tuvan_id',10)->nullable();
            $table->integer('sophieu');
            $table->integer('status')->default(1);
            $table->string('nguoiduyet_id',10)->nullable();
            $table->string('chinhanh_id',10);
            $table->date('ngay')->nullable();
            $table->time('gio')->nullable();
            $table->text('noidung')->nullable();
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
        Schema::dropIfExists('phieu');
    }
}
