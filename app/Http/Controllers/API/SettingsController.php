<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Mobilesettings;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller 
{
	public $successStatus = 200;
    public function getsettings(Request $request)
    { 
		$header = $request->header('Authorization');
		$user = Auth()->guard('api')->user($header);
		if($user->language == 0){
        $message = 'Setting get succefully.';
     }
    if($user->language == 1){
      $message = 'Réglage réussi.';
    }
		$con = array();
		$Mobilesettings = Mobilesettings::where('user_id',$user->id)->first();
		$con['notification'] = $Mobilesettings->notification;
		$con['gps'] = $Mobilesettings->gps;
		$con['sms'] = $Mobilesettings->sms;
		$con['email'] = $Mobilesettings->email;
		
			$success['status']  = '200';
        $success['message'] = $message;
        $success['data']  = $con;
        return response()->json($success, $this-> successStatus);	
	}
	
	public function updatesettings(Request $request){
		
		$header = $request->header('Authorization');
		$user = Auth()->guard('api')->user($header);
		if($user->language == 0){
        $message = 'Setting Update succefully.';
     }
    if($user->language == 1){
      $message = 'Paramètres mis à jour avec succès.';
    }
		$con = array();
		
		$updates = array();
		$updates['notification'] = $request->notification;
		$updates['gps'] = $request->gps;
		$updates['sms'] = $request->sms;
		$updates['email'] = $request->email;
		
		$Mobilesettings = Mobilesettings::where('user_id',$user->id)->update($updates);
		
		$success['status']  = '200';
    $success['message'] = $message;
    $success['data']  = $con;
    return response()->json($success, $this-> successStatus);	
	}
	
}