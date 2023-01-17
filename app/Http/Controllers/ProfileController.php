<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use Mail;
use Validator;
use Newsletter;
use Socialite;

class ProfileController extends Controller
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

        
        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 
        $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->first();
 
        if($resultData['result']['profile_submit_type'] == 'submit' || $resultData['result']['profile_submit_type'] == 'preview'  ){

            \App\Helpers\commonHelper::setLocale();
            return view('profile',compact('resultData','groupInfoResult'));

        }else{

            if($resultData['result']['spouse_confirm_token'] != ''){

                return redirect('spouse-confirm-registration/'.$resultData['result']['spouse_confirm_token'])->with('gpro_success','Are you coming, too?');

            }else{

                \App\Helpers\commonHelper::setLocale();
                return view('profile',compact('resultData','groupInfoResult'));
            }
            
        }
        
        
    }
    
    public function getGroupInformation(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 
 
        // if($resultData['result']['profile_status']!='Pending'){

            $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->get();
 
            \App\Helpers\commonHelper::setLocale();
            return view('group_info',compact('resultData','groupInfoResult'));

        // }else{

        //     return redirect('profile-update')->with('gpro_error','Your profile is incomplete');
        // }
        
        
    }
    
    public function payment(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');

        $resultData=json_decode($result->content, true); 
       
        if($resultData['result']['profile_status'] =='Approved'){

            $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->first();
            
            \App\Helpers\commonHelper::setLocale();

            $transactions = \App\Models\Transaction::where('user_id',$resultData['result']['id'])->get();

            return view('payment',compact('resultData','groupInfoResult','transactions'));

        }else{

            \App\Helpers\commonHelper::setLocale();
            return redirect('profile-update')->with('gpro_error',\Lang::get('web/home.Yourprofile-isunder-review'));
        }
        
        
    }

    public function travelInformation(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 
       
        if($resultData['result']['stage'] >= 3){
           
            $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->first();

            $SpouseInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Spouse')->first();

            $travelInfo=\App\Helpers\commonHelper::callAPI('userTokenget', '/travel-info-details');
            
            $travelInfo=json_decode($travelInfo->content, true); 
            // print_r($travelInfo); die;

            \App\Helpers\commonHelper::setLocale();
            
            return view('travel_info',compact('resultData','groupInfoResult','SpouseInfoResult','travelInfo'));

        }else if($resultData['result']['stage'] < 3){

            return redirect('payment');

        }else{

            return redirect('session-information');
        }
        
        
    }

    public function SessionInformation(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 
       
        if($resultData['result']['stage'] > 4){

            return redirect('event-day-information');

        }else if($resultData['result']['stage'] > 3){

            $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->first();

            $sessions = \App\Models\DaySession::where('status','1')->get();
 
            $userSessionInfo = \App\Models\SessionInfo::where('user_id', $resultData['result']['id'])->get();
		
            \App\Helpers\commonHelper::setLocale();

            return view('session_info',compact('resultData','groupInfoResult','sessions','userSessionInfo'));

        }else{

            \App\Helpers\commonHelper::setLocale();
            return redirect('travel-information')->with('gpro_error',\Lang::get('web/home.Your-Travel-Information-pending'));
        }
        
        
    }

    public function EventDayInformation(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 
       
        if($resultData['result']['stage'] > 4){

            $groupInfoResult=\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Group')->first();

            \App\Helpers\commonHelper::setLocale();

            return view('qrcode_stage5',compact('resultData','groupInfoResult'));

        }else{

            \App\Helpers\commonHelper::setLocale();
            return redirect('session-information')->with('gpro_error',\Lang::get('web/home.YourSession-Informationpending'));
        }
        
        
    }

    
    public function groupInfo(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true);
       
        \Session::put('lang',$resultData['result']['language']);
        
        if($resultData['result']['profile_status']=='Review'){ 
   
            return redirect('profile');
             
        }else if($resultData['result']['profile_status'] =='Approved'){ 

            return redirect('group-information');

        }else if($resultData['result']['added_as']!=''){ 

            return redirect('profile-update');

        }else{

            if($request->ajax()){
 
                $data=array(
                    'is_group'=>$request->post('is_group'),  
                    'user_whatsup_code'=>$request->post('user_whatsup_code'),  
                    'contact_whatsapp_number'=>$request->post('contact_whatsapp_number'),  
                    'user_mobile_code'=>$request->post('user_mobile_code'),  
                    'contact_business_number'=>$request->post('contact_business_number'),  
                );
    
                $data['group_list']=[];
                if($request->post('is_group')=='Yes'){
    
                    $totalEmail=count($request->post('email')); 
    
                    if($totalEmail>0){

                        for($i=0;$i<$totalEmail;$i++){

                            $data['group_list'][]=array(
                                'name'=>$request->post('name')[$i],
                                'email'=>$request->post('email')[$i],
                                'mobile_code'=>$request->post('mobile_code')[$i],
                                'mobile'=>$request->post('mobile')[$i],
                                'whatsapp_code'=>$request->post('whatsapp_code')[$i],
                                'whatsapp_number'=>$request->post('whatsup')[$i],
                            );

                        }

                    } 
                } 
    
                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/group-leader', json_encode($data));
                $resultData=json_decode($result->content, true);
                
                if($result->status==200){
    
                    return response(array('message'=>$resultData['message']), 200); 
    
                }else{
                    return response(array('message'=>$resultData['message']), $result->status);
                }
            }else{ 
    
                $country=\App\Models\Country::get();
                $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
                $resultData=json_decode($result->content, true); 
                \App\Helpers\commonHelper::setLocale();
                return view('groupDetails',compact('resultData','country'));
            }

        } 
    }

    public function profileDetails(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 

        if($resultData['result']['profile_status'] == 'Approved'){
    
            \Session::flash('gpro_success', \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Your-application-alreadyApproved'));
            return redirect('profile');
            
        }elseif($resultData['result']['profile_submit_type'] == 'submit'){

            return redirect('profile');

        }else{

            if($request->ajax()){

                $data=array(
                    'salutation'=>$request->post('salutation'),
                    'first_name'=>$request->post('first_name'),
                    'last_name'=>$request->post('last_name'),
                    'gender'=>$request->post('gender'),
                    'dob'=>$request->post('dob'),
                    'marital_status'=>$request->post('marital_status'),
                    'citizenship'=>$request->post('citizenship')
                );

                if($request->post('marital_status')=='Unmarried'){

                    $data['room']=$request->post('room');
                }
    
                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/profile-update', json_encode($data));
                $resultData=json_decode($result->content, true);
               
                if($result->status==200){

                    return response(array("error"=>false,'message'=>$resultData['message']), 200); 

                }else{
                    return response(array('message'=>$resultData['message']), $result->status);
                }

            }else{

                
                $country = \App\Models\Pricing::orderBy('country_name', 'asc')->get();
                $SpouseDetails =\App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Spouse')->first();
                if(!$SpouseDetails){
                    $SpouseDetails = [];
                }
                $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
                $resultData=json_decode($result->content, true); 
                \App\Helpers\commonHelper::setLocale();

                if($resultData['result']['spouse_confirm_token'] != ''){

                    return redirect('spouse-confirm-registration/'.$resultData['result']['spouse_confirm_token'])->with('gpro_error','Are you coming, too?');

                }else{

                    return view('profileDetails',compact('country','resultData','SpouseDetails'));
                }

                
            }

        }
    }

    public function spouseUpdate(Request $request){
 
        if($request->post('is_spouse')=='Yes' && $request->post('is_spouse_registered')=='Yes'){

            $data=[ 
                'is_spouse'=>'Yes',
                'is_spouse_registered'=>'Yes',
                'email'=>$request->post('email'),
                'id'=>$request->post('id'),
            ];
            
        }else{

            $data=[ 
                'is_spouse'=>'Yes',
                'is_spouse_registered'=>'No',
                'id'=>$request->post('id'), 
                'salutation'=>$request->post('salutation'), 
                'first_name'=>$request->post('first_name'),
                'last_name'=>$request->post('last_name'),
                'email'=>$request->post('email'),
                'gender'=>$request->post('gender'),
                'date_of_birth'=>$request->post('date_of_birth'),
                'citizenship'=>$request->post('citizenship'),
            ];

        }
 
        $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/spouse-add', json_encode($data));
        $resultData=json_decode($result->content, true);
        
        if($result->status==200){

            return response(array("error"=>false,'message'=>$resultData['message']), 200); 

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }
    }

    public function roomUpdate(Request $request){

        $data=array(
            'room'=>$request->post('room'),
        );

        $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/stay-room', json_encode($data));
        $resultData=json_decode($result->content, true);

        if($result->status==200){

            return response(array('message'=>$resultData['message']), 200); 

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }
        
    }

    public function sponsorPaymentsSubmit(Request $request){

        $data=array(
            'email'=>$request->post('email'),
            'amount'=>$request->post('amount'),
            'name'=>$request->post('name'),
        );
        
        $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/sponsor-payments-submit', json_encode($data));
        $resultData=json_decode($result->content, true);
       
        if($result->status==200){

            return response(array('message'=>$resultData['message']), 200); 

        }else{
            return response(array('message'=>$resultData['message']), $result->status);
        }
        
    }

    public function contactDetails(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 

        
        if($resultData['result']['profile_update'] != '1'){
    
            return redirect('profile-update');
            
        }elseif($resultData['result']['profile_submit_type'] == 'submit'){

            return redirect('profile');

        }else{

            if($request->ajax()){
                
                $data=array(
                    'contact_address'=>$request->post('contact_address'),
                    'contact_zip_code'=>$request->post('contact_zip_code'),
                    'contact_country_id'=>$request->post('contact_country_id'),
                    'contact_state_id'=>$request->post('contact_state_id'),
                    'contact_city_id'=>$request->post('contact_city_id'),
                    'contact_state_name'=>$request->post('contact_state_name'),
                    'contact_city_name'=>$request->post('contact_city_name'),
                    'user_mobile_code'=>$request->post('user_mobile_code'),
                    'mobile'=>$request->post('mobile'),
                    'contact_business_codenumber'=>$request->post('contact_business_codenumber'),
                    'contact_business_number'=>$request->post('contact_business_number'),
                    'contact_whatsapp_codenumber'=>$request->post('contact_whatsapp_codenumber'),
                    'contact_whatsapp_number'=>$request->post('contact_whatsapp_number'),
                    'whatsapp_number_same_mobile'=>$request->post('whatsapp_number_same_mobile') ?? 'No',
                    'terms_and_condition'=>$request->post('terms_and_condition') ?? '0',
                );

                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/contact-details', json_encode($data));
                $resultData=json_decode($result->content, true);

                if($result->status==200){

                    return response(array('message'=>$resultData['message']), 200); 

                }else{
                    return response(array('message'=>$resultData['message']), $result->status);
                }

            }else{

                $country=\App\Models\Country::select('id','name','phonecode')->get();
                $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
                $resultData=json_decode($result->content, true); 
                \App\Helpers\commonHelper::setLocale();
                return view('contactDetails',compact('country','resultData'));
            }
        }
    }

    public function ministryDetails(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 

        if($resultData['result']['contact_address']  == null){
    
            return redirect('contact-details');
            
        }elseif($resultData['result']['profile_submit_type'] == 'submit'){

            return redirect('profile');

        }else{

            if($request->ajax()){

                if($request->post('type') == 'preview'){

                    $data=array(
                    
                        'ministry_name'=>$request->post('ministry_name'),
                        'ministry_address'=>$request->post('ministry_address'),
                        'ministry_zip_code'=>$request->post('ministry_zip_code'),
                        'ministry_country_id'=>$request->post('ministry_country_id'),
                        'ministry_state_id'=>$request->post('ministry_state_id'),
                        'ministry_city_id'=>$request->post('ministry_city_id'),
                        'ministry_state_name'=>$request->post('ministry_state_name'),
                        'ministry_city_name'=>$request->post('ministry_city_name'),
                        'ministry_pastor_trainer'=>$request->post('ministry_pastor_trainer'),
                        'type'=>$request->post('type'),
                    );

                }else{

                    $data=array(
                    
                        'type'=>$request->post('type'),
                    );
                }
                
                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/ministry-details', json_encode($data));
                $resultData=json_decode($result->content, true);
                
                if($result->status==200){

                    return response(array('message'=>$resultData['message']), 200); 

                }else{

                    return response(array('message'=>$resultData['message']), $result->status);
                }

            }else{

                $country=\App\Models\Country::select('id','name','phonecode')->get();
                $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
                $resultData=json_decode($result->content, true); 
                \App\Helpers\commonHelper::setLocale();
                return view('ministryDetails',compact('country','resultData'));
            }
        }
        
    }

    public function updatePastoralLeader(Request $request){


        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 

        if($resultData['result']['profile_status']!='Pending'){
    
            return redirect('profile');
            
        }else{

            if($request->ajax()){

                if($request->post('ministry_pastor_trainer')=='Yes'){

                    $data=array( 
                        'ministry_pastor_trainer'=>$request->post('ministry_pastor_trainer'),
                        'non_formal_trainor'=>$request->post('non_formal_trainor'),
                        'formal_theological'=>$request->post('formal_theological'),
                        'informal_personal'=>$request->post('informal_personal'),
                        'howmany_pastoral'=>$request->post('howmany_pastoral'),
                        'howmany_futurepastor'=>$request->post('howmany_futurepastor'),
                        'willing_to_commit'=>$request->post('willing_to_commit'),
                        'comment'=>$request->post('comment'),
                    );

                }else{

                    $data=array( 
                        'ministry_pastor_trainer'=>$request->post('ministry_pastor_trainer'),
                        'pastorno'=>$request->post('pastorno'),
                        'comment'=>$request->post('comment')
                    );
                }
                
                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/pastoral-leaders-detailupdate', json_encode($data));
                $resultData=json_decode($result->content, true);

                if($result->status==200){

                    return response(array('message'=>$resultData['message']), 200); 

                }else{
                    return response(array('message'=>$resultData['message']), $result->status);
                }

            }else{

                $country=\App\Models\Country::select('id','name','phonecode')->get();

                \App\Helpers\commonHelper::setLocale();
                return view('ministryDetails',compact('country'));
            }
        }
        
    }

    public function logOut(Request $request){

        Session::forget('gpro_user');
        Session::forget('gpro_result');

        return redirect()->route('home');

    }

    public function changePassword(Request $request){
 
        if($request->ajax()){

            $data=array(
                'old_password'=>$request->post('old_password'),
                'new_password'=>$request->post('new_password'),
                'confirm_password'=>$request->post('confirm_password'),
            );
              
            $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/change-password', json_encode($data));
 
            $resultData=json_decode($result->content, true);
    
            if($result->status==200){
                
                
                Session::put('gpro_result', $resultData['result']);

                return response(array('message'=>$resultData['message']), 200); 
    
            }else{
                return response(array('message'=>$resultData['message']), $result->status);
            }
        }

        \App\Helpers\commonHelper::setLocale();
        return view('change_password');

    }

    
    public function OnlinePaymentFull(Request $request){

        $user = \App\Models\User::where('id',\Session::get('gpro_result')['id'])->first();

        if($user && $user->amount >0){

            $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, false);
            
            $data = \App\Helpers\commonHelper::paymentGateway($user->id,$totalPendingAmount,1);
            
            $intent = $data['intent'];
            $order_id = $data['order_id'];
            $id = $data['order_id'];
            \Session::put('intent',$intent);
            \App\Helpers\commonHelper::setLocale();
            return view('stripe',compact('intent','order_id','id'));
            
        }else{

            \App\Helpers\commonHelper::setLocale();
            \Session::flash('gpro_error', \Lang::get('web/home.Requestor-Payment-is-completed'));
            return redirect('/');
        }

    }

    public function fullPaymentOfflineSubmit(Request $request){
 
        if($request->ajax()){

            if($request->post('type') != 'Online'){

                $rules = [
                    'reference_number' => 'required',
                    'amount' => 'required|numeric',
                    'name' => 'required',
                    'type' => 'required|in:Offline,Online',
                ];

                if($request->post('mode') != 'Wire'){
                    $rules['country_of_sender'] = 'required';
                }
        
                $validator = \Validator::make($request->all(), $rules);
                 
                if ($validator->fails()) {
                    $message = [];
                    $messages_l = json_decode(json_encode($validator->messages()), true);
                    foreach ($messages_l as $msg) {
                        $message = $msg[0];
                        break;
                    }
                    
                    return response(array("error"=>true, 'message'=>$message), 403);
                    
                }else{
        
                    try {

                        $referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
                        if($referenceNumberCheck){

                            \App\Helpers\commonHelper::setLocale();
                            return response(array("error"=>true, "message"=> \Lang::get('web/home.Transaction-already-exists')), 403);

                        }else{

                            $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount(\Session::get('gpro_result')['id'], false); 
                            if($request->post('amount') <= $totalPendingAmount){
            
                                $transactionId=strtotime("now").rand(11,99);
            
                                $orderId=strtotime("now").rand(11,99);
                
                                $transaction = new \App\Models\Transaction();
                                $transaction->user_id = \Session::get('gpro_result')['id'];
                                $transaction->bank = $request->post('mode');
                                $transaction->order_id = $orderId;
                                $transaction->transaction_id = $transactionId;
                                $transaction->method = $request->post('type');
                                $transaction->amount = $request->post('amount');
                                $transaction->name = $request->post('name');
						        $transaction->country_of_sender = $request->post('country_of_sender') ?? '';
                                $transaction->bank_transaction_id = $request->post('reference_number');
                                $transaction->status = '0';
                                $transaction->particular_id = '1';
            
                                if($request->hasFile('file')){
                                    $imageData = $request->file('file');
                                    $image = 'image_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                                    $destinationPath = public_path('/uploads/transaction');
                                    $imageData->move($destinationPath, $image);
            
                                    $transaction->file = $image;
                                }
            
                                $transaction->save();
            
                                $Wallet = new \App\Models\Wallet();
                                $Wallet->user_id = \Session::get('gpro_result')['id'];
                                $Wallet->type  = 'Cr';
                                $Wallet->amount = $request->post('amount');
                                $Wallet->transaction_id = $transaction->id;
                                $Wallet->status = 'Pending';
                                $Wallet->save();
                
                                $user = \App\Models\User::where('id',\Session::get('gpro_result')['id'])->first();

                                $to = $user->email;
                                $subject = 'Transaction Complete';
                                $msg = 'Your '.$request->post('amount').' transaction has been send successfully';
                                \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
            
                                $type = $request->post('type');
                                
                                $name = $user->name.' '.$user->last_name;
                                $amount = $request->post('amount');

                                $subject = '[GProCongress II Admin]  Payment Received';
                                $msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has made Payment of&nbsp; '.$amount.' for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Payment Mode: '.$type.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team Gpro</font></span></p>';
                                \App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);
            
                                // \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
            
                                \App\Helpers\commonHelper::setLocale();
                                return response(array("error"=>false, "message"=> \Lang::get('web/home.Offline-payment-successful')), 200);
                
                            }else{
            
                                 \App\Helpers\commonHelper::setLocale();
                                return response(array("error"=>true, "message"=> \Lang::get('web/home.Requestor-Payment-is-completed')), 403);
                
                            }
                            
                        }
        
                        
                        
                    } catch (\Exception $e) {
                        return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Something-went-wrongPlease-try-again')), 403);
                    }
                }

            }else{

                $data = \App\Helpers\commonHelper::paymentGateway(\Session::get('gpro_result')['id'],$request->post('amount'));

                $intent = $data['intent'];

                \Session::put('intent',$data['intent']);

                $order_id = $data['order_id'];
                
                return response(array('message'=>'','urlPage'=>true,'url'=>url('stripe/'.$order_id)), 200);
            }
            
        }
        
    }

    public function cashPaymentSubmit(Request $request){
 
        if($request->ajax()){

            $rules = [
                'amount' => 'required|numeric',
                'type' => 'required|in:Cash',
            ];
    
            $validator = \Validator::make($request->all(), $rules);
                
            if ($validator->fails()) {
                $message = [];
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message = $msg[0];
                    break;
                }
                
                return response(array("error"=>true, 'message'=>$message), 403);
                
            }else{
    
                try {

                    $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount(\Session::get('gpro_result')['id'], false); 
                    if($request->post('amount') <= $totalPendingAmount){

                        $transactionId=strtotime("now").rand(11,99);

                        $orderId=strtotime("now").rand(11,99);
        
                        $transaction = new \App\Models\Transaction();
                        $transaction->user_id = \Session::get('gpro_result')['id'];
                        $transaction->bank = $request->post('type');
                        $transaction->order_id = $orderId;
                        $transaction->transaction_id = $transactionId;
                        $transaction->method = 'Offline';
                        $transaction->amount = $request->post('amount');
                        $transaction->status = '0';
                        $transaction->particular_id = '1';
                        $transaction->save();

                        $Wallet = new \App\Models\Wallet();
                        $Wallet->user_id = \Session::get('gpro_result')['id'];
                        $Wallet->type  = 'Cr';
                        $Wallet->amount = $request->post('amount');
                        $Wallet->transaction_id = $transaction->id;
                        $Wallet->status = 'Pending';
                        $Wallet->save();
        
                        $user = \App\Models\User::where('id',\Session::get('gpro_result')['id'])->first();

                        $to = $user->email;
                        $subject = 'Transaction Complete';
                        $msg = 'Your '.$request->post('amount').' transaction has been send successfully';
                        \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

                        $type = 'Cash';
                                
                        $name = $user->name.' '.$user->last_name;
                        $amount = $request->post('amount');

                        $subject = '[GProCongress II Admin] || Cash Payment Received';
                        $msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has made Payment of&nbsp; '.$amount.' for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Payment Mode: '.$type.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team Gpro</font></span></p>';

                        \App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

                        // \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

                        
                        \App\Helpers\commonHelper::setLocale();
                        return response(array("error"=>false, "message"=> \Lang::get('web/home.Cash-Payment-addedSuccessful')), 200);
        
                    }else{

                        \App\Helpers\commonHelper::setLocale();
                        return response(array("error"=>true, "message"=> \Lang::get('web/home.Requestor-Payment-is-completed')), 403);
        
                    }
                      
                } catch (\Exception $e) {
                    return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Something-went-wrongPlease-try-again')), 403);
                }
            }

        }
        
    }

    
    public function travelInformationSubmit(Request $request){

        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/user-profile');
        $resultData=json_decode($result->content, true); 

        if($resultData['result']['stage'] =='2'){
    
            return redirect('payment');
            
        }else{

            if($request->ajax()){

                $data=array(
                    'arrival_flight_number'=>$request->post('arrival_flight_number'),
                    'arrival_start_location'=>$request->post('arrival_start_location'),
                    'arrival_date_departure'=>$request->post('arrival_date_departure'),
                    'arrival_date_arrival'=>$request->post('arrival_date_arrival'),
                    'departure_flight_number'=>$request->post('departure_flight_number'),
                    'departure_start_location'=>$request->post('departure_start_location'),
                    'departure_date_departure'=>$request->post('departure_date_departure'),
                    'departure_date_arrival'=>$request->post('departure_date_arrival'),
                    'spouse_arrival_flight_number'=>$request->post('spouse_arrival_flight_number'),
                    'spouse_arrival_start_location'=>$request->post('spouse_arrival_start_location'),
                    'spouse_arrival_date_departure'=>$request->post('spouse_arrival_date_departure'),
                    'spouse_arrival_date_arrival'=>$request->post('spouse_arrival_date_arrival'),
                    'spouse_departure_flight_number'=>$request->post('spouse_departure_flight_number'),
                    'spouse_departure_start_location'=>$request->post('spouse_departure_start_location'),
                    'spouse_departure_date_departure'=>$request->post('spouse_departure_date_departure'),
                    'spouse_departure_date_arrival'=>$request->post('spouse_departure_date_arrival'),
                    'logistics_dropped'=>$request->post('logistics_dropped'),
                    'logistics_picked'=>$request->post('logistics_picked'),
                    'mobile'=>$request->post('mobile'),
                    'name'=>$request->post('name'),
                    'id'=>$request->post('id'),
                    'share_your_room_with'=>$request->post('share_your_room_with'),
                );

                $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/travel-info', json_encode($data));
                // print_r($result); die;
                $resultData=json_decode($result->content, true);

                if($result->status==200){

                    return response(array('message'=>$resultData['message']), 200); 

                }else{

                    return response(array('message'=>$resultData['message']), $result->status);
                }

            }
        }
        
    }
    
    public function travelInformationRemarkSubmit(Request $request){

        if($request->ajax()){

            $data=array(
                'remark'=>$request->post('remark'),
            );

            $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/travel-info-remark', json_encode($data));
            // print_r($result); die;
            $resultData=json_decode($result->content, true);

            if($result->status==200){

                return response(array('message'=>$resultData['message']), 200); 

            }else{

                return response(array('message'=>$resultData['message']), $result->status);
            }

        }
        
        
    }


    public function travelInformationVerify(Request $request){
 
        $data=array(
            'status'=>'1',
        );
        
        $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/travel-info-verify', json_encode($data));
        
        $resultData=json_decode($result->content, true);
        \App\Helpers\commonHelper::setLocale();
        if($result->status==200){
            
           
            return redirect('travel-information')->with('gpro_success', \Lang::get('web/home.TravelInformation-approved-successful'));

        }else{

            return redirect('travel-information')->with('gpro_error', \Lang::get('web/home.Travel-Information-notApproved'));
        }
    }

    public function SessionInformationSubmit(Request $request){
        
       
        $data=array(
            'session_id'=>$request->post('session_id'),
            'session_date'=>$request->post('session_date'),
            'session'=>$request->post('session'),
        );
        
        $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/session-info', json_encode($data));
      
        $resultData=json_decode($result->content, true);
       
        if($result->status==200){

            return response(array('message'=>$resultData['message']), 200); 

        }else{

            return response(array('message'=>$resultData['message']), $result->status);
        }
        
    }

    public function SessionInformationFinalSubmit(Request $request){
 
       
        $result=\App\Helpers\commonHelper::callAPI('userTokenget', '/session-info-verify');
       
        $resultData=json_decode($result->content, true); 

        if($result->status==200){

            return redirect('event-day-information')->with('gpro_success',$resultData['message']);

        }else{

            return redirect('event-day-information')->with('gpro_error',$resultData['message']);

        }
        
    }

	
}
