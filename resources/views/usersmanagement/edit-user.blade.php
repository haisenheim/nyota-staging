@extends('layouts.admin.app')

@section('template_title')
    @lang('usersmanagement.editing-user', ['name' => $user->name])
@endsection

@section('template_linked_css')
    <style type="text/css">
        .btn-save,
        .pw-change-container {
            display: none;
        }
    </style>
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
					<a href="{{ url('admin/users/' . $user->id) }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left" title="@lang('usersmanagement.tooltips.back-users')">
						<i class="fa fa-fw fa-reply" aria-hidden="true"></i>
						@lang('usersmanagement.buttons.back-to-user')
					</a>
				</div>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
           {!! Form::open(array('route' => ['users.update', $user->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
                 {!! csrf_field() !!}
				<div class="box-body">
				<div class="form-group">
                    <label for="exampleInputFile">Profile Image</label>
                    <div class="input-group">
					  <div class="custom-file">
                        <input type="file" class="custom-file-input" name="avatar">
                      </div>
                    </div>
                  </div>
					<div class="form-group">
						{!! Form::label('name', trans('forms.create_user_label_username'), array('class' => 'control-label')); !!}
						{!! Form::text('name', $user->name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_username'))) !!}
						@if($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('first_name', trans('forms.create_user_label_firstname'), array('class' => 'control-label')); !!}
						{!! Form::text('first_name', $user->first_name, array('id' => 'first_name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_firstname'))) !!}
						 @if($errors->has('first_name'))
							<span class="help-block">
								<strong>{{ $errors->first('first_name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('last_name', trans('forms.create_user_label_lastname'), array('class' => 'control-label')); !!}
						{!! Form::text('last_name', $user->last_name, array('id' => 'last_name', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_lastname'))) !!}
						@if($errors->has('last_name'))
							<span class="help-block">
								<strong>{{ $errors->first('last_name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('forms.create_user_label_email'), array('class' => 'control-label')); !!}
						{!! Form::text('email', $user->email, array('id' => 'email', 'class' => 'form-control', 'placeholder' => trans('forms.create_user_ph_email'))) !!}
						@if ($errors->has('email'))
							<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group">
						{!! Form::label('role', trans('forms.create_user_label_role'), array('class' => 'control-label')); !!}
						<select class="custom-select form-control" name="role" id="role">
							<option value="">{{ trans('forms.create_user_ph_role') }}</option>
							@if ($roles)
								@foreach($roles as $role)
									<option value="{{ $role->id }}" {{ $currentRole->id == $role->id ? 'selected="selected"' : '' }}>{{ $role->name }}</option>
								@endforeach
							@endif
						</select>
						@if ($errors->has('role'))
							<span class="help-block">
								<strong>{{ $errors->first('role') }}</strong>
							</span>
						@endif
					</div>
					<div class="pw-change-container">
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
					<div class="box-footer">
						<div class="row">
							<div class="col-12 col-sm-6 mb-2">
								<a href="#" class="btn btn-outline-secondary btn-block btn-change-pw mt-3" title="@lang('forms.change-pw')">
									<i class="fa fa-fw fa-lock" aria-hidden="true"></i>
									<span></span> @lang('forms.change-pw')
								</a>
							</div>
							<div class="col-12 col-sm-6">
								{!! Form::button(trans('forms.save-changes'), array('class' => 'btn btn-success btn-block margin-bottom-1 mt-3 mb-2','type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#confirmSave', 'data-title' => trans('modals.edit_user__modal_text_confirm_title'), 'data-message' => trans('modals.edit_user__modal_text_confirm_message'))) !!}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
			

    @include('modals.modal-save')
    @include('modals.modal-delete')

@endsection

@section('footer_scripts')
  @include('scripts.delete-modal-script')
  @include('scripts.save-modal-script')
  @include('scripts.check-changed')
@endsection
