<?php



namespace App\Http\Controllers;



use App\Models\State;

use App\Models\Country;

use App\Models\District;

use App\Models\City;

use App\Models\Shipping;


use Auth;

use File;

use DB;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use App\Models\Customer;

use Carbon\Carbon;

use Illuminate\Routing\UrlGenerator;

use jeremykenedy\LaravelRoles\Models\Role;

use Validator;

use Image;



class ShippingController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth');

    }



    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $shipping = DB::table('shipping')

            ->join('city', 'city.id','=','shipping.city_id')             
            ->select('shipping.*','city.name as city_name')
            ->paginate(100);
        return View('pages.admin.shipping.show', compact('shipping'));

    }
    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
           
         $shipping = City::all();

         return view('pages.admin.shipping.create', compact('shipping'));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     *

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), 

        [
            'shipping_price' => 'required|numeric',
            'city' => 'required',

        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

        $shipping = Shipping::create([

            'city_id'    => $request->input('city'),

            'shipping_price'           => $request->input('shipping_price'),

        ]);

       return redirect('admin/shipping')->with('success', 'Record insert successfully');

    }



    /**

     * Display the specified resource.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {       

    }
    /**

     * Show the form for editing the specified resource.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {
        $shipping = Shipping::where('id',$id)->first();
		
        $city = City::all();		
	    return view('pages.admin.shipping.edit',compact('shipping','city'));
    }



    /**

     * Update the specified resource in storage.

     *

     * @param \Illuminate\Http\Request $request

     * @param int                      $id

     *

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {
        $validator = Validator::make($request->all(), 

            [
              'city'    => 'required',
              'shipping_price'  => 'required| numeric',
            ]);	

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        $datatwo = array(

            'city_id'    => $request->input('city'),
            'shipping_price'   => $request->input('shipping_price'),
        );	
	   
        $customer= Shipping::where('id',$id)->update($datatwo);        
        return redirect('admin/shipping');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
       $shipping  = DB::table('shipping')->where('id',$id)->delete();
        return redirect()->back();

    }



    /**

     * Method to search the users.

     *

     * @param Request $request

     *

     * @return \Illuminate\Http\Response

     */

    public function search(Request $request)

    {
        $data = $request->all();
       
        $searchTerm = $request->input('search_box');

        $shipping = DB::table('shipping')

            ->join('city', 'city.id','=','shipping.city_id')
            ->select('shipping.*','city.name as city_name')
            ->where(function ($query) use ($searchTerm) {

        $query->where('shipping.id', 'like','%'. $searchTerm.'%')
              ->orWhere('shipping.shipping_price', 'like', '%'. $searchTerm.'%')
              ->orWhere('city.name', 'like', '%'. $searchTerm.'%');
        })

        ->paginate(100);
       
        return view('pages.admin.shipping.show',compact('shipping'));

    }



    public function getdistrictList($id)

    {

        $districts = DB::table("district")

        ->where("state_id",$id)

        ->select("district.*")->get();

        $html = '<option value="">Select District</option>';

        foreach ($districts as $key => $district) {

            $html .= '<option value="'.$district->id.'">'.$district->name.'</option>';

        }

        echo $html;

    }

    

}

