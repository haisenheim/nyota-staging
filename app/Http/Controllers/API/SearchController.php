<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Category;
use App\Models\Settings;
use App\Models\Cart;
use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use DB; 

class SearchController extends Controller 
{
	public $successStatus = 200;
	
	public function index(Request $request)
  {
		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Search data not found.';
          $message = 'search data.';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Données de recherche introuvables.";
          $message = 'rechercher des données.';
	  }
	  
	     // $latitude = $request->latitude;
         // $longitude = $request->longitude;
        //  $city = $request->city;

			$searchTerm = $request->input('search_box');
			// $datas = DB::table('product')
   //          ->join('category', 'category.id','=','product.category_id')
   //          ->select('product.*','category.name as category','category.id as category_id')
   //          ->where('product.name', 'like','%'. $searchTerm.'%')
   //          ->groupBy('category.id')
   //          ->groupBy('product.name')
   //          ->get();
			$datas = DB::table('product as p')
            ->join('category as s', 's.id','=','p.child_category_id')
			->join('category as c', 'c.id','=','p.category_id')
			->join('users','users.id','=','p.user_id')
			->join('city','city.id','=','users.city')
            ->select('p.*','s.name as sub_category','s.id as sub_category_id','c.name as category','c.id as category_id')
			->where('p.name', 'like','%'. $searchTerm.'%')
			->where('city.name',$request->city)
            ->groupBy('s.id')
            ->groupBy('p.name')
            ->get();
            
			$datascount = $datas->count(); 
			$curency = Settings::where('slug','=','currency')->first();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] = isset($data->id) ? $data->id."" : "";
					$con['category_id'] = isset($data->category_id) ? $data->category_id."" : "";
					$con['sub_category_id'] = isset($data->sub_category_id) ? $data->sub_category_id."" : "";
					$con['product_name'] = isset($data->name) ? $data->name."" : "";
					$con['category_name'] = isset($data->category) ? $data->category."" : "";
					$con['sub_category_name'] = isset($data->sub_category) ? $data->sub_category."" : "";
					
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
          $error   = 'Search data not found.';
          $message = 'search data.';
      }
      else{
         	$error   = "Données de recherche introuvables.";
          $message = 'rechercher des données.';
	  }
	  
	  // $latitude = $request->latitude;
         // $longitude = $request->longitude;
		//  $city = $request->city;
		
			$searchTerm = $request->input('search_box');
			// $datas = DB::table('product')
   //          ->join('category', 'category.id','=','product.category_id')
   //          ->select('product.*','category.name as category','category.id as category_id')
   //          ->where('product.name', 'like','%'. $searchTerm.'%')
   //          ->groupBy('category.id')
   //          ->groupBy('product.name')
   //          ->get();

		$datas = DB::table('product as p')
            ->leftjoin('category as s', 's.id','=','p.child_category_id')
			->leftjoin('category as c', 'c.id','=','p.category_id')
			->join('users','users.id','=','p.user_id')
			->join('city','city.id','=','users.city')
            ->select('p.*','s.name as sub_category','s.id as sub_category_id','c.name as category','c.id as category_id')
			->where('p.name', 'like','%'. $searchTerm.'%')
			->where('city.name',$request->city)
            ->groupBy('s.id')
            ->groupBy('p.name')
            ->get();
            
			$datascount = $datas->count(); 
			$curency = Settings::where('slug','=','currency')->first();
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['product_id'] = isset($data->id) ? $data->id."" : "";
					$con['category_id'] = isset($data->category_id) ? $data->category_id."" : "";
					$con['sub_category_id'] = isset($data->sub_category_id) ? $data->sub_category_id."" : "";
					$con['product_name'] = isset($data->name) ? $data->name."" : "";
					$con['category_name'] = isset($data->category) ? $data->category."" : "";
					$con['sub_category_name'] = isset($data->sub_category) ? $data->sub_category."" : "";
					
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
	public function categorysearchpage(Request $request){

		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Search data not found.';
          $message = 'search data.';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Données de recherche introuvables.";
          $message = 'rechercher des données.';
	  }
	  
	      $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;

			$searchTerm = $request->input('search_box');
			$category_id = $request->input('category_id');
			$datas = DB::table('product')
						->leftjoin('category as s', 's.id','=','product.child_category_id')
			->leftjoin('category as c', 'c.id','=','product.category_id')
			->join('users','users.id','=','product.user_id')
			->join('city','city.id','=','users.city')
            ->select('product.*','s.name as sub_category','s.id as sub_category_id','c.name as category','c.id as category_id')
            ->where('product.name', 'like','%'. $searchTerm.'%')
			->where('s.id','=',$category_id)
			->where('city.name',$request->city)
            ->groupBy('product.name')
            ->get();
            
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
					$con['category_name'] = isset($data->category) ? $data->category."" : "";
					$con['sub_category_name'] = isset($data->sub_category) ? $data->sub_category."" : "";
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
					$wishlist = DB::table('wishlist')->join('users','users.id','=','wishlist.user_id')->select('wishlist.*')->where('wishlist.product_id',$request->product_id)->groupBy('wishlist.product_id')->first();
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
          $error   = 'Search data not found.';
          $message = 'search data.';
      }
      else{
         	$error   = "Données de recherche introuvables.";
          $message = 'rechercher des données.';
	  }
	  
	      $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;

			$searchTerm = $request->input('search_box');
			$category_id = $request->input('category_id');
			$datas = DB::table('product')
            ->join('category as s', 's.id','=','product.child_category_id')
			->leftjoin('category as c', 'c.id','=','product.category_id')
			->join('users','users.id','=','product.user_id')
			->join('city','city.id','=','users.city')
            ->select('product.*','s.name as sub_category','s.id as sub_category_id','c.name as category','c.id as category_id')
            ->where('product.name', 'like','%'. $searchTerm.'%')
			->where('s.id','=',$category_id)
			->where('city.name',$request->city)
            ->groupBy('product.name')
            ->get();
            
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
					$con['category_name'] = isset($data->category) ? $data->category."" : "";
					$con['sub_category_name'] = isset($data->sub_category) ? $data->sub_category."" : "";
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
