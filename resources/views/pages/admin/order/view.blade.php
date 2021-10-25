@extends('layouts.admin.app')

@section('template_title')

   Current Order

@endsection

@section('content')

<section class="content-header" >

    <h1>Current Order</h1>

</section>
{{--@php
echo "<pre>";
print_r($products);die();
@endphp--}}

<div class="row">

<div class="col-md-12">

  <div class="box box-primary">

    <div class="box-header with-border">

      <h3 class="box-title">Show</h3>

      <div class="pull-right">

        <a href="{{ url('/admin/order') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">

          <i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>

          Back

        </a>

      </div>

    </div>

    <div class="box-body">

      <div class="row invoice-info">

        <div class="col-md-3 invoice-col">

          <b class="">Order Details</b><br>

          <br>
         
          <div class="col-md-6 invoice-row"><b>Order ID: </b></div><div class="col-md-6">{{$products[0]->order_id}}</div><br>

         
          <div class="col-md-6 invoice-row"><b>Customer Name: </b></div><div class="col-md-6">{{$products[0]->first_name}}</div><br>

          <div class="col-md-6 invoice-row"><b>Order Status:</b></div><div class="col-md-6">@if($products[0]->status == 0)

                         @if($products[0]->modification == 1 ) Order Modified @else Order placed @endif

                        @elseif ($products[0]->status == 1) 

                          Order Confirmed

                        @elseif ($products[0]->status == 2) 

                          Packed

                        @elseif ($products[0]->status == 3) 

                          Shipped

                        @elseif ($products[0]->status == 4) 

                          Delivered

                        @elseif ($products[0]->status == 5) 

                          Canceled    

                    @endif

                  </div>

          <div class="col-md-6 invoice-row"><b>Payment Type:</b></div><div class="col-md-6">@if($products[0]->payment_type == 0)

                          Cash On Delivery

                        @elseif ($products[0]->payment_type == 1) 

                          Airtel Money

                        @endif

                  </div>



 <div class="col-md-6 invoice-row"><b>Delievery Tag:</b></div><div class="col-md-6">@if($products[0]->delievery_tag != NULL)
@if($products[0]->delievery_tag == 0)

                          Express

                        @elseif ($products[0]->delievery_tag == 1) 

                          Standard

                        @endif
