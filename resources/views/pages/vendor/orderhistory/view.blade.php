@extends('layouts.admin.app')

@section('template_title')

Order History

@endsection

@section('content')

<section class="content-header" >

  <h1>Order History</h1>

</section>

<div class="row">

  <div class="col-md-12">

    <div class="box box-primary">

      <div class="box-header with-border">

        <h3 class="box-title">Show</h3>

        <div class="pull-right">

          <a href="{{ url('/vendor/orderhistory') }}" class="btn btn-light btn-sm float-right" data-toggle="tooltip" data-placement="left">

          <i class="fa fa-fw fa-reply-all" aria-hidden="true"></i>

          Back

          </a>

        </div>

      </div>

      <div class="box-body">

        <div class="row" style="display: none">

        <div class="row">

          <div id="PDFtitle"><b class="" style="font-size: 30px; background-color: #808080">Order History</b></div>

            <div class="col-md-4 invoice-col">

            <div id="PDFordertitle"><b class="" style="font-size: 20px;">Order Details</b></div>

            <div id="PDForderid"><b>Order ID: </b>{{$products[0]->order_id}}<br></div>



              <div id="PDFordername"><b>Customer Name: </b>{{$products[0]->first_name}}<br></div>

              <div id="PDForderstatus"><b>Order Status: </b>@if($products[0]->status == 0)

              Order placed

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

            <div id="PDForderpayment"><b>Payment Type: </b>@if($products[0]->payment_type == 0)

                Cash On Delivery

              @elseif ($products[0]->payment_type == 1) 

                Airtel Money

              @endif

            </div>   

          </div>

          <div class="col-md-4 invoice-col">

              <div id="PDFaddtitle"><b class="" style="font-size: 20px;">Delivery Address</b><br></div>

               <div id="PDFaddcontent">{{$products[0]->apartment_name}},<br></div>

              <div id="PDFaddcontent1">{{$products[0]->neighbour_hood}},<br></div>

              <div id="PDFaddcontent2">{{$products[0]->street}},<br></div>

              <div id="PDFaddcontent3">{{$products[0]->city}},<br></div>

              <div id="PDFaddcontent4">{{$products[0]->state}}@if($products[0]->pincode)-{{$products[0]->pincode}}@endif.<br></div>

              <div id="PDFaddcontent5">Phone: {{$products[0]->userphone}}<br></div>

          </div>

        </div>

        

        <div class="row">

          <div class="col-xs-12 table-responsive">

            <div id="PDFproductdetail"><b class="center" style="font-size: 20px;">Product Details</b></div>

            <div id="PDFproducttable"><table class="table table-striped" id="basic-table" >

              <thead>

                <tr>

                <th class="" style="font-size: 10px;">Product Name</th>

                <th>Variant </th>

                 <th>Qty.</th>

                <th>Subtotal </th>

                </tr>

              </thead>

              <tbody>

                @foreach ($product_details as $key => $product) 

                <tr>

                  <td>{{$product['name']}}</td>

                  <td>@php

                  $seletedatts = json_decode($product['odr_variant']);

                  $test = json_decode(json_encode($seletedatts), True);

                  if (is_array($test) || is_object($test)){

                    foreach ($test as $value){

                      unset($value['quantity']);

                      unset($value['prize']);

                      unset($value['price']);

                      unset($value['sale_price']);

                      foreach ($value as $key => $product_variant) {

                        echo  $key.':'.$product_variant;

                      }

                    }

                  }

                  @endphp</td>

                  <td>{{$product['qty']}}</td>

                  <td>{{number_format($product['total'],2,',','.')}}{{$currency->contain}}</td>

                </tr>

                @endforeach

                <tr>

                  <td></td>

                  <td></td>

                  <td ><b>Subtotal: </b></td>

                  <td>{{number_format($sum,2,',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td></td>

                  <td></td>

                  <td><b>Tax ({{$tax->contain}}%): </b></td>

                  <td>{{number_format($tax_val,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td></td>

                  <td></td>

                  <td><b>Shipping price: </b></td>

                  <td>{{number_format($price,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td class="grand-border"></td>

                  <td class="grand-border"></td>

                  <td class="grand-border"><b>Total:</b></td>

                  <td class="grand-border">@if($products[0]->total_price){{number_format($products[0]->total_price,'2',',','.')}}{{$currency->contain}}@endif</td>

                </tr>

              </tbody>

            </table>

          </div>

          </div>

        </div>

        </div>

        <div class="row invoice-info">

          <div class="col-sm-4 invoice-col">

            <b class="">Order Details</b><br>

            <br>

            <div class="col-md-6 invoice-row"><b>Order ID:</b></div><div class="col-md-6 invoice-row">{{$products[0]->order_id}}</div><br>

            <div class="col-md-6 invoice-row"><b>Customer Name:</b></div><div class="col-md-6 invoice-row">{{$products[0]->first_name}}</div><br>

            <div class="col-md-6 invoice-row"><b>Order Status:</b></div><div class="col-md-6 invoice-row">@if($products[0]->status == 0)

            Order placed

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

            <div class="col-md-6 invoice-row"><b>Payment Type:</b></div><div class="col-md-6 invoice-row">@if($products[0]->payment_type == 0)

                  Cash On Delivery

                @elseif ($products[0]->payment_type == 1) 

                  Airtel Money

                @endif

            </div>

          </div>

          <div class="col-sm-4 invoice-col">

            <b class="">Delivery Address</b><br>

            <address class="">

            {{$products[0]->apartment_name}},<br>

            {{$products[0]->neighbour_hood}},<br>

            {{$products[0]->street}},<br>

            {{$products[0]->city}},<br>

            {{$products[0]->state}}@if($products[0]->pincode)-{{$products[0]->pincode}}@endif.<br>

            Phone: {{$products[0]->userphone}}<br>

          </address>

          </div>

        </div>

        <div class="row">

          <div class="col-xs-12 table-responsive">

            <h4 class="center">Product Details</h4>

            <table class="table table-striped">

              <thead>

                <tr>

                <th class="">Product Name</th>

                <th>Variant </th>

                <th>Qty.</th>

                <th>Subtotal </th>

                </tr>

              </thead>

              <tbody>

                @foreach ($product_details as $key => $product) 

                <tr>

                  <td>{{$product['name']}}</td>

                  <td>@php

                  $seletedatts = json_decode($product['odr_variant']);

                  $test = json_decode(json_encode($seletedatts), True);

                  if (is_array($test) || is_object($test)){

                    foreach ($test as $value){

                      unset($value['quantity']);

                      unset($value['prize']);

                      unset($value['price']);

                      unset($value['sale_price']);

                      foreach ($value as $key => $product_variant) {

                        echo  $key.':'.$product_variant;

                      }

                    }

                  }

                  @endphp</td>

                  <td>{{$product['qty']}}</td>

                  <td>{{number_format($product['total'],2,',','.')}}{{$currency->contain}}</td>

                </tr>

                @endforeach

                <tr>

                  <td colspan="2"></td>

                  <td ><b>Subtotal: </b></td>

                  <td>{{number_format($sum,2,',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td colspan="2"></td>

                  <td><b>Tax ({{$tax->contain}}%): </b></td>

                  <td>{{number_format($tax_val,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td colspan="2"></td>

                  <td><b>Shipping price: </b></td>

                  <td>{{number_format($price,'2',',','.')}}{{$currency->contain}}</td>

                </tr>

                <tr>

                  <td class="grand-border" colspan="2"></td>

                  <td class="grand-border"><b>Total:</b></td>

                  <td class="grand-border">@if($products[0]->total_price){{number_format($products[0]->total_price,'2',',','.')}}{{$currency->contain}}@endif</td>

                </tr>

              </tbody>

            </table>

          </div>

        </div>

        <div class="row no-print">

        <div class="col-xs-12">

          <button type="button" id="dwn-pdf" class="btn btn-primary pull-right" style="margin-right: 5px;">

            <i class="fa fa-download"></i> Generate PDF

          </button>

        </div>

      </div>

      </div>

    </div>

  </div>

</div>

@endsection

@section('footer_scripts')



<script type="text/javascript">

 $(document).ready(function(){

    $('#dwn-pdf').click(function(){

       let doc = new jsPDF('p', 'pt', 'letter');

       doc.setFontSize(1);

       var res = doc.autoTableHtmlToJson(document.getElementById("basic-table"));

        var options = {

        margin: {

          top: 300

        },

      };

      doc.autoTable(res.columns, res.data, options);

      

      doc.fromHTML($('#PDFtitle').html(), 250, 20, {

      'width': 100000000,

      }); 

      doc.fromHTML($('#PDFordertitle').html(), 80, 100, {

      'width': 100000000,

      });

      doc.fromHTML($('#PDForderid').html(), 80, 120, {

      'width': 100000000,

      });

      doc.fromHTML($('#PDFordername').html(), 80, 135, {

      'width': 100000000,

      });

      doc.fromHTML($('#PDForderstatus').html(), 80, 150, {

      'width': 100000000,

      });

      doc.fromHTML($('#PDForderpayment').html(), 80, 165, {

      'width': 100000000,

      });

      doc.fromHTML($('#PDFaddtitle').html(), 300, 100, {

         'width': 100000000,

     });

      doc.fromHTML($('#PDFaddcontent').html(), 300, 120, {

         'width': 100000000,

     });

     doc.fromHTML($('#PDFaddcontent1').html(), 300, 135, {

         'width': 100000000,

     });

      doc.fromHTML($('#PDFaddcontent2').html(), 300, 150, {

         'width': 100000000,

     });

      doc.fromHTML($('#PDFaddcontent3').html(), 300, 165, {

         'width': 100000000,

     });

      doc.fromHTML($('#PDFaddcontent4').html(), 300, 180, {

         'width': 100000000,

     });

      doc.fromHTML($('#PDFaddcontent5').html(), 300, 195, {

         'width': 100000000,

     });

       doc.fromHTML($('#PDFproductdetail').html(), 250, 270, {

         'width': 100000000,

     });

     doc.save("order_history.pdf");

    });

  });

</script>

@endsection