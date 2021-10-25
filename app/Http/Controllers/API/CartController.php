<?php

namespace App\Http\Controllers\API;



use Illuminate\Http\Request; 

use App\Http\Controllers\Controller; 

use App\Models\Cart;

use App\Models\User;


use App\Models\City;

use App\Models\Shipping;

use App\Models\Settings;

use App\Models\Product;

use App\Models\ProductVariation;

use DB;

use App\Models\ProductAttribute;

use App\Models\ProductAttributeType;

use Illuminate\Support\Facades\Auth; 



class CartController extends Controller 

{

	public $successStatus = 200;

	

	public function addcart(Request $request)

    {

        

        $header = $request->header('Authorization');

    		if($header != null)

    		{

            $addcart = $request->cart_id;

            $user = Auth()->guard('api')->user($header);

            if($user->language == 0){

                $token_error    = 'Invalid token.';

                $error          = 'Your cart not add please try again.';

                $message        = 'Your product has been added to the cart. Please go to cart.';

                $update_error   = 'Your cart not update please try again.';

                $update_msg     = 'Cart updated succefully.';

            }

            else{

                $token_error  = 'Jeton invalide.';

                $error        = "Votre panier n'est pas ajouté, veuillez réessayer.";

                $message      = 'Votre produit a été ajouté au panier. Veuillez aller au panier.';

                $update_error = "Joue l'arbre contre votre panier Remarque";

                $update_msg   = 'Cart Upadded Succulent.';
            }

            if(!$addcart)

            {

    			$insert = array();

    			$insert['user_id']  = $user->id;

            	$insert['product_id']   = $request->product_id;

            	$insert['variant']   = json_encode($request->attribute);

            	$cart_detail = Cart::create($insert);

            	if(!$cart_detail)

    			{
                    return response()->json(['status' => '401', 'message'=>$error], 401);
                }

                $con = array();

                $con[]['cart_id'] = $cart_detail->id;

                $message    =   $message;

            }

            else{

                $datatwo = array(

                'user_id'        => $user->id,

                'product_id'     => $request->product_id,

                'variant'        => json_encode($request->attribute),

                );

                $address= Cart::where('id',$addcart)->update($datatwo);

                if(!$address)

                {
                    return response()->json(['status' => '401', 'message'=>$update_error], 401);
                }

                $con = array();

                $con[]['cart_id'] = $request->cart_id;

                $message    =   $update_msg;
            }


			$success['status']  = '200';

        	$success['message'] = $message;

        	$success['data']  = $con;

        	return response()->json($success, $this-> successStatus);
        }

		else{
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }

	}

	public function deletecart(Request $request)

    {

    	$header = $request->header('Authorization');

        if($header != null)

        {

        	$user = Auth()->guard('api')->user($header);

          if($user->language == 0){

              $token_error   = 'Invalid token.';

              $error   = 'Your cart not delete please try again.';

              $message = 'success';

          }

          else{

              $token_error   = 'Jeton invalide.';

              $error   = "Votre panier ne sera pas supprimé, veuillez réessayer.";

              $message = 'Succès';

          }

        	$cart = Cart::where('id',$request->cart_id)->delete();

        	if(!$cart)

			{

				return response()->json(['status' => '401', 'message'=>$error], 401);

			}

			    $success['status']  = '200';

        	$success['message'] = $message;

        	$success['data']    = [];

        	return response()->json($success, $this-> successStatus);

        }

        else

        {

            return response()->json(['status' => '401', 'message'=>$token_error], 401); 

        }

    }

    public function getcart(Request $request)

