<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class Dashboard2Controller extends Controller
{
        public function index2() {

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

            return view('admin.dashboard2', compact('total_applications', 'total_rejections', 'total_revenue'));
        }

        public function getPayments(){
      
        $prices = array(
          'Fully Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '1'],['profile_status', 'Approved']])->count() ?? 0,
          'Partially Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '0'],['profile_status', 'Approved']])->count() ?? 0
        );

        return response()->json($prices);
      }

      public function getUserByCountry(){
    
      
          $country = [];

          $userCountry = \App\Models\User::select('users.*','countries.name as cname','countries.id as cId')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2],['users.profile_status', 'Approved']])->join('countries','users.citizenship','=','countries.id')->groupBy('countries.id')->get();
          
          if(!empty($userCountry)){

            foreach($userCountry as $countryData){

                  $country[$countryData->cname] = \App\Models\User::where([['user_type', '!=', '1'],['citizenship', $countryData->cId],['profile_status', 'Approved']])->count();

            }

          }
          

          return response()->json($country);
      }

      public function getUserByUserAge(){
    
      
        $stages = [["Element", "No. of Users" ]];
        $stages[] = array(
          
          'Under 30 years Age',\App\Models\User::whereDate('dob', '>=', date('Y-m-d', strtotime('-29 years')))->where([['user_type', '!=', '1'], ['designation_id', 2],['profile_status', 'Approved']])->count()
          
          
        );
        $stages[] = array(
          
          '30-50 years Age',\App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2],['profile_status', 'Approved']])
                            
                                ->whereDate('dob', '<=', date('Y-m-d', strtotime('-29 years')))
                                ->whereDate('dob', '>=', date('Y-m-d', strtotime('-50 years')))
                                ->count(),
        );
        $stages[] = array(
          
          '50+ years Age',\App\Models\User::whereDate('dob', '<=', date('Y-m-d', strtotime('-51 years')))->where([['user_type', '!=', '1'], ['designation_id', 2]])->count(),
          
        );

        return response()->json($stages);
      }

      public function getUserByContinents(){
    
      
          $country = [["Element", "No. of Users" ]];

          $userCountry = \App\Models\User::select('users.*','countries.region as cname','countries.id as cId')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2],['users.profile_status', 'Approved']])->join('countries','users.citizenship','=','countries.id')->orderBy('countries.region','Asc')->groupBy('countries.region')->get();
          
          if(!empty($userCountry)){

            foreach($userCountry as $countryData){

                  
                  $country[]=[$countryData->cname,
                              \App\Models\User::where([['user_type', '!=', '1'],['citizenship', $countryData->cId], ['designation_id', 2],['profile_status', 'Approved']])->count()
                            ];
            }


          }
          
          return response()->json($country);
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
      
        $stages = array(
                'Credit/Debit Card' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'Card']])->count(),
                'Western Union' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'WU']])->count(),
                'Money Gram' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'MG']])->count(),
                'Bank Wire Transfer' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'Wire']])->count(),
        );

        return response()->json($stages);
      }

      public function getGroupRegisteredChartAjax(){
      
            $totalGroup1 = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'],['profile_status', 'Approved']])->groupBy('parent_id')->count();
            $totalGroup = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'],['profile_status', 'Approved']])->get();
            $totalUser = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'],['profile_status', 'Approved']])->count();
           
            if(!empty($totalGroup) && count($totalGroup)>0){
              $array =[];

                foreach($totalGroup as $key=>$Groups){

                  $array[$key]= $Groups->parent_id;

              }

              $array = count(array_unique($array));

            }

            $stages = array(
                  'Total Group' => $array,
                  'Total Group candidates' => $totalUser+$array,
            );

            return response()->json($stages);
      }

      
      public function getSingleMarriedWSChartAjax(){
      
            $stages = array(
                  'Single' => \App\Models\User::where([['designation_id', 2], ['room', 'Single'], ['profile_status', 'Approved']])->count(),
                  'Twin Sharing' => \App\Models\User::where([['designation_id', 2], ['room', 'Sharing'], ['profile_status', 'Approved']])->orWhere('room','Twin Sharing Deluxe Room')->count(),
                  'Suite' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Suite'], ['profile_status', 'Approved']])->count(),
                  'Club Floor' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Club Floor'], ['profile_status', 'Approved']])->count(),
                  'Double Deluxe' => \App\Models\User::where([['designation_id', 2],['added_as', 'Spouse'], ['room', null],['profile_status', 'Approved']])->count(),
            );

            return response()->json($stages);
      }

      public function getMarriedWSChartAjax(){
      
            $Singles = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['marital_status', 'Married'],['profile_status', 'Approved']])->get();
            if(!empty($Singles) && count($Singles)>0){

              $BothTotal = 0; $singleTotal = 0;$nonTrainerCount = 0;
                foreach($Singles as $Single){

                    $user = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['parent_id', $Single->id],['profile_status', 'Approved']])->first();

                    if($user && $user->ministry_pastor_trainer == 'Yes' && $Single->ministry_pastor_trainer == 'Yes'){

                            $BothTotal++;

                    }else if($Single->ministry_pastor_trainer == 'Yes'){

                        if($user && $user->ministry_pastor_trainer == 'No' ){

                            $singleTotal++;
                        }
                          
                    }else if($Single->ministry_pastor_trainer == 'No'){
                      
                      if($user && $user->ministry_pastor_trainer == 'Yes' ){

                          $singleTotal++;
                      }
                      

                    }else{

                            $nonTrainerCount++;

                    }
                }

            }

            $stages = array(
                  'Pastoral Trainer - Both' => $BothTotal,
                  'Aspirational Trainer- Both' => $singleTotal,
                  'Pastoral Trainer and Aspirational Trainer' => $nonTrainerCount,
                  'Pastoral Trainer and Not a Trainer' => $nonTrainerCount,
                  'Aspirational Trainer and Not a Trainer' => $nonTrainerCount,
                  'Not Trainers - Both' => $nonTrainerCount,
            );

            return response()->json($stages);
      }

      
      public function getPastoralTrainersChartAjax(){
          
            $Pastoral = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'Yes'], ['profile_status', 'Approved']])->count();
            
            $yes = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['doyouseek_postoral', 'Yes'], ['profile_status', 'Approved']])->count();
            
            $no = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['doyouseek_postoral', 'No'], ['profile_status', 'Approved']])->count();
            
            $stages = array(
                  'Pastoral Trainer' => $Pastoral,
                  'Aspirational trainer' => $yes,
                  'Not a Trainer' => $no,
            );

            return response()->json($stages);
      }

      public function localization(Request $request) {

        if($request->ajax() && $request->isMethod('post')){
            \Session::put('lang', $request->post('lang'));
                return response(array('reload' => true), 200);
            }
      }

    //   public function getDoYouSeekPastoralTraining(){
          
    //     $yes = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'No'],  ['doyouseek_postoral', 'Yes']])->count();
        
    //     $no = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'No'], ['doyouseek_postoral', 'No']])->count();
        
    //     $stages = array(
    //           'Yes' => $yes,
    //           'No' => $no,
    //     );

    //     return response()->json($stages);
    // }
}
