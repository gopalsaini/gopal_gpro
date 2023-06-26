<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
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
      
      public function index() {

          $total_applications = \App\Models\User::where([['stage', '1'], ['profile_status', 'Review']])->where(function ($query) {
            $query->where('added_as',null)
              ->orWhere('added_as', '=', 'Group');
          })->count();

          $total_rejections = \App\Models\User::where([['stage', '1'], ['profile_status', 'Rejected']])
          ->where(function ($query) {
            $query->where('added_as',null)
              ->orWhere('added_as', '=', 'Group');
          })->count();

          $total_revenue = \App\Models\Wallet::where([['type', 'Cr'], ['status', 'Success']])->sum('amount');
          \App\Helpers\commonHelper::setLocale();

          $chartData = \App\Models\ChartDataDB::where('id','1')->first();

          return view('admin.dashboard', compact('total_applications', 'total_rejections', 'total_revenue','chartData'));
      }

      public function getPayments(){
        
          $chartData = \App\Models\ChartDataDB::where('id','1')->first();

          return response($chartData->getPayments);
      }

      // public function getUserByCountry(){
    
      
      //     $country = [];

      //     $userCountry = \App\Models\User::select('users.*','countries.name as cname','countries.id as cId')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2]])->join('countries','users.citizenship','=','countries.id')->groupBy('countries.id')->get();
          
      //     if(!empty($userCountry)){

      //       foreach($userCountry as $countryData){

      //             $country[$countryData->cname] = \App\Models\User::where([['user_type', '!=', '1'],['citizenship', $countryData->cId]])->count();

      //       }

      //     }
      //     return response()->json($country);
      // }
      

    public function getUserByUserAge(){
  
    
      $stages = [["Element", "No. of Users" ]];
      $stages[] = array(
        
        'Under 30 years Age',\App\Models\User::whereDate('dob', '>=', date('Y-m-d', strtotime('-29 years')))->where([['user_type', '!=', '1'], ['designation_id', 2]])->count()
        
        
      );
      $stages[] = array(
        
        '30-50 years Age',\App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])
                          
                              ->whereDate('dob', '<=', date('Y-m-d', strtotime('-29 years')))
                              ->whereDate('dob', '>=', date('Y-m-d', strtotime('-50 years')))
                              ->count(),
      );
      $stages[] = array(
        
        '50+ years Age',\App\Models\User::whereDate('dob', '<=', date('Y-m-d', strtotime('-50 years')))->where([['user_type', '!=', '1'], ['designation_id', 2]])->count(),
        
      );

      return response()->json($stages);
    }

    public function getUserByContinents(){

        $chartData = \App\Models\ChartDataDB::where('id','1')->first();
        
        return response($chartData->getUserByContinents);
    }
    

    public function getStages(){
    
      $chartData = \App\Models\ChartDataDB::where('id','1')->first();

      return response($chartData->getStages);
    }

    public function getPaymentChartAjax(){
    
      $totalPendingAmount = 0;

      $results = \App\Models\User::where('profile_status','Approved')->where('stage','2')->get(); 
      if($results){

          foreach($results as $val){
              $totalPendingAmount +=\App\Helpers\commonHelper::getTotalPendingAmount($val->id);
          }
      }
      
      $stages = array(
              'Pending' => round($totalPendingAmount),
              'Declined' => round(\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Failed']])->sum('amount')),
              'In Process' => round(\App\Models\Transaction::where([['status', '=', Null]])->sum('amount')),
              'Accepted' => round(\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Success']])->sum('amount')),
              
      );

      return response()->json($stages);
    }

    public function getPaymentTypeChartAjax(){
    
      $chartData = \App\Models\ChartDataDB::where('id','1')->first();

      return response($chartData->getPaymentTypeChartAjax);
    }

    public function getGroupRegisteredChartAjax(){
    
        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->getGroupRegisteredChartAjax);
    }

      
    public function getSingleMarriedWSChartAjax(){
    
        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->getSingleMarriedWSChartAjax);
    }

    public function getMarriedWSChartAjax(){

        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->getMarriedWSChartAjax);
    }

      
    public function getPastoralTrainersChartAjax(){
        
        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->getPastoralTrainersChartAjax);
    }

    public function localization(Request $request) {

      if($request->ajax() && $request->isMethod('post')){
          \Session::put('lang', $request->post('lang'));
              return response(array('reload' => true), 200);
          }
    }

    public function getDoYouSeekPastoralTraining(){
        
        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->getDoYouSeekPastoralTraining);
    }

    
    public function TotalGroupRegistration(){
          
        $chartData = \App\Models\ChartDataDB::where('id','1')->first();

        return response($chartData->TotalGroupRegistration);
    }


      public function TotalMarriedCouples(){

          $chartData = \App\Models\ChartDataDB::where('id','1')->first();

          return response($chartData->TotalMarriedCouples);
      }

      public function SingleMarriedComing(){
        
          $chartData = \App\Models\ChartDataDB::where('id','1')->first();

          return response($chartData->SingleMarriedComing);
      }

    

    

}
