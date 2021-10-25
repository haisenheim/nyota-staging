<?php



namespace App\Http\Controllers;



use App\Models\Order;

use App\Models\ProductAttributeType;

use App\Models\ProductAttribute;

use App\Models\ProductVariation;

use App\Models\Product;

use App\Models\OrderMaster;

use App\Models\Settings;

use App\Models\Mobilesettings;

use App\Models\User;

use App\Models\Shipping;

use App\Models\City;

use App\Models\Notification;

use Auth;

use File;

use DB;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Carbon\Carbon;

use Illuminate\Routing\UrlGenerator;

use jeremykenedy\LaravelRoles\Models\Role;

use Validator;

use Image;

use Mail;



class VendorOrderController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth');

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $user = Auth::user();

        $orders = DB::table('order')

            ->join('users', 'users.id','=','order.user_id')

            ->join('product', 'product.id','=','order.product_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->join('address', 'address.id','=','order_master.address_id')

            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id')

            ->where('order_master.status','!=',4)

            ->where('order_master.status','!=',5)

            ->where('product.user_id','=',$user->id)

            ->groupBy('order_master.id')

            ->paginate(100);

        return View('pages.vendor.order.show', compact('orders'));

    }



    public function orderhistory(){

        $user = Auth::user();

        $orderhistory = DB::table('order')

            ->join('users', 'users.id','=','order.user_id')

            ->join('product', 'product.id','=','order.product_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->join('address', 'address.id','=','order_master.address_id')

            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id')

            ->where('product.user_id','=',$user->id)

            ->where(function ($query){

            $query->where('order_master.status', '=', 4)

            ->orWhere('order_master.status', '=', 5);

            })

            ->groupBy('order_master.id')

            ->paginate(100);

         return View('pages.vendor.orderhistory.show', compact('orderhistory'));

    }



    public function orderhistoryshow($id){

        $user = Auth::user();

        $products = DB::table('product')

            ->join('order','product.id','=','order.product_id')

            ->join('users', 'users.id','=','order.user_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->join('address', 'address.id','=','order_master.address_id')

            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')

            ->select('product.*','order_master.status','users.first_name','users.city as cityid','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.payment_type')

            ->where('product.user_id','=',$user->id)

            ->where('order.order_id','=',$id)

            ->get();



            $sum = 0;

            $product_details = array();

            $settings = Settings::where('slug','currency')->first();

         foreach ($products as $key => $product) {

            $att = array();

            $medias =   DB::table('media')

                ->select('media.*')

                ->where('media.module_id',$product->id)

                ->where('media.module_type',0)

                ->first();

                if(count($medias) > 0)

                {

                    $att['image']   = $medias->image;

                }

                $att['name']    = $product->name;

                $att['odr_variant']    = $product->odr_variant;

                

                $salepricess = json_decode($product->odr_variant);

                $saleprices = json_decode(json_encode($salepricess), True);

                $att['qty'] = $saleprices[0]['quantity'];

                $pricess = explode($settings->contain,$saleprices[0]['sale_price']);

                $sub_total  = ($pricess[0] * (int)$saleprices[0]['quantity']);

                $att['total'] = $sub_total;

                $product_details[] = $att;

                foreach ($saleprices as $saleprice) {

                    $pricess = explode($settings->contain,$saleprice['sale_price']);

                    $sum += $pricess[0]*$saleprice['quantity'];

                }



        }

            $tax = Settings::where('slug','=','tax')->first();

           // $shipping_price = Settings::where('slug','=','shipping_price')->first();
$city=$product->cityid;

$shipping =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();
if(!empty($shipping)){
    $price = $shipping->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}


            $currency   = Settings::where('slug','=','currency')->first();

            $tax_val    = $sum*$tax->contain/100;

        return view('pages.vendor.orderhistory.view',compact('product_details','products','tax','tax_val','sum','currency','price'));



    }





    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

       

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     *

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

       

    }



    /**

     * Display the specified resource.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $user = Auth::user();

        $products = DB::table('product')

            ->join('order','product.id','=','order.product_id')

            ->join('users', 'users.id','=','order.user_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->join('address', 'address.id','=','order_master.address_id')

            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')

            ->select('product.*','order_master.status','users.first_name','users.city as cityid','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.estimated_delivery_date','order_master.payment_type')

            ->where('product.user_id','=',$user->id)

            ->where('order.order_id','=',$id)

            ->get();

       $sum = 0;

             $sub_total = 0;

            $product_details = array();

            $settings = Settings::where('slug','currency')->first();

         foreach ($products as $key => $product) {

            $att = array();

            $medias =   DB::table('media')

                ->select('media.*')

                ->where('media.module_id',$product->id)

                ->where('media.module_type',0)

                ->first();

                if(count($medias) > 0)

                {

                    $att['image']   = $medias->image;

                }

                $att['name']    = $product->name;

                $att['odr_variant']    = $product->odr_variant;

                $salepricess = json_decode($product->odr_variant);

                $saleprices = json_decode(json_encode($salepricess), True);

                $att['qty'] = $saleprices[0]['quantity'];



                $pricess = explode($settings->contain,$saleprices[0]['sale_price']);

                $sub_total  = ($pricess[0] * (int)$saleprices[0]['quantity']);

                $att['total'] = $sub_total;

                $product_details[] = $att;

                

               foreach ($saleprices as $saleprice) {

                    $pricess = explode($settings->contain,$saleprice['sale_price']);

                    $sum += $pricess[0]*$saleprice['quantity'];

                }

        }

            $tax = Settings::where('slug','=','tax')->first();

            //$shipping_price = Settings::where('slug','=','shipping_price')->first();
$city=$product->cityid;

$shipping =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();
if(!empty($shipping)){
    $price = $shipping->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}


            $currency = Settings::where('slug','=','currency')->first();

            $tax_val    = $sum*$tax->contain/100;

        return view('pages.vendor.order.view',compact('product_details','products','tax','tax_val','sum','currency','price'));

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

       

    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int                      $id

     *

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

    

       

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        

    }



    /**

     * Method to search the users.

     *

     * @param Request $request

     *

     * @return \Illuminate\Http\Response

     */

    public function search(Request $request)

    {

        $user = Auth::user();

        $data = $request->all();

        $searchTerm = $request->input('search_box');

        $orders = DB::table('order')

            ->join('users', 'users.id','=','order.user_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->select('order.*','order_master.status','users.first_name')

            ->where('users.id','=',$user->id)

            ->where('order_master.status','!=',4)

            ->where('order_master.status','!=',5)

            ->where(function ($query) use ($searchTerm) {

            $query->where('order_master.id', 'like','%'. $searchTerm.'%')

            ->orWhere('users.first_name', 'like','%'. $searchTerm.'%');

            })

            ->groupBy('order_master.id')

            ->paginate(100);

        return view('pages.vendor.order.show',compact('orders'));

    }



    public function searchistory(Request $request){

        

        $user = Auth::user();

        $data = $request->all();

        $searchTerm = $request->input('search_box');

        $orderhistory = DB::table('order')

            ->join('users', 'users.id','=','order.user_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->select('order.*','order_master.status','users.first_name')

            ->where('users.id','=',$user->id)

            ->where(function ($query){

            $query->where('order_master.status', '=', 4)

            ->orWhere('order_master.status', '=', 5);

            })

            ->where(function ($query) use ($searchTerm) {

            $query->where('order_master.id', '=', $searchTerm)

            ->orWhere('users.first_name', 'like','%'. $searchTerm.'%');

            })

            ->groupBy('order_master.id')

            ->paginate(100);

        return view('pages.vendor.orderhistory.show',compact('orderhistory'));

        

    }



    public function changeorderstatus($id,$order_id){

        $date = date('Y-m-d H:i:s');

        if($id == 1)

        {

            $datatwo = array('status' => $id,'order_confirmed_date' => $date);

            $product = OrderMaster::where('id',$order_id)->update($datatwo);

        }

        else if($id == 2)

        {

            $datatwo = array('status' => $id,'packed_date' => $date);

            $product = OrderMaster::where('id',$order_id)->update($datatwo);

        }

        else if($id == 3){

            $datatwo = array('status' => $id,'shipped_date' => $date);

            $product = OrderMaster::where('id',$order_id)->update($datatwo);

        }

        else if($id == 4){

            $datatwo = array('status' => $id,'delivery_date' => $date);

            $product = OrderMaster::where('id',$order_id)->update($datatwo);

        }

        else if($id == 5){

            $datatwo    = array('status' => $id,'cancel_date' => $date);

            $product    = OrderMaster::where('id',$order_id)->update($datatwo);

            $user       = OrderMaster::select('user_id')->where('id',$order_id)->first();

            $curency    = Settings::where('slug','=','currency')->first();

            $orders     = Order::where('user_id',$user->user_id)->where('order_id',$order_id)->get();

            //print_r($orders);

            //die;

            foreach ($orders as $key => $order) {



                $checkarray = array();



                $order_variant = json_decode($order->variant);

                $price        = explode($curency->contain, $order_variant[0]->price);

                $sale_price   = explode($curency->contain, $order_variant[0]->sale_price);

                $quantity     = $order_variant[0]->quantity;



                foreach ($order_variant as $key => $value) {

                    foreach ($value as $key => $values) {

                        $dat = ProductAttributeType::where('name',$key)->first();

                        $att = ProductAttribute::where('name',$values)->first();

                        if(!empty($dat)){

                            $checkarray['a_'.$dat->id] = ''.$att->id;

                        }

                    }

                }



                $checkarray['rprice'] = $price[0];

                $checkarray['sprice'] = $sale_price[0];

                $product_variant = ProductVariation::where('product_id',$order->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();



                foreach ($product_variant as $pro_variant) {

                    $checkarray['quantity'] = $pro_variant->quantity;



                    if($pro_variant->attribute == json_encode($checkarray)){



                        $qty = $pro_variant->quantity+$quantity;

                        $checkarray['quantity'] = ''.$qty;



                        $datatwo = array('quantity' => $checkarray['quantity'],'attribute' => json_encode($checkarray));



                        $pro_variant = ProductVariation::where('id',$pro_variant->id)->update($datatwo);

                    }

                }

                $mailarray = array();

                $product_variantupdate = ProductVariation::where('product_id',$order->product_id)->get();

                foreach ($product_variantupdate as $key => $product_variantupdates) {

                    $mailarray[] = $product_variantupdates->attribute;

                }

                $attributes = "[".implode(",", $mailarray)."]";

                $pro_attribute = Product::where('id',$order->product_id)->update(['attribute' => $attributes]);

            }

        }

        

        $order = OrderMaster::where('id',$order_id)->first();

        $Mobilesettings = Mobilesettings::where('user_id',$order->user_id)->first();

        $user_name = User::where('id',$order->user_id)->first();



        $products = DB::table('product')

            ->join('order','product.id','=','order.product_id')

            ->join('users', 'users.id','=','order.user_id')

            ->join('order_master', 'order_master.id','=','order.order_id')

            ->join('address', 'address.id','=','order_master.address_id')

            ->join('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')

            ->select('product.*','order_master.status','users.first_name','users.city as cityid','users.language','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.estimated_delivery_date')

            ->where('order.order_id','=',$order_id)

            ->get();

            $sum = 0;

            $product_details = array();

            $settings = Settings::where('slug','currency')->first();

            

         foreach ($products as $key => $product) {

            $att = array();

            $medias =   DB::table('media')

                ->select('media.*')

                ->where('media.module_id',$product->id)

                ->where('media.module_type',0)

                ->first();

                if(count($medias) > 0)

                {

                    $att['image']   = $medias->image;

                }

                $att['name']    = $product->name;

                $att['odr_variant']    = $product->odr_variant;

                

                $salepricess = json_decode(htmlspecialchars_decode($product->odr_variant));

                $saleprices = json_decode(json_encode($salepricess), True);

                $att['qty'] = $saleprices[0]['quantity'];

                $pricess = explode($settings->contain,$saleprices[0]['sale_price']);

                $sub_total  = ($pricess[0] * (int)$saleprices[0]['quantity']);

                $att['total'] = $sub_total;

                $product_details[] = $att;



                foreach ($saleprices as $saleprice) {

                    $pricess = explode($settings->contain,$saleprice['sale_price']);

                    $sum += $pricess[0]*$saleprice['quantity'];

                }

            }

            $tax = Settings::where('slug','=','tax')->first();

          //$shipping_price = Settings::where('slug','=','shipping_price')->first();

$city=$product->cityid;

$shipping =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();
if(!empty($shipping)){
    $price = $shipping->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}


            $currency = Settings::where('slug','=','currency')->first();

            $tax_val    = $sum*$tax->contain/100;



            $order_title_en = array();

            $order_title_fr = array();

            if($id == 0){

                $order_title_en = 'Placed';

            }

            elseif ($id == 1) {

                $order_title_en = 'Confirmed';

            }

             elseif ($id == 2) {

                $order_title_en = 'Packed';

            }

             elseif ($id == 3) {

                $order_title_en = 'Shipped';

            }

             elseif ($id == 4) {

                $order_title_en = 'Delivered';

            }

             elseif ($id == 5) {

                $order_title_en = 'Cancelled';

            }

            $message_fr = 'Your order No. '.$order_id.' has '.$order_title_en;

            $message_en = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;

            if($product->language == 0){

                $order_title = $order_title_en;



                $noti_title   = 'Order '.$order_title_en;

                $noti_message = 'Your order No. '.$order_id.' has '.$order_title_en;



                $mail_subject = 'Order '.$order_title_en;

                $mail_message = 'Your order No. '.$order_id.' has '.$order_title_en;



                $sms_message = 'Your order No. '.$order_id.' has '.$order_title_en;

            }else{

                $order_title = $order_title_fr;

                

                $noti_title   = 'Commander '.$order_title_fr;

                $noti_message = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;



                $mail_subject = 'Commander '.$order_title_fr;

                $mail_message = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;



                $sms_message = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;

            }

            if($Mobilesettings->email == '0'){



                $message =  $mail_message;

                $email =    trim($user_name->email);

                $subject = $mail_subject;

                Mail::send('emails.Notification',['key' => $message,'name' => $user_name->first_name,'products' => $products,'product_details' => $product_details,'tax' => $tax,'tax_val' => $tax_val,'shipping_price' => $price,'sum' => $sum,'currency' => $currency,'order_title'=>$order_title,'language'=>$product->language], function($message) use($email, $subject)

                {

                    $message->to($email, 'Order ')->subject($subject);

                });

        

            }

            if($Mobilesettings->sms == '0'){



                $src = '<?xml version="1.0" encoding="UTF-8"?>';

                $src = $src . "<SMS> 

                    <operations>  

                        <operation>SEND</operation> 

                    </operations> 

                    <authentification>    

                        <username>jesusnaissant@gmail.com</username>   

                        <password>Isis@2014</password>   

                    </authentification>   

                    <message> 

                        <sender>SMS</sender>    

                        <text>'".$sms_message."'</text>   

                    </message> 

                    <numbers>   

                    <number>'".$user_name->phone."'</number>    

                    </numbers>

                </SMS>";



                $curl = curl_init();

                $curlOptions = array(

                    CURLOPT_URL => 'http://api.atompark.com/members/sms/xml.php',

                    CURLOPT_FOLLOWLOCATION => false,

                    CURLOPT_POST => true,

                    CURLOPT_HEADER => false,

                    CURLOPT_RETURNTRANSFER => true,

                    CURLOPT_CONNECTTIMEOUT => 15,

                    CURLOPT_TIMEOUT => 100,

                    CURLOPT_POSTFIELDS => array('XML' => $src),

                );

                curl_setopt_array($curl, $curlOptions);

                if (false === ($xmlString = curl_exec($curl))) {

                    throw new Exception('Http request failed');

                }

                curl_close($curl);

            }



            if($Mobilesettings->notification == '0'){



                if(!empty($order->user_id))

                {

                    $user_token =  DB::table('users')

                    ->join('role_user', 'role_user.user_id','=','users.id')

                    ->select('users.*')

                    ->whereNotNull('users.device_token')

                    ->where('users.id','=',$order->user_id)

                    ->where('role_user.role_id','=','2')

                    ->first();

                    if(!empty($user_token->id))

                    {

                        $notificationdb = new Notification;

                        $notificationdb->title_en = $order_title_en;

                        $notificationdb->message_en = $message_en;

                        $notificationdb->title_fr = $order_title_fr;

                        $notificationdb->message_fr = $message_fr;

                        $notificationdb->user_id = $user_token->id;

                        $notificationdb->save();

                    }

                    

                    if(!empty($user_token->device_token)){

                       $json_data =[

                            "to" => $user_token->device_token,

                            "data" => [

                                "body"  => $noti_message,

                                "title" => $noti_title,

                            ],

                        ];

                        

                    }

                    else{



                        $json_data =[

                            "to" => '',

                            "data" => [

                                "body"  => $noti_message,

                                "title" => $noti_title,

                            ],

                        ];

                    }

                }

                $data = json_encode($json_data);

                $url = 'https://fcm.googleapis.com/fcm/send';

                $server_key = 'AAAAVjwwJqY:APA91bEkZtbzslIM_druLxpC2Ckei6wyMwJApblVYWCDJG3j_psnogCRudkUtC_DfOjlZgdJ77HR8C-TL_0H69Yky-khQYjLMJmX-WiNmsUVsGTMFQ8OROss5lRUjC203uWlZrO2rtH6';

                $headers = array(

                    'Content-Type:application/json',

                    'Authorization:key='.$server_key

                );



                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                $result = curl_exec($ch);

                if ($result === FALSE) {

                    return 'Notifcation not send';

                }

                else

                {

                     return 'Notifcation send';

                }

            }

    }



    public function changestimatedate(Request $request,$order_id){

       

        $validator = Validator::make($request->all(), 

            [

                'date'                 => 'required',

            ]

        );

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

        $datatwo = array(

            'estimated_delivery_date'  => $request->input('date'),

        );

        $product = OrderMaster::where('id',$order_id)->update($datatwo);

        return redirect('vendor/order/'.$order_id);

    }

    

    

}

