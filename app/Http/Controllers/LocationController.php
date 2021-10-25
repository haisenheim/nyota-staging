<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use App\Models\Location;
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

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $location = DB::table('delivery_address')
            ->join('city', 'city.id','=','delivery_address.city_id')
            ->join('district', 'district.id','=','city.district_id')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('delivery_address.*','countries.country_name as country','state.name as state_name','district.name as district_name','city.name as city_name')
            ->paginate(100);
        return View('pages.admin.location.show', compact('location'));
    }
    public function create()
    {
        $countries = Country::all();
        return view('pages.admin.location.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), 
        [
            'address'              => 'required',
            'country'              => 'required',
            'state'                => 'required',
            'district'             => 'required',
            'city'                 => 'required',
            'pincode'              => 'required|numeric|digits:6', 
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $location = Location::create([
            'user_id'           => $user->id,
            'city_id'           => $request->input('city'),
            'address'           => $request->input('address'),
            'pincode'           => $request->input('pincode'),
        ]);
        return redirect('admin/location')->with('success', 'Record insert successfully');
    }

    public function show($id)
    {
        
    }

    public function edit($id)
    {
        $countries = Country::all();
        $location = DB::table('delivery_address')
            ->join('city', 'city.id','=','delivery_address.city_id')
            ->join('district', 'district.id','=','city.district_id')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('delivery_address.*','countries.country_name as country','countries.id as countryid','state.name as state_name','district.name as district_name','state.id as stateid','city.name as city_name','district.name as district_name','district.id as districtid')
            ->where('delivery_address.id','=',$id)
            ->first();
        $states = DB::table('state')->where('country_id','=',$location->countryid)->get();
        $districts = DB::table('district')->where('state_id','=',$location->stateid)->get();
        $citys = DB::table('city')->where('district_id','=',$location->districtid)->get();
        return view('pages.admin.location.edit',compact('location','countries','states','districts','citys'));
    }

    public function update(Request $request, $id)
    {
    
        $validator = Validator::make($request->all(), 
            [
                'address'              => 'required',
                'country'              => 'required',
                'state'                => 'required',
                'district'             => 'required',
                'city'                 => 'required',
                'pincode'              => 'required|numeric|digits:6', 
           ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $datatwo = array(
            'city_id'           => $request->input('city'),
            'address'           => $request->input('address'),
            'pincode'           => $request->input('pincode'),
        );
        $location= Location::where('id',$id)->update($datatwo);
        
        return redirect('admin/location');
    }

    public function destroy($id)
    {
          $state       = DB::table('delivery_address')->where('id',$id)->delete();
          return redirect()->back();
    }

    public function search(Request $request)
    {
        $data = $request->all();
        $searchTerm = $request->input('search_box');
        $location = DB::table('delivery_address')
            ->join('city', 'city.id','=','delivery_address.city_id')
            ->join('district', 'district.id','=','city.district_id')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('delivery_address.*','countries.country_name as country','state.name as state_name','district.name as district_name','city.name as city_name')
            ->where(function ($query) use ($searchTerm) {
        $query->where('delivery_address.id', 'like','%'. $searchTerm.'%')
          ->orWhere('state.name', 'like','%'. $searchTerm.'%')
          ->orWhere('countries.country_name', 'like', '%'. $searchTerm.'%')
          ->orWhere('delivery_address.address', 'like', '%'. $searchTerm.'%')
          ->orWhere('delivery_address.pincode', 'like', '%'. $searchTerm.'%')
          ->orWhere('city.name', 'like', '%'. $searchTerm.'%')
          ->orWhere('district.name', 'like', '%'. $searchTerm.'%');
        })
        ->paginate(100);
        return view('pages.admin.location.show',compact('location'));
    }

    public function getcityList($id)
    {
        $citys = DB::table("city")
        ->where("district_id",$id)
        ->select("city.*")->get();
        $html = '<option value="">Select City</option>';
        foreach ($citys as $key => $city) {
            $html .= '<option value="'.$city->id.'">'.$city->name.'</option>';
        }
        echo $html;
    }
}
