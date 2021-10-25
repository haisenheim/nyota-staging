	<!-- Main Sidebar Container -->
  <aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
		<div class="pull-left image">
		@if ((Auth::User()->profile) && Auth::user()->profile->avatar_status == 1)
			<img src="{{ Auth::user()->profile->avatar }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav img-circle">
		@else
			<img src="{{ Gravatar::get(Auth::user()->email) }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav img-circle">
		@endif
		</div>
		<div class="pull-left info">
		  <p>{{ Auth::user()->first_name }}</p>
		  <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		</div>
		</div>
		<!-- search form -->
		<!-- <form action="#" method="get" class="sidebar-form">
		<div class="input-group">
		  <input type="text" name="q" class="form-control" placeholder="Search...">
		  <span class="input-group-btn">
				<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
				</button>
			  </span>
		</div>
		</form> -->
		@include('partials.admin.sidebar_nav')
    <!-- /.sidebar -->
	</section>
  </aside>