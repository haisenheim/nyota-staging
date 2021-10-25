@extends('layouts.admin.app')
@section('template_title')
Notification
@endsection
@section('content')
  <section class="content-header" >
    <h1>Notification</h1>
  </section>
  <div class="row">
    <div class="col-md-12">
    <!-- general form elements -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create</h3>
        </div>
        @if(Session::has('message'))
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
          <p class="alert alert-success" style="padding: 2px;">{{ Session::get('message') }}</p>
          </div>
        </div>
        @endif
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(array('route' => 'notification.store', 'method' => 'POST', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}
        {!! csrf_field() !!}
        <div class="box-body">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('title', 'Title(en)', array('class' => 'control-label')); !!}
              {!! Form::text('title_en', NULL, array('id' => 'title_en', 'class' => 'form-control', 'placeholder' => '')) !!}
              @if ($errors->has('title_en'))
              <span class="help-block">
              <strong>{{ $errors->first('title_en') }}</strong>
              </span>
              @endif
            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('title', 'Title(fr)', array('class' => 'control-label')); !!}
              {!! Form::text('title_fr', NULL, array('id' => 'title_fr', 'class' => 'form-control', 'placeholder' => '')) !!}
              @if ($errors->has('title_fr'))
              <span class="help-block">
              <strong>{{ $errors->first('title_fr') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('message', 'Message(en)', array('class' => 'control-label')); !!}
              {!! Form::textarea('message_en', NULL, array('id' => 'message_en', 'class' => 'form-control', 'placeholder' => '')) !!}
              @if ($errors->has('message_en'))
              <span class="help-block">
              <strong>{{ $errors->first('message_en') }}</strong>
              </span>
              @endif
            </div>
            <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('message', 'Message(fr)', array('class' => 'control-label')); !!}
              {!! Form::textarea('message_fr', NULL, array('id' => 'message_fr', 'class' => 'form-control', 'placeholder' => '')) !!}
              @if ($errors->has('message_fr'))
              <span class="help-block">
              <strong>{{ $errors->first('message_fr') }}</strong>
              </span>
              @endif
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group col-lg-6 col-md-6 col-sm-6">
              {!! Form::label('user', 'User', array('class' => 'control-label')); !!}
              <select class="form-control" name="user" id="user">
              <option value="">Select all user</option>
              @if ($users)
              @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->first_name }}</option>
              @endforeach
              @endif
              </select>
              @if ($errors->has('user'))
              <span class="help-block">
              <strong>{{ $errors->first('user') }}</strong>
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
