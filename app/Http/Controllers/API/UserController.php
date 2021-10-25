<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request; 
use App\Traits\ActivationTrait;
use App\Models\Profile;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mobilesettings;
use Carbon\Carbon;
use App\Models\Roleuser;
use jeremykenedy\LaravelRoles\Models\Role;
use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;
use File;
use Image;

class UserController extends Controller 
{
    public $successStatus = 200;
    use ActivationTrait;
    use RegistersUsers;

    /* User Login */
    public function login()
    {
        $language = request('languageCode');
        if($language == 0){
            $verify_error   = 'Please verify your email id to login into the system.';
            $login_error   = 'Login failed. Please try again.';
            $email_address_error = 'Email already exists';
            $error   = 'The credentials does not match.';
            $message = 'Successfully Login.';
            $account_error = 'Your account has not created please try again.';
            $account_msg = 'Successfully Create Your Account. Please go to your email address and verify your account.';
        }
        else{
            $verify_error   = 'Veuillez confirmer ou nous envoyer par e-mail la connexion au système.';
            $login_error   = 'Connexion déposée. Pleasay Tree Again.';
            $email_address_error = 'Courriel Alriaday Exhausters';
            $error   = "The Credentials Deutsche Note Match.";
            $message = 'Connexion au sous-sol.';
            $account_error = "Votre compte Hans: Avis et lecture de l'arbre à nouveau.";
            $account_msg = 'Votre compte na pas été créé, veuillez réessayer.';
            $account_msg ='Bonne chance pour cela et votre compte. Playas va à votre adresse e-mail et confirmez votre compte.';
        }
        if(request('userType') == 0){
        if(Auth::attempt(['email' => request('emailID'), 'password' => request('password')]))
            {
          $user = Auth::user();
                if($user->activated == 1)
                {
                
                    if($user->isUser())
                    {
                        $data = array(); 
                        $datainsert = array(
                          'device_token'  => request('token'),
                          'device_id'     => request('deviceId'),
                        );
                        $usersupdate = User::where('id',$user->id)->update($datainsert);
                        $data['token'] =  $user->createToken('MyApp')-> accessToken;    
                        $success['status'] = '200';
                        $success['message'] = $message;
                        $success['languageCode'] = $user->language;
                        $success['data'][] = $data;
                        return response()->json($success, $this-> successStatus);
                    }
                    else{
                       return response()->json(['status' => '401', 'message'=>$login_error], 401);
                    }
                }
                else
                {
                    return response()->json(['status' => '401', 'message'=>$verify_error, 'data' => []], 401);
                }
            }
            else{
                return response()->json(['status' => '401', 'message'=>$error], 401); 
            }
        }
        elseif (request('userType') == 1) {
            $query = array('email' => request('emailID'),'login_type'=> 1);
            $socialuserscount = DB::table('users')->where($query)->count();

            if($socialuserscount != 0)
            { 
                $activated = array();
                
                $user = DB::table('users')->where($query)->first();
                $activated = ['activated' => 1];

                $userss = User::where($query)->update($activated);
                $user = DB::table('users')->where($query)->first();
                if($user->activated == 1)
                {
                    $datainsert = array(
                        'device_token'  => request('token'),
                        'device_id'     => request('deviceId'),
                    );
                    $usersupdate = User::where('id',$user->id)->update($datainsert);
                    $user = User::where($query)->first();
                    $data['token'] =  $user->createToken('MyApp')-> accessToken;
                    $success['status'] = '200';
                    $success['message'] = $message;
                    $success['languageCode'] = $user->language;
                    $success['data'][] = $data;         
                    return response()->json($success, $this-> successStatus);
                }
                else
                {
                    return response()->json(['status' => '401', 'message'=>$verify_error, 'data' => []], 401);
                }   
            }
            else{
                
                $userscount = DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->select('users.id')
                ->where('users.email',request('emailID'))
                ->where('role_user.role_id',2)
                ->count();
                if($userscount != 0)
                {   
                   return response()->json(['status' => '401', 'message'=>$email_address_error, 'data' => []], 401);
                }
                $insert['first_name'] = request('name');
                $insert['email']    = request('emailID');
                $insert['phone']    = request('phonenumber');
                $insert['city']     = request('city');
                $insert['login_type'] = 1;
                $insert['password'] = "";
                $insert['device_id'] = request('deviceId');
                $insert['device_token'] = request('token');
                $insert['activated'] = 1;
                $insert['signup_ip_address'] = '70.120.207.107';
                $user = User::create($insert);
                $profile = new Profile();
                $user->profile()->save($profile);
                $Mobilesettings = Mobilesettings::create(['user_id' => $user->id]);
                $roleinput['user_id'] = $user->id;
                $roleinput['role_id'] = 2;
                $role = Roleuser::create($roleinput);
                if(!$user || !$role)
                {
                    return response()->json(['status' => '401', 'message'=>$account_error], 401);
                }
                $this->initiateEmailActivation($user);
                $data['token'] =  $user->createToken('MyApp')-> accessToken;
                $success['status']  = '200';
                $success['message'] = $account_msg;
                $success['data'][]  = $data;
                return response()->json($success, $this-> successStatus);
            }
        }
        elseif (request('userType') == 2){
            $query = array('apple_id' => request('appleId'),'login_type'=> 2);
            $appleuserscount = DB::table('users')->where($query)->count();

            if($appleuserscount != 0)
            { 
                $activated = array();
                
                
                $activated = ['activated' => 1];

                $userss = User::where($query)->update($activated);
                $user = DB::table('users')->where($query)->first();
                if($user->activated == 1)
                {
                    $datainsert = array(
                        'device_token'  => request('token'),
                        'device_id'     => request('deviceId'),
                    );
                    $usersupdate             = User::where('id',$user->id)->update($datainsert);
                    $user                    = User::where($query)->first();
                    $data['token']           = $user->createToken('MyApp')-> accessToken;
                    $success['status']       = '200';
                    $success['message']      = $message;
                    $success['languageCode'] = $user->language;
                    $success['data'][]       = $data;         
                    return response()->json($success, $this-> successStatus);
                }
                else
                {
                    return response()->json(['status' => '401', 'message'=>$verify_error, 'data' => []], 401);
                }
            }
            else{
                $userscount = DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->select('users.id')
                ->where('users.email',request('emailID'))
                ->where('role_user.role_id',2)
                ->count();
                if($userscount != 0)
                {   
                   return response()->json(['status' => '401', 'message'=>$email_address_error, 'data' => []], 401);
                }
                $insert['first_name']   = request('name');
                $insert['email']        = request('emailID');
                $insert['phone']        = request('phonenumber');
                $insert['city']         = request('city');
                $insert['login_type']   = 2;
                $insert['password']     = "";
                $insert['device_id']    = request('deviceId');
                $insert['apple_id']     = request('appleId');
                $insert['device_token'] = request('token');
                $insert['activated']    = 1;
                $insert['signup_ip_address'] = '70.120.207.107';
                $user = User::create($insert);
                $roleinput['user_id']   = $user->id;
                $roleinput['role_id']   = 2;
                $role = Roleuser::create($roleinput);
                $profile = new Profile();
                $user->profile()->save($profile);
                $Mobilesettings = Mobilesettings::create(['user_id' => $user->id]);
                if(!$user || !$role)
                {
                    return response()->json(['status' => '401', 'message'=>$account_error], 401);
                }
                $this->initiateEmailActivation($user);
                $data['token']      = $user->createToken('MyApp')-> accessToken;
                $success['status']  = '200';
                $success['message'] = $account_msg;
                $success['data'][]  = $data;
                return response()->json($success, $this-> successStatus);
            }
        }
    }

