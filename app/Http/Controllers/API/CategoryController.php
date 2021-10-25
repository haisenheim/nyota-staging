<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Category;
use App\Models\Settings;
use App\Models\Product;
use App\Models\Cart;
use App\Models\City;
use App\Models\User;


use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use DB;
use Illuminate\Support\Facades\Auth; 

class CategoryController extends Controller 
{
	public $successStatus = 200;
	
	public function index(Request $request)
    {		
		$header = $request->header('Authorization');
		if($header != null)
		{	
			$user = Auth()->guard('api')->user($header); 
			if($user->language == 0){
        $message = 'Category data.';
        $error   = 'Category not found.';
        $token_error   = 'Invalid token.';
      }
      if($user->language == 1){
        $message = 'Données de catégorie.';
        $error   = 'Catégorie introuvable.';
        $token_error   = 'Jeton invalide.';
      }
			$query = array('status' => 0,'parent_id' => 0);
			$datas = Category::where($query)->get();
			$datascount = Category::where($query)->count(); 
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['category_id'] = isset($data->id) ? $data->id."" : "";
					$con['category_name'] = isset($data->name) ? $data->name."" : "";
					if(!empty($data->image))
					{
						$con['category_image'] = url('/public/category_images').'/'.$data->image;
					}
					else{
						$con['category_image'] = "";
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
			// $language = request('languageCode');
			// if($language == 0){
   //    	$message = 'Category data.';
   //      $error   = 'Category not found.';
   //    }
   //    else{
   //      $message = 'Données de catégorie.';
   //      $error   = 'Catégorie introuvable.';
   //    }
			$query = array('status' => 0,'parent_id' => 0);
			$datas = Category::where($query)->get();
			$datascount = Category::where($query)->count(); 
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['category_id'] = isset($data->id) ? $data->id."" : "";
					$con['category_name'] = isset($data->name) ? $data->name."" : "";
					if(!empty($data->image))
					{
						$con['category_image'] = url('/public/category_images').'/'.$data->image;
					}
					else{
						$con['category_image'] = "";
					}
					$confinal[] = $con;
				}			
				$success['status'] = '200';
				$success['message'] = 'Category data.';
				$success['data'] = $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message'=>'Category not found.', 'data' => []], 401); 
			} 
		}
	}

	public function subcategory(Request $request)
  {		
		$header = $request->header('Authorization');
		if($header != null)
		{	
			$user = Auth()->guard('api')->user($header); 
			if($user->language == 0){
        $message = 'Category data.';
        $error   = 'Category not found.';
        $token_error   = 'Invalid token.';
      }
      if($user->language == 1){
        $message = 'Données de catégorie.';
        $error   = 'Catégorie introuvable.';
        $token_error   = 'Jeton invalide.';
      }
			$query = array('status' => 0,'parent_id' => $request->category_id);
			$datas = Category::where($query)->get();
			$datascount = Category::where($query)->count(); 
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['sub_category_id'] = isset($data->id) ? $data->id."" : "";
					$con['sub_category_name'] = isset($data->name) ? $data->name."" : "";
					if(!empty($data->image))
					{
						$con['sub_category_image'] = url('/public/category_images').'/'.$data->image;
					}
					else{
						$con['sub_category_image'] = "";
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
      	$message = 'Category data.';
        $error   = 'Category not found.';
      }
      else{
        $message = 'Données de catégorie.';
        $error   = 'Catégorie introuvable.';
      }
			$query = array('status' => 0,'parent_id' => $request->category_id);
			$datas = Category::where($query)->get();
			$datascount = Category::where($query)->count(); 
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['sub_category_id'] = isset($data->id) ? $data->id."" : "";
					$con['sub_category_name'] = isset($data->name) ? $data->name."" : "";
					if(!empty($data->image))
					{
						$con['sub_category_image'] = url('/public/category_images').'/'.$data->image;
					}
					else{
						$con['sub_category_image'] = "";
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
	}

	public function detail(Request $request){

		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			if($user->language == 0){
        $message = 'Category Details.';
        $error   = 'Category details not found.';
        $token_error   = 'Invalid token.';
      }
      if($user->language == 1){
        $message = 'détails de la catégorie.';
        $error   = 'Détails de la catégorie introuvables.';
        $token_error   = 'Jeton invalide.';
      }


          $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;


			$currentPage = $request->page_no;
			$request['page'] = $currentPage; // show current page wise record.
			$categoryDetail 	= $request->category_id;
			$query = array('is_active' => 0,'child_category_id' => $request->category_id);
			//$datas = Product::where($query)->paginate(25);

$datas 	= DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where($query)->paginate(25);

//print_r($datas);die();

			$counts = Product::where($query)->count();
			$datascount = $datas->count();
			$curency = Settings::where('slug','=','currency')->first();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] = isset($data->id) ? $data->id."" : "";
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
				//$wishlist = DB::table('wishlist')->select('wishlist.*')->where('wishlist.product_id',$data->id)->where('wishlist.user_id',$user->id)->groupBy('wishlist.product_id')->first();
					
$wishlist = DB::table('wishlist')->join('users','users.id','=','wishlist.user_id')->select('wishlist.*')->where('wishlist.product_id',$data->id)->groupBy('wishlist.product_id')->first();

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
				       //$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
					

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
				$success['status'] = '200';
				$success['message'] = $message;
				$success['currentPage'] = "".$currentPage;
				$success['totalPage'] = "".$datas->lastPage();
				$success['totalitems'] = "".$counts;
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
          $message = 'Category Details.';
        	$error   = 'Category details not found.';
        }
      else{
        $message = 'détails de la catégorie.';
        $error   = 'Détails de la catégorie introuvables.';
     	}
			$currentPage = $request->page_no;
			$request['page'] = $currentPage; // show current page wise record.
			$categoryDetail 	= $request->category_id;
			$query = array('is_active' => 0,'child_category_id'=>$request->category_id);
			//$datas = Product::where($query)->paginate(25);


$datas 	= DB::table('product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('product.*')->where('city.name',$request->city)->where($query)->paginate(25);
	
			$counts = Product::where($query)->count();
			$datascount = $datas->count();
			$curency = Settings::where('slug','=','currency')->first();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] = isset($data->id) ? $data->id."" : "";
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
					//$comment = DB::table('product_comment')->join('users','users.id','=','product_comment.user_id')->select('product_comment.*','users.first_name as username')->where('product_comment.product_id',$data->id)->get();
					
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
				$success['status'] = '200';
				$success['message'] = $message;
				$success['currentPage'] = "".$currentPage;
				$success['totalPage'] = "".$datas->lastPage();
				$success['totalitems'] = "".$counts;
				$success['data'] = $confinal;	
				return response()->json($success, $this-> successStatus);
			}
			else
			{
				return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
			}
		}
	}
}
