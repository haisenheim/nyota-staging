@extends('layouts.admin.app')
@section('template_title')
   Location
@endsection
@section('content')
<section class="content-header" >
    <h1>Location</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
          	<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/location') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
						Back
					</a>
				</div>
            </div>
           
            {!! Form::open(array('route' => ['location.update', $location->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="col-lg-12 col-md-12 col-sm-12">
					         <div class="form-group col-lg-6 col-md-6 col-sm-6">
                    {!! Form::label('address', 'Address', array('class' => 'control-label')); !!}
                    {!! Form::textarea('address', $location->address, array('id' => 'address', 'rows' => 4, 'class' => 'form-control','placeholder' => '')) !!}
                    @if ($errors->has('address'))
                  <span class="help-block">
                    <strong>{{ $errors->first('address') }}</strong>
                  </span>
                  @endif
                </div>
                <div class="form-group col-lg-6 col-md-6 col-sm-6">
                {!! Form::label('pincode', 'Pincode', array('class' => 'control-label')); !!}
                {!! Form::text('pincode', $location->pincode, array('id' => 'pincode', 'class' => 'form-control', 'placeholder' => '')) !!}
                @if ($errors->has('pincode'))
                  <span class="help-block">
                    <strong>{{ $errors->first('pincode') }}</strong>
                  </span>
                @endif
              </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
					{!! Form::label('country', 'Country', array('class' => 'control-label')); !!}
					<select class="form-control" name="country" id="country">
                      <option value="">Select Country</option>
                      @if ($countries)
                        @foreach($countries as $country)
                          <option value="{{ $country->id }}" {{ $country->id == $location->countryid ? 'selected="selected"' : '' }}>{{ $country->country_name }}</option>
                        @endforeach
                      @endif
                    </select>
						@if ($errors->has('country'))
							<span class="help-block">
								<strong>{{ $errors->first('country') }}</strong>
							</span>
						@endif
					</div>
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
          {!! Form::label('state', 'State', array('class' => 'control-label')); !!}
          <select class="form-control" name="state" id="state">
                      <option value="">Select Country</option>
                      @if ($states)
                        @foreach($states as $state)
                          <option value="{{ $state->id }}" {{ $state->id == $location->stateid ? 'selected="selected"' : '' }}>{{ $state->name }}</option>
                        @endforeach
                      @endif
                    </select>
            @if ($errors->has('state'))
              <span class="help-block">
                <strong>{{ $errors->first('state') }}</strong>
              </span>
            @endif
          </div>  
				</div>	
        <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="form-group col-lg-6 col-md-6 col-sm-6">
          {!! Form::label('district', 'District', array('class' => 'control-label')); !!}
          <select class="form-control" name="district" id="district">
                       <option value="">Select District</option>
                       @if ($districts)
                        @foreach($districts as $district)
                          <option value="{{ $district->id }}" {{ $district->id == $location->districtid ? 'selected="selected"' : '' }}>{{ $district->name }}</option>
                        @endforeach
                      @endif
          </select>
            @if ($errors->has('district'))
              <span class="help-block">
                <strong>{{ $errors->first('district') }}</strong>
              </span>
            @endif
          </div>
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
          {!! Form::label('city', 'City', array('class' => 'control-label')); !!}
          <select class="form-control" name="city" id="city">
                         <option value="">Select City</option>
                       @if ($citys)
                        @foreach($citys as $city)
                          <option value="{{ $city->id }}" {{ $city->id == $location->city_id ? 'selected="selected"' : '' }}>{{ $city->name }}</option>
                        @endforeach
                      @endif
          </select>
              @if ($errors->has('city'))
                <span class="help-block">
                  <strong>{{ $errors->first('city') }}</strong>
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
