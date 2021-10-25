@extends('layouts.admin.app')

@section('template_title')
    @lang('usersmanagement.showing-all-users')
@endsection

@section('template_linked_css')
    @if(config('laravelusers.enabledDatatablesJs'))
        <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.datatablesCssCDN') }}">
    @endif
    <style type="text/css" media="screen">
        .users-table {
            border: 0;
        }
        .users-table tr td:first-child {
            padding-left: 15px;
        }
        .users-table tr td:last-child {
            padding-right: 15px;
        }
        .users-table.table-responsive,
        .users-table.table-responsive table {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
        <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">@lang('usersmanagement.showing-all-users')</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
			
                <div class="card">
					<div class="row">
						<div class="box-tools pull-left col-lg-2">
						<a class="btn btn-block btn-primary" href="{{ URL::to('admin/users/create') }}">
							<i class="fa fa-fw fa-user-plus" aria-hidden="true"></i>
							@lang('usersmanagement.buttons.create-new')
						</a>
						</div>
						<div class="col-lg-10">
							@if(config('usersmanagement.enableSearchUsers'))
								@include('partials.search-users-form')
							@endif
						</div>
					</div>
                    <div class="card-body">
                        <div class="table-responsive users-table">
                            <table class="table table-striped table-sm data-table">
                                <caption id="user_count">
                                    {{ trans_choice('usersmanagement.users-table.caption', 1, ['userscount' => $users->count()]) }}
                                </caption>
                                <thead class="thead">
                                    <tr>
                                        <th>@lang('usersmanagement.users-table.id')</th>
                                        <th>@lang('usersmanagement.users-table.name')</th>
                                        <th class="hidden-xs">@lang('usersmanagement.users-table.email')</th>
                                        <th class="hidden-xs">@lang('usersmanagement.users-table.fname')</th>
                                        <th class="hidden-xs">@lang('usersmanagement.users-table.lname')</th>
                                        <th>@lang('usersmanagement.users-table.role')</th>
                                        <th class="hidden-sm hidden-xs hidden-md">@lang('usersmanagement.users-table.created')</th>
                                        <th class="hidden-sm hidden-xs hidden-md">@lang('usersmanagement.users-table.updated')</th>
                                        <th>@lang('usersmanagement.users-table.actions')</th>
                                        <th class="no-search no-sort"></th>
                                        <th class="no-search no-sort"></th>
                                    </tr>
                                </thead>
                                <tbody id="users_table">
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->id}}</td>
                                            <td>{{$user->name}}</td>
                                            <td class="hidden-xs"><a href="mailto:{{ $user->email }}" title="email {{ $user->email }}">{{ $user->email }}</a></td>
                                            <td class="hidden-xs">{{$user->first_name}}</td>
                                            <td class="hidden-xs">{{$user->last_name}}</td>
                                            <td>
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
                                            </td>
                                            <td class="hidden-sm hidden-xs hidden-md">{{$user->created_at}}</td>
                                            <td class="hidden-sm hidden-xs hidden-md">{{$user->updated_at}}</td>
                                            <td>
                                                {!! Form::open(array('url' => 'admin/users/' . $user->id, 'class' => '', 'data-toggle' => 'tooltip', 'title' => 'Delete')) !!}
                                                    {!! Form::hidden('_method', 'DELETE') !!}
                                                    {!! Form::button(trans('usersmanagement.buttons.delete'), array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width: 100%;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete User', 'data-message' => 'Are you sure you want to delete this user ?')) !!}
                                                {!! Form::close() !!}
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-success btn-block" href="{{ URL::to('admin/users/' . $user->id) }}" data-toggle="tooltip" title="Show">
                                                    @lang('usersmanagement.buttons.show')
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-info btn-block" href="{{ URL::to('admin/users/' . $user->id . '/edit') }}" data-toggle="tooltip" title="Edit">
                                                    @lang('usersmanagement.buttons.edit')
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tbody id="search_results"></tbody>
                                @if(config('usersmanagement.enableSearchUsers'))
                                    <tbody id="search_results"></tbody>
                                @endif

                            </table>

                            @if(config('usersmanagement.enablePagination'))
                                {{ $users->links() }}
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @include('modals.modal-delete')

@endsection

@section('footer_scripts')
    @if ((count($users) > config('usersmanagement.datatablesJsStartCount')) && config('usersmanagement.enabledDatatablesJs'))
        @include('scripts.datatables')
    @endif
    @include('scripts.delete-modal-script')
    @include('scripts.save-modal-script')
    @if(config('usersmanagement.tooltipsEnabled'))
        @include('scripts.tooltips')
    @endif
    @if(config('usersmanagement.enableSearchUsers'))
        @include('scripts.search-users')
    @endif
@endsection
