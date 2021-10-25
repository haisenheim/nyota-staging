<!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          {{--<li class="{{ Request::is('admin/home') ? 'active' : null }}">
            <a class="" href="{{ url('/admin/home') }}">
              <i class="fa fa-dashboard"></i>
              <span>@lang('titles.dashboard')</span>
            </a>
          </li>--}}
          @if(Auth::user()->isAdmin())
          <li class="{{ Request::is('admin/home') ? 'active' : null }}">
            <a class="" href="{{ url('/admin/home') }}">
              <i class="fa fa-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="@if(Request::is('admin/user/*') || Request::is('admin/user'))active @endif">
            <a class="" href="{{ url('/admin/user') }}">
              <i class="fa fa-users"></i>
              <span>Users</span>
            </a>
          </li>
          <li class="@if(Request::is('admin/vendors/*') || Request::is('admin/vendors'))active @endif">
            <a class="" href="{{ url('/admin/vendors') }}">
              <i class="fa fa-user-secret"></i>
              <span>Vendors</span>
            </a>
          </li>
          <li class="@if(Request::is('admin/categories/*') || Request::is('admin/categories'))active @endif">
            <a class="" href="{{ url('/admin/categories') }}">
              <i class="fa fa-list-alt"></i>
              <span>Categories</span>
            </a>
          </li>
          <li class="@if(Request::is('admin/banner/*') || Request::is('admin/banner'))active @endif">
            <a class="" href="{{ url('/admin/banner') }}">
              <i class="fa fa-picture-o"></i>
              <span>Banners</span>
            </a>
          </li>
          <li class="treeview @if(Request::is('admin/product_attribute/*') || Request::is('admin/product_attribute') || Request::is('admin/product_attribute/*') || Request::is('admin/product_attribute')) menu-open @endif">
          	<a href="#">
	            <i class="fa fa-pie-chart"></i>
	            <span>Product Attribute</span>
	            <span class="pull-right-container">
	              <i class="fa fa-angle-left pull-right"></i>
	            </span>
          	</a>
          	<ul class="treeview-menu" style="@if(Request::is('admin/product_attribute_type/*') || Request::is('admin/product_attribute_type') || Request::is('admin/product_attribute/*') || Request::is('admin/product_attribute')) display:block; @endif">
          	<li class="@if(Request::is('admin/product_attribute_type/*') || Request::is('admin/product_attribute_type')) active @endif"><a class="" href="{{ url('/admin/product_attribute_type') }}">
              <i class="fa fa-sort-amount-asc"></i>
              <span>Attribute Types</span>
            </a></li>
            <li class="@if(Request::is('admin/product_attribute/*') || Request::is('admin/product_attribute')) active @endif"><a class="" href="{{ url('/admin/product_attribute') }}">
              <i class="fa fa-sort-amount-desc"></i>
              <span>Attributes</span>
            </a></li>
            </ul>
        </li>
		
		    <li class="@if(Request::is('admin/products/*') || Request::is('admin/products'))active @endif">
          <a class="" href="{{ url('/admin/products') }}">
            <i class="fa fa-product-hunt"></i>
            <span>Products</span>
          </a>
        </li>

        <li class="@if(Request::is('admin/pages/*') || Request::is('admin/pages'))active @endif">
          <a class="" href="{{ url('/admin/pages') }}">
            <i class="fa fa-file"></i>
            <span>Pages</span>
          </a>
        </li>
        
        <li class="@if(Request::is('admin/cart/*') || Request::is('admin/cart'))active @endif">
          
          <a class="" href="{{ url('/admin/cart') }}">
            <i class="fa fa-shopping-cart"></i>
            <span>Cart</span>
          </a>
        </li>

        <li class="@if(Request::is('admin/neighbourhood/*') || Request::is('admin/neighbourhood'))active @endif">
          <a class="" href="{{ url('/admin/neighbourhood') }}">
            <i class="fa fa-address-card"></i>
            <span>Neighbourhood</span>
          </a>
        </li>

		{{--<li class="treeview">
          	<a href="#">
	            <i class="fa fa-globe"></i>
	            <span>Delivery Location</span>
	            <span class="pull-right-container">
	              <i class="fa fa-angle-left pull-right"></i>
	            </span>
          	</a>
          	<ul class="treeview-menu">
          	<li><a class="" href="{{ url('/admin/state') }}">
              <i class="fa fa-location-arrow"></i>
              <span>State</span>
            </a></li>
            <li><a class="" href="{{ url('/admin/district') }}">
              <i class="fa fa-location-arrow"></i>
              <span>District</span>
            </a></li> --}}
			<li><a class="" href="{{ url('/admin/city') }}">
              <i class="fa fa-location-arrow"></i>
              <span>City</span>
            </a></li>


