@extends('layouts.admin.app')
@section('template_title')
   Product
@endsection
@section('content')
<section class="content-header" >
    <h1>Product</h1>
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
            {!! Form::open(array('route' => 'products.store', 'method' => 'POST','role' => 'form', 'class' => 'needs-validation','id' => 'myform','enctype' => 'multipart/form-data' ,'files' => true)) !!}
				{!! csrf_field() !!}
              <div class="box-body">
              	<div class="row">
              		
				<div class="col-lg-12 col-md-12 col-sm-12">
					
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('name', 'Name', array('class' => 'control-label')); !!}
						{!! Form::text('name', NULL, array('id' => 'name', 'class' => 'form-control' .($errors->has('name')?" is-invalid":""), 'placeholder' => '')) !!}
						<strong id="error-name" class="invalid-feedback"></strong>
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
					{!! Form::label('category', 'Category', array('class' => 'control-label')); !!}
					<select  name="category" class="form-control{{ ($errors->has('category') ? ' is-invalid' : '') }}" id="category">
                      <option value="">Select Category</option>
                      @if ($categories)
                        @foreach($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                      @endif
          </select>
          <strong id="error-category" class="invalid-feedback"></strong>
					</div>
          
				</div>
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
          {!! Form::label('child_category', 'Child Category', array('class' => 'control-label')); !!}
          <select  name="child_category" class="form-control{{ ($errors->has('child_category') ? ' is-invalid' : '') }}" id="child_category">
                  <option value="">Select Child Category</option>
                {{--    @if ($child_categories)
                        @foreach($child_categories as $child_category)
                          <option value="{{ $child_category->id }}">{{ $child_category->name }}</option>
                        @endforeach
                      @endif  --}}
          </select>
          <strong id="error-child_category" class="invalid-feedback"></strong>
          </div>
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
          {!! Form::label('vendor', 'Vendor', array('class' => 'control-label')); !!}
          <select  name="vendor" class="form-control{{ ($errors->has('vendor') ? ' is-invalid' : '') }}" id="vendor">
                      <option value="">Select Vendor</option>
                      @if ($vendor)
                        @foreach($vendor as $vendors)
                          <option value="{{ $vendors->id }}">{{ $vendors->first_name }}</option>
                        @endforeach
                      @endif
          </select>
          <strong id="error-vendor" class="invalid-feedback"></strong>
          </div>
        </div>

				<div class="col-lg-12 col-md-12 col-sm-12">	
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('sku', 'SKU', array('class' => 'control-label')); !!}
						{!! Form::text('sku', NULL, array('id' => 'sku', 'class' => 'form-control' .($errors->has('sku')?" is-invalid":""), 'placeholder' => '')) !!}
						<strong class="invalid-feedback" id="error-sku"></strong>
						
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
					{!! Form::label('status', 'Status', array('class' => 'control-label')); !!}
					<select class="form-control{{ ($errors->has('status') ? ' is-invalid' : '') }}"  name="status" id="status">
                      <option value="">Select Status</option>
                      <option value="0">Active</option>
                      <option value="1">Deactive</option>
                    </select>
						<strong class="invalid-feedback" id="error-status"></strong>
					</div>
				</div>
				
				<div class="col-lg-12 col-md-12 col-sm-12">
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('short_description', 'Short Description', array('class' => 'control-label')); !!}
						{!! Form::textarea('short_description', NULL, array('id' => 'short_description', 'rows' => 4, 'class' => 'form-control' .($errors->has('short_description')?" is-invalid":""), 'placeholder' => '')) !!}
						<strong class="invalid-feedback" id="error-short_description"></strong>
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-6">
						{!! Form::label('full_description', 'Full Description', array('class' => 'control-label')); !!}
						{!! Form::textarea('full_description',NULL, array('id' => 'full_description', 'rows' => 4, 'class' => 'form-control' .($errors->has('full_description')?" is-invalid":""), 'placeholder' => '')) !!}
						<strong class="invalid-feedback" id="error-full_description"></strong>
					</div>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12">	
					<div class="form-group col-lg-12 col-md-12 col-sm-12">
  					<div class="needsclick dropzone" id="document-dropzone">
  					</div>
	       </div>
				</div>
		{{--<div id="" class="form-group col-lg-12 col-md-12 col-sm-12">
			<div class="form-group col-lg-5 col-md-5 col-sm-5">
				{!! Form::label('latitude', 'Latitude', array('class' => 'control-label')); !!}
				{!! Form::text('latitude', NULL, array('id' => 'latitude ', 'class' => 'form-control' .($errors->has('latitude')?" is-invalid":""), 'placeholder' => '')) !!}
				<strong class="invalid-feedback" id="error-latitude"></strong>
			</div>
			<div class="form-group col-lg-5 col-md-5 col-sm-5">
				{!! Form::label('longitude', 'Longitude', array('class' => 'control-label')); !!}
				{!! Form::text('longitude', NULL, array('id' => 'longitude', 'class' => 'form-control' .($errors->has('longitude')?" is-invalid":""), 'placeholder' => '')) !!}
				<strong class="invalid-feedback" id="error-longitude"></strong>
			</div>
        	<div class="form-group col-lg-2 col-md-2 col-sm-2 button-top"><a href="" id = "test" >get lat & longitude</a></div>
		</div>
        <div class="form-group col-lg-12 col-md-12 col-sm-12"><p id="demo" class="col-lg-12 col-md-12 col-sm-12"></p></div>--}}
        <div class="col-lg-12 col-md-12 col-sm-12">	
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
            @if($attributetypes)
            @foreach($attributetypes as $attributetype)
            {!! Form::label('attribute_type', $attributetype->name, array('class' => 'control-label')); !!}
            <input type="checkbox" name="attribute[]" id="{{$attributetype->name}}" value="{{$attributetype->id}}" class="myCheckbox{{ ($errors->has('attribute') ? ' is-invalid' : '') }}" >
            <input type="hidden" name="attribute_name[]" value="{{$attributetype->name}}">
            @endforeach
            
            <button type="button" id="generate">Generate</button>
            <button type="button" id="re-generate" class="pull-right re-generate" hidden="hidden">Re-generate</button>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
            <strong class="invalid-feedback" id="error-attribute"></strong>
            
            </div>
            @endif
            
          <div class="form-group col-lg-6 col-md-6 col-sm-6">
            <button type="button" id="add" class="add pull-right btn btn-success btn-xs" style="display: none;"><i class="fa fa-plus" aria-hidden="true"></i></button>
          </div>
        </div>
        	
        <div id="second_attribute" class="col-lg-12 col-md-12 col-sm-12">
        </div>
        
        
				</div>
			   </div>
			
              <!-- /.box-body -->

              <div class="box-footer">
                {!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit','id'=>'product_submit')) !!}
              </div>
            {!! Form::close() !!}

          </div>
          <!-- /.box -->
		</div>
	</div>
	
@endsection
@section('footer_scripts')
{{--<script>
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
</script>--}}
<script>
  var uploadedDocumentMap = {}
  Dropzone.options.documentDropzone = {
    url: "{{ route('product.storeMedia') }}",
    maxFilesize: 2, // MB
    addRemoveLinks: true,
    acceptedFiles: ".png, .jpg, .jpeg",
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    success: function (file, response) {

      $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
      uploadedDocumentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDocumentMap[file.name]
      }
      $('form').find('input[name="document[]"][value="' + name + '"]').remove()
    }
  }
