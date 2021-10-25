<?php

namespace App\Http\Controllers\API;



use Illuminate\Http\Request; 

use App\Http\Controllers\Controller; 

use App\Models\WishList;

use App\Models\Settings;

use App\Models\Cart;

use App\Models\City;

use App\Models\User;

use App\Models\Product;

use App\Models\ProductAttributeType;

use App\Models\ProductAttribute;

use DB;

use Illuminate\Support\Facades\Auth; 



class WishListController extends Controller 

{

	public $successStatus = 200;

	

	public function addwishlist(Request $request)

    {

    	$header = $request->header('Authorization');

		if($header != null)

		{

			$user = Auth()->guard('api')->user($header);

            if($user->language == 0){

                $token_error   = 'Invalid token.';

                $error   = 'Your wishlist not add please try again.';

                $message = 'Added to wishlist';

            }

            else{

                $token_error   = 'Jeton invalide.';

                $error   = "Votre liste de souhaits n'ajoute pas, veuillez réessayer.";

                $message = 'Ajouté à la liste de souhaits';

            }

            $insert = array();

			$insert['user_id']  = $user->id;

        	$insert['product_id']   = $request->product_id;

        	$insert['variant']   = json_encode($request->attribute);

        	$comment = WishList::create($insert);

        	if(!$comment)

			{

				return response()->json(['status' => '401', 'message'=>$error], 401);

			}

            $con = array();

            $con['wishListId']  = isset($comment->id) ? $comment->id."" : "";

            if(!empty($request->attribute)){



                $con['attribute']  = $request->attribute;

            }

            else{

                $con['attribute']  = "";

            }

           // $con['attribute']  = isset($request->attribute) ? $request->attribute."" : "";

            $confinal[] = $con;

			$success['status']  = '200';

        	$success['message'] = $message;

        	$success['data']    = $confinal;

        	return response()->json($success, $this-> successStatus);

		}

		else

		{

			return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 

		}		 

	}

	

	public function deletewishlist(Request $request)

    {

    	$header = $request->header('Authorization');

        if($header != null)

        {

        	$user = Auth()->guard('api')->user($header);

            if($user->language == 0){

                $token_error   = 'Invalid token.';

                $error   = 'Your wishlist not delete please try again.';

                $message = 'success';

            }

            else{

                $token_error   = 'Jeton invalide.';

                $error   = "Votre liste de souhaits ne doit pas être supprimée, veuillez réessayer.";

                $message = 'Succès';

            }

        	$wishlist = WishList::where('id',$request->wishlist_id)->delete();

        	if(!$wishlist)

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

	public function getwishlist(Request $request)

    {

  		$header = $request->header('Authorization');

        if($header != null)

        {

            $user = Auth()->guard('api')->user($header);

            if($user->language == 0){

                $token_error   = 'Invalid token.';

                $error   = 'Sorry item not found.';

                $message = 'success';

            }

            else{

                $token_error   = 'Jeton invalide.';

                $error   = 'Désolé article introuvable.';

                $message = 'success';

            }

            $curency = Settings::where('slug','=','currency')->first();

            $datas =  DB::table('wishlist')

            ->join('product', 'wishlist.product_id','=','product.id')

            ->join('users','users.id','=','product.user_id')
            ->join('city','city.id','=','users.city')
            ->select('product.*','wishlist.variant','wishlist.id as wishlistid','wishlist.variant')
            ->where('city.name',$request->city)
            ->where('wishlist.user_id','=',$user->id)

            ->orderBy('wishlist.id', 'desc')

            ->get();

            $datascount = $datas->count();

            if($datascount != 0)

            {

                foreach ($datas as $key => $data) {

                    $con = array();

                    $con['product_id'] = isset($data->id) ? $data->id."" : "";

                    $con['category_id'] = isset($data->category_id) ? $data->category_id."" : "";

                    $con['sub_category_id'] = isset($data->child_category_id) ? $data->child_category_id."" : "";

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

                        $con['product_image'] = [];

                    }

                    $carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();

                    if(count($carts) > 0)

                    {

                      foreach ($carts as  $key=>$cart) {

                        $attributesss = json_decode($cart->variant);

                        $attributesss[0]->id = $cart->id;

                        $con['cart'][] = $attributesss[0];

                      }

                    }

                    else{

                      $con['cart'] = "";

                    }

                    $con['short_description'] = isset($data->short_description) ? $data->short_description."" : "";

                    $con['full_description'] = isset($data->full_description) ? $data->full_description."" : "";

                    $con['wishListID'] = isset($data->wishlistid) ? $data->wishlistid."" : "";



                    if(!empty($data->variant)){



                        $con['wishlist_attribute'] = json_decode($data->variant);

                    }

                    else{



                        $con['wishlist_attribute'] = "";

                    }



                    $mailatt = array();

                    if(!empty($data->attribute_type_id)){

                        $attribute_type_ids = explode(",",$data->attribute_type_id);

                        foreach($attribute_type_ids as $attribute_type)

                        {

                            

                             $att = array(); 

                             $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();

                             $att['id'] = $attributetypesget->id;

                             $att['name'] = $attributetypesget->name;



                            $productAttributes = ProductAttribute::where('type_id','=',$attributetypesget->id)->get();

                            foreach($productAttributes as $productAttribute){

                                $subarray = array();

                                $subarray['id'] = $productAttribute->id;  

                                $subarray['name'] = $productAttribute->name;

                                $att['attr'][]= $subarray;

                            }

                            $mailatt[] = $att;

                        }

                    }

                    if(!empty($data->attribute)){

                        $attributes = json_decode($data->attribute);

                        $mainv = array();

                        foreach ($attributes as  $attribute) {

                            $variant = array();

                            foreach ($mailatt as $key => $mailat) {

                                $iii = 'a_'.$mailat['id'];

                                $att_name = $mailat['name'];

                                foreach ($mailat['attr'] as $matt) {

                                    if($matt['id'] == $attribute->$iii){

                                        $variant[$att_name] = $matt['name'];

                                    }

                                }

                            }

                            $variant['price'] = $attribute->rprice.$curency->contain;

                            $variant['sale_price'] = $attribute->sprice.$curency->contain;

                            $variant['quantity'] = $attribute->quantity;

                            $mainv[] = $variant;

                        }

                        $con['attribute'] = $mainv;

                    }

                    else{

                        $con['attribute'] = "";

                    }

                  //  $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                      $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();

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

                   // $avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');

                      $avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');


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

            $success['data']  = $confinal;

            return response()->json($success, $this-> successStatus);

        }

        else

        {

            return response()->json(['status' => '401', 'message'=>$token_error], 401); 

        }

	}

}