<li><a class="" href="{{ url('/admin/shipping') }}">
              <i class="fa fa-usd"></i>
              <span>Shipping Price</span>
            </a></li>

			{{--<li><a class="" href="{{ url('/admin/location') }}">
              <i class="fa fa-map-marker"></i>
              <span>Location</span>
            </a></li>
			
            </ul>
        </li>--}}
		
		<li class="@if(Request::is('admin/faq/*') || Request::is('admin/faq'))active @endif">
          <a class="" href="{{ url('/admin/faq') }}">
            <i class="fa fa-quora"></i>
            <span>FAQ</span>
          </a>
        </li>
        <li class="@if(Request::is('admin/notification/*') || Request::is('admin/notification'))active @endif">
          <a class="" href="{{ url('/admin/notification') }}">
            <i class="fa fa-bell"></i>
            <span>Notification</span>
          </a>
        </li>
        <li class="@if(Request::is('admin/order/*') || Request::is('admin/order'))active @endif">
          <a class="" href="{{ url('/admin/order') }}">
            <i class="fa fa-bars"></i>
            <span>Current Order</span>
          </a>
        </li>
        <li class="@if(Request::is('admin/orderhistory/*') || Request::is('admin/orderhistory'))active @endif">
          <a class="" href="{{ url('/admin/orderhistory') }}">
            <i class="fa fa-history"></i>
            <span>Order History</span>
          </a>
        </li>
        <li class="@if(Request::is('admin/settings/*') || Request::is('admin/settings'))active @endif">
          <a class="" href="{{ url('/admin/settings') }}">
            <i class="fa fa-cog"></i>
            <span>Setting</span>
          </a>
        </li>
         @endif
          @if(Auth::user()->isvendor())
          <li class="@if(Request::is('vendor/home/*') || Request::is('vendor/home'))active @endif">
            <a class="" href="{{ url('/vendor/home') }}">
              <i class="fa fa-dashboard"></i>
              <span>Dashboard</span>
            </a>
          </li>
         <li class="@if(Request::is('vendor/product/*') || Request::is('vendor/product'))active @endif">
            <a class="" href="{{ url('/vendor/product') }}">
              <i class="fa fa-product-hunt"></i>
              <span>Products</span>
            </a>
          </li>
          <li class="@if(Request::is('vendor/cart/*') || Request::is('vendor/cart'))active @endif">
          
          <a class="" href="{{ url('/vendor/cart') }}">
            <i class="fa fa-shopping-cart"></i>
            <span>Cart</span>
          </a>
        </li>
          <li class="@if(Request::is('vendor/order/*') || Request::is('vendor/order'))active @endif">
          <a class="" href="{{ url('/vendor/order') }}">
            <i class="fa fa-bars"></i>
            <span>Current Order</span>
          </a>
        </li>
        <li class="@if(Request::is('vendor/orderhistory/*') || Request::is('vendor/orderhistory'))active @endif">
          <a class="" href="{{ url('/vendor/orderhistory') }}">
            <i class="fa fa-history"></i>
            <span>Order History</span>
          </a>
        </li>
          @endif
          {{--<li class="">
            <a class="" href="{{ url('/admin/users') }}">
              <i class="fa fa-users"></i>
              <span>@lang('titles.adminUserList')</span>
            </a>
          </li>
       <li class="treeview">
          <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Charts</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="pages/charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a></li>
            <li><a href="pages/charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a></li>
            <li><a href="pages/charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a></li>
            <li><a href="pages/charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a></li>
          </ul>
		  </li>--}}
		 @if(Auth::user()->isAdmin())
			<li class="">
				<a class="" href="https://dashboard.tawk.to/" target="_blank">
					<i class="fa fa-comments" aria-hidden="true"></i>
					<span>www.tawk.to</span>
				</a>
			</li>

		 @endif	
			 
		  
		  
        </ul>
      <!-- /.sidebar-menu -->