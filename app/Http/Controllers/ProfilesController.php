<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Theme;
use App\Models\User;
use App\Notifications\SendGoodbyeEmail;
use App\Traits\CaptureIpTrait;
use File;
use Auth;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\UrlGenerator;
use Image;
use jeremykenedy\Uuid\Uuid;
use Validator;
use View;

class ProfilesController extends Controller
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
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function index()
    {
        $user = Auth::user();
        $customer = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('users.id','=', $user->id)
           ->first();
            return view('pages.vendor.profile.profile', compact('user','customer'));
        
    }

    /**
     * Fetch user
     * (You can extract this to repository method).
     *
     * @param $username
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), 
            [
                'name'        => 'required',
                
            ]
               
            );
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $ipAddress = new CaptureIpTrait();
            $datatwo = array(
                'first_name'        => $request->input('name'),
                'phone'             => $request->input('phone'),
                'city'              => $request->input('city'),
                'token'             => str_random(64),
                'admin_ip_address'  => $ipAddress->getClientIp(),
                'activated'         => 1,
            );
            $customer= User::where('id',$id)->update($datatwo);
        
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param string $username
     *
     * @return Response
     */
    public function changepass(Request $request,$id)
    {
        $user = Auth::user();
        $request->validate([
        'new_password' => 'required|min:6',
        'old_password' => ['required', function ($attribute, $value, $fail) use ($user) {
            if (!\Hash::check($value, $user->password)) {
                return $fail(__('The current password is incorrect.'));
            }
        }],
        ]);
        $ipAddress = new CaptureIpTrait();
        $datatwo = array(
            'password'          => bcrypt($request->input('new_password')),
        );
        $customer= User::where('id',$id)->update($datatwo);
        return redirect()->back();
    }

    
}
