<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaGiaidoan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_sanpham', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('muavu_id',10);
            $table->string('ten');
            $table->integer('tu');
            $table->integer('den');
            $table->string('phanloai');

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
        Schema::dropIfExists('quytrinhlua_sanpham');
    }
}
