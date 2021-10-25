@extends('layouts.admin.app')
@section('template_title')
   Page
@endsection
@section('content')

<section class="content-header" >
    <h1>Page</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
          	<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/pages') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
           
            {!! Form::open(array('route' => ['pages.update', $page->slug], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group">
						{!! Form::label('title', 'Title', array('class' => 'control-label')); !!}
						{!! Form::text('title', $page->title, array('id' => 'title', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('title'))
							<span class="help-block">
								<strong>{{ $errors->first('title') }}</strong>
							</span>
						@endif
					</div>
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group">
						{!! Form::label('contain', 'Contain', array('class' => 'control-label')); !!}
						{!! Form::textarea('contain',$page->contain, array('id' => 'contain', 'rows' => 4, 'class' => 'form-control summernote', 'placeholder' => '')) !!}
						@if ($errors->has('contain'))
						<span class="help-block">
							<strong>{{ $errors->first('contain') }}</strong>
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

