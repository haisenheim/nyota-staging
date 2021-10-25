@extends('layouts.admin.app')

@section('template_title')
    Welcome
@endsection

@section('head')
@endsection

@section('content')
       {{-- <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-gear-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">CPU Traffic</span>
              <span class="info-box-number">90<small>%</small></span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-google-plus"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Likes</span>
              <span class="info-box-number">41,410</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Sales</span>
              <span class="info-box-number">760</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">New Members</span>
              <span class="info-box-number">2,000</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>--}}
      <div class="row das-color">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{$vendors}}</h3>

                <p>Vendors</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-people-outline"></i>
              </div>
              <a href="{{url('admin/vendors/')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{$customers}}</h3>

                <p>Customers</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{url('admin/user/')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6 ">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{$products}}</h3>

                <p>Products</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{url('admin/products/')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6 das-color">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{$carts}}</h3>

                <p>Products In Basket</p>
              </div>
              <div class="icon">
                <i class="ion ion-ios-cart-outline"></i>
              </div>
              <a href="{{url('admin/cart/')}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <!-- ./col -->
          
          <!-- ./col -->
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Daily Sales</h3>
                <div class="box-tools pull-right">
                  
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive" style="overflow-x:unset;">
                  <div id="chartContainer" style="height: 300px; width: 100%;"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Monthly Sales</h3>
                <div class="box-tools pull-right">
                 <select name = "year" class="form-control" id="year">
                      @for ($year = 1995; $year <= date('Y') ; $year++) 
                      <option value = "{{ $year }}" {{ $year == date('Y') ? 'selected="selected"' : '' }}>{{ $year }}</option>
                      @endfor
                  </select>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive" id="yearcontainer" style="overflow-x:unset;">
                  <div id="chartmonthlyContainer" style="height: 300px; width: 100%;"></div>
                </div>
              </div>
            </div>
          </div>
		   </div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Deliveries</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="chart-responsive">
                    <canvas id="pieChart" height="200"></canvas>
                  </div>
                  <!-- ./chart-responsive -->
                </div>
                <!-- /.col -->
                <div class="col-md-4">
                  <ul class="chart-legend clearfix">
                    <li><i class="fa fa-circle-o text-red"></i> Delivery On Time</li>
                    <li><i class="fa fa-circle-o text-green"></i> Delivery On Early</li>
                    <li><i class="fa fa-circle-o text-yellow"></i> Delivery On Late</li>
                  </ul>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.box-body -->
            <!-- /.footer -->
			</div>
			</div>	
			
			<div class="col-md-6">
        <div class="box box-info">
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-right">
              <li><a href="#previous" data-toggle="tab">Preivous Week</a></li>
              <li class="active"><a href="#current" data-toggle="tab">Current Week</a></li>
              <li class="pull-left header"> Weekly Sales</li>
            </ul>
           
            <div class="tab-content no-padding">
              <!-- Morris chart - Sales -->
              <div class="box-body tab-pane fade in active" id="current">
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-number">{{$current}}</span>
                      <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
              <div class="box-body tab-pane" id="previous">
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                  <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-credit-card"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-number">@if(!empty($previous[0]->total_price)){{$previous[0]->total_price}}@else 0 @endif</span>
                      <div class="progress">
                      <div class="progress-bar" style="width: 100%"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
              <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>
            </div>
          </div>
        </div>
			</div>	
			
		</div>
		
  @endsection
  @section('footer_scripts')   
  
<script src="{{ asset('/public/js/canvasjs.min.js') }}"></script>  
<script src="{{ asset('/public/bower_components/chart.js/Chart.js') }}"></script>
<script type="text/javascript">
  window.onload = function () {
var chart = new CanvasJS.Chart("chartContainer", {
  animationEnabled: true,
  theme: "light2",
  title:{
    text: ""
  },
  axisX:{
    valueFormatString: "DD MMM",
    crosshair: {
      enabled: true,
      snapToDataPoint: true
    }
  },
  axisY: {
    title: "Number of Sales",
    crosshair: {
      enabled: true
    }
  },
  toolTip:{
    shared:true
  },  
  legend:{
    cursor:"pointer",
    verticalAlign: "bottom",
    horizontalAlign: "left",
    dockInsidePlotArea: true,
    itemclick: toogleDataSeries
  },
  data: [{
    type: "line",
    showInLegend: true,
    name: "Total Sale",
    markerType: "square",
    xValueFormatString: "DD MMM, YYYY",
    color: "#00c0ef",
    dataPoints: [
      @foreach($totalsalearrays as $totalsalesarrays)
       { x: new Date({{ $totalsalesarrays->year  }}, {{ $totalsalesarrays->month-1  }}, {{ $totalsalesarrays->day  }}), y: {{ $totalsalesarrays->total  }} },
      @endforeach
    ]
  },
  ]
});
var charts = new CanvasJS.Chart("chartmonthlyContainer", {
  animationEnabled: true,
  theme: "light2",
  title:{
    text: ""
  },
  axisX:{
    valueFormatString: "MMM",
    crosshair: {
      enabled: true,
      snapToDataPoint: true
    }
  },
  axisY: {
    title: "Number of Sales",
    crosshair: {
      enabled: true
    }
  },
  toolTip:{
    shared:true
  },  
  legend:{
    cursor:"pointer",
    verticalAlign: "bottom",
    horizontalAlign: "left",
    dockInsidePlotArea: true,
    itemclick: toogleDataSeries
  },
  data: [{
    type: "line",
    showInLegend: true,
    name: "Total Sale",
    markerType: "square",
    xValueFormatString: "MMM, YYYY",
    color: "#00c0ef",
    dataPoints: [
      @foreach($totalmonthsalesarrays as $totalmonthsalesarray)
       { x: new Date({{ $totalmonthsalesarray->year  }}, {{ $totalmonthsalesarray->month-1  }}), y: {{ $totalmonthsalesarray->total  }} },
      @endforeach
    ]
  },
  ]
});
chart.render();
charts.render();
}


var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
  var pieChart       = new Chart(pieChartCanvas);
  var PieData        = [
    {
      value    : {{$delivery_on_time}},
      color    : '#f56954',
      highlight: '#f56954',
      label    : 'Delivery On Time'
    },
    {
      value    : {{$delivery_on_early}},
      color    : '#00a65a',
      highlight: '#00a65a',
      label    : 'Delivery On Early'
    },
    {
      value    : {{$delivery_on_late}},
      color    : '#f39c12',
      highlight: '#f39c12',
      label    : 'Delivery On Late'
    },
    
  ];
  var pieOptions     = {
    // Boolean - Whether we should show a stroke on each segment
    segmentShowStroke    : true,
    // String - The colour of each segment stroke
    segmentStrokeColor   : '#fff',
    // Number - The width of each segment stroke
    segmentStrokeWidth   : 1,
    // Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    // Number - Amount of animation steps
    animationSteps       : 100,
    // String - Animation easing effect
    animationEasing      : 'easeOutBounce',
    // Boolean - Whether we animate the rotation of the Doughnut
    animateRotate        : true,
    // Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale         : false,
    // Boolean - whether to make the chart responsive to window resizing
    responsive           : true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio  : false,
    // String - A legend template
    legendTemplate       : '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<segments.length; i++){%><li><span style=\'background-color:<%=segments[i].fillColor%>\'></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>',
    // String - A tooltip template
    tooltipTemplate      : '<%=value %> <%=label%>'
  };

pieChart.Doughnut(PieData, pieOptions);
</script>

<script type="text/javascript">
  $(document).ready(function() {
$('#year').change(function(){
    var year = $(this).val();
    if(year){
        $.ajax({
           type:"GET",
           url:"{{URL::to('admin/home/get-chart-list/')}}/"+year,
           success:function(data){  
            $("#yearcontainer").html(data);
            }
        });
    }
    else{
       // $("#state").empty();
        
    }      
   });
});
</script>
<script>
function toogleDataSeries(e){
  if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
    e.dataSeries.visible = false;
  } else{
    e.dataSeries.visible = true;
  }
  charts.render();
  chart.render();
}
</script>
<script>
const sb = new SendBird({ appId : 'b830f3bd7c9f82a260fc7c1b55988e028b3b6ca3' });
const channelHandler = new sb.ChannelHandler();
channelHandler.onMessageReceived = (channel, message) => {
    // message received
};
sb.addChannelHandler(handlerId, channelHandler);
sb.connect(userId, (user, err) => {
    // get channel
    sb.GroupChannel.getChannel(channelUrl, (channel, err) => {
      // send message
      channel.sendUserMessage(message, (message, err) => {
          // message sent
      });
    });
});
</script>
@endsection
