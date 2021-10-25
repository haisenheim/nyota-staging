<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CaptureIpTrait;
use Auth;
use DB;
use Hash;
use Validator;
use View;

class ChangePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
    }

    public function update(Request $request, $id)
    {
    	$user = Auth::user();
    	$validator = Validator::make($request->all(), 
            [
                'old_password'             => 'required|min:6',
                'new_password'             => 'required_with:old_password|same:old_password|min:6',
            ],
            [
                'old_password.required'     => 'Please enter email address.',
                'new_password.required'   	=> 'Please enter confirm password.',
                'old_password.min'        	=> 'Enter minimum value 6.',
                'old_password.max'        	=> 'Enter maximum value 20.',
                'new_password.min'			=> 'Enter minimum value 6..',
                'new_password.max'          => 'Enter maximum value 20.',
                'new_password.same'			=> 'Not match password.'
                
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $ipAddress = new CaptureIpTrait();
		$datatwo = array(
            'password'     		=> bcrypt($request->input('old_password')),
            'token'            	=> str_random(64),
            'admin_ip_address' 	=> $ipAddress->getClientIp(),
            'activated'        	=> 1,
        );
        $customer= User::where('id',$id)->update($datatwo);
	    
	    return redirect()->back();
    }
}
