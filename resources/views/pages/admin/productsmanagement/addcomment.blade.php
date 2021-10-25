@extends('layouts.admin.app')
@section('template_title')
   Comments
@endsection
@section('content')
<section class="content-header" >
    <h1>Comments</h1>
</section>
<div class="row">
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
				<h3 class="box-title">Create</h3>
				<div class="pull-right">
					<a href="{{ url('/admin/products') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
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
            {!! Form::open(array('route' => ['product-storecomment', $id], 'method' => 'POST','role' => 'form', 'class' => 'needs-validation','id' => 'myform','enctype' => 'multipart/form-data' ,'files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="row">
              	<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('comment', 'Comment', array('class' => 'control-label')); !!}
						{!! Form::textarea('comment', NULL, array('id' => 'comment', 'rows' => 4, 'class' => 'form-control', 'placeholder' => '')) !!}
						@if ($errors->has('comment'))
							<span class="help-block">
								<strong>{{ $errors->first('comment') }}</strong>
							</span>
						@endif
					</div>

					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('rating', 'Rating', array('class' => 'control-label')); !!}
						<input type="number" step="1" name="rating" id="rating" min="1" max="5" value="1" class="form-control" required="">
						@if ($errors->has('rating'))
							<span class="help-block">
								<strong>{{ $errors->first('rating') }}</strong>
							</span>
						@endif
					</div>
				</div>
				</div>
			   </div>
              <!-- /.box-body -->

              <div class="box-footer">
                {!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit','id'=>'product_submit')) !!}
              </div>
            {!! Form::close() !!}
          </div>
          </div>
          <div class="col-md-12">
          <!-- Box Comment -->
          
           <div class="box-footer_text box-comments">
           	<div class="box-header with-border">
                    <h3 class="box-title">List</h3>
             </div>
              <div class="box-comment">
                @foreach($comments as $comment)
                <div class="comment-text">
                      <span class="username">
                        {{$comment->username}}
                      </span>
                      {{$comment->comment}}
                      <p><span class="user_name">Rating: </span>{{$comment->rating}}</p>
                      <p><small>{!! Form::open(array('url' => 'admin/products/deletecomment/' . $comment->id, 'class' => '', 'data-toggle' => 'tooltip')) !!}
                            {!! Form::hidden('_method', 'DELETE') !!}
                            {!! Form::button('Delete', array('class' => 'btn btn-danger btn-xs','type' => 'button', 'style' =>'width:auto; float:left;margin-right:5px;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete Comment', 'data-message' => 'Are you sure you want to delete this comment ?')) !!}
                            {!! Form::close() !!}  <a class="btn btn-xs btn-info" href="{{ URL::to('/admin/products/editcomment/' . $comment->id ) }}">Edit</a></small></p>
                </div><hr>
                @endforeach
              </div>
             </div>
            
              
              </div>
          <!-- /.box -->
	</div>
@include('modals.modal-delete')
@endsection
@section('footer_scripts')
@include('scripts.delete-modal-script')
@endsection