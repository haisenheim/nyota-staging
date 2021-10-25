<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Notification;
use DB;
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;

class NotificationController extends Controller 
{
	public $successStatus = 200;
	
    public function index(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
			$confinal = array();
            $user = Auth()->guard('api')->user($header);
            $datas =  Notification::where('user_id',$user->id)->orderBy('id', 'DESC')->get();
            $datascount =  Notification::where('user_id',$user->id)->count();
            if($user->language == 0){
                $token_error   = 'Invalid token.';
                $error   = 'Sorry item not found.';
                $message = 'success';
                if($datascount != 0)
                {
                    foreach ($datas as $key => $data) {
                        $con = array();
                        $con['title'] = !empty($data->title_en) ? $data->title_en : "";
                        $con['date'] = Carbon::parse($data->created_at)->format('Y-m-d');
                        $con['message'] = !empty($data->message_en) ? $data->message_en : "";
                        $confinal[] = $con;
                    }
                }
                else
                {
                    return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
                }
            }
            else{
                $token_error   = 'Jeton invalide.';
                $error   = "Désolé article introuvable.";
                $message = 'Succès';
                if($datascount != 0)
                {
                    foreach ($datas as $key => $data) {
                        $con = array();
                        $con['title'] = !empty($data->title_fr) ? $data->title_fr : "";
                        $con['date'] = Carbon::parse($data->created_at)->format('Y-m-d');
                        $con['message'] = !empty($data->message_fr) ? $data->message_fr : "";
                        $confinal[] = $con;
                    }
                }
                else
                {
                    return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
                }
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