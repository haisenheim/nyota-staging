<?php



namespace App\Http\Controllers;



use App\Models\State;

use App\Models\Country;

use App\Models\District;

use App\Models\City;

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



class CityController extends Controller

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

        $citys = DB::table('city')

            //->join('district', 'district.id','=','city.district_id')

           // ->join('state', 'state.id','=','district.state_id')

           // ->join('countries', 'countries.id','=','state.country_id')

           // ->select('city.*','countries.country_name as country','state.name as state_name','district.name as district_name')
             
              ->select('city.*')


              ->paginate(100);



        

        return View('pages.admin.city.show', compact('citys'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
           
         $city = City::all();

         return view('pages.admin.city.create', compact('city'));



       // $countries = Country::all();

       // return view('pages.admin.city.create', compact('countries'));

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

            

            'name'                  => 'required|regex:/^[\pL\s\-]+$/u|unique:city',

           // 'country'               => 'required',

           // 'state'                 => 'required',

           // 'district'              => 'required',

            

        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

        $city = City::create([

           // 'district_id'    => $request->input('district'),

            'name'           => $request->input('name'),

        ]);

       return redirect('admin/city')->with('success', 'Record insert successfully');

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

         $countries = City::all();


        //$countries = Country::all();

        $city = DB::table('city')

            //->join('district', 'district.id','=','city.district_id')

            //->join('state', 'state.id','=','district.state_id')

            //->join('countries', 'countries.id','=','state.country_id')

            //->select('city.*','countries.country_name as country','countries.id as countryid','state.name as state_name','district.name as district_name','state.id as stateid')
               
              ->select('city.*')


            ->where('city.id','=',$id)

            ->first();

       // $states = DB::table('state')->where('country_id','=',$city->countryid)->get();

       // $districts = DB::table('district')->where('state_id','=',$city->stateid)->get();

      //  return view('pages.admin.city.edit',compact('city','countries','states','districts'));

return view('pages.admin.city.edit',compact('city'));

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

                'name'                 => 'required|regex:/^[\pL\s\-]+$/u|unique:city',


              //  'country'              => 'required',

             //   'state'                => 'required',
              
             //   'district'             => 'required',

                

           ]

        );

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

        $datatwo = array(

            'name'              => $request->input('name'),

           // 'district_id'       => $request->input('district'),

        );

        $customer= City::where('id',$id)->update($datatwo);

        

        return redirect('admin/city');

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

          $state    = DB::table('city')->where('id',$id)->delete();

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

        $citys = DB::table('city')

           // ->join('district', 'district.id','=','city.district_id')

           // ->join('state', 'state.id','=','district.state_id')

           // ->join('countries', 'countries.id','=','state.country_id')

           // ->select('city.*','countries.country_name as country','state.name as state_name','district.name as district_name')

             ->select('city.*')


            ->where(function ($query) use ($searchTerm) {

        $query->where('city.id', 'like','%'. $searchTerm.'%')

          //->orWhere('state.name', 'like','%'. $searchTerm.'%')

         // ->orWhere('countries.country_name', 'like', '%'. $searchTerm.'%')

          ->orWhere('city.name', 'like', '%'. $searchTerm.'%');

         // ->orWhere('district.name', 'like', '%'. $searchTerm.'%')

        })

        ->paginate(100);

        return view('pages.admin.city.show',compact('citys'));

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

