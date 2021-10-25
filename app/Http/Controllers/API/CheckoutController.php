<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Cart;
use App\Models\ProductVariation;
use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\Settings;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth; 

class CheckoutController extends Controller 
{
	public $successStatus = 200;
	
	public function checkout(Request $request)
    {
        
        $header = $request->header('Authorization');
		if($header != null)
		{
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
                $token_error   = 'Invalid token.';
                $error         = 'Sorry item not found.';
                $product_error = 'Product not avaiable';
                $message       = 'success';
            }
            else{
                $token_error   = 'Jeton invalide.';
                $error         = "Désolé article introuvable.";
                $product_error = 'Produit non disponible';
                $message       = 'Succès';
            }
            $datas      = Cart::where('user_id',$user->id)->get();

            $curency    = Settings::where('slug','=','currency')->first();
            $datascount = $datas->count();
             $msgarray  = array();
            if($datascount != 0)
            {
               
                foreach ($datas as $key => $data) {

                    $checkarray = array();

                    $cart_variant = json_decode($data->variant);
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

                    $product_variant = ProductVariation::where('product_id',$data->product_id)->where('sale_price',$sale_price[0])->where('regular_price',$price[0])->get();

                    foreach ($product_variant as $pro_variant) {
                       
                        $checkarray['quantity'] = $pro_variant->quantity;

                        if($pro_variant->attribute == json_encode($checkarray)){
                            if($pro_variant->quantity >= $quantity){
                            }else{
                                $products = Product::where('id',$pro_variant->product_id)->first();
                                $msgarray[] = $products->name." has maximum ".$pro_variant->quantity." quantity.";    
                            }

                        }
                    }

                }
                if(empty($msgarray)){
                    $success['status']  = '200';
                    $success['message'] = $message;
                    $success['data']  = [];
                    return response()->json($success, $this-> successStatus);
                }

                else{

                    $success['status']  = '401';
                    $success['message'] = $product_error;
                    $success['data']  = $msgarray;
                    return response()->json($success, $this-> successStatus);
                }

            }
             else
            {

                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
            }
        }
		else
		{
			return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
		}		 
	}
}