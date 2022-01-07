<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaGiaidoanPhanhoi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_giaidoan_phanhoi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('giaidoan_id');
            $table->string('thuaruong_id');
            $table->integer('nongdan_id')->nullable();
            $table->string('nhanvien_id',10)->nullable();
            $table->text('noidung');

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
        Schema::dropIfExists('quytrinhlua_giaidoan_phanhoi');
    }
}
