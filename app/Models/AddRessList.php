<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddRessList extends Model
{
    //
    protected $table='addresslist';
    protected $fillable = [
        'provence','city','area','detail_address','name','tel','member_id',
    ];
}
