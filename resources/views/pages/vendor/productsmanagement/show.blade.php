@extends('layouts.admin.app')



@section('template_title')

Products

@endsection

@if(config('laravelusers.enabledDatatablesJs'))

  <link rel="stylesheet" type="text/css" href="{{ config('laravelusers.datatablesCssCDN') }}">

@endif

@section('content')

<section class="content-header" >

  <h1>Products</h1>

</section>

<div class="row">

  <div class="col-lg-12">

    <div class="box box-primary">

      <div class="box-header with-border">

        <h3 class="box-title">List</h3>

      </div> 

      <div class="row row-box">
        <div class="alert alert-danger" role="alert" style="display: none; margin-left: 8px;">
          <ul class="error_alert"></ul>
        </div>
        <div class="box-tools pull-left col-lg-2">  

          <a class="btn btn-primary btn-sm" href="{{ URL::to('/vendor/product/create') }}">

          Create

          </a>

        </div>

        

       <!--  @if (!empty($failures))

        <div class="alert alert-danger" role="alert">

          <strong>Errors:</strong>

            <ul>

              @foreach ($failures as $failure)

                @foreach ($failure->errors() as $error)

                  <li>{{ $error }}</li>

                @endforeach

              @endforeach

            </ul>

        </div>

        @endif -->

      

      <div class="col-lg-4">

        <div class="row">

          <form action="{{ route('vendor-blukproduct') }}" id="importproduct" method="POST" enctype="multipart/form-data">

            @csrf



            <input type="file" name="upload_file" style="display: inline-block; width: 50%">

           

            @if ($errors->has('upload_file'))

              <span class="help-block">

                <strong>{{ $errors->first('upload_file') }}</strong>

              </span>

            @endif

            <button class="btn btn-success" type="submit" style="display: inline-block;">import</button>
            <a href="{{URL::to('storage/Book1csv.csv')}}" download>Sample file</a>
          </form>

        </div>

      </div>



        <div class="col-lg-6">

          <div class="row">

            {!! Form::open(['route' => 'search-products-vendor','method' => 'get', 'role' => 'form', 'class' => 'needs-validation', 'id' => 'search_pages']) !!}

            {!! csrf_field() !!}

            <div class="input-group-append pull-right">

              <a href="#" class="input-group-addon btn btn-warning clear-search" style="display:none;">

                <i class="fa fa-fw fa-times" aria-hidden="true"></i>

                <span class="sr-only">

                @lang('usersmanagement.tooltips.clear-search')

                </span> 

              </a>

              <button class="input-group-addon btn btn-secondary" id="search" data-placement="bottom" ><i class="fa fa-search fa-fw" aria-hidden="true"></i>

                <span class="sr-only">

                {{  trans('usersmanagement.tooltips.submit-search') }}

                </span>

              </button>

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

                  <th>Category</th>

                  <th>SKU</th>

                  <th>Status</th>

                  <th>Action</th>

                  </tr>

                </thead>

                <tbody>

                  @foreach($products as $product)

                  <tr>

                  <td>{{$product->name}}</td>

                  <td>{{$product->category}}</td>

                  <td>{{$product->sku}}</td>

                  <td>@if($product->is_active == 0)

                  Active

                  @else 

                  Deactive

                  @endif</td>

                  <td>

                  {!! Form::open(array('url' => 'vendor/product/' . $product->id, 'class' => '', 'data-toggle' => 'tooltip')) !!}

                  {!! Form::hidden('_method', 'DELETE') !!}

                  {!! Form::button('<i class="fa fa-trash-o"></i>', array('class' => 'btn btn-danger btn-sm','type' => 'button', 'style' =>'width:auto; float:left;margin-right:5px;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete Product', 'data-message' => 'Are you sure you want to delete this product ?')) !!}

                  {!! Form::close() !!}

                  <a class="btn btn-sm btn-info" href="{{ URL::to('/vendor/product/' . $product->id . '/edit') }}" data-toggle="tooltip">

                  <i class="fa fa-edit"></i>

                  </a>

                  <a class="btn btn-sm btn-primary" href="{{ URL::to('/vendor/product/' . $product->id ) }}" data-toggle="tooltip"><i class="fa fa-eye"></i></a>

                  <a class="btn btn-sm btn-warning" href="{{ URL::to('/vendor/products/addcomment/' . $product->id ) }}" data-toggle="tooltip">Comment</a>

                  </td>

                  </tr>

                  @endforeach

                </tbody>

              </table>

              {{ $products->appends(Request::except('page'))->links() }}

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

  jQuery(document).ready(function(){

  $( "#importproduct" ).submit(function( event ) {

  event.preventDefault();

    var form = $(this);

    var data = new FormData($(this)[0]);

    var url = form.attr("action");

    



     $.ajax({

        type: form.attr('method'),

        url: url,

        data: data,

        cache: false,

        contentType: false,

        processData: false,

        success: function (data) {

          

          $('.is-invalid').removeClass('is-invalid');

          if (data.fail) {

            for (control in data) {

              console.log(data.errors);

              $('.alert').show();

              $('.alert').text(data.errors).after("</br>");

            }

          }

          else {

              //console.log(data.success);

             var url = "{{ URL::to('/vendor/product') }}";

             $(location).attr('href', url); 

            }

        },

        error: function (xhr, textStatus, errorThrown) {

          alert("Error: " + errorThrown);

        }

    });

   return false;

  });

});

</script>





@endsection