    /* User Signup */
    public function register(Request $request) 
    { 
        $language = request('languageCode');
         if($language == 0){
            \App::setlocale('en');
        }else{
            \App::setlocale('fr');
        }
        if($language == 0){
            $email_address_error = 'Email already exists';
            $error   = 'Your account has not created please try again.';
            $message = 'Successfully Create Your Account. Please go to your email address and verify your account.';
        }
        else{
            $email_address_error = 'Courriel Alriaday Exhausters';
            $error   = "Votre compte Hans: Avis et lecture de l'arbre à nouveau.";
            $message = 'Bonne chance pour cela et votre compte. Playas va à votre adresse e-mail et confirmez votre compte.';
        }
        $data = array();
        $input = $request->all();
        $insert = array();
        $profile = new Profile();
        $userscount = DB::table('users')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->select('users.id')
            ->where('users.email',$input['emailID'])
            // ->where('role_user.role_id',2)
            // ->where('role_user.role_id',3)
            // ->where('role_user.role_id',1)
            ->count();
        if($userscount != 0)
        {   
           return response()->json(['status' => '401', 'message'=>$email_address_error, 'data' => []], 401);
        }
        $insert['first_name'] = $input['name'];
        $insert['email']    = $input['emailID'];
        $insert['phone']    = $input['phonenumber'];
        $insert['city']     = $input['city'];
        $insert['password'] = bcrypt($input['password']);
        $insert['device_id'] = $input['deviceId'];
        $insert['device_token'] = $input['token'];
        $insert['activated'] = 0;
        $insert['signup_ip_address'] = '70.120.207.107';
        $insert['token'] = str_random(64);      
        $user = User::create($insert);
        $user->profile()->save($profile);
        $roleinput['user_id'] = $user->id;
        $roleinput['role_id'] = 2;
        $role = Roleuser::create($roleinput);
        $Mobilesettings = Mobilesettings::create(['user_id' => $user->id]);
        if(!$user || !$role)
        {
            return response()->json(['status' => '401', 'message'=>$error], 401);
        }
        $this->initiateEmailActivation($user);
        \App::setlocale('en');
        $data['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['status']  = '200';
        $success['message'] = $message;
        $success['data'][]  = $data;
        return response()->json($success, $this-> successStatus);
    }

    /* User forgot password */
    public function forgotpassword(Request $request)
    {
        $user = User::where('email', '=', $request->get('emailID'))->first();
        // dd($request);
        // echo "string";
        // die;
        // if($user->language == 0){
        //     \App::setlocale('en');
        // }else{
        //     \App::setlocale('fr');
         //}
        if($user->language == 0){
          $error   = 'Please enter registered email address.';
          $message = 'Forgot Password link has been sent on your mail id.';
        }
        else{
          $error   = "Veuillez saisir une adresse e-mail enregistrée.";
          $message = 'Le lien Mot de passe oublié a été envoyé sur votre identifiant de messagerie.';
        }
        if(!$user) {
            return response()->json(['status' => '401', 'message'=>$error], 401);
        }
        $request['email'] =$request->get('emailID');
        $broker = $this->getPasswordBroker();
        $sendingResponse = $broker->sendResetLink($request->only('email'));

        if($sendingResponse !== Password::RESET_LINK_SENT) {
            throw new HttpException(500);
        }
        //\App::setlocale('en');
        $data = array();
        $success['status']  = '200';
        $success['message'] = $message;
        $success['data']    = $data;    

        return response()->json($success, $this-> successStatus);
    }

    /* User Profile */
    public function details(Request $request) 
    { 
        $header = $request->header('Authorization');
        if($header != null)
        {
            $user = Auth()->guard('api')->user($header);
            if($user->language == 0){
              $token_error   = 'Invalid token.';
              $message = 'User profile details.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $message = 'Détails du profil utilisateur.';
            }
            $data = array();
            $data['name'] = isset($user->first_name) ? $user->first_name."" : "";
            $data['email_address'] = isset($user->email) ? $user->email."" : "";
            $data['password'] = isset($user->password) ? $user->password."" : "";
            $data['phone_no'] = isset($user->phone) ? $user->phone."" : "";

            $image = Profile::where('user_id',$user->id)->first();
            if(!empty($image))
            {
                if($image['avatar'] == null)
                {
                    $data['image'] = "";
                }
                else
                {           
                    $data['image'] = $image->avatar;
                }
            }
            else
            {
                $data['image'] = "";
            }
            $success['status'] = '200';
            $success['message'] = $message;
            $success['data'][] = $data;         
            return response()->json($success, $this-> successStatus);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error, 'data' => []], 401); 
        }
         
    }

    /* User Profile Update */
    public function userupdate(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $currentUser = Auth()->guard('api')->user($header);
            $user = User::find($currentUser->id);
            // print_r($user);
            // die;
            if($user->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Details not updated.';
              $message = 'Profile Updated successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Détails non mis à jour.";
              $message = 'Mise à jour du profil réussie.';
            }
            $users = array();
            $users['first_name'] = $request->name;
            $users['phone'] = $request->phonenumber;
            $data = array();
            $user->fill($users);
            
            

           if($request->hasFile('image')) 
            {
                $avatar =  $request->file('image');
                $filename = Carbon::now()->format('Ymdhis').'avatar.'.$avatar->getClientOriginalExtension();
                $save_path = storage_path().'/users/'.$user->id.'/avatar/'; 
                $path = $save_path.$filename;
                $public_path = url('/').'/storage/users/'.$user->id.'/avatar/'.$filename; 
                File::makeDirectory($save_path, $mode = 0755, true, true);
                Image::make($avatar)->save($save_path.$filename);  
                $user->profile->avatar = $public_path;
                $user->profile->avatar_status = 1;
                $user->profile->save();
            }
            $success['status'] = '200';
            $success['message'] = $message;
            if($user->save())
            {
                $data['name'] = isset($user->first_name) ? $user->first_name : "";
                $data['phonenumber'] = isset($user->phone) ? $user->phone : "";
                $image = Profile::where('user_id',$user->id)->first();
                
                if(!empty($image))
                {
                    if($image['avatar'] == null)
                    {
                        $data['image'] = "";
                    }
                    else
                    {           
                        $data['image'] = $image->avatar;
                    }
                }
                else
                {
                    $data['image'] = "";
                }
                $success['data'][] = $data; 
                return response()->json($success,$this-> successStatus);
            }   
            else
            {
                return response()->json(['status' => '401', 'message'=>$error], 401); 
            }
            
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }

    /* Change Password */
    public function changepassword(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $users = array();
            $currentUser = Auth()->guard('api')->user($header);
            if($currentUser->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Password not changed. Please try again.';
              $pass_error = 'Old password do not match.';
              $message = 'Password changed successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Mot de passe non modifié. Veuillez réessayer.";
              $pass_error = "L'ancien mot de passe ne correspond pas.";
              $message = 'Le mot de passe a été changé avec succès.';
            }
            $user = User::find($currentUser->id);
            if (!Hash::check($request->old_password,$user->password)) 
            {
                return response()->json(['status' => '401', 'message'=>$pass_error], 401);
            }
            else
            {
                $data = array();
                if(!$user)
                    throw new NotFoundHttpException;
                    $users['password'] = Hash::make($request->new_password);
                    $user->fill($users);
            
                    $success['status'] = '200';
                    $success['message'] = $message;
                    $success['data'] = $data; 
                
                if($user->save())
                    return response()->json($success,$this-> successStatus);
                else   
                    return response()->json(['status' => '401', 'message'=>$error], 401);
            }
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }

    public function deleteuser(Request $request)
    {
        $header = $request->header('Authorization');
        if($header != null)
        {
            $currentUser = Auth()->guard('api')->user($header);
            if($currentUser->language == 0){
              $token_error   = 'Invalid token.';
              $error   = 'Your account can not delete. Please try again.';
              $message = 'Your account has been deleted successfully.';
            }
            else{
              $token_error   = 'Jeton invalide.';
              $error   = "Votre compte ne peut pas supprimer. Veuillez réessayer.";
              $message = 'Votre compte a bien été supprimé.';
            }
            $user = User::find($currentUser->id);
            $users = array();
            $users['activated'] = 0;
            $user->fill($users);
            $data = array();
            $success['status'] = '200';
            $success['message'] = $message;
            $success['data'] = $data; 
            if($user->save())
                return response()->json($success,$this-> successStatus);
            else   
                return response()->json(['status' => '401', 'message'=>$error], 401);
        }
        else
        {
            return response()->json(['status' => '401', 'message'=>$token_error], 401); 
        }
    }

    private function getPasswordBroker()
    {
        return Password::broker();
    }

}