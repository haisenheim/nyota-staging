<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Category;
use App\Models\Settings;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use DB;
use Illuminate\Support\Facades\Auth; 

class ProductController extends Controller 
{
	public $successStatus = 200;
	
	public function detail(Request $request)
    {
		$header = $request->header('Authorization');
		if($header != null)
		{
			$user 		= Auth()->guard('api')->user($header);
			if($user->language == 0){
          $token_error = 'Invalid token.';
          $error   		 = 'Product details not found.';
          $message 		= 'Product Details.';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Détails du produit introuvables.";
          $message = 'Détails du produit.';
      }



          $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;


			$curency 	= Settings::where('slug','=','currency')->first();

			$data 		= DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where('product.id',$request->product_id)->where('product.is_active', 0)->where('product.category_id', '!=', 0)->where('product.child_category_id', '!=', 0)->first();
			
//print_r($data);die();
                        $datascount = count($data);
			if($datascount != 0)
			{
				$confinal 	= array();
				$con 		= array();
				$con['product_id'] 		= isset($data->id) ? $data->id."" : "";
				$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
				$con['category_id']		= isset($data->category_id) ? $data->category_id."" : "";
				$con['sub_category_id']		= isset($data->child_category_id) ? $data->child_category_id."" : "";
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
				$carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();
				if(count($carts) > 0)
				{
					foreach ($carts as  $key=>$cart) {
						
						$attributesss 	= json_decode($cart->variant);
						$attributesss[0]->id = $cart->id;
						$con['cart'][] 	= $attributesss[0];
					}
				}
				else{

					$con['cart'] = "";
				}
				$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
				$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";
				$wishlist = DB::table('wishlist')->join('users','users.id','=','wishlist.user_id')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->groupBy('wishlist.product_id')->first();

//print_r($wishlist);die();
					if(count($wishlist) > 0){
						$con['wishListID'] = $wishlist->id;
					}
					else{
						$con['wishListID'] = "";
					}
				$mailatt = array();
				if(!empty($data->attribute_type_id)){
					$attribute_type_ids = explode(",",$data->attribute_type_id);
					foreach($attribute_type_ids as $attribute_type)
					{
						
						 $att = array(); 
						 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
						 $att['id'] 		= $attributetypesget->id;
						 $att['name'] 		= $attributetypesget->name;

						$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
						foreach($productAttributes as $productAttribute){
						    $subarray 		= array();
						    $subarray['id'] = $productAttribute->id;  
						    $subarray['name'] = $productAttribute->name;
						    $att['attr'][]	= $subarray;
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
						$variant['price'] 		= $attribute->rprice.$curency->contain;
						$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
						$variant['quantity'] 	= $attribute->quantity;
						$mainv[] = $variant;
					}
					$con['attribute'] = $mainv;
				}
				$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
//print_r($comment);die();
                                
                                if(count($comment) > 0)
				{
					$main = array();
					foreach ($comment as  $comment) {
						$sub = array();
						$sub['comment'] = $comment->comment;
						$sub['user'] 	= $comment->username;
						$main[] = $sub;

					}
					$con['comments'] = $main;
				}
				else{
					$con['comments'] = [];
				}
				$avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
//print_r($avg_stars);die();				
                                if(count($avg_stars) > 0)
				{
					$con['avg_rating'] = round($avg_stars, 1);
				}
				else{
					$con['avg_rating'] = "";
				}
				$confinal[] = $con;
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
			}
		}
		else
		{
			$language = request('languageCode');
			if($language == 0){
          $error   		 = 'Product details not found.';
          $message 		= 'Product Details.';
      }
      else{
          $error   = "Détails du produit introuvables.";
          $message = 'Détails du produit.';
      }
			$curency 	= Settings::where('slug','=','currency')->first();
			//$data 		= Product::where('id',$request->product_id)->where('is_active', 0)->where('category_id', '!=', 0)->where('child_category_id', '!=', 0)->first();


                         $data   =  DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where('product.id',$request->product_id)->where('product.is_active', 0)->where('product.category_id', '!=', 0)->where('product.child_category_id', '!=', 0)->first();
			

                        $datascount = count($data);
			if($datascount != 0)
			{
				$confinal 	= array();
				$con 		= array();
				$con['product_id'] 		= isset($data->id) ? $data->id."" : "";
				$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
				$con['category_id']		= isset($data->category_id) ? $data->category_id."" : "";
				$con['sub_category_id']		= isset($data->child_category_id) ? $data->child_category_id."" : "";
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
				// $carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();
				// if(count($carts) > 0)
				// {
				// 	foreach ($carts as  $key=>$cart) {
						
				// 		$attributesss 	= json_decode($cart->variant);
				// 		$attributesss[0]->id = $cart->id;
				// 		$con['cart'][] 	= $attributesss[0];
				// 	}
				// }
				// else{

				// 	$con['cart'] = "";
				// }
				$con['cart'] = "";
				$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
				$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";
				// $wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();

				// 	if(count($wishlist) > 0){
				// 		$con['wishListID'] = $wishlist->id;
				// 	}
				// 	else{
				// 		$con['wishListID'] = "";
				// 	}
					$con['wishListID'] = "";
				$mailatt = array();
				if(!empty($data->attribute_type_id)){
					$attribute_type_ids = explode(",",$data->attribute_type_id);
					foreach($attribute_type_ids as $attribute_type)
					{
						
						 $att = array(); 
						 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
						 $att['id'] 		= $attributetypesget->id;
						 $att['name'] 		= $attributetypesget->name;

						$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
						foreach($productAttributes as $productAttribute){
						    $subarray 		= array();
						    $subarray['id'] = $productAttribute->id;  
						    $subarray['name'] = $productAttribute->name;
						    $att['attr'][]	= $subarray;
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
						$variant['price'] 		= $attribute->rprice.$curency->contain;
						$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
						$variant['quantity'] 	= $attribute->quantity;
						$mainv[] = $variant;
					}
					$con['attribute'] = $mainv;
				}
				//$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
				
                                  $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                                if(count($comment) > 0)
				{
					$main = array();
					foreach ($comment as  $comment) {
						$sub = array();
						$sub['comment'] = $comment->comment;
						$sub['user'] 	= $comment->username;
						$main[] = $sub;

					}
					$con['comments'] = $main;
				}
				else{
					$con['comments'] = [];
				}
			    //	$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
				
                                $avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');


                                if(count($avg_stars) > 0)
				{
					$con['avg_rating'] = round($avg_stars, 1);
				}
				else{
					$con['avg_rating'] = "";
				}
				$confinal[] = $con;
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
			} 
		}
	}



	public function similardetail(Request $request){

		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Similar product details not found.';
          $message = 'Similar Product Details.';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Détails de produit similaires non trouvés.";
          $message = 'Détails du produit similaires.';
      }
		


        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $city = $request->city;
				$curency = Settings::where('slug','=','currency')->first();
		    //	$selectedproduct = Product::where('id',$request->product_id)->first();
 
        $selectedproduct = DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('product.id',$request->product_id)->where('city.name',$request->city)->first();
        

			  $query = array('is_active' => 0,'child_category_id'=>$selectedproduct->child_category_id);
		     	$datas = Product::where($query)->where('id','!=',$request->product_id)->where('category_id', '!=', 0)->where('child_category_id', '!=', 0)->limit(5)->get();
			
                      //  $datas = DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where($query)->where('city.name',$request->city)->where('product.is_active', 0)->where('product.category_id', '!=', 0)->where('product.child_category_id', '!=', 0)->limit(5)->get();

 
//print_r($datas);die();

      $datascount = $datas->count();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] 		= isset($data->id) ? $data->id."" : "";
					$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
					$con['category_id']		= isset($data->category_id) ? $data->category_id."" : "";
					$con['sub_category_id']		= isset($data->child_category_id) ? $data->child_category_id."" : "";
					$medias = DB::table('media')->select('media.*')->where('media.module_id',$data->id)->where('media.module_type',0)->get();
					if(count($medias) > 0)
					{
						$url = array();
						foreach ($medias as  $media) {
							$url[] 	= url('/storage/tmp/uploads').'/'.$media->image;

						}
						$con['product_image'] = $url;
					}
					else{
						$con['product_image'] = "";
					}
					$carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();
					if(count($carts) > 0)
					{
						foreach ($carts as  $key=>$cart) {
							
							$attributesss 	= json_decode($cart->variant);
							$attributesss[0]->id = $cart->id;
							$con['cart'][] 	= $attributesss[0];
						}
					}
					else{

						$con['cart'] = "";
					}
					$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
					$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";
				
                                    // $wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();

				       $wishlist = DB::table('wishlist')->join('users','users.id','=','wishlist.user_id')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->groupBy('wishlist.product_id')->first();


                                        if(count($wishlist) > 0){
						$con['wishListID'] = $wishlist->id;
					}
					else{
						$con['wishListID'] = "";
					}
					$mailatt = array();
					if(!empty($data->attribute_type_id)){
						$attribute_type_ids 	= explode(",",$data->attribute_type_id);
						foreach($attribute_type_ids as $attribute_type)
						{
							
							 $att = array(); 
							 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
							 $att['id'] 		= $attributetypesget->id;
							 $att['name'] 		= $attributetypesget->name;

							$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
							foreach($productAttributes as $productAttribute){
							    $subarray 		= array();
							    $subarray['id'] = $productAttribute->id;  
							    $subarray['name'] = $productAttribute->name;
							    $att['attr'][]	= $subarray;
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
							$variant['price'] 		= $attribute->rprice.$curency->contain;
							$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
							$variant['quantity'] 	= $attribute->quantity;
							$mainv[] = $variant;
						}
						$con['attribute'] = $mainv;
					}
					
                                      //  $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
					


                                          $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                                       if(count($comment) > 0)
					{
						$main = array();
						foreach ($comment as  $comment) {
							$sub = array();
							$sub['comment'] = $comment->comment;
							$sub['user'] 	= $comment->username;
							$main[] = $sub;

						}
						$con['comments'] = $main;
					}
					else{
						$con['comments'] = "";
					}

				      //$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
					
                                        $avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');


                                        if(count($avg_stars) > 0)
					{
						$con['avg_rating'] = round($avg_stars, 1);
					}
					else{
						$con['avg_rating'] = "";
					}
					$confinal[] 	= $con; 
			}
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
			return response()->json(['status' => '401', 'message' => $error, 'data' => []], 401); 
			}
		}
		else
		{
			$language = request('languageCode');
			if($language == 0){
          $error   = 'Similar product details not found.';
          $message = 'Similar Product Details.';
      }
      else{
         	$error   = "Détails de produit similaires non trouvés.";
          $message = 'Détails du produit similaires.';
      }
			$curency = Settings::where('slug','=','currency')->first();
			//$selectedproduct = Product::where('id',$request->product_id)->first();

                          $selectedproduct = DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('product.id',$request->product_id)->where('city.name',$request->city)->first();
			

                         $query = array('is_active' => 0,'child_category_id'=>$selectedproduct->child_category_id);
		       //$datas = Product::where($query)->where('id','!=',$request->product_id)->where('category_id', '!=', 0)->where('child_category_id', '!=', 0)->limit(5)->get();

                         $datas = Product::where($query)->where('id','!=',$request->product_id)->where('category_id', '!=', 0)->where('child_category_id', '!=', 0)->limit(5)->get();

			$datascount = $datas->count();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] 		= isset($data->id) ? $data->id."" : "";
					$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
					$con['category_id']		= isset($data->category_id) ? $data->category_id."" : "";
					$con['sub_category_id']		= isset($data->child_category_id) ? $data->child_category_id."" : "";
					$medias = DB::table('media')->select('media.*')->where('media.module_id',$data->id)->where('media.module_type',0)->get();
					if(count($medias) > 0)
					{
						$url = array();
						foreach ($medias as  $media) {
							$url[] 	= url('/storage/tmp/uploads').'/'.$media->image;

						}
						$con['product_image'] = $url;
					}
					else{
						$con['product_image'] = "";
					}
					// $carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();
					// if(count($carts) > 0)
					// {
					// 	foreach ($carts as  $key=>$cart) {
							
					// 		$attributesss 	= json_decode($cart->variant);
					// 		$attributesss[0]->id = $cart->id;
					// 		$con['cart'][] 	= $attributesss[0];
					// 	}
					// }
					// else{

					// 	$con['cart'] = "";
					// }
					$con['cart'] = "";
					$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
					$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";
					// $wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();

					// if(count($wishlist) > 0){
					// 	$con['wishListID'] = $wishlist->id;
					// }
					// else{
					// 	$con['wishListID'] = "";
					// }
					$con['wishListID'] = "";
					$mailatt = array();
					if(!empty($data->attribute_type_id)){
						$attribute_type_ids 	= explode(",",$data->attribute_type_id);
						foreach($attribute_type_ids as $attribute_type)
						{
							
							 $att = array(); 
							 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
							 $att['id'] 		= $attributetypesget->id;
							 $att['name'] 		= $attributetypesget->name;

							$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
							foreach($productAttributes as $productAttribute){
							    $subarray 		= array();
							    $subarray['id'] = $productAttribute->id;  
							    $subarray['name'] = $productAttribute->name;
							    $att['attr'][]	= $subarray;
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
							$variant['price'] 		= $attribute->rprice.$curency->contain;
							$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
							$variant['quantity'] 	= $attribute->quantity;
							$mainv[] = $variant;
						}
						$con['attribute'] = $mainv;
					}
				
                                      //$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
					
                                        $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                                       if(count($comment) > 0)
					{
						$main = array();
						foreach ($comment as  $comment) {
							$sub = array();
							$sub['comment'] = $comment->comment;
							$sub['user'] 	= $comment->username;
							$main[] = $sub;

						}
						$con['comments'] = $main;
					}
					else{
						$con['comments'] = "";
					}
					
                                      //$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
					
                                        $avg_stars = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');


                                        if(count($avg_stars) > 0)
					{
						$con['avg_rating'] = round($avg_stars, 1);
					}
					else{
						$con['avg_rating'] = "";
					}
					$confinal[] 	= $con; 
			}
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message' => $error, 'data' => []], 401); 
			}
		}
	}

	public function search(Request $request){

		$header = $request->header('Authorization');
		if($header != null)
		{

			$user 	= Auth()->guard('api')->user($header);
			if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Sorry item not found.';
          $message = 'Item list data.';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Désolé article introuvable.";
          $message = "Données de liste d'articles.";
      }



          $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;

			$curency = Settings::where('slug','=','currency')->first();
			
                       // $datas 	= Product::where('is_active','=',0)->where('name','like','%'.$request->product_name.'%')->get();

                          $datas	= DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where('product.is_active', 0)->where('product.name','like','%'.$request->product_name.'%')->get();


			$datascount = $datas->count();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] 	= isset($data->id) ? $data->id."" : "";

					$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
					$medias = DB::table('media')->select('media.*')->where('media.module_id',$data->id)->where('media.module_type',0)->get();

					if(count($medias) > 0)
					{
						$url 	= array();
						foreach ($medias as  $media) {
							$url[] = url('/storage/tmp/uploads').'/'.$media->image;

						}
						$con['product_image'] = $url;
					}
					else{
						$con['product_image'] = "";
					}
					$carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();

					if(count($carts) > 0)
					{
						foreach ($carts as  $key=>$cart) {
							
							$attributesss 	= json_decode($cart->variant);
							$attributesss[0]->id = $cart->id;
							$con['cart'][]	= $attributesss[0];
						}
					}
					else{

						$con['cart'] = "";
					}

					$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
					$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";

				//$wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$data->id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();
					
                                  $wishlist = DB::table('wishlist')->join('users','users.id','=','wishlist.user_id')->select('wishlist.*')->where('wishlist.product_id',$data->id)->groupBy('wishlist.product_id')->first();
				

                                   	if(count($wishlist) > 0){
						$con['wishListID'] = $wishlist->id;
					}
					else{
						$con['wishListID'] = "";
					}
					
					$mailatt = array();
					if(!empty($data->attribute_type_id)){
						$attribute_type_ids = explode(",",$data->attribute_type_id);
						foreach($attribute_type_ids as $attribute_type)
						{
							
							 $att = array(); 
							 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
							 $att['id'] 		= $attributetypesget->id;
							 $att['name'] 		= $attributetypesget->name;

							$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
							foreach($productAttributes as $productAttribute){
							    $subarray 		= array();
							    $subarray['id'] = $productAttribute->id;  
							    $subarray['name'] = $productAttribute->name;
							    $att['attr'][]	= $subarray;
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
							$variant['price'] 		= $attribute->rprice.$curency->contain;
							$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
							$variant['quantity'] 	= $attribute->quantity;
							$mainv[] = $variant;
						}
						$con['attribute'] = $mainv;
					}

				//$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
				
                                  $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();



                                 	if(count($comment) > 0)
					{
						$main = array();
						foreach ($comment as  $comment) {
							$sub = array();
							$sub['comment'] = $comment->comment;
							$sub['user'] 	= $comment->username;
							$main[] = $sub;

						}
						$con['comments'] = $main;
					}
					else{
						$con['comments'] = "";
					}

				//$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
				
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
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{

				return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
			}
		}
		else
		{
			$language = request('languageCode');
			if($language == 0){
          $error   = 'Sorry item not found.';
          $message = 'Item list data.';
      }
      else{
         	$error   = "Désolé article introuvable.";
          $message = "Données de liste d'articles.";
      }
			$curency = Settings::where('slug','=','currency')->first();

		       //$datas 	= Product::where('is_active','=',0)->where('name','like','%'.$request->product_name.'%')->get();

                         $datas	= DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where('product.is_active', 0)->where('product.name','like','%'.$request->product_name.'%')->where('city.name',$request->city)->get();


			$datascount = $datas->count();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] 	= isset($data->id) ? $data->id."" : "";

					$con['product_name'] 	= isset($data->name) ? $data->name."" : "";
					$medias = DB::table('media')->select('media.*')->where('media.module_id',$data->id)->where('media.module_type',0)->get();

					if(count($medias) > 0)
					{
						$url 	= array();
						foreach ($medias as  $media) {
							$url[] = url('/storage/tmp/uploads').'/'.$media->image;

						}
						$con['product_image'] = $url;
					}
					else{
						$con['product_image'] = "";
					}
					// $carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();

					// if(count($carts) > 0)
					// {
					// 	foreach ($carts as  $key=>$cart) {
							
					// 		$attributesss 	= json_decode($cart->variant);
					// 		$attributesss[0]->id = $cart->id;
					// 		$con['cart'][]	= $attributesss[0];
					// 	}
					// }
					// else{

					// 	$con['cart'] = "";
					// }
					$con['cart'] = "";
					$con['short_description'] 	= isset($data->short_description) ? $data->short_description."" : "";
					$con['full_description'] 	= isset($data->full_description) ? $data->full_description."" : "";

					// $wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$data->id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();
					
					// if(count($wishlist) > 0){
					// 	$con['wishListID'] = $wishlist->id;
					// }
					// else{
					// 	$con['wishListID'] = "";
					// }
					$con['wishListID'] = "";
					$mailatt = array();
					if(!empty($data->attribute_type_id)){
						$attribute_type_ids = explode(",",$data->attribute_type_id);
						foreach($attribute_type_ids as $attribute_type)
						{
							
							 $att = array(); 
							 $attributetypesget = ProductAttributeType::where('id','=',$attribute_type)->first();
							 $att['id'] 		= $attributetypesget->id;
							 $att['name'] 		= $attributetypesget->name;

							$productAttributes 	= ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
							foreach($productAttributes as $productAttribute){
							    $subarray 		= array();
							    $subarray['id'] = $productAttribute->id;  
							    $subarray['name'] = $productAttribute->name;
							    $att['attr'][]	= $subarray;
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
							$variant['price'] 		= $attribute->rprice.$curency->contain;
							$variant['sale_price'] 	= $attribute->sprice.$curency->contain;
							$variant['quantity'] 	= $attribute->quantity;
							$mainv[] = $variant;
						}
						$con['attribute'] = $mainv;
					}

				//$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
					
                                  $comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();


                                        if(count($comment) > 0)
					{
						$main = array();
						foreach ($comment as  $comment) {
							$sub = array();
							$sub['comment'] = $comment->comment;
							$sub['user'] 	= $comment->username;
							$main[] = $sub;

						}
						$con['comments'] = $main;
					}
					else{
						$con['comments'] = "";
					}

				//$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
					

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
				$success['status'] 	= '200';
				$success['message'] = $message;
				
				$success['data'] 	= $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
			}
		}
	}
}
