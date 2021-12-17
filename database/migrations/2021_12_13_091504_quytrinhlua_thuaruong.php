<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaThuaRuong extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_thuaruong', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('diachi')->nullable();
            $table->string('_diachi')->nullable();
            $table->string('xa')->nullable();
            $table->string('huyen')->nullable();
            $table->string('tinh')->nullable();
            $table->string('caytrong')->nullable();
            $table->string('dientich')->nullable();
            $table->string('nongdan_id',10);
            $table->string('nhanvien_id',10)->nullable();
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
        Schema::dropIfExists('quytrinhlua_thuaruong');
    }
}
