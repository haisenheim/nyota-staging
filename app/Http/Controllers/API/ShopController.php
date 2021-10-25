<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth; 

class ShopController extends Controller 
{
	public $successStatus = 200;
	
	public function index(Request $request)
    {		
		$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
			if($user->language == 0){
        $message = 'Shop data.';
        $error   = 'Shop not found.';
        $token_error   = 'Invalid token.';
      }
      if($user->language == 1){
        $message = 'Données de la boutique.';
        $error   = 'Boutique introuvable.';
        $token_error   = 'Jeton invalide.';
      } 
			$datas = DB::table('users')
		            ->join('role_user', 'role_user.user_id','=','users.id')
		            ->select('users.*')
		            ->where('role_user.role_id','=','3')
		            ->orderBy('users.id', 'DESC')
		            ->get();
		    $maxprice = DB::table('product')
		            ->join('product_variation', 'product_variation.product_id','=','product.id')
		            ->select(DB::raw('MAX(product_variation.sale_price*1) as price'))
		            ->where('product.child_category_id',$request->category_id)
		            ->where('product.is_active',0)
		            ->first();
		            
		    $datascount = $datas->count();
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['id'] = isset($data->id) ? $data->id."" : "";
					$con['name'] = isset($data->first_name) ? $data->first_name."" : "";
					
					$confinal[] = $con;
				}			
				$success['status'] = '200';
				$success['message'] = $message;
				$success['max_price'] = $maxprice->price;
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
        $message = 'Shop data.';
        $error   = 'Shop not found.';
      }
      else{
        $message = 'Données de la boutique.';
        $error   = 'Boutique introuvable.';
      }
			$datas = DB::table('users')
		            ->join('role_user', 'role_user.user_id','=','users.id')
		            ->select('users.*')
		            ->where('role_user.role_id','=','3')
		            ->orderBy('users.id', 'DESC')
		            ->get();
		    $maxprice = DB::table('product')
		            ->join('product_variation', 'product_variation.product_id','=','product.id')
		            ->select(DB::raw('MAX(product_variation.sale_price*1) as price'))
		            ->where('product.category_id',$request->category_id)
		            ->where('product.is_active',0)
		            ->first();
		            
		    $datascount = $datas->count();
			
			if($datascount != 0)
			{
				$confinal = array();
				foreach($datas as $data)
				{
					$con = array();
					$con['id'] = isset($data->id) ? $data->id."" : "";
					$con['name'] = isset($data->first_name) ? $data->first_name."" : "";
					
					$confinal[] = $con;
				}			
				$success['status'] = '200';
				$success['message'] = $message;
				$success['max_price'] = $maxprice->price;
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