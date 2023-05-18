<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class PricingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
 
    public function index(Request $request){

        
        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        
        $pricing = \App\Models\Pricing::orderBy('country_name', 'asc')->get();
        \App\Helpers\commonHelper::setLocale();

        if (date('d-m-Y') === '01-06-2023') {  

            return view('pricing', compact('pricing'));

        } else {

            return view('pricing2', compact('pricing'));
        }

    }
}
