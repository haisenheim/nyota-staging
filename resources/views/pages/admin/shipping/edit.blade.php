@extends('layouts.admin.app')

@section('template_title')

   Shipping

@endsection

@section('content')

<section class="content-header" >

    <h1>Shipping</h1>
	

</section>
	
<div class="row">

        <div class="col-md-12">

          <!-- general form elements -->

          <div class="box box-primary">

          	<div class="box-header with-border">

				<h3 class="box-title">Edit</h3>

				<div class="pull-right">

					<a href="{{ url('/admin/shipping') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">

						<i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>

						Back

					</a>

				</div>

            </div>

           

            {!! Form::open(array('route' => ['shipping.update', $shipping->id], 'method' => 'PUT', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}

				{!! csrf_field() !!}

              <div class="box-body">

              	<div class="col-lg-12 col-md-12 col-sm-12">

					<div class="form-group col-lg-6 col-md-6 col-sm-6">

						{!! Form::label('shipping_price', 'Shipping Price', array('class' => 'control-label')); !!}

						{!! Form::text('shipping_price', $shipping->shipping_price, array('id' => 'shipping_price', 'class' => 'form-control', 'placeholder' => '')) !!}

						@if ($errors->has('shipping_price'))

							<span class="help-block">

								<strong>{{ $errors->first('shipping_price') }}</strong>

							</span>

						@endif

					</div>

					<div class="form-group col-lg-6 col-md-6 col-sm-6">

					{!! Form::label('city', 'City', array('class' => 'control-label')); !!}
	

				<select class="form-control" name="city" id="city">

                      <option value="">Select City</option>
						
			
                       @foreach($city as $c)			

                        <option value="{{ $c->id }}" {{ $shipping->city_id == $c->id ? 'selected="selected"' : '' }}>{{ $c->name }}</option> 

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


     {{--   <div class="form-group col-lg-6 col-md-6 col-sm-6">

          {!! Form::label('state', 'State', array('class' => 'control-label')); !!}

          <select class="form-control" name="state" id="state">

                      <option value="">Select Country</option>

                      @if ($states)

                        @foreach($states as $state)

                          <option value="{{ $state->id }}" {{ $state->id == $city->stateid ? 'selected="selected"' : '' }}>{{ $state->name }}</option>

                        @endforeach

                      @endif

                    </select>

            @if ($errors->has('state'))

              <span class="help-block">

                <strong>{{ $errors->first('state') }}</strong>

              </span>

            @endif

          </div> 	

          <div class="form-group col-lg-6 col-md-6 col-sm-6">

          {!! Form::label('district', 'District', array('class' => 'control-label')); !!}

          <select class="form-control" name="district" id="district">

                       <option value="">Select District</option>

                       @if ($districts)

                        @foreach($districts as $district)

                          <option value="{{ $district->id }}" {{ $district->id == $city->district_id ? 'selected="selected"' : '' }}>{{ $district->name }}</option>

                        @endforeach

                      @endif

          </select>

            @if ($errors->has('district'))

              <span class="help-block">

                <strong>{{ $errors->first('district') }}</strong>

              </span>

            @endif

          </div> --}}

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

