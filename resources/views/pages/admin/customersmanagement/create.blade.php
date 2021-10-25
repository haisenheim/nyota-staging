@extends('layouts.admin.app')
@section('template_title')
   User
@endsection
@section('content')
<section class="content-header" >
    <h1>User</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
				<h3 class="box-title">Create</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/user') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
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
            {!! Form::open(array('route' => 'user.store', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	
				<div class="col-lg-12 col-md-12 col-sm-12">
					
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', NULL, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('name'))
							<span class="help-block">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('email', 'Email', array('class' => 'control-label')); !!}
						{!! Form::text('email', NULL, array('id' => 'email', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('email'))
							<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
						@endif
					</div>
					
					
				</div>

				<div class="col-lg-12 col-md-12 col-sm-12">	
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('password', 'Password', array('class' => 'control-label')); !!}
						{!! Form::password('password', array('id' => 'password', 'class' => 'form-control ', 'placeholder' => '')) !!}
						@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
						@endif
                	</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('city', 'City', array('class' => 'control-label')); !!}
            <select class="form-control" name="city" id="city">
              <option value="">Select City</option>
              @foreach($citys as $city)
                <option value="{{ $city->id }}">{{ $city->name }}</option>
              @endforeach
            </select>
						@if ($errors->has('city'))
							<span class="help-block">
								<strong>{{ $errors->first('city') }}</strong>
							</span>
						@endif
					</div>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('phone', 'Phone Number', array('class' => 'control-label')); !!}
						{!! Form::text('phone', NULL, array('id' => 'phone', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if($errors->has('phone'))
							<span class="help-block">
								<strong>{{ $errors->first('phone') }}</strong>
							</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
	                    <label for="exampleInputFile">Image</label><span class="help-comment-box">(Upload max size 2MB and type jpeg,jpg,png)</span>
	                    <div class="input-group">
						  <div class="custom-file">
	                        <input type="file" class="custom-file-input" name="image">
	                      </div>
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