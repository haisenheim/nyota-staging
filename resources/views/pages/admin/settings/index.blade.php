@extends('layouts.admin.app')

@section('template_title')

   Setting

@endsection

@section('content')

<section class="content-header" >

    <h1>Setting</h1>

</section>

<div class="row">

  <div class="col-md-12">

    <!-- general form elements -->

    <div class="box box-primary">

      <div class="box-header with-border">

        <h3 class="box-title">Setting</h3>

      </div>

     

      {!! Form::open(array('route' => 'updatesettings', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}

      {!! csrf_field() !!}

        <div class="box-body">

          <div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('tax', 'Tax', array('class' => 'control-label')); !!}

              {!! Form::text('tax',$tax->contain, array('id' => 'tax', 'class' => 'form-control')) !!}

               @if ($errors->has('tax'))

                <span class="help-block">

                  <strong>{{ $errors->first('tax') }}</strong>

                </span>

              @endif

            </div>

          </div>



          <div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('max_price', 'Max Price', array('class' => 'control-label')); !!}

              {!! Form::text('max_price',$max_price->contain, array('id' => 'max_price', 'class' => 'form-control')) !!}

               @if ($errors->has('max_price'))

                <span class="help-block">

                  <strong>{{ $errors->first('max_price') }}</strong>

                </span>

              @endif

            </div>

          </div>

          <div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('shipping_price', 'Shipping Price', array('class' => 'control-label')); !!}

              {!! Form::text('shipping_price',$shipping_price->contain, array('id' => 'shipping_price', 'class' => 'form-control')) !!}

               @if ($errors->has('shipping_price'))

                <span class="help-block">

                  <strong>{{ $errors->first('shipping_price') }}</strong>

                </span>

              @endif

            </div>

          </div>





 <div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('express_delievery', 'Express delivery', array('class' => 'control-label')); !!}

              {!! Form::text('express_delievery',$express_delievery->contain, array('id' => 'express_delievery', 'class' => 'form-control')) !!}

               @if ($errors->has('express_delievery'))

                <span class="help-block">

                  <strong>{{ $errors->first('express_delievery') }}</strong>

                </span>

              @endif

            </div>

          </div>


<div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('express_en', 'Express en', array('class' => 'control-label')); !!}

              {!! Form::text('express_en',$express_en->contain, array('id' => 'express_en', 'class' => 'form-control')) !!}

               @if ($errors->has('express_en'))

                <span class="help-block">

                  <strong>{{ $errors->first('express_en') }}</strong>

                </span>

              @endif

            </div>

          </div>



<div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('express_fr', 'Express fr', array('class' => 'control-label')); !!}

              {!! Form::text('express_fr',$express_fr->contain, array('id' => 'express_fr', 'class' => 'form-control')) !!}

               @if ($errors->has('Express_fr'))

                <span class="help-block">

                  <strong>{{ $errors->first('express_fr') }}</strong>

                </span>

              @endif

            </div>

          </div>



<div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('standard_delievery', 'Standard delievery', array('class' => 'control-label')); !!}

              {!! Form::text('standard_delievery',$standard_delievery->contain, array('id' => 'standard_delievery', 'class' => 'form-control')) !!}

               @if ($errors->has('standard_delievery'))

                <span class="help-block">

                  <strong>{{ $errors->first('standard_delievery') }}</strong>

                </span>

              @endif

            </div>

          </div>


<div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('standard_en', 'Standard en', array('class' => 'control-label')); !!}

              {!! Form::text('standard_en',$standard_en->contain, array('id' => 'standard_en', 'class' => 'form-control')) !!}

               @if ($errors->has('standard_en'))

                <span class="help-block">

                  <strong>{{ $errors->first('standard_en') }}</strong>

                </span>

              @endif

            </div>

          </div>



<div class="col-lg-6 col-md-6 col-sm-6">

            <div class="form-group">

              {!! Form::label('standard_fr', 'Standard fr', array('class' => 'control-label')); !!}

              {!! Form::text('standard_fr',$standard_fr->contain, array('id' => 'standard_fr', 'class' => 'form-control')) !!}

               @if ($errors->has('standard_fr'))

                <span class="help-block">

                  <strong>{{ $errors->first('standard_fr') }}</strong>

                </span>

              @endif

            </div>

          </div>




















        </div>

        <div class="box-footer">

          {!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}

        </div>

      {!! Form::close() !!}

    </div>

    <!-- /.box -->

  </div>

</div>

@endsection

    

