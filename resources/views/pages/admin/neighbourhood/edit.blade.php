@extends('layouts.admin.app')
@section('template_title')
   Neighbourhood
@endsection
@section('content')
<section class="content-header" >
    <h1>Neighbourhood</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
          	<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/neighbourhood') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
           
            {!! Form::open(array('route' => ['neighbourhood.update', $neighbourhood->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="col-lg-12 col-md-12 col-sm-12">
        					<div class="form-group col-lg-6 col-md-6 col-sm-6">
        						{!! Form::label('neighbourhood', 'Neighbourhood', array('class' => 'control-label')); !!}
        						{!! Form::text('neighbourhood', $neighbourhood->neighbour_hood, array('id' => 'neighbourhood', 'class' => 'form-control', 'placeholder' => '')) !!}
        						@if ($errors->has('neighbourhood'))
        							<span class="help-block">
        								<strong>{{ $errors->first('neighbourhood') }}</strong>
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