    {


        $header = $request->header('Authorization');

        if($header != null)

        {

            $user = Auth()->guard('api')->user($header);
             // print_r($user);
             // die;

            if($user->language == 0){

              $token_error   = 'Invalid token.';

              $error   = 'Sorry item not found.';

              $message = 'success';

            }

            else{

                $token_error   = 'Jeton invalide.';

                $error   = "Désolé article introuvable.";

                $message = 'Succès';

            }



            
           $latitude = $request->latitude;
           $longitude = $request->longitude;
           $city = $request->city;




            $curency = Settings::where('slug','=','currency')->first();

            $maximum_price = Settings::where('slug','=','max_price')->first();

            $tax = Settings::where('slug','=','tax')->first();

           // $shipping_price = Settings::where('slug','=','shipping_price')->first();

            $shipping_price =  DB::table('shipping')->join('city','city.id','=','shipping.city_id')->select('shipping.shipping_price')->where('city.name',$request->city)->first();
             $default_shipping_price = Settings::where('slug','=','shipping_price')->first();
             if(!empty($shipping_price)){
                $prices = $shipping_price->$shipping_price;
            }else{
                $prices = $default_shipping_price->contain;
            }

            
            $express_delievery = Settings::where('slug','=','express_delievery')->first();

            $express_en = Settings::where('slug','=','express_en')->first();

            $express_fr = Settings::where('slug','=','express_fr')->first();

            $standard_delievery = Settings::where('slug','=','standard_delievery')->first();

            $standard_en = Settings::where('slug','=','standard_en')->first();

            $standard_fr = Settings::where('slug','=','standard_fr')->first();

            $datas =  DB::table('cart')

                ->join('product', 'cart.product_id','=','product.id')
                ->join('users','cart.user_id','=','users.id')
                //->join('users as p','product.user_id','=','p.id')
                //->join('city','city.id','=','p.city')
                //->join('city','city.id','=','users.city')
                ->select('product.*','cart.variant','cart.id as cartid','cart.product_id')
                //->where('city.name',$request->city)
                ->where('cart.user_id','=',$user->id)
                ->get();
            


                
        

            $curency    = Settings::where('slug','=','currency')->first();

            $datascount = $datas->count();

            if($datascount != 0)

            {

                foreach ($datas as $key => $data) {

                   

                    $con = array();



                    



                    $con['product_id'] = isset($data->id) ? $data->id."" : "";

                    $con['cart_id'] = isset($data->cartid) ? $data->cartid."" : "";

                    $con['product_name'] = isset($data->name) ? $data->name."" : "";

                    $medias = DB::table('media')->select('media.*')->where('media.module_id',$data->id)->where('media.module_type',0)->get();

                    if(count($medias) > 0)

                    {

                        $url = array();

                        foreach ($medias as  $media) {

                            $url[] = url('/storage/tmp/uploads').'/'.$media->image;



                        }

                        $con['product_image'] = $url;

                    }

                    else{

                        $con['product_image'] = "";

                    }

                    $con['short_description'] = isset($data->short_description) ? $data->short_description."" : "";

                    $con['full_description'] = isset($data->full_description) ? $data->full_description."" : "";

                    if($data->variant != 'null'){

                        $variant_attribute = json_decode($data->variant);

                        



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



                            	$variant_attribute[0]->total_qty = $pro_variant->quantity;

                            }

                        }

                        $con['attribute'] = $variant_attribute;

					}

                    else{

                        $con['attribute'] = "";

                    }

                      //$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                      $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->join('city','city.id','=','users.city')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->where('city.name',$request->city)->get();

                         
                    if(count($comment) > 0)

                    {

                        $main = array();

                        foreach ($comment as  $comment) {

                            $sub = array();

                            $sub['comment'] = $comment->comment;

                            $sub['user'] = $comment->username;

                            $main[] = $sub;



                        }

                        $con['comments'] = $main;

                    }

                    else{

                        $con['comments'] = "";

                    }

                      //$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');

                      $avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->join('city','city.id','=','users.city')->where('product_comment.product_id', $data->id)->where('city.name',$request->city)->groupBy('product_comment.product_id')->AVG('rating');

                 
                    if(count($avg_stars) > 0)

                    {

                        $con['avg_rating'] = round($avg_stars, 1);

                    }

                    else{

                        $con['avg_rating'] = "";

                    }



                    $confinal[] = $con;

                }



            }

            else

            {
                
                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 

            }

            $success['status']  = '200';

            $success['message'] = $message;

            $success['cart_value'] = $datascount;

            $success['tax'] = $tax->contain;

            $success['maximum_price'] = $maximum_price->contain;

            $success['shipping_price'] = $prices;
 
            $success['express_delievery'] = $express_delievery->contain;

            $success['express_en'] = $express_en->contain;

            $success['express_fr'] = $express_fr->contain;

            $success['standard_delievery'] = $standard_delievery->contain;

            $success['standard_en'] = $standard_en->contain;

            $success['standard_fr'] = $standard_fr->contain;


            $success['data']  = $confinal;

//print_r($confinal);exit();

            return response()->json($success, $this-> successStatus);

        }

        else

        {

            return response()->json(['status' => '401', 'message'=>$token_error], 401); 

        }

    }

}