<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    //商店详情
    public function shops(){
        $api=DB::table('shops')->get();
//        $val=0;
        foreach ($api as $val){
            $val->distance='100';
        }
//        dd($table);die;
        return $api;
    }

    //
    public function shop(Request $request){
//        var_dump($api);die;
        $shop=DB::table('shops')->where('id',$request->id)->first();
//    dd($shop);
        $shop->evaluate=[
            ["user_id"=> 12344,
                "username"=> "w******k",
                "user_img"=>"http://www.homework.com/images/slider-pic4.jpeg",
                "time"=> "2017-2-22",
                "evaluate_code"=> 1,
                "send_time"=> 30,
                "evaluate_details"=> "不怎么好吃"
            ]
        ];

        $food_c=DB::table('food_category')->where('shop_id',$request->id)->get();
        foreach ($food_c as $f){
            $food_menu=DB::table('food_menu')->where([
                    ['category_id','=',$f->id],
                    ['shop_id','=',$request->id]
                ]
            )->get();
            foreach ($food_menu as $m){
                $m->goods_id = $m->id;
                $f->goods_list[] = $m;
            }

        }
        $shop->commodity=$food_c;
        return json_encode($shop);







    }



}
