<?php

namespace App\Http\Controllers;

use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
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

class ProductAttributeController extends Controller
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
        $attributes = DB::table('product_attribute')
            ->join('product_attribute_type', 'product_attribute_type.id','=','product_attribute.type_id')
            ->select('product_attribute.*','product_attribute_type.name as product_type_name')
            ->where('product_attribute.status','=',0)
            ->paginate(100);
        return View('pages.admin.attribute.show', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attributes_types = ProductAttributeType::where('status','=',0)->get();
        return view('pages.admin.attribute.create', compact('attributes_types'));
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
            
            'name'                          => 'required',
            'attribute_type'                =>  'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $attribute = ProductAttribute::create([
            'name'            => $request->input('name'),
            'language_id'     => 1,
            'type_id'         => $request->input('attribute_type'),
                   
        ]);
        return redirect('admin/product_attribute')->with('success', 'Record insert successfully');
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
       $attribute = ProductAttribute::where('id',$id)->where('status',0)->first();
       $attributes_types = ProductAttributeType::where('status','=',0)->get();
        return view('pages.admin.attribute.edit',compact('attribute','attributes_types'));
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
            'type_id'         => $request->input('attribute_type'),
        );
        $attribute= ProductAttribute::where('id',$id)->update($datatwo);
        
        return redirect('admin/product_attribute');
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
        $banner  = DB::table('product_attribute')->where('id',$id)->update($status);
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
        $attributes = DB::table('product_attribute')
            ->join('product_attribute_type', 'product_attribute_type.id','=','product_attribute.type_id')
            ->select('product_attribute.*','product_attribute_type.name as product_type_name')
            ->where('product_attribute.status','=',0)
            ->where(function ($query) use ($searchTerm) {
        $query->where('product_attribute.name', 'like','%'. $searchTerm.'%')
          ->orWhere('product_attribute_type.name', 'like','%'. $searchTerm.'%');
        })
            ->paginate(100);

        return view('pages.admin.attribute.show',compact('attributes'));
    }
    
}
