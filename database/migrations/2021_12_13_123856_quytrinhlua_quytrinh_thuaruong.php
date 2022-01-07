<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaQuytrinhThuaruong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_quytrinh_thuaruong', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quytrinh_id');
            $table->integer('thuaruong_id');
            $table->tinyInteger('status')->default(1);
            $table->text('lydo')->nullable();
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
        Schema::dropIfExists('quytrinhlua_quytrinh_thuaruong');
    }
}
