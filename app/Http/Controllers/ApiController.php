<?php

namespace App\Http\Controllers;

use App\Models\AddRessList;
use App\Models\Cart;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderGoods;
use App\SignatureHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    //商店详情
    public function shops(){

//        $api=DB::table('shops')->get();
//        foreach ($api as $val){
//            $val->distance='100';
//        }
//        return $api;

        //redis优化
        $redis=new \Redis();//开启redis
        $redis->connect('127.0.0.1');
        $data=$redis->get('shops');//获取redis的值
        if ($data===false){
            $api=DB::table('shops')->get();
            foreach ($api as $val){
                $val->distance='100';
            }
            $redis->set('shops',serialize($api),3600);//序列化
        }else{
            $api=unserialize($data);
        }
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
                    ['shop_id','=',$request->id],
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

    //注册接口
    public function regist(){

    }

    //订单短信短信
    public function sendSmsx(Request $request) {

        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIeJKBa7xYuICI";
        $accessKeySecret = "8QlZvyCKJvPWdWSwYOt14dUKtXlRmJ";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] =$request->tel;
        $tel=$params["PhoneNumbers"];

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "郑家小厨房";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_134080585";//第二个，订单提示

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => mt_rand(100000,999999),
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );

//$content->Message=='OK'默认if条件
//        if('OK'=='OK'){//666666是
//            Redis::setex('code_'.$tel,5*60,666666);
//            echo '{
//      "status": "true",
//      "message": "获取短信验证码成功"
//    }';
//        }else{
//            echo '{
//      "status": "false",
//      "message": "获取短信验证码失败或者手机号不合法"
//    }';
//        }
//        return $content;
    }

    //短信验证码
    function sendSms(Request $request) {

        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIeJKBa7xYuICI";
        $accessKeySecret = "8QlZvyCKJvPWdWSwYOt14dUKtXlRmJ";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] =$request->tel;
        $tel=$params["PhoneNumbers"];

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "郑家小厨房";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_133820009";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $code=mt_rand(100000,999999);
        $params['TemplateParam'] = Array (
            "code" => $code,
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );

//$content->Message=='OK'默认if条件
        if('OK'=='OK'){//666666是
            Redis::setex('code_'.$tel,5*60,$code);
            echo '{
      "status": "true",
      "message": "获取短信验证码成功"
    }';
        }else{
            echo '{
      "status": "false",
      "message": "获取短信验证码失败或者手机号不合法"
    }';
        }
//        return $content;
    }

    //登录验证接口
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|max:24',
            'tel' => 'required',
            'sms' => 'required',
        ],[
            'username.required'=>'用户名不为空',
            'password.required'=>'密码不为空',
            'password.max'=>'密码最大为24位',
            'tel.required'=>'手机号码不为空',
            'sms.required'=>'验证码不为空',
        ]);
        if($validator->fails()){//fails有错误就为turn
            $errors=$validator->errors();
            return ['status'=>'false','message'=>$errors->first()];//返回的结果根据接口来写
        }

        //判断对应手机号码的验证码是否正确
        if(Redis::get('code_'.$request->tel)==$request->sms){
            Member::create([
                'username'=>$request->username,
                'tel'=>$request->tel,
                'password'=>bcrypt($request->password),
            ]);
            return response()->json(['status'=>'true','message'=>'注册成功']);
        }else{
            return ['status'=>'false','message'=>'验证码错误'];
        }





    }

    //登录验证
    public function loginCheck(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|max:24',
        ],[
            'name.required'=>'用户名不为空',
            'password.required'=>'密码不为空',
            'password.max'=>'密码最大为24位',
        ]);
        if($validator->fails()){//fails有错误就为turn
            $errors=$validator->errors();
            return ['status'=>'false','message'=>$errors->first()];//返回的结果根据接口来写
//            "status": "true",
//            "message": "获取短信验证码成功"
        }
        if(Auth::attempt(['username'=>$request->name,'password'=>$request->password])){

            return response()->json(['status'=>'true','message'=>'登录成功','username'=>Auth::user()->username,'user_id'=>Auth::user()->id]);

        }else{

            return response()->json(['status'=>'false','message'=>'登录失败,请重新登录']);

        }

