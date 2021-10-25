<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\Settings;
use App\Models\ProductAttribute;
use App\Models\Notification;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Models\ProductAttributeType;
use App\Models\Order;
use App\Models\User;
use App\Models\City;
use App\Models\Shipping;
use App\Models\Mobilesettings;
use Illuminate\Support\Facades\Auth; 
use DB;
use Validator;
use File;
use Image;
use Mail;




class OrderController extends Controller 
{
	public $successStatus = 200;
    public function gethistory(Request $request)
    { 
		$header = $request->header('Authorization');
		if($header != null)
		{
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $message                = 'success';
                $error                  = 'Sorry item not found.';
                $token_error            = 'Invalid token.';
                $payment_type_cash      = 'Cash On Delivery';
                $payment_type_airtel    = 'Airtel Money';
                $staus_delivery         = 'Delivered';
                $status_cancle          = 'Cancled';
                $delievery_tag_express  = 'express';
                $delievery_tag_standard = 'standard';

            }
            if($user->language == 1){
                $message = 'Succès';
                $error   = 'Désolé article introuvable.';
                $token_error   = 'Jeton invalide.';
                $payment_type_cash  = 'Payer en cash à la livraison';
                $payment_type_airtel = 'Airtel Money';
                $staus_delivery      = 'Livré';
                $status_cancle              = 'annulée';
                $delievery_tag_express = 'Express';
                $delievery_tag_standard = 'la norme';

            }
            $curency = Settings::where('slug','=','currency')->first();
            $tax = Settings::where('slug','=','tax')->first();

            $datas = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->leftjoin('address', 'order_master.address_id','=','address.id')
            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','order_master.user_id as user_id','order_master.address_id as address_id','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order.is_active','order_master.payment_type','order_master.modification','order_master.modification_date')
            ->where('order.is_active','=',0)
            ->where('order.product_status','!=',2)
            ->where('order.user_id',$user->id)
            ->where(function ($query)  {
            $query->where('order_master.status','=',4)
              ->orWhere('order_master.status','=',5);
            })
            ->groupBy('order_master.id')
            ->get();
            $datascount = $datas->count();
            if($datascount != 0)
            {
            	foreach ($datas as $key => $data) {
                    $con = array();
          
                    $con['order_id'] = $data->order_id;
                    $con['user_id'] = $data->user_id;
                    $con['address_id'] = $data->address_id;
                    $con['apartment_name'] = isset($data->apartment_name) ? $data->apartment_name."" : "";
                    $con['neighbourhood'] = isset($data->neighbour_hood) ? $data->neighbour_hood."" : "";
                    $con['street'] = isset($data->street) ? $data->street."" : "";
                    $con['city'] = isset($data->city) ? $data->city."" : "";
                    $con['state'] = isset($data->state) ? $data->state."" : "";
                    $con['pincode'] = isset($data->pincode) ? $data->pincode."" : "";
                    $con['phone'] = isset($data->userphone) ? $data->userphone."" : "";
                    if(!empty($data->updated_at)){
                        $date = $data->updated_at;
                        $updated_at = date('j M, D - H:i', strtotime($date));
                        $con['date'] = $updated_at;
                    }
                    else{
                        $con['date'] = "";
                    }


                    if($data->modification == 1){
                    if(!empty($data->modification_date)){

                        $date = $data->modification_date;
                        $modification_date = date('j M, D - H:i', strtotime($date));
                        $con['modification_date'] = $modification_date;
                    }
                    else{
                        $con['modification_date'] = "";
                    }
                    }

                    if($data->payment_type == 0)
                    {
                        $con['payment_type'] = $payment_type_cash;
                    }
                    elseif ($data->payment_type == 1) {
                       $con['payment_type'] = $payment_type_airtel;
                    }
                    else{
                        $con['payment_type'] = "";
                    }

                    if(!empty($data->delievery_tag)){
                        if($data->delievery_tag == 0)
                        {
                            $con['delievery_tag'] = $delievery_tag_express;
                        }
                        if ($data->delievery_tag == 1){
                           $con['delievery_tag'] = $delievery_tag_standard;
                        }
                    }
                    else{
                        $con['delievery_tag'] = "";
                    }


                    $con['order_status_id'] = isset($data->status) ? $data->status."" : "";
                    if($data->status == 4)
                    {
                        $con['order_status'] = $staus_delivery;
                    }
                    elseif($data->status == 5) {
                       $con['order_status'] = $status_cancle;
                    }
                    $products = DB::table('product')
                    ->join('order','product.id','=','order.product_id')
                    ->join('users', 'users.id','=','order.user_id')
                    ->join('order_master', 'order_master.id','=','order.order_id')
                    ->select('product.*','order_master.status','users.first_name','order_master.id as order_id','order.variant as  odr_variant','order.is_review as is_review','order.id as o_id')
                    ->where('order.order_id','=',$data->order_id)
                    ->get();
                    $productnew = array();
                    
                    foreach ($products as $key => $product) {
                        $producttttt = array();
                        $producttttt['o_id']= isset($product->o_id) ? $product->o_id."" : "";
                        $producttttt['product_id']= isset($product->id) ? $product->id."" : "";
                        $producttttt['name']= isset($product->name) ? $product->name."" : "";
                        if($data->status == 5){
                            $producttttt['is_review'] = "1";
                        }
                        else{
                            $producttttt['is_review']= isset($product->is_review) ? $product->is_review."" : "";
                        }
                        
                        if(!empty($product->odr_variant)){
                            $variant_attribute = json_decode($product->odr_variant);
                            $producttttt['attribute']= $variant_attribute;
                        }
                        else{
                            $producttttt['attribute'] = "";
                        }
                        $medias = DB::table('media')->select('media.*')->where('media.module_id',$product->id)->where('media.module_type',0)->get();
                        if(count($medias) > 0)
                        {
                            $url = array();
                            foreach ($medias as  $media) {
                                $url[] = url('/storage/tmp/uploads').'/'.$media->image;
                            }

                            $producttttt['order_image'] = $url;
                        }
                        else{
                            $producttttt['order_image'] = "";
                        }
                        $productnew[] = $producttttt; 
                    }
                    $con['detail'] = $productnew;

                    $confinal[] = $con;
                }
            }
            else
            {
                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
            }
            $success['status']  = '200';
            $success['message'] = $message;
            $success['tax'] = isset($tax->contain) ? $tax->contain."" : "";
            $success['data']  = $confinal;

            return response()->json($success, $this-> successStatus);
		}
		else
		{
			return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
		}
	}
	public function cancelorder(Request $request)
    {
    	$header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            
            if($user->language == 0){
                $message      = 'Order canceled successfully.';
                $noti_title   = 'Order Cancelled';
                $noti_message = 'Your order No. '.$request->order_id.' has Cancelled';
                $mail_subject = 'Order Cancelled';
                $order_title  = 'Cancelled';
                $error        = 'Order can not cancel.Please try again.';
                $token_error  = 'Invalid token.';
                $mail_message = 'Your order No. '.$request->order_id.' has Cancelled';
                $sms_message  = 'Your order No. '.$request->order_id.' has Cancelled';
            }
            if($user->language == 1){
                $message      = 'Commande annulée avec succès.';
                $mail_subject = 'Order Annulé';
                $noti_title   = 'Commande Annulé';
                $noti_message = 'Votre commande est non. '.$request->order_id.' Cygne Annulé';
                $order_title  = 'Annulé';
                $error        = 'La commande ne peut pas être  annulée. Veuillez réessayer.';
                $token_error  = 'Jeton invalide.';
                $mail_message = 'Votre commande est non. '.$request->order_id.' Cygne Annulé';
                $sms_message  = 'Hello! Votre commande n ° '.$request->order_id.' a été annulée';
            }
            $date = date('Y-m-d H:i:s');
            $latitude  = $request->latitude;
            $longitude = $request->longitude;
            $city = $request->city;
            
        	//$order = Order::where('id',$request->order_id)->first();
            $insert = array();
            $insert['status']  = 5;
            $insert['cancel_date'] = $date;
            $cancle_order = OrderMaster::where('id',$request->order_id)->update($insert);
            if(!$cancle_order){
                return response()->json(['status' => '401', 'message'=>$error], 401);
            }
            else{
                $curency    = Settings::where('slug','=','currency')->first();
                $orders  = Order::join('users', 'users.id','=','order.user_id')
                //->join('city','city.id','=','users.city')
                ->where('user_id',$user->id)
                ->where('order_id',$request->order_id)
                //->where('city.name',$request->city)
                ->get();
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

                $products = DB::table('product')
                    ->join('order','product.id','=','order.product_id')
                    ->join('users', 'users.id','=','order.user_id')
                    //->join('users as p','product.user_id','=','p.id')
                   // ->join('city','city.id','=','p.city')
                    ->join('order_master', 'order_master.id','=','order.order_id')
                    ->join('address', 'address.id','=','order_master.address_id')
                    ->join('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
                    ->select('product.*','order_master.status','users.first_name','users.language','order_master.id as order_id','order.variant as  odr_variant','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price','order_master.created_at as create_date','order_master.estimated_delivery_date')
                    ->where('order.order_id','=',$request->order_id)
                    //->where('city.name',$request->city)
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
                        $att['image'] = $medias->image;
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
                 $shipping_price =  DB::table('shipping')->join('city','city.id','=','shipping.city_id')->select('shipping.shipping_price')->where('city.name',$request->city)->first();

                 if(!empty($shipping_price)){
                    $price = $shipping_price->shipping_price;
                }else{
                    $shipping_price = Settings::where('slug','=','shipping_price')->first();
                    $price= $shipping_price->contain;
                }
                $currency = Settings::where('slug','=','currency')->first();
                $tax_val    = $sum*$tax->contain/100;
                $Mobilesettings = Mobilesettings::where('user_id',$user->id)->first();
                $user_name = User::where('id',$user->id)->first();
                if($Mobilesettings->email == '0'){
                    $message =  $mail_message;
                     $email =    trim($user_name->email);
                     $subject = $mail_subject;
                     
                     Mail::send('emails.Notification',['key' => $message,'current_order_id'=> $request->order_id,'name' => $user_name->first_name,'products' => $products,'product_details' => $product_details,'tax' => $tax,'shipping_price' => $price,'tax_val' => $tax_val,'sum' => $sum,'currency' => $currency,'order_title'=>$order_title,'language'=>$user->language], function($message) use($email, $subject)
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

               if(!empty($user->id))
                {
                    $user_token =  DB::table('users')
                    ->join('role_user', 'role_user.user_id','=','users.id')
                    ->select('users.*')
                    ->whereNotNull('users.device_token')
                    ->where('users.id','=',$user->id)
                    ->where('role_user.role_id','=','2')
                    ->first();
                    if(!empty($user_token->id))
                    {
                        $notificationdb = new Notification;
                        $notificationdb->title_en = 'Cancelled';
                        $notificationdb->message_en = 'Your order No. '.$request->order_id.' has Cancelled';
                        $notificationdb->title_fr = 'Annulé';
                        $notificationdb->message_fr = 'Votre commande est non. '.$request->order_id.' Cygne Annulé';
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
            }
            }

            $success['status']  = '200';
        	$success['message'] = $message;
        	$success['data']  = [];
        	return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }
    public function currentorder(Request $request)
    { 
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
        //print_r($user);die();
            if($user->language == 0){
                $message                = 'success';
                $error                  = 'Sorry item not found.';
                $token_error            = 'Invalid token.';
                $payment_type_cash      = 'Cash On Delivery';
                $payment_type_airtel    = 'Airtel Money';
                $staus_delivery         = 'Delivered';
                $status_shipped         = 'Shipped';
                $status_packed          = 'Packed';
                $status_confirm         = 'Order Confirmed';
                $status_placed          = 'Order placed';
                $delievery_tag_express  = 'express';
                $delievery_tag_standard = 'standard';
            }
            if($user->language == 1){
                $message                = 'Succès';
                $error                  = 'Désolé article non trouvé.';
                $token_error            = 'Jeton invalide.';
                $payment_type_cash      = 'Payer en cash à la livraison';
                $payment_type_airtel    = 'Airtel Money';
                $staus_delivery         = 'Livré';
                $status_shipped         = 'Expédié';
                $status_packed          = 'Emballée';
                $status_confirm         = 'Commande confirmée';
                $status_placed          = 'Commande passée';
                $delievery_tag_express  = 'Express';
                $delievery_tag_standard = 'la norme';
            }


            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $city = $request->city;

            $tax = Settings::where('slug','=','tax')->first();
            //$shipping_price = Settings::where('slug','=','shipping_price')->first();

            $shipping_price =  DB::table('shipping')->join('city','city.id','=','shipping.city_id')->select('shipping.shipping_price')->where('city.name',$request->city)->first();
            if(!empty($shipping_price)){
                $price = $shipping_price->shipping_price;
            }else{
                $shipping_price = Settings::where('slug','=','shipping_price')->first();
                $price= $shipping_price->contain;
            }
            $express_delievery = Settings::where('slug','=','express_delievery')->first();
            $standard_delievery = Settings::where('slug','=','standard_delievery')->first();



            $currency = Settings::where('slug','=','currency')->first();
         //   $datas = DB::table('order')
        //    ->join('users', 'users.id','=','order.user_id')
         //   ->join('order_master', 'order_master.id','=','order.order_id')
         //   ->leftjoin('address', 'address.id','=','order_master.address_id')
         //   ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
         //   ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','order_master.user_id as user_id','order_master.address_id as address_id','address.address as useraddress','order_master.created_at as created_at','order_master.estimated_delivery_date as estimated_delivery_date','order_master.order_confirmed_date as order_confirmed_date','order_master.packed_date as packed_date','order_master.shipped_date as shipped_date','order_master.delivery_date as delivery_date','order_master.cancel_date as cancel_date','order_master.status','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price as total_price','order_master.payment_type','order_master.delievery_tag','order_master.modification_date','order_master.modification')
        //    ->where('order_master.status','!=',4)
        //    ->where('order_master.status','!=',5)
        //    ->where('order.user_id',$user->id)
       //     ->where('order.product_status','!=',2)
         //     ->groupBy('order_master.id')
        //    ->get();
//print_r($datas);die();



              $datas = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            //->join('city','city.id','=','users.city')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->join('address', 'address.id','=','order_master.address_id')
            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','order_master.user_id as user_id','order_master.address_id as address_id','address.address as useraddress','order_master.created_at as created_at','order_master.estimated_delivery_date as estimated_delivery_date','order_master.order_confirmed_date as order_confirmed_date','order_master.packed_date as packed_date','order_master.shipped_date as shipped_date','order_master.delivery_date as delivery_date','order_master.cancel_date as cancel_date','order_master.status','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price as total_price','order_master.payment_type','order_master.delievery_tag','order_master.modification_date','order_master.modification')
            ->where('order_master.status','!=',4)
            ->where('order_master.status','!=',5)
            ->where('order.user_id',$user->id)
            ->where('order.product_status','!=',2)
            //->where('city.name',$request->city)
             //->where('order.id','=',162)
            ->groupBy('order_master.id')
            ->get();



            $datascount = $datas->count();
            if($datascount != 0)
            {
                $confinal = array();
                foreach ($datas as $key => $data) {
                     $con = array();
                     $con['order_id'] = $data->order_id;
                     $con['user_id'] = $data->user_id;
                     $con['address_id'] = $data->address_id;

                      

                     if(!empty($data->created_at)){
                            $date = $data->created_at;
                            $updated_at = date('j M, D - H:i', strtotime($date));
                            $con['purchase_date'] = $updated_at;
                        }
                        else{
                            $con['purchase_date'] = "";
                        }
                        if(!empty($data->estimated_delivery_date)){
                            $date = $data->estimated_delivery_date;
                            $updated_at = date('j M, D - H:i', strtotime($date));
                            $con['est_delivery_date'] = $updated_at;
                        }
                        else{
                            $con['est_delivery_date'] = "";
                        }
                        if(!empty($data->created_at)){
                            $date = $data->created_at;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_placed'] = $updated_at;
                        }
                        else{
                            $con['order_placed'] = "";
                        }
                        if(!empty($data->order_confirmed_date)){
                            $date = $data->order_confirmed_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_confirmed'] = $updated_at;
                        }
                        else{
                            $con['order_confirmed'] = "";
                        }


                       if($data->modification == 1){
                       if(!empty($data->modification_date)){

                            $date = $data->modification_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_modification'] = $updated_at;
                        }
                        else{
                            $con['order_modification'] = "";
                        }
                        }

                        
                        if(!empty($data->packed_date)){
                            $date = $data->packed_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_packed'] = $updated_at;
                        }
                        else{
                            $con['order_packed'] = "";
                        }
                        if(!empty($data->shipped_date)){
                            $date = $data->shipped_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_shipped'] = $updated_at;
                        }
                        else{
                            $con['order_shipped'] = "";
                        }
                        if(!empty($data->delivery_date)){
                            $date = $data->delivery_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_delivered'] = $updated_at;
                        }
                        else{
                            $con['order_delivered'] = "";
                        }
                        if(!empty($data->cancel_date)){
                            $date = $data->cancel_date;
                            $updated_at = date('H:i - j M, D', strtotime($date));
                            $con['order_cancel'] = $updated_at;
                        }
                        else{
                            $con['order_cancel'] = "";
                        }
                        if($data->payment_type == 0)
                        {
                            $con['payment_type'] = $payment_type_cash;
                        }
                        elseif ($data->payment_type == 1) {
                           $con['payment_type'] = $payment_type_airtel;
                        }
                        else{
                            $con['payment_type'] = "";
                        }

                        if(!empty($data->delievery_tag)){
                            if($data->delievery_tag == 0)
                            {
                                $con['delievery_tag'] = $delievery_tag_express;
$success['express_delievery'] = isset($express_delievery->contain) ? $express_delievery->contain."" : "";

                            }
                            if ($data->delievery_tag == 1) {
                                $con['delievery_tag'] = $delievery_tag_standard;
$success['standard_delievery'] = isset($standard_delievery->contain) ? $standard_delievery->contain."" : "";

                            }
                        }
                        else{
                            $con['delievery_tag'] = "";
                        }

                        $con['apartment_name'] = isset($data->apartment_name) ? $data->apartment_name."" : "";
                        $con['neighbourhood'] = isset($data->neighbour_hood) ? $data->neighbour_hood."" : "";
                        $con['street'] = isset($data->street) ? $data->street."" : "";
                        $con['city'] = isset($data->city) ? $data->city."" : "";
                        $con['state'] = isset($data->state) ? $data->state."" : "";
                        $con['pincode'] = isset($data->pincode) ? $data->pincode."" : "";
                        $con['phone'] = isset($data->userphone) ? $data->userphone."" : "";
                        $con['order_status_id'] = isset($data->status) ? $data->status."" : "";

                        if($data->status == 0)
                        {
                            $con['order_status'] = $status_placed;
                        }
                        elseif ($data->status == 1) {
                           $con['order_status'] = $status_confirm;
                        }
                        elseif ($data->status == 2) {
                           $con['order_status'] = $status_packed;
                        }
                        elseif ($data->status == 3) {
                           $con['order_status'] = $status_shipped;
                        }
                        elseif ($data->status == 4) {
                           $con['order_status'] = $staus_delivery;
                        }
                        if(!empty($data->total_price)){
                            $con['order_amount'] = $data->total_price.$currency->contain. '(paid)';
                        }
                        else{
                            $con['order_amount'] = "";
                        }
                    $products = DB::table('product')
                    ->join('order','product.id','=','order.product_id')
                    ->join('users', 'users.id','=','order.user_id')
                    ->join('users as p','product.user_id','=','p.id')
                    ->join('city','city.id','=','p.city')
                    ->join('order_master', 'order_master.id','=','order.order_id')
                    ->select('product.*','order_master.status','users.first_name','order_master.id as order_id','order.variant as  odr_variant','order.id as id','order.variant as  odr_variant','order.product_id as product_id')
                    ->where('order.order_id','=',$data->order_id)
                    ->where('city.name',$request->city)
                    ->get();
                    $productnew = array();
                    
                    foreach ($products as $key => $product) {
                        $producttttt = array();
                        $producttttt['name']= isset($product->name) ? $product->name."" : "";
                        $producttttt['id']= isset($product->id) ? $product->id."" : "";
                        $producttttt['product_id']= isset($product->product_id) ? $product->product_id."" : "";


                        if(!empty($product->odr_variant)){
                            $variant_attribute = json_decode($product->odr_variant);
                            $producttttt['attribute']= $variant_attribute;
                        }
                        else{
                            $producttttt['attribute'] = "";
                        }
                        $medias = DB::table('media')->select('media.*')->where('media.module_id',$product->id)->where('media.module_type',0)->get();
                        if(count($medias) > 0)
                        {
                            $url = array();
                            foreach ($medias as  $media) {
                                $url[] = url('/storage/tmp/uploads').'/'.$media->image;
                            }

                            $producttttt['order_image'] = $url;
                        }
                        else{
                            $producttttt['order_image'] = "";
                        }
                        $productnew[] = $producttttt; 
                    }
                    $con['detail'] = $productnew;
                    $confinal[] = $con;
                }
            }
            else
            {
                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
            }
            

            $success['status']  = '200';
            $success['message'] = $message;
            $success['tax'] = isset($tax->contain) ? $tax->contain."" : "";
           // $success['shipping_price'] = isset($shipping_price->contain) ? $shipping_price->contain."" : "";

            $success['shipping_price'] = $price;
            $success['data']  = $confinal;
            return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
    }
    public function clearorderhistory(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $message = 'Clear Order history successfully.';
                $error   = 'Order history already clear.';
                $token_error   = 'Invalid token.';
            }
            if($user->language == 1){
                $message = "Effacer l'historique des commandes avec succès.";
                $error   = "L'historique des commandes est déjà clair.";
                $token_error   = 'Jeton invalide.';
            }
            $order = Order::where('id',$request->order_id)->first();
            $insert = array();
            $insert['order.is_active']  = 1;
            $clear_history = DB::table('order')
                ->join('order_master', 'order_master.id','=','order.order_id')
                ->join('product', 'order.product_id','=','product.id')
                ->select('order.*','product.name as productname','order_master.status')
                ->where('order.user_id',$user->id)
                ->where(function ($query)  {
                $query->where('order_master.status','=',4)
                  ->orWhere('order_master.status','=',5);
                })
                ->update($insert);
            if(!$clear_history)
            {
                return response()->json(['status' => '401', 'message'=>$error], 401);
            }
            $success['status']  = '200';
            $success['message'] = $message;
            $success['data']  = [];
            return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }

    public function modifyorder(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $message                = 'success';
                $error                  = 'Sorry item not found.';
                $token_error            = 'Invalid token.';
                $payment_type_cash      = 'Cash On Delivery';
                $payment_type_airtel    = 'Airtel Money';
                $staus_delivery         = 'Delivered';
                $status_shipped         = 'Shipped';
                $status_packed          = 'Packed';
                $status_confirm         = 'Order Confirmed';
                $status_placed          = 'Order placed';
                $delievery_tag_express  = 'express';
                $delievery_tag_standard = 'standard';
            }
            if($user->language == 1){
                $message                = 'Succès';
                $error                  = 'Désolé article introuvable.';
                $token_error            = 'Jeton invalide.';
                $payment_type_cash      = 'Payer en cash à la livraison';
                $payment_type_airtel    = 'Airtel Money';
                $staus_delivery         = 'Livré';
                $status_shipped         = 'Expédié';
                $status_packed          = 'Emballée';
                $status_confirm         = 'Commande confirmée';
                $status_placed          = 'Commande passée';
                $delievery_tag_express  = 'Express';
                $delievery_tag_standard = 'la norme';
            }

            $date                     = date('Y-m-d H:i:s');
            $order_id                 = $request->order_id;

            

            if(!empty($request->data)){

            $datascount = count($request->data);
            if($datascount != 0)
            {
                $datas = $request->data;
                foreach ($datas as $key => $data) {
                $curency        = Settings::where('slug','=','currency')->first();
                $orders         = Order::where('user_id',$request->user_id)->where('id',$data['id'])->first();
                $quantity_order = $data['attribute'][0]['quantity'];
                $checkarray    = array();
                $order_variant = json_decode($orders->variant);
                $old_quantity  = $order_variant[0]->quantity;
                $quantity      = $quantity_order-$old_quantity;
                $price         = explode($curency->contain, $order_variant[0]->price);
                $sale_price    = explode($curency->contain, $order_variant[0]->sale_price);
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
                $product_variant = ProductVariation::where('product_id',$orders->product_id)
                                    ->where('sale_price',$sale_price[0])
                                    ->where('regular_price',$price[0])
                                    ->get();

                foreach ($product_variant as $pro_variant) {
                $checkarray['quantity'] = $pro_variant->quantity;

                if($pro_variant->attribute == json_encode($checkarray)){
                $qty = $pro_variant->quantity-($quantity);
                $checkarray['quantity'] = ''.$qty;

                $datatwo = array('quantity' => $checkarray['quantity'],'attribute' => json_encode($checkarray));

                $pro_variant = ProductVariation::where('id',$pro_variant->id)->update($datatwo);
                }
                }
                $mailarray = array();
                $product_variantupdate = ProductVariation::where('product_id',$orders->product_id)->get();
                foreach ($product_variantupdate as $key => $product_variantupdates) {
                $mailarray[] = $product_variantupdates->attribute;
                }
                $attributes = "[".implode(",", $mailarray)."]";
                $pro_attribute = Product::where('id',$orders->product_id)->update(['attribute' => $attributes]);
                if($quantity_order == 0){
                $modification = Order::where('id',$data['id'])->where('product_id',$data['product_id'])->update(['product_status' => 2]);
                }else{
                $attribute    = json_encode($data['attribute']);
                $modification = Order::where('id',$data['id'])->where('product_id',$data['product_id'])->update(['variant' => $attribute]);
                }
                $order_master = OrderMaster::where('id',$order_id)->first();
                
                if($order_master->total_price > $request->total){
                $price_diff = $order_master->total_price-$request->total;

                $status = 1;
                $total = $order_master->total_price-$price_diff;
                if($order_master->payment_type == 1){
                $message = 'Your payment return '.$price_diff;
                }
                }else{
                $price_diff = $request->total-$order_master->total_price;
                $status = 0;
                $total = $order_master->total_price+$price_diff;
                if($order_master->payment_type == 1){
                $message = 'You will pay '.$price_diff .'on cash when delvery your order.';
                }
                }
                $prev_order             = !empty($request->detail) ? json_encode($request->detail) : '';
                $datatwo                = array(
                'modification_date'     => $date,
                'payment_status'        => $status,
                'diff_price'            => $price_diff,
                'total_price'           => $total,
                'modification'          => 1,
                'previous_order'        => $prev_order,
                );
                $modification = OrderMaster::where('id',$order_id)->update($datatwo);
                }
            }
            }

            $success['status']      = '200';
            $success['message']     = $message;
            $success['data']        = [];
            $data['orderid']        = $order_id;
            return response()->json($success, $this-> successStatus);
        }
        else{
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
    }


}