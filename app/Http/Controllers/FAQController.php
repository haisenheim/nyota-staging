<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use App\Models\User;
use App\Traits\CaptureIpTrait;
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

class FAQController extends Controller
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
             $faqs = DB::table('faq')
            ->select('faq.*')
            ->paginate(100);
        return View('pages.admin.FAQmanagement.show', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.FAQmanagement.create');
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
        $user = Auth::user();
        $validator = Validator::make($request->all(), 
        [
            
            'question'  => 'required',
            'answer'    => 'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user = FAQ::create([
            'question'       => $request->input('question'),
            'answer'         => $request->input('answer'),
            'language_id'    => 1,
            
                   
         ]);
        return redirect('admin/faq')->with('success', 'Record insert successfully');
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
       $faq = FAQ::where("id",$id)->first();
        return view('pages.admin.FAQmanagement.edit',compact('faq'));
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
		
            $validator = Validator::make($request->all(), 
            [
                'question'  => 'required',
            ]
        );
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
         $datatwo = array(
            'question'        => $request->input('question'),
            'answer'          => $request->input('answer'),
         );
        $vendor= FAQ::where('id',$id)->update($datatwo);
        return redirect('admin/faq');
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
        $faq       = DB::table('faq')->where('id',$id)->delete();
        return redirect()->back();
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
       $searchTerm = $request->input('search_box');
        $faqs = DB::table('faq')
            ->select('faq.*')
            ->where('faq.question','like','%'. $searchTerm.'%')
            ->paginate(100);
     return view('pages.admin.FAQmanagement.show',compact('faqs'));
    }
    
}