@endif

                  </div>


                </div>

       <div class="col-md-3 invoice-col">

          <b class="">Delivery Address</b><br>

         

          <address class="">

            {{!empty($products[0]->apartment_name) ? $products[0]->apartment_name : ''}},<br>

            {{!empty($products[0]->neighbour_hood) ?$products[0]->neighbour_hood : ''}},<br>

            {{!empty($products[0]->street) ?$products[0]->street : ''}},<br>

            {{!empty($products[0]->city) ?$products[0]->city : ''}},<br>

            {{$products[0]->state}}@if($products[0]->pincode)-{{$products[0]->pincode}}@endif.<br>

            Phone: {{$products[0]->userphone}}<br>

          </address>

          

        </div>

        <!-- /.col -->
         @if($products[0]->modification == 1 )
          <div class="col-md-6"><b>@if($products[0]->modification == 1 )
            This order is modified on {{$date}}.
            @endif</b></div><br><br>@endif
        <div class="col-md-3 invoice-col">

          <b class="">Status Change</b><br>

          <select class="form-control"  name="order_status" id="order_status">

            <option value="">Select Order Status</option>

            @if($products[0]->status <= 0)

            <option value="0" {{ $products[0]->status == 0 ? 'selected="selected"' : '' }}>@if($products[0]->modification == 1 ) Order Modified @else Order placed @endif</option>

            <option value="1" {{ $products[0]->status == 1 ? 'selected="selected"' : '' }}>Order Confirmed</option>

            <option value="2" {{ $products[0]->status == 2 ? 'selected="selected"' : '' }}>Packed</option>

            <option value="3" {{ $products[0]->status == 3 ? 'selected="selected"' : '' }}>Shipped</option>

            <option value="4" {{ $products[0]->status == 4 ? 'selected="selected"' : '' }}>Delivered</option>

            <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option>

            @elseif($products[0]->status <= 1)

            <option value="1" {{ $products[0]->status == 1 ? 'selected="selected"' : '' }}>Order Confirmed</option>

            <option value="2" {{ $products[0]->status == 2 ? 'selected="selected"' : '' }}>Packed</option>

            <option value="3" {{ $products[0]->status == 3 ? 'selected="selected"' : '' }}>Shipped</option>

            <option value="4" {{ $products[0]->status == 4 ? 'selected="selected"' : '' }}>Delivered</option>

            <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option>

            @elseif($products[0]->status <= 2)

            <option value="2" {{ $products[0]->status == 2 ? 'selected="selected"' : '' }}>Packed</option>

            <option value="3" {{ $products[0]->status == 3 ? 'selected="selected"' : '' }}>Shipped</option>

            <option value="4" {{ $products[0]->status == 4 ? 'selected="selected"' : '' }}>Delivered</option>

            <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option>

            @elseif($products[0]->status <= 3)

            <option value="3" {{ $products[0]->status == 3 ? 'selected="selected"' : '' }}>Shipped</option>

            <option value="4" {{ $products[0]->status == 4 ? 'selected="selected"' : '' }}>Delivered</option>

             <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option>

            @elseif($products[0]->status <= 4)

            <option value="4" {{ $products[0]->status == 4 ? 'selected="selected"' : '' }}>Delivered</option>

            {{-- <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option> --}}

            @elseif($products[0]->status <= 5)

            <option value="5" {{ $products[0]->status == 5 ? 'selected="selected"' : '' }}>Canceled</option>

            @endif

          </select>

        </div>

        

          {!! Form::open(array('route' => ['order-estimate_date', $products[0]->order_id], 'method' => 'post', 'role' => 'form', 'class' => 'needs-validation','files' => true)) !!}

          {!! csrf_field() !!}

          <div class="col-md-2 invoice-col">

          <b class="">Estimated Date</b><br>

         <input type="text" id="date" name="date" value="{{ $products[0]->estimated_delivery_date}}" class="form-control date" data-id="date">

         @if ($errors->has('date'))

            <span class="help-block">

              <strong>{{ $errors->first('date') }}</strong>

            </span>

          @endif

         </div>

         <div class="col-md-1 invoice-col"><br>

           {!! Form::button('Save', array('class' => 'btn btn-success margin-bottom-1 mb-1 float-right','type' => 'submit' )) !!}

         </div>

         {!! Form::close() !!}

        

      </div>

        <!-- /.col -->

        

        <!-- /.col -->

      

      <div class="row">

      <div class="col-md-12 table-responsive">

        <h4 class="center">Product Details</h4>

          <table class="table table-striped">

            <thead>

            <tr>

             <th class="">Product Name</th>

             <th class="">Product Image</th>

              <th>Variant </th>

              <th>Qty.</th>

              <th>Subtotal </th>

             </tr>

            </thead>

            <tbody>

            @foreach ($product_details as $key => $product) 

            <tr>

              <td>{{$product['name']}}</td>

              <td>@if(!empty($product['image']))

            <img class="card-img-top" style="height: 50px;width: 50px;display: block;" src="{{url('/storage/tmp/uploads')}}/{{$product['image']}}" data-holder-rendered="true">

            @endif</td>

              <td>@php

              $seletedatts = json_decode($product['odr_variant']);

              $test = json_decode(json_encode($seletedatts), True);

              if (is_array($test) || is_object($test)){

                foreach ($test as $value){

                  unset($value['quantity']);

                  unset($value['price']);

                  unset($value['prize']);

                  unset($value['sale_price']);

                 foreach ($value as $key => $product_variant) {

                    echo  $key.':'.$product_variant.'<br>';

                  }

                }

              }

              @endphp</td>

              <td>{{$product['qty']}}</td>

              <td>{{number_format($product['total'],2,',','.')}}{{$currency->contain}}</td>



            </tr>

            @endforeach

              <tr>

                <td colspan="3"></td>

                  <td ><b>Subtotal: </b></td>

                  <td>{{number_format($sum,2,',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td colspan="3"></td>

                  <td><b>Tax ({{$tax->contain}}%): </b></td>

                  <td>{{number_format($tax_val,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td colspan="3"></td>

                  <td><b>Shipping price: </b></td>

                  <td>{{number_format($price,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                <tr>
                @if($products[0]->delievery_tag != NULL)

                  <td colspan="3"></td>

                  <td><b>Delivery charges: </b></td>

                  <td>@if($products[0]->delievery_tag == 0){{number_format($express_charge->contain,'2',',','.')}}{{$currency->contain}} @elseif($products[0]->delievery_tag == 1) {{number_format($standard_charge->contain,'2',',','.')}}{{$currency->contain}}@endif</td>
                @endif
                </tr>

                <tr>

                  <td colspan="3" class="grand-border"></td>

                  <td class="grand-border"><b>Total:</b></td>

                  <td class="grand-border">@if($products[0]->total_price){{number_format($products[0]->total_price,'2',',','.')}}{{$currency->contain}}@endif</td>

                </tr>

            </tbody>

          </table>

        </div>

      </div>

      </div>

     </div>

    </div>

    </div>

   

@endsection

@section('footer_scripts')



</script>

  <script type="text/javascript">

    var order_id =  @php echo $products[0]->order_id; @endphp;

    var prev_val = $('#order_status').val();



    $(document).ready(function() {

      $('#order_status').change(function(){

      var success = confirm('Are you sure you want to change the Status?');

        if (success == true) {

          var order_status = $(this).val();

          if(order_status){

            $.ajax({

            type:"GET",

            url:"{{URL::to('admin/order-status/')}}/"+order_status+"/"+order_id,

            success:function(data){

              console.log(data);

             //location.reload(true);

            }

            });

          }

        }

        else{

          $(this).val(prev_val);

        }

      });

    });

  </script>

 

@endsection