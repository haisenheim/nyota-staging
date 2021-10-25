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
          <!-- general form elements -->
          <div class="box box-primary">
          	<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/categories') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
           
            {!! Form::open(array('route' => ['categories.update', $category->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', $category->name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
					{!! Form::label('parent_category', 'Parent Category', array('class' => 'control-label')); !!}
					<select class="form-control" name="parent_category" id="parent_category">
                      <option value="">Select Category</option>
                      @if ($categories)
                        @foreach($categories as $cate)
                          <option value="{{ $cate->id }}" {{ $cate->id == $category->parent_id ? 'selected="selected"' : '' }}>{{ $cate->name }}</option>
                        @endforeach
                      @endif
                    </select>
						@if ($errors->has('parent_category'))
							<span class="help-block">
								<strong>{{ $errors->first('parent_category') }}</strong>
							</span>
						@endif
					</div>
				</div>	
				
				<div class="col-lg-12 col-md-12 col-sm-12">	
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('short_description', 'Short Description', array('class' => 'control-label')); !!}
						{!! Form::textarea('short_description', $category->short_description, array('id' => 'short_description', 'rows' => 4, 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('short_description'))
							<span class="help-block">
								<strong>{{ $errors->first('short_description') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('full_description', 'Full Description', array('class' => 'control-label')); !!}
						{!! Form::textarea('full_description',$category->full_description, array('id' => 'full_description', 'rows' => 4, 'class' => 'form-control ', 'placeholder' => '')) !!}
						@if ($errors->has('full_description'))
						<span class="help-block">
							<strong>{{ $errors->first('full_description') }}</strong>
						</span>
						@endif
                	</div>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						<label for="exampleInputFile">Image</label><span class="help-comment-box">(Upload max size 2MB and type jpeg,jpg,png)</span>
	                    <div class="input-group">
						  <input type="file" class="custom-file-input" name="image">
						  @if(!empty($category->image))
						  <img src="{{url('/public/category_images')}}/{{$category->image}}" alt="products" class="img-responsive">
	                      @endif  
	                    </div>
						 <span class="help-block">
                        <strong>{{ $errors->first('image') }}</strong>
                      </span>
                  	</div>
				</div>
			</div>
              <!-- /.box-body -->

              <div class="box-footer">
                {!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
              </div>
            {!! Form::close() !!}
       
        
           </div>
          <!-- /.box -->
		</div>
	</div>
@endsection
@section('footer_scripts')

<script>
$(function () {
     $('input[type="file"]').change(function () {
          if ($(this).val() != "") {
                 $(this).css('color', '#333');
          }else{
                 $(this).css('color', 'transparent');
          }
     });
})
</script>
@endsection
