<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\Page;
use Illuminate\Support\Facades\Auth; 

class PagesController extends Controller 
{
	public $successStatus = 200;
	
	public function aboutus(Request $request)
    {
    $language = request('languageCode');
    if($language == 0){
        $error   = 'Page not found';
        $message = 'About us data.';
    }
    else{
        $error   = 'Pas trouvé à pied';
        $message = 'À propos de nous Données.';
    }
		$query = array('slug' => 'about-us');
    $pages = Page::where($query)->get();
		$confinal = array();
		$pagescount = Page::where($query)->count();
		if($pagescount != 0)
		{
			
			foreach($pages as $pp)
			{
				$con = array();
				$con['description'] = $pp->contain;
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
	
	
	public function privacypolicy(Request $request)
    {
    $language = request('languageCode');
    if($language == 0){
        $error   = 'Page not found';
        $message = 'Privacy policy data.';
    }
    else{
        $error   = 'Pas trouvé à pied';
        $message = 'Données de politique de confidentialité.';
    }
		$query = array('slug' => 'privacy-policy');
    $pages = Page::where($query)->get();
		$confinal = array();
		$pagescount = Page::where($query)->count();
		if($pagescount != 0)
		{ 
			
			foreach($pages as $pp)
			{
				$con = array();
				$con['description'] = $pp->contain;
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
