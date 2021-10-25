<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Models\OrderMaster;
use App\Models\Mobilesettings;
use App\Models\Notification;
use App\Models\User;
use App\Models\Settings;
use App\Models\Shipping;
use App\Models\City;
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

class OrderController extends Controller
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

       $orders = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','address.address as useraddress','order_master.estimated_delivery_date as estimated_delivery_date')
            ->where('order_master.status','!=',4)
            ->where('order_master.status','!=',5)
            ->where('order.product_status','!=',2)
            ->groupBy('order_master.id')
            ->paginate(100);

            // print_r($orders);
            // die;
        return View('pages.admin.order.show', compact('orders'));
    }

    public function orderhistory(){

        $orderhistory = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id')
            ->where('order_master.status','=',4)
            ->orwhere('order_master.status','=',5)
            ->where('order.product_status','!=',2)
            ->groupBy('order_master.id')
            ->paginate(100);
         return View('pages.admin.orderhistory.show', compact('orderhistory'));
    }

    public function orderhistoryshow($id){

        $products = DB::table('product')
            ->join('order','product.id','=','order.product_id')
            ->join('users', 'users.id','=','order.user_id')
            ->join('users as p', 'p.id','=','product.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('product.*','order_master.status','users.first_name','p.city as cityid','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.payment_type','order_master.delievery_tag')
            ->where('order.order_id','=',$id)
            ->where('order.product_status','!=',2)
            ->get();
            
            $settings = Settings::where('slug','currency')->first();
            $standard_charge = Settings::where('slug','standard_delievery')->first();
            // echo "<pre>";
            // print_r($standard_charge);
            //  die;
            $express_charge = Settings::where('slug','express_delievery')->first();
            $sum = 0;
            $sub_total = 0;
            $product_details = array();
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
                $att['odr_variant'] = $product->odr_variant;
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
           // $shipping_price = Settings::where('slug','=','shipping_price')->first();
if(!empty($product->cityid)){
    $city = $product->cityid;
}
else{
    $city = '';
}
//$city=$product->cityid;

$shipping =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();


if(!empty($shipping)){
    $price = $shipping->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}

//print_r($price);die();



            $tax = Settings::where('slug','=','tax')->first();
            $currency = Settings::where('slug','=','currency')->first();
            $tax_val    = $sum*$tax->contain/100;
            
        return view('pages.admin.orderhistory.view',compact('product_details','products','tax','tax_val','sum','currency','price','standard_charge','express_charge'));

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

        $products = DB::table('product')
            ->join('order','product.id','=','order.product_id')
            ->join('users', 'users.id','=','order.user_id')
            ->join('users as p', 'p.id','=','product.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('product.*','order_master.status','users.first_name','p.city as cityid','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.estimated_delivery_date','order_master.payment_type','order_master.delievery_tag','order_master.modification','order_master.modification_date')
            ->where('order.order_id','=',$id)
            ->where('order.product_status','!=',2)
            ->get();
            $sum = 0;
            $sub_total = 0;
            $product_details = array();
            $settings = Settings::where('slug','currency')->first();
            $standard_charge = Settings::where('slug','standard_delievery')->first();
            // echo "<pre>";
            // print_r($standard_charge);
            //  die;
            $express_charge = Settings::where('slug','express_delievery')->first();
			
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
                //print_r($pricess[1] * $saleprices[0]['quantity']);
                //die;
                $att['total'] = $sub_total;
                $product_details[] = $att;
                foreach ($saleprices as $saleprice) {
					$pricess = explode($settings->contain,$saleprice['sale_price']);
                    $sum += $pricess[0]*$saleprice['quantity'];
                   
                }
                
			}
            if(!empty($product->cityid)){
                $city = $product->cityid;
            }
            else{
                $city = '';
            }
            // echo "<pre>";
            //  print_r($products);
            // die;
            // if(count($products)<0){
            //     $city = $products[0]->cityid;
            // }else{
            //     $city = '';
            // }


//echo "<pre>";
//print_r($product);die();
            if(!empty($products[0]->modification_date)){
               $date = date('j M, D - H:i', strtotime($products[0]->modification_date));
            }else{
                $date = '';
            }
            $tax = Settings::where('slug','=','tax')->first();
            
           //$shipping_price = Settings::where('slug','=','shipping_price')->first();
          // $shipping_price =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.shipping_price')->first();

$shipping =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();

if(!empty($shipping)){
    $price = $shipping->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}



//echo "<pre>";
//print_r($shipping_price);die();


            $currency = Settings::where('slug','=','currency')->first();
            $tax_val  = $sum*$tax->contain/100;
            //$total_qty = $sum*$qty;
        return view('pages.admin.order.view',compact('product_details','products','tax','tax_val','sum','currency','price','date','standard_charge','express_charge'));
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
        $data = $request->all();
        $searchTerm = $request->input('search_box');
        $orders = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id')
            ->where('order_master.status','!=',4)
            ->where('order_master.status','!=',5)
            ->where(function ($query) use ($searchTerm) {
            $query->where('order_master.id', '=', $searchTerm)
            ->orWhere('users.first_name', 'like','%'. $searchTerm.'%');
            })
            ->groupBy('order_master.id')
            ->paginate(100);
        return view('pages.admin.order.show',compact('orders'));
    }

    public function searchistory(Request $request){
        $data = $request->all();
        $searchTerm = $request->input('search_box');
        $orderhistory = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id')
            ->where(function ($query) {
            $query->where('order_master.status', '=', 4)
            ->orWhere('order_master.status', '=',5);
            })
           ->where(function ($query) use ($searchTerm) {
            $query->where('order_id', '=', $searchTerm)
            ->orWhere('users.first_name', 'like','%'. $searchTerm.'%');
            })
            ->groupBy('order_master.id')
            ->paginate(100);
        return view('pages.admin.orderhistory.show',compact('orderhistory'));
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
        // print_r($user_name->phone);
        // die;
        $products = DB::table('product')
            ->join('order','product.id','=','order.product_id')
            ->join('users', 'users.id','=','order.user_id')
            ->join('users as p', 'p.id','=','product.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->join('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('product.*','order_master.status','users.first_name','p.city as cityid','users.language','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.estimated_delivery_date')
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
$shipping_price =  DB::table('shipping')->join('users','users.city','=','shipping.city_id')->select('shipping.*','users.city')->where('city_id','=',$city)->first();

if(!empty($shipping_price)){
    $price = $shipping_price->shipping_price;
}else{
    $shipping_price = Settings::where('slug','=','shipping_price')->first();
    $price= $shipping_price->contain;
}
            $currency = Settings::where('slug','=','currency')->first();
            $tax_val    = $sum*$tax->contain/100;
            $order_title_en = array();
            
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
                $order_title_fr = array();
                if($id == 0){
                    $order_title_fr = 'Placid';
                }
                elseif ($id == 1) {
                    $order_title_fr = 'Confirmée';
                }
                 elseif ($id == 2) {
                    $order_title_fr = 'Préparée';
                }
                 elseif ($id == 3) {
                    $order_title_fr = 'Expédiée';
                }
                 elseif ($id == 4) {
                    $order_title_fr = 'Livrée';
                }
                 elseif ($id == 5) {
                    $order_title_fr = 'Annulée';
                }
                $message_fr = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;
                $message_en = 'Your order No. '.$order_id.' has '.$order_title_en;
            if($product->language == 0){
                $order_title = $order_title_en;
                $noti_title   = 'Order '.$order_title_en;
                $noti_message = 'Your order No. '.$order_id.' has '.$order_title_en;

                $mail_subject = 'Order '.$order_title_en;
                $mail_message = 'Your order No. '.$order_id.' has '.$order_title_en;

                $sms_message = 'Your order No. '.$order_id.' has '.$order_title_en;
            }else{
                $order_title = $order_title_fr;
                $noti_title   = 'Commande '.$order_title_fr;
                $noti_message = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;

                $mail_subject = 'Commande '.$order_title_fr;
                $mail_message = 'Votre commande est non. '.$order_id.' Cygne '.$order_title_fr;
                if($id == 1){
                    $sms_message = 'Hello! Votre commande N° '.$order_id.' a été annulée avec succès. ';
                }elseif ($id == 5) {
                    $sms_message = 'Hello! Votre commande N° '.$order_id.' a été annulée avec succès. ';
                }elseif ($id == 0) {
                    $sms_message = 'Hello! Votre commande N° '.$order_id.' a été annulée avec succès. ';
                }
                else{
                    $sms_message = 'Hello! Votre commande N° '.$order_id.' vient d’être '.$order_title_fr;
                }
            }
            // print_r($Mobilesettings);
            // die;
            
            if($Mobilesettings->email == '0'){

                $message =  $mail_message;
                $email =    trim($user_name->email);
                $subject = $mail_subject;
                Mail::send('emails.Notification',['key' => $message,'current_order_id'=> $id,'name' => $user_name->first_name,'products' => $products,'product_details' => $product_details,'tax' => $tax,'shipping_price' => $price,'tax_val' => $tax_val,'sum' => $sum,'currency' => $currency,'order_title'=>$order_title,'language'=>$product->language], function($message) use($email, $subject)
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
                         <text>".$sms_message."</text>
                      </message>
                     <numbers>
                     <number>+91".$user_name->phone."</number>
                     </numbers>
                 </SMS>";
                 // print_r($src);
                 // die;
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
        return redirect('admin/order/'.$order_id);
    }
    // public function SendSMS($hostUrl){
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $hostUrl);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //     curl_setopt($ch, CURLOPT_POST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // change to 1 to verify cert
    //     curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    //     //curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    //     $result = curl_exec($ch);
    //     return $result;
    // }
    
}
