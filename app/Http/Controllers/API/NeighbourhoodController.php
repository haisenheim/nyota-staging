<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Neighbourhood;
use App\Models\Settings;
use DB;
use Illuminate\Support\Facades\Auth; 

class NeighbourhoodController extends Controller 
{
	public $successStatus = 200;
	
    public function getneighbourhood(Request $request)
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
              $error   = "Désolé article introuvable.";
              $message = 'Succès';
            }
            $datas =  Neighbourhood::all();
            $datascount = $datas->count();
            if($datascount != 0)
            {
                foreach ($datas as $key => $data) {
                   
                    $con = array();
                    $con['id'] = isset($data->id) ? $data->id."" : "";
                    $con['neighbour_hood'] = isset($data->neighbour_hood) ? $data->neighbour_hood."" : "";
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