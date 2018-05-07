<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table='order_list';
    protected $fillable = [
        'order_code','order_birth_time','order_status','shop_id','shop_name','shop_img','provence','city','area','detail_address','name','tel','member_id',
    ];
}
