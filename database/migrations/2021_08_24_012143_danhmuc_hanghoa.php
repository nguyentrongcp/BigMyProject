<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhmucHanghoa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('danhmuc_hanghoa', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('old_id',8)->nullable();
            $table->string('mamoi')->nullable();
            $table->string('ma',8);
            $table->string('ten');
            $table->string('slug');
            $table->string('donvitinh');
            $table->integer('quycach')->default(1);
            $table->string('nhom')->nullable();
            $table->string('gianhap')->default(0);
            $table->tinyInteger('is_quydoi')->default(0);
            $table->string('dang')->nullable();
            $table->text('congdung')->nullable();
            $table->text('hoatchat')->nullable();
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
        Schema::dropIfExists('danhmuc_hanghoa');
    }
}
