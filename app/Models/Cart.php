<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    protected $table='cart';
    protected $fillable = [
        'menu_id','goods_count','member_id',
    ];
}
