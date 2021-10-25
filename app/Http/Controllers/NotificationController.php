<?php

namespace App\Http\Controllers;

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
use App\Models\Notification;


class NotificationController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $users = DB::table('users')
                ->join('role_user', 'role_user.user_id','=','users.id')
                ->select('users.*')
                ->where('role_user.role_id','=','2')
                ->get();
        return View('pages.admin.notification.create', compact('users'));
    }

    public function create()
    {
      
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'title_en'                 => 'required',
            'message_en'               => 'required',
            'title_fr'                 => 'required',
            'message_fr'               => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        

        if(!empty($request->user))
        {
            $user_token =  DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->whereNotNull('users.device_token')
            ->where('users.id','=',$request->user)
            ->where('role_user.role_id','=','2')
            ->first();
            if(!empty($user_token->id))
            {
				$notificationdb = new Notification;
				$notificationdb->title_en = $request->title_en;
				$notificationdb->message_en = $request->message_en;
                $notificationdb->title_fr = $request->title_fr;
                $notificationdb->message_fr = $request->message_fr;
				$notificationdb->user_id = $user_token->id;
				$notificationdb->save();
			}
            if($user_token->language == 0){
                $title = $request->title_en;
                $message = $request->message_en;
            }else{
                $title = $request->title_fr;
                $message = $request->message_fr;
            }
			
            if(!empty($user_token->device_token)){
                $json_data =[
                    "to" => $user_token->device_token,
                    "mutable_content" => true,
                    "content_available" => true,
                    "data" => [
                        "body"  => $message,
                        "title" => $title,
                        "sound" => "default",
                         "alert" => $title,
                    ],
                    // "notification" => [
                    //     "sound"  => 1,
                    //     "body"  => $message,
                    //     "title" => $title,
                    // ],
                   
                ];
            }
            else{

                $json_data =[
                    "to" => '',
                    "mutable_content" => true,
                    "content_available" => true,
                    "data" => [
                        "body"  => $message,
                        "title" => $title,
                        "sound" => "default",
                        "alert" => $title,
                    ],
                    // "notification" => [
                    //     "sound"  => 1,
                    //     "body"  => $message,
                    //     "title" => $title,
                    // ],
                   
                ];
            }
            $data = json_encode($json_data);
            $url = 'https://fcm.googleapis.com/fcm/send';
            $server_key = 'AAAAVjwwJqY:APA91bEkZtbzslIM_druLxpC2Ckei6wyMwJApblVYWCDJG3j_psnogCRudkUtC_DfOjlZgdJ77HR8C-TL_0H69Yky-khQYjLMJmX-WiNmsUVsGTMFQ8OROss5lRUjC203uWlZrO2rtH6';
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key='.$server_key
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            if ($result === FALSE) {
                return redirect('admin/notification')->with('message', 'Notifcation not send');
            }
            else
            {
                return redirect('admin/notification')->with('message', 'Notifcation send');
            }
        }
        else
        {

            $user_token = DB::table('users')
                    ->join('role_user', 'role_user.user_id','=','users.id')
                    ->select('users.*')
                    ->whereNotNull('users.device_token')
                    ->where('role_user.role_id','=','2')
                    ->get();
            if(!empty($user_token)){
                foreach ($user_token as $key => $user_tokens) {
                    $notificationdb = new Notification;
					$notificationdb->title_en = $request->title_en;
                    $notificationdb->message_en = $request->message_en;
                    $notificationdb->title_fr = $request->title_fr;
                    $notificationdb->message_fr = $request->message_fr;
					$notificationdb->user_id = $user_tokens->id;
					$notificationdb->save();
                    if($user_tokens->language == 0){
                        $title = $request->title_en;
                        $message = $request->message_en;
                    }else{
                        $title = $request->title_fr;
                        $message = $request->message_fr;
                    }
                    if(!empty($user_tokens->device_token)){
                        $json_data =[
                            "to" => $user_tokens->device_token,
                            "mutable_content" => true,
                           "content_available" => true,
                            "data" => [
                                "body"  => $message,
                                "title" => $title,
                                "sound" => "default",
                                "alert" => $title,
                            ],
                            // "notification" => [
                            //     "sound"  => 1,
                            //     "body"  => $message,
                            //     "title" => $title,
                            // ],
                           
                        ];
                    }
                    else{

                        $json_data =[
                            "to" => '',
                            "mutable_content" => true,
                    "content_available" => true,
                            "data" => [
                                "body"  => $message,
                                "title" => $title,
                                "sound" => "default",
                                "alert" => $title,
                            ],
                            // "notification" => [
                            //     "sound"  => 1,
                            //     "body"  => $message,
                            //     "title" => $title,
                            // ],
                           
                        ];
                    }
                    $data = json_encode($json_data);
                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $server_key = 'AAAAVjwwJqY:APA91bEkZtbzslIM_druLxpC2Ckei6wyMwJApblVYWCDJG3j_psnogCRudkUtC_DfOjlZgdJ77HR8C-TL_0H69Yky-khQYjLMJmX-WiNmsUVsGTMFQ8OROss5lRUjC203uWlZrO2rtH6';
                    $headers = array(
                        'Content-Type:application/json',
                        'Authorization:key='.$server_key
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    $result = curl_exec($ch);
                }
            }
            return redirect('admin/notification')->with('message', 'Notifcation send');
        }
    }
}