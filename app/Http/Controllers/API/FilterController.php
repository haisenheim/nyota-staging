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

class FilterController extends Controller 
{
	public $successStatus = 200;
	
	public function filter(Request $request){
		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			// print_r($user);
			// die;
			if($user->language == 0){
          $message = 'Item list data.';
          $error   = 'Sorry item not found.';
          $token_error   = 'Invalid token.';
      }
      if($user->language == 1){
          $message = "Données de liste d'articles.";
          $error   = "Désolé article introuvable.";
          $token_error   = 'Jeton invalide.';
      }
			$low = $request->price_low;
			$category_id = $request->category_id;
			$high = $request->price_high;
			$order = "";
			

			if($request->type == 1){
				$order = "`product_variation`.`sale_price`*1 ASC";
			}
			if($request->type == 2){
				$order = "`product_variation`.`sale_price`*1 DESC";	
			}
			if($request->type == 3){
				$order ="`rating` DESC";
			}
			$selectdata = "";
			$having = "";
			$user_data = "";
			if($request->type == 4){
				$selectdata = " , ( 3959 * acos( cos( radians(".$request->lat.") ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians(".$request->long.") ) + sin( radians(".$request->lat.") ) * sin(radians(users.latitude)) ) ) AS distance ";
				$order ="distance ASC";
				$having = "HAVING distance < 100";
				$user_data = "inner join `users` on `product`.`user_id` = `users`.`id`";
			}

			$category_id = "where product.child_category_id = ".$category_id." AND `product`.`is_active` = 0 AND `product_variation`.`sale_price` between ".$low." and ".$high."";
			if(empty($request->searchdata))
			{
				$searchdata = "";		
			}else{
				$ids = implode(",", $request->searchdata);
				$searchdata = "AND product.user_id IN (".$ids.")";
			}


			
			$datas = DB::select("select (SELECT AVG(rating) FROM product_comment where product_id = `product`.`id`) as rating ,`product`.*, `product_variation`.`sale_price`, `product_variation`.`sale_price` ".$selectdata." from `product` inner join `product_variation` on `product`.`id` = `product_variation`.`product_id` ".$user_data." ".$category_id ." ".$searchdata." group by `product_variation`.`product_id` ".$having." order by ".$order." ");

			// print_r($datas);
			// die;

			$curency = Settings::where('slug','=','currency')->first();
			$datascount = count($datas);
			if($datascount != 0)
				{
					foreach($datas as $data)
					{
						$con = array();
						$con['product_id'] = isset($data->id) ? $data->id."" : "";
						$con['category_id'] = isset($data->category_id) ? $data->category_id."" : "";
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
						$wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();
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
									foreach ($mailat['attr'] as $key => $matt) {
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
						$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
						if(count($avg_stars) > 0)
						{
							$con['avg_rating'] = round($avg_stars, 1);
						}
						else{
							$con['avg_rating'] = "";
						}
						$confinal[] = $con;
					}
					$success['status'] = '200';
					$success['message'] = $message;
					
					$success['data'] = $confinal;	
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
        $message = 'Item list data.';
        $error   = 'Sorry item not found.';
      }
      else{
        $message = "Données de liste d'articles.";
        $error   = "Désolé article introuvable.";
      }
			$low = $request->price_low;
			$category_id = $request->category_id;
			$high = $request->price_high;
			$order = "";
			

			if($request->type == 1){
				$order = "`product_variation`.`sale_price`*1 ASC";
			}
			if($request->type == 2){
				$order = "`product_variation`.`sale_price`*1 DESC";	
			}
			if($request->type == 3){
				$order ="`rating` DESC";
			}
			$selectdata = "";
			$having = "";
			$user_data = "";
			if($request->type == 4){
				$selectdata = " , ( 3959 * acos( cos( radians(".$request->lat.") ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians(".$request->long.") ) + sin( radians(".$request->lat.") ) * sin(radians(users.latitude)) ) ) AS distance ";
				$order ="distance ASC";
				$having = "HAVING distance < 100";
				$user_data = "inner join `users` on `product`.`user_id` = `users`.`id`";
			}

			$category_id = "where product.category_id = ".$category_id." AND `product`.`is_active` = 0 AND `product_variation`.`sale_price` between ".$low." and ".$high."";
			if(empty($request->searchdata))
			{
				$searchdata = "";		
			}else{
				$ids = implode(",", $request->searchdata);
				$searchdata = "AND product.user_id IN (".$ids.")";
			}


			
			$datas = DB::select("select (SELECT AVG(rating) FROM product_comment where product_id = `product`.`id`) as rating ,`product`.*, `product_variation`.`sale_price`, `product_variation`.`sale_price` ".$selectdata." from `product` inner join `product_variation` on `product`.`id` = `product_variation`.`product_id` ".$user_data." ".$category_id ." ".$searchdata." group by `product_variation`.`product_id` ".$having." order by ".$order." ");

			

			$curency = Settings::where('slug','=','currency')->first();
			$datascount = count($datas);
			if($datascount != 0)
				{
					foreach($datas as $data)
					{
						$con = array();
						$con['product_id'] = isset($data->id) ? $data->id."" : "";
						$con['category_id'] = isset($data->category_id) ? $data->category_id."" : "";
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
						// $carts = Cart::where('product_id',$data->id)->where('user_id',$user->id)->get();
						// if(count($carts) > 0)
						// {
						// 	foreach ($carts as  $key=>$cart) {
								
						// 		$attributesss = json_decode($cart->variant);
						// 		$attributesss[0]->id = $cart->id;
						// 		$con['cart'][] = $attributesss[0];
						// 	}
						// }
						// else{

						// 	$con['cart'] = "";
						// }
						$con['cart'] = "";
						$con['short_description'] = isset($data->short_description) ? $data->short_description."" : "";
						$con['full_description'] = isset($data->full_description) ? $data->full_description."" : "";
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
									foreach ($mailat['attr'] as $key => $matt) {
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
						$avg_stars = DB::table('product_comment')->where('product_comment.product_id', $data->id)->groupBy('product_comment.product_id')->AVG('rating');
						if(count($avg_stars) > 0)
						{
							$con['avg_rating'] = round($avg_stars, 1);
						}
						else{
							$con['avg_rating'] = "";
						}
						$confinal[] = $con;
					}
					$success['status'] = '200';
					$success['message'] = $message;
					
					$success['data'] = $confinal;	
					return response()->json($success, $this-> successStatus);
				}
				else
				{
					return response()->json(['status' => '401', 'message'=> $error, 'data' => []], 401); 
				}
		}
	}
}
