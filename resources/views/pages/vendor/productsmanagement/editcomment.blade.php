@extends('layouts.admin.app')
@section('template_title')
Comment
@endsection
@section('content')
<section class="content-header" >
	<h1>Comment</h1>
</section>
<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Edit</h3>
				<div class="pull-right">
					<a href="{{ url('/vendor/products/addcomment/'.$comment->product_id) }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
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
			{!! Form::open(array('route' => ['vendor-product-updatecomment', $comment->id], 'method' => 'POST','role' => 'form', 'class' => 'needs-validation','id' => 'myform','enctype' => 'multipart/form-data' ,'files' => true)) !!}
			{!! csrf_field() !!}
			<div class="box-body">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('comment', 'Comment', array('class' => 'control-label')); !!}
							{!! Form::textarea('comment', $comment->comment, array('id' => 'comment', 'rows' => 4, 'class' => 'form-control', 'placeholder' => '')) !!}
							@if ($errors->has('comment'))
							<span class="help-block">
							<strong>{{ $errors->first('comment') }}</strong>
							</span>
							@endif
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-6">
							{!! Form::label('rating', 'Rating', array('class' => 'control-label')); !!}
							<input type="number" step="1" name="rating" id="rating" min="1" max="5" value="{{$comment->rating}}" class="form-control" required="">
							<input type="hidden" name="product_id" value="{{$comment->product_id}}">
							@if ($errors->has('rating'))
							<span class="help-block">
							<strong>{{ $errors->first('rating') }}</strong>
							</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
			{!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit','id'=>'product_submit')) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection
@section('footer_scripts')
@endsection