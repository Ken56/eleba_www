<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddresslistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresslist', function (Blueprint $table) {
            $table->increments('id')->comment('地址主键id');
            $table->string('provence')->comment('省');
            $table->string('city')->comment('市');
            $table->string('area')->comment('区');
            $table->string('detail_address')->comment('详细地址');
            $table->string('name')->comment('姓名');
            $table->string('tel')->comment('电话');
            $table->integer('member_id')->unsigned()->comment('所属人外键');
            $table->foreign('member_id')->references('id')->on('member');
            $table->engine='InnoDB';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresslist');
    }
}
