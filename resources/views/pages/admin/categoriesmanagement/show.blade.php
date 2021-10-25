@extends('layouts.admin.app')



@section('template_title')

   Categories

@endsection

@if(config('laravelusers.enabledDatatablesJs'))

        <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.datatablesCssCDN') }}">

        

    @endif

@section('content')

<section class="content-header" >

    <h1>Categories</h1>

</section>

<div class="row">

  <div class="col-lg-12">

    <div class="box box-primary">

      <div class="box-header with-border">

          <h3 class="box-title">List</h3>

      </div> 

      <div class="row row-box">

        <div class="box-tools pull-left col-lg-2">  

        <a class="btn btn-primary btn-sm" href="{{ URL::to('/admin/categories/create') }}">

          Create

        </a>

      </div>

      <div class="col-lg-10">

              <div class="row">

                {!! Form::open(['route' => 'search-categories','method' => 'get', 'role' => 'form', 'class' => 'needs-validation', 'id' => 'search_pages']) !!}

                 {!! csrf_field() !!}

                    <div class="input-group-append pull-right">

                      <a href="#" class="input-group-addon btn btn-warning clear-search" style="display:none;">

                        <i class="fa fa-fw fa-times" aria-hidden="true"></i>

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

                    <button class="input-group-addon btn btn-secondary" id="search" data-placement="bottom" ><i class="fa fa-search fa-fw" aria-hidden="true"></i>

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

                  <table id="datatable" class="table table-bordered table-striped data-table">

                    <thead>

                      <tr>

                          <th>Name</th>

                          <th>Category Type</th>

                          <th>Parent Category Name</th>

                          <th>Action</th>

                      </tr>

                    </thead>

                    <tbody>

                      @foreach($categories as $category)

                     	<tr>

                          <td>{{$category->name}}</td>

                          <td>@if($category->parent_id == 0)

                                Parent

                                @else

                                Child

                                @endif

                          </td>

                          <td>

                            {{$category->parentcategory_name}}

                          </td>

                          <td>

                            <!-- <a class="btn btn-sm btn-danger delete_cat" id="delete" href="#" data-toggle="tooltip" title="Delete" data-delete-id="{{$category->id}}">

                                                    <i class="fa fa-trash-o fa-fw" aria-hidden="true"></i>

                                                </a> -->

                            {!! Form::open(array('url' => 'admin/categories/' . $category->id, 'class' => '', 'data-toggle' => 'tooltip')) !!}

                            {!! Form::hidden('_method', 'DELETE') !!}

                            {!! Form::hidden('category_id', $category->id, array('id' => 'category_id')) !!}

                            {!! Form::button('<i class="fa fa-trash-o"></i>', array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width:auto; float:left;margin-right:5px;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete Category', 'data-message' => 'Are you sure you want to delete this category ?')) !!}

                            {!! Form::close() !!}

                            <a class="btn btn-sm btn-info" href="{{ URL::to('/admin/categories/' . $category->id . '/edit') }}" data-toggle="tooltip">

                            <i class="fa fa-edit"></i>

                            </a>

                            <a class="btn btn-sm btn-primary" href="{{ URL::to('/admin/categories/' . $category->id ) }}" data-toggle="tooltip"><i class="fa fa-eye"></i></a>

                          </td>

                      </tr>

                      @endforeach

                    </tbody>

                     

		              </table>

                  {{ $categories->links() }}

              </div>

            </div>

          </div>

      </div>

    </div>

  </div>

</div>

<div class="modal fade modal-danger" id="messageDelete" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true" data-backdrop="static">

  <div class="modal-dialog" role="document">

      <div class="modal-content">

      <div class="modal-header"><!-- 

          <h5 class="modal-title">

          Delete

          </h5>

          

       -->

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

          <span class="sr-only">close</span>

        </button>

       </div>

      <div class="modal-body">

          <p></p>

      </div>

      <div class="modal-footer">

          {!! Form::button('Ok', array('class' => 'btn btn-danger pull-right remove', 'type' => 'button', 'data-dismiss' => 'modal')) !!}

      </div>

      </div>

  </div>

</div>

@include('modals.modal-delete')

@endsection

@section('footer_scripts')







<script type="text/javascript">

  var message_delete = '';

  // CONFIRMATION DELETE MODAL

  $('#confirmDelete').on('show.bs.modal', function (e) {

    //e.preventDefault();

    var message = $(e.relatedTarget).attr('data-message');

    var title = $(e.relatedTarget).attr('data-title');

    var form = $(e.relatedTarget).closest('form');

    $(this).find('.modal-body p').text(message);

    $(this).find('.modal-title').text(title);

    $(this).find('.modal-footer #confirm').data('form', form);

  });

  $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });

  $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){

      //e.preventDefault();

        var delete_id = $(this).data('form')[0].elements['category_id'].value;

        $.ajax({

            type:'POST',

            url: "{{ route('category-delete') }}",

            data: { cat_id : delete_id },

            success: function (result) {

                let jsonData = JSON.parse(result);

                if(jsonData.status) {

                    message_delete = 'Category Deleted Successfully';

                    $('#confirmDelete').modal('hide');

                    //$('#messageDelete').modal('show');

                    // alert('Delete Successfully');

                    location.reload(true);

                } else {

                    message_delete = 'Category can not be deleted because child category is exist so please delete child category first';

                    // $('#confirmDelete')

                    // .modal('hide')

                    // .on('hidden.bs.modal', function (e) {

                    //     $('#messageDelete').modal('show');



                    //     $(this).off('hidden.bs.modal'); // Remove the 'on' event binding

                    // });

                    $('#confirmDelete').modal('hide');

                    $('#messageDelete').modal('show');

                    //setTimeout(function(){ $('#messageDelete').modal('show'); }, 1000);
                  
 

                }

            },

            error: function (response, status, error) {

            }

        });

         return false;

    });



     $('#messageDelete').on('show.bs.modal', function (e) {

     var message = $(e.relatedTarget).attr('data-message');

      $(this).find('.modal-body p').text(message_delete);

    //e.preventDefault();

   });



</script>



@endsection