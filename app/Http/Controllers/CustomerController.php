<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\City;
use App\Traits\CaptureIpTrait;
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

class CustomerController extends Controller
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
        $customers = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->leftjoin('city', 'city.id','=','users.city')
            ->select('users.*','city.name as city')
            ->where('role_user.role_id','=','2')
            ->paginate(100);
        return View('pages.admin.customersmanagement.show', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $citys = City::all();
      return view('pages.admin.customersmanagement.create',compact('citys'));
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
            'email'                 => 'required|unique:users|email|max:255',
            'password'              => 'required|min:6|max:20',
            'image'                 => 'file|max:2000|mimes:jpeg,png,jpg',
            'phone'                 => 'numeric',
        ],
        [
          'image.max'               => 'The file size should be maximum 2MB.',
        ]
      );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $ipAddress = new CaptureIpTrait();
        if($request->hasFile('image')){
        
            $customer_image =  $request->file('image');
            $filename = Carbon::now()->format('Ymdhis').'customer'.$user->id.'.'.'jpg';
            $save_path = public_path().'/customer_images/';
            $path = $save_path.$filename;
            $public_path = url('/').'/public/customer_images/'.$filename;
            Image::make($customer_image)->save($save_path.$filename,20);
        }else{
            $public_path  ="";
        }
        $user = User::create([
            'first_name'       => $request->input('name'),
            'email'            => $request->input('email'),
            'phone'            => $request->input('phone'),
            'city'             => $request->input('city'),
            'password'         => bcrypt($request->input('password')),
            'token'            => str_random(64),
            'modified_user_id' => $user->id,
            'created_user_id'  => $user->id, 
            'admin_ip_address' => $ipAddress->getClientIp(),
            'activated'        => 1,
            'avtar'            => $public_path,
                   
        ]);
        $user->attachRole(2);
        return redirect('admin/user')->with('success', 'Record insert successfully');
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
       $customer = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('users.id','=',$id)
            ->first();
        $citys = City::all();
        return view('pages.admin.customersmanagement.edit',compact('customer','citys'));
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
                'email'                 => 'required|email|max:255',
                'image'                 => 'file|max:2000|mimes:jpeg,png,jpg',
                'phone'                 => 'numeric',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $ipAddress = new CaptureIpTrait();
        if(!empty($request->hasFile('image'))){
            $customer =  $request->file('image');
            $filename = Carbon::now()->format('Ymdhis').'customer'.$id.'.'.'jpg';
            $save_path = public_path().'/customer_images/';
            $path = $save_path.$filename;
            $public_path = url('/').'/public/customer_images/'.$filename;
            Image::make($customer)->save($save_path.$filename,20);
            $image  = array('avtar' => $public_path );
            $customer= User::where('id',$id)->update($image);
        }
        $datatwo = array(
            'first_name'        => $request->input('name'),
            'email'             => $request->input('email'),
            'phone'             => $request->input('phone'),
            'city'              => $request->input('city'),
        );
        $customer= User::where('id',$id)->update($datatwo);
        
        return redirect('admin/user');
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
        $user       = DB::table('users')->where('id',$id)->delete();
        $user       = DB::table('role_user')->where('user_id',$id)->delete();
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
        $customers = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->leftjoin('city', 'city.id','=','users.city')
            ->select('users.*','city.name as city')
            ->where('role_user.role_id','=','2')
            ->where(function ($query) use ($searchTerm) {
        $query->where('users.id', 'like','%'. $searchTerm.'%')
          ->orWhere('users.first_name', 'like','%'. $searchTerm.'%')
          ->orWhere('users.email', 'like', '%'. $searchTerm.'%')
          ->orWhere('users.city', 'like', '%'. $searchTerm.'%');
        })
        ->paginate(100);

                       // $html =    '<table id="datatable" class="table table-bordered table-striped">
                       //          <thead>
                       //              <tr>
                       //                 <th>Name</th>
                       //                 <th>Email</th>
                       //                 <th>City</th>
                       //                 <th>Status</th>
                       //                 <th>Action</th>
                       //              </tr>
                       //          </thead><tbody id="search_results">';
                       //          if($results->count() != 0){
                       //           foreach($results as $result){
                       //              $status = '';
                       //              if($result->activated == 0){
                       //                  $status = 'Inactive';
                       //              }
                       //              else{
                       //                  $status = 'Active';
                       //              }
                       //              $html .=   '<tr>
                       //                  <td>'.$result->first_name.'</td>
                       //                  <td>'.$result->email.'</td>
                       //                  <td>'.$result->city.'</td>
                       //                  <td>'. $status.'</td>
                       //                  <td>';
                       //                  $html .= '<form method="POST" action="/admin/user/'. $result->id .'" accept-charset="UTF-8" data-toggle="tooltip" title="Delete">
                       //                      <input name="_method" type="hidden" value="DELETE">
                       //                      <input type="hidden" name="_token" value="'.csrf_token().'">
                       //                      <button class="btn btn-danger btn-sm fa_padding_class" type="button" style="width: 100%;" data-toggle="modal" data-target="#confirmDelete" data-title="Delete User" data-message="Are you sure you want to delete this user ?"])">
                       //                      <i class="fa fa-trash-o"></i> 
                       //                      </button> 
                       //                      </form>';
                       //          $html .= '<a class="btn btn-sm btn-info fa_padding_class" href="/admin/user/' . $result->id .       '/edit" data-toggle="tooltip">
                       //                      <i class="fa fa-edit"></i>
                       //                      </a>';
                                       
                       //          if($result->activated == 1){
                       //              $html .= '<a class="btn btn-sm btn-primary fa_padding_class" href="/admin/user/unblock/'.$result->id.'" data-toggle="tooltip">Inactive</a>';
                                    
                       //              }
                       //               else{
                       //                 $html .= '<a class="btn btn-sm btn-danger fa_padding_class" href="/admin/user/block/'.$result->id.'" data-toggle="tooltip">Active</a>';
                       //               }
                       //           '</td>
                       //          </tr>';
                       //              }

                       //          }else{
                       //          $html .= '<tr>
                       //                  <td> no result found</td>
                       //                  <td></td>
                       //                  <td></td>
                       //                  <td></td>
                       //                  <td></td>
                       //          </tr>';
                       //         }
                       //        $html .=  '</tbody></table>'; 
                       //        $html .= $results->appends($data)->links();
                             
                       //        echo $html;
        return view('pages.admin.customersmanagement.show',compact('customers'));
    }
    public function block($id)
    {
        $active  = array('activated' => 1);
        $customer= User::where('id',$id)->update($active);
        return redirect('admin/user');     
    }
    public function unblock($id)
    {
        $active  = array('activated' => 0 );
        $customer= User::where('id',$id)->update($active);
        return redirect('admin/user');
    }
}
