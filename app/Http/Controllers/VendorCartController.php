<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Auth;
use File;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Routing\UrlGenerator;
use jeremykenedy\LaravelRoles\Models\Role;
use Validator;
use Image;

class VendorCartController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $carts = DB::table('cart')
            ->join('product', 'product.id','=','cart.product_id')
            ->select('cart.*','product.name as product_name', DB::raw('COUNT(cart.product_id) as count'))
            ->groupBy('cart.product_id')
            ->where('product.user_id',$user->id)
            ->paginate(100);
        
        return View('pages.vendor.cart.show', compact('carts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    /**
     * Method to search the users.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->all();
        $searchTerm = $request->input('search_box');
        $carts = DB::table('cart')
            ->join('product', 'product.id','=','cart.product_id')
            ->select('cart.*','product.name as product_name', DB::raw('COUNT(cart.product_id) as count'))
            ->where(function ($query) use ($searchTerm) {
        $query->where('product.name', 'like','%'. $searchTerm.'%');
          })
        ->groupBy('cart.product_id')
        ->where('product.user_id',$user->id)
        ->paginate(100);
        return view('pages.vendor.cart.show',compact('carts'));
    }

    
    
}
