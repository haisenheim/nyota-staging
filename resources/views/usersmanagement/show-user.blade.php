@extends('layouts.admin.app')

@section('template_title')
  @lang('usersmanagement.showing-user', ['name' => $user->name])
@endsection

@php
  $levelAmount = trans('usersmanagement.labelUserLevel');
  if ($user->level() >= 2) {
    $levelAmount = trans('usersmanagement.labelUserLevels');
  }
@endphp

@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-3">
          <!-- Profile Image -->
			<div class="box box-primary">
				<div class="box-body box-profile">
					<img src="@if ($user->profile && $user->profile->avatar_status == 1) {{ $user->profile->avatar }} @else {{ Gravatar::get($user->email) }} @endif" alt="{{ $user->name }}" class="profile-user-img img-responsive img-circle">
					<h3 class="profile-username text-center">{{ $user->name }}</h3>
					<p class="text-muted text-center">{{ $user->first_name }} {{ $user->last_name }}</p>
					<a href="{{ url('/profile/'.$user->name) }}" class="btn btn-block btn-info" data-toggle="tooltip" data-placement="left" title="{{ trans('usersmanagement.viewProfile') }}">
					  <i class="fa fa-eye fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm hidden-md"> {{ trans('usersmanagement.viewProfile') }}</span>
					</a>
					<a href="{{ url('admin/users/'.$user->id.'/edit') }}" class="btn btn-block btn-warning" data-toggle="tooltip" data-placement="top" title="{{ trans('usersmanagement.editUser') }}">
                      <i class="fa fa-pencil fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm hidden-md"> {{ trans('usersmanagement.editUser') }} </span>
                    </a>
                    {!! Form::open(array('url' => 'users/' . $user->id, 'class' => 'form-inline', 'data-toggle' => 'tooltip', 'data-placement' => 'right', 'title' => trans('usersmanagement.deleteUser'))) !!}
                      {!! Form::hidden('_method', 'DELETE') !!}
                      {!! Form::button('<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> <span class="hidden-xs hidden-sm hidden-md">' . trans('usersmanagement.deleteUser') . '</span>' , array('class' => 'btn btn-danger btn-block','type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete User', 'data-message' => 'Are you sure you want to delete this user?', 'style' => 'margin-top:5px')) !!}
                    {!! Form::close() !!}
				</div>
            <!-- /.box-body -->
			</div>
          <!-- /.box -->
		</div>
		<div class="col-md-9">
			 <div class="box box-primary">
            <div class="box-header with-border">
				<h3 class="box-title">@lang('usersmanagement.showing-user-title', ['name' => $user->name])</h3>
				<a href="{{ route('users') }}" class="btn btn-light btn-sm pull-right" data-toggle="tooltip" data-placement="left" title="@lang('usersmanagement.tooltips.back-users')">
                  <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
                  @lang('usersmanagement.buttons.back-to-users')
                </a>
			</div>
            <!-- /.box-header -->
            <div class="box-body">
				 @if ($user->name)
					<strong>{{ trans('usersmanagement.labelUserName') }}</strong>
					<span class="text-muted">{{ $user->name }}</span>
					<hr style="margin: 8px 0px;">
				 @endif
				 @if ($user->email)
					<strong>{{ trans('usersmanagement.labelEmail') }}</strong>
					<span class="text-muted" data-toggle="tooltip" data-placement="top" title="@lang('usersmanagement.tooltips.email-user', ['user' => $user->email])">
						{{ HTML::mailto($user->email, $user->email) }}
					</span>
					<hr style="margin: 8px 0px;">
				 @endif
				 @if ($user->first_name)
					<strong>{{ trans('usersmanagement.labelFirstName') }}</strong>
					<span class="text-muted">{{ $user->first_name }}</span>
					<hr style="margin: 8px 0px;">
				 @endif
				 @if ($user->last_name)
					<strong>{{ trans('usersmanagement.labelLastName') }}</strong>
					<span class="text-muted">{{ $user->last_name }}</span>
					<hr style="margin: 8px 0px;">
				 @endif
				<strong>{{ trans('usersmanagement.labelRole') }}</strong>
					@foreach ($user->roles as $user_role)

						@if ($user_role->name == 'User')
						  @php $badgeClass = 'primary' @endphp

						@elseif ($user_role->name == 'Admin')
						  @php $badgeClass = 'warning' @endphp

						@elseif ($user_role->name == 'Unverified')
						  @php $badgeClass = 'danger' @endphp

						@else
						  @php $badgeClass = 'default' @endphp

						@endif
						<span class="badge badge-{{$badgeClass}}">{{ $user_role->name }}</span>
					@endforeach
				<hr style="margin: 8px 0px;">
				<strong>{{ trans('usersmanagement.labelStatus') }}</strong>
				@if ($user->activated == 1)
				<span class="badge badge-success">
				  Activated
				</span>
				@else
				<span class="badge badge-danger">
				  Not-Activated
				</span>
				@endif
				<hr style="margin: 8px 0px;">
				<strong>{{ trans('usersmanagement.labelAccessLevel')}} {{ $levelAmount }}:</strong>
				@if($user->level() >= 5)
					<span class="badge badge-primary margin-half margin-left-0">5</span>
				@endif
				@if($user->level() >= 4)
					<span class="badge badge-info margin-half margin-left-0">4</span>
				@endif
				@if($user->level() >= 3)
					<span class="badge badge-success margin-half margin-left-0">3</span>
				@endif
				@if($user->level() >= 2)
					<span class="badge badge-warning margin-half margin-left-0">2</span>
				@endif
				@if($user->level() >= 1)
					<span class="badge badge-default margin-half margin-left-0">1</span>
				@endif
				<hr style="margin: 8px 0px;">
				<strong>{{ trans('usersmanagement.labelPermissions') }}</strong>
				@if($user->canViewUsers())
				<span class="badge badge-primary margin-half margin-left-0">
				  {{ trans('permsandroles.permissionView') }}
				</span>
				@endif
				@if($user->canCreateUsers())
				<span class="badge badge-info margin-half margin-left-0">
				  {{ trans('permsandroles.permissionCreate') }}
				</span>
				@endif
				@if($user->canEditUsers())
				<span class="badge badge-warning margin-half margin-left-0">
				  {{ trans('permsandroles.permissionEdit') }}
				</span>
				@endif
				@if($user->canDeleteUsers())
				<span class="badge badge-danger margin-half margin-left-0">
				  {{ trans('permsandroles.permissionDelete') }}
				</span>
				@endif
              <hr style="margin: 8px 0px;">
			  @if ($user->created_at)
				  <strong>{{ trans('usersmanagement.labelCreatedAt') }}</strong>
				  <span>{{ $user->created_at }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->updated_at)
				  <strong>{{ trans('usersmanagement.labelUpdatedAt') }}</strong>
				  <span>{{ $user->updated_at }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->signup_ip_address)
				  <strong>{{ trans('usersmanagement.labelIpEmail') }}/strong>
				  <span>{{ $user->signup_ip_address }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->signup_confirmation_ip_address)
				  <strong>{{ trans('usersmanagement.labelIpConfirm') }}</strong>
				  <span>{{ $user->signup_confirmation_ip_address }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->signup_sm_ip_address)
				  <strong>{{ trans('usersmanagement.labelIpSocial') }}</strong>
				  <span>{{ $user->signup_sm_ip_address }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->admin_ip_address)
				  <strong>{{ trans('usersmanagement.labelIpAdmin') }}</strong>
				  <span>{{ $user->admin_ip_address }}</span>
				  <hr style="margin: 8px 0px;">
			  @endif
			  @if ($user->updated_ip_address)
				  <strong>{{ trans('usersmanagement.labelIpUpdate') }}</strong>
				  <span>{{ $user->updated_ip_address }}</span>
			  @endif
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		</div>
	</div>
</section>
  @include('modals.modal-delete')

@endsection

@section('footer_scripts')
  @include('scripts.delete-modal-script')
  @if(config('usersmanagement.tooltipsEnabled'))
    @include('scripts.tooltips')
  @endif
@endsection
