<header class="main-header">

    <!-- Logo -->

    @if(Auth::user()->isAdmin())

    <a href="{{ url('/admin/home') }}" class="logo">

    @endif

    @if(Auth::user()->isvendor())

    <a href="{{ url('/vendor/home') }}" class="logo">

    @endif

      <!-- mini logo for sidebar mini 50x50 pixels -->

      <span class="logo-mini"></span>

      <!-- logo for regular state and mobile devices -->

      <span class="logo-lg"><img style="height: 48px;" src="{{ URL::to('public/images/noyta-Image.png') }}"></span>

    </a>

    <!-- Header Navbar: style can be found in header.less -->

    <nav class="navbar navbar-static-top">

      <!-- Sidebar toggle button-->

      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">

        <span class="sr-only">Toggle navigation</span>

      </a>



      <div class="navbar-custom-menu">

        <ul class="nav navbar-nav">

          

          <!-- User Account: style can be found in dropdown.less -->

          <li class="dropdown user user-menu">

            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

			@if ((Auth::User()->profile) && Auth::user()->profile->avatar_status == 1)

				<img src="{{ Auth::user()->profile->avatar }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav user-image">

			@else

				<img src="{{ Gravatar::get(Auth::user()->email) }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav user-image">

			@endif

              <span class="hidden-xs">{{ Auth::user()->first_name }}</span>

            </a>

            <ul class="dropdown-menu">

              <!-- User image -->

              <li class="user-header">

				@if ((Auth::User()->profile) && Auth::user()->profile->avatar_status == 1)

					<img src="{{ Auth::user()->profile->avatar }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav img-circle">

				@else

					<img src="{{ Gravatar::get(Auth::user()->email) }}" alt="{{ Auth::user()->name }}" class="user-avatar-nav img-circle">

				@endif
                <p>

                  {{ Auth::user()->name }}

                </p>

              </li>

              <!-- Menu Footer-->

              <li class="user-footer">

                <div class="pull-left">

                  @if(Auth::user()->isvendor())

					<a class="dropdown-item btn btn-default btn-flat {{ Request::is('profile/'.Auth::user()->name, 'profile/'.Auth::user()->name . '/edit') ? 'active' : null }}" href="{{ URL::to('/vendor/profile') }} ">

						@lang('titles.profile')

					</a>

          @elseif(Auth::user()->isAdmin())

          <a class="dropdown-item btn btn-default btn-flat {{ Request::is('profile/'.Auth::user()->name, 'profile/'.Auth::user()->name . '/edit') ? 'active' : null }}" href="{{ URL::to('/admin/profile') }} ">

            @lang('titles.profile')

          </a>

          @endif

                </div>

                <div class="pull-right">

				<a class="dropdown-item btn btn-default btn-flat" href="{{ route('logout') }}"

					   onclick="event.preventDefault();

									 document.getElementById('logout-form').submit();">

						{{ __('Logout') }}

					</a>

					<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">

						@csrf

					</form>

                </div>

              </li>

            </ul>

          </li>

        </ul>

      </div>

    </nav>

  </header>