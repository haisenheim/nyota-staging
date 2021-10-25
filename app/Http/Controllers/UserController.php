<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderMaster;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {

            $customers = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('role_user.role_id','=','2')
            ->count();

            $vendors = DB::table('users')
            ->join('role_user', 'role_user.user_id','=','users.id')
            ->select('users.*')
            ->where('role_user.role_id','=','3')
            ->count();

            $products = DB::table('product')
            ->join('category', 'category.id','=','product.category_id')
            ->select('product.*','category.name as category')
            ->count();

            $cart = DB::table('cart')
            ->join('product', 'product.id','=','cart.product_id')
            ->select('cart.product_id')
            ->groupBy('cart.product_id')
            ->get();
            $carts = $cart->count();

            $totalsalearrays = array();
            for($i=0; $i<=30; $i++)
            {
                $totalsalessubarray = array();
                $dates = Carbon::now()->subDays($i);
                $year = Carbon::parse($dates)->format('Y');
                $month = Carbon::parse($dates)->format('m');
                $day = Carbon::parse($dates)->format('d');
                $totaldailysales = DB::table('order_master')->whereYear('updated_at',$year)->whereMonth('updated_at',$month)->whereDay('updated_at',$day)->sum('order_master.total_price');
                $totalsalessubarray['day'] = $day;
                $totalsalessubarray['month'] = $month;
                $totalsalessubarray['year'] = $year;
                $totalsalessubarray['total'] = $totaldailysales; 
                $totalsalearrays[] = $totalsalessubarray;
            }
            $totalsalearrays = json_decode(json_encode($totalsalearrays));

            $totalmonthsalesarrays = array();
            for($i=0; $i<=11; $i++)
            {
                $totalmonthsalesubarray = array();
                $dates = Carbon::now()->subMonth($i);
                //$year = Carbon::parse($dates)->format('Y');
                $year = date('Y');
                $month = Carbon::parse($dates)->format('m');
               $totaldailysales = DB::table('order_master')->whereYear('updated_at',$year)->whereMonth('updated_at',$month)->sum('order_master.total_price');
                $totalmonthsalesubarray['month'] = $month;
                $totalmonthsalesubarray['year'] = $year;
                $totalmonthsalesubarray['total'] = $totaldailysales; 
                $totalmonthsalesarrays[] = $totalmonthsalesubarray;
            }
            $totalmonthsalesarrays = json_decode(json_encode($totalmonthsalesarrays));

            $delivery_on_earlys   = DB::select("SELECT * FROM `order_master` WHERE DATE(estimated_delivery_date) > DATE(delivery_date)");
            $delivery_on_lates    = DB::select("SELECT * FROM `order_master` WHERE DATE(estimated_delivery_date) < DATE(delivery_date)");
            $delivery_on_times    = DB::select("SELECT * FROM `order_master` WHERE DATE(estimated_delivery_date) = DATE(delivery_date)");
            $delivery_on_time     = count($delivery_on_times);
            $delivery_on_early    = count($delivery_on_earlys);
            $delivery_on_late     = count($delivery_on_lates);
            $current = OrderMaster::where('created_at', '>=', Carbon::now()->startOfWeek())
            ->where('created_at', '<=', Carbon::now()->endOfWeek())
            ->sum('total_price');
            $previos_end_date = date('Y-m-d', strtotime('last Sunday', strtotime(Carbon::now())));
            $previos_start_date = date('Y-m-d', strtotime('-6 days', strtotime($previos_end_date))); 
            $previous = DB::select("SELECT SUM(total_price) as total_price FROM `order_master`where `created_at` between '".$previos_start_date."' and '".$previos_end_date."'");
            return view('pages.admin.home', compact('customers','vendors','carts','products','totalsalearrays','totalmonthsalesarrays','delivery_on_time','delivery_on_early','delivery_on_late','current','previous'));
        }
        if($user->isvendor()){
        	$products = DB::table('product')
            ->join('category', 'category.id','=','product.category_id')
            ->select('product.*','category.name as category')
            ->where('product.user_id',$user->id)
            ->count();

            $cart = DB::table('cart')
            ->join('product', 'product.id','=','cart.product_id')
            ->select('cart.product_id')
            ->groupBy('cart.product_id')
            ->where('product.user_id',$user->id)
            ->get();
            $carts = $cart->count();

             $totalsalearrays = array();
            for($i=0; $i<=30; $i++)
            {
                $totalsalessubarray = array();
                $dates = Carbon::now()->subDays($i);
                $year = Carbon::parse($dates)->format('Y');
                $month = Carbon::parse($dates)->format('m');
                $day = Carbon::parse($dates)->format('d');
                $totaldailysales = DB::table('order_master')->join('order', 'order.order_id','=','order_master.id')->join('product', 'order.product_id','=','product.id')->whereYear('order_master.updated_at',$year)->whereMonth('order_master.updated_at',$month)->whereDay('order_master.updated_at',$day)->where('product.user_id',$user->id)->sum('order_master.total_price');
                $totalsalessubarray['day'] = $day;
                $totalsalessubarray['month'] = $month;
                $totalsalessubarray['year'] = $year;
                $totalsalessubarray['total'] = $totaldailysales; 
                $totalsalearrays[] = $totalsalessubarray;
            }
            $totalsalearrays = json_decode(json_encode($totalsalearrays));

            $totalmonthsalesarrays = array();
            for($i=0; $i<=11; $i++)
            {
                $totalmonthsalesubarray = array();
                $dates = Carbon::now()->subMonth($i);
                //$year = Carbon::parse($dates)->format('Y');
                $year = date('Y');
                $month = Carbon::parse($dates)->format('m');
               $totaldailysales = DB::table('order_master')->join('order', 'order.order_id','=','order_master.id')->join('product', 'order.product_id','=','product.id')->whereYear('order_master.updated_at',$year)->whereMonth('order_master.updated_at',$month)->where('product.user_id',$user->id)->sum('order_master.total_price');
                $totalmonthsalesubarray['month'] = $month;
                $totalmonthsalesubarray['year'] = $year;
                $totalmonthsalesubarray['total'] = $totaldailysales; 
                $totalmonthsalesarrays[] = $totalmonthsalesubarray;
            }
            $totalmonthsalesarrays = json_decode(json_encode($totalmonthsalesarrays));

            $delivery_on_earlys   = DB::select("SELECT `order_master`.* FROM `order_master` inner join `order` on `order_master`.`id` = `order`.`order_id` inner join `product` on `product`.`id` = `order`.`product_id` WHERE DATE(order_master.estimated_delivery_date) > DATE(order_master.delivery_date) AND product.user_id ='".$user->id."'");

            $delivery_on_lates    = DB::select("SELECT `order_master`.* FROM `order_master` inner join `order` on `order_master`.`id` = `order`.`order_id` inner join `product` on `product`.`id` = `order`.`product_id` WHERE DATE(order_master.estimated_delivery_date) < DATE(order_master.delivery_date) AND product.user_id ='".$user->id."'");

            $delivery_on_times    = DB::select("SELECT `order_master`.* FROM `order_master` inner join `order` on `order_master`.`id` = `order`.`order_id` inner join `product` on `product`.`id` = `order`.`product_id` WHERE DATE(order_master.estimated_delivery_date) = DATE(order_master.delivery_date) AND product.user_id ='".$user->id."'");

            $delivery_on_time     = count($delivery_on_times);
            $delivery_on_early    = count($delivery_on_earlys);
            $delivery_on_late     = count($delivery_on_lates);

            $current = DB::table('order_master')->join('order', 'order.order_id','=','order_master.id')->join('product', 'order.product_id','=','product.id')->where('order_master.created_at', '>=', Carbon::now()->startOfWeek())->where('order_master.created_at', '<=', Carbon::now()->endOfWeek())->where('product.user_id',$user->id)->sum('total_price');

            $previos_end_date = date('Y-m-d', strtotime('last Sunday', strtotime(Carbon::now())));
            $previos_start_date = date('Y-m-d', strtotime('-6 days', strtotime($previos_end_date))); 
            $previous = DB::select("SELECT SUM(order_master.total_price) as total_price FROM `order_master` inner join `order` on `order_master`.`id` = `order`.`order_id` inner join `product` on `product`.`id` = `order`.`product_id` where order_master.`created_at` between '".$previos_start_date."' and '".$previos_end_date."' AND product.user_id ='".$user->id."'");
        
            return view('pages.vendor.home',compact('carts','products','totalsalearrays','totalmonthsalesarrays','delivery_on_time','delivery_on_early','delivery_on_late','current','previous'));
        }
    }
    public function search($year)
    {
    		$totalmonthsalesarrays = array();
            for($i=0; $i<=11; $i++)
            {
                $totalmonthsalesubarray = array();
                $dates = Carbon::now()->subMonth($i);
                $year = $year;
                $month = Carbon::parse($dates)->format('m');
               $totaldailysales = DB::table('order_master')->whereYear('updated_at',$year)->whereMonth('updated_at',$month)->sum('order_master.total_price');
                $totalmonthsalesubarray['month'] = $month;
                $totalmonthsalesubarray['year'] = $year;
                $totalmonthsalesubarray['total'] = $totaldailysales; 
                $totalmonthsalesarrays[] = $totalmonthsalesubarray;
            }
            $totalmonthsalesarrays = json_decode(json_encode($totalmonthsalesarrays));
            $html = '<div id="chartmonthlyContainer" style="height: 300px; width: 100%;"></div>';
            $html .= '<script type="text/javascript">var charts = new CanvasJS.Chart("chartmonthlyContainer", {
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
		    name: "Total Sales",
		    markerType: "square",
		    xValueFormatString: "MMM, YYYY",
		    color: "#00c0ef",
		    dataPoints: [';
		     foreach($totalmonthsalesarrays as $totalmonthsalesarray){
		     	$html .=  '{ x: new Date(';
				$html .= $totalmonthsalesarray->year;
		     	$html .=  ',';
		     	$html .= $totalmonthsalesarray->month-1;
		     	$html .= '), y:';
		     	$html .= $totalmonthsalesarray->total; 
		     	$html .= '},';
		      }
   			$html .=  ']},]});charts.render();</script>';

   			$html .= '<script>
						function toogleDataSeries(e){
  						if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
    						e.dataSeries.visible = false;
  						} else{
    						e.dataSeries.visible = true;
  						}
  						charts.render();
  						}
						</script>';
         		echo $html;
    }
    public function vendorsearch($year){
    	$user = Auth::user();
    	$totalmonthsalesarrays = array();
        for($i=0; $i<=11; $i++)
        {
            $totalmonthsalesubarray = array();
            $dates = Carbon::now()->subMonth($i);
            $year = $year;
            $month = Carbon::parse($dates)->format('m');
           $totaldailysales = DB::table('order_master')->join('order', 'order.order_id','=','order_master.id')->whereYear('order_master.updated_at',$year)->whereMonth('order_master.updated_at',$month)->where('order.user_id',$user->id)->sum('order_master.total_price');
            $totalmonthsalesubarray['month'] = $month;
            $totalmonthsalesubarray['year'] = $year;
            $totalmonthsalesubarray['total'] = $totaldailysales; 
            $totalmonthsalesarrays[] = $totalmonthsalesubarray;
        }
        $totalmonthsalesarrays = json_decode(json_encode($totalmonthsalesarrays));
        $html = '<div id="chartmonthlyContainer" style="height: 300px; width: 100%;"></div>';
        $html .= '<script type="text/javascript">var charts = new CanvasJS.Chart("chartmonthlyContainer", {
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
	    name: "Total Sales",
	    markerType: "square",
	    xValueFormatString: "MMM, YYYY",
	    color: "#00c0ef",
	    dataPoints: [';
	    foreach($totalmonthsalesarrays as $totalmonthsalesarray){
	     	$html .=  '{ x: new Date(';
			$html .= $totalmonthsalesarray->year;
	     	$html .=  ',';
	     	$html .= $totalmonthsalesarray->month-1;
	     	$html .= '), y:';
	     	$html .= $totalmonthsalesarray->total; 
	     	$html .= '},';
	    }
		$html .=  ']},]});charts.render();</script>';

		$html .= '<script>
				function toogleDataSeries(e){
					if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
					e.dataSeries.visible = false;
					} else{
					e.dataSeries.visible = true;
					}
					charts.render();
					}
				</script>';
 		echo $html;
    }
}
