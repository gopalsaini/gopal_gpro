<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class Dashboard2Controller extends Controller
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

            return view('admin.dashboard2', compact('total_applications', 'total_rejections', 'total_revenue'));
        }

        public function getPayments(){
          
            $prices = array(
              'Fully Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '1']])->count() ?? 0,
              'Partially Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '0']])->count() ?? 0
            );

            return response()->json($prices);
        }

      public function getUserByCountry(){
    
          $country = [["Element", "No. of Users" ]];
          
          $userCountry = \App\Models\User::selectRaw('count(*) as count, region')
              ->where([['users.user_type', '!=', '1'], ['users.designation_id', 2],['users.stage','>=', '2']])
              ->join('countries','users.citizenship','=','countries.id')
              ->groupBy('countries.region')
              ->orderBy('countries.region', 'ASC')
              ->get();
      
          if(!empty($userCountry)){
              foreach($userCountry as $countryData){
                  $totalCon = $countryData->count;
                  $country[] = [$countryData->region, $totalCon];
              }
          }


          return response()->json($country);
      }

      public function getUserByUserAge(){
    
      
        $stages = [["Element", "No. of Users" ]];
        $stages[] = array(
          
          'Under 30 years Age',\App\Models\User::whereDate('dob', '>=', date('Y-m-d', strtotime('-29 years')))->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage','>=', '2']])->count()
          
          
        );
        $stages[] = array(
          
          '30-50 years Age',\App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage','>=', '2']])
                            
                                ->whereDate('dob', '<=', date('Y-m-d', strtotime('-29 years')))
                                ->whereDate('dob', '>=', date('Y-m-d', strtotime('-50 years')))
                                ->count(),
        );
        $stages[] = array(
          
          '50+ years Age',\App\Models\User::whereDate('dob', '<=', date('Y-m-d', strtotime('-50 years')))->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage','>=', '2']])->count(),
          
        );

        return response()->json($stages);
      }

      public function getUserByContinents(){
    
      
          $country = [["Element", "No. of Users" ]];

          $userCountry = \App\Models\User::select('users.*','countries.region as cname','countries.id as cId')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2], ['users.stage','>=', '2']])->join('countries','users.citizenship','=','countries.id')->orderBy('countries.region','Asc')->groupBy('countries.region')->get();
          
          if(!empty($userCountry)){

            foreach($userCountry as $countryData){
              $totalCon=0;
              $userCountryData = \App\Models\User::select('users.*','countries.name as cname','countries.id as cId','countries.region as region')->where([['users.user_type', '!=', '1'], ['users.designation_id', 2], ['users.stage','>=', '2'], ['countries.region', $countryData->cname]])->join('countries','users.citizenship','=','countries.id')->groupBy('countries.id')->get();
              foreach($userCountryData as $key=>$val){
                  $totalCon+= \App\Models\User::where([['user_type', '!=', '1'], ['stage','>=', '2'],['citizenship', $val->cId]])->count();
              }                                         
              $country[]=[$countryData->cname,
                          $totalCon
                        ];
        }



          }
          
          return response()->json($country);
      }

      public function getStages(){
      
        $stages = array(
                'stage0' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '0']])->count(),
                'stage1' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1']])
                            ->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count(),
                'stage2' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])->where(function ($query) {
                              $query->where('added_as',null)
                                ->orWhere('added_as', '=', 'Group');
                            })->count(),
                'stage3' => \App\Models\User::with('TravelInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '3']])->count(),
                'stage4' => \App\Models\User::with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '4']])->count(),
                'stage5' => \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '5']])->count(),
                
        );

        return response()->json($stages);
      }

      public function getPaymentChartAjax(){
      
        $totalPendingAmount = 0;

        $results = \App\Models\User::where('profile_status','Approved')->where('stage','>=','2')->get(); 
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
                'MG' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'MG']])->count(),
                'RIA' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'RIA']])->count(),
                'Bank Wire Transfer' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'Wire']])->count(),
        );

        return response()->json($stages);
      }

      public function getGroupRegisteredChartAjax(){
      
            $totalGroup1 = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'], ['stage','>=', '2']])->groupBy('parent_id')->count();
            $totalGroup = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'], ['stage','>=', '2']])->get();
            $totalUser = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'], ['stage','>=', '2']])->count();
           
            if(!empty($totalGroup) && count($totalGroup)>0){
              $array =[];

                foreach($totalGroup as $key=>$Groups){

                  $array[$key]= $Groups->parent_id;

              }

              $array = count(array_unique($array));

            }

            $stages = array(
                  'Total Group' => $array,
                  'Total Group Candidates' => $totalUser+$array,
            );

            return response()->json($stages);
      }

      
      public function getSingleMarriedWSChartAjax(){
      
            $stages = array(
                  'Single' => \App\Models\User::where([['room', 'Single'], ['stage','>=', 2]])->count(),
                  'Twin Sharing' => \App\Models\User::where([['designation_id', 2], ['room', 'Sharing'], ['stage','>=', '2']])->orWhere('room','Twin Sharing Deluxe Room')->count(),
                  'Suite' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Suite'], ['stage','>=', '2']])->count(),
                  'Club Floor' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Club Floor'], ['stage','>=', '2']])->count(),
                  'Double Deluxe' => \App\Models\User::where([['designation_id', 2],['added_as', 'Spouse'], ['room', null], ['stage','>=', '2']])->count(),
            );

            return response()->json($stages);
      }

      public function getMarriedWSChartAjax(){
      
            $Singles = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage','>=', '2']])->get();
            if(!empty($Singles) && count($Singles)>0){

              $BothTotal = 0; $singleTotal = 0;$nonTrainerCount = 0; $AspirationalBothTotal = 0;  $PastoralAndAspirational = 0; $singleAspirationalTotal=0;
                foreach($Singles as $Single){

                    $user = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['stage','>=', '2'], ['parent_id', $Single->id]])->first();
                    
                    
                    if($user && $user->ministry_pastor_trainer == 'Yes' && $Single->ministry_pastor_trainer == 'Yes'){

                            $BothTotal++;

                    }else if($user && $user->doyouseek_postoral == 'Yes' && $Single->doyouseek_postoral == 'Yes'){

                        $AspirationalBothTotal++;

                    }else if($user && $user->ministry_pastor_trainer == 'Yes' && $Single->doyouseek_postoral == 'No'){

                        $PastoralAndAspirational++;

                    }else if($user && $Single->ministry_pastor_trainer == 'No' && $user->doyouseek_postoral == 'Yes'){

                        $PastoralAndAspirational++;

                    }else if($Single->ministry_pastor_trainer == 'Yes'){

                        if($user && $user->ministry_pastor_trainer == 'No'){

                            $singleTotal++;
                        }
                          
                    }else if($Single->ministry_pastor_trainer == 'No'){
                      
                      if($user && $user->ministry_pastor_trainer == 'Yes' ){

                          $singleTotal++;

                      }
                      

                    }else if($Single->doyouseek_postoral == 'Yes'){

                        if($user && $user->doyouseek_postoral == 'No' ){

                            $singleAspirationalTotal++;
                        }
                          
                    }else if($Single->doyouseek_postoral == 'No'){
                      
                      if($user && $user->doyouseek_postoral == 'Yes' ){

                          $singleAspirationalTotal++;
                      }
                      
                    }else{

                        $nonTrainerCount++;

                    }
                }

            }

            $stages = array(
                  'Pastoral Trainer - Both' => $BothTotal,
                  'Aspirational Trainer- Both' => $AspirationalBothTotal,
                  'Pastoral Trainer and Aspirational Trainer' => $PastoralAndAspirational,
                  'Pastoral Trainer and Not a Trainer' => $singleTotal,
                  'Aspirational Trainer and Not a Trainer' => $singleAspirationalTotal,
                  'Not Trainers - Both' => $nonTrainerCount,
            );

            return response()->json($stages);
      }

      
      public function getPastoralTrainersChartAjax(){
        
            $Pastoral = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'Yes'], ['stage','>=', '2']])->count();
            
            $yes = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['doyouseek_postoral', 'Yes'], ['stage', '>=','2']])->count();
            $no = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['doyouseek_postoral', 'No'], ['stage','>=', '2']])->count();
            
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

    
    public function TotalGroupRegistration(){
          
          $totalGroup = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'], ['stage','>=', '2']])->get();
          $totalUser = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group'], ['stage','>=', '2']])->count();
        
          if(!empty($totalGroup) && count($totalGroup)>0){
            $array =[];

              foreach($totalGroup as $key=>$Groups){

                $array[$key]= $Groups->parent_id;

            }

            $array = count(array_unique($array));

          }

          $stages = array(
                'Total Group' => $array,
                'Total Group Candidates' => $totalUser+$array,
          );

          return response()->json($stages);
    }


      public function TotalMarriedCouples(){
        
        
        $stages = array(
              'Total Married couples' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['stage','>=', '2']])->count(),
              'Total Married Candidates' => (\App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['stage','>=', '2']])->count())*2,
        );

        return response()->json($stages);
      }


    public function SingleMarriedComing(){
      
      $Singles = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2],['marital_status', 'Married'], ['stage','>=', '2']])->get();
      if(!empty($Singles) && count($Singles)>0){

        $MarriedTotal = 0; $singleTotal = 0;
          foreach($Singles as $Single){

              $user = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '>=','2'], ['added_as', 'Spouse'], ['parent_id', $Single->id]])->first();

              if(!$user && $Single->marital_status == 'Married'){

                  $singleTotal++;

              }
          }

      }

      $stages = array(
            'Single' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['marital_status', 'Unmarried'], ['stage','>=', '2']])->count(),
            'Married coming without Spouse' => $singleTotal,
      );

      return response()->json($stages);
}
}
