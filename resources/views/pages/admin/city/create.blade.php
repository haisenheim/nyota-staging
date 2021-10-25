@extends('layouts.admin.app')

@section('template_title')

   City

@endsection

@section('content')

<section class="content-header" >

    <h1>City</h1>

</section>

<div class="row">

        <div class="col-md-12">

          <!-- general form elements -->

          <div class="box box-primary">

            <div class="box-header with-border">

        <h3 class="box-title">Create</h3>

        <div class="pull-right">

          <a href="{{ url('/admin/city') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">

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

            {!! Form::open(array('route' => 'city.store', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}

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

     {{--     <div class="form-group col-lg-6 col-md-6 col-sm-6">

          {!! Form::label('country', 'Country', array('class' => 'control-label')); !!}

          <select class="form-control" name="country" id="country">

                      <option value="">Select Country</option>

                      @if ($countries)

                        @foreach($countries as $country)

                          <option value="{{ $country->id }}">{{ $country->country_name }}</option>

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

                       <option value="">Select State</option>

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

