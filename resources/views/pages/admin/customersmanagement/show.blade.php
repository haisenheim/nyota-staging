@extends('layouts.admin.app')

@section('template_title')
   Users
@endsection
@section('content')
<section class="content-header" >
    <h1>Users</h1>
</section>
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">List</h3>
      </div>
      <div class="row row-box">
        <div class="box-tools pull-left col-lg-2">  
        <a class="btn btn-primary btn-sm" href="{{ URL::to('/admin/user/create') }}">
          Create
        </a>
      </div>
      <div class="col-lg-10">
              <div class="row">
                {!! Form::open(['route' => 'search-users','method' => 'get', 'role' => 'form', 'class' => 'needs-validation', 'id' => 'search_pages']) !!}
                 {!! csrf_field() !!}
                    <div class="input-group-append pull-right">
                      <a href="#" class="input-group-addon btn btn-warning clear-search" style="display:none;">
                        <i class="fa fa-fw fa-times fa-align" aria-hidden="true"></i>
                        <span class="sr-only">
                          @lang('usersmanagement.tooltips.clear-search')
                        </span> 
                      </a>
                      <!-- <a href="#" class="input-group-addon btn btn-secondary" id="search" data-toggle="tooltip" data-placement="bottom" title="@lang('usersmanagement.tooltips.submit-search')" >
                        <i class="fa fa-search fa-fw" aria-hidden="true"></i>
                        <span class="sr-only">
                            {{  trans('usersmanagement.tooltips.submit-search') }}
                        </span>
                    </a> -->
                    <button class="input-group-addon btn btn-secondary search_box_right" id="search"  data-placement="bottom"  ><i class="fa fa-search fa-fw" aria-hidden="true"></i>
                        <span class="sr-only">
                            {{  trans('usersmanagement.tooltips.submit-search') }}
                        </span></button>
                      
                    </div>
                    <div class="pull-right">
                    {!! Form::text('search_box', NULL, ['id' => 'search_box', 'class' => 'form-control', 'placeholder' => 'Search', 'aria-label' => 'Search', 'required' => false]) !!}
                    </div>
                    
                {!! Form::close() !!}
              </div>
            </div>
      </div>
      <div class="box-body">
	       <div class="card">
            <div class="card-body">
              <div class="col-md-12 box-body table-responsive no-padding" id="users_table">
                  <table id="datatable" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>City</th>
                          <th>Status</th>
                          <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($customers as $customer)
                     	<tr>
                          <td>{{$customer->first_name}}</td>
                          <td>{{$customer->email}}</td>
                          <td>{{$customer->city}}</td>
			                    <td>
                            @if($customer->activated == 0)
                              Inactive
                            @else 
                              Active
                            @endif
                          </td>
                          <td>
                            {!! Form::open(array('url' => 'admin/user/' . $customer->id, 'class' => '', 'data-toggle' => 'tooltip')) !!}
                            {!! Form::hidden('_method', 'DELETE') !!}
                            {!! Form::button('<i class="fa fa-trash-o"></i>', array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width:auto; float:left;margin-right:5px;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete User', 'data-message' => 'Are you sure you want to delete this user ?')) !!}
                            {!! Form::close() !!}
                            <a class="btn btn-sm btn-info" href="{{ URL::to('/admin/user/' . $customer->id . '/edit') }}" data-toggle="tooltip">
                            <i class="fa fa-edit"></i>
                            </a>
                            @if($customer->activated == 1)
                            <a class="btn btn-sm btn-primary" href="{{route('unblock', ['id' => $customer->id])}}" data-toggle="tooltip">Inactive</a>
                            @else 
                            <a class="btn btn-sm btn-danger" href="{{route('block', ['id' => $customer->id])}}" data-toggle="tooltip">Active</a>
                            @endif
                          </td>
                      </tr>
                      @endforeach

                    </tbody>
		              </table>
                  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
                  {{ $customers->links() }}
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
@include('modals.modal-delete')
@endsection
@section('footer_scripts')
@include('scripts.delete-modal-script')
<script type="text/javascript">
        //$(document).ready(function() {
          // var searchform = $('#search_pages');
          // var userPagination = $('#user_pagination');
          //   $('#search').click(function() {
          //       var searchformInput = $('#search_box').val();
          //       $.ajax({
          //           type: 'POST',
          //           url: '{{ route("search-users") }}',
          //           data: searchform.serialize(),
          //           success:function(data) {
          //           //$('#users_table').html(data);
          //           $('tbody').html('');
          //           $('tbody').html(data);
          //           },
          //       });
          // });
    //});
//     $(document).ajaxComplete(function() {
//     $('.pagination li a').click(function(e) {
//         e.preventDefault();
//         var url = $(this).attr('href');
//         $.ajax({
//             url: url,
//             success: function(data) {
//                 $('#users_table').html(data);
//             }
//         });
//     });
// });
</script>
@endsection