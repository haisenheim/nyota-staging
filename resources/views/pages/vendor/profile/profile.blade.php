@extends('layouts.admin.app')
@section('template_title')
Profile
@endsection
@section('content')
<section class="content-header" >
	<h1>Profile</h1>
</section>
<div class="row">
	<div class="col-md-12">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="{{ $errors->has('old_password')||$errors->has('new_password') ? '' : 'active' }}"><a href="#activity" data-toggle="tab">Profile</a></li>
			<li class="{{ $errors->has('old_password')||$errors->has('new_password') ? 'active' : '' }}"><a href="#settings" data-toggle="tab">Change Password</a></li>
			<div class="pull-right" style="line-height: 40px;">
				<a href="{{ url('/vendor/home') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left" >
				<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
				Back
				</a>
			</div>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade in {{ $errors->has('old_password')||$errors->has('new_password') ? '' : 'active' }}  " id="activity">
				@if($user->isAdmin())
				{!! Form::open(array('route' =>  ['profileupdateadmin','id' => $user->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
				<div class="box-body">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', $user->first_name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('email', 'Email', array('class' => 'control-label')); !!}
						{!! Form::text('email', $user->email, array('id' => 'email', 'class' => 'form-control','readonly' => 'true', 'placeholder' => '')) !!}
						@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div class="box-footer">
					{!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
				</div>
				{!! Form::close() !!}
				@elseif($user->isvendor())
				{!! Form::open(array('route' =>  ['profileupdate','id' => $customer->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
				<div class="box-body">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
							{!! Form::text('name', $customer->first_name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
							@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
							@endif
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('phone', 'Phone Number', array('class' => 'control-label')); !!}
							{!! Form::text('phone', $customer->phone, array('id' => 'phone', 'class' => 'form-control', 'placeholder' => '')) !!}
							@if($errors->has('phone'))
							<span class="help-block">
								<strong>{{ $errors->first('phone') }}</strong>
							</span>
							@endif
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('email', 'Email', array('class' => 'control-label')); !!}
							{!! Form::text('email', $customer->email, array('id' => 'email', 'class' => 'form-control','readonly' => 'true', 'placeholder' => '')) !!}
							@if ($errors->has('email'))
							<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
							@endif
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('city', 'City', array('class' => 'control-label')); !!}
							{!! Form::text('city', $customer->city, array('id' => 'city', 'class' => 'form-control', 'rows' => 5, 'placeholder' => '')) !!}
							@if($errors->has('city'))
							<span class="help-block">
								<strong>{{ $errors->first('city') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
				<div class="box-footer">
					{!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
				</div>
				{!! Form::close() !!}
				@endif
			</div>
			<div class="{{ $errors->has('old_password')||$errors->has('new_password') ? 'active' : '' }} tab-pane" id="settings">
				@if($user->isAdmin())
				{!! Form::open(array('route' => ['changepasswordadmin','id' => $user->id], 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
				<div class="box-body">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('old_password', 'Old Password', array('class' => 'control-label')); !!}
						<input name="old_password" type="password" value="" id="old_password" class="form-control">
						@if ($errors->has('old_password'))
						<span class="help-block">
							<strong>{{ $errors->first('old_password') }}</strong>
						</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('new_password', 'New Password', array('class' => 'control-label')); !!}
						<input name="new_password" type="password" value="" id="new_password" class="form-control">
						@if ($errors->has('new_password'))
						<span class="help-block">
							<strong>{{ $errors->first('new_password') }}</strong>
						</span>
						@endif
					</div>
				</div>
				@elseif($user->isvendor())
				{!! Form::open(array('route' => ['changepassword','id' => $user->id], 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
				<div class="box-body">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('old_password', 'Old Password', array('class' => 'control-label')); !!}
						<input name="old_password" type="password" value="" id="old_password" class="form-control">
						@if ($errors->has('old_password'))
						<span class="help-block">
							<strong>{{ $errors->first('old_password') }}</strong>
						</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('new_password', 'New Password', array('class' => 'control-label')); !!}
						<input name="new_password" type="password" value="" id="new_password" class="form-control">
						@if ($errors->has('new_password'))
						<span class="help-block">
							<strong>{{ $errors->first('new_password') }}</strong>
						</span>
						@endif
					</div>
				</div>
				@endif
				<div class="box-footer">
					{!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
	</div>
</div>
@endsection