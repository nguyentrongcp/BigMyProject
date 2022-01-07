<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaQuytrinh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_quytrinh', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tu');
            $table->integer('den');
            $table->text('congdung')->nullable();
            $table->integer('giaidoan_id');
            $table->string('giaidoan');
            $table->string('phanloai');
            $table->integer('sanpham_id');
            $table->decimal('soluong',8,2);
            $table->integer('muavu_id');
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
        Schema::dropIfExists('quytrinhlua_quytrinh');
    }
}
