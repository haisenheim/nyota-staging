<?php

namespace App\Http\Controllers;

use App\Models\Neighbourhood;
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

class NeighbourhoodController extends Controller
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
        $neighbourhoods = DB::table('neighbourhood')
            ->select('neighbourhood.*')
            ->paginate(100);
        return View('pages.admin.neighbourhood.show', compact('neighbourhoods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.neighbourhood.create');
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
            
            'neighbourhood'         => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $neighbourhood = Neighbourhood::create([
            'neighbour_hood'       => $request->input('neighbourhood'),
        ]);
        return redirect('admin/neighbourhood')->with('success', 'Record insert successfully');
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
        $neighbourhood = DB::table('neighbourhood')
            ->select('neighbourhood.*')
            ->where('neighbourhood.id','=',$id)
            ->first();
        return view('pages.admin.neighbourhood.edit',compact('neighbourhood'));
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
                'neighbourhood'        => 'required',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $datatwo = array(
            'neighbour_hood'              => $request->input('neighbourhood'),
        );
        $neighbourhood= Neighbourhood::where('id',$id)->update($datatwo);
        
        return redirect('admin/neighbourhood');
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
          $neighbourhood    = DB::table('neighbourhood')->where('id',$id)->delete();
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
        $neighbourhoods = DB::table('neighbourhood')
            ->select('neighbourhood.*')
            ->where(function ($query) use ($searchTerm) {
        $query->where('neighbourhood.id', '=', $searchTerm)
          ->orWhere('neighbourhood.neighbour_hood', 'like','%'. $searchTerm.'%');
        })
        ->paginate(100);
        return view('pages.admin.neighbourhood.show',compact('neighbourhoods'));
    }

   
}