//        "status":"true",
//        "message":"登录成功",
//        "user_id":"1",
//        "username":"张三"
    }

    //修改密码---前端有问题先不做
    public function changePassword(Request $request){
//        /**
//         * oldPassword: 旧密码
//         * newPassword: 新密码
//         */
//        echo <<<JSON
//    {
//      "status": "true",
//      "message": "修改成功"
//    }
//JSON;
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:3',
        ],[
            'oldPassword.required'=>'旧密码不能为空',
            'newPassword.required'=>'新密码不能为空',
            'password.min'=>'密码最小为3位',
        ]);
        if($validator->fails()){//fails有错误就为turn
            $errors=$validator->errors();
            return ['status'=>'false','message'=>$errors->first()];//返回的结果根据接口来写
        }
        if(Hash::ckeck($request->oldPassword,Auth::user()->password)){//散列验证密码
            return ['status'=>'false','message'=>'密码错误'];
        }


        $password=DB::table('member')->where('password',bcrypt($request->oldPassword));
        if($password){//查得到就
            Member::where('id',Auth::user()->id)->update([
                'password'=>bcrypt($request->newPassword),
            ]);
            return ['status'=>'true','message'=>'修改成功'];
        }else{
            return ['status'=>'false','message'=>'修改失败'];
        }



    }

    //忘记密码功能
    public function forgetPassword(Request $request){
        /*
         * /**
 * tel: 手机号
 * sms: 短信验证码
 * password: 密码
         * {
      "status": "true",
      "message": "添加成功"
    }
 */
        //自定义忘记密码验证规制
        //无
        //判断手机验证码是否和redis验证码相同
        $validator = Validator::make($request->all(), [
            'tel' => 'require|regex:/^[1][3,4,5,7,8][0-9]{9}$/',
            'password' => 'required|min:3',
        ],[
            'tel.required'=>'手机号不能为空',
            'tel.regex'=>'手机号不规范',
            'password.required'=>'密码不能为空',
            'password.min'=>'密码最小为3位',
        ]);
//        if($validator->fails()){//fails有错误就为turn
//            $errors=$validator->errors();
//            return ['status'=>'false','message'=>$errors->first()];//返回的结果根据接口来写
//        }


        if(Redis::get('code_'.$request->tel)==$request->sms){//如果相同就保存到数据库
            DB::table('member')
                ->where('tel', $request->tel)
                ->update(['password' =>bcrypt($request->password)]);
            return ['status'=>'true','message'=>'密码重置成功'];
        }else{
            return ['status'=>'false','message'=>'密码重置失败'];
        }

    }

   //收货地址
    public function addressList(Request $request){
        //地址
        /*
         * [{
          "id": "1",
          "provence": "四川省",
          "city": "成都市",
          "area": "武侯区",
          "detail_address": "四川省成都市武侯区天府大道56号",
          "name": "张三",
          "tel": "18584675789"
        }, {
          "id": "2",
         "provence": "河北省",
         "city": "保定市",
         "area": "武侯区",
         "detail_address": "四川省成都市武侯区天府大道56号",
         "name": "张三",
         "tel": "18584675789"
        }]
         * */

        $res=DB::table('addresslist')->where('member_id',Auth::user()->id)->get();

        //666
        return $res;

    }

    //指定地址接口
    public function addAddress(Request $request){

        //保存到数据库
        AddRessList::create([
            'provence'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail_address'=>$request->detail_address,
            'name'=>$request->name,
            'tel'=>$request->tel,
            'member_id'=>Auth::user()->id,
        ]);

        //成功跳转
        return response()->json(['status'=>'true','message'=>'地址添加成功']);
    }

    //指定地址接口---修改回显地址
    public function address(Request $request){

        /*
         * {
      "id": "2",
     "provence": "河北省",
     "city": "保定市",
     "area": "武侯区",
     "detail_address": "四川省成都市武侯区天府大道56号",
     "name": "张三",
     "tel": "18584675789"
    }
         * */
        $res=DB::table('addresslist')->where('id',$request->id)->first();


        //显示返回结果
        return json_encode($res);
    }

    // 保存修改地址接口
    public function editAddress(Request $request){

        /*
         * /**
 * id: 地址id,
 * name: 收货人
 * tel: 联系方式
 * provence: 省
 * city: 市
 * area: 区
 * detail_address: 详细地址
 */

        AddRessList::where('id',$request->id)->update([
            'provence'=>$request->provence,
            'city'=>$request->city,
            'area'=>$request->area,
            'detail_address'=>$request->detail_address,
            'name'=>$request->name,
            'tel'=>$request->tel,
            'member_id'=>Auth::user()->id,
        ]);

        return ['status'=>'true','message'=>'修改成功'];

    }

    //保存购物车接口
    public function addCart(Request $request){
        Cart::where('member_id',Auth::user()->id)->delete();//清理所有数据
//        /**
//         * goodsList: 商品列表
//         * goodsCount: 商品数量
//         */
//        echo <<<JSON
//    {
//      "status": "true",
//      "message": "添加成功"
//    }
//JSON;


        $goodsList=$request->input()['goodsList'];
        $goodsCount=$request->input()['goodsCount'];

        foreach ($goodsList as $key=>$good){

            Cart::create([
                'menu_id'=>$good,
                'goods_count'=>$goodsCount[$key],
                'member_id'=>Auth::user()->id,
            ]);

        }

        return ['status'=>'true','message'=>'添加成功'];
    }

    //获取购物车数据接口
    public function cart(Request $request){
    /*
     * echo <<<JSON
    {
      "goods_list": [{
        "goods_id": "1",
        "goods_name": "汉堡",
        "goods_img": "http://www.homework.com/images/slider-pic2.jpeg",
        "amount": 6,
        "goods_price": 10
      },{
        "goods_id": "1",
        "goods_name": "汉堡",
        "goods_img": "http://www.homework.com/images/slider-pic2.jpeg",
        "amount": 6,
        "goods_price": 10
      }],
     "totalCost": 120
    }
JSON;
     * */
        $carts=Cart::where('member_id',Auth::user()->id)->get();

        //goods_list菜品数据
        $res['goods_list']=[];
        $res['totalCost']=0;
        foreach ($carts as $cart){
            $menus=DB::table('food_menu')->where('id',$cart->menu_id)->first();
            $res['goods_list'][]=[
                'goods_id'=>$menus->id,
                'goods_name'=>$menus->goods_name,
                'goods_img'=>$menus->goods_img,
                'amount'=>$cart->goods_count,
                'goods_price'=>$menus->goods_price,
            ];
            $res['totalCost']+=$menus->goods_price*$cart->goods_count;//计算出总价格
        }


        return json_encode($res);

    }


    //1添加订单接口
    public function addorder(Request $request){

        /*DB:transaction(function ()use($request){//开启事务订单
        });*/

            //根据上传的地址id找到member
            $addresslist=AddRessList::where('id',$request->address_id)->first();
            $carts=Cart::where('member_id',$addresslist->member_id)->get();
            foreach ($carts as $cart){
                $menu=DB::table('food_menu')->where('id',$cart->menu_id)->first();
                $shop=DB::table('shops')->where('id',$menu->shop_id)->first();
            }
            $order=Order::create([
                'order_code'=>uniqid().date('Y-m-d H:i:s',time()),
                'order_birth_time'=>time(),
                'order_status'=>0,

                'shop_id'=>$shop->id,
                'shop_name'=>$shop->shop_name,
                'shop_img'=>$shop->shop_img,

                'provence'=>$addresslist->provence,
                'city'=>$addresslist->city,
                'area'=>$addresslist->area,
                'detail_address'=>$addresslist->detail_address,
                'name'=>$addresslist->name,
                'tel'=>$addresslist->tel,
                'member_id'=>$addresslist->member_id,
            ]);

            foreach ($carts as $cart){
                $menu=DB::table('food_menu')->where('id',$cart->menu_id)->first();
                OrderGoods::create([
                    'order_id'=>$order->id,
                    'goods_id'=>$menu->id,
                    'goods_name'=>$menu->goods_name,
                    'goods_price'=>$menu->goods_price,
                    'goods_img'=>$menu->goods_img,
                    'amount'=>$cart->goods_count,
                ]);
            }

            //根据shop_id找到商家
            $users=DB::table('users')->where('shop_id',$shop->id)->first();
//        发送邮件
            Mail::send(
                'mail',//需要一个邮箱模板地址
                ['name'=>$users->name],//名字
                function ($message) use ($users){//subject是邮箱标题
                    $message->to($users->email)->subject('订单通知');
                }
            );


        $usersx=DB::table('member')->where('id',$addresslist->member_id)->first();

        $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIeJKBa7xYuICI";
        $accessKeySecret = "8QlZvyCKJvPWdWSwYOt14dUKtXlRmJ";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] =$usersx->tel;
