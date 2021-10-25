<?php
namespace App\Http\Controllers;
use DB;
use Carbon\Carbon;
use App\Models\Genreslanguage;
use Illuminate\Http\Request;
use Auth;
use Session;
use Validator;
use App\Models\Page;
use App\Models\Channelslanguage;
use App\Traits\CaptureIpTrait;
use Mail;



class WelcomeController extends Controller
{ 
    public function welcome(Request $request)
    {
        return redirect('/login');
		//return View('welcome-home');
    }
    public function privacypolicy(Request $request)
    { 
        $query = array('slug' => 'privacy-policy');
        $pages = Page::where($query)->first();
        return View('privacy-policy',compact('pages'));
    }
    public function sendmail (Request $request){
    	$validator = Validator::make($request->all(),
            [
                'first_name'  => 'required',
                'pswd' => 'required|numeric',
                'email' => 'required',
            ],
            [
            	'first_name.required'	=> 'Ce champ est requis.',
            	'pswd.required'	=> 'Ce champ est requis.',
            	'pswd.numeric'	=> 'Ce champ doit être un nombre.',
            	'email.required'	=> 'Ce champ est requis.',
            ]
        );
        if ($validator->fails()) {
          return response()->json(['fail' => true,'errors'=>$validator->errors()]);
        }
    	$message 	=  	'Contact form';
    	$firstname 	= $request->first_name;
    	$cellno		= $request->pswd;
    	$email 		= $request->email;
        $email 		=  	trim('info@nyota-app.com');
        $subject 	= 	'Contact form';
      	  Mail::send('emails.Notification-email',['key' => $message, 'first_name' => $firstname, 'cellno' => $cellno, 'email' => $email], function($message) use($email, $subject)
        {
            $message->to($email, 'Contact form')->subject($subject);
        });
    }
}
