<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaMuavuThuaruong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_muavu_thuaruong', function (Blueprint $table) {
            $table->increments('id');
            $table->string('muavu_id',10);
            $table->string('thuaruong_id',10);
            $table->date('ngaysa');
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
        Schema::dropIfExists('quytrinhlua_muavu_thuaruong');
    }
}
