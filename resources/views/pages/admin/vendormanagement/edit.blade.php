@extends('layouts.admin.app')
@section('template_title')
	Vendor
@endsection
@section('content')
<section class="content-header" >
	<h1>Vendor</h1>
</section>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/vendors') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
					<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
					Back
					</a>
				</div>
			</div>
			{!! Form::open(array('route' => ['vendors.update', $vendor->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
			{!! csrf_field() !!}
			<div class="box-body">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', $vendor->first_name, array('id' => 'name', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('name'))
						<span class="help-block">
							<strong>{{ $errors->first('name') }}</strong>
						</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('email', 'Email', array('class' => 'control-label')); !!}
						{!! Form::text('email', $vendor->email, array('id' => 'email', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
						@endif
					</div>
				</div>	
				<div class="col-lg-12 col-md-12 col-sm-12">	
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('phone', 'Phone', array('class' => 'control-label')); !!}
						{!! Form::text('phone', $vendor->phone, array('id' => 'phone', 'class' => 'form-control', 'placeholder' => '')) !!}
						@if($errors->has('phone'))
						<span class="help-block">
						<strong>{{ $errors->first('phone') }}</strong>
						</span>
						@endif
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('city', 'City', array('class' => 'control-label')); !!}
            <select class="form-control" name="city" id="city">
              <option value="">Select City</option>
              @foreach($citys as $city)
                <option value="{{ $city->id }}" {{ $city->id == $vendor->city ? 'selected="selected"' : ''}}>{{ $city->name }}</option>
              @endforeach
            </select>
						@if ($errors->has('city'))
						<span class="help-block">
							<strong>{{ $errors->first('city') }}</strong>
						</span>
						@endif
					</div>
				</div>
				<div id="" class="form-group col-lg-12 col-md-12 col-sm-12">
		          <div class="form-group col-lg-5 col-md-5 col-sm-5">
		            {!! Form::label('latitude', 'Latitude', array('class' => 'control-label')); !!}
		            {!! Form::text('latitude', $vendor->latitude, array('id' => 'latitude ', 'class' => 'form-control' .($errors->has('latitude')?" is-invalid":""), 'placeholder' => '')) !!}
		            <strong class="invalid-feedback" id="error-latitude"></strong>
		          </div>
		          <div class="form-group col-lg-5 col-md-5 col-sm-5">
		            {!! Form::label('longitude', 'Longitude', array('class' => 'control-label')); !!}
		            {!! Form::text('longitude', $vendor->longitude, array('id' => 'longitude', 'class' => 'form-control' .($errors->has('longitude')?" is-invalid":""), 'placeholder' => '')) !!}
		            <strong class="invalid-feedback" id="error-longitude"></strong>
		          </div>
		          <div class="form-group col-lg-2 col-md-2 col-sm-2 button-top"><a href="" id = "test" >get lat & longitude</a></div>
		        </div>
		        <div class="form-group col-lg-12 col-md-12 col-sm-12"><p id="demo" class="col-lg-12 col-md-12 col-sm-12"></p></div>
			</div>
			<div class="box-footer">
			{!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection
@section('footer_scripts')
<script>
var x = document.getElementById("demo");
$("#test").on('click', function(e) {
  e.preventDefault();
  
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition, showError);
  } else { 
     x.innerHTML = "Geolocation is not supported by this browser.";
  }
});

function showPosition(position) {
  $("#latitude").val(position.coords.latitude);
  $("#longitude").val(position.coords.longitude);
}
function showError(error) {
  switch(error.code) {
    case error.PERMISSION_DENIED:
      x.innerHTML = "Please enter manual latitude & longitude."
      break;
    case error.POSITION_UNAVAILABLE:
      x.innerHTML = "Please enter manual latitude & longitude."
      break;
    case error.TIMEOUT:
      x.innerHTML = "Please enter manual latitude & longitude."
      break;
    case error.UNKNOWN_ERROR:
      x.innerHTML = "Please enter manual latitude & longitude."
      break;
  }
}
</script>
@endsection
