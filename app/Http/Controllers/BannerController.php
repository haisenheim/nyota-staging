<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Product;
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

class BannerController extends Controller
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
        $banners = DB::table('banner')
            ->select('banner.*')
            ->paginate(100);
        return View('pages.admin.banner.show', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$products = Product::join('category', 'category.id','=','product.category_id')->select('product.*')->where('product.is_active',0)->get();
        return view('pages.admin.banner.create', compact('products'));
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
            
            'image'                => 'required|file|max:2000|mimes:jpeg,png,jpg',
            'product'                 =>  'required',
        ],
        [
            'image.max'            => 'The file size should be maximum 2MB.', 
        ]
    );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $ipAddress = new CaptureIpTrait();
        if($request->hasFile('image')){
        
            $banner =  $request->file('image');
            $filename = Carbon::now()->format('Ymdhis').'banner'.'.'.'jpg';
            $save_path = public_path().'/banner/';
            $path = $save_path.$filename;
            $public_path = url('/').'/public/banner/'.$filename;
            Image::make($banner)->save($save_path.$filename,20);
        }else{
            $filename  ="";
        }
        $banner = Banner::create([
            'product'          => $request->input('product'),
            'language_id'   => 1,
            'image'         => $filename,
                   
        ]);
        return redirect('admin/banner')->with('success', 'Record insert successfully');
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
		$banner = Banner::where('id',$id)->first();
		$products = Product::where('is_active',0)->get();
        return view('pages.admin.banner.edit',compact('banner','products'));
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
                'image'                 => 'file|max:2000|mimes:jpeg,png,jpg',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if(!empty($request->hasFile('image'))){
            $banner =  $request->file('image');
            $filename = Carbon::now()->format('Ymdhis').'banner'.'.'.'jpg';
            $save_path = public_path().'/banner/';
            $path = $save_path.$filename;
            $public_path = url('/').'/public/banner/'.$filename;
            Image::make($banner)->save($save_path.$filename,20);
            $image  = array('image' => $filename );
            $banner= Banner::where('id',$id)->update($image);
        }
        $datatwo = array(
            'product'              => $request->input('product'),
        );
        $banner= Banner::where('id',$id)->update($datatwo);
        
        return redirect('admin/banner');
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
        $banner       = DB::table('banner')->where('id',$id)->delete();
        return redirect()->back();
    }

    /**
     * Method to search the users.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    
    
}
