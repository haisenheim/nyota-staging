<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\City;
use App\Models\Location;
use App\Models\Address;
use DB;
use Illuminate\Support\Facades\Auth; 

class AddressController extends Controller 
{
	public $successStatus = 200;
	public function getcity(Request $request)
    {
        $language = $request->languageCode;
        if($language == 0){
            $error   = 'Sorry city not found.';
            $message = 'success';
        }
        else{
            $error   = "Désolé, ville introuvable.";
            $message = 'Succès';
        }
  		    $datas = City::all();
            $datascount = $datas->count();
            if($datascount != 0)
            {
                foreach ($datas as $key => $data) {
                    $con = array();
                    $con['id'] = isset($data->id) ? $data->id."" : "";
                    $con['name'] = isset($data->name) ? $data->name."" : "";
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
    public function getaddress(Request $request)
    {
        $datas = Location::where('city_id','=',$request->city_id)->get();
        $datascount = $datas->count();
        if($datascount != 0)
        {
            $confinal = array();
            foreach ($datas as $key => $data) {
                $con = array();
                $con['id'] = isset($data->id) ? $data->id."" : "";
                $con['address'] = isset($data->address) ? $data->address."" : "";
                $confinal[] = $con;
            }
        }
        else
        {

            return response()->json(['status' => '401', 'message'=>'Sorry address not found.', 'data' => []], 401); 
        }
        $success['status']  = '200';
        $success['message'] = 'success';
        $success['data']  = $confinal;
        return response()->json($success, $this-> successStatus);
    }

    public function getuseraddress(Request $request){
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
            $datas = DB::table('address')
                    ->join('neighbourhood', 'neighbourhood.id','=','address.neighbourhood_id')
                    ->select('address.*','neighbourhood.neighbour_hood')
                    ->where('address.user_id',$user->id)
                    ->get();
            $datascount = $datas->count();
            if($datascount != 0)
            {
                foreach ($datas as $key => $data) {
                    $con = array();
                    $con['id'] = isset($data->id) ? $data->id."" : "";
                    $con['apartment_name'] = isset($data->apartment_name) ? $data->apartment_name."" : "";
                    $con['neighbourhood'] = isset($data->neighbour_hood) ? $data->neighbour_hood."" : "";
                    $con['street'] = isset($data->street) ? $data->street."" : "";
                    $con['city'] = isset($data->city) ? $data->city."" : "";
                    $con['state'] = isset($data->state) ? $data->state."" : "";
                    $con['pincode'] = isset($data->pincode) ? $data->pincode."" : "";
                    $con['phone'] = isset($data->phone) ? $data->phone."" : "";
                    $confinal[] = $con;
                }
            }
            else
            {

                return response()->json(['status' => '401', 'message'=>$error, 'data' => []], 401); 
            }
            $success['status']  = '200';
            $success['message'] = $message;
            $success['count_address'] = $datascount;
            $success['data']  = $confinal;
            return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
    }

    public function addaddress(Request $request){
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Your address not add please try again.';
              $message = 'Address added successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Votre adresse n'a pas été ajoutée, veuillez réessayer.";
              $message = 'Adresse ajoutée avec succès.';
            }
            $insert = array();
            $insert['user_id']  = $user->id;
            $insert['apartment_name']   = $request->apartment_name;
            $insert['neighbourhood_id'] = $request->neighbourhood_id;
            $insert['street']           = $request->street;
            $insert['city']             = $request->city;
            $insert['state']            = $request->state;
            $insert['pincode']          = $request->pincode;
            //$insert['address']  = $request->address;
            $insert['phone']            = $request->phone;
            $address_detail             = Address::create($insert);
            if(!$address_detail)
            {
                return response()->json(['status' => '401', 'message'=>$error], 401);
            }
            $con = array();
            $con[]['address_id'] = $address_detail->id;
            $success['status']  = '200';
            $success['message'] = $message;
            $success['data']  = $con;
            return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }   
    }

    public function deleteaddress(Request $request){
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Your address not delete please try again.';
              $message = 'Address delete successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Votre adresse ne doit pas être supprimée, veuillez réessayer.";
              $message = 'Adresse supprimée avec succès.';
            }
            $address = Address::where('id',$request->address_id)->delete();
            if(!$address)
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
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }

    public function editaddress(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Your address not edit please try again.';
              $message = 'Address Updated successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Votre adresse ne doit pas être modifiée, veuillez réessayer.";
              $message = 'Adresse mise à jour avec succès.';
            }
            $datatwo = array(
                'user_id'               => $user->id,
                'apartment_name'        => $request->apartment_name,
                'neighbourhood_id'      => $request->neighbourhood_id,
                'street'                => $request->street,
                'city'                  => $request->city,
                'state'                 => $request->state,
                'pincode'               => $request->pincode,
                'phone'                 => $request->phone,
            );
            $address= Address::where('id',$request->address_id)->update($datatwo);
            if(!$address)
            {
                return response()->json(['status' => '401', 'message'=>$error], 401);
            }
            $con = array();
            $con[]['address_id']    = $request->address_id;
            $success['status']      = '200';
            $success['message']     = $message;
            $success['data']        = $con;
            return response()->json($success, $this-> successStatus);

        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }
}