</script>
<script type="text/javascript">
  jQuery(document).ready(function(){
  $( "#myform" ).submit(function( event ) {
	event.preventDefault();
    var form = $(this);
    var data = new FormData($(this)[0]);
    var url = form.attr("action");
    var attribute_name = [];
    var attribute_id = [];
    $.each($("input[name='attribute[]']:checked"), function(){ 
      attribute_name.push($(this).attr('id'));
      attribute_id.push($(this).val());
      });

     $.ajax({
        type: form.attr('method'),
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
        	
        	$('.is-invalid').removeClass('is-invalid');
        	if (data.fail) {
            if(data.attr_errors){
              $('#second_attribute').html('<div class="col-lg-12 col-md-12 col-sm-12"><p class="form-group col-lg-12 col-md-12 col-sm-12" style="color:red">Please click generate button.</p></div>');
            }      		

            for (control in data.errors) {
    					if(data.errors.attribute){
    						$('#error-attribute').show();
    					}else{
    						$('#error-attribute').hide();
    					}
              if(control.indexOf(".") != -1){
                var arr = control.split(".");
                $("input[name='"+arr[0]+'[]'+"']").addClass('is-invalid');
                $("textarea[name='"+arr[0]+'[]'+"']").addClass('is-invalid');
                $("input[name='"+arr[0]+'['+arr[1]+']'+"']").addClass('is-invalid');
                $("select[name='"+arr[0]+'['+arr[1]+']'+"']").addClass('is-invalid');
                $('#error-' + arr[1]).html(data.errors[control]);
                $('#error-' + arr[0]).html(data.errors[control]);
                if(arr[2] !== null){
                  $("input[name='"+arr[0]+'['+arr[1]+']'+'['+arr[2]+']'+"']").addClass('is-invalid');
                  $("textarea[name='"+arr[0]+'['+arr[1]+']'+'['+arr[2]+']'+"']").addClass('is-invalid');
                  $("select[name='"+arr[0]+'['+arr[1]+']'+'['+arr[2]+']'+"']").addClass('is-invalid');
                  $('#error-'+arr[1]+'-'+ arr[2]).html(data.errors[control]);
                  $('#error-' + arr[2]).html(data.errors[control]);
                }
              }
              else{
                $('input[name=' + control + ']').addClass('is-invalid');
                $('select[name=' + control + ']').addClass('is-invalid');
                $('#error-' + control).html(data.errors[control]);
              }
            	
            }
          }
           // if (data.attribute) {
            	
            	// for (control in data.errors) {
            	// 	$('input[name=' + control + ']').addClass('is-invalid');
            	// 	('#error-' + control).html(data.errors[control]);
            	// }
            //	console.log(data.errors);
           // }
            else {
            	//console.log(data.success);
            	var url = "{{ URL::to('/admin/products') }}";
              $(location).attr('href', url); 
            }
        },
        error: function (xhr, textStatus, errorThrown) {
          alert("Error: " + errorThrown);
        }
    });
   return false;
  });
});
  </script>
  <script type="text/javascript">
    var variable=0;
