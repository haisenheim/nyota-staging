<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\OrderMaster;
use App\Models\ProductVariation;
use App\Models\Order;
use App\Models\Product;
use App\Models\Settings;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeType;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use jeremykenedy\LaravelRoles\Models\Role;
use DB;
use Mail;
class PaymentController extends Controller 
{
	public $successStatus = 200;
    public function createorder(Request $request)
    { 
        if(isset($_GET['status'])) 
        {
           
            $status       = $_GET['status'];
            $msisdn       = $_GET['msisdn'];
            $amount       = $_GET['amount'];
           // $delievry_tag = $_GET['delievery_tag'];
            $order        = $_GET['transaction_id'];
            $new_order    = explode('_', $order);
            $order_id     = $new_order[1];   

            $query = array('phone' => $msisdn);
            $datas = User::where($query)->first();
            if($datas->language == 0){
                $user_error    = 'Payment Fail, User not found';
                $error         = 'Your cart is empty';
                $message       = 'Payment Success';
                $payment_title = 'Payment';
                $payment_fail  = 'Payment Fail';
            }
            else{
                $user_error    = 'Échec de paiement, utilisateur introuvable';
                $error         = "Votre panier est vide";
                $message       = 'Succès de paiement';
                $payment_title = 'Paiement';
                $payment_fail  = 'Échec de paiement';
                
            }
            $curency    = Settings::where('slug','=','currency')->first();
            $usercount = count($datas);
           
            if($usercount != 0){
                if($status == 200)
                {
                    $carts= Cart::where('user_id',$datas->id)->get();
                    $datacount = count($carts);
                    if($datacount != 0){
                  if($order_id == 0){

                        $insert = array();
                        $insert['status']  = 0;
                        $insert['user_id'] = $carts[0]->user_id;
                        $insert['payment_type'] = 1;
                        $insert['total_price']  = $amount;  // if pass total price this insert $request->totla_price;
                        //$insert['delievery_tag'] = $delievery_tag;
                        $order_master = OrderMaster::create($insert);
                 }else{
                    $products = DB::table('product')
                    ->join('order','product.id','=','order.product_id')
                    ->join('users', 'users.id','=','order.user_id')
                    ->join('order_master', 'order_master.id','=','order.order_id')
                    ->select('product.*','order_master.status','users.first_name','order_master.id as order_id','order.variant as  odr_variant','order.id as id','order.variant as  odr_variant','order.product_id as product_id')
                    ->where('order.order_id','=',$order_id)
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
                    $order_master_get        = OrderMaster::where('id',$order_id)->first();
                    $prev_order              = json_encode($productnew);
                    $total                   = $amount+$order_master_get->total_price;
                    $update_order            = ['modification_type' => 0,'modification_total' => $amount,'modification' => 1, 'modification_date' => $date,'total_price' => $total,'payment_status' => 0,'diff_price' => $amount,'previous_order' => $prev_order];
                    $order                   = OrderMaster::where('id',$order_id)->update($update_order);
                 }


                        foreach ($carts as $key => $cart) {

                            $checkarray = array();

                            $cart_variant = json_decode($cart->variant);
                            $price        = explode($curency->contain, $cart_variant[0]->price);
                            $sale_price   = explode($curency->contain, $cart_variant[0]->sale_price);
                            $quantity     = $cart_variant[0]->quantity;

                            foreach ($cart_variant as $key => $value) {
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
                            $product_variant = ProductVariation::where('product_id',$cart->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();

                            foreach ($product_variant as $pro_variant) {
                                $checkarray['quantity'] = $pro_variant->quantity;
                                
                                if($pro_variant->attribute == json_encode($checkarray)){
                                    
                                    $qty = $pro_variant->quantity-$quantity;
                                    $checkarray['quantity'] = ''.$qty;
                                    
                                    $datatwo = array('quantity' => $checkarray['quantity'],'attribute' => json_encode($checkarray));

                                    $pro_variant = ProductVariation::where('id',$pro_variant->id)->update($datatwo);
                                }
                            }
                            $mailarray = array();
                            $product_variantupdate = ProductVariation::where('product_id',$cart->product_id)->get();
                            foreach ($product_variantupdate as $key => $product_variantupdates) {
                                $mailarray[] = $product_variantupdates->attribute;
                            }
                            $attributes = "[".implode(",", $mailarray)."]";
                            $pro_attribute = Product::where('id',$cart->product_id)->update(['attribute' => $attributes]);
                            

                            $order_insert = array();
                            $order_insert['order_id']   = $order_id == 0 ? $order_master->id : $order_id;
                            $order_insert['product_id'] = $cart->product_id;
                            $order_insert['user_id']    = $cart->user_id;
                            $order_insert['variant']    = $cart->variant;
                            $order  = Order::create($order_insert);
                            $cart   = Cart::where('user_id',$datas->id)->delete();
                        }

                        $user_token =  DB::table('users')
                        ->join('role_user', 'role_user.user_id','=','users.id')
                        ->select('users.*')
                        ->whereNotNull('users.device_token')
                        ->where('users.id','=',$carts[0]->user_id)
                        ->where('role_user.role_id','=','2')
                        ->first();
                        if($order_id == 0){
                            $order = $order_master->id;
                        }else{
                            $order = $order_id;
                        }
                       
                        if(!empty($user_token->device_token)){
                            $json_data =[
                                "to" => $user_token->device_token,
                                "data" => [
                                    "body"  => $order,
                                    "title" => $payment_title,
                                ],
                            ];
                        }
                        else{

                            $json_data =[
                                "to" => '',
                                "data" => [
                                    "body"  => $order,
                                    "title" => $payment_title,
                                ],
                            ];
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
                        $success['status'] = '200';
                        $success['message'] = $message;
                        $success['data'] = [];
                        return response()->json($success, $this-> successStatus);
                    }
                    else{
                        return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401);
                    }
                }
                else
                {
                    $carts= Cart::where('user_id',$datas->id)->get();
                    $datacount = count($carts);
                    if($datacount != 0){
                       
                        $user_token =  DB::table('users')
                        ->join('role_user', 'role_user.user_id','=','users.id')
                        ->select('users.*')
                        ->whereNotNull('users.device_token')
                        ->where('users.id','=',$carts[0]->user_id)
                        ->where('role_user.role_id','=','2')
                        ->first();
                       
                        
                        if(!empty($user_token->device_token)){
                            $json_data =[
                                "to" => $user_token->device_token,
                                "data" => [
                                    "body"  => $payment_fail,
                                    "title" => $payment_title,
                                ],
                            ];
                        }
                        else{
                            $json_data =[
                                "to" => '',
                                "data" => [
                                    "body"  => $payment_fail,
                                    "title" => $payment_title,
                                ],
                            ];
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
                    else{
                        return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401);
                    }
                }
            }
            else{
                return response()->json(['status' => '401', 'message'=>$user_error, 'data' => []], 401);
            }
        }
    }

    public function addorder(Request $request){


        $language = $request->languageCode;
        if($language == 0){
            $error   = 'Your order address not add please try again.';
            $message = 'Order address added successfully.';
            $subject_place = 'New Order Placed';
        }
        else{
            $error   = "Votre note d'adresse de commande sera à côté de l'arbre suivant.";
            $message = 'Adresse de commande sous-sol adressé.';
            $subject_place = 'Nouvelle commande passée';
        }

		$order_id =  $request->order_id;
                
                 

        $datatwo = array(
			'address_id'          => $request->address_id,
            'delievery_tag'       => $request->delievery_tag,
		);
		$address= OrderMaster::where('id',$order_id)->update($datatwo);
		if(!$address)
		{
			return response()->json(['status' => '401', 'message'=>$error], 401);
		}
        $success['status']      = '200';
		$success['message']     = $message;
		$success['data']        = [];
		
        $data['orderid'] = $order_id;
        $email = 'contact@nyota-app.com';
        $subject = $subject_place;
        Mail::send('emails.adminorder',['data' => $data,'language'=>$request->languageCode], function($message) use($email,$subject)
        {
            $message->to($email, 'Nyotaapp')->subject($subject);
        });


        return response()->json($success, $this-> successStatus);

    }
	
	public function temppeyment(Request $request){ 
	
		$header = $request->header('Authorization');
		$user = Auth()->guard('api')->user($header);
		$carts= Cart::where('user_id',$user->id)->get();
		if(!empty($carts)){
        		
			$insert = array();
			$insert['status']  = 0;
			$insert['address_id'] = $request->address_id;
			$insert['user_id'] = $user->id;
			$insert['total_price']  = $request->total;  // if pass total price this insert $request->totla_price;
    		$order_master = OrderMaster::create($insert);
	        	
        	foreach ($carts as $key => $cart) {
        		$order_insert = array();
				$order_insert['order_id']  	= $order_master->id;
				$order_insert['product_id'] = $cart->product_id;
				$order_insert['user_id']  	= $cart->user_id;
				$order_insert['variant']  	= $cart->variant;
				$order 	= Order::create($order_insert);
				
				$cart 	= Cart::where('user_id',$user->id)->delete();
        	}
        }
		$success['status']      = '200';
        $success['message']     = 'Order Added Successfully.';
        $success['data']        = [];
        return response()->json($success, $this-> successStatus);
	}

    public function cashondelivery(Request $request){

        $header = $request->header('Authorization');
        if($header != null)
        {
            $user  = Auth()->guard('api')->user($header);
            // print_r($user->id);
            // die;
            if($user->language == 0){
                $token_error                = 'Invalid token.';
                $error                      = 'Your cart is empty';
                $message                    = 'Payment Success';
                $subject_place              = 'New Order Placed';
            }
            else{
                $token_error                = 'Jeton invalide.';
                $error                      = "Votre panier est vide";
                $message                    = 'Succès de paiement';
                $subject_place              = 'Nouvelle commande passée';
            }
            $carts                          = Cart::where('user_id',$user->id)->get();
            $curency                        = Settings::where('slug','=','currency')->first();
            $datacount                      = count($carts);

            if($datacount != 0){
                $date                       = date('Y-m-d H:i:s');
                if($request->order_id == 0){
                    $insert                  = array();
                    $insert['status']        = 0;
                    $insert['user_id']       = $user->id;
                    $insert['address_id']    = $request->address_id;
                    $insert['payment_type']  = 0;
                    $insert['total_price']   = $request->total_price;  // if pass total price this insert $request->totla_price;
                    $insert['delievery_tag'] = $request->delievery_tag;
                    $order_master            = OrderMaster::create($insert);
                }else{
                    
                    $order_master_get        = OrderMaster::where('id',$request->order_id)->first();
                    $prev_order              = json_encode($request->detail);
                    $total                   = $request->total_price+$order_master_get->total_price;
                    $update_order            = ['modification_type' => 0,'modification_total' => $request->total_price,'modification' => 1, 'modification_date' => $date,'total_price' => $total,'payment_status' => 0,'diff_price' => $request->total_price,'previous_order' => $prev_order];
                    $order                   = OrderMaster::where('id',$request->order_id)->update($update_order);
                }
                foreach ($carts as $key => $cart) {
                    $checkarray              = array();
                    $cart_variant            = json_decode($cart->variant);
                    $price                   = explode($curency->contain, $cart_variant[0]->price);
                    $sale_price              = explode($curency->contain, $cart_variant[0]->sale_price);
                    $quantity                = $cart_variant[0]->quantity;

                    foreach ($cart_variant as $key => $value) {
                        foreach ($value as $key => $values) {
                            $dat = ProductAttributeType::where('name',$key)->first();
                            $att = ProductAttribute::where('name',$values)->first();
                            if(!empty($dat)){
                                $checkarray['a_'.$dat->id] = ''.$att->id;
                            }
                        }
                    }

                    $checkarray['rprice']           = $price[0];
                    $checkarray['sprice']           = $sale_price[0];
                    $product_variant                = ProductVariation::where('product_id',$cart->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();

                    foreach ($product_variant as $pro_variant){
                        $checkarray['quantity']     = $pro_variant->quantity;
                        if($pro_variant->attribute == json_encode($checkarray)){
                            $qty                    = $pro_variant->quantity-$quantity;
                            $checkarray['quantity'] = ''.$qty;
                            $datatwo                = array('quantity' => $checkarray['quantity'],'attribute' => json_encode($checkarray));
                            $pro_variant            = ProductVariation::where('id',$pro_variant->id)->update($datatwo);
                        }
                    }
                    $mailarray = array();
                    $product_variantupdate = ProductVariation::where('product_id',$cart->product_id)->get();
                    foreach ($product_variantupdate as $key => $product_variantupdates) {
                        $mailarray[] = $product_variantupdates->attribute;
                    }
                    $attributes                 = "[".implode(",", $mailarray)."]";
                    $pro_attribute              = Product::where('id',$cart->product_id)->update(['attribute' => $attributes]);
                    $order_insert               = array();
                    $order_insert['order_id']   = $request->order_id == 0 ? $order_master->id : $request->order_id;
                    $order_insert['product_id'] = $cart->product_id;
                    $order_insert['user_id']    = $cart->user_id;
                    $order_insert['variant']    = $cart->variant;
                    if($request->order_id != 0){
                        $order_insert['product_status']    = 1;
                    }
                    $order                      = Order::create($order_insert);
                    $cart                       = Cart::where('user_id',$user->id)->delete();  
                }
                $data['orderid'] = $request->order_id == 0 ? $order_master->id : $request->order_id;
                $email = 'contact@nyota-app.com';
                $subject = $subject_place;
                Mail::send('emails.adminorder',['data' => $data,'name'=> $user->first_name,'language'=>$user->language], function($message) use($email,$subject)
                {
                    $message->to($email, 'Nyotaapp')->subject($subject);
                });

                $success['status']  = '200';
                $success['message'] = $message;
                $success['data']    = [];  

                return response()->json($success, $this-> successStatus);
            }
            else{
                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401);
            }
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
    }
}