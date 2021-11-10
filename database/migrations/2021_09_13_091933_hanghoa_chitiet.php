<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HanghoaChiTiet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hanghoa_chitiet', function (Blueprint $table) {
            $table->increments('id');
            $table->string('hanghoa_ma',8);
            $table->string('hanghoa_id', 10);
            $table->string('chinhanh_id',10);
            $table->decimal('tonkho')->default(0);
            $table->double('dongia',12,0);

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
        Schema::dropIfExists('hanghoa_chitiet');
    }
}
