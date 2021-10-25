<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Country;
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

class StateController extends Controller
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
        $states = DB::table('state')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('state.*','countries.country_name as country')
            ->paginate(100);
        return View('pages.admin.state.show', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::all();
        return view('pages.admin.state.create', compact('countries'));
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
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = State::create([
            'country_id'       => $request->input('country'),
            'name'             => $request->input('name'),
        ]);
        return redirect('admin/state')->with('success', 'Record insert successfully');
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
        $state = DB::table('state')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('state.*','countries.country_name as country')
            ->where('state.id','=',$id)
            ->first();
        return view('pages.admin.state.edit',compact('state','countries'));
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
                'country'                 => 'required',
           ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $datatwo = array(
            'name'              => $request->input('name'),
            'country_id'        => $request->input('country'),
        );
        $customer= State::where('id',$id)->update($datatwo);
        
        return redirect('admin/state');
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
        $state          = DB::table('state')->where('id',$id)->delete();
        $district       = DB::table('district')->where('state_id',$id)->delete();
        $city           = DB::table('state')->where('id',$id)->delete();
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
        $states = DB::table('state')
            ->join('countries', 'countries.id','=','state.country_id')
            ->select('state.*','countries.country_name as country')
            ->where(function ($query) use ($searchTerm) {
        $query->where('state.id', 'like','%'. $searchTerm.'%')
          ->orWhere('state.name', 'like','%'. $searchTerm.'%')
          ->orWhere('countries.country_name', 'like', '%'. $searchTerm.'%');
        })
        ->paginate(100);
        return view('pages.admin.state.show',compact('states'));
    }
    
}
