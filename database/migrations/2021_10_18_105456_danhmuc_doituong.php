<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhmucDoituong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_doituong', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('ma',8);
            $table->string('ten');
            $table->string('slug');
            $table->string('dienthoai');
            $table->string('diachi')->nullable();
            $table->string('_diachi')->nullable();
            $table->string('chinhanh_id',10);
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
        Schema::dropIfExists('danhmuc_doituong');
    }
}
