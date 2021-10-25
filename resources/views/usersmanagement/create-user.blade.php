@extends('layouts.admin.app')

@section('template_title')
    @lang('usersmanagement.create-new-user')
@endsection

@section('template_fastload_css')
@endsection

@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
				<div class="pull-right">
					<a href="{{ route('users') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left" title="@lang('usersmanagement.tooltips.back-users')">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						@lang('usersmanagement.buttons.back-to-users')
					</a>
				</div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(array('route' => 'users.store', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation')) !!}
				{!! csrf_field() !!}
              <div class="box-body">
                <div class="form-group">
					{!! Form::label('email', trans('forms.create_user_label_email'), array('class' => 'control-label')); !!}
					{!! Form::text('email', NULL, array('id' => 'email', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_email'))) !!}
					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
					{!! Form::label('name', trans('forms.create_user_label_username'), array('class' => 'control-label')); !!}
					{!! Form::text('name', NULL, array('id' => 'name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_username'))) !!}
					@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
					{!! Form::label('first_name', trans('forms.create_user_label_firstname'), array('class' => 'control-label')); !!}
					{!! Form::text('first_name', NULL, array('id' => 'first_name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_firstname'))) !!}
					@if ($errors->has('first_name'))
						<span class="help-block">
							<strong>{{ $errors->first('first_name') }}</strong>
						</span>
					@endif
				</div>
				<div class="form-group">
                  {!! Form::label('last_name', trans('forms.create_user_label_lastname'), array('class' => 'control-label')); !!}
                  {!! Form::text('last_name', NULL, array('id' => 'last_name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_lastname'))) !!}
				  @if ($errors->has('last_name'))
					<span class="help-block">
						<strong>{{ $errors->first('last_name') }}</strong>
					</span>
				@endif
                </div>
				<div class="form-group">
                  {!! Form::label('role', trans('forms.create_user_label_role'), array('class' => 'control-label')); !!}
                  <select class="custom-select form-control" name="role" id="role">
						<option value="">{{ trans('forms.create_user_ph_role') }}</option>
						@if ($roles)
							@foreach($roles as $role)
								<option value="{{ $role->id }}">{{ $role->name }}</option>
							@endforeach
						@endif
					</select>
					@if ($errors->has('role'))
						<span class="help-block">
							<strong>{{ $errors->first('role') }}</strong>
						</span>
					@endif
                </div>
				<div class="form-group">
                  {!! Form::label('password', trans('forms.create_user_label_password'), array('class' => 'control-label')); !!}
                  {!! Form::password('password', array('id' => 'password', 'class' => 'form-control ', 'placeholder' => trans('forms.create_user_ph_password'))) !!}
				  @if ($errors->has('password'))
					<span class="help-block">
						<strong>{{ $errors->first('password') }}</strong>
					</span>
				@endif
                </div>
				<div class="form-group">
                  {!! Form::label('password_confirmation', trans('forms.create_user_label_pw_confirmation'), array('class' => 'control-label')); !!}
                  {!! Form::password('password_confirmation', array('id' => 'password_confirmation', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_pw_confirmation'))) !!}
				  @if ($errors->has('password_confirmation'))
						<span class="help-block">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                {!! Form::button(trans('forms.create_user_button_text'), array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
              </div>
            {!! Form::close() !!}
          </div>
          <!-- /.box -->
		</div>
	</div>
@endsection

@section('footer_scripts')
@endsection
