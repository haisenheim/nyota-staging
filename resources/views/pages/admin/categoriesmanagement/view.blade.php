@extends('layouts.admin.app')
@section('template_title')
   Category
@endsection
@section('content')
<section class="content-header" >
    <h1>Category</h1>
</section>
<div class="row">
<div class="col-md-12">
  <div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Show</h3>
        <div class="pull-right">
          <a href="{{ url('/admin/categories') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
            <i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
            Back
          </a>
        </div>
            </div>
            <div class="box-body">
            	<div class="col-lg-12 col-md-12 col-sm-12">
            	<div class="col-lg-6 col-md-6 col-sm-6">
      			<h4 class="center">Category Details</h4>
		          <div class="table-responsive">
		            <table class="table">
		              <tbody><tr>
		                <th>Name:</th>
		                <td>{{$category->name}}</td>
		              </tr>
		             <tr>
		                <th>Short Description:</th>
		                <td>{{$category->short_description}}</td>
		              </tr>
		              <tr>
		                <th>Full Description:</th>
		                <td>{{$category->full_description}}</td>
		              </tr>
                  
                  <tr>
                    
                    @if($category->parentcategory_name)
                    <th>Parent Category:</th>
                    <td>{{$category->parentcategory_name}}</td>
                     @endif
                  </tr>
                 
		             </tbody></table>
		          </div>
         </div>
        <div class="form-group col-lg-6 col-md-6 col-sm-6 center">
        	<h4 class="">Category Image</h4>
           	@if($category->image)
            	<img src="{{ url('public/category_images')}}/{{$category->image}}" height="175px" width="175px" class="responsive img-align">
            @endif
            
        </div>
    	</div>
      </div>
        </div>
      </div>
    </div>
      @endsection