$(document).ready(function() {
  $("#generate").click(function(){
  var attribute_type_id = [];
    var attribute_name = [];
      $.each($("input[name='attribute[]']:checked"), function(){ 
       attribute_type_id.push($(this).val());
       attribute_name.push($(this).attr('id'));
      });
      if(attribute_type_id != ""){
      $.ajax({
        url: "{{ URL::to('admin/search_attribute/') }}/"+attribute_type_id,
        type: 'GET',
        data: { 
                attribute_name: attribute_name,variable: variable
              },
        success: function (data){
                $('.myCheckbox').attr('readonly','readonly');
                $('#generate').hide();
                $('#re-generate').show();
                $('#add').show();
                $('#second_attribute').html(data);
            }
      })
    }
    else{
           $('#second_attribute').html('<div class="col-lg-12 col-md-12 col-sm-12"><p class="form-group col-lg-12 col-md-12 col-sm-12" style="color:red">Please select attribute type.</p></div>');
    }
  });
});
</script>
<script type="text/javascript">
$(document).on('click', '.remove', function () {
    $(this).parents('.main').remove();
});
</script>
<script type="text/javascript">
 // rowNum++;
 var ii = 1;
$(document).on('click', '.add', function () {
  
  var variable=  ii++;
   var attribute_type_id = [];
    $.each($("input[name='attribute[]']:checked"), function(){ 
       attribute_type_id.push($(this).val());
      });
    if(attribute_type_id != ""){
      $.ajax({
        url: "{{ URL::to('admin/search_attribute/') }}/"+attribute_type_id,
        type: 'GET',
        data: { 
                variable: variable
              },
        success: function (data){
               $('#second_attribute').append(data);
            }
      })
    }
    else{
           $('#second_attribute').html('<div class="col-lg-12 col-md-12 col-sm-12"><p class="form-group col-lg-12 col-md-12 col-sm-12" style="color:red">Please checked attributetype.</p></div>');
    }
});
</script>
<script type="text/javascript">
$(document).on('click', '.re-generate', function () {
    $('.myCheckbox').attr('readonly',false);
    $('#generate').show();
    $('#re-generate').hide();
    $('#add').hide();
    $('#second_attribute').empty();
});
</script>
<script>
 
//     $(document).ready(function () {
 
//     $('#myform').validate({ // initialize the plugin
//         rules: {
//             price: {
//                 required: true
//             },
//         }
//     });
// });
</script>





<script type="text/javascript">
 $(document).ready(function() {
     $('#category').on('change', function() {
         var category_id = $(this).val();
        if(category_id) {
          $.ajax({
                 url: "{{url('admin/get-subcategory/')}}/"+category_id ,
                 type: "GET",
               success:function(data) {

                   $("#child_category").empty();
                    $("#child_category").html(data);
                  }
              });
            }else{
                $('#child_category').empty();
           }
       });
    });
</script>







   
@endsection