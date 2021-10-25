<?php

namespace App\Http\Controllers;

use App\Models\ProductAttributeType;
use App\Traits\CaptureIpTrait;
use Auth;
use File;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Routing\UrlGenerator;
use Validator;
use Image;

class ProductAttributeTypeController extends Controller
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
        $attributetypes = DB::table('product_attribute_type')
            ->select('product_attribute_type.*')
            ->where('status','=',0)
            ->paginate(100);
        return View('pages.admin.attributetype.show', compact('attributetypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.attributetype.create');
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
            
            'name'                => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $attributetypes = ProductAttributeType::create([
            'name'          => $request->input('name'),
            'language_id'   => 1,
        ]);
        return redirect('admin/product_attribute_type')->with('success', 'Record insert successfully');
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
       $attributetype = ProductAttributeType::where('id',$id)->where('status',0)->first();
        return view('pages.admin.attributetype.edit',compact('attributetype'));
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
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $datatwo = array(
            'name'              => $request->input('name'),
        );
        $attributetype = ProductAttributeType::where('id',$id)->update($datatwo);
        
        return redirect('admin/product_attribute_type');
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
    	$status  = array('status' => 1);
        $product_attr_types = DB::table('product_attribute_type')->where('id',$id)->update($status);
        $product_attribute  = DB::table('product_attribute')->where('type_id',$id)->update($status);
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
        $attributetypes = DB::table('product_attribute_type')
            ->select('product_attribute_type.*')
            ->where('product_attribute_type.name','like','%'.$searchTerm.'%')
            ->where('product_attribute_type.status','=',0)
            ->paginate(100);

        return view('pages.admin.attributetype.show',compact('attributetypes'));
    }
}
