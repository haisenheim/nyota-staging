<?php



namespace App\Http\Controllers;

use App\Traits\CaptureIpTrait;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Illuminate\Routing\UrlGenerator;

use jeremykenedy\LaravelRoles\Models\Role;

use Validator;

use Image;

use DB;

use Auth;

use File;

use Carbon\Carbon;

use App\Models\Settings;





class SettingsController extends Controller

{ 

    public function __construct()

    { 

        $this->middleware('auth');

    }

	 

	public function index()

    {

    	        $taxquery = array('slug'=>'tax');

		$tax = Settings::where($taxquery)->first();



		$maxquery = array('slug'=>'max_price');

		$max_price = Settings::where($maxquery)->first();



		$shippingquery = array('slug'=>'shipping_price');

		$shipping_price = Settings::where($shippingquery)->first();


                $expressdelieveryquery = array('slug'=>'express_delievery');

		$express_delievery = Settings::where($expressdelieveryquery)->first();


                $expressenquery = array('slug'=>'express_en');

		$express_en = Settings::where($expressenquery)->first();


                $expressfrquery = array('slug'=>'express_fr');

		$express_fr = Settings::where($expressfrquery)->first();


                $standarddelieveryquery = array('slug'=>'Standard_delievery');

		$standard_delievery = Settings::where($standarddelieveryquery)->first();


                $standardenquery = array('slug'=>'Standard_en');

		$standard_en = Settings::where($standardenquery)->first();


                $standardfrquery = array('slug'=>'Standard_fr');

		$standard_fr = Settings::where($standardfrquery)->first();






               return View('pages.admin.settings.index',compact('tax','max_price','shipping_price','express_delievery','express_en','express_fr','standard_delievery','standard_en','standard_fr'));

    }

	

	

	public function update(Request $request)

    {

    	$validator = Validator::make($request->all(), 

        [

            

            'tax'				=> 'numeric',

            'max_price'			=> 'numeric',

            'shipping_price'	=> 'numeric',

            'express_delievery'	=> 'numeric',
            'standard_delievery'=> 'numeric',
            // 'Express_en'	=> 'alpha',
            // 'express_fr'	=> 'alpha',
            // 'standard_en'	=> 'alpha',
           //  'standard_fr'	=> 'alpha',






            

            

        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

		

		$tax = array('contain' => $request->input('tax'));

		$querytax = array('slug'=>'tax');

		$querytaxs = Settings::where($querytax)->update($tax);



		$max_price = array('contain' => $request->input('max_price'));

		$querymaxprice = array('slug'=>'max_price');

		$querymaxprices = Settings::where($querymaxprice)->update($max_price);



		$shipping_price = array('contain' => $request->input('shipping_price'));

		$queryshippingprice = array('slug'=>'shipping_price');

		$queryshippingprices = Settings::where($queryshippingprice)->update($shipping_price);


                $express_delievery = array('contain' => $request->input('express_delievery'));

		$queryexpressdelievery = array('slug'=>'express_delievery');

		$queryexpressdelieverys = Settings::where($queryexpressdelievery)->update($express_delievery);


                $express_en = array('contain' => $request->input('express_en'));

		$queryexpressen = array('slug'=>'express_en');

		$queryexpressens = Settings::where($queryexpressen)->update($express_en);


                $express_fr = array('contain' => $request->input('express_fr'));

		$queryexpressfr = array('slug'=>'express_fr');

		$queryexpressfrs = Settings::where($queryexpressfr)->update($express_fr);

                
                $standard_delievery = array('contain' => $request->input('standard_delievery'));

		$querystandarddelievery = array('slug'=>'standard_delievery');

		$querystandarddelieverys = Settings::where($querystandarddelievery)->update($standard_delievery);


                $standard_en = array('contain' => $request->input('standard_en'));

		$querystandarden = array('slug'=>'standard_en');

		$querystandardens = Settings::where($querystandarden)->update($standard_en);


                $standard_fr = array('contain' => $request->input('standard_fr'));

		$querystandardfr = array('slug'=>'standard_fr');

		$querystandardfrs = Settings::where($querystandardfr)->update($standard_fr);




		return redirect()->back();

    }

}