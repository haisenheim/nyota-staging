<?php

namespace App\Http\Controllers\API;



use Illuminate\Http\Request; 

use App\Http\Controllers\Controller; 

use App\Models\Banner;

use App\Models\City;

use App\Models\Product;

use App\Models\User;

use DB;

use Illuminate\Support\Facades\Auth; 



class OfferController extends Controller 

{

	public $successStatus = 200;

	

	public function index(Request $request)

    {		

		$header = $request->header('Authorization');

		if($header != null)

		{

			$user = Auth()->guard('api')->user($header);

			if($user->language == 0){

        $message = 'Offer data.';

        $error   = 'Offer not found.';

        $token_error   = 'Invalid token.';

      }

      if($user->language == 1){

        $message = 'Offrir des données.';

        $error   = 'Offre non trouvée.';

        $token_error   = 'Jeton invalide.';

      }


          $latitude = $request->latitude;
          $longitude = $request->longitude;
          $city = $request->city;


		      //$datas = Banner::all();

                        $datas  = DB::table('banner')->join('product','product.id','=','banner.product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('banner.*')->where('city.name',$request->city)->get();
//print_r($datas);die();

		      //$datascount = $datas->count(); 
                       
                        $datascount = count($datas);

			

			if($datascount != 0)

			{

				$confinal = array();

				foreach($datas as $data)

				{

					$con = array();

					

					if(!empty($data->image))

					{

						$con['offer_image'] = url('/public/banner').'/'.$data->image;

					}

					else{

						$con['offer_image'] = "";

					}

					$con['product_id'] = isset($data->product) ? $data->product."" : "";

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

   //    	$message = 'Offer data.';

   //      $error   = 'Offer not found.';

   //   }

   //    else{

   //      $message = 'Offrir des données.';

   //      $error   = 'Offre non trouvée.';

   //    }

		       //$datas = Banner::all();

                         $datas  = DB::table('banner')->join('product','product.id','=','banner.product')->join('users','users.id','=','product.user_id')->join('city','city.id','=','users.city')->select('banner.*')->where('city.name',$request->city)->first();


		       //$datascount = $datas->count(); 
 
                         $datascount = count($datas);


			

			if($datascount != 0)

			{

				$confinal = array();

				foreach($datas as $data)

				{

					$con = array();

					

					if(!empty($data->image))

					{

						$con['offer_image'] = url('/public/banner').'/'.$data->image;

					}

					else{

						$con['offer_image'] = "";

					}

					$con['product_id'] = isset($data->product) ? $data->product."" : "";

					$confinal[] = $con;

				}			

				$success['status'] = '200';

				$success['message'] = 'Offer data.';

				$success['data'] = $confinal;	

				return response()->json($success, $this-> successStatus);

			}

			else

			{

				return response()->json(['status' => '401', 'message'=>'Offer not found.', 'data' => []], 401); 

			} 

		}

		//return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401);

	}

	

}

