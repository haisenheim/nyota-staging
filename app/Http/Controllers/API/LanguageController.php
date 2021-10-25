<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class LanguageController extends Controller 
{
  public $successStatus = 200;
  
  public function index(Request $request)
  {   
    $header = $request->header('Authorization');
    if($header != null)
    { 
      $user = Auth()->guard('api')->user($header); 
      $datatwo  = array();
      $datatwo['language']  = $request->languageCode;
      $language = User::where('id',$user->id)->Update($datatwo);
      $message = array();
      $error  = array();
      $token_error = array();
      if($user->language == 0){
        $message = 'Language Updated successfully.';
        $error   = 'Language not updated.';
        $token_error   = 'Invalid token.';
      }
      else{
        $message = 'Langue mise à jour avec succès.';
        $error   = 'Langue non mise à jour.';
        $token_error   = 'Jeton invalide.';
      }
      if($language == 1){
        $success['status'] = '200';
        $success['message'] = $message;
        $success['data'] = []; 
        return response()->json($success,$this-> successStatus);
      }else{
        return response()->json(['status' => '401', 'message'=>$error], 401);
      }
    }
    else
    {
      return response()->json(['status' => '401', 'message'=> $token_error, 'data' => []], 401); 
    }
  }
}
