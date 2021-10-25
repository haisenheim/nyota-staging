<?php



namespace App\Http\Controllers;



use App\Models\User;

use App\Models\Category;

use App\Traits\CaptureIpTrait;

use Auth;

use File;

use DB;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Carbon\Carbon;

use Illuminate\Routing\UrlGenerator;

use jeremykenedy\LaravelRoles\Models\Role;

use Validator;

use Image;



class CategoryController extends Controller

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

        $categories = DB::table('category')

            ->select('category.*')

            ->where('category.status','=',0)

            ->paginate(100);


            foreach ($categories as $key => $value) {



                if($value->parent_id != '0'){

                        
                    $parent_category = DB::table('category')->where('id',$value->parent_id)->first();
                    $categories[$key]->parentcategory_name = !empty($parent_category->name) ? $parent_category->name : ''; 

                }

                else{

                    $categories[$key]->parentcategory_name = "";    

                }   

            }

        return View('pages.admin.categoriesmanagement.show', compact('categories'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $cat_array  = array('parent_id' => 0, 'status'=> 0);

        $categories = Category::where($cat_array)->get();

        return View('pages.admin.categoriesmanagement.create', compact('categories'));

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

            'image'                 => 'file|max:2000|mimes:jpeg,png,jpg',

        ],

        [

            'image.max'             => 'The file size should be maximum 2MB.',

        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }

        if($request->hasFile('image')){

        

            $category_image =  $request->file('image');

           // $filename = Carbon::now()->format('Ymdhis').'category'.'.'.$category_image->getClientOriginalExtension();

            $filename = Carbon::now()->format('Ymdhis').'category'.'.'.'jpg';

            $save_path = public_path().'/category_images/';

            $path = $save_path.$filename;

            $public_path = url('/').'/public/category_images/'.$filename;

            Image::make($category_image)->save($save_path.$filename,20);

        }else{

            $filename  ="";

        }

        if(empty($request->input('parent_category')))

        {

          $parent_id = 0;

        }

        else{

          $parent_id = $request->input('parent_category');

        }

        $categories = Category::create([

            'name'             => $request->input('name'),

            'slug'             => str_slug($request->input('name'), '_'),

            'parent_id'        => $parent_id,

            'full_description' => $request->input('full_description'),

            'short_description' => $request->input('short_description'),

            'is_active'        => 0,

            'language_id'      => 1, 

            'image'            => $filename,

                   

        ]);

       return redirect('admin/categories')->with('success', 'Record insert successfully');

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

        $category     = Category::where('id',$id)->where('status',0)->first();

        if($category->parent_id != '0'){

                    $parent_category = DB::table('category')->where('id',$category->parent_id)->first();

                    $category['parentcategory_name'] = $parent_category->name; 

                }

                else{

                    $category['parentcategory_name'] = "";    

                } 

        return view('pages.admin.categoriesmanagement.view',compact('category','parent_name'));

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

      $category   = Category::where('id',$id)->where('status',0)->first();

      $categories = Category::where('parent_id',0)->where('status',0)->get();   

      return view('pages.admin.categoriesmanagement.edit',compact('category','categories'));

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

            

            'image'                 => 'file|max:2000|mimes:jpeg,png',

        ]);

        if ($validator->fails()) {

            return back()->withErrors($validator)->withInput();

        }



        if(!empty($request->hasFile('image'))){

            $category_image =  $request->file('image');

            $filename = Carbon::now()->format('Ymdhis').'category'.'.'.'jpg';

          

            $save_path = public_path().'/category_images/';

            $path = $save_path.$filename;

            $public_path = url('/').'/public/category_images/'.$filename;

           $img_name = Image::make($category_image)->save($save_path.$filename,20);

            $image  = array('image' => $filename );

            $customer= category::where('id',$id)->update($image);

        }

         if(empty($request->input('parent_category')))

        {

          $parent_id = 0;

        }

        else{

          $parent_id = $request->input('parent_category');

        }

        $datatwo = array(

            'name'              => $request->input('name'),

            'slug'              => str_slug($request->input('name'), '_'),

            'parent_id'         => $parent_id,

            'full_description'  => $request->input('full_description'),

            'short_description' => $request->input('short_description'),

        );

        $customer= category::where('id',$id)->update($datatwo);

        

        return redirect('admin/categories');		  

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param int $id

     *

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request, Category $category)

    {
        $category_count = Category::where('parent_id',$request->input('cat_id'))
                                        ->where('status','0')
                                        ->count();
        $response_arr = array();
        if($category_count > 0) {
            $response_arr = array('status' => false, "message" => "Child is exist" );
            echo json_encode($response_arr);
        } else {
                $CategoryUpdate = Category::find($request->input('cat_id'));
                $CategoryUpdate->fill(array(
                    'status'     => 1,
                //    'deleted_at'  => date('Y-m-d H:i:s'),
                ));
                $CategoryUpdate->save();
                $cate_id        = array('category_id' => "");

                $product         = DB::table('product')->where('category_id',$request->input('cat_id'))->update($cate_id);
                $child_cate_id  = array('child_category_id' => "");

                $product         = DB::table('product')->where('child_category_id',$request->input('cat_id'))->update($child_cate_id);
                $response_arr = array('status' => true, "message" => trans('backend.usersmanagement.deleteSuccess') );
                echo json_encode($response_arr);
        }

        // $status         = array('status' => 1);

        // $category       = DB::table('category')->where('id',$id)->update($status);

        // $category       = DB::table('category')->where('parent_id',$id)->update($status);

        // $cate_id 		= array('category_id' => "");

        // $product 		= DB::table('product')->where('category_id',$id)->update($cate_id);

        // $child_cate_id 	= array('child_category_id' => "");

        // $product 		= DB::table('product')->where('child_category_id',$id)->update($child_cate_id);

        // return redirect()->back();

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

        $categories = DB::table('category')

            ->select('category.*')

            ->where('category.name','like','%'.$searchTerm.'%')

            ->where('category.status','=',0)

            ->paginate(100);

        foreach ($categories as $key => $value) {



                if($value->parent_id != '0'){

                    $parent_category = DB::table('category')->where('id',$value->parent_id)->first();

                    $categories[$key]->parentcategory_name = $parent_category->name; 

                }

                else{

                    $categories[$key]->parentcategory_name = "";    

                }   

            }

        return view('pages.admin.categoriesmanagement.show',compact('categories'));

    }

}

