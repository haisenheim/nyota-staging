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
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Show</h3>
        <div class="pull-right">
        <a href="{{ url('/admin/products') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">
        <i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>
        Back
        </a>
        </div>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-6 border-padding">
            <h4 class="center">Product Details</h4><br>
            <div class="product-padding"><div class="col-md-4"><b>Name:</b></div><div class="col-md-8">{{$product->name}}</div></div>
            <div class="product-padding"><div class="col-md-4"><b>SKU:</b></div><div class="col-md-8">{{$product->sku}}</div></div>
            <div class="product-padding"><div class="col-md-4"><b>Category:</b></div><div class="col-md-8">{{$product->category}}</div></div>
            <div class="product-padding"><div class="col-md-4"><b>Child Category:</b></div><div class="col-md-8">{{$product->child_category}}</div></div>
            <div class="product-padding"><div class="col-md-4"><b>Short Description:</b></div><div class="col-md-8"><p>{{$product->short_description}}</p></div></div>
            <div class="product-padding"><div class="col-md-4"><b>Full Description:</b></div><div class="col-md-8"><p>{{$product->full_description}}</p></div></div>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-6 dropzone-border">
            <h4 class="center">Product Images</h4>
            @if(count($medias) > 0)
            <div class="needsclick dropzone" id="document-dropzone">
            @foreach($medias as $image)
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="dz-preview" data-id="{{ $image->id }}"  data-path="{{ $image->image}}">
            <img class="dropzone-thumbnail" src="{{url('/storage/tmp/uploads')}}/{{ $image->image }}" height="100" width="100" >
            </div>
            @endforeach
            </div>
            @else
            <div class="needsclick dropzone" id="document-dropzone">
            <div class="dz-default dz-message"><span class="">This product image not found</span></div>
            </div>
            @endif
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h4 class="center">Product Variants</h4>
            <table class="table table-striped">
              <thead>
                <tr>
                @foreach($mailatt as $mailat)
                @if(!empty($mailat['attr']))
                <th class="">{{ $mailat['name'] }}</th>
                @endif
                @endforeach
                <th>Price </th>
                <th>Sale Price </th>
                <th>Quantity</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($seletedatts as $key => $seletedatt) 
                <tr>
                @foreach($mailatt as $mailat)
                @php $iii = 'a_'.$mailat['id'];@endphp
                @if(!empty($mailat['attr']))
                @foreach($mailat['attr'] as $matt)
                @if($matt['id'] == $seletedatt->$iii)
                <td>{{$matt['name'] }}</td>
                @endif
                @endforeach
                @endif
                @endforeach
                <td>{{ number_format($seletedatt->rprice,2,',','.') }}{{$currency->contain}}</td>
                <td>{{ number_format($seletedatt->sprice,2,',','.') }}{{$currency->contain}}</td>
                <td>{{ $seletedatt->quantity }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection