<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Omnipay\Omnipay;

use Stripe;

class HomeController extends Controller
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

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        
        \App\Helpers\commonHelper::setLocale();
        
        if(\App::getLocale() == 'en'){
            
            $testimonials = \App\Models\Testimonial::where('status', '1')->inRandomOrder()->limit(3)->get();
        
        }else{

            $testimonials = \App\Models\Testimonial::where(function ($query) {
                $query->where('id',3)
                      ->orWhere('id',4)
                      ->orWhere('id',5);
            })->get();
        
        }

        $speakers = \App\Models\Speaker::where('status', '1')->inRandomOrder()->limit(3)->get();
        $preRecordedVideo = \App\Models\PreRecordedVideo::where('status', '1')->inRandomOrder()->limit(3)->get();

        return view('index', compact('testimonials','speakers','preRecordedVideo'));
    } 

    public function Registration() {

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        $testimonials = \App\Models\Testimonial::where('status', '1')->inRandomOrder()->limit(3)->get();
        \App\Helpers\commonHelper::setLocale();
        
        return view('index', compact('testimonials'));
    }

    public function contactDetails() {
        \App\Helpers\commonHelper::setLocale();
        return view('contactDetails');
    }

    public function ministryDetails() {
        \App\Helpers\commonHelper::setLocale();
        return view('ministryDetails');
    }

    public function profileDetails() {
        \App\Helpers\commonHelper::setLocale();
        return view('profileDetails');
    }

    public function getState(Request $request){

        $country_id=$request->get('country_id');
        \App\Helpers\commonHelper::setLocale();
        $option="<option value='' selected >--".\Lang::get('web/ministry-details.select')."--</option>";

        if($country_id>0){

            $stateResult=\App\Models\State::orderBy('name','Asc')->where('country_id',$country_id)->get();

            foreach($stateResult as $state){

                $option.="<option value='".$state['id']."'>".ucfirst($state['name'])."</option>";
            }
            $option.="<option value='0'>".\Lang::get('web/home.other')."</option>";

        }

       
        return response(array('message'=>'state fetched successfully.','html'=>$option));
    }
    
    public function getCity(Request $request){

        $stateId=$request->get('state_id');
        \App\Helpers\commonHelper::setLocale();
        $option="<option value='' selected >--".\Lang::get('web/ministry-details.select')."--</option>";

        if($stateId>0){

            $cityResult=\App\Models\City::orderBy('name','Asc')->where('state_id',$stateId)->get();

            foreach($cityResult as $city){
    
                $option.="<option value='".$city['id']."'>".ucfirst($city['name'])."</option>";
            }
            $option.="<option value='0'>".\Lang::get('web/home.other')."</option>";
        }else{
            $option.="<option value='0'>".\Lang::get('web/home.other')."</option>";
        }
        

        return response(array('message'=>'City fetched successfully.','html'=>$option));
    }
	
    public function mapCountry(Request $request){

        $country=\App\Models\Pricing::all();

        foreach($country as $country_id){

            $countryPrice=\App\Models\Pricing::where('id',$country_id->id)->first(); 
            if($countryPrice){

                $countryPrice->single_sharing_per_person_deluxe_room_early_bird = ((round($country_id->PRICES))-100)*2;
                $countryPrice->single_sharing_per_person_deluxe_room_with_out_early_bird = (round($country_id->PRICES))*2;
                $countryPrice->single_trainers_deluxe_room_early_bird = (round($country_id->PRICES))*2;
                $countryPrice->single_trainers_deluxe_room_with_out_early_bird = (round($country_id->PRICES))*2;
                $countryPrice->twin_sharing_per_person_deluxe_room_with_out_eb = (round($country_id->PRICES))*2;
                $countryPrice->save();
            } 

        }

        echo "Done"; die;

    }
	
    public function sponsorPaymentSubmit(Request $request,$token){

        $linkPayment = \App\Models\SponsorPayment::where('token',$token)->first();

        if($linkPayment){

            $user = \App\Models\User::where('id',$linkPayment->user_id)->first();

            if($user && $user->amount >0){

                $data = \App\Helpers\commonHelper::paymentGateway($user->id,$linkPayment->amount,2);

                $intent = $data['intent'];
                echo $order_id = $data['order_id'];
                die;
                $linkPayment->order_id = $data['order_id'];
                $linkPayment->save();

                \App\Helpers\commonHelper::sendSponsorPaymentTriggeredToUserMail($linkPayment->user_id,$linkPayment->amount,$linkPayment->name);
                
                return view('stripe',compact('intent','order_id'));
                
            }else{

                \Session::flash('gpro_error', \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Payment-Successful'));
                return redirect('/');
            }

        }else{

            $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Payment-link-hasbeen-expired');
            \Session::flash('gpro_error', $message);

            return redirect('/');
        }

    }

    public function sponsorPaymentLink(Request $request,$token){

        $linkPayment = \App\Models\SponsorPayment::where('token',$token)->first();
        \App\Helpers\commonHelper::setLocale();

        if($linkPayment){

            return view('sponsor_payment',compact('linkPayment'));

        }else{

            \Session::flash('gpro_error',\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Payment-link-hasbeen-expired'));
            return redirect('/');
        }

    }

    
	public function stripePost(Request $request)
    {

		$rules['order']='required';

        $messages = array(
			'order.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'order_required'),
				
		);

		$validator = \Validator::make($request->all(), $rules,$messages);
		
		if ($validator->fails()){
			
			$message = "";
			$messages_l = $validator->messages();
			foreach ($messages_l as $msg) {
				$message= $msg[0];
				break;
			}
			
			return redirect()->back();
			
			
		}else{

			\Session::forget('intent');
            \App\Helpers\commonHelper::setLocale();
            \Session::flash('gpro_success', \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Payment-Successful'));
                
            return redirect('payment')->with('gpro_success', \Lang::get('web/home.Payment-Successful'));
			
			
		}

    }

    public function help(Request $request){

        if($request->ajax()){

            $rules = [
                'name' => 'required',
                'email' => 'required|email',
                'mobile' => 'required|numeric',
                'message' => 'required',
                'phonecode' => 'required',
            ];

            $messages = array(
                'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'email_required'),
                'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'email_email'),			
                'mobile.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'mobile_required'), 
                'mobile.numeric' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'mobile_numeric'), 
                'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'name_required'), 
                'phonecode.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'phonecode_required'), 
                'message.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'message_required'), 

            );

            $validator = \Validator::make($request->all(), $rules, $messages);
            
            if ($validator->fails()) {
                $message = [];
                $messages_l = json_decode(json_encode($validator->messages()), true);
                foreach ($messages_l as $msg) {
                    $message= $msg[0];
                    break;
                }
                
                return response(array('message'=>$message,"error" => true),403);
                
            }else{

                try{
                    
                    $message = new \App\Models\Message;
                    $message->name = $request->post('name');
                    $message->email = $request->post('email');
                    $message->mobile =  $request->post('phonecode').$request->post('mobile');
                    $message->message = $request->post('message');
                    

                    if($request->hasFile('attachment')){
                        $imageData = $request->file('attachment');
                        $image = 'image_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                        $destinationPath = public_path('/uploads/attachment');
                        $imageData->move($destinationPath, $image);

                        $message->file = $image;

                    }

                    
                    $message->save();

                    return response(array('message'=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Your_submission_has_been_sent'),"error" => false), 200);
                    
                }catch (\Exception $e){
                    
                    return response(array("error" => true, "message" => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Something-went-wrongPlease-try-again')), 403);
                
                }

            }
        }

        $country=\App\Models\Country::select('id','name','phonecode')->get();

        \App\Helpers\commonHelper::setLocale();
        return view('help', compact('country'));

    }

    public function information(Request $request, $slug) {

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        
        $information = \App\Models\Information::where([['slug', $slug], ['status', '1']])->first();
        \App\Helpers\commonHelper::setLocale();
        return view('information', compact('information'));

    }

    public function faq(Request $request) {

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        
        $categories = \App\Models\Faq::where('status', '1')->groupBy('category')->get();
        $faqs = \App\Models\Faq::where('status', '1')->get();
        \App\Helpers\commonHelper::setLocale();

        
        return view('faq', compact('categories', 'faqs'));

    }

    public function donate(Request $request) {

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        \App\Helpers\commonHelper::setLocale();

        return view('donate');

    }

    public function localization(Request $request) {
		if($request->ajax() && $request->isMethod('post')){

		    \Session::put('lang', $request->post('lang'));

            // if(\Session::has('gpro_user')){

            //     $data=array(
            //         'language'=>$request->post('lang'),
            //     );

            //     $result=\App\Helpers\commonHelper::callAPI('userTokenpost', '/change-user-language', json_encode($data));
            //     $resultData=json_decode($result->content, true);
               

            // }
            return response(array('reload' => true), 200);
        }
	}
    
    public function sponsorPaymentsPay(Request $request){
 
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'user_id' => 'required',
            'id' => 'numeric|required',
		];

        $messages = array(
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'name_required'),
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'email_required'),
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'amount_required'),
			'user_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'user_id'),
				
		);

		$validator = \Validator::make($request->all(), $rules,$messages);
		 
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

				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->post('user_id'), false); 
				if($request->post('amount') <= $totalPendingAmount){

                    
					$data = \App\Helpers\commonHelper::paymentGateway($request->post('user_id'),$request->post('amount'),2);

                    $intent = $data['intent'];

                    \Session::put('intent',$data['intent']);

                    $order_id = $data['order_id'];

                    $linkPayment = \App\Models\SponsorPayment::where('id',$request->post('id'))->first();
                    if($linkPayment){

                        $linkPayment->order_id = $data['order_id'];
                        $linkPayment->save();
    
                        \App\Helpers\commonHelper::sendSponsorPaymentTriggeredToUserMail($linkPayment->user_id,$request->post('amount'),$linkPayment->name);
    
                    }
                
                    $subject='Sponsor Submitted Payment';
                    $msg='Sponsor Submitted Payment';

                    \App\Helpers\commonHelper::sendNotificationAndUserHistory($request->post('user_id'),$subject,$msg,'Sponsor Submitted Payment');
					
                    return response(array('message'=>'','urlPage'=>true,'url'=>url('stripe/'.$order_id)), 200);
	
				}else{

					return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'amount_lesser_than')), 403);
	
				}
				
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Something-went-wrongPlease-try-again')), 403);
			}
		}

        
    }

    
    public function donatePaymentsSubmit(Request $request){
 
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'user_id' => 'required',
		];

        $messages = array(
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'name_required'),
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'email_required'),
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'amount_required'),
			'user_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'user_id'),
				
		);

		$validator = \Validator::make($request->all(), $rules,$messages);
		 
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

                $Donation = new \App\Models\Donation();
                $Donation->user_id = $request->post('user_id');
                $Donation->name = $request->post('name');
                $Donation->email = $request->post('email');
                $Donation->amount = $request->post('amount');
                $Donation->save();

                $data = \App\Helpers\commonHelper::paymentGateway($request->post('user_id'),$request->post('amount'),3);

                $intent = $data['intent'];

                \Session::put('intent',$data['intent']);

                $order_id = $data['order_id'];

                $subject='User Donate Amount Submitted Payment';
                $msg='User Donate Amount Submitted Payment';
                 
                \App\Helpers\commonHelper::sendNotificationAndUserHistory($request->post('user_id'),$subject,$msg,'User Donate Amount Submitted Payment');
                
                return response(array('message'=>'','urlPage'=>true,'url'=>url('stripe/'.$order_id)), 200);
	
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Something-went-wrongPlease-try-again')), 403);
			}
		}

        
    }

	public function stripePaymentPage($id)
    {
		
        return view('stripe',compact('id'));
    }

    
    public function SpouseConfirmRegistration(Request $request,$token){

        $linkPayment = \App\Models\User::where('spouse_confirm_token',$token)->first();

        if($linkPayment){

            $user = \App\Models\User::where('id',$linkPayment->parent_id)->first();

            return view('spouse_confirm',compact('linkPayment','user'));

        }else{

            $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Confirmation-link-has-expired');
            \Session::flash('gpro_error', $message);
            return redirect('/');
        }

    }

    public function SpouseConfirmAction(Request $request,$type,$token){

        $linkPayment = \App\Models\User::where('spouse_confirm_token',$token)->first();

        if($linkPayment){

            $history = new \App\Models\SpouseStatusHistory;
            $history->spouse_id = $linkPayment->id;
            $history->parent_id = $linkPayment->parent_id;

            if($type == 'confirm'){

                $history->remark = 'Your spouse confirmation is Approved';
                $history->status = 'Approve';

                $subject = 'Spouse confirmation approved';
                
                \App\Helpers\commonHelper::sendNotificationAndUserHistory($linkPayment->id,$subject,'Spouse confirmation is Approved','Spouse confirmation is Approved');

                $user = \App\Models\User::where('id',$linkPayment->parent_id)->first();
               
                $name = $user->salutation.' '.$user->name.' '.$user->last_name;

                $subject = 'Your spouse confirmation is Approved';
                $msg = '<div>Dear '.$user->salutation.' '.$user->name.' '.$user->last_name.',</div><div><br></div><div>has begun your registration for the GProCongress II! Please use this link </div>';
                
                // \App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
                \App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,'Your spouse confirmation is Approved','Your spouse confirmation is Approved');

                if(date('Y-m-d',strtotime($linkPayment->created_at)) < date('Y-m-d',strtotime($user->created_at))){

                    // $linkPayment->parent_id = null;
                    // $linkPayment->added_as = null;
                    $linkPayment->spouse_confirm_status = 'Approve';
                    $linkPayment->spouse_confirm_token = '';
                   

                }else{

                    $linkPayment->spouse_confirm_status = 'Approve';
                    $linkPayment->spouse_confirm_token = '';
                    
                }

                if($linkPayment->room == ''){
                    $linkPayment->room = null;
                }
                
                $linkPayment->spouse_confirm_reminder_email = null;
                $linkPayment->save();

            }else{

                $history->remark = 'Your Spouse Confirmation is Declined!';
                $history->status = 'Reject';

                $subject = 'Spouse confirmation declined';
                
                \App\Helpers\commonHelper::sendNotificationAndUserHistory($linkPayment->id,$subject,'Spouse confirmation declined','Spouse confirmation declined');

                $user = \App\Models\User::where('id',$linkPayment->parent_id)->first();
                if($user){

                    $name = $user->salutation.' '.$user->name.' '.$user->last_name;
                    $nameSpouse = $linkPayment->salutation.' '.$linkPayment->name.' '.$linkPayment->last_name;
                    $faq = '<a href="'.url('faq').'">Click here</a>';

                    if($user->language == 'sp'){

                        $subject = "La solicitud de su cónyuge ha sido rechazada";
                        $msg = '<p>Estimado '.$name.',&nbsp;</p><p><br></p><p><br></p><p>Gracias por registrarse para asistir al GProCongress II 2023 en Ciudad de Panamá, Panamá con su cónyuge '.$nameSpouse.'. Lamentablemente, esta vez, su solicitud ha sido rechazada</p><p><br></p><p>Estamos cambiando nuestro registro para que su estado de inscripción diga "Persona casada que asiste sin cónyuge".</p><p>La tarifa de su habitación se ajustará en consecuencia.</p><p><br></p><p>Sin embargo, esto no significa el fin de nuestra relación.&nbsp;</p><p><br></p><p>Animamos a su cónyuge y a usted a que se mantengan conectados con la comunidad GProCommission haciendo clic aquí: &lt;enlace&gt;. Recibirá aliento continuo, ideas, apoyo en oración y mucho más mientras usted forma líderes pastorales.</p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
                    
                    }elseif($user->language == 'fr'){
                    
                        $subject = "La demande de votre conjoint/e a été refusée.";
                        $msg = '<p>Cher '.$name.',</p><p><br></p><p>Merci de vous être inscrit pour assister au GProCongrès II l’année prochaine à Panama City, au Panama avec votre conjoint/e '.$nameSpouse.'. Malheureusement, cette fois, leur demande a été refusée.</p><p><br></p><p>Nous modifions notre dossier afin que votre statut d’inscription indique « Personne mariée participant sans conjoint/e ».&nbsp;</p><p>Le tarif de votre chambre sera ajusté en conséquence.</p><p><br></p><p>Cependant, ce n’est pas la fin de notre relation.&nbsp;</p><p>Nous encourageons votre conjoint/e et vous à rester en contact avec la communauté GProCommission en cliquant ici: &lt;link&gt;. Vous recevrez des encouragements continus, des idées, un soutien à la prière et autre alors que vous préparez les responsables pastoraux.&nbsp;</p><p><br></p><p>Vous avez d’autres questions? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';
                    
                    }elseif($user->language == 'pt'){
                    
                        $subject = "O pedido do seu cônjuge foi declinado.";
                        $msg = '<p>Prezado '.$name.',</p><p><br></p><p><br></p><p>Agradecemos pela sua inscrição para participar no II CongressoGPro no próximo ano na Cidade de Panamá, Panamá, junto com seu cônjuge '.$nameSpouse.'. Infelizmente, desta vez, o pedido dela/e foi declinado.</p><p><br></p><p>Estamos mudando os nossos registos e assim, o estado da sua inscrição aparecerá “Pessoa Casada Participará Sem Cônjuge”.</p><p>A tarifa do seu quarto será ajustada de acordo.</p><p><br></p><p>Contudo, este não é o fim do nosso relacionamento.</p><p>&nbsp;</p><p>Encorajamos seu cônjuge e você a se manter conectado a nossa ComunidadeGPro clicando aqui: &lt;link&gt;. Você continuará recebendo encorajamento contínuo, ideias, suporte em oração e muito mais, à medida que prepara os líderes pastorais.</p><p><br></p><p>Ainda tem perguntas? Simplesmente responda este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
                    
                    }else{
                    
                        $subject = "Your spouse's application has been declined.";
                        $msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for registering to attend GProCongress II next year in Panama City, Panama with your spouse '.$nameSpouse.'. Regretfully, this time, their application has been declined.</p><p><br></p><p>We are changing our record so your registration status reads “Married Person Attending Without Spouse.”&nbsp;</p><p>Your room rate will be adjusted accordingly.</p><p><br></p><p>However, this is not the end of our relationship.&nbsp;</p><p>We encourage your spouse and you to stay connected to the GProCommission community by clicking here: '.$faq.'. You will receive ongoing encouragement, ideas, prayer support, and more as you prepare pastoral leaders.&nbsp;</p><p><br></p><p>Still have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p><div><br></div>';
                    
                    }

                    \App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
                    \App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your spouse application has been declined.');
                    \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);


                }

                $linkPayment->parent_id = null;
                $linkPayment->added_as = null;
                $linkPayment->spouse_confirm_status = 'Decline';
                $linkPayment->spouse_confirm_token = '';
                $linkPayment->spouse_confirm_reminder_email = null;
                $linkPayment->room = 'Sharing';
                $linkPayment->save();

                $user->room = 'Sharing';
                $user->save();
                
            }

            $history->save();

            \App\Helpers\commonHelper::setLocale();
            \Session::flash('gpro_success', \Lang::get('web/home.Confirmation-Successful'));
            return redirect('profile-update');

        }else{

			\App\Helpers\commonHelper::setLocale();
            \Session::flash('gpro_error', \Lang::get('web/home.Confirmation-link-has-expired'));
            return redirect('profile-update');
        }

    }

    public function PaypalSuccessUrl(Request $request)
    {

        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        $gateway->setSecret(env('PAYPAL_CLIENT_SECRET'));
        $gateway->setTestMode(true);

        if ($request->input('paymentId') && $request->input('PayerID')) {

            $transaction = $gateway->completePurchase(array(
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId')
            ));

            $response = $transaction->send();

            if ($response->isSuccessful()) {

                $arr = $response->getData();
                
                if(isset($arr['transactions'][0]['description'])){

                    $transaction=\App\Models\Transaction::where('order_id',$arr['transactions'][0]['description'])->first();
		
                    if($transaction){

                        $transaction->razorpay_order_id=$arr['id'];
                        $transaction->razorpay_paymentid=$arr['id'];
                        $transaction->card_id=$arr['cart'];
                        $transaction->bank=$arr['payer']['payment_method'];
                        $transaction->bank_transaction_id=$arr['id'];
                        $transaction->payment_status='2';
                        $transaction->status='1';
                        $transaction->method='Online';
                        $transaction->bank='Paypal';
                        $transaction->save();

                        $Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
                        $Wallet->status = 'Success';
                        $Wallet->save();

                        if(\App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id) <= 0) {

                            $totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($transaction->user_id, true);
                            $totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($transaction->user_id, true);
                            $totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($transaction->user_id, true);
                            $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id, true);

                            $user = \App\Models\User::find($transaction->user_id);
                            $user->stage = 3;
                            $user->save();

                            $resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
                        
                            if($resultSpouse){

                                $resultSpouse->stage = 3;
                                $resultSpouse->payment_status = '2';
                                $resultSpouse->save();
                            }

                            
                            if($user->language == 'sp'){

                                $subject = 'Pago recibido. ¡Gracias!';
                                $msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Se ha recibido la cantidad de $'.$user->amount.' en su cuenta.  </p><p><br></p><p>Gracias por hacer este pago.</p><p> <br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p>IMPORTE TOTAL A PAGAR:'.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p><p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p><p>Atentamente,</p><p>El equipo del GProCongress II</p>';

                            }elseif($user->language == 'fr'){
                            
                                $subject = 'Paiement intégral reçu.  Merci !';
                                $msg = '<p>Cher '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Un montant de '.$user->amount.'$ a été reçu sur votre compte.  </p><p><br></p><p>Vous avez maintenant payé la somme totale pour le GProCongrès II.  Merci !</p><p> <br></p><p>Voici un résumé de l’état de votre paiement :</p><p>MONTANT TOTAL À PAYER:'.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p><p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

                            }elseif($user->language == 'pt'){
                            
                                $subject = 'Pagamento recebido na totalidade. Obrigado!';
                                $msg = '<p>Prezado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Uma quantia de $'.$user->amount.' foi recebido na sua conta.  </p><p><br></p><p>Você agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p> <br></p><p>Aqui está o resumo do estado do seu pagamento:</p><p>VALOR TOTAL A SER PAGO:'.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p><p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';

                            }else{
                            
                                $subject = 'Payment received in full. Thank you!';
                                $msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>An amount of $'.$user->amount.' has been received on your account.  </p><p><br></p><p>You have now paid in full for GProCongress II.  Thank you!</p><p> <br></p><p>Here is a summary of your payment status:</p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
                
                            }
                            \App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
                            \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

                            // \App\Helpers\commonHelper::sendSMS($result->User->mobile);
                            
                            if($user->language == 'sp'){

                                $subject = "Por favor, envíe su información de viaje.";
                                $msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp; &nbsp;&nbsp;</p>';
                            
                            }elseif($user->language == 'fr'){
                            
                                $subject = "Veuillez soumettre vos informations de voyage.";
                                $msg = "<p>Cher '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p>Nous sommes ravis de vous voir au GProCongrès à Panama City, au Panama !</p><p><br></p><p>Pour aider les délégués à obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</p><p><br></p><p>Veuillez répondre à cet e-mail avec vos informations de vol.&nbsp; Dès réception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons reçues sont correctes.&nbsp;</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>";
                    
                            }elseif($user->language == 'pt'){
                            
                                $subject = "Por favor submeta sua informação de viagem";
                                $msg = '<p>Prezado '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Nós estamos emocionados em ver você no CongressoGPro na Cidade de Panamá, Panamá!</p><p><br></p><p>Para ajudar os delegados na obtenção de vistos, nós estamos pedindo que submetam a nós sua informação de viagem.&nbsp;</p><p><br></p><p>Por favor responda este e-mail com informações do seu voo. Depois de recebermos, iremos lhe enviar um e-mail confirmando que a informação que recebemos é correta.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro&nbsp; &nbsp; &nbsp;&nbsp;</p>';
                            
                            }else{
                            
                                $subject = 'Please submit your travel information.';
                                $msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
                                                    
                            }
                            \App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
                            \App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

                        }

                        \Session::flash('gpro_success', \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Payment-Successful'));
                        return redirect('payment');

                    }
                    
                }else{

                    \Session::flash('gpro_error', 'Payment declined!!');
                    return redirect('payment');
                }
                
                

            }else{

                $paymentIntent = \Session::get('paypal_order_id'); 
					
                if(isset($paymentIntent)){

                    $transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();

                    if($transaction){

                        $transaction->payment_status='7';
                        $transaction->status='0';
                        $transaction->save();

                        $Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
                        $Wallet->status = 'Failed';
                        $Wallet->save();

                    }
                }
                
                \Session::flash('gpro_error', $response->getMessage());
                return redirect('payment');
            }

        }else{

            $paymentIntent = \Session::get('paypal_order_id'); 
					
            if(isset($paymentIntent)){

                $transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();

                if($transaction){

                    $transaction->payment_status='7';
                    $transaction->status='0';
                    $transaction->save();

                    $Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
                    $Wallet->status = 'Failed';
                    $Wallet->save();

                }
            }
            
            \Session::flash('gpro_error', 'Payment declined!!');
            return redirect('payment');

        }
    }

    public function PaypalErrorUrl(Request $request)
    {
        
        $paymentIntent = \Session::get('paypal_order_id'); 
					
        if(isset($paymentIntent)){

            $transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();

            if($transaction){

                $transaction->payment_status='7';
                $transaction->status='0';
                $transaction->save();

                $Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
                $Wallet->status = 'Failed';
                $Wallet->save();

            }
        }
        \Session::flash('gpro_error', 'User declined the payment');
        return redirect('payment');

    }

    public function exhibitorPolicy(Request $request) {

        \App\Helpers\commonHelper::setLocale();

        return view('exhibitors.exhibitor_policy');

    }

    public function exhibitorsHome(Request $request){
 
        return view('exhibitors.index');
        
    }

    public function ExhibitorRegistration(Request $request){
	
        if($request->ajax()){

            $rules=[
            
                'name' => 'required',
                'email' => ['required', 'email','unique:users,email'],
                'mobile' => 'required',
                'citizenship' => 'required',
                'business_name' => 'required',
                'business_identification_no' => 'required',
                'website' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'passport_number' => 'required',
                'passport_copy' => 'required',
                'logo' => 'required',
                'salutation' => 'required',
                'mobile_code' => 'required',
                'room' => 'required',
                'diplomatic_passport' => 'required',
            ];

            if($request->post('coming_with_spouse') == 'Yes'){

                $rules['spouse_name'] = 'required';
                $rules['spouse_citizenship'] = 'required';
                $rules['spouse_gender'] = 'required';
                $rules['spouse_dob'] = 'required';
                $rules['spouse_email'] = ['required', 'email','unique:users,email'];
                $rules['spouse_mobile'] = 'required';
                $rules['spouse_passport_number'] = 'required';
                $rules['spouse_passport_copy'] = 'required';
                $rules['spouse_mobile_code'] = 'required';
                $rules['spouse_salutation'] = 'required';
                $rules['spouse_diplomatic_passport'] = 'required';
            }

            if($request->post('any_one_coming_with_along') == 'Yes'){

                $rules['along_name'] = 'array|required';
                $rules['along_citizenship'] = 'array|required';
                $rules['along_gender'] = 'array|required';
                $rules['along_dob'] = 'array|required';
                $rules['along_email'] = 'array|required';
                $rules['along_mobile'] = 'array|required';
                $rules['along_passport_number'] = 'array|required';
                $rules['along_passport_copy'] = 'array|required';
                $rules['along_salutation'] = 'array|required';
                $rules['along_mobile_code'] = 'array|required';
                $rules['along_diplomatic_passport'] = 'array|required';
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

                // try {

                    
                    if($request->post('email') == $request->post('spouse_email')){

                        $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Wehave-duplicate-email-group-users');
                        return response(array( "message"=>$message), 403);
                    
                    }
                    

                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
                    $password = substr(str_shuffle($chars),0,8);
                    $user=new \App\Models\User();
                    $user->name=$request->post('name');
                    $user->email=$request->post('email');
                    $user->parent_id=$request->post('parent_id');
                    $user->citizenship=$request->post('citizenship');
                    $user->phone_code=$request->post('mobile_code');
                    $user->mobile=$request->post('mobile');
                    $user->added_as=$request->post('added_as');
                    $user->dob=$request->post('dob');
                    $user->gender=$request->post('gender');
                    $user->last_name=$request->post('salutation');
                    $user->reg_type='email';
                    $user->designation_id='14';
                    $user->password=\Hash::make($password);
                    $user->otp_verified='Yes';

                    if($request->hasFile('passport_copy')){
                        $imageData = $request->file('passport_copy');
                        $image = 'image_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                        $destinationPath = public_path('/uploads/passport');
                        $imageData->move($destinationPath, $image);

                        // $user->passport_copy = $image;
                    }

                    if($request->hasFile('logo')){
                        $imageData = $request->file('logo');
                        $imageLogo = 'image_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
                        $destinationPath = public_path('/uploads/logo');
                        $imageData->move($destinationPath, $imageLogo);

                        // $user->logo = $image;
                    }

                    $user->save();
                    
                    \App\Helpers\commonHelper::sendExhibitorsRegistrationMailSend($user->id,$password);
                    
                    
                    $data=[
                        'user_id' => $user->id,
                        'business_owner_id' => $user->id,
                        'parent_id' => null,
                        'added_as' => null,
                        'name' => $request->post('name'),
                        'email' => $request->post('email'),
                        'mobile' => $request->post('mobile'),
                        'citizenship' => $request->post('citizenship'),
                        'business_name' => $request->post('business_name'),
                        'business_identification_no' => $request->post('business_identification_no'),
                        'website' => $request->post('website'),
                        'dob' => $request->post('dob'),
                        'gender' => $request->post('gender'),
                        'passport_number' => $request->post('passport_number'),
                        'any_one_coming_with_along' => $request->post('any_one_coming_with_along'),
                        'coming_with_spouse' => $request->post('coming_with_spouse'),
                        'salutation' => $request->post('salutation'),
                        'mobile_code' => $request->post('mobile_code'),
                        'diplomatic_passport' => $request->post('diplomatic_passport'),
                        'room' => $request->post('room'),
                        'passport_copy' =>  $image,
                        'logo' => $imageLogo,

                    ];

                  \App\Helpers\commonHelper::sendExhibitorsRegistrationUserHistory($data);
                  

                    if($request->post('coming_with_spouse') == 'Yes'){

                        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
                        $password = substr(str_shuffle($chars),0,8);

                        $userSpouse=new \App\Models\User();
                        $userSpouse->parent_id=$user->id;
                        $userSpouse->added_as='Spouse';
                        $userSpouse->name=$request->post('spouse_name');
                        $userSpouse->email=$request->post('spouse_email');
                        $userSpouse->mobile=$request->post('spouse_mobile');
                        $userSpouse->citizenship=$request->post('spouse_citizenship');
                        $userSpouse->dob=$request->post('spouse_dob');
                        $userSpouse->gender=$request->post('spouse_gender');
                        $userSpouse->last_name=$request->post('spouse_salutation');
                        $userSpouse->phone_code=$request->post('spouse_mobile_code');
                        $userSpouse->reg_type='email';
                        $userSpouse->designation_id='14';
                        $userSpouse->password=\Hash::make($password);
                        $userSpouse->otp_verified='Yes';

                        if($request->hasFile('spouse_passport_copy')){
                            $imageData = $request->file('spouse_passport_copy');
                            $image = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(0000,99999).'.'.$imageData->getClientOriginalExtension();
                            $destinationPath = public_path('/uploads/passport');
                            $imageData->move($destinationPath, $image);
        
                            // $userSpouse->passport_copy = $image;
                        }

                        $userSpouse->save();

                        \App\Helpers\commonHelper::sendExhibitorsRegistrationMailSend($userSpouse->id,$password);

                        $data=[
                            'user_id' => $userSpouse->id,
                            'business_owner_id' => $user->id,
                            'parent_id' => $user->id,
                            'added_as' => 'Spouse',
                            'name' => $request->post('spouse_name'),
                            'email' => $request->post('spouse_email'),
                            'mobile' => $request->post('spouse_mobile'),
                            'citizenship' => $request->post('spouse_citizenship'),
                            'business_name' =>null,
                            'business_identification_no' =>null,
                            'website' => null,
                            'dob' => $request->post('spouse_dob'),
                            'gender' => $request->post('spouse_gender'),
                            'passport_number' => $request->post('spouse_passport_number'),
                            'any_one_coming_with_along' => null,
                            'coming_with_spouse' => null,
                            'salutation' => $request->post('spouse_salutation'),
                            'mobile_code' => $request->post('spouse_mobile_code'),
                            'diplomatic_passport' => $request->post('spouse_diplomatic_passport'),
                            'room' => null,
                            'passport_copy' =>  $image,
                            'logo' => null,
                            'room' => null,
    
                        ];
                        
                        
                        \App\Helpers\commonHelper::sendExhibitorsRegistrationUserHistory($data);

                    }

                    
                
                    if($request->post('any_one_coming_with_along') == 'Yes'){

                        //check email address
                        $tempArr = array_unique($request->post('along_email'));
                        $uniqueGroupUsers=array_intersect_key($request->post('along_email'), $tempArr);

                        if(count($uniqueGroupUsers) != count($request->post('along_email'))){

                            $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($user->language,'Wehave-duplicate-email-group-users');
                            return response(array( "message"=>$message), 403);
                        
                        }else{

                            $groupEmails = $names = array_column($request->post('along_email'), 'email'); 
                            $checkExistUsers=\App\Models\User::whereIn('email',$groupEmails)->get();

                            if($checkExistUsers->count()>0){

                                $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($user->language,'Wehave-duplicate-email-group-users');
                                return response(array("message"=>$checkExistUsers[0]['email'].$message), 403);

                            }
                        }

                        $users=[];
                        
                        $totalEmail=count($request->post('along_email')); 

                        if($totalEmail>0){

                            for($i=0;$i<$totalEmail;$i++){

                                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                                $image = '';
                                $password = substr(str_shuffle($chars),0,8);

                                $userDataEmail= \App\Models\User::where('email',$request->post('along_email')[$i])->first();
                                if(!$userDataEmail){

                                    $userAlong=new \App\Models\User();
                                    
                                    $userAlong->parent_id=$user->id;
                                    $userAlong->added_as='Group';
                                    $userAlong->name=$request->post('along_name')[$i];
                                    $userAlong->last_name=$request->post('along_salutation')[$i];
                                    $userAlong->phone_code=$request->post('along_mobile_code')[$i];
                                    $userAlong->email=$request->post('along_email')[$i];
                                    $userAlong->mobile=$request->post('along_mobile')[$i];
                                    $userAlong->citizenship=$request->post('along_citizenship')[$i];
                                    $userAlong->dob=$request->post('along_dob')[$i];
                                    $userAlong->gender=$request->post('along_gender')[$i];
                                    $userAlong->reg_type='email';
                                    $userAlong->designation_id='14';
                                    $userAlong->password=\Hash::make($password);
                                    $userAlong->otp_verified='Yes';

                                    if($request->file('along_passport_copy')[$i]){
                                        $imageData = $request->file('along_passport_copy')[$i];
                                        $image = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(000000,9999999).'.'.$imageData->getClientOriginalExtension();
                                        $destinationPath = public_path('/uploads/passport');
                                        $imageData->move($destinationPath, $image);
                    
                                    }

                                    // $userAlong->passport_copy= $image;
                                    $userAlong->save();

                                    $data=[
                                        'user_id' => $userAlong->id,
                                        'business_owner_id' => $user->id,
                                        'parent_id' => $user->id,
                                        'added_as' => 'Group',
                                        'name' => $request->post('along_name')[$i],
                                        'email' => $request->post('along_email')[$i],
                                        'mobile' => $request->post('along_mobile')[$i],
                                        'citizenship' => $request->post('along_citizenship')[$i],
                                        'business_name' =>null,
                                        'business_identification_no' =>null,
                                        'website' => null,
                                        'dob' => $request->post('along_dob')[$i],
                                        'gender' => $request->post('along_gender')[$i],
                                        'passport_number' => $request->post('along_passport_number')[$i],
                                        'any_one_coming_with_along' => null,
                                        'coming_with_spouse' => $request->post('along_spouse')[$i],
                                        'salutation' => $request->post('along_salutation')[$i],
                                        'mobile_code' => $request->post('along_mobile_code')[$i],
                                        'diplomatic_passport' => $request->post('along_diplomatic_passport')[$i],
                                        'room' => null,
                                        'passport_copy' =>  $image,
                                        'logo' => null,
                                        'room' => null,
                
                                    ];

                                    \App\Helpers\commonHelper::sendExhibitorsRegistrationUserHistory($data);

                                    \App\Helpers\commonHelper::sendExhibitorsRegistrationMailSend($userAlong->id,$password);
                        
                                    if($request->post('along_spouse')[$i] == 'Yes'){

                                        $userDataEmail= \App\Models\User::where('email',$request->post('along_spouse_email')[$i])->first();

                                        if(!$userDataEmail){

                                            $userAlongSpouse=new \App\Models\User();
                                            
                                            $userAlongSpouse->parent_id=$userAlong->id;
                                            $userAlongSpouse->added_as='Spouse';
                                            $userAlongSpouse->name=$request->post('along_spouse_name')[$i];
                                            $userAlongSpouse->last_name=$request->post('along_spouse_salutation')[$i];
                                            $userAlongSpouse->phone_code=$request->post('along_spouse_mobile_code')[$i];
                                            $userAlongSpouse->email=$request->post('along_spouse_email')[$i];
                                            $userAlongSpouse->mobile=$request->post('along_spouse_mobile')[$i];
                                            $userAlongSpouse->citizenship=$request->post('along_spouse_citizenship')[$i];
                                            $userAlongSpouse->dob=$request->post('along_spouse_dob')[$i];
                                            $userAlongSpouse->gender=$request->post('along_spouse_gender')[$i];
                                            $userAlongSpouse->reg_type='email';
                                            $userAlongSpouse->designation_id='14';
                                            $userAlongSpouse->password=\Hash::make($password);
                                            $userAlongSpouse->otp_verified='Yes';
                                            

                                            if($request->file('along_spouse_passport_copy')[$i]){
                                                $imageData = $request->file('along_spouse_passport_copy')[$i];
                                                $image = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(000000,9999999).'.'.$imageData->getClientOriginalExtension();
                                                $destinationPath = public_path('/uploads/passport');
                                                $imageData->move($destinationPath, $image);
                            
                                            }
                                            // $userAlongSpouse->passport_copy= $image;
                                            $userAlongSpouse->save();

                                            $data=[
                                                'user_id' => $userAlongSpouse->id,
                                                'business_owner_id' => $user->id,
                                                'parent_id' => $userAlong->id,
                                                'added_as' => 'Spouse',
                                                'name' => $request->post('along_spouse_name')[$i],
                                                'email' => $request->post('along_spouse_email')[$i],
                                                'mobile' => $request->post('along_spouse_mobile')[$i],
                                                'citizenship' => $request->post('along_spouse_citizenship')[$i],
                                                'business_name' =>null,
                                                'business_identification_no' =>null,
                                                'website' => null,
                                                'dob' => $request->post('along_spouse_dob')[$i],
                                                'gender' => $request->post('along_spouse_gender')[$i],
                                                'passport_number' => $request->post('along_spouse_passport_number')[$i],
                                                'any_one_coming_with_along' => null,
                                                'coming_with_spouse' => null,
                                                'salutation' => $request->post('along_spouse_salutation')[$i],
                                                'mobile_code' => $request->post('along_spouse_mobile_code')[$i],
                                                'diplomatic_passport' => $request->post('along_spouse_diplomatic_passport')[$i],
                                                'room' => null,
                                                'passport_copy' =>  $image,
                                                'logo' => null,
                                                'room' => null,
                        
                                            ];
                                            
                                            
                                            \App\Helpers\commonHelper::sendExhibitorsRegistrationUserHistory($data);

                                            \App\Helpers\commonHelper::sendExhibitorsRegistrationMailSend($userAlongSpouse->id,$password);
                        
                                            
                                        }
                                    }

                                    
                                }
                                
                            }

                        } 
                    }

                    $message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile');
                    return response(array("error"=>true, "message"=>'Registration Successfully','reset'=>true), 200);
                    

                // } catch (\Exception $e) {
                // 	return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($user->language,'Something-went-wrongPlease-try-again')), 403);
                // }
            }
        }
        $country = \App\Models\Country::get();
        return view('exhibitors.exhibitors',compact('country'));

    }

    public function attendTheCongress(Request $request) {

        if(isset($_GET['lang']) && $_GET['lang'] !=  ''){
            \App::setLocale($_GET['lang']);
        }
        \App\Helpers\commonHelper::setLocale();

        return view('attend_the_congress');

    }
}
