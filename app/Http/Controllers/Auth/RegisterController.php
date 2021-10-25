<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ActivationTrait;
use App\Traits\CaptchaTrait;
use App\Traits\CaptureIpTrait;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use jeremykenedy\LaravelRoles\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use ActivationTrait;
    use CaptchaTrait;
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', [
            'except' => 'logout',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function showregisterForm(){
        return view('auth.register');
    }
    protected function validator(array $data)
    {
        $data['captcha'] = $this->captchaCheck();

        if (!config('settings.reCaptchStatus')) {
            $data['captcha'] = true;
        }

        return Validator::make($data,
            [
                'name'            => '',
                'email'                 => 'required|unique:users|email|max:255',
                'password'              => 'required|min:6|max:30|confirmed',
                'g-recaptcha-response'  => '',
                'phone'                 => 'numeric',
             ]
            // [
            //     'name.unique'                   => trans('auth.userNameTaken'),
            //     'name.required'                 => trans('auth.userNameRequired'),
            //     'first_name.required'           => trans('auth.fNameRequired'),
            //     'last_name.required'            => trans('auth.lNameRequired'),
            //     'email.required'                => trans('auth.emailRequired'),
            //     'email.email'                   => trans('auth.emailInvalid'),
            //     'password.required'             => trans('auth.passwordRequired'),
            //     'password.min'                  => trans('auth.PasswordMin'),
            //     'password.max'                  => trans('auth.PasswordMax'),
            //     'g-recaptcha-response.required' => trans('auth.captchaRequire'),
            //     'captcha.min'                   => trans('auth.CaptchaWrong'),
            // ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(Request $request)
    {
        $ipAddress = new CaptureIpTrait();
        $role = Role::where('slug', '=', 'unverified')->first();
        $validator = Validator::make($request->all(), 
        [
                'name'            => '',
                'email'                 => 'required|unique:users|email|max:255',
                'password'              => 'required|min:6|max:30',
                'g-recaptcha-response'  => '',
                'phone'                 => 'numeric',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = User::create([
                'first_name'        => $request['name'],
                'email'             => $request['email'],
                'password'          => Hash::make($request['password']),
                'token'             => str_random(64),
                'signup_ip_address' => $ipAddress->getClientIp(),
                'activated'         => 1,
                'phone'             => $request['phone'],
                'city'              => $request['city'],
                //'activated'         => !config('settings.activation'),
            ]);

        $user->attachRole(3);
       // $this->initiateEmailActivation($user);
        //$this->auth->login($this->registrar->create($request->all()));
      return redirect('/')->with('message', 'Registered successfully, please login...!');
    }
}
