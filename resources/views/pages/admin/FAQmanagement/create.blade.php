@extends('layouts.admin.app')
@section('template_title')
   FAQ
@endsection
@section('content')
<section class="content-header" >
    <h1>FAQ</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
				<h3 class="box-title">Create</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/faq') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
             @if(Session::has('message'))
              <div class="col-md-12">
                <p class="alert alert-danger" style="padding: 2px;">{{ Session::get('message') }}</p>
              </div>
            @endif
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(array('route' => 'faq.store', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation' ,'files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	
				<div class="col-lg-12 col-md-12 col-sm-12">
					
					<div class="form-group">
						{!! Form::label('question', 'Question', array('class' => 'control-label')); !!}
						{!! Form::text('question', NULL, array('id' => 'question', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('question'))
							<span class="help-block">
								<strong>{{ $errors->first('question') }}</strong>
							</span>
						@endif
					</div>
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group">
						{!! Form::label('answer', 'Answer', array('class' => 'control-label')); !!}
						{!! Form::textarea('answer',NULL, array('id' => 'summernote', 'rows' => 4, 'class' => 'form-control summernote', 'placeholder' => '')) !!}
						@if ($errors->has('answer'))
						<span class="help-block">
							<strong>{{ $errors->first('answer') }}</strong>
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