//        $tel=$params["PhoneNumbers"];

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "郑家小厨房";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_134080585";//第二个，订单提示

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "name"=>$shop->shop_name,
//            "code" => mt_rand(100000,999999),
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );




            return ['status'=>'true','message'=>'订单添加成功',"order_id"=>$order->id];

    }
    //2// 获得指定订单接口
    public function order(Request $request){
        //传入了一个order表的id
        $order=Order::where('id',$request->id)->first();
        $goods_list=OrderGoods::where('order_id',$order->id)->get();
        $total_money=0;
        foreach ($goods_list as $goods){
            $total_money+=$goods['goods_price']*$goods['amount'];
        }
        $order['order_status']=$order['order_status']==0?'代付款':'已付款';
        $order['order_price']=$total_money;
        $order['goods_list'] = $goods_list;
        $order['order_address']=$order['provence'].$order['city'].$order['area'].$order['detail_address'];

        return $order;

    }

    //获得订单列表接口
    public function orderList()
    {
        //传入了一个order表的id
        $orders=Order::where('member_id',Auth::user()->id)->get();
        foreach ($orders as $order){
            $goods_list=OrderGoods::where('order_id',$order->id)->get();
            $total_money=0;
            foreach ($goods_list as $goods){
                $total_money+=$goods['goods_price']*$goods['amount'];
            }
            $order['order_status']=$order['order_status']==0?'代付款':'已付款';
            $order['order_price']=$total_money;
            $order['goods_list'] = $goods_list;
            $order['order_address']=$order['provence'].$order['city'].$order['area'].$order['detail_address'];
            $order['order_birth_time']=date('Y-m-d H:i',$order->order_birth_time);

        }

        return response()->json($orders);
    }

}
