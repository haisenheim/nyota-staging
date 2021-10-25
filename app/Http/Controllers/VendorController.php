<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\City;
use App\Models\User;
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

class VendorController extends Controller
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
             $vendors = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->leftjoin('city', 'city.id','=','users.city')
            ->select('users.*','city.name as city')
            ->where('role_user.role_id','=','3')
            ->paginate(100);
        return View('pages.admin.vendormanagement.show', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $citys = City::all();
        return view('pages.admin.vendormanagement.create',compact('citys'));
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
            
            'name'          => 'required',
            'email'         => 'required|unique:users|email|max:255',
            'password'      => 'required|min:6|max:20',
            'phone'         => 'numeric',
            'latitude'      => 'required',
            'longitude'     => 'required',

        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $ipAddress = new CaptureIpTrait();
        $user = User::create([
          'first_name'       => $request->input('name'),
          'email'            => $request->input('email'),
          'latitude'         => $request->input('latitude'),
          'longitude'        => $request->input('longitude'),
          'password'         => bcrypt($request->input('password')),
          'token'            => str_random(64),
          'modified_user_id' => $user->id,
          'created_user_id'  => $user->id, 
          'admin_ip_address' => $ipAddress->getClientIp(),
          'activated'        => 1,
          'phone'            => $request->input('phone'),
          'city'             => $request->input('city'), 
        ]);
         $last_id = $user->id;
         $user->attachRole(3);
        
         return redirect('admin/vendors')->with('success', 'Record insert successfully');
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
       $vendor = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('users.id','=',$id)
            ->first();
        $citys = City::all();
        return view('pages.admin.vendormanagement.edit',compact('vendor','citys'));
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
                'phone'                 => 'numeric',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
         $ipAddress = new CaptureIpTrait();
         $datatwo = array(
            'first_name'        => $request->input('name'),
            'email'             => $request->input('email'),
            'latitude'         => $request->input('latitude'),
            'longitude'        => $request->input('longitude'),
            'phone'            => $request->input('phone'),
            'city'             => $request->input('city'), 
         );
        $vendor= User::where('id',$id)->update($datatwo);
        return redirect('admin/vendors');
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
       $searchTerm = $request->input('search_box');
        $vendors = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('role_user.role_id','=','3')
            ->where(function ($query) use ($searchTerm) {
        $query->where('users.id', 'like','%'. $searchTerm.'%')
          ->orWhere('users.first_name', 'like','%'. $searchTerm.'%')
          ->orWhere('users.email', 'like','%'. $searchTerm.'%')
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
                       //          </thead>
                       //          <tbody id="search_results">';
                       //          if($results->count() != 0){
                       //           foreach($results as $result){
                       //              $status = '';
                       //              if($result->activated == 0){
                       //                  $status = 'Inactive';
                       //              }
                       //              else{
                       //                  $status = 'Active';
                       //              }
                       //             $html .=   '<tr>
                       //                  <td>'.$result->first_name.'</td>
                       //                  <td>'.$result->email.'</td>
                       //                  <td>'.$result->city.'</td>
                       //                  <td>'. $status.'</td>
                       //                  <td>';
                                      
                       //                 $html .= '<form method="POST" action="/admin/vendors/'. $result->id .'" accept-charset="UTF-8" data-toggle="tooltip" title="Delete">
                       //              <input name="_method" type="hidden" value="DELETE">
                       //              <input type="hidden" name="_token" value="'.csrf_token().'">
                       //              <button class="btn btn-danger btn-sm fa_padding_class" type="button" style="width: 100%;" data-toggle="modal" data-target="#confirmDelete" data-title="Delete vendor" data-message="Are you sure you want to delete this vendor ?"])">
                       //                  <i class="fa fa-trash-o "></i> 
                       //              </button> 
                       //          </form>';

                       //           $html .= '<a class="btn btn-sm btn-info fa_padding_class" href="/admin/vendors/' . $result->id . '/edit" data-toggle="tooltip">
                       //                      <i class="fa fa-edit"></i>
                       //                      </a>';

                       //               if($result->activated == 1){
                       //              $html .= '<a class="btn btn-sm btn-primary fa_padding_class" href="/admin/vendors/unblock/'.$result->id.'" data-toggle="tooltip">Inactive</a>';
                                    
                       //              }
                       //               else{
                       //                 $html .= '<a class="btn btn-sm btn-danger fa_padding_class" href="/admin/vendors/block/'.$result->id.'" data-toggle="tooltip">Active</a>';
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
                                      
                       //         $html .=  '</tbody></table>';
                              
                       //       echo $html;

        // return response()->json(['data' => json_encode($results)], Response::HTTP_OK);
        return view('pages.admin.vendormanagement.show',compact('vendors'));
    }
    public function block($id)
    {
        $active  = array('activated' => 1);
        $customer= User::where('id',$id)->update($active);
        return redirect('admin/vendors');     
    }
    public function unblock($id)
    {
        $active  = array('activated' => 0 );
        $customer= User::where('id',$id)->update($active);
        return redirect('admin/vendors');
    }
}
