@extends('layouts.admin.app')

@section('template_title')
   Pages
@endsection
@if(config('laravelusers.enabledDatatablesJs'))
        
    @endif
@section('content')
<section class="content-header" >
    <h1>Pages</h1>
</section>
<div class="row">
  <div class="col-lg-12">
    <div class="box box-primary">
      <div class="box-header with-border">
          <h3 class="box-title">List</h3>
      </div> 
      <div class="box-body">
	       <div class="card">
            <div class="card-body">
              <div class="col-md-12 box-body table-responsive no-padding" id="users_table">
                  <table id="datatable" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Contain</th>
                        <th>Slug</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($pages as $page)
                     	<tr>
                          <td>{{$page->title}}</td>
                          <td>{{$page->contain}}</td>
                          <td>{{$page->slug}}</td>
                          <td>
                            <a class="btn btn-sm btn-info" href="{{ URL::to('/admin/pages/' . $page->slug . '/edit') }}" data-toggle="tooltip">
                            <i class="fa fa-edit"></i>
                            </a>
                          </td>
                      </tr>
                      @endforeach
                    </tbody>
                     
		              </table>
                  {{ $pages->links() }}
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
       // $(document).ready(function() {
          // var searchform = $('#search_pages');
          //   $('#search').click(function() {
          //       var searchformInput = $('#search_box').val();
          //       $.ajax({
          //           type: 'POST',
          //           url: '{{ route("search-vendors") }}',
          //           data: searchform.serialize(),
          //           success:function(data) {
          //            $('#users_table').html(data);
          //           },
          //       });
          // });
    //});
</script>

@endsection