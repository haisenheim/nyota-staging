<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use DB;
use App\Models\OrderMaster;
use App\Models\Settings;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Models\ProductAttributeType;
use App\Models\Order;

use Illuminate\Support\Facades\Auth;  

class OrdernewController extends Controller 
{
	public $successStatus = 200;
    public function gethistory(Request $request)
    { 
		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $message = 'success';
                $error   = 'Sorry item not found.';
                $token_error   = 'Invalid token.';
                $payment_type_cash  = 'Cash On Delivery';
                $payment_type_airtel = 'Airtel Money';
                $staus_delivery      = 'Delivered';
                $status_cancle       = 'Cancled';
                $delievery_tag_express = 'express';
                $delievery_tag_standard = 'standard';

            }
            if($user->language == 1){
                $message = 'Succès';
                $error   = 'Désolé article introuvable.';
                $token_error   = 'Jeton invalide.';
                $payment_type_cash  = 'Paiement à la livraison';
                $payment_type_airtel = 'Airtel Money';
                $staus_delivery      = 'Livré';
                $status_cancle              = 'Annulé';
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
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order.is_active','order_master.payment_type')
            ->where('order.is_active','=',0)
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
                $message = 'Order canceled successfully.';
                $error   = 'Order can not cancel.Please try again.';
                $token_error   = 'Invalid token.';
            }
            if($user->language == 1){
                $message = 'Commande annulée avec succès.';
                $error   = 'La commande ne peut pas être annulée. Veuillez réessayer.';
                $token_error   = 'Jeton invalide.';
            }
            $date = date('Y-m-d H:i:s');
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
                $orders  = Order::where('user_id',$user->id)->where('order_id',$request->order_id)->get();
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
                $message = 'success';
                $error   = 'Sorry item not found.';
                $token_error   = 'Invalid token.';
                $payment_type_cash  = 'Cash On Delivery';
                $payment_type_airtel = 'Airtel Money';
                $staus_delivery      = 'Delivered';
                $status_shipped = 'Shipped';
                $status_packed  = 'Packed';
                $status_confirm = 'Order Confirmed';
                $status_placed = 'Order placed';
                $delievery_tag_express = 'express';
                $delievery_tag_standard = 'standard';

            }
            if($user->language == 1){
                $message = 'Succès';
                $error   = 'Désolé article introuvable.';
                $token_error   = 'Jeton invalide.';
                $payment_type_cash  = 'Paiement à la livraison';
                $payment_type_airtel = 'Airtel Money';
                $staus_delivery      = 'Livré';
                $status_shipped = 'Expédié';
                $status_packed  = 'Emballée';
                $status_confirm = 'Commande confirmée';
                $status_placed = 'Commande passée';
                $delievery_tag_express = 'Express';
                $delievery_tag_standard = 'la norme';

            }
            $tax = Settings::where('slug','=','tax')->first();
            $currency = Settings::where('slug','=','currency')->first();
            $datas = DB::table('order')
            ->join('users', 'users.id','=','order.user_id')
            ->join('order_master', 'order_master.id','=','order.order_id')
            ->leftjoin('address', 'address.id','=','order_master.address_id')
            ->leftjoin('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
            ->select('order.*','order_master.status','users.first_name','order_master.id as order_id','address.address as useraddress','order_master.created_at as created_at','order_master.estimated_delivery_date as estimated_delivery_date','order_master.order_confirmed_date as order_confirmed_date','order_master.packed_date as packed_date','order_master.shipped_date as shipped_date','order_master.delivery_date as delivery_date','order_master.cancel_date as cancel_date','order_master.status','address.apartment_name','address.phone as userphone','neighbourhood.neighbour_hood','address.street','address.city','address.state','address.pincode','order_master.total_price as total_price','order_master.payment_type','order_master.delievery_tag')
            ->where('order_master.status','!=',4)
            ->where('order_master.status','!=',5)
            ->where('order.user_id',$user->id)
             //->where('order.id','=',162)
            ->groupBy('order_master.id')
            ->get();
//print_r($datas);die();
            $datascount = $datas->count();
            if($datascount != 0)
            {
                $confinal = array();
                foreach ($datas as $key => $data) {
                     $con = array();
                     $con['order_id'] = $data->order_id;

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
                            }
                            if ($data->delievery_tag == 1) {
                                $con['delievery_tag'] = $delievery_tag_standard;
                            }
                        }
                        else{
                            $con['delievery_tag'] = "";
                        }

                        $con['apartment_name']  = isset($data->apartment_name) ? $data->apartment_name."" : "";
                        $con['neighbourhood']   = isset($data->neighbour_hood) ? $data->neighbour_hood."" : "";
                        $con['street']          = isset($data->street) ? $data->street."" : "";
                        $con['city']            = isset($data->city) ? $data->city."" : "";
                        $con['state']           = isset($data->state) ? $data->state."" : "";
                        $con['pincode']         = isset($data->pincode) ? $data->pincode."" : "";
                        $con['phone']           = isset($data->userphone) ? $data->userphone."" : "";
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
                    ->join('order_master', 'order_master.id','=','order.order_id')
                    ->select('product.*','order_master.status','users.first_name','order_master.id as order_id','order.variant as  odr_variant')
                    ->where('order.order_id','=',$data->order_id)
                    ->get();
                    $productnew = array();
                    
                    foreach ($products as $key => $product) {
                        $producttttt = array();
                        $producttttt['name']= isset($product->name) ? $product->name."" : "";
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
            $payment_type_cash      = 'Paiement à la livraison';
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
                $quantity_order = $data['variant'][0]['quantity'];
                

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
                $product_variant = ProductVariation::where('product_id',$orders->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();

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
                  $attribute    = json_encode($data['variant']);
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
                 $datatwo                = array(
                    'modification_date'     => $date,
                    'payment_status'        => $status,
                    'diff_price'            => $price_diff,
                    'total_price'           => $total,
                    'modification'          => 1,
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

    public function addneworder(Request $request){
        $header = $request->header('Authorization');
        if($header != null)
        {
            $addcart = $request->cart_id;
            $user    = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $token_error          = 'Invalid token.';
                $error                = 'Your cart not add please try again.';
                $message              = 'Your product has been added to the cart. Please go to cart.';
                $update_error         = 'Your cart not update please try again.';
                $update_msg           = 'Cart updated succefully.';
            }
            else{
                $token_error          = 'Jeton invalide.';
                $error                = "Votre panier n'est pas ajouté, veuillez réessayer.";
                $message              = 'Votre produit a été ajouté au panier. Veuillez aller au panier.';
                $update_error         = "Joue l'arbre contre votre panier Remarque.";
                $update_msg           = 'Cart Upadded Succulent.';
            }
            if(!$addcart)
            {
                $insert               = array();
                $insert['user_id']    = $user->id;
                $insert['product_id'] = $request->product_id;
                $insert['variant']    = json_encode($request->attribute);
                $cart_detail          = Cart::create($insert);
                if(!$cart_detail)
                {
                    return response()->json(['status' => '401', 'message' => $error], 401);
                }

                // $con              = array();
                // $con[]['cart_id'] = $cart_detail->id;
                // $message          = $message;
            }
            else{
                $datatwo = array(
                    'user_id'     => $user->id,
                    'product_id'  => $request->product_id,
                    'variant'     => json_encode($request->attribute),
                );
                $address          = Cart::where('id',$addcart)->update($datatwo);
                if(!$address)
                {
                    return response()->json(['status' => '401', 'message'=>$update_error], 401);
                }
                $con              = array();
                $con[]['cart_id'] = $request->cart_id;
                $message          = $update_msg;
            }
            //  $carts                       = Cart::where('user_id',$user->id)->get();
            // $curency                     = Settings::where('slug','=','currency')->first();
            // $datacount                   = count($carts);

            // if($datacount != 0){
            //     $insert                  = array();
            //     $insert['status']        = 0;
            //     $insert['user_id']       = $user->id;
            //     $insert['address_id']    = $request->address_id;
            //     $insert['payment_type']  = 0;
            //     $insert['total_price']   = $request->total_price;  // if pass total price this insert $request->totla_price;
            //     $insert['delievery_tag'] = $request->delievery_tag;
            //     $order_master            = OrderMaster::create($insert);

            //     foreach ($carts as $key => $cart) {
            //         $checkarray          = array();
            //         $cart_variant        = json_decode($cart->variant);
            //         $price               = explode($curency->contain, $cart_variant[0]->price);
            //         $sale_price          = explode($curency->contain, $cart_variant[0]->sale_price);
            //         $quantity            = $cart_variant[0]->quantity;

            //         foreach ($cart_variant as $key => $value) {
            //             foreach ($value as $key => $values) {
            //                 $dat = ProductAttributeType::where('name',$key)->first();
            //                 $att = ProductAttribute::where('name',$values)->first();
            //                 if(!empty($dat)){
            //                     $checkarray['a_'.$dat->id] = ''.$att->id;
            //                 }
            //             }
            //         }

            //         $checkarray['rprice'] = $price[0];
            //         $checkarray['sprice'] = $sale_price[0];
            //         $product_variant      = ProductVariation::where('product_id',$cart->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();

            //         foreach ($product_variant as $pro_variant) {
            //             $checkarray['quantity'] = $pro_variant->quantity;
            //             if($pro_variant->attribute == json_encode($checkarray)){
            //                 $qty = $pro_variant->quantity-$quantity;
            //                 $checkarray['quantity'] = ''.$qty;
            //                 $datatwo = array('quantity' => $checkarray['quantity'],'attribute' => json_encode($checkarray));
            //                 $pro_variant = ProductVariation::where('id',$pro_variant->id)->update($datatwo);
            //             }
            //         }
            //         $mailarray = array();
            //         $product_variantupdate = ProductVariation::where('product_id',$cart->product_id)->get();
            //         foreach ($product_variantupdate as $key => $product_variantupdates) {
            //             $mailarray[] = $product_variantupdates->attribute;
            //         }
            //         $attributes = "[".implode(",", $mailarray)."]";
            //         $pro_attribute = Product::where('id',$cart->product_id)->update(['attribute' => $attributes]);
            //         $order_insert = array();
            //         $order_insert['order_id']   = $order_master->id;
            //         $order_insert['product_id'] = $cart->product_id;
            //         $order_insert['user_id']    = $cart->user_id;
            //         $order_insert['variant']    = $cart->variant;
            //         $order  = Order::create($order_insert);
            //         $cart   = Cart::where('user_id',$user->id)->delete();  
            //     }
            //     $data['orderid'] = $order_master->id;
            //     $email = $user->email;
            //     $subject = $subject_place;
            //     Mail::send('emails.adminorder',['data' => $data,'name'=> $user->first_name,'language'=>$user->language], function($message) use($email,$subject)
            //     {
            //         $message->to($email, 'Nyotaapp')->subject($subject);
            //     });

            //     $success['status'] = '200';
            //     $success['message'] = $message;
            //     $success['data'] = [];  

            //     return response()->json($success, $this-> successStatus);
            // }
            // else{
            //     return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401);
            // }
            // $success['status']    = '200';
            // $success['message']   = $message;
            // $success['data']      = $con;
            // return response()->json($success, $this-> successStatus);
        }
        else{
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
    }
}