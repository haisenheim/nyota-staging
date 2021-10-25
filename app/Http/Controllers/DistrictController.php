<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
use App\Models\District;
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

class DistrictController extends Controller
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
        $districts = DB::table('district')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('district.*','countries.country_name as country','state.name as state_name')
            ->paginate(100);
        return View('pages.admin.district.show', compact('districts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('pages.admin.district.create', compact('countries'));
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
            
            'name'                  => 'required',
            'country'               => 'required',
            'state'                 => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = District::create([
            'state_id'       => $request->input('state'),
            'name'           => $request->input('name'),
        ]);
        return redirect('admin/district')->with('success', 'Record insert successfully');
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
        $countries = Country::all();
        $district = DB::table('district')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('district.*','countries.country_name as country','countries.id as countryid','state.name as state_name')
            ->where('district.id','=',$id)
            ->first();
        $states = DB::table('state')->where('country_id','=',$district->countryid)->get();
        return view('pages.admin.district.edit',compact('district','countries','states'));
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
                'name'                 => 'required',
                'country'              => 'required',
                'state'                => 'required',
           ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $datatwo = array(
            'name'              => $request->input('name'),
            'state_id'        => $request->input('state'),
        );
        $customer= District::where('id',$id)->update($datatwo);
        
        return redirect('admin/district');
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
          $state    = DB::table('district')->where('id',$id)->delete();
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
        $districts = DB::table('district')
            ->join('state', 'state.id','=','district.state_id')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('district.*','countries.country_name as country','state.name as state_name')
            ->where(function ($query) use ($searchTerm) {
        $query->where('state.id', 'like','%'. $searchTerm.'%')
          ->orWhere('state.name', 'like','%'. $searchTerm.'%')
          ->orWhere('countries.country_name', 'like', '%'. $searchTerm.'%')
          ->orWhere('district.name', 'like', '%'. $searchTerm.'%');
        })
        ->paginate(100);
        return view('pages.admin.district.show',compact('districts'));
    }

    public function getStateList($id)
    {
        $states = DB::table("state")
        ->where("country_id",$id)
        ->select("state.*")->get();
        $html = '<option value="">Select State</option>';
        foreach ($states as $key => $state) {
            $html .= '<option value="'.$state->id.'">'.$state->name.'</option>';
        }
        echo $html;
    }
    
}
