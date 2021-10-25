@extends('layouts.app')

@section('content')
<div class="login-box">
	<div class="login-logo">
		<a href="{{ URL::to('') }}"><img src="{{ URL::to('public/images/noyta-logo.png') }}" class=""></a>
	</div>
   
    <div class="login-box-body">
                <p class="login-box-msg">{{ __('Login') }}</p>
                    @if(session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                    @endif
                <form method="POST" action="{{ route('login') }}">
                        @csrf
						<div class="form-group has-feedback">
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
								@if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="form-group has-feedback">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="row">
							<div class="col-xs-8">
								<div class="checkbox icheck">
									<label>
										<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
									</label>
								</div>
							</div>
        
							<div class="col-xs-4">
								<button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Login') }}</button>
							</div>
        				</div>
					</form>
                    <div class="social-auth-links text-center">
    				</div>
                    <div class="row">
                        <div class="col-xs-8">
                    <a href="{{ route('password.request') }}">Forgot Password?</a><br>
                </div>
                {{--<div class="col-xs-4">
                    <a href="{{route('register')}}">Register</a><br>
                </div>--}}
                </div>
            </div>
      
</div>
@endsection
