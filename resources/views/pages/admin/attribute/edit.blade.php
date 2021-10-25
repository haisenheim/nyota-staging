@extends('layouts.admin.app')
@section('template_title')
   Attribute
@endsection
@section('content')
<section class="content-header" >
    <h1>Attribute</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
          	<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/product_attribute') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
           
            {!! Form::open(array('route' => ['product_attribute.update', $attribute->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', $attribute->name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
					{!! Form::label('attribute_type', 'Attribute Type', array('class' => 'control-label')); !!}
					<select class="form-control" name="attribute_type" id="attribute_type">
                      <option value="">Select Attribute Type</option>
                      @if ($attributes_types)
                        @foreach($attributes_types as $attributes_type)
                          <option value="{{ $attributes_type->id }}" {{ $attributes_type->id == $attribute->type_id ? 'selected="selected"' : '' }}>{{ $attributes_type->name }}</option>
                        @endforeach
                      @endif
                    </select>
						@if ($errors->has('attribute_type'))
							<span class="help-block">
								<strong>{{ $errors->first('attribute_type') }}</strong>
							</span>
						@endif
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
