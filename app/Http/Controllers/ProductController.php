<?php

namespace App\Http\Controllers;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductAttributeType;
use App\Models\Product;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Settings;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
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

class ProductController extends Controller
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

        $products = DB::table('product')
        ->join('category', 'category.id','=','product.category_id')
            ->select('product.*','category.name as category')
            ->where('product.is_active',0)
            ->paginate(100);
        return View('pages.admin.productsmanagement.show', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $attributetypes = DB::table('product_attribute_type')
                    ->join('product_attribute', 'product_attribute.type_id','=','product_attribute_type.id')
                    ->select('product_attribute_type.*')
                    ->where('product_attribute_type.status',0)
                    ->groupBy('product_attribute.type_id')
                    ->get();
        $vendor =  DB::table('users')
                    ->join('role_user', 'role_user.user_id','=','users.id')
                    ->select('users.*')
                    ->where('role_user.role_id',3)
                    ->get();
        $categories = Category::where('status','=',0)->where('parent_id',0)->get();
        $child_categories = Category::where('status','=',0)->where('parent_id','!=',0)->get();
        return view('pages.admin.productsmanagement.create', compact('categories','attributetypes','child_categories','vendor'));
    }

    public function addcomment($id){
      $comments = DB::table('product_comment')
                    ->join('users', 'users.id','=','product_comment.user_id')
                    ->select('product_comment.*','users.first_name as username')
                    ->where('product_comment.product_id',$id)
                    ->get();
      return view('pages.admin.productsmanagement.addcomment',compact('id','comments'));
    }

    public function storecomment(Request $request,$id){
      $validator = Validator::make($request->all(), 
            [
                'comment'        => 'required',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
      $user = Auth::user();
      $user_id = $user->id;
      $Comments = Comment::create([
            'product_id'            => $id, 
            'user_id'               => $user_id, 
            'comment'               => $request->input('comment'),
            'rating'                => $request->input('rating'),
        ]);
      return redirect()->back();
    }

    public function editcomment($id){
      $comment = DB::table('product_comment')
                    ->join('users', 'users.id','=','product_comment.user_id')
                    ->select('product_comment.*','users.first_name as username')
                    ->where('product_comment.id',$id)
                    ->first();
      return view('pages.admin.productsmanagement.editcomment',compact('comment'));
    }

    public function updatecomment(Request $request, $id){
      $user = Auth::user();
      $user_id = $user->id;
      $datatwo = array(
          //  'product_id'            => $id, 
            'user_id'               => $user_id, 
            'comment'               => $request->input('comment'),
            'rating'                => $request->input('rating'),
      );
     $comment = Comment::where('id',$id)->update($datatwo);
      return redirect('admin/products/addcomment/'.$request->input('product_id'))->with('success', 'Record Update successfully');
    }

    public function destroycomment($id){
      $comment  = DB::table('product_comment')->where('id',$id)->delete();
      return redirect()->back();
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
            
            'name'                => 'required|max:150',
            'category'            => 'required',
            'child_category'      => 'required',
            'sku'                 => 'required|unique:product',
            'status'              => 'required',
            'image'               => 'max:2000|mimes:jpeg,png,jpg',
            'attribute'           => 'required',
            'vendor'              => 'required',
            'attributes.*.sprice' => 'required',
            'attributes.*.rprice' => 'required|numeric|gte:attributes.*.sprice',
        ],
        [
           'attributes.*.rprice.gte' => 'The price must be greater than and equal to sale price.',
        ]
      );
        if ($validator->fails()) {
          return response()->json(['fail' => true,'errors'=>$validator->errors()]);
        }


        if(!empty($request->attribute))
        {
          $attribute = implode(", ",$request->attribute);
          $attributet 	= $request->input('attributes');

          if(!empty($attributet)){
            foreach ($attributet as $key => $value) {
          		$attr[] = $value;
          	}
            $json_attribute = json_encode($attr);
          }
          else{
            return response()->json(['fail' => true,'attr_errors'=>'test']);
          }
        }
		    else
        {
            $json_attribute = NULL;
            
        }
        $product = Product::create([
            'name'          => $request->name,
            'category_id'   => $request->category,
            'child_category_id' => $request->child_category,
            'user_id'       => $request->vendor,
            'sku'           => $request->sku,
            'full_description'  => $request->full_description,
            'short_description' => $request->short_description,
            'is_active'         => $request->status,
            'attribute' 		=> $json_attribute,
            'attribute_type_id' => $attribute,             
            'language_id'       => 1,
                      
        ]);
        $last_id = $product->id;
        if(!empty($request->attribute))
        {
            $attributet  = $request->input('attributes');
          foreach ($attributet as $key => $value) {
            $insert = array();
            $insert['product_id'] = $last_id;
            $insert['sale_price'] = $value['sprice'];
            $insert['regular_price'] = $value['rprice'];
            $insert['quantity']   = $value['quantity'];
            $insert['attribute'] =  json_encode($value);
            $product_variation = ProductVariation::create($insert);
          }
         }
       if(!empty($request->input('document'))){
            foreach ($request->input('document', []) as $file) {
                $Gallery = Gallery::create([
                    'image' => $file, 
                    'module_type' => 0, 
                    'module_id' => $last_id,
                ]);
            }
        }
		    return response()->json([
                'fail' => false,
                'success' => 'Record insert successfully'
            ]);
         return redirect('admin/products')->with('success', 'Record insert successfully');
    }

    public function storeimage(Request $request){
     
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);

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
        $currency = Settings::where('slug','=','currency')->first();
        $product = DB::table('product')
                    ->join('category AS cate', 'cate.id','=','product.category_id')
                    ->leftjoin('category AS child_cate', 'child_cate.id','=','product.child_category_id')
                    ->select('product.*','cate.name as category','child_cate.name as child_category')
                    ->where('product.id',$id)
                    ->first();
        $medias =   DB::table('media')
                    ->select('media.*')
                    ->where('media.module_id',$id)
                    ->where('media.module_type',0)
                    ->get();
        $seletedatts = json_decode($product->attribute);
        $mailatt = array();
		if(!empty($product->attribute_type_id)){
  		$attribute_type_ids = explode(",",$product->attribute_type_id);
  		foreach($attribute_type_ids as $attribute_type_id)
  		{
  			$att = array(); 
  			$attributetypesget = ProductAttributeType::where('id','=',$attribute_type_id)->first();
  			$att['id'] = $attributetypesget->id;
  			$att['name'] = $attributetypesget->name;
  			
  			$productAttributes = ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
  			foreach($productAttributes as $productAttribute){
          $subarray = array();
          $subarray['id'] = $productAttribute->id;  
          $subarray['name'] = $productAttribute->name;
          $att['attr'][]= $subarray;
        }
  			$mailatt[] = $att;
      }
    }            
        return view('pages.admin.productsmanagement.view',compact('product','medias','mailatt','seletedatts','currency'));
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
		$product = DB::table('product')->select('product.*')->where('product.id',$id)->first();
    $vendor   =  DB::table('users')
                    ->join('role_user', 'role_user.user_id','=','users.id')
                    ->select('users.*')
                    ->where('role_user.role_id',3)
                    ->get();	
    $categories = Category::where('status','=',0)->where('parent_id',0)->get();
    $child_categories = Category::where('status','=',0)->where('parent_id',$product->category_id)->get();
		$medias = DB::table('media')->select('media.*')->where('media.module_id',$id)->where('media.module_type',0)->get(); 
		$attributetypes = DB::table('product_attribute_type')
                    ->join('product_attribute', 'product_attribute.type_id','=','product_attribute_type.id')
                    ->select('product_attribute_type.*')
                    ->where('product_attribute_type.status',0)
                    ->groupBy('product_attribute.type_id')
                    ->get();
		
		$seletedatts = json_decode($product->attribute);
    $mailatt = array();
		if(!empty($product->attribute_type_id)){
		$attribute_type_ids = explode(",",$product->attribute_type_id);
		foreach($attribute_type_ids as $attribute_type_id)
		{
			$att = array(); 
			$attributetypesget = ProductAttributeType::where('id','=',$attribute_type_id)->first();
     
			$att['id'] = $attributetypesget->id;
			$att['name'] = $attributetypesget->name;
			
			$productAttributes = ProductAttribute::where('type_id','=',$attributetypesget->id)->get();
			foreach($productAttributes as $productAttribute){
				$subarray = array();
				$subarray['id'] = $productAttribute->id;	
				$subarray['name'] = $productAttribute->name;
				$att['attr'][]= $subarray;
			}
			
			$mailatt[] = $att;
      
		}
		}	
		return view('pages.admin.productsmanagement.edit',compact('product','categories','medias','attributetypes','mailatt','seletedatts','child_categories','vendor'));
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
		$user = Auth::user();
            $validator = Validator::make($request->all(), 
            [
                'name'          => 'required|max:150',
                'image'         => 'file|max:2000|mimes:jpeg,png,jpg',
                'attribute'     => 'required',
                'category'      => 'required',
                'child_category'      => 'required',
                'vendor'        => 'required',
                'attributes.*.sprice' =>'required',
                'attributes.*.rprice' =>'required|numeric|gte:attributes.*.sprice',
            ],
            [
               'attributes.*.rprice.gte' => 'The price must be greater than and equal to sale price.',
            ]
        );
            if ($validator->fails()) {
          return response()->json(['fail' => true,'errors'=>$validator->errors()]);
        }
        if(!empty($request->attribute))
        {
          $attribute  = $request->input('attributes');
          if(empty($attribute)){
            return response()->json(['fail' => true,'attr_errors'=>'test']);
          }
          foreach ($attribute as $key => $value) {
            $attr[] = $value;
          }
          $json_attribute = json_encode($attr);
          $attr = array('attribute'   => $json_attribute ,'attribute_type_id' => implode(", ",$request->attribute));
        }
        else
        {
          $json_attribute = NULL;
          $attr = array('attribute'   => $json_attribute ,'attribute_type_id' => NULL);
        }
        
            $product = Product::where('id',$id)->update($attr);
        $datatwo = array(
            'name'          => $request->input('name'),
            'category_id'   => $request->input('category'),
            'child_category_id' => $request->child_category,
            'user_id'       => $request->vendor,
            'sku'           => $request->input('sku'),
            'full_description'  => $request->input('full_description'),
            'short_description' => $request->input('short_description'),
            'is_active'         => $request->input('status'),
        );
        $product = Product::where('id',$id)->update($datatwo);
        $variant = ProductVariation::where('product_id',$id)->delete();
        if(!empty($request->attribute))
        {
            $attribute  = $request->input('attributes');
          foreach ($attribute as $key => $value) {
            $insert = array();
            $insert['product_id'] = $id;
            $insert['sale_price'] = $value['sprice'];
            $insert['regular_price'] = $value['rprice'];
            $insert['quantity']   = $value['quantity'];
            $insert['attribute'] =  json_encode($value);
            $product_variation = ProductVariation::create($insert);
          }
         }
        if(!empty($request->input('document'))){
            foreach ($request->input('document', []) as $file) {
                $Gallery = Gallery::create([
                    'image' => $file, 
                    'module_type' => 0, 
                    'module_id' => $id,
                ]);
            }
        }
         return redirect('admin/products');
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
        $product     = DB::table('product')->where('id',$id)->delete();
        $mediarry    = array('module_type' => 0, 'module_id' => $id );
        $gallery     = DB::table('media')->where($mediarry)->delete();
        $variant = ProductVariation::where('product_id',$id)->delete();
        return redirect()->back();
    }

    public function destroyimage($id)
    {
      $mediarry    = array('module_type' => 0, 'id' => $id );
      $gallery     = DB::table('media')->where($mediarry)->delete();
      return response()->json([
        'success' => 'Record deleted successfully!'
      ]);
    }

    


    public function searchattribute($id)
    {
        $data = explode(",", $id);
        $variable = $_GET['variable'];
       	$attribute = array();
        foreach($data as $attribute_type_id){
            $attributetypes = DB::table('product_attribute')
                        ->join('product_attribute_type','product_attribute_type.id','=','product_attribute.type_id')
                        ->where('product_attribute.type_id',$attribute_type_id)
                        ->where('product_attribute.status',0)
                        ->select('product_attribute.name','product_attribute.id','product_attribute_type.name as attribute_type_name','product_attribute_type.id as attribute_type_id')->get();
            foreach ($attributetypes as $key => $value) {
                      $attribute[$value->attribute_type_name][$key]['name'] = $value->name;
                      $attribute[$value->attribute_type_name][$key]['id'] = $value->id;
                      $attribute[$value->attribute_type_name][$key]['type_id'] = $value->attribute_type_id;

            }           
        }
        $html = '<div class="main col-lg-12 col-md-12 col-sm-12" id="div1"><div class="col-lg-12 col-md-12 col-sm-12 border-class"><div class="col-lg-12 col-md-12 col-sm-12 no-padding">'; 
                foreach ($attribute as $key => $value) {
                  $html .= '<div class="form-group col-lg-4 col-md-4 col-sm-4">
                            <label for="color" class = "control-label">'.$key.'</label>
                            <select class="form-control" name="attributes['.$variable.'][a_'.$value[0]['type_id'].']" id="status" required>
                            <option value="">Select '.$key.'</option>';
                    foreach ($value as $key => $attrbute) {        
                        $html .= '<option value="'.$attrbute['id'].'">'.$attrbute['name'].'</option>';
                    }
                    $html .=  '</select><strong class="invalid-feedback" id="error-'.$variable.'-a_'.$value[0]['type_id'].'"></strong></div>';
                }  
        $html .= '</div><div class="col-lg-12 col-md-12 col-sm-12 no-padding"><div class="form-group col-lg-4 col-md-4 col-sm-4">
                  <label for="price" class = "control-label">Price</label>
                  <input type="text" name="attributes['.$variable.'][rprice]" id="price" value="" class="form-control" required>
                  <strong class="invalid-feedback" id="error-'.$variable.'-rprice"></strong>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-4">
                  <label for="sale_price" class = "control-label">Sale Price</label>
                  <input type="text" name="attributes['.$variable.'][sprice]" id="sale_price" value="" class="form-control" required>
                  <strong class="invalid-feedback" id="error-'.$variable.'-sprice"></strong>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-3">
                  <label for="quantity" class = "control-label">Quantity</label>
                  <input type="number" step="1" name="attributes['.$variable.'][quantity]" id="quantity" min="0" value="" class="form-control" required>
                  <strong class="invalid-feedback" id="error-'.$variable.'-quantity"></strong>
                </div>
                <div class="form-group col-lg-1 col-md-1 col-sm-1">
                <a class="remove pull-right btn btn-danger btn-xs button-top"><i class="fa fa-minus" aria-hidden="true"></i></a>
                </div>
                </div></div></div>';
            echo $html;
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
        $products= DB::table('product')
            ->join('category', 'category.id','=','product.category_id')
            ->select('product.*','category.name as category')
            ->where(function ($query) use ($searchTerm) {
            $query->where('product.id', 'like','%'. $searchTerm.'%')
            ->orWhere('product.name', 'like','%'. $searchTerm.'%')
            ->orWhere('category.name', 'like', '%'. $searchTerm.'%')
            ->orWhere('product.sku', 'like', '%'. $searchTerm.'%');
            })
        ->paginate(100);

        return view('pages.admin.productsmanagement.show',compact('products'));
    }

  public function getcategory(Request $request,$id)
  {
    $child_categories = Category::where('parent_id', '=', $id)->get();
    $html = '<option value="">Select Child Category</option>';
          foreach ($child_categories as $key => $child_categorie) {
              $html .= '<option value="'.$child_categorie->id.'">'.$child_categorie->name.'</option>';
          }
    echo $html;
  }
  public function import()
  {
    //Excel::import(new ProductImport, request()->file('upload_file'));
    try{
      Excel::import(new ProductImport, request()->file('upload_file'));
    }catch(\Maatwebsite\Excel\Validators\ValidationException $e){
      $failures = $e->failures();
      // print_r($failures);
      // die;
      $test = array();
      foreach ($failures as $key => $failure) {
        foreach ($failure->errors() as $error){
           $error_message = 'There was an error on row '.$failure->row().' '.$error;
        }
        $test[] = $error_message;
      }
     return response()->json(['fail' => true,'errors'=>$test]);
    }
  }
}
