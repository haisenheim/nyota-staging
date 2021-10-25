<!-- Content Header (Page header) -->
    <section class="content-header content-head">
		 <h1>@if (Request::is('admin/home'))
				@lang('titles.dashboard')
			@elseif (Request::is('vendor/home'))
				@lang('titles.dashboard')	
			@elseif (Request::is('admin/users'))
				@lang('titles.adminUserList')
			@elseif (Request::is('admin/users/create'))
				@lang('usersmanagement.create-new-user')
			@elseif (Request::is('admin/users/*'))
				@lang('usersmanagement.editing-user', ['name' => $user->name])
			@endif</h1>
	</section>