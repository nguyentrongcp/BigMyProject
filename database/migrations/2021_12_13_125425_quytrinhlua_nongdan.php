<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class QuytrinhluaNongdan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quytrinhlua_nongdan', function (Blueprint $table) {
            $table->string('id',10)->primary();
            $table->string('ma',8);
            $table->string('ten');
            $table->string('slug');
            $table->string('danhxung');
            $table->string('dienthoai');
            $table->string('dienthoai2')->nullable();
            $table->string('matkhau')->default(Hash::make('Hailuannv2021'));
            $table->string('diachi')->nullable();
            $table->string('_diachi')->nullable();
            $table->string('xa')->nullable();
            $table->string('huyen')->nullable();
            $table->string('tinh')->nullable();
            $table->text('ghichu')->nullable();

            $table->dateTime('xacthuc_lancuoi')->nullable();
            $table->rememberToken()->nullable();

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
        Schema::dropIfExists('quytrinhlua_nongdan');
    }
}
