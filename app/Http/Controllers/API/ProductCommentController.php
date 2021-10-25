<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Auth; 

class ProductCommentController extends Controller 
{
	public $successStatus = 200;
	
	public function addcomment(Request $request)
    {
    	$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
      if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Your comment not add please try again.';
          $message = 'success';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Votre commentaire n'est pas ajouté, veuillez réessayer.";
          $message = 'Succès';
      }
			$insert = array();
			$insert['user_id']  = $user->id;
        	$insert['product_id']   = $request->product_id;
        	$insert['comment']    	= $request->comment;
        	$insert['rating']    	= $request->rating;
        	$comment = Comment::create($insert);
        	$order_id = $request->order_id;
        	$datass = array('is_review' => 1);
        	$order_comment = Order::where('id',$order_id)->update($datass);
        	if(!$comment)
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
			return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
		}		 
	}
	
	
	public function getcomment(Request $request)
    {
    	$header = $request->header('Authorization');
		if($header != null)
		{
			$user = Auth()->guard('api')->user($header);
      if($user->language == 0){
          $token_error   = 'Invalid token.';
          $error   = 'Comment details not found.';
          $message = 'success';
      }
      else{
          $token_error   = 'Jeton invalide.';
          $error   = "Détails du commentaire introuvables.";
          $message = 'Succès';
      }
			$datas = DB::table('product_comment')
            ->join('users', 'users.id','=','product_comment.user_id')
            ->select('product_comment.*','users.first_name as username')
            ->where('product_id',$request->product_id)->get();
            $datascount = $datas->count();
            if($datascount != 0)
			{
				foreach($datas as $data)
				{
					$con = array();
					$con['userName'] = isset($data->username) ? $data->username."" : "";
					$con['userComment'] = isset($data->comment) ? $data->comment."" : "";
					$con['rating'] = isset($data->rating) ? $data->rating."" : "";
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
			return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
		}		
	}
}
