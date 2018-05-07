<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_code')->comment('订单流水号');
            $table->integer('order_birth_time')->comment('下单时间');
            $table->tinyInteger('order_status')->default(0)->comment('订单状态');
            $table->integer('shop_id')->comment('店铺id');
            $table->string('shop_name')->comment('店铺名称');
            $table->string('shop_img')->comment('店铺图片');
            $table->string('provence')->comment('省');
            $table->string('city')->comment('市');
            $table->string('area')->comment('区');
            $table->string('detail_address')->comment('详细地址');
            $table->string('name')->comment('收货人');
            $table->string('tel')->comment('联系方式');
            $table->integer('member_id')->unsigned()->comment('用户外键');
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
        Schema::dropIfExists('order_list');
    }
}
