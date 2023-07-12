<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;

class PreLoginController extends Controller {


	
	public function appUpdateVersionApi(Request $request){
		
		return response(array("version"=>env('MOBILE_APP_VERSIONS'),"message"=>'App Version Api'), 200);

    }

    public function registration(Request $request){
	
	
		$rules = [
            'email' => ['required', 'email','unique:users,email'],
			'password' => 'required|confirmed',
			'terms_and_condition' => 'required|in:0,1',
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'phone_code' => 'required',
			'mobile' => 'required|numeric',
			'password_confirmation' => 'required',
			'language' => 'required|in:en,sp,fr,pt',
		];

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'email_email'),
			'password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'password_required'), 
			'password_confirmation.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'password_confirmed_required'), 
			'password.confirmed' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'password_confirmed'), 
			'language.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'language_required'),  
			'terms_and_condition.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'terms_and_condition_required'),  
			'email.unique' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'email_unique'),
			'last_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'last_name_required'),
			'first_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'first_name_required'),
		);

		$validator = \Validator::make($request->json()->all(), $rules, $messages);
		 
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
				
				$result = \App\Models\User::where([
										['email', '=', $request->json()->get('email')]
										])->first();
										
				if($result){

					return response(array("error"=>true, 'message'=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'Emailalready-existsPlease-trywithanother-emailid')), 403);
				
				}else{
   
					$user=new \App\Models\User();
					$user->email=$request->json()->get('email');
					$user->language=$request->json()->get('language');
					$user->name=$request->json()->get('first_name');
					$user->last_name=$request->json()->get('last_name');
					$user->phone_code=$request->json()->get('phone_code');
					$user->mobile=$request->json()->get('mobile');
					$user->reg_type='email';
					$user->designation_id='2';
					$user->terms_and_condition=$request->json()->get('terms_and_condition');
					$user->password=\Hash::make($request->json()->get('password'));
					$user->otp_verified='No';
					$user->system_generated_password='0';
					$user->save();
					$addedUserId=$user->id;

					$subject='Registration Action';
					$msg='Registration Action';
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Registration Action');
					

					$sendOtpResult = \App\Helpers\commonHelper::callAPI('POST','/send-otp?lang='.$request->json()->get('language'), json_encode(array('email'=>$request->json()->get('email'))));
 
					return response((array)json_decode($sendOtpResult->content), $sendOtpResult->status);
					
				}
				
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->json()->get('language'),'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function sendOtp(Request $request){
		
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}

		$rules = [
            'email' => 'required|email',
		];

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_email'),
		);

		$validator = \Validator::make($request->json()->all(), $rules,$messages);
		 
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}
			
			return response(array('message'=>$message),403);

		}else{

			try{
					
				$otp = \App\Helpers\commonHelper::getOtp();

				$to=$request->json()->get('email'); 
				$token = md5($to);
				\App\Models\User::where('email', $request->json()->get('email'))->update(['otp'=>$token]);
				$userData = \App\Models\User::where('email', $request->json()->get('email'))->first();
 							 
				$UserHistory=new \App\Models\UserHistory();
				$UserHistory->user_id=$userData->id;
				$UserHistory->action='Send Email Verification';
				$UserHistory->save();

				$name = $userData->name.' '.$userData->last_name;

				
				if($userData->language == 'sp'){

					$link = '<a href="'.url('email-registration-confirm/'.$token).'">aqui</a>';

					$subject = 'Se requiere verificación de correo electrónico para inscribirse en el GProCongress II';
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p>¡Felicidades! Ha creado con éxito su cuenta para el GProCongress II. Para verificar su dirección de correo electrónico y completar el proceso de inscripción, utilice este enlace: haga click '.$link.'.</p><p><br></p><p>¿Necesita ayuda o tiene alguna pregunta? Solo tienes que responder a este correo electrónico, y nuestro equipo se pondrá en contacto con usted.&nbsp;</p><p>¿Tiene preguntas? Simplemente responda a este correo para conectarse con algún miembro de nuestro equipo.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';

				}elseif($userData->language == 'fr'){

					$link = '<a href="'.url('email-registration-confirm/'.$token).'">Click here</a>';

					$subject = 'Vérification des e-mails requise pour l’inscription au GProCongrès II';
					$msg = '<p>Cher '.$name.', Félicitations !&nbsp; Vous avez créé avec succès votre compte pour le GProCongrès II.&nbsp; Pour vérifier votre adresse e-mail et terminer le processus d’inscription, veuillez utiliser ce lien : '.$link.'.</p><p><br></p><p><br></p><p>Besoin d’aide ou vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';

				}elseif($userData->language == 'pt'){

					$link = '<a href="'.url('email-registration-confirm/'.$token).'">link</a>';

					$subject = 'Pedido de verificação de e-mail para inscrição ao II CongressoGPro';
					$msg = '<p>Prezado '.$name.',&nbsp;</p><p><br></p><p><br></p><p>Parabéns! Você criou sua conta para o II CongressoGPro com sucesso. Para verificar o seu endereço eletrônico, e completar o processo de inscrição, por favor use este : '.$link.'.&nbsp;</p><p><br></p><p><br></p><p>Precisa de ajuda, ou tem perguntas? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.&nbsp;</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';

				}else{

					$link = '<a href="'.url('email-registration-confirm/'.$token).'">link</a>';

					$subject = 'Email verification required for GProCongress II registration';
					$msg = '<p>Dear '.$name.',&nbsp;</p><p><br></p><p>Congratulations!&nbsp; You have successfully created your account for the GProCongress II.&nbsp; To verify your email address, and complete the registration process, please use this : '.$link.'.</p><p><br></p><p>Need help, or have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';

				}
				
				\Mail::send('email_templates.otp', compact('to', 'msg'), function($message) use ($to, $subject) {
					$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
					$message->subject($subject);
					$message->to($to);
				});
				

				\App\Helpers\commonHelper::userMailTrigger($userData->id,$msg,$subject);

				// $html = view('email_templates.otp', compact('to', 'otp'))->render();
				// $a = \App\Helpers\commonHelper::sendZeptoEmail($html);
				
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userData->language,'EmailVerification-linkhasbeen-sentsuccessfully-onyour-emailid').' : '.$to;
					 	
				return response(array('message'=>$message), 200);
				
			}catch (\Exception $e){

				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403); 
			}
		}
		
	}

	public function validateOtp(Request $request){
		
		$rules = [
            'email' => 'required_:mobile|email', 
            'otp' => 'required|numeric|digits:4'
		];
		
		$validator = \Validator::make($request->json()->all(), $rules);
		
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}
			
			return response(array("error"=>true, "message"=>$message), 403);

		}else{

			try{

				$userResult = \App\Models\User::where('email', $request->json()->get('email'))->where('otp', $request->json()->get('otp'))->first();

				if($userResult){

					$userResult->otp='0';
					
					if($userResult->otp_verified=='No'){

						$userResult->otp_verified='Yes';
						$userResult->Save();

						$UserHistory=new \App\Models\UserHistory();
						$UserHistory->user_id=$userResult->id;
						$UserHistory->action='Submit Otp Verify';
						$UserHistory->save();

						$name = $userResult->name.' '.$userResult->last_name;

						if($userResult->language == 'sp'){

							$url = '<a href="'.url('profile-update').'" target="_blank">aqui</a>';
							$faq = '<a href="'.url('faq').'">aqui</a>';
							$subject = 'Welcome '.$name.", Here's your GProCongress II registration information!";
							$msg = '<span style="color: rgb(0, 0, 0);">Congratulations !</span><br><br><span style="color: rgb(0, 0, 0);">You have successfully created your account for the GProCongress II. To access your account, follow this link: '.$url.'. Please use the link to complete and edit your application at any time.</span><br><br><span style="color: rgb(0, 0, 0);">And, if you would like to find out more about the criteria to attend the Congress, '.$faq.'.</span><br><br><span style="color: rgb(0, 0, 0);">We are here to help! To talk with one of our team members, you can simply respond to this email.</span><br><br><span style="color: rgb(0, 0, 0);">Pray with us toward multiplying the quantity and quality of trainers of pastors.</span><br><br><span style="color: rgb(0, 0, 0);">Warmly,</span><br><span style="color: rgb(0, 0, 0);">GProCongress II Team</span><br>';
						
		
						}elseif($userResult->language == 'fr'){
		
							$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';
							$faq = '<a href="'.url('faq').'">Click here</a>';
							$subject = 'Welcome '.$name.", Here's your GProCongress II registration information!";
							$msg = '<span style="color: rgb(0, 0, 0);">Congratulations !</span><br><br><span style="color: rgb(0, 0, 0);">You have successfully created your account for the GProCongress II. To access your account, follow this link: '.$url.'. Please use the link to complete and edit your application at any time.</span><br><br><span style="color: rgb(0, 0, 0);">And, if you would like to find out more about the criteria to attend the Congress, '.$faq.'.</span><br><br><span style="color: rgb(0, 0, 0);">We are here to help! To talk with one of our team members, you can simply respond to this email.</span><br><br><span style="color: rgb(0, 0, 0);">Pray with us toward multiplying the quantity and quality of trainers of pastors.</span><br><br><span style="color: rgb(0, 0, 0);">Warmly,</span><br><span style="color: rgb(0, 0, 0);">GProCongress II Team</span><br>';
						
		
						}elseif($userResult->language == 'pt'){
		
							$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';
							$faq = '<a href="'.url('faq').'">Click here</a>';
							$subject = 'Welcome '.$name.", Here's your GProCongress II registration information!";
							$msg = '<span style="color: rgb(0, 0, 0);">Congratulations !</span><br><br><span style="color: rgb(0, 0, 0);">You have successfully created your account for the GProCongress II. To access your account, follow this link: '.$url.'. Please use the link to complete and edit your application at any time.</span><br><br><span style="color: rgb(0, 0, 0);">And, if you would like to find out more about the criteria to attend the Congress, '.$faq.'.</span><br><br><span style="color: rgb(0, 0, 0);">We are here to help! To talk with one of our team members, you can simply respond to this email.</span><br><br><span style="color: rgb(0, 0, 0);">Pray with us toward multiplying the quantity and quality of trainers of pastors.</span><br><br><span style="color: rgb(0, 0, 0);">Warmly,</span><br><span style="color: rgb(0, 0, 0);">GProCongress II Team</span><br>';
						
		
						}else{
		
							$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
							$faq = '<a href="'.url('faq').'">Click here</a>';
							$subject = 'Welcome '.$name.", Here's your GProCongress II registration information!";
							$msg = '<p><span style="color: rgb(0, 0, 0);">Congratulations !</span><br></p><p><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;;
							mso-ascii-theme-font:minor-latin;mso-fareast-font-family:&quot;Times New Roman&quot;;
							mso-hansi-theme-font:minor-latin;mso-bidi-theme-font:minor-latin;color:black;
							mso-ansi-language:EN-IN;mso-fareast-language:EN-GB;mso-bidi-language:AR-SA">You
							have successfully created your account for the GProCongress II. To access,
							edit, and complete your account at any time, use this :&nbsp;</span><span style="color: rgb(0, 0, 0);">'.$url.'</span><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;;
							mso-ascii-theme-font:minor-latin;mso-fareast-font-family:&quot;Times New Roman&quot;;
							mso-hansi-theme-font:minor-latin;mso-bidi-theme-font:minor-latin;color:black;
							mso-ansi-language:EN-IN;mso-fareast-language:EN-GB;mso-bidi-language:AR-SA">.</span><br><br><span style="font-size:12.0pt;font-family:&quot;Calibri&quot;,&quot;sans-serif&quot;;
							mso-ascii-theme-font:minor-latin;mso-fareast-font-family:&quot;Times New Roman&quot;;
							mso-hansi-theme-font:minor-latin;mso-bidi-theme-font:minor-latin;color:black;
							mso-ansi-language:EN-IN;mso-fareast-language:EN-GB;mso-bidi-language:AR-SA">If
							you want more information about the eligibility criteria for potential Congress
							attendees, before proceeding,</span><span style="color: rgb(0, 0, 0);">&nbsp;'.$faq.'.</span><br><br><font color="#000000">To speak with one of our team members, you can simply respond to this email. We are here to help!</font><br><br><font color="#000000">Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</font><br><br><span style="color: rgb(0, 0, 0);">Warmly,</span></p><p><font color="#000000">The GProCongress II Team</font><br></p>';
						
						}

						\App\Helpers\commonHelper::userMailTrigger($userResult->id,$msg,$subject);
						
						
						$to = $request->json()->get('email');

						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						
						$msg = 'Hi,<br>Thank you for your interest in GProCongress II conference in Panama. Kindly click the link below and complete your registration.<br>'.$url;
						\App\Helpers\commonHelper::emailSendToAdmin('[GProCongress II Admin]  New User Registration', $msg);

						// \App\Helpers\commonHelper::sendSMS($userResult->mobile);

						return response(array('message'=>'Your registration has been completed successfully, Please update your profile.', "token"=>$userResult->createToken('authToken')->accessToken, "result"=>$userResult->toArray()), 200);
					} 

					$userResult->Save();

 
					return response(array('message'=>'OTP verified successfully.'), '200');

				}else{
					return response(array("error"=>true, "message"=>"OTP doesn't exist. Please try again."), 403);
				}
				
			}catch (\Exception $e){
				return response(array("error"=>true, "message"=>$e->getMessage()), 403); 
			}
		}
	}

	public function validateToken(Request $request){
		
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}
		$rules = [
            'token' => 'required',
		];

		
		$messages = array(
			'token.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'token_required'),
		);
		
		$validator = \Validator::make($request->json()->all(), $rules,$messages);
		
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}
			
			return response(array("error"=>true, "message"=>$message), 403);

		}else{

			try{

				$userResult = \App\Models\User::where('otp', $request->json()->get('token'))->first();

				if($userResult){

					
					if($userResult->otp_verified=='No'){

						$userResult->otp_verified='Yes';
						
						$userResult->otp='0';
						$userResult->Save();

						$UserHistory=new \App\Models\UserHistory();
						$UserHistory->user_id=$userResult->id;
						$UserHistory->action='Submit Token Verify';
						$UserHistory->save();

						$name = $userResult->name.' '.$userResult->last_name;

						if($userResult->language == 'sp'){

							
							$url = '<a href="'.url('profile-update').'" target="_blank">aqui</a>';
							$faq = '<a href="'.url('faq').'">aqui</a>';
							$subject = "¡Bienvenida, aquí esta su información de inscripcion al GproCongress II!";
							$msg = '<p>Felicidades, '.$name.'</p><p><br></p><p>Usted a creado su cuenta para el GproCongress II satisfactoriamente. Para acceder, editar y completar su cuenta en cualquier moemento, haga click&nbsp; '.$url.'</p><p><br></p><p>Si usted desea más información sobre los criterios de admisibilidad para candidatos potenciales al congreso, antes de continuar, haga click&nbsp;, '.$faq.'.</p><p><br></p><p>Para hablar con uno de los miembros de nuestro equipo, usted solo tiene que responder a este email. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';//Vineet - 080123
						
		
						}elseif($userResult->language == 'fr'){
		
							$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';
							$faq = '<a href="'.url('faq').'">cliquez ici</a>';
							$subject = "Ligne d’objet : Bienvenue ".$name.", voici vos informations d’inscription du GProCongrès II !";
							$msg = '<p>Félicitations, '.$name.' !</p><p><br></p><p>Vous avez créé avec succès votre compte pour le GProCongrès II. Pour accéder, modifier et compléter votre compte à tout moment, utilisez ce lien : '.$url.'.</p><p><br></p><p>Si vous souhaitez plus d’informations sur les critères d’éligibilité pour les participants potentiels au Congrès, avant de continuer,  '.$faq.'..</p><p><br></p><p>Pour parler à l’un des membres de notre équipe, vous pouvez simplement répondre à ce courriel. Nous sommes là pour vous aider !</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
		
						}elseif($userResult->language == 'pt'){
		
							$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
							$faq = '<a href="'.url('faq').'">clique aqui</a>';
							$subject = "Assunto: Bem-vindo ".$name.", aqui está sua informação da inscrição para o II CongressoGPro!";
							$msg = '<p>Parabéns, '.$name.'!</p><p><br></p><p>Você criou sua conta para o II CongressoGPro com sucesso. Para aceder, editar e completar a sua conta a qualquer momento, use este '.$url.'.: </p><p><br></p><p>Se você precisa de mais informações sobre o critério de elegibilidade para participantes potenciais ao Congresso, antes de continuar,  '.$faq.'.</p><p><br></p><p>Para falar com um dos nossos membros da equipe, você pode simplesmente responder a este e-mail. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, a medida que nos esforçamos para multiplicar o número, e desenvolvemos a capacidade dos treinadores de pastores&nbsp;</p><p><br></p><p>Calorosamente,</p><p><br></p><p>A Equipe do II CongressoGPro</p>';
						
		
						}else{
		
							$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
							$faq = '<a href="'.url('faq').'">click here</a>';
							$subject = 'Welcome '.$name.", Here's your GProCongress II registration information!";
							$msg = '<div>Congratulations, '.$name.' !</div><div><br></div><div>You have successfully created your account for the GProCongress II. To access, edit, and complete your account at any time, use this '.$url.'.: </div><div><br></div><div>If you want more information about the eligibility criteria for potential Congress attendees, before proceeding,  '.$faq.'.</div><div><br></div><div>To speak with one of our team members, you can simply respond to this email. We are here to help!</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div>';
						
						}

						\App\Helpers\commonHelper::userMailTrigger($userResult->id,$msg,$subject);

						$to = $userResult->email;
						$name = $userResult->name.' '.$userResult->last_name;
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						
						$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has shown interest in GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
						\App\Helpers\commonHelper::emailSendToAdmin('[GProCongress II Admin]  New User Registration', $msg);

						// \App\Helpers\commonHelper::sendSMS($userResult->mobile);

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile');

						return response(array('message'=>$message, "token"=>$userResult->createToken('authToken')->accessToken, "result"=>$userResult->toArray()), 200);
					} 

					$userResult->Save();

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'Confirmation-Successful');

					return response(array('message'=>$message), '200');

				}else{

					return response(array("error"=>true, "langError"=>true, "message"=>'Link not found'), 403);
				}
				
			}catch (\Exception $e){
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403); 
			}
		}
	}

	public function login(Request $request){
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}
		$rules = [
            'email' => 'required|email',
			'password' => 'required',
		];

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_email'),
			'password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'password_required'), 
			
		);
		
		$validator = \Validator::make($request->json()->all(), $rules,$messages);
		
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message = $msg[0];
				break;
			}
			
			return response(array("error"=>true, 'message'=>$message), 403);

		}else{

			try{
				
				$userResult = \App\Models\User::where([['email','=',$request->json()->get('email')]])->first();
				
				if (!$userResult) {

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'This-account-doesnot-exist');
					return response(array("error"=>true, 'message'=> $message), 403);

				} else if ($userResult->status == '1') {

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'YourAccount-hasbeenBlocked-Pleasecontact-Administrator');
					return response(array("error"=>true, 'message'=> $message), 403);

				} else if (\Hash::check($request->json()->get('password'), $userResult->password) && $userResult->otp_verified=='Yes') {

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->user_id=$userResult->id;
					$UserHistory->action='User Login';
					$UserHistory->save();

					if($request->json()->get('device_token')){

						$userResult->device_token=$request->json()->get('device_token');
						$userResult->save();
					}
					

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'Your-registration-hasbeen-completed-successfullyPlease-updateyourProfile');
					return response(array("error"=>false,'message'=> $message, "otp_verified"=>'Yes',"token"=>$userResult->createToken('authToken')->accessToken, "result"=>$userResult->toArray()), 200);
					
				} else if (\Hash::check($request->json()->get('password'), $userResult->password) && $userResult->otp_verified=='No'){

					$sendOtpResult = \App\Helpers\commonHelper::callAPI('POST','/send-otp?lang='.$userResult->language, json_encode(array('email'=>$request->json()->get('email'))));
					$response=(array)json_decode($sendOtpResult->content);
					$response['otp_verified']='No';
					$response['error']=false;


					return response($response, $sendOtpResult->status);
					
				}else{


					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'Invalid-Password');
					return response(array("error"=>true, 'message'=>$message), 403); 
				}

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403); 
			}
		}
	}

	public function profileUpdateReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['profile_update', '=', '0'],['designation_id', '!=', '14'], ['stage', '=', '0'], ['email_reminder', '=', '1']])
									->whereDate('created_at', '=', now()->subDays(2)->setTime(0, 0, 0)->toDateTimeString())
									->get();
									
			
			if(count($results) > 0){
				foreach ($results as $key => $result) {
					
					$created_at = \Carbon\Carbon::parse(date('Y-m-d', strtotime($result->created_at)));
					$today =  \Carbon\Carbon::parse(date('Y-m-d'));
				
					$days = $today->diffInDays($created_at);

					
					$to = $result->email;
					$name= $result->name.' '.$result->last_name;
					
					if($result->language == 'sp'){
						$url = '<a href="'.url('profile-update').'" target="_blank">aqui</a>';

						$subject = "Un cordial recordatorio: Complete su solicitud para el GProCongress II";
						$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Su solicitud para asistir al GProCongress II no ha sido completada.</p><p><br></p><p>Por favor, utilice este enlace haga click '.$url.' para acceder, editar y completer su cuenta en cualquier momento. Si completa el formulario a tiempo, nos ayudará a asegurarle su lugar.</p><p>¿Todavía tiene preguntas o necesita ayuda?</p><p><br></p><p>Simplemente responda a este correo, para conectarse con algún miembro nuestro equipo. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($result->language == 'fr'){
						$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';

						$subject = "Rappel amical : Compléter votre demande du GProCongrès II";
						$msg = '<p>Cher '.$name.',</p><p>Votre demande pour assister au GProCongrès II est incomplète !</p><p>Veuillez utiliser ce lien '.$url.' pour accéder, modifier et compléter votre compte à tout moment. L’achèvement en temps opportun nous aidera à sécuriser votre place !&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ?</p><p>Il suffit de répondre à cet e-mail pour communiquer avec un membre de l’équipe. Nous sommes là pour vous aider !</p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
					
					}elseif($result->language == 'pt'){
						$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';

						$subject = "Lembrete amigável: Complete a sua inscrição para o II CongressoGPro";
						$msg = '<p>Prezado '.$name.',</p><p><br></p><p>A sua inscrição para participar do II CongressoGPro está incompleta!</p><p>Por favor use este  '.$url.' para aceder, editar e completar a sua conta a qualquer momento. O término em tempo útil nos ajudará a garantir a sua vaga!&nbsp;</p><p><br></p><p>Tem alguma pergunta por agora? Simplesmente responda a este e-mail para se conectar com membro da nossa equipe. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
					
					}else{
						$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';

						$subject = 'Friendly reminder: Complete your GProCongress II application';
						$msg = '<div>Dear '.$name.',</div><div><br></div><div>Your application to attend the GProCongress II is incomplete!</div><div>Please use this  '.$url.'; to access, edit, and complete your account at any time. Timely completion will help us secure your spot!&nbsp;</div><div><br></div><div>Do you still have questions, or require assistance&nbsp;<span style="color: rgb(0, 0, 0); background-color: transparent;">Simply respond to this email to connect with a team member. We are here to help!&nbsp;</span><span style="color: rgb(0, 0, 0); background-color: transparent;">Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</span></div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div>';
						
					}

					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id,$subject,$msg,'Your GProCongress II application needs to be completed');
					
					// \App\Helpers\commonHelper::sendSMS($result->mobile);
				}
				
				// return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			$results = \App\Models\User::where([['user_type', '=', '2'], ['profile_update', '=', '0'], ['stage', '=', '0']])
									->whereDate('created_at', '=', now()->subDays(10)->setTime(0, 0, 0)->toDateTimeString())
									->get();
									
			
			if(count($results) > 0){
				foreach ($results as $key => $result) {
					
					$created_at = \Carbon\Carbon::parse(date('Y-m-d', strtotime($result->created_at)));
					$today =  \Carbon\Carbon::parse(date('Y-m-d'));
				
					$days = $today->diffInDays($created_at);

				
					$to = $result->email;
					$name= $result->name.' '.$result->last_name;
					
					if($result->language == 'sp'){
						$url = '<a href="'.url('profile-update').'" target="_blank">aqui</a>';
					
						$subject = "Un cordial recordatorio: Complete su solicitud para el GProCongress II";
						$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Su solicitud para asistir al GProCongress II no ha sido completada.</p><p><br></p><p>Por favor, utilice este enlace haga click '.$url.' para acceder, editar y completer su cuenta en cualquier momento. Si completa el formulario a tiempo, nos ayudará a asegurarle su lugar.</p><p>¿Todavía tiene preguntas o necesita ayuda?</p><p><br></p><p>Simplemente responda a este correo, para conectarse con algún miembro nuestro equipo. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($result->language == 'fr'){
						$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
					
						$subject = "Rappel amical : Compléter votre demande du GProCongrès II";
						$msg = '<p>Cher '.$name.',</p><p>Votre demande pour assister au GProCongrès II est incomplète !</p><p>Veuillez utiliser ce lien '.$url.' pour accéder, modifier et compléter votre compte à tout moment. L’achèvement en temps opportun nous aidera à sécuriser votre place !&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ?</p><p>Il suffit de répondre à cet e-mail pour communiquer avec un membre de l’équipe. Nous sommes là pour vous aider !</p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
					
					}elseif($result->language == 'pt'){
						$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
					
						$subject = "Lembrete amigável: Complete a sua inscrição para o II CongressoGPro";
						$msg = '<p>Prezado '.$name.',</p><p><br></p><p>A sua inscrição para participar do II CongressoGPro está incompleta!</p><p>Por favor use este  '.$url.' para aceder, editar e completar a sua conta a qualquer momento. O término em tempo útil nos ajudará a garantir a sua vaga!&nbsp;</p><p><br></p><p>Tem alguma pergunta por agora? Simplesmente responda a este e-mail para se conectar com membro da nossa equipe. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
					
					}else{
						$url = '<a href="'.url('profile-update').'" target="_blank">link</a>';
					
						$subject = 'Friendly reminder: Complete your GProCongress II application';
						$msg = '<div>Dear '.$name.',</div><div><br></div><div>Your application to attend the GProCongress II is incomplete!</div><div>Please use this '.$url.'; to access, edit, and complete your account at any time. Timely completion will help us secure your spot!&nbsp;</div><div><br></div><div>Do you still have questions, or require assistance&nbsp;<span style="color: rgb(0, 0, 0); background-color: transparent;">Simply respond to this email to connect with a team member. We are here to help!&nbsp;</span><span style="color: rgb(0, 0, 0); background-color: transparent;">Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</span></div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div>';
						
					}

					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id,$subject,$msg,'Your GProCongress II application needs to be completed');
					
					// \App\Helpers\commonHelper::sendSMS($result->mobile);
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function paymentReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'],['designation_id', '!=', '14'], ['profile_status','=','Approved'], ['stage', '=', '2'], ['email_reminder', '=', '1']])
									->whereDate('status_change_at', '=', now()->subDays(5)->setTime(0, 0, 0)->toDateTimeString())
									->get();
									
			if(count($results) > 0){
				foreach ($results as $key => $result) {
				
					$to = $result->email;

					$userData = \App\Models\User::where('id',$result->id)->first();
					if($userData){
						$userData->status_change_at = date('Y-m-d H:i:s');
						$userData->save();
					}
					
					$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($result->id, true);
					$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($result->id, true);
					$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($result->id, true);
					$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($result->id, true);

					if($result->language == 'sp'){

						$subject = "Su pago está pendiente";
						$msg = '<p>Estimado '.$result->name.',&nbsp;</p><p><br></p>
						<p>¿Sabía que su pago reciente a GProCongress II sigue pendiente en este momento?</p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p>
						<p>IMPORTE TOTAL A PAGAR: '.$userData->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p><br></p>
						<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
						<p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p>
						<p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo se comunicará con usted.</p><p><br></p>
						<p>Lo mantendremos informado sobre si su pago ha sido aceptado o rechazado.</p><p><br></p><p><br></p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
						<p>Para realizar el pago ingrese a <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
						<p>Para mayor información vea el siguiente tutorial https://youtu.be/xSV96xW_Dx0 </p>
							<p>Atentamente,</p><p><br></p>
							<p>El equipo del GProCongress II</p>';
					
					}elseif($result->language == 'fr'){
					
						$subject = "Votre paiement est en attente";
						$msg = '<p>Cher '.$result->name.',&nbsp;</p><p><br></p>
						<p>Saviez-vous que votre récent paiement pour le GProCongrès II est toujours en attente en ce moment ?&nbsp;</p>
						<p>Voici un résumé actuel de l’état de votre paiement :&nbsp;</p><p><br></p>
						<p>MONTANT TOTAL À PAYER : '.$userData->amount.'</p>
						<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p>
						<p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p>
						<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
						<p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p>
						<p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p>
						<p>Nous vous tiendrons au courant si votre paiement a été accepté ou refusé.&nbsp;</p><p><br></p>
						<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
						<p>Pour effectuer le paiement, veuillez vous rendre sur <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
						<p>Pour plus d` informations, regardez le tutoriel https://youtu.be/xSV96xW_Dx0 </p>
							<p>Cordialement,</p><p>&nbsp;L’équipe GProCongrès II</p>';
					
					}elseif($result->language == 'pt'){
					
						$subject = "Seu pagamento está pendente";
						$msg = '<p>Prezado '.$result->name.',</p><p><br></p>
						<p>Sabia que o seu recente pagamento para o II CongressoGPro ainda está pendente até agora?</p>
						<p>Aqui está o resumo atual do estado do seu pagamento:</p><p><br></p>
						<p>VALOR TOTAL A SER PAGO: '.$userData->amount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p>
						<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
    					<p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p>
						<p>Tem perguntas? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p>
						<p>Nós vamos lhe manter informado sobre seu pagamento se foi aceite ou declinado.&nbsp;</p><p><br></p>
						<p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Para fazer o pagamento, favor ir par <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
							<p>Para mais informações, veja o tutorial https://youtu.be/xSV96xW_Dx0 </p>
								<p>Calorosamente,</p>
								<p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Your payment is pending';
						$msg = '<p>Dear '.$result->name.',</p><p><br></p>
						<p>Did you know your recent payment to GProCongress II is still pending at this time?</p>
						<p>Here is a current summary of your payment status:</p><p><br></p>
						<p>TOTAL AMOUNT TO BE PAID: '.$userData->amount.'</p>
						<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED: '.$totalAcceptedAmount.'</p>
						<p>PAYMENTS CURRENTLY IN PROCESS: '.$totalAmountInProcess.'</p>
						<p>REMAINING BALANCE DUE: '.$totalPendingAmount.'</p><p><br></p>
						<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
						<p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p>
						<p>Do you have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p>
						<p>We will keep you updated about whether your payment has been accepted or declined.</p><p><br></p>
						<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
						<p>To make the payment please go to <a href="https://www.gprocongress.org/payment" traget="blank"> www.gprocongress.org/payment </a> </p>
						<p>For more information watch the tutorial https://youtu.be/xSV96xW_Dx0 </p>
							<p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
					
					}

					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id, $subject, $msg, 'Please Complete Your Payment');
					
					// \App\Helpers\commonHelper::sendSMS($result->mobile);
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function forgotPassword(Request $request){
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}
		$rules['email'] = 'required|email';
		
		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_email'),
		);

		$validator = \Validator::make($request->json()->all(), $rules, $messages);
		
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message= $msg[0];
				break;
			}
			
			return response(array("error"=>true, 'message'=>$message), 403);
			
		}else{

			try{
				
				$userResult=\App\Models\User::where([['email','=',$request->json()->get('email')]])->first();

				$lang = 'en';
				if(isset($_GET['lang']) && $_GET['lang'] != ''){

					$lang = $_GET['lang'];
					
				}
				
				if(!$userResult){
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'This-account-doesnot-exist');
					return response(array('message'=>$message), 403);
					
				}else{

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($userResult->language,'WeHave-sentPassword-resetLinkOn-yourEmail-address');
					return response(array('message'=>$message,"token"=>md5(rand(1111,4444))), 200);

				}
								
			}catch (\Exception $e){
				
				return response(array("error"=>true, "message" => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403);
			
			}
		}
	}

	public function resetPassword(Request $request){
		
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}

		$rules = [
            'token' => 'required',
            'email' => 'required|email',    
            'password' => 'required|confirmed'
		];

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_email'),
			'token.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'token_required'),			
			'password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'password_required'), 

		);

		$validator = \Validator::make($request->json()->all(), $rules, $messages);
		 
		if ($validator->fails()) {
			$message = [];
			$messages_l = json_decode(json_encode($validator->messages()), true);
			foreach ($messages_l as $msg) {
				$message= $msg[0];
				break;
			}
			
			return response(array('message'=>$message),403);
			
		}else{

			try{
				
				$emailResult=\App\Models\User::where([['email','=',$request->json()->get('email')]])->first();
				
				if(!$emailResult){
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Sorry-yourAccount-didntPassOur-verifiCationsystem');
					return response(array('message'=>$message), 403);
				
				}else{

					$tokenResult=\DB::table('password_resets')->where([
											['email','=',$request->json()->get('email')],
											['token','=',$request->json()->get('token')],
											])->first();

					if(!$tokenResult){

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'This-account-doesnot-exist');
						return response(array('message'=> $message), 403);
					}else{

						$user = \App\Models\User::where('email', $request->json()->get('email'))->update([
							'password'=>\Hash::make($request->json()->get('password'))
						]);

						\DB::table('password_resets')->where(['email'=> $request->json()->get('email')])->delete();

						$name = $emailResult->name.' '.$emailResult->last_name;

						if($emailResult->language == 'sp'){

							$subject = "¡Éxito! Su contraseña ha sido cambiada.";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Usted ha cambiado correctamente su contraseña.</p><p><br></p><p>¿Aún tiene preguntas o necesita ayuda? Simplemente, responda a este correo electrónico y nuestro equipo se pondrá en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($emailResult->language == 'fr'){
						
							$subject = "Succès! Votre mot de passe a été modifié";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Vous avez changé votre mot de passe avec succès.&nbsp;&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
						}elseif($emailResult->language == 'pt'){
						
							$subject = "Sucesso! Sua senha foi alterada";
							$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Você mudou sua senha com sucesso.&nbsp;</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assistência? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Success! Your password has been changed';
							$msg = '<div>Dear '.$name.',</div><div><br></div><div>You have successfully changed your password.&nbsp;</div><div><br></div><div>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div>';
							
						}

						\App\Helpers\commonHelper::userMailTrigger($emailResult->id,$msg,$subject);

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($emailResult->id,$subject,$msg,'User Reset Password');
					
						\App\Helpers\commonHelper::emailSendToUser($request->json()->get('email'), $subject, $msg);

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($emailResult->language,'NewPassword-update-successful');
						return response(array('message'=>$message), 200);

					}
					
				}
				
			}catch (\Exception $e){
				
				return response(array("error" => true, "message" => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403);
			
			}
		}

    }

	public function help(Request $request){
		
		
		$lang = 'en';
		if(isset($_GET['lang']) && $_GET['lang'] != ''){

			$lang = $_GET['lang'];
			
		}
		$rules = [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'message' => 'required',
			'phonecode' => 'required',
		];

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'email_email'),			
			'mobile.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'mobile_required'), 
			'mobile.numeric' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'mobile_numeric'), 
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'name_required'), 
			'phonecode.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'phonecode_required'), 
			'message.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'message_required'), 

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

				return response(array('message'=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Your_submission_has_been_sent'),"error" => false), 200);
				
			}catch (\Exception $e){
				
				return response(array("error" => true, "message" => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($lang,'Something-went-wrongPlease-try-again')), 403);
			
			}
		}

    }

	public function webhookResponse(){ 

		$payload = file_get_contents('php://input');
			
		$event = \Stripe\Event::constructFrom(
			json_decode($payload, true)
		);

		$console=new \App\Models\PaymentConsole();

		$console->value=file_get_contents('php://input');
		$console->order_id=$event->data->object->metadata->order_id;

		$console->save();

		try{
			
			$event = null;

			try {
				$event = \Stripe\Event::constructFrom(
					json_decode($payload, true)
				);
				
			} catch(\UnexpectedValueException $e) {
				// Invalid payload
				http_response_code(400);
				exit();
			}

			
			// Handle the event
			switch ($event->type) {
				case 'payment_intent.succeeded':
					$paymentIntent = $event->data->object->metadata->order_id; 
					
					if(isset($paymentIntent)){

						$transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();
		
						if($transaction){
		
							$transaction->razorpay_order_id=$event->id;
							$transaction->razorpay_paymentid=$event->data->object->charges->data[0]->payment_intent;
							$transaction->card_id=$event->data->object->charges->data[0]->payment_method_details->type;
							$transaction->bank=$event->data->object->charges->data[0]->payment_method;
							$transaction->description=$event->data->object->charges->data[0]->description;
							$transaction->bank_transaction_id=$event->data->object->charges->data[0]->balance_transaction;
							// $transaction->customer_id=$event->data->object->charges->data[0]->customer;
							$transaction->payment_status='2';
							$transaction->status='1';
							$transaction->method='Online';
							$transaction->bank='Card';
							$transaction->save();

							$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
							$Wallet->status = 'Success';
							$Wallet->save();

							$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('order_id',$transaction->order_id)->where('profile_status','Approved')->where('payment_status','Pending')->first();
									
							if(!$userDataresult){


								if($transaction->particular_id == '5'){

									$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$transaction->user_id)->first();
				
									if($roomupgrade){

										$roomupgrade->status ='Accept';
										$roomupgrade->token =null;
										$roomupgrade->save();
									}
									
									$user = \App\Models\User::find($transaction->user_id);

									if($user){
										$user->room = $roomupgrade->category;
										$user->change_room_type = $roomupgrade->room_type;
										$user->upgrade_category = $roomupgrade->category;
										$user->save();	

										$spouseResult=\App\Models\User::where('parent_id',$user->id)->where('added_as','Spouse')->first();
										if($spouseResult){
											$spouseResult->change_room_type = $roomupgrade->room_type;
											$spouseResult->upgrade_category = $roomupgrade->category;
											$spouseResult->save();	
										}

										$spouseParentResult=\App\Models\User::where('id',$user->parent_id)->first();
										if($spouseParentResult){
											$spouseParentResult->change_room_type = $roomupgrade->room_type;
											$spouseParentResult->upgrade_category = $roomupgrade->category;
											$spouseParentResult->save();	
										}

										if($user->language == 'sp'){

											$subject = 'Hemos recibido el pago por el cambio de habitación en el GProCongress II.';
											$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.',</p>
											<p>Se ha recibido un pago suyo por un importe de $'.$transaction->amount.'.  Gracias por su pago.  Ha efectuado el pago correspondiente a la mejora de categoría de su habitación en el GProCongress II. </p>
											<p>Si usted tiene alguna pregunta acerca de su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
											<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de capacitadores de pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipo GProCongress II</p>';

										}elseif($user->language == 'fr'){

											$subject = 'Le paiement de votre surclassement à GProCongress II a été reçu.';
											$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
											<p>Nous avons reçu un paiement de votre part d'un montant de $".$transaction->amount."  Nous vous remercions pour votre paiement. Vous avez maintenant réglé le surclassement de votre chambre au GProCongress II.</p>
											<p>Si vous avez des questions concernant votre paiement ou si vous souhaitez parler à l'un des membres de notre équipe, répondez simplement à cet e-mail.</p>
											<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
											<p>Chaleureusement,</p>
											<p>L'équipe de GProCongress II</p>";

										}elseif($user->language == 'pt'){

											$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recebido.';
											$msg = '<p>Prezado  '.$user->name.' '.$user->last_name.',</p>
											<p>Foi recebido um pagamento da sua parte no valor de $'.$transaction->amount.'.  Obrigado pelo seu pagamento.  Já pagou o upgrade de quarto no GProCongress II.  </p>
											<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.											</p>
											<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipe do GProCongress II</p>';

										}else{
									
											$subject = 'Payment for your room upgrade at GProCongress II has been received';
											$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
											<p>A payment has been received from you in the amount of $'.$transaction->amount.' Thank you for your payment.  You have now paid for your room upgrade at GProCongress II.  </p>
											<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
											<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
											<p>Warmly,</p>
											<p>The GProCongress II Team</p>';

										}

										\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
										\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,'Payment for your room upgrade at GProCongress II has been received','Payment for your room upgrade at GProCongress II has been received','Payment for your room upgrade at GProCongress II has been received');

										
									}
									

								}else{

									$user = \App\Models\User::find($transaction->user_id);

									if(\App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id) <= 0) {

										$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($transaction->user_id, true);
										$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($transaction->user_id, true);
										$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($transaction->user_id, true);
										$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id, true);

										$userData = \App\Models\User::where('id',$user->id)->first();

										$userData->stage = '3';
										$userData->status_change_at = date('Y-m-d H:i:s');
										$userData->save();

										$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
									
										if($resultSpouse){

											$resultSpouse->stage = '3';
											$resultSpouse->payment_status = '2';
											$resultSpouse->status_change_at = date('Y-m-d H:i:s');
											$resultSpouse->save();

											if($resultSpouse->language == 'sp'){

												$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
												$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
												$msg = "<p>Estimado ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
												<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
												<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
												<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
												<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
												<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
												<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
						
											}elseif($resultSpouse->language == 'fr'){
											
												$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
												$msg = "<p>Cher  ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
												<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
												<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
												<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
												<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
												<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
												<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
						
											}elseif($resultSpouse->language == 'pt'){
											
												$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
												$msg = "<p>Caro ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
												<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
												<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
												<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
												<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
												<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
												<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
												<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
												<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
						
											}else{
											
												$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
												$subject = 'GProCongress II registration!  Please login and submit your passport information.';
												$msg = "<p>Dear ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
												<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
												<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
												<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
												<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
												<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
												<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
												<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
												<p>Warmly,</p><p>GProCongress II Team</p>";
								
											}
						
											\App\Helpers\commonHelper::userMailTrigger($resultSpouse->id,$msg,$subject);
											\App\Helpers\commonHelper::emailSendToUser($resultSpouse->email, $subject, $msg);
											\App\Helpers\commonHelper::sendNotificationAndUserHistory($resultSpouse->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
						
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
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Payment received in full. Thank you!');
		
										// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
										
										if($user->language == 'sp'){

											$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
											$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
											$msg = "<p>Estimado ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
											<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
											<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
											<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
											<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
											<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
											<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
					
										}elseif($user->language == 'fr'){
										
											$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
											$msg = "<p>Cher  ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
											<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
											<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
											<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
											<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
											<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
											<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
					
										}elseif($user->language == 'pt'){
										
											$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
											$msg = "<p>Caro ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
											<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
											<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
											<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
											<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
											<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
											<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
											<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
											<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
					
										}else{
										
											$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
											$subject = 'GProCongress II registration!  Please login and submit your passport information.';
											$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
											<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
											<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
											<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
											<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
											<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
											<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
											<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
											<p>Warmly,</p><p>GProCongress II Team</p>";
							
										}
					
										\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
										\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
					

									}else{

										$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($transaction->user_id, true);
										$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($transaction->user_id, true);
										$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($transaction->user_id, true);
										$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id, true);

										if($user->language == 'sp'){

											$subject = 'Pago parcial Aprobado. ¡Gracias!';
											$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Se ha aprobado una cantidad de  $'.$transaction->amount.' en su cuenta.  </p><p><br></p><p>Gracias por hacer este pago.</p><p> <br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p>IMPORTE TOTAL A PAGAR:'.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
											<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p><p></p>
											<p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p><br></p>
					
											<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p><p>Atentamente,</p><p>El equipo del GProCongress II</p>';

										}elseif($user->language == 'fr'){
										
											$subject = 'Paiement partiel Approuvéu.  Merci !';
											$msg = '<p>Cher '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Un montant de  '.$transaction->amount.'$ a été approuvé sur votre compte.  </p><p><br></p><p>Vous avez maintenant payé la somme totale pour le GProCongrès II.  Merci !</p><p> <br></p>
											<p>Voici un résumé de l’état de votre paiement :</p><p>MONTANT TOTAL À PAYER:'.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
											<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p><p></p>
											<p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</mark><p><br></p><p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

										}elseif($user->language == 'pt'){
										
											$subject = 'Aprovado o pagamento parcial. Obrigado!';
											$msg = '<p>Prezado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Foi aprovado um montante de $'.$transaction->amount.' na sua conta.  </p><p><br></p><p>Você agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p> <br></p><p>Aqui está o resumo do estado do seu pagamento:</p><p>VALOR TOTAL A SER PAGO:'.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
											<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p><p></p>
											<p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p>
					
											<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';

										}else{
										
											$subject = 'Partial payment Approved. Thank you!';
											$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>An amount of $'.$transaction->amount.' has been approved on your account.  </p><p><br></p><p>You have now paid in full for GProCongress II.  Thank you!</p><p> <br></p><p>Here is a summary of your payment status:</p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
											<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p><p></p>
											<div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div>
					
											<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
							
										}
										
										\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
										\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Partial payment Approved. Thank you!');
		
									}
								}
								

							}else{

								$userExhibitors = \App\Models\Exhibitors::where('id',$userDataresult->id)->first();
								if($userExhibitors){

									$userExhibitors->payment_status = 'Success';
									$userExhibitors->payment_token = null;
									$userExhibitors->save();
	
									$name = $userExhibitors->name.' '.$userExhibitors->last_name;
	
									\App\Helpers\commonHelper::sendExhibitorsPaymentTriggeredToUserMail($userExhibitors->user_id,$userExhibitors->amount,$name);
									
									if(\App\Helpers\commonHelper::countExhibitorPaymentSuccess()){
										
										$results = \App\Models\Exhibitors::where('profile_status','Approved')
																		->where('payment_status','Pending')
																		->get();

										if(!empty($results)){

											foreach($results as $userData){
												
												$exhibitorsUser = \App\Models\User::where('id',$userData->user_id)->first();

												$resultsData = \App\Models\Exhibitors::where('id',$userData->id)->first();

												if($resultsData){
											
													$resultsData->profile_status = 'Declined';
													$resultsData->save();

													if($exhibitorsUser->language == 'sp'){

														$subject = 'Su solicitud para ser Exhibidor ha sido ahora rechazada';
														$msg = '<p>Estimado  '.$exhibitorsUser->name.' '.$exhibitorsUser->last_name.' ,&nbsp;</p><p><br></p><p>Lamentamos informarle que su solicitud para ser exhibidor en el GProCongress II ha sido rechazada. Como le dijimos cuando fue aceptado inicialmente, los exhibidores para el Congreso se eligen sobre la base de "primero en pagar, primero en entrar". Tenemos muy pocos lugares disponibles, y todos ellos ya estaban pagados, antes de que recibiéramos el pago de su parte.  No obstante, le agradecemos su interés y su deseo de formar parte de este evento como exhibidor.</p><p><br></p><p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p><p><br></p><p>Cordialmente,</p><p>Equipo GProCongress II</p>';
				
													}elseif($exhibitorsUser->language == 'fr'){
													
														$subject = "Votre demande d'inscription en tant qu'exposant a été refusée.";
														$msg = "<p>Cher '.$exhibitorsUser->name.' '.$exhibitorsUser->last_name.' ,&nbsp;</p><p><br></p><p>Nous avons le regret de vous informer que votre demande d'inscription en tant qu'exposant au GProCongress II a été rejetée. Comme nous vous l’avons dit lorsque votre demande a été acceptée, les exposants du Congrès sont choisis sur la base du « premier à payer, premier arrivé ».  Nous avons très peu de places disponibles, et toutes ont déjà été payées, avant que nous recevions le paiement de votre part.  Nous vous remercions néanmoins de l'intérêt que vous portez à cet événement et de votre désir d'y participer en tant qu'exposant. </p><p><br></p><p>Si vous avez des questions ou si vous souhaitez parler à l'un des membres de notre équipe, il vous suffit de répondre à cet e-mail.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongress II</p>";
				
													}elseif($exhibitorsUser->language == 'pt'){
													
														$subject = 'Sua inscrição para ser Expositor foi recusada.';
														$msg = '<p>Caro  '.$exhibitorsUser->name.' '.$exhibitorsUser->last_name.' ,&nbsp;</p><p><br></p><p>Lamentamos informar que sua inscrição para ser Expositor no GProCongresso II foi recusada. Como dissemos quando você foi inicialmente aceito, os expositores do Congresso são escolhidos com base em “quem chegou primeiro e pagou primeiro”. Temos pouquíssimas vagas disponíveis, e todas já foram pagas, antes mesmo de recebermos o pagamento de vocês. No entanto, agradecemos seu interesse e desejo de fazer parte deste evento como expositor.</p><p><br></p><p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, basta responder a este e-mail.</p><p><br></p><p>Calorosamente,</p><p>Equipe GProCongresso II</p>';
				
													}else{
													
														$subject = 'Your application to be an Exhibitor has now been declined.';
														$msg = '<p>Dear '.$exhibitorsUser->name.' '.$exhibitorsUser->last_name.' ,&nbsp;</p><p><br></p><p>We are sorry to inform you that your application to be an Exhibitor at GProCongress II has been declined.  As we told you when you were initially accepted, exhibitors for the Congress are chosen on a “first pay, first come” basis.  We have very few spaces available, and all of them  have already been paid for, before we received payment from you.  However, we are grateful for your interest in, and your desire to be a part of, this event as an exhibitor.</p><p><br></p><p>If you have any questions, or if you need to speak to one of our team members, simply reply to this email.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
										
													}
													
													\App\Helpers\commonHelper::emailSendToAdminExhibitor($subject,$msg);
													\App\Helpers\commonHelper::emailSendToUser($exhibitorsUser->email, $subject, $msg);
													\App\Helpers\commonHelper::userMailTrigger($exhibitorsUser->id,$msg,$subject);
													\App\Helpers\commonHelper::sendNotificationAndUserHistory($exhibitorsUser->id,$subject,$msg,'Your application to be an Exhibitor has now been declined.');
												}
											}
										}								
									}
								}
								
								// $resultSpouse = \App\Models\User::select('users.*','exhibitors.financial_letter','exhibitors.sponsorship_letter','exhibitors.diplomatic_passport')->join('exhibitors','users.id','=','exhibitors.user_id')->where('exhibitors.business_owner_id',$userDataresult->id)->get();
								// $files = [];

								// if(!empty($resultSpouse)){
									
								// 	foreach($resultSpouse as $user){

								// 		if($user->financial_letter){
								// 			$financial_letter = explode(',',$user->financial_letter);
								// 			$files = [
								// 				public_path('uploads/file/'.$financial_letter[0]),
								// 			];
								// 			if(isset($financial_letter[1])){
								// 				$files = [
								// 					public_path('uploads/file/'.$financial_letter[1]),
								// 				];
								// 			}
											
								// 		}
								// 		if($user->sponsorship_letter){
											
								// 			$files = [
								// 				public_path('uploads/file/'.$user->sponsorship_letter),
								// 			];
											
								// 		}

								// 	}
								// }

								// \Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files) {
								// 	$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
								// 	$message->subject($subject);
								// 	$message->to($to);
									
								// 	foreach ($files as $file){
								// 		$message->attach($file);
								// 	}
									
								// });

								// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
								// \App\Helpers\commonHelper::userMailTrigger($userDataresult->user_id,$msg,$subject);

			
							}
								

						}
		
					}
					
					break;
				case 'payment_intent.payment_failed':

					$paymentIntent = $event->data->object->metadata->order_id; 
					
					if(isset($paymentIntent)){

						$transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();
		
						if($transaction){
		
							$transaction->razorpay_order_id=$event->id;
							$transaction->description=$event->data->object->last_payment_error->message;
							$transaction->payment_status='7';
							$transaction->status='0';
							$transaction->save();

							$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
							$Wallet->status = 'Failed';
							$Wallet->save();
		
							$user = \App\Models\User::where('id',$transaction->user_id)->first();

							$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user->id, true);
							$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($user->id, true);
							$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($user->id, true);
							$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, true);

							if($transaction->particular_id == '2'){

								$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('profile_status','Approved')->first();
								if(!$userDataresult){
									\App\Helpers\commonHelper::sendMailMadeByTheSponsorIsDeclined($transaction->order_id);
									\App\Helpers\commonHelper::sendSponsorPaymentDeclinedToUserMail($transaction->user_id,$user->amount,$transaction->order_id);
	
								}
								

							}elseif($transaction->particular_id == '5'){

								$user = \App\Models\User::find($transaction->user_id);

								if($user){

									$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$user->id)->first();
									
									if($user->language == 'sp'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">ENLACE</a>';

										$subject = 'El pago correspondiente a la actualización de su habitación en el GProCongress II ha sido ';
										$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',</p>
										<p>Gracias por enviar su pago correspondiente a una mejora de habitación en el GProCongress II.</p>
										<p>Lamentablemente, no hemos podido procesar su pago.  Por favor, inténtelo de nuevo a través de este enlace: '.$website.'</p>
										<p>Si tiene alguna pregunta sobre su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
										<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de los capacitadores de pastores.</p>
										<p>Cordialmente,</p>
										<p>Equipo GProCongress II</p>';

									}elseif($user->language == 'fr'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'Le paiement de votre surclassement au GProCongress II a été refusé.';
										$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
										<p>Merci d'avoir soumis votre paiement pour un surclassement de chambre à GProCongress II.</p>
										<p>Malheureusement, nous n'avons pas pu traiter votre paiement. Veuillez réessayer votre paiement en vous rendant sur ce lien :".$website."</p>
										<p>Si vous avez des questions concernant votre paiement, ou si vous souhaitez parler à un membre de notre équipe, répondez simplement à cet e-mail.</p>
										<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
										<p>Chaleureusement,</p>
										<p>L'équipe de GProCongress II</p>";

									}elseif($user->language == 'pt'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recusado.';
										$msg = '<p>Caro '.$user->name.' '.$user->last_name.',</p>
										<p>Obrigado por enviar o seu pagamento para um upgrade de quarto no GProCongress II.</p>
										<p>Infelizmente, não foi possível processar o seu pagamento.  Por favor, tente efetuar o pagamento novamente, acedendo a este link: '.$website.'</p>
										<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
										<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
										<p>Cordialmente,</p>
										<p>Equipe do GProCongress II</p>';

									}else{
								
										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'Payment for your room upgrade at GProCongress II has been declined';
										$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
										<p>Thank you for submitting your payment for a room upgrade at GProCongress II.</p>
										<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link: '.$website.'</p>
										<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
										<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
										<p>Warmly,</p>
										<p>The GProCongress II Team</p>';

									}

									\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
									\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$subject,$subject);

									
								}
								

							}else{

								if($user->language == 'sp'){

									$subject = "Su pago ha sido rechazado";
									$msg = '<p>Estimado '.$user->name.'</p><p><br></p><p>Su pago reciente a GProCongress ha sido rechazado.</p><p>Este es un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Necesita asesoramiento mientras intenta pagar de nuevo? Responda a este correo electrónico y los miembros de nuestro equipo le ayudarán sin lugar a dudas.</p><p><br></p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($user->language == 'fr'){
								
									$subject = "Votre paiement a été refusé";
									$msg = '<p>Cher '.$user->name.',&nbsp;</p><p>Votre paiement récent pour le GProCongrès a été refusé.&nbsp;</p><p><br></p><p>Voici un résumé actuel de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : '.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous besoin d’aide pendant que vous tentez à nouveau de payer ?&nbsp; Veuillez répondre à cet e-mail et les membres de notre équipe vous aideront à coup sûr.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe GProCongrès II</p>';
								
								}elseif($user->language == 'pt'){
								
									$subject = "Seu pagamento foi declinado";
									$msg = '<p>Prezado '.$user->name.',&nbsp;</p><p><br></p><p>O seu recente pagamento para o CongressoGPro foi declinado.</p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento não for feito até 31st August 2023 , a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Precisa de ajuda enquanto você tenta terminar fazer o pagamento? Por favor responda a este e-mail e membro da nossa equipe vai lhe ajudar com certeza.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Your payment has been declined';
									$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
								
								}
								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your payment has been declined');
					
							}
							
		
						}
					}
					
					break;
				
				default:
					echo 'Received unknown event type ' . $event->type;
			}

		}catch (\Exception $e){
						
			$console=new \App\Models\PaymentConsole();

			$console->value=$e->getMessage();
	
			$console->save();
		
		}
		echo 'done';
	}

	public function croneJobSalesStatus(){ 

		$results = \App\Models\Transaction::where('payment_status','0')
											->where('method','Online')
											->where('bank_transaction_id','=',null)
											->whereDate('updated_at','>=', now()->subDays(1)->setTime(0, 0, 0)->toDateTimeString())
											->get();

		$userDataSet = '';
		
		if(!empty($results)){

			foreach($results as $value){

				if($value->razorpay_paymentid != ''){

					$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
					$event = $stripe->paymentIntents->retrieve(
						$value->razorpay_paymentid,
						[]
					);
					// echo "<pre>";
					// print_r($event); die;

					$paymentIntent = $event['metadata']['order_id'];
						
					if(isset($event) && $event['status'] == 'succeeded'){

						$transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();

						if($transaction){

							$transaction->razorpay_order_id=$event->id;
							$transaction->razorpay_paymentid=$event->data->object->charges->data[0]->payment_intent;
							$transaction->card_id=$event->data->object->charges->data[0]->payment_method_details->type;
							$transaction->bank=$event->data->object->charges->data[0]->payment_method;
							$transaction->description=$event->data->object->charges->data[0]->description;
							$transaction->bank_transaction_id=$event->data->object->charges->data[0]->balance_transaction;
							$transaction->payment_status='2';
							$transaction->status='1';
							$transaction->method='Online';
							$transaction->bank='Card';
							$transaction->save();

							$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
							$Wallet->status = 'Success';
							$Wallet->save();

							$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($transaction->user_id, true);
							$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($transaction->user_id, true);
							$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($transaction->user_id, true);
							$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id, true);


							if(\App\Helpers\commonHelper::getTotalPendingAmount($transaction->user_id) == 0) {

								$user = \App\Models\User::where('id',$transaction->user_id)->first();
								$user->stage = 3;
								$user->status_change_at = date('Y-m-d H:i:s');
								$user->save();
			
								$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
							
								if($resultSpouse){
			
									$resultSpouse->stage = 3;
									$resultSpouse->payment_status = '2';
									$resultSpouse->status_change_at = date('Y-m-d H:i:s');
									$resultSpouse->save();

									if($resultSpouse->language == 'sp'){

										$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
										$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
										$msg = "<p>Estimado ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
										<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
										<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
										<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
										<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
										<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
										<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
				
									}elseif($resultSpouse->language == 'fr'){
									
										$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
										$msg = "<p>Cher  ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
										<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
										<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
										<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
										<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
										<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
										<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
				
									}elseif($resultSpouse->language == 'pt'){
									
										$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
										$msg = "<p>Caro ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
										<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
										<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
										<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
										<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
										<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
										<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
										<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
				
									}else{
									
										$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
										$subject = 'GProCongress II registration!  Please login and submit your passport information.';
										$msg = "<p>Dear ".$resultSpouse->name.' '.$resultSpouse->last_name." ,&nbsp;</p><p><br></p>
										<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
										<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
										<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
										<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
										<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
										<p>Warmly,</p><p>GProCongress II Team</p>";
						
									}
				
									\App\Helpers\commonHelper::userMailTrigger($resultSpouse->id,$msg,$subject);
									\App\Helpers\commonHelper::emailSendToUser($resultSpouse->email, $subject, $msg);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($resultSpouse->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');

								}
			
								if($transaction->particular_id == '2'){

									$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('order_id',$transaction->order_id)->where('profile_status','Approved')->where('payment_status','Pending')->first();
									if(!$userDataresult){
										\App\Helpers\commonHelper::sendMailMadeByTheSponsorIsApproved($transaction->order_id);
										\App\Helpers\commonHelper::sendSponsorPaymentApprovedToUserMail($transaction->user_id,$transaction->amount,'full',$transaction->order_id);
									}

								}elseif($transaction->particular_id == '5'){

									$user = \App\Models\User::find($transaction->user_id);
	
									if($user){
	
										$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$user->id)->first();
										
										if($user->language == 'sp'){
	
											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">ENLACE</a>';
	
											$subject = 'El pago correspondiente a la actualización de su habitación en el GProCongress II ha sido ';
											$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',</p>
											<p>Gracias por enviar su pago correspondiente a una mejora de habitación en el GProCongress II.</p>
											<p>Lamentablemente, no hemos podido procesar su pago.  Por favor, inténtelo de nuevo a través de este enlace: '.$website.'</p>
											<p>Si tiene alguna pregunta sobre su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
											<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de los capacitadores de pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipo GProCongress II</p>';
	
										}elseif($user->language == 'fr'){
	
											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';
	
											$subject = 'Le paiement de votre surclassement au GProCongress II a été refusé.';
											$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
											<p>Merci d'avoir soumis votre paiement pour un surclassement de chambre à GProCongress II.</p>
											<p>Malheureusement, nous n'avons pas pu traiter votre paiement. Veuillez réessayer votre paiement en vous rendant sur ce lien :".$website."</p>
											<p>Si vous avez des questions concernant votre paiement, ou si vous souhaitez parler à un membre de notre équipe, répondez simplement à cet e-mail.</p>
											<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
											<p>Chaleureusement,</p>
											<p>L'équipe de GProCongress II</p>";
	
										}elseif($user->language == 'pt'){
	
											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';
	
											$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recusado.';
											$msg = '<p>Caro '.$user->name.' '.$user->last_name.',</p>
											<p>Obrigado por enviar o seu pagamento para um upgrade de quarto no GProCongress II.</p>
											<p>Infelizmente, não foi possível processar o seu pagamento.  Por favor, tente efetuar o pagamento novamente, acedendo a este link: '.$website.'</p>
											<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
											<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipe do GProCongress II</p>';
	
										}else{
									
											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';
	
											$subject = 'Payment for your room upgrade at GProCongress II has been declined';
											$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
											<p>Thank you for submitting your payment for a room upgrade at GProCongress II.</p>
											<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link: '.$website.'</p>
											<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
											<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
											<p>Warmly,</p>
											<p>The GProCongress II Team</p>';
	
										}
	
										\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
										\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$subject,$subject);
	
									}
									
	
								}else{

									if($user->language == 'sp'){
				
										$subject = 'Pago recibido. ¡Gracias!';
										$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Se ha aprobado una cantidad de $'.$user->amount.' en su cuenta.  </p><p><br></p><p>Gracias por hacer este pago.</p><p> <br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p>IMPORTE TOTAL A PAGAR:'.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p><p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p><p>Atentamente,</p><p>El equipo del GProCongress II</p>';
				
									}elseif($user->language == 'fr'){
									
										$subject = 'Paiement intégral reçu.  Merci !';
										$msg = '<p>Cher '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Un montant de '.$user->amount.'$ a été approuvé sur votre compte.  </p><p><br></p><p>Vous avez maintenant payé la somme totale pour le GProCongrès II.  Merci !</p><p> <br></p><p>Voici un résumé de l’état de votre paiement :</p><p>MONTANT TOTAL À PAYER:'.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p><p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';
				
									}elseif($user->language == 'pt'){
									
										$subject = 'Pagamento recebido na totalidade. Obrigado!';
										$msg = '<p>Prezado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Foi aprovado um montante de $'.$user->amount.' na sua conta.  </p><p><br></p><p>Você agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p> <br></p><p>Aqui está o resumo do estado do seu pagamento:</p><p>VALOR TOTAL A SER PAGO:'.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p><p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';
				
									}else{
									
										$subject = 'Payment received in full. Thank you!';
										$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>An amount of $'.$user->amount.' has been approved on your account. </p><p><br></p><p>You have now paid in full for GProCongress II.  Thank you!</p><p> <br></p><p>Here is a summary of your payment status:</p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
						
									}
									
									\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
				
									\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Payment received in full. Thank you!');

									if($user->language == 'sp'){

										$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
										$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
										$msg = "<p>Estimado ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
										<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
										<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
										<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
										<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
										<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
										<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
				
									}elseif($user->language == 'fr'){
									
										$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
										$msg = "<p>Cher  ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
										<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
										<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
										<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
										<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
										<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
										<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
				
									}elseif($user->language == 'pt'){
									
										$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
										$msg = "<p>Caro ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
										<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
										<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
										<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
										<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
										<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
										<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
										<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
				
									}else{
									
										$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
										$subject = 'GProCongress II registration!  Please login and submit your passport information.';
										$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
										<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
										<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
										<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
										<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
										<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
										<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
										<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
										<p>Warmly,</p><p>GProCongress II Team</p>";
						
									}
				
									\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
									\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');

			
								}

							}else {

								$user = \App\Models\User::find($transaction->user_id);
								
								if($transaction->particular_id == '2'){

									$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('profile_status','Approved')->first();
									if(!$userDataresult){
										\App\Helpers\commonHelper::sendMailMadeByTheSponsorIsApproved($transaction->order_id);
										\App\Helpers\commonHelper::sendSponsorPaymentApprovedToUserMail($transaction->user_id,$transaction->amount,'partial',$transaction->order_id);
									}

								}elseif($transaction->particular_id == '5'){

									$user = \App\Models\User::find($transaction->user_id);

									if($user){

										$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$user->id)->first();
										
										if($user->language == 'sp'){

											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">ENLACE</a>';

											$subject = 'El pago correspondiente a la actualización de su habitación en el GProCongress II ha sido ';
											$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',</p>
											<p>Gracias por enviar su pago correspondiente a una mejora de habitación en el GProCongress II.</p>
											<p>Lamentablemente, no hemos podido procesar su pago.  Por favor, inténtelo de nuevo a través de este enlace: '.$website.'</p>
											<p>Si tiene alguna pregunta sobre su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
											<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de los capacitadores de pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipo GProCongress II</p>';

										}elseif($user->language == 'fr'){

											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

											$subject = 'Le paiement de votre surclassement au GProCongress II a été refusé.';
											$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
											<p>Merci d'avoir soumis votre paiement pour un surclassement de chambre à GProCongress II.</p>
											<p>Malheureusement, nous n'avons pas pu traiter votre paiement. Veuillez réessayer votre paiement en vous rendant sur ce lien :".$website."</p>
											<p>Si vous avez des questions concernant votre paiement, ou si vous souhaitez parler à un membre de notre équipe, répondez simplement à cet e-mail.</p>
											<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
											<p>Chaleureusement,</p>
											<p>L'équipe de GProCongress II</p>";

										}elseif($user->language == 'pt'){

											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

											$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recusado.';
											$msg = '<p>Caro '.$user->name.' '.$user->last_name.',</p>
											<p>Obrigado por enviar o seu pagamento para um upgrade de quarto no GProCongress II.</p>
											<p>Infelizmente, não foi possível processar o seu pagamento.  Por favor, tente efetuar o pagamento novamente, acedendo a este link: '.$website.'</p>
											<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
											<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
											<p>Cordialmente,</p>
											<p>Equipe do GProCongress II</p>';

										}else{
									
											$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

											$subject = 'Payment for your room upgrade at GProCongress II has been declined';
											$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
											<p>Thank you for submitting your payment for a room upgrade at GProCongress II.</p>
											<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link: '.$website.'</p>
											<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
											<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
											<p>Warmly,</p>
											<p>The GProCongress II Team</p>';

										}

										\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
										\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
										\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$subject,$subject);

										
									}
								

								}else{
									
									if($user->language == 'sp'){

										$subject = 'Pago parcial Aprobado. ¡Gracias!';
										$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Se ha aprobado una cantidad de  $'.$transaction->amount.' en su cuenta.  </p><p><br></p><p>Gracias por hacer este pago.</p><p> <br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p>IMPORTE TOTAL A PAGAR:'.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
										<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p><p></p>
    									<p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p><br></p>
				
										<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p><p>Atentamente,</p><p>El equipo del GProCongress II</p>';

									}elseif($user->language == 'fr'){
									
										$subject = 'Paiement partiel Approuvéu.  Merci !';
										$msg = '<p>Cher '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Un montant de  '.$transaction->amount.'$ a été approuvé sur votre compte.  </p><p><br></p><p>Vous avez maintenant payé la somme totale pour le GProCongrès II.  Merci !</p><p> <br></p><p>Voici un résumé de l’état de votre paiement :</p><p>MONTANT TOTAL À PAYER:'.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
										<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p><p></p>
    									<p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</mark><p><br></p><p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';

									}elseif($user->language == 'pt'){
									
										$subject = 'Aprovado o pagamento parcial. Obrigado!';
										$msg = '<p>Prezado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>Foi aprovado um montante de $'.$transaction->amount.' na sua conta.  </p><p><br></p><p>Você agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p> <br></p><p>Aqui está o resumo do estado do seu pagamento:</p><p>VALOR TOTAL A SER PAGO:'.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
										<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p><p></p>
										<p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p>
				
										<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';

									}else{
									
										$subject = 'Partial payment Approved. Thank you!';
										$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p><p>An amount of $'.$transaction->amount.' has been approved on your account.  </p><p><br></p><p>You have now paid in full for GProCongress II.  Thank you!</p><p> <br></p><p>Here is a summary of your payment status:</p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
										<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p><p></p>
    									<div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div>
				
										<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
						
									}
									
									\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
									\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Partial payment Approved. Thank you!');
	
								}
							}

							$userDataSet.= 'Email : '.$user->email.'Status : Success <br>';

						}

					}else{

						$transaction=\App\Models\Transaction::where('order_id',$paymentIntent)->first();

						if($transaction){

							$transaction->razorpay_order_id=$event->id;
							$transaction->description=$event->last_payment_error->message;
							$transaction->payment_status='7';
							$transaction->status='0';
							$transaction->save();

							$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
							$Wallet->status = 'Failed';
							$Wallet->save();

							$user = \App\Models\User::where('id',$transaction->user_id)->first();

							$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user->id, true);
							$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($user->id, true);
							$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($user->id, true);
							$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, true);

							if($transaction->particular_id == '2'){

								$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('profile_status','Approved')->first();
								if(!$userDataresult){
									\App\Helpers\commonHelper::sendMailMadeByTheSponsorIsDeclined($transaction->order_id);
									\App\Helpers\commonHelper::sendSponsorPaymentDeclinedToUserMail($transaction->user_id,$user->amount,$transaction->order_id);
		
								}


							}elseif($transaction->particular_id == '5'){

								$user = \App\Models\User::find($transaction->user_id);

								if($user){

									$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$user->id)->first();
									
									if($user->language == 'sp'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">ENLACE</a>';

										$subject = 'El pago correspondiente a la actualización de su habitación en el GProCongress II ha sido ';
										$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',</p>
										<p>Gracias por enviar su pago correspondiente a una mejora de habitación en el GProCongress II.</p>
										<p>Lamentablemente, no hemos podido procesar su pago.  Por favor, inténtelo de nuevo a través de este enlace: '.$website.'</p>
										<p>Si tiene alguna pregunta sobre su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
										<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de los capacitadores de pastores.</p>
										<p>Cordialmente,</p>
										<p>Equipo GProCongress II</p>';

									}elseif($user->language == 'fr'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'Le paiement de votre surclassement au GProCongress II a été refusé.';
										$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
										<p>Merci d'avoir soumis votre paiement pour un surclassement de chambre à GProCongress II.</p>
										<p>Malheureusement, nous n'avons pas pu traiter votre paiement. Veuillez réessayer votre paiement en vous rendant sur ce lien :".$website."</p>
										<p>Si vous avez des questions concernant votre paiement, ou si vous souhaitez parler à un membre de notre équipe, répondez simplement à cet e-mail.</p>
										<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
										<p>Chaleureusement,</p>
										<p>L'équipe de GProCongress II</p>";

									}elseif($user->language == 'pt'){

										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recusado.';
										$msg = '<p>Caro '.$user->name.' '.$user->last_name.',</p>
										<p>Obrigado por enviar o seu pagamento para um upgrade de quarto no GProCongress II.</p>
										<p>Infelizmente, não foi possível processar o seu pagamento.  Por favor, tente efetuar o pagamento novamente, acedendo a este link: '.$website.'</p>
										<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
										<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
										<p>Cordialmente,</p>
										<p>Equipe do GProCongress II</p>';

									}else{
								
										$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

										$subject = 'Payment for your room upgrade at GProCongress II has been declined';
										$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
										<p>Thank you for submitting your payment for a room upgrade at GProCongress II.</p>
										<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link: '.$website.'</p>
										<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
										<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
										<p>Warmly,</p>
										<p>The GProCongress II Team</p>';

									}

									\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
									\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
									\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$subject,$subject);

									
								}
								

							}else{

								if($user->language == 'sp'){

									$subject = "Su pago ha sido rechazado";
									$msg = '<p>Estimado '.$user->name.'</p><p><br></p><p>Su pago reciente a GProCongress ha sido rechazado.</p><p>Este es un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Necesita asesoramiento mientras intenta pagar de nuevo? Responda a este correo electrónico y los miembros de nuestro equipo le ayudarán sin lugar a dudas.</p><p><br></p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($user->language == 'fr'){
								
									$subject = "Votre paiement a été refusé";
									$msg = '<p>Cher '.$user->name.',&nbsp;</p><p>Votre paiement récent pour le GProCongrès a été refusé.&nbsp;</p><p><br></p><p>Voici un résumé actuel de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : '.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous besoin d’aide pendant que vous tentez à nouveau de payer ?&nbsp; Veuillez répondre à cet e-mail et les membres de notre équipe vous aideront à coup sûr.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe GProCongrès II</p>';
								
								}elseif($user->language == 'pt'){
								
									$subject = "Seu pagamento foi declinado";
									$msg = '<p>Prezado '.$user->name.',&nbsp;</p><p><br></p><p>O seu recente pagamento para o CongressoGPro foi declinado.</p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento não for feito até 31st August 2023 , a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Precisa de ajuda enquanto você tenta terminar fazer o pagamento? Por favor responda a este e-mail e membro da nossa equipe vai lhe ajudar com certeza.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Your payment has been declined';
									$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
								
								}

								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your payment has been declined');
							}

							$userDataSet.= 'Email : '.$user->email.'Status : Failed <br>';

						}
					}
					
				}else{

					if($value->order_id){

						$transaction=\App\Models\Transaction::where('id',$value->id)->first();
						$transaction->razorpay_order_id=$value->order_id;
						$transaction->description='Payment failed';
						$transaction->payment_status='7';
						$transaction->status='0';
						$transaction->save();

						$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
						$Wallet->status = 'Failed';
						$Wallet->save();

						$user = \App\Models\User::where('id',$transaction->user_id)->first();

						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user->id, true);
						$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($user->id, true);
						$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($user->id, true);
						$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, true);

						if($transaction->particular_id == '2'){

							$userDataresult = \App\Models\Exhibitors::where('user_id',$transaction->user_id)->where('profile_status','Approved')->first();
							if(!$userDataresult){
								\App\Helpers\commonHelper::sendMailMadeByTheSponsorIsDeclined($transaction->order_id);
								\App\Helpers\commonHelper::sendSponsorPaymentDeclinedToUserMail($transaction->user_id,$user->amount,$transaction->order_id);
		
							}

						}elseif($transaction->particular_id == '5'){

							$user = \App\Models\User::find($transaction->user_id);

							if($user){

								$roomupgrade = \App\Models\RoomUpgrade::where('user_id',$user->id)->first();
								
								if($user->language == 'sp'){

									$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">ENLACE</a>';

									$subject = 'El pago correspondiente a la actualización de su habitación en el GProCongress II ha sido ';
									$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',</p>
									<p>Gracias por enviar su pago correspondiente a una mejora de habitación en el GProCongress II.</p>
									<p>Lamentablemente, no hemos podido procesar su pago.  Por favor, inténtelo de nuevo a través de este enlace: '.$website.'</p>
									<p>Si tiene alguna pregunta sobre su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
									<p>Únase a nosotros en oración para multiplicar la cantidad y calidad de los capacitadores de pastores.</p>
									<p>Cordialmente,</p>
									<p>Equipo GProCongress II</p>';

								}elseif($user->language == 'fr'){

									$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

									$subject = 'Le paiement de votre surclassement au GProCongress II a été refusé.';
									$msg = "<p>Cher ".$user->name." ".$user->last_name.",</p>
									<p>Merci d'avoir soumis votre paiement pour un surclassement de chambre à GProCongress II.</p>
									<p>Malheureusement, nous n'avons pas pu traiter votre paiement. Veuillez réessayer votre paiement en vous rendant sur ce lien :".$website."</p>
									<p>Si vous avez des questions concernant votre paiement, ou si vous souhaitez parler à un membre de notre équipe, répondez simplement à cet e-mail.</p>
									<p>Priez avec nous pour multiplier la quantité et la qualité des formateurs de pasteurs.</p>
									<p>Chaleureusement,</p>
									<p>L'équipe de GProCongress II</p>";

								}elseif($user->language == 'pt'){

									$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

									$subject = 'O pagamento do upgrade do seu quarto no GProCongress II foi recusado.';
									$msg = '<p>Caro '.$user->name.' '.$user->last_name.',</p>
									<p>Obrigado por enviar o seu pagamento para um upgrade de quarto no GProCongress II.</p>
									<p>Infelizmente, não foi possível processar o seu pagamento.  Por favor, tente efetuar o pagamento novamente, acedendo a este link: '.$website.'</p>
									<p>Se tiver alguma dúvida sobre o seu pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
									<p>Ore connosco para multiplicar a quantidade e a qualidade dos Treinadores de Pastores.</p>
									<p>Cordialmente,</p>
									<p>Equipe do GProCongress II</p>';

								}else{
							
									$website = '<a href="'.url('room-change-payment/'.$roomupgrade->token).'">LINK</a>';

									$subject = 'Payment for your room upgrade at GProCongress II has been declined';
									$msg = '<p>Dear '.$user->name.' '.$user->last_name.',</p>
									<p>Thank you for submitting your payment for a room upgrade at GProCongress II.</p>
									<p>Unfortunately, we could not process your payment.  Please try your payment again, by going to this link: '.$website.'</p>
									<p>If you have any questions about your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
									<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
									<p>Warmly,</p>
									<p>The GProCongress II Team</p>';

								}

								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$subject,$subject);

								
							}
							

						}else{

							if($user->language == 'sp'){

								$subject = "Su pago ha sido rechazado";
								$msg = '<p>Estimado '.$user->name.'</p><p><br></p><p>Su pago reciente a GProCongress ha sido rechazado.</p><p>Este es un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$user->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Necesita asesoramiento mientras intenta pagar de nuevo? Responda a este correo electrónico y los miembros de nuestro equipo le ayudarán sin lugar a dudas.</p><p><br></p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
							
							}elseif($user->language == 'fr'){
							
								$subject = "Votre paiement a été refusé";
								$msg = '<p>Cher '.$user->name.',&nbsp;</p><p>Votre paiement récent pour le GProCongrès a été refusé.&nbsp;</p><p><br></p><p>Voici un résumé actuel de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : '.$user->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous besoin d’aide pendant que vous tentez à nouveau de payer ?&nbsp; Veuillez répondre à cet e-mail et les membres de notre équipe vous aideront à coup sûr.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe GProCongrès II</p>';
							
							}elseif($user->language == 'pt'){
							
								$subject = "Seu pagamento foi declinado";
								$msg = '<p>Prezado '.$user->name.',&nbsp;</p><p><br></p><p>O seu recente pagamento para o CongressoGPro foi declinado.</p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$user->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento não for feito até 31st August 2023 , a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Precisa de ajuda enquanto você tenta terminar fazer o pagamento? Por favor responda a este e-mail e membro da nossa equipe vai lhe ajudar com certeza.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'Your payment has been declined';
								$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
							
							}

							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
							\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your payment has been declined');
		
							
						}

						// $userDataSet.= 'Email : '.$user->email.'Status : Failed <br>';
						$userDataSet .= 'Email: ' . $user->email . '<br>Status: Failed <br>';


					}
				}
				
				
			}
		}
		
		$subject = 'Cron Job Ran - '.date('d-m-Y H:i:s');
		$msg = $userDataSet;
		$result = array();
		$to = 'vineet@d2rtech.com';
		if($userDataSet) {
				
			\Mail::send('email_templates.mail', compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
				$message->from(env('MAIL_FROM_ADDRESS'), 'GProCongress II Team');
				$message->subject($subject);
				$message->to($to);
			});


		} else {
			$subject = 'Cron Job Ran - '.date('d-m-Y H:i:s').' - No Records';
			$msg='No records Updated';
			\Mail::send('email_templates.mail', compact('to', 'subject', 'msg', 'result'), function($message) use ($to, $subject) {
				$message->from(env('MAIL_FROM_ADDRESS'), 'GProCongress II Team');
				$message->subject($subject);
				$message->to($to);
			});
		}


		echo 'done';
	}

	public function sendTravelInfoReminder(Request $request) {
		
		$results = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_status',null)->get();

		if (!empty($results)) {

			foreach($results as $result){

				$to = $result->email;
				$subject = 'Verify Travel Info';
				$msg = 'Please verify your travel information';
				$travel_info = $result;
				
				$pdf = \PDF::loadView('email_templates.travel_info', $travel_info->toArray());
	
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id,$subject,$msg,'Travel info reminder');
					
				// \App\Helpers\commonHelper::sendSMS($result->mobile);
			}
			

			return response(array('message'=>'Travel info reminder has been sent successfully.'), 200);

		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}
	
	public function TravelInfoUpdateReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['stage', '=', '3'],['designation_id', '!=', '14']])
									->whereDate('updated_at', '=', now()->subDays(2)->setTime(0, 0, 0)->toDateTimeString())
									->orWhereDate('updated_at', '=', now()->subDays(10)->setTime(0, 0, 0)->toDateTimeString())
									->get();

			if(count($results) > 0){
				foreach ($results as $key => $result) {
				
					$updated_at = \Carbon\Carbon::parse(date('Y-m-d', strtotime($result->updated_at)));
					$today =  \Carbon\Carbon::parse(date('Y-m-d'));
				
					$days = $today->diffInDays($updated_at);

					
					$to = $result->email;
					
					if($result->language == 'sp'){

						$subject = "Su información de viaje está incompleta.";
						$msg = '<p style=""><font color="#999999"><span style="font-size: 14px;">Estimado '.$result->name.' '.$result->last_name.'</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">¡Nos alegramos de poder verle en el GProCongress en Ciudad de Panamá, Panamá!</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">Para asistir a los delgados con la obtención de visas, necesitamos que nos envíen su información de viaje.&nbsp;</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">Gracias por enviar la suya.&nbsp;</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">Lamentablemente, la información que hemos recibido sobre el viaje está incompleta.&nbsp;&nbsp;</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">Por favor, responda a este correo electrónico con la información completa de su viaje para que podamos ayudarle.&nbsp; Una vez recibida, le enviaremos un correo electrónico para confirmar que la información que hemos recibido es correcta.</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">Atentamente,&nbsp;</span></font></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><span style="font-size: 14px;"><br></span></p><p style=""><font color="#999999"><span style="font-size: 14px;">El Equipo GProCOngress II</span></font></p><div><br></div>';
					
					}elseif($result->language == 'fr'){
					
						$subject = "Les informations sur votre voyage sont incomplètes.";
						$msg = '<div><font color="#000000"><span style="font-size: 14px;">Cher '.$result->name.' '.$result->last_name.',&nbsp;</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Nous sommes ravis de vous voir au GProCongrès à Panama City, au Panama !</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Pour aider les délégués à obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Merci d’avoir soumis le vôtre.&nbsp;</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Malheureusement, les informations de voyage que nous avons reçues sont incomplètes.&nbsp;&nbsp;</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Veuillez répondre à cet e-mail avec vos informations de voyage complètes, afin que nous puissions vous aider.&nbsp; Dès réception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons reçues sont correctes.&nbsp;</span></font></div><div><span style="font-size: 14px;"><br></span></div><div><font color="#000000"><span style="font-size: 14px;">Cordialement,&nbsp;</span></font></div><div><font color="#000000"><span style="font-size: 14px;">L’équipe du GProCongrès II</span></font></div><div><br></div>';
			
					}elseif($result->language == 'pt'){
					
						$subject = "Sua informação de viagem está incompleta";
						$msg = '<div><div><span style="font-size: 14px;">Prezado '.$result->name.' '.$result->last_name.',&nbsp;</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Nós estamos esperançosos em lhe ver no CongressoGPro na Cidade de Panamá, Panamá!</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Para ajudarmos os delegados a obter vistos, estamos solicitando que submetam sua informação de viagem a nós.</span></div><div><span style="font-size: 14px;">Agradecemos por ter submetido a sua informação</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Infelizmente, a informação de viagem que nós recebemos está incompleta.</span></div><div><font color="#24695c"><span style="font-size: 14px;"><br></span></font></div><div><span style="font-size: 14px;">Por favor responda este e-mail com sua informação de viagem completa, para que possamos lhe ajudar. Após o recebimento, nós lhe enviaremos um e-mail para confirmar que a informação que recebemos é correta.&nbsp;</span></div><div><span style="font-size: 14px;">Calorosamente,</span></div><div><span style="font-size: 14px;">Equipe do II CongressoGPro&nbsp;&nbsp;</span></div></div><div><br></div>';
					
					}else{
					
						$subject = 'Your travel information is incomplete';
						$msg = '<p>Dear '.$result->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Thank you for submitting yours.&nbsp;</p><p><br></p><p>Regretfully, the travel information we have received is incomplete.&nbsp;&nbsp;</p><p><br></p><p>Please reply to this email with your complete travel information, so we can assist you.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
										
					}

					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
				
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					$subject='User travel information reminder';
					$msg='User travel information reminder';
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id,$subject,$msg,'User travel information reminder');
					
				}
				
				return response(array('message'=>count($results).'Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function sendSessionInfoReminder(Request $request) {
		

		$results = \App\Models\SessionInfo::join('users','users.id','=','session_infos.user_id')->where('session_infos.admin_status',null)->get();

		if (!empty($results)) {

			foreach($results as $result){

				$UserHistory=new \App\Models\UserHistory();
				$UserHistory->user_id=$result->id;
				$UserHistory->action='Verify Session Info Reminder';
				$UserHistory->save();

				$to = $result->email;
				$subject = 'Verify Session Info';
				$msg = 'Please verify your session information';
	
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			}
			

			return response(array('message'=>'Session info reminder has been sent successfully.'), 200);

		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

		

	}

	public function TestimonialList(Request $request){
 
		try{

			
			if($request->json()->get('lang') == 'en'){
            
				$Result = \App\Models\Testimonial::where('status', '1')->orderBy('id','desc')->get();
			
			}else{
	
				$Result = \App\Models\Testimonial::where(function ($query) {
													$query->where('id',3)
														->orWhere('id',4)
														->orWhere('id',5);
												})->get();
			
			}
			
			if(!$Result){
				
				return response(array("message" => 'Result not found.','error'=>true),404); 
			}else{
				
				$result=[];
				
				foreach($Result as $val){
					
					if($request->json()->get('lang') == 'fr'){

						$result[]=[
							'title'=>$val->fr_title,
							'designation'=>$val->fr_designation,
							'description'=>$val->fr_description
						];

					}elseif($request->json()->get('lang') == 'pt'){

						$result[]=[
							'title'=>$val->pt_title,
							'designation'=>$val->pt_designation,
							'description'=>$val->pt_description
						];

					}elseif($request->json()->get('lang') == 'sp'){

						$result[]=[
							'title'=>$val->sp_title,
							'designation'=>$val->sp_designation,
							'description'=>$val->sp_description
						];
						
					}else{

						$result[]=[
							'title'=>$val->title,
							'designation'=>$val->designation,
							'description'=>$val->description
						];
					}
				}
				return response(array("message" => 'Testimonial fetched successfully.','result'=>$result,'error'=>false),200); 
				
			}
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}
	
	public function HomeContent(Request $request){
 
		try{

			$result=[];
			
			
			if($request->json()->get('lang') == 'fr'){

				$result=[
					// 'Video1'=>asset('images/Gpromobile French-1.m4vv'),//Vineet-13012023
					'Video1'=>asset('images/Gpromobile-French-1.m4v'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];

			}elseif($request->json()->get('lang') == 'pt'){

				$result=[
					// 'Video1'=>asset('images/Gpromobile Protugese-1.m4v'), //Vineet-13012023
					'Video1'=>asset('images/Gpromobile-Protugese-1.m4v'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];

			}elseif($request->json()->get('lang') == 'sp'){

				$result=[
					// 'Video1'=>asset('images/Gpromobile Spain-1.m4v'),//Vineet-13012023
					'Video1'=>asset('images/Gpromobile-Spain-1.m4v'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];
				
			}else{

				$result=[
					'Video1'=>asset('assets/images/Gpromobile-1.mp4'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];
			}
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}
	
	
	public function GetSessions(Request $request){
 
		try{

			$result=[];
			
			$sessions = \App\Models\DaySession::where('status','1')->get();

			if(!empty($sessions) && count($sessions)>0){

				foreach($sessions as $session){

					$result[]=[
						'id'=>$session->id,
						'name'=>$session->name,
						'session_name'=>$session->session_name,
						'date'=>$session->date,
						'start_time'=>$session->start_time,
						'end_time'=>$session->end_time,
					];
				}
				
			}
			
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}
	
	public function GetFAQs(Request $request){
 
		try{

			$result=[];
			
			$sessions = \App\Models\Faq::where('status','1')->orderBy('id','desc')->get();

			if(!empty($sessions) && count($sessions)>0){

				foreach($sessions as $session){

					
					$result[]=[

						'category'=>$session->category,
						'question'=>$session->question,
						'answer'=>$session->answer,
					];
				}
				
			}
			
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}

	public function getCountryList(Request $request){
 
		try{

			$result=[];
			
			$countries = \App\Models\Country::orderBy('name','Asc')->get();

			if(!empty($countries) && count($countries)>0){

				foreach($countries as $country){

					
					$result[]=[

						'id'=>$country->id,
						'name'=>$country->name,
					];
				}
				
			}
			
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}
	
	public function GetInformation(Request $request,$id){
 
		try{

			$result=[];
			
			$session = \App\Models\Information::where('status','1')->where('id',$id)->first();

			if($session){

				if($request->json()->get('lang') == 'fr'){

					$result=[

						'title'=>$session->fr_title,
						'description'=>$session->fr_description,
					];

				}elseif($request->json()->get('lang') == 'pt'){

					$result=[

						'title'=>$session->pt_title,
						'description'=>$session->pt_description,
					];

				}elseif($request->json()->get('lang') == 'sp'){

					$result=[

						'title'=>$session->sp_title,
						'description'=>$session->sp_description,
					];
					
				}else{

					$result=[

						'title'=>$session->title,
						'description'=>$session->description,
					];
				}
					
				
			}
			
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}

	public function EarlyBird1Jun2023(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['designation_id', '!=', '14'],['stage', '=', '2'], ['amount', '>', 0], ['early_bird', '=', 'Yes'], ['email', '=', 'gopalsaini.img@gmail.com']])->get();
			
			if(count($results) > 0){

				foreach ($results as $result) {
				
					if(\App\Helpers\commonHelper::getTotalPendingAmount($result->id) > 0) {

						$user = \App\Models\User::where('id', $result->id)->first();
						
						if($user){

							$Spouse = \App\Models\User::where('parent_id', $user->id)->where('added_as', 'Spouse')->first();
							
							if($user->marital_status == 'Unmarried'){

								$trainer = 'No';

							}else if($user->marital_status == 'Married' && !$Spouse){

								$trainer = 'No';

							}else if($user->marital_status == 'Married' && $Spouse){

								if($user->parent_spouse_stage >= 2){

									$trainer = 'No';

								}else{

									
									$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWSpouse($user->doyouseek_postoral,$Spouse->doyouseek_postoral,$user->ministry_pastor_trainer,$Spouse->ministry_pastor_trainer,$user->amount);

									$trainer = $data ['trainer'];
								}
								
							}
							
							if($trainer == 'Yes'){
								$amount = $result->amount+200;
							}else{
								$amount = $result->amount+100;
							}

							$user->amount = $amount;
							$user->early_bird = 'No';
							$user->save();

							$name = $user->salutation.' '.$user->name.' '.$user->last_name;
					

							if($user->language == 'sp'){
							
								$url = '<a href="'.url('payment').'" target="_blank">enlace</a>';
								$subject = 'RECORDATORIO: El pago para asistir al GProCongress II ha vencido.';
								$msg = '<p>Estimado '.$name.',</p>
								<p></p>
								<p>El pago para asistir al GProCongress II ha vencido.  Por favor, vaya a '.$url.', y realice su pago ahora.</p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b><i>POR FAVOR, TENGA EN CUENTA: Debido a que no recibió su pago el 31 de mayo de 2023 o antes, ha perdido su descuento de "inscripción anticipada" y ahora tendrá que pagar $100 más para poder asistir al Congreso.</i></b></p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b>Su nuevo monto de pago es $'.$user->amount.'. ATENCIÓN: Este importe debe pagarse antes del 31 de agosto de 2023, o aumentará aún más su costo.</b></p>
								<p></p>
								<p>Si tiene alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
								<p></p>
								<p><i>Ore con nosotros para multiplicar la cantidad y calidad de capacitadores de pastores. </i></p>
								<p><br></p>
								<p>Cordialmente,</p>
								<p>&nbsp;Equipo GProCongress II</p><div><br></div>';
		
		
							}elseif($user->language == 'fr'){
							
								$url = '<a href="'.url('payment').'" target="_blank">lien</a>';
								$subject = 'RAPPEL - Votre paiement GProCongress II est maintenant dû.';
								$msg = '<p>Cher '.$name.',</p>
								<p></p>
								<p>Le paiement de votre participation au GProCongress II est maintenant dû.  Prière d’aller sur '.$url.', et effectuer votre paiement maintenant.</p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b><i>VEUILLEZ NOTER : Parce que le paiement n’a pas été reçu de votre part avant le 31 mai 2023, vous avez perdu votre rabais « inscription anticipée » et vous devrez payer 100 $ de plus pour assister au Congrès.</i></b></p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b>Le montant de votre nouveau paiement est de $'.$user->amount.'.   VEUILLEZ NOTER: Ce montant doit être payé avant le 31 août 2023, sinon votre coût augmentera encore plus.</b></p>
								<p></p>
								<p>Si vous avez des questions sur votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</p>
								<p></p>
								<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs. </i></p>
								<p><br></p>
								<p>Cordialement,</p>
								<p>&nbsp;L’équipe GProCongress II</p><div><br></div>';
		
							}elseif($user->language == 'pt'){
							
								$url = '<a href="'.url('payment').'" target="_blank">link</a>';
								$subject = 'LEMBRETE - O pagamento do GProCongress II está ainda em aberto.';
								$msg = '<p>Caro  '.$name.',</p>
								<p></p>
								<p>O pagamento da sua participação no GProCongress II está em aberto.  Por favor, visite o'.$url.', e faça o seu pagamento agora.</p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b><i>Uma vez que o pagamento não for recebido até 31 de Maio de 2023, perderá o  desconto de "early bird" e tem agora de pagar mais $100 para participar no Congresso.</i></b></p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b>O seu novo montante de pagamento é de $'.$user->amount.'.  ATENÇÃO: Esse valor deve ser pago até 31 de agosto de 2023, ou seu custo aumentará ainda mais.</b></p>
								<p></p>
								<p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipa, basta responder a este e-mail.</p>
								<p></p>
								<p><i>Ore conosco para multiplicar a quantidade e a qualidade dos pastores-formadores. </i></p>
								<p><br></p>
								<p>Cordialmente,</p>
								<p>&nbsp;Equipe do GProCongress II.</p><div><br></div>';
		
							}else{
							
								$url = '<a href="'.url('payment').'" target="_blank">link</a>';
		
								$subject = 'REMINDER – Your GProCongress II payment is now due.';
								$msg = '<p>Dear '.$name.',</p>
								<p></p>
								<p>Payment for your attendance at GProCongress II is now due.  Please go to '.$url.', and make your payment now.</p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b><i>Because payment was not received from you by May 31, 2023, you have lost your “early bird” discount, and you must now pay an additional $100 to attend the Congress.</i></b></p>
								<p></p>
								<p style="background-color:yellow; display: inline;"><b>Your new payment amount is $'.$user->amount.'.  PLEASE NOTE: This amount must be paid by August 31, 2023, or your cost will go up even more.</b></p>
								<p></p>
								<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email. </p>
								<p></p>
								<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p>
								<p><br></p>
								<p>Warmly,</p>
								<p>&nbsp;The GProCongress II Team</p><div><br></div>';
							
							}
		
							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		
							\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
		
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'REMINDER – Your “<b>EARLY BIRD</b>” discount is expiring on <b>MAY 31, 2023!</b>');
						
							
						}
					}
				}
				
				
			}

			return response(array("message"=>'Data set.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function sendEarlyBirdReminderMail(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['designation_id', '!=', '14'],['stage', '>', '1'], ['amount', '>', 0], ['early_bird', '=', 'Yes']])->get();
			
			if(count($results) > 0){

				foreach ($results as $key => $user) {
				
					$name = '';

					$name = $user->salutation.' '.$user->name.' '.$user->last_name;

					if($user->language == 'sp'){
						
						$subject = 'El pago correspondiente al GProCongreso II ha vencido.';
						$msg = '<div>Estimado '.$name.',</div><div><br></div>
						<div>El pago total de su inscripción al GProCongress II ha vencido.</div><div>&nbsp;</div>
						<div>Usted puede efectuar el pago en nuestra página web (https://www.gprocongress.org/payment) utilizando cualquiera de los siguientes métodos de pago:</div><div><br></div>
						<div><font color="#000000"><b>1.&nbsp; Pago en línea:</b></font></div>
						<div><font color="#000000"><b><br></b></font></div>
						<div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Pago con tarjeta de crédito:</i></b> puede realizar sus pagos con cualquiera de las principales tarjetas de crédito.</font></div>
						<div><br></div><div>&nbsp;</div>
						<div><font color="#000000"><b>2.&nbsp; Pago fuera de línea:</b> Si no puede pagar en línea, utilice una de las siguientes opciones de pago. Después de realizar el pago a través del modo fuera de línea, por favor registre su pago yendo a su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org.</a></div>
						<div><font color="#000000"><br></font></div>
						<div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Transferencia bancaria:</i></b> puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá las instrucciones por ese medio.</font></div>
						<div style="margin-left: 25px;"><font color="#000000"><b>b.&nbsp;&nbsp;<i>Western Union:</i></b> puede realizar sus pagos a través de Western Union. Por favor, envíe sus fondos a David Brugger, Dallas, Texas, USA.&nbsp; Junto con sus pagos, envíe la siguiente información a través de su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org.</a></div>
							<div style="margin-left: 25px;"><font color="#000000"><br></font></div>
							<div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">i. Su nombre completo</span></div>
							<div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">ii. País de procedencia del envío</span></div>
							<div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">iii. La cantidad enviada en USD</span></div>
							<div style="margin-left: 75px;"><span style="background-color: transparent; color: rgb(0, 0, 0);">iv. El código proporcionado por Western Union.</span></div><div>&nbsp;</div>
							<div>&nbsp;</div><div><p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p></div>
							<div>&nbsp;</div><div>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</div><div><br></div><div>Si tiene alguna pregunta sobre cómo hacer su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</div><div>&nbsp;</div><div>Únase a nuestra oración en favor de la cantidad y la calidad de los capacitadores de pastores.</div><div><br></div><div>Saludos cordiales,</div><div>El equipo del GProCongreso II</div>';
					
					}elseif($user->language == 'fr'){
					
						$subject = 'Votre paiement GProCongress II est maintenant dû.';
						$msg = '<p><font color="#242934" face="Montserrat, sans-serif">Cher '.$name.',</font></p>
						<p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">Le paiement de votre participation au GProCongress II est maintenant dû en totalité.</span></p><p><font color="#242934" face="Montserrat, sans-serif">Vous pouvez payer vos frais sur notre site Web (https://www.gprocongress.org/payment) en utilisant l’un des modes de paiement suivants:</font></p>
							<p><font color="#242934" face="Montserrat, sans-serif"><b>1. Paiement en ligne :</b></font></p>
							<p style="margin-left: 25px;"><span style="background-color: transparent; font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">a.</span><span style="background-color: transparent; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">&nbsp;</span><span style="font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;"><i>Paiement par carte de credit-</i></span><span style="background-color: transparent;"><font color="#999999"><b><i>&nbsp;</i></b></font><b style="font-style: italic; letter-spacing: inherit;">&nbsp;</b></span><font color="#242934" face="Montserrat, sans-serif" style="letter-spacing: inherit; background-color: transparent;">vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</font></p>
							<p><font color="#242934" face="Montserrat, sans-serif"><b>&nbsp;2. Paiement hors ligne :</b> Si vous ne pouvez pas payer en ligne, veuillez utiliser l’une des options de paiement suivantes. Après avoir effectué le paiement en mode hors ligne, veuillez enregistrer votre paiement en accédant à votre profil sur notre site Web https://www.gprocongress.org/payment.</font></p>
								<p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif">
								<b>a. <i style="">Virement bancaire –</i></b> vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org. Vous recevrez des instructions par réponse de l’e-mail.</font></p>
								<p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>b. <i>Western Union –</i> </b>vous pouvez payer vos frais via Western Union. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes en accédant à votre profil sur notre site Web https://www.gprocongress.org/payment.</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; i. Votre nom complet</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ii. Le pays à partir duquel vous envoyez</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iii. Le montant envoyé en dollars</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iv. Le code qui vous a été donné par Western Union.</font></p>
									
									<p><font color="#242934" face="Montserrat, sans-serif"><p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p></font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Si vous avez des questions concernant votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Cordialement</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">L’équipe GProCongress II</span></p>';
					
					}elseif($user->language == 'pt'){
					
						$subject = 'O seu pagamento para o II CongressoGPro em aberto';
						$msg = '<p>Prezado '.$name.',</p>
						<p>O pagamento para sua participação no II CongressoGPro está com o valor total em aberto.</p>
						<p>Pode pagar a sua inscrição no nosso website (https://www.gprocongress.org/payment) utilizando qualquer um dos vários métodos de pagamento:</p>
						<p><b>1. Pagamento online:</b></p><p style="margin-left: 25px;"><b>a.&nbsp; <i>Pagamento com cartão de crédito -</i></b> pode pagar as suas taxas utilizando qualquer um dos principais cartões de crédito.</p><p><b>2.&nbsp; Pagamento off-line:</b> Se não puder pagar on-line, por favor utilize uma das seguintes opções de pagamento. Após efetuar o pagamento através do modo offline, por favor registe o seu pagamento indo ao seu perfil no nosso website https://www.gprocongress.org/payment.</p>
						<p style="margin-left: 25px;"><b>a.&nbsp; <i>Transferência bancária -</i></b> pode pagar através de transferência bancária do seu banco. Se quiser fazer uma transferência bancária, envie por favor um e-mail para david@rreach.org. Receberá instruções através de e-mail de resposta.</p>
						<p style="margin-left: 25px;"><b>b.&nbsp; <i>Western Union -&nbsp;</i> </b>pode pagar as suas taxas através da Western Union. Por favor envie os seus fundos para David Brugger, Dallas, Texas, EUA.&nbsp; Juntamente com os seus fundos, envie por favor as seguintes informações, indo ao seu perfil no nosso website https://www.gprocongress.org/payment.</p>
							<p style="margin-left: 50px;">I. O seu nome completo</p>
							<p style="margin-left: 50px;">ii. O país de onde vai enviar</p>
							<p style="margin-left: 50px;">iii. O montante enviado em USD</p>
							<p style="margin-left: 50px;">iv. O código que lhe foi dado pela Western Union.</p>
							
							<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
							<p>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor&nbsp; previamente pagos por si serão retidos.</p>
							<p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p><p>Ore conosco no sentido de multiplicar a quantidade e qualidade dos formadores de pastores.</p><p>Com muito carinho,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Your GProCongress II payment is now due.';
						$msg = '<p><font color="#242934" face="Montserrat, sans-serif">Dear '.$name.',</font></p>
						<p><font color="#242934" face="Montserrat, sans-serif">Payment for your attendance at GProCongress II is now due in full.</font></p>
						<p><font color="#242934" face="Montserrat, sans-serif">You may pay your fees on our website (https://www.gprocongress.org/payment) using any of several payment methods:</font></p>
							<p><font color="#242934" face="Montserrat, sans-serif">1. <span style="white-space:pre">	</span><b>Online Payment:</b></font></p>
							<p style="margin-left: 50px;"><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">a. </span><span style="font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent; white-space: pre;">	</span><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;"><b><i style="">Payment using credit card</i> –</b> you can pay your fees using any major credit card.</span></p><p><font color="#242934" face="Montserrat, sans-serif">2. <b>Offline Payment:</b> If you cannot pay online, then please use one of the following payment options. After making the payment via offline mode, please register your payment by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">a.&nbsp; &nbsp; <b><i>Bank transfer –</i></b> you can pay via wire transfer from your bank. If you want to make a wire transfer, please emai david@rreach.org. You will receive instructions via reply email.&nbsp;</font></p>
								<p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">b.&nbsp; &nbsp; <b><i>Western Union –</i></b> you can pay your fees via Western Union. Please send your funds to David Brugger, Dallas Texas, USA.&nbsp; Along with your funds, please submit the following information by going to your profile in our website https://www.gprocongress.org/payment.</font></p>
									<p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;i.&nbsp; Your full name</font></p>
									<p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ii.&nbsp;&nbsp;The country you are sending from</font></p>
									<p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iii.&nbsp; The amount sent in USD</font></p>
									<p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iv.&nbsp; The code given to you by Western Union.</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif"><p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p></font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</font></p>
									<p><font color="#242934" face="Montserrat, sans-serif">If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Pray with us toward multiplying the quantity and quality of pastor-trainers.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Warmly,</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">GProCongress II Team</span></p><div><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; font-weight: 600;"><br></span></div>';
					
					}

					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'No response confirmation of their attendance');
				
					
				}
				
				return response(array('message'=>' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function EarlyBird2Aug2023(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'],['designation_id', '!=', '14'], ['stage', '>', '1'], ['amount', '>', 0]])->get();
			
			if(count($results) > 0){

				foreach ($results as $result) {

					if($result->early_bird == 'Yes'){
				
						$amounts = \App\Models\Wallet::where([['user_id', '=', $result->id], ['type', '=', 'Cr'], ['status', '=', 'Success']])->whereDate('created_at','<=','2022-11-23')->sum('amount');
						
						if($amounts > 0){

							$percentage = (((int)$amounts / (int)($result->amount)) * 100);

							if($percentage < 100) {

								$result->amount = $result->amount+100;
								$result->early_bird == 'No';
								$result->save();

							}
							
						}else{

							$result->amount = $result->amount+100;
							$result->save();
						}
					}
					
				}
				
				
			}

			return response(array("message"=>'Data set.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function spouseConfirmationMemberReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where('spouse_confirm_token','!=',null)->where('designation_id','!=','14')->where('email_reminder','1')->get();

			if(count($results) > 0){
				foreach ($results as $key => $existSpouse) {
				
					$name = '';

					$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
					if($user){

						$name = $user->salutation.' '.$user->name.' '.$user->last_name;

						if($user->language == 'sp'){

							$subject = "El estado de inscripción de su cónyuge no está confirmado";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Gracias por registrarse para asistir al GProCongress II 2023 en Ciudad de Panamá, Panamá con su cónyuge.&nbsp;&nbsp;</p><p><br></p><p>Estamos escribiendo para informarle que nuestro equipo ha hecho varios intentos para conectarse con su cónyuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', vía correo electrónico a '.$existSpouse->email.', para solicitar confirmación de su asistencia, pero no hemos tenido respuesta alguna.&nbsp;&nbsp;</p><p>Si desea que actualicemos la información de contacto de su cónyuge, responda a este correo electrónico y conéctese con nuestro equipo.</p><p><br></p><p>Por el momento, estamos cambiando nuestro registro para que su estado de inscripción diga "Persona casada que asiste sin cónyuge".</p><p>La tarifa de su habitación se ajustará en consecuencia.</p><p><br></p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($user->language == 'fr'){
						
							$subject = "Le statut d’inscription de votre conjoint/e n’est pas confirmé";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci de vous être inscrit pour assister au GProCongrès II l’année prochaine à Panama City, au Panama, avec votre conjoint/e.&nbsp;&nbsp;</p><p><br></p><p>Nous vous écrivons pour vous informer que notre équipe a tenté à plusieurs reprises de joindre votre conjoint/e, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', par courriel à '.$existSpouse->email.', pour demander une confirmation de sa présence, mais en vain.</p><p><br></p><p>Si vous souhaitez que nous mettions à jour les coordonnées de votre conjoint/e, veuillez répondre à ce courriel et communiquer avec notre équipe.&nbsp;</p><p><br></p><p>Pour le moment, nous modifions notre dossier afin que votre statut d’inscription indique « Personne mariée participant sans conjoint/e ».&nbsp;</p><p>Le tarif de votre chambre sera ajusté en conséquence.&nbsp;</p><p><br></p><p><br></p><p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
						}elseif($user->language == 'pt'){
						
							$subject = "O estado da inscrição do seu cônjuge não está confirmado";
							$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pela sua inscrição para participar no II CongressoGPro no próximo ano na Cidade de Panamá, Panamá, junto com seu cônjuge.&nbsp;</p><p><br></p><p>Estamos a escrever para lhe informar que nossa equipe fez várias tentativas para alcançar o seu cônjuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via e-mail pelo '.$existSpouse->email.', para pedir confirmação da participação dele/a, mas sem sucesso.&nbsp;</p><p><br></p><p>Se você gostaria que nós atualizássemos as informações de contato de seu cônjuge, por favor responda este e-mail, e se conecte com nossa equipe.</p><p><br></p><p>Por agora, estamos a mudar os nossos registos e assim, o estado da sua inscrição aparecerá “Pessoa Casada Participará Sem Cônjuge”.</p><p>A tarifa do seu quarto será ajustada de acordo.</p><p><br></p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Your spouse’s registration status is unconfirmed';
							$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for registering to attend GProCongress II next year in Panama City, Panama, with your spouse.&nbsp;&nbsp;</p><p><br></p><p>We are writing to inform you that our team has made several attempts to reach your spouse, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via email at '.$existSpouse->email.', to request confirmation of their attendance, but to no avail.</p><p><br></p><p>If you would like us to update your spouse’s contact information, please reply to this email, and connect with our team.&nbsp;</p><p><br></p><p>For the moment, we are changing our record so your registration status reads “Married Person Attending Without Spouse.”&nbsp;</p><p>Your room rate will be adjusted accordingly.&nbsp;</p><p><br></p><p>Have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
						
						}

						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

						\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
	
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'No response confirmation of their attendance');
					
					}
					
					
				}
				
				return response(array('message'=>' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	
	public function spouseConfirmationFirstReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where('spouse_confirm_token','!=','')->where('spouse_confirm_token','!=',null)->where('designation_id','!=','14')->get();

			if(count($results) > 0){

				foreach ($results as $key => $existSpouse) {
				
					$reminder = json_decode($existSpouse->spouse_confirm_reminder_email);
					
					if($existSpouse->spouse_confirm_token && $reminder->reminder == 0){
						 
						if($reminder->date == date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))))){

							$name = '';

							$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
							if($user){

								$name = $user->salutation.' '.$user->name.' '.$user->last_name;
								
								if($user->language == 'sp'){

									$subject = "El estado de inscripción de su cónyuge no está confirmado";
									$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Gracias por registrarse para asistir al GProCongress II 2023 en Ciudad de Panamá, Panamá con su cónyuge.&nbsp;&nbsp;</p><p><br></p><p>Estamos escribiendo para informarle que nuestro equipo ha hecho varios intentos para conectarse con su cónyuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', vía correo electrónico a '.$existSpouse->email.', para solicitar confirmación de su asistencia, pero no hemos tenido respuesta alguna.&nbsp;&nbsp;</p><p>Si desea que actualicemos la información de contacto de su cónyuge, responda a este correo electrónico y conéctese con nuestro equipo.</p><p><br></p><p>Por el momento, estamos cambiando nuestro registro para que su estado de inscripción diga "Persona casada que asiste sin cónyuge".</p><p>La tarifa de su habitación se ajustará en consecuencia.</p><p><br></p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($user->language == 'fr'){
								
									$subject = "Le statut d’inscription de votre conjoint/e n’est pas confirmé";
									$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci de vous être inscrit pour assister au GProCongrès II l’année prochaine à Panama City, au Panama, avec votre conjoint/e.&nbsp;&nbsp;</p><p><br></p><p>Nous vous écrivons pour vous informer que notre équipe a tenté à plusieurs reprises de joindre votre conjoint/e, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', par courriel à '.$existSpouse->email.', pour demander une confirmation de sa présence, mais en vain.</p><p><br></p><p>Si vous souhaitez que nous mettions à jour les coordonnées de votre conjoint/e, veuillez répondre à ce courriel et communiquer avec notre équipe.&nbsp;</p><p><br></p><p>Pour le moment, nous modifions notre dossier afin que votre statut d’inscription indique « Personne mariée participant sans conjoint/e ».&nbsp;</p><p>Le tarif de votre chambre sera ajusté en conséquence.&nbsp;</p><p><br></p><p><br></p><p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
								
								}elseif($user->language == 'pt'){
								
									$subject = "O estado da inscrição do seu cônjuge não está confirmado";
									$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pela sua inscrição para participar no II CongressoGPro no próximo ano na Cidade de Panamá, Panamá, junto com seu cônjuge.&nbsp;</p><p><br></p><p>Estamos a escrever para lhe informar que nossa equipe fez várias tentativas para alcançar o seu cônjuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via e-mail pelo '.$existSpouse->email.', para pedir confirmação da participação dele/a, mas sem sucesso.&nbsp;</p><p><br></p><p>Se você gostaria que nós atualizássemos as informações de contato de seu cônjuge, por favor responda este e-mail, e se conecte com nossa equipe.</p><p><br></p><p>Por agora, estamos a mudar os nossos registos e assim, o estado da sua inscrição aparecerá “Pessoa Casada Participará Sem Cônjuge”.</p><p>A tarifa do seu quarto será ajustada de acordo.</p><p><br></p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Your spouse’s registration status is unconfirmed';
									$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for registering to attend GProCongress II next year in Panama City, Panama, with your spouse.&nbsp;&nbsp;</p><p><br></p><p>We are writing to inform you that our team has made several attempts to reach your spouse, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via email at '.$existSpouse->email.', to request confirmation of their attendance, but to no avail.</p><p><br></p><p>If you would like us to update your spouse’s contact information, please reply to this email, and connect with our team.&nbsp;</p><p><br></p><p>For the moment, we are changing our record so your registration status reads “Married Person Attending Without Spouse.”&nbsp;</p><p>Your room rate will be adjusted accordingly.&nbsp;</p><p><br></p><p>Have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
								
								}

								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
			
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'No response confirmation of their attendance');
								
							}

							$userData = \App\Models\User::where('id',$existSpouse->id)->first();
							if($userData){

								$reminderData = [
									'type'=>'spouse_reminder',
									'date'=>$reminder->date,
									'reminder'=>'1',
		
								];

								$userData->spouse_confirm_reminder_email = json_encode($reminderData);
								$userData->save();
							}

							$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';

							if($existSpouse->language == 'sp'){

								$subject = "IMPORTANTE: Por favor, confirme el estado de su registro. (PRIMER RECORDATORIO)";
								$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p><br></p><p>Hemos recibido '.$name.' una solicitud&nbsp; para el GproCongress II</p><p><br></p><p>'.$name.' ha indicado que asistirán juntos al Congreso. También hemos recibido su solicitud, pero para seguir procesando su registro, necesitamos verificar cierta información.</p><p>Por favor, reponda este correo '.$confLink.' para confirmer que usted y '.$name.' estan casados y que asistirán al GproCongress II juntos.&nbsp;&nbsp;</p><p><br></p><p>TOME NOTA: Si no responde a este correo electrónico, su inscripción NO se habrá completado y NO podrá participar en el Congreso.</p><p><br></p><p>Los esperamos a usted y a '.$name.' en Ciudad de Panamá, Panamá, del 12 al 17 de noviembre de 2023.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
							
							}elseif($existSpouse->language == 'fr'){
							
								$subject = "IMPORTANT: Veuillez confirmer votre statut d’inscription. (PREMIER RAPPEL)";
								$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Nous avons reçu la demande de '.$name.' pour le GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqué que vous assisterez au Congrès ensemble.&nbsp; Votre demande a également été reçue, mais pour poursuivre le processus de votre inscription, nous devons vérifier certaines informations.</p><p><br></p><p>Veuillez répondre à cet e-mail '.$confLink.' pour confirmer que vous et '.$name.' êtes mariés et que vous assisterez ensemble au GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>VEUILLEZ NOTER: Si vous ne répondez pas à ce courriel, votre inscription ne sera PAS complétée et vous ne serez PAS admissible à participer au Congrès.</p><p><br></p><p>Nous avons hâte de vous voir, vous et '.$name.', à Panama City, au Panama du 12 au 17 novembre 2023!&nbsp;</p><p><br></p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe GProCongrès II</p>';
							
							}elseif($existSpouse->language == 'pt'){
							
								$subject = "IMPORTANTE: Por favor confirme o estado da sua inscrição. (PRIMEIRO LEMBRETE)";
								$msg = '<p>Prezado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p><br></p><p>Recebemos o pedido de '.$name.' para o II CongressoGPro.&nbsp;</p><p><br></p><p>'.$name.' afirmou que vocês iriam participar o Congresso juntos.&nbsp;</p><p>Seu pedido também foi recebido, mas para dar seguimento ao processo da sua inscrição, nós precisamos de verificar algumas informações.&nbsp;</p><p><br></p><p>Por favor responda a este e-mail '.$confLink.' para confirmar que você e '.$name.' estão casados, e que vocês irão participar do II CongressoGPro juntos.</p><p><br></p><p>POR FAVOR NOTE: Se você não responder a este e-mail, sua inscrição NÃO estará completa, e você NÃO será elegível a participar do Congresso.</p><p>Esperamos vos ver, ambos, você e '.$name.', na Cidade de Panamá, Panamá, de 12 a 17 de Novembro de 2023!</p><p><br></p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'IMPORTANT: Please confirm your registration status. (FIRST REMINDER)';
								$msg = '<p>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>We have received '.$name.'’s application for GProCongress II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' has indicated that you will be attending the Congress, together.&nbsp; Your application has also been received, but to further process your registration, we need to verify some information.</p><p><br></p><p>Please reply to this email '.$confLink.' to confirm that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</p><p><br></p><p>PLEASE NOTE: If you do not reply to this email, your registration will NOT be completed, and you will NOT be eligible to participate in the Congress.</p><p><br></p><p>We look forward to seeing, both, you and '.$name.', in Panama City, Panama on November 12-17, 2023!&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
							
							}
							\App\Helpers\commonHelper::userMailTrigger($existSpouse->id,$msg,$subject);

							\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
							
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- FIRST REMINDER');
							
						}
						
					}	
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function spouseConfirmation2Reminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where('spouse_confirm_token','!=','')->where('spouse_confirm_token','!=',null)->where('designation_id','!=','14')->where('email_reminder','1')->get();

			if(count($results) > 0){
				foreach ($results as $key => $existSpouse) {
				
					$reminder = json_decode($existSpouse->spouse_confirm_reminder_email);

					if($existSpouse->spouse_confirm_token && $reminder->reminder == 1){
						 
						if($reminder->date == date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))))){

							$name = '';

							$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
							if($user){

								$name = $user->salutation.' '.$user->name.' '.$user->last_name;

								
								if($user->language == 'sp'){

									$subject = "El estado de inscripción de su cónyuge no está confirmado";
									$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Gracias por registrarse para asistir al GProCongress II 2023 en Ciudad de Panamá, Panamá con su cónyuge.&nbsp;&nbsp;</p><p><br></p><p>Estamos escribiendo para informarle que nuestro equipo ha hecho varios intentos para conectarse con su cónyuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', vía correo electrónico a '.$existSpouse->email.', para solicitar confirmación de su asistencia, pero no hemos tenido respuesta alguna.&nbsp;&nbsp;</p><p>Si desea que actualicemos la información de contacto de su cónyuge, responda a este correo electrónico y conéctese con nuestro equipo.</p><p><br></p><p>Por el momento, estamos cambiando nuestro registro para que su estado de inscripción diga "Persona casada que asiste sin cónyuge".</p><p>La tarifa de su habitación se ajustará en consecuencia.</p><p><br></p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($user->language == 'fr'){
								
									$subject = "Le statut d’inscription de votre conjoint/e n’est pas confirmé";
									$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci de vous être inscrit pour assister au GProCongrès II l’année prochaine à Panama City, au Panama, avec votre conjoint/e.&nbsp;&nbsp;</p><p><br></p><p>Nous vous écrivons pour vous informer que notre équipe a tenté à plusieurs reprises de joindre votre conjoint/e, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', par courriel à '.$existSpouse->email.', pour demander une confirmation de sa présence, mais en vain.</p><p><br></p><p>Si vous souhaitez que nous mettions à jour les coordonnées de votre conjoint/e, veuillez répondre à ce courriel et communiquer avec notre équipe.&nbsp;</p><p><br></p><p>Pour le moment, nous modifions notre dossier afin que votre statut d’inscription indique « Personne mariée participant sans conjoint/e ».&nbsp;</p><p>Le tarif de votre chambre sera ajusté en conséquence.&nbsp;</p><p><br></p><p><br></p><p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
								
								}elseif($user->language == 'pt'){
								
									$subject = "O estado da inscrição do seu cônjuge não está confirmado";
									$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pela sua inscrição para participar no II CongressoGPro no próximo ano na Cidade de Panamá, Panamá, junto com seu cônjuge.&nbsp;</p><p><br></p><p>Estamos a escrever para lhe informar que nossa equipe fez várias tentativas para alcançar o seu cônjuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via e-mail pelo '.$existSpouse->email.', para pedir confirmação da participação dele/a, mas sem sucesso.&nbsp;</p><p><br></p><p>Se você gostaria que nós atualizássemos as informações de contato de seu cônjuge, por favor responda este e-mail, e se conecte com nossa equipe.</p><p><br></p><p>Por agora, estamos a mudar os nossos registos e assim, o estado da sua inscrição aparecerá “Pessoa Casada Participará Sem Cônjuge”.</p><p>A tarifa do seu quarto será ajustada de acordo.</p><p><br></p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Your spouse’s registration status is unconfirmed';
									$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for registering to attend GProCongress II next year in Panama City, Panama, with your spouse.&nbsp;&nbsp;</p><p><br></p><p>We are writing to inform you that our team has made several attempts to reach your spouse, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via email at '.$existSpouse->email.', to request confirmation of their attendance, but to no avail.</p><p><br></p><p>If you would like us to update your spouse’s contact information, please reply to this email, and connect with our team.&nbsp;</p><p><br></p><p>For the moment, we are changing our record so your registration status reads “Married Person Attending Without Spouse.”&nbsp;</p><p>Your room rate will be adjusted accordingly.&nbsp;</p><p><br></p><p>Have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
								
								}

								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
			
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'No response confirmation of their attendance');
								
							}

							
							$userData = \App\Models\User::where('id',$existSpouse->id)->first();
							if($userData){

								$reminderData = [
									'type'=>'spouse_reminder',
									'date'=>$reminder->date,
									'reminder'=>'2',
		
								];

								$userData->spouse_confirm_reminder_email = json_encode($reminderData);
								$userData->save();
							}
							
							
							$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';

							if($existSpouse->language == 'sp'){

								$subject = "URGENTE: Por favor, confirme su estado de inscripción. (SEGUNDO RECORDATORIO)";
								$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p>Hemos recibido la solicitud de '.$name.' para participar en el GProCongress II.</p><p><br></p><p>'.$name.' ha indicado que asistirán juntos al Congreso. También hemos recibido su solicitud, pero para seguir procesando su inscripción, necesitamos verificar cierta información.</p><p><br></p><p>Por favor responda a este correo para confirmar que usted y '.$name.' están casados, y que asistirán al GproCongress juntos.&nbsp;</p><p><br></p><p>TOME NOTA: Si no responde a este correo electrónico '.$confLink.', su inscripción NO se habrá completado y NO podrá participar en el Congreso.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
							
							}elseif($existSpouse->language == 'fr'){
							
								$subject = "URGENT: Veuillez confirmer votre statut d’inscription. (DEUXIÈME RAPPEL)";
								$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Nous avons reçu la demande de '.$name.' pour le GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqué que vous assisterez au Congrès ensemble.&nbsp; Votre demande a également été reçue, mais pour poursuivre le processus de votre inscription, nous devons vérifier certaines informations.</p><p><br></p><p>Veuillez répondre à cet e-mail pour confirmer que vous et '.$name.' êtes mariés et que vous assisterez ensemble au GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>VEUILLEZ NOTER: Si vous ne répondez pas à ce courriel '.$confLink.', votre inscription ne sera PAS complétée et vous ne serez PAS admissible à participer au Congrès.</p><p><br></p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';
							
							}elseif($existSpouse->language == 'pt'){
							
								$subject = "URGENTE: Por favor confirme o estado da sua inscrição. (SEGUNDO LEMBRETE)";
								$msg = '<p>Prezado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p><br></p><p><br></p><p>Recebemos o pedido de '.$name.' para o II CongressoGPro.&nbsp;</p><p><br></p><p>'.$name.' afirmou que vocês iriam participar o Congresso juntos.&nbsp;</p><p>Seu pedido também foi recebido, mas para dar seguimento ao processo da sua inscrição, nós precisamos de verificar algumas informações.</p><p><br></p><p>Por favor responda a este e-mail para confirmar que você e '.$name.' estão casados, e que vocês irão participar do II CongressoGPro juntos.</p><p><br></p><p>POR FAVOR NOTE: Se você não responder a este e-mail '.$confLink.', sua inscrição NÃO estará completa, e você NÃO será elegível a participar do Congresso.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'URGENT: Please confirm your registration status. (SECOND REMINDER)';
								$msg = '<p>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>We have received '.$name.'’s application for GProCongress II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' has indicated that you will be attending the Congress, together.&nbsp; Your application has also been received, but to further process your registration, we need to verify some information.</p><p><br></p><p>Please reply to this email to confirm that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</p><p><br></p><p>PLEASE NOTE: If you do not reply to this email '.$confLink.', your registration will NOT be completed, and you will NOT be eligible to participate in the Congress.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
							
							}

							\App\Helpers\commonHelper::userMailTrigger($existSpouse->id,$msg,$subject);

							\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
							
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- SECOND REMINDER');
						}
					}
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function spouseConfirmation3Reminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where('spouse_confirm_token','!=','')->where('spouse_confirm_token','!=',null)->where('designation_id','!=','14')->where('email_reminder','1')->get();

			if(count($results) > 0){

				foreach ($results as $key => $existSpouse) {
				
					$reminder = json_decode($existSpouse->spouse_confirm_reminder_email);

					if($existSpouse->spouse_confirm_token && $reminder->reminder == 2){
						 
						if($reminder->date == date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))))){

							$name = '';

							$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
							if($user){

								$name = $user->salutation.' '.$user->name.' '.$user->last_name;

								
								if($user->language == 'sp'){

									$subject = "El estado de inscripción de su cónyuge no está confirmado";
									$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Gracias por registrarse para asistir al GProCongress II 2023 en Ciudad de Panamá, Panamá con su cónyuge.&nbsp;&nbsp;</p><p><br></p><p>Estamos escribiendo para informarle que nuestro equipo ha hecho varios intentos para conectarse con su cónyuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', vía correo electrónico a '.$existSpouse->email.', para solicitar confirmación de su asistencia, pero no hemos tenido respuesta alguna.&nbsp;&nbsp;</p><p>Si desea que actualicemos la información de contacto de su cónyuge, responda a este correo electrónico y conéctese con nuestro equipo.</p><p><br></p><p>Por el momento, estamos cambiando nuestro registro para que su estado de inscripción diga "Persona casada que asiste sin cónyuge".</p><p>La tarifa de su habitación se ajustará en consecuencia.</p><p><br></p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($user->language == 'fr'){
								
									$subject = "Le statut d’inscription de votre conjoint/e n’est pas confirmé";
									$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci de vous être inscrit pour assister au GProCongrès II l’année prochaine à Panama City, au Panama, avec votre conjoint/e.&nbsp;&nbsp;</p><p><br></p><p>Nous vous écrivons pour vous informer que notre équipe a tenté à plusieurs reprises de joindre votre conjoint/e, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', par courriel à '.$existSpouse->email.', pour demander une confirmation de sa présence, mais en vain.</p><p><br></p><p>Si vous souhaitez que nous mettions à jour les coordonnées de votre conjoint/e, veuillez répondre à ce courriel et communiquer avec notre équipe.&nbsp;</p><p><br></p><p>Pour le moment, nous modifions notre dossier afin que votre statut d’inscription indique « Personne mariée participant sans conjoint/e ».&nbsp;</p><p>Le tarif de votre chambre sera ajusté en conséquence.&nbsp;</p><p><br></p><p><br></p><p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
								
								}elseif($user->language == 'pt'){
								
									$subject = "O estado da inscrição do seu cônjuge não está confirmado";
									$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pela sua inscrição para participar no II CongressoGPro no próximo ano na Cidade de Panamá, Panamá, junto com seu cônjuge.&nbsp;</p><p><br></p><p>Estamos a escrever para lhe informar que nossa equipe fez várias tentativas para alcançar o seu cônjuge, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via e-mail pelo '.$existSpouse->email.', para pedir confirmação da participação dele/a, mas sem sucesso.&nbsp;</p><p><br></p><p>Se você gostaria que nós atualizássemos as informações de contato de seu cônjuge, por favor responda este e-mail, e se conecte com nossa equipe.</p><p><br></p><p>Por agora, estamos a mudar os nossos registos e assim, o estado da sua inscrição aparecerá “Pessoa Casada Participará Sem Cônjuge”.</p><p>A tarifa do seu quarto será ajustada de acordo.</p><p><br></p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Your spouse’s registration status is unconfirmed';
									$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for registering to attend GProCongress II next year in Panama City, Panama, with your spouse.&nbsp;&nbsp;</p><p><br></p><p>We are writing to inform you that our team has made several attempts to reach your spouse, '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', via email at '.$existSpouse->email.', to request confirmation of their attendance, but to no avail.</p><p><br></p><p>If you would like us to update your spouse’s contact information, please reply to this email, and connect with our team.&nbsp;</p><p><br></p><p>For the moment, we are changing our record so your registration status reads “Married Person Attending Without Spouse.”&nbsp;</p><p>Your room rate will be adjusted accordingly.&nbsp;</p><p><br></p><p>Have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
								
								}

								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
			
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'No response confirmation of their attendance');
								
							}

							$userData = \App\Models\User::where('id',$existSpouse->id)->first();
							if($userData){

								$reminderData = [
									'type'=>'spouse_reminder',
									'date'=>$reminder->date,
									'reminder'=>'3',
		
								];

								$userData->spouse_confirm_reminder_email = json_encode($reminderData);
								$userData->save();
							}

							$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';

							if($user->language == 'sp'){

								$subject = "MUY URGENTE: Por favor, confirme su estado de inscripción. (TERCER RECORDATORIO)";
								$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Hemos recibido la solicitud de '.$name.' para participar en el GProCongress II.</p><p><br></p><p>'.$name.' ha indicado que asistirán juntos al Congreso. También hemos recibido su solicitud, pero para seguir procesando su inscripción, necesitamos verificar cierta información.</p><p><br></p><p>Por favor responda a este '.$confLink.' correo para confirmar que usted y '.$name.' están casados, y que asistirán al GproCongress juntos.&nbsp;</p><p><br></p><p>TENGA EN CUENTA QUE ESTE ES EL TERCER Y ÚLTIMO RECORDATORIO. Si no responde a este correo electrónico, su inscripción NO se completará y NO podrá participar en el Congreso.Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
							
							}elseif($user->language == 'fr'){
							
								$subject = "TRÈS URGENT: Veuillez confirmer votre statut d’inscription. (TROISIÈME RAPPEL)";
								$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',&nbsp;</p><p>Nous avons reçu la demande de '.$name.' pour le GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p><br></p><p><br></p><p>'.$name.' a indiqué que vous assisterez au Congrès ensemble.&nbsp; Votre demande a également été reçue, mais pour poursuivre le processus de votre inscription, nous devons vérifier certaines informations.&nbsp;</p><p><br></p><p>Veuillez répondre à cet e-mail '.$confLink.' pour confirmer que vous et '.$name.' êtes mariés et que vous assisterez ensemble au GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>VEUILLEZ NOTER QU’IL S’AGIT DE VOTRE TROISIÈME ET DERNIER RAPPEL.&nbsp; Si vous ne répondez pas à ce courriel, votre inscription ne sera PAS complétée et vous ne serez PAS admissible à participer au Congrès.</p><p><br></p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';
							
							}elseif($user->language == 'pt'){
							
								$subject = "MUITO URGENTE: Por favor confirme o estado da sua inscrição. (TERCEIRO LEMBRETE)";
								$msg = '<p>Prezado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Recebemos o pedido de '.$name.' para o II CongressoGPro.&nbsp;</p><p><br></p><p>'.$name.' afirmou que vocês iriam participar o Congresso juntos.&nbsp;</p><p>Seu pedido também foi recebido, mas para dar seguimento ao processo da sua inscrição, nós precisamos de verificar algumas informações.</p><p><br></p><p>Por favor responda a este e-mail '.$confLink.' para confirmar que você e '.$name.' estão casados, e que vocês irão participar do II CongressoGPro juntos.</p><p><br></p><p>POR FAVOR NOTE: ESTE É O SEU TERCEIRO E ÚLTIMO LEMBRETE. Se você não responder a este e-mail, sua inscrição NÃO estará completa, e você NÃO será elegível a participar do Congresso.</p><p><br></p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'VERY URGENT: Please confirm your registration status. (THIRD REMINDER)';
								$msg = '<p>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>We have received '.$name.'’s application for GProCongress II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' has indicated that you will be attending the Congress, together.&nbsp; Your application has also been received, but to further process your registration, we need to verify some information.</p><p><br></p><p>Please reply to this email '.$confLink.' to confirm that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</p><p><br></p><p>PLEASE NOTE: THIS IS YOUR THIRD AND FINAL REMINDER. If you do not reply to this email, your registration will NOT be completed, and you will NOT be eligible to participate in the Congress.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
							
							}

							\App\Helpers\commonHelper::userMailTrigger($existSpouse->id,$msg,$subject);

							\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
							
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- THIRD REMINDER');
							
						}
					}
					
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

    public function SpouseRejectActionCron(Request $request){

        $existSpouse = \App\Models\User::where('spouse_confirm_token','!=','')->where('spouse_confirm_token','!=',null)->where('designation_id','!=','14')->get();

		if(count($existSpouse) > 0){

			foreach ($existSpouse as $key => $linkPayment) {

				$reminder = json_decode($existSpouse->spouse_confirm_reminder_email);

				if($existSpouse->spouse_confirm_token && $reminder->reminder == 3){
						 
					if($reminder->date == date('Y-m-d', strtotime('-4 day', strtotime(date('Y-m-d'))))){
									
						$history = new \App\Models\SpouseStatusHistory;
						$history->spouse_id = $linkPayment->id;
						$history->parent_id = $linkPayment->parent_id;

						$history->remark = "Your spouse's application has been declined.";
						$history->status = 'Reject';

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
							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

							\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your spouse application has been declined.');


						}

						$spouseUser = \App\Models\User::where('id',$linkPayment->id)->first();
						$spouseUser->parent_id = null;
						$spouseUser->added_as = null;
						$spouseUser->spouse_confirm_status = 'Decline';
						$spouseUser->spouse_confirm_token = '';
						$spouseUser->room = 'Sharing';
						$spouseUser->save();

						$user->room = 'Sharing';
						$user->save();
					
						$history->save();
					}
				}
			}

			return response(array('message'=>'Reminders has been sent successfully.'), 200);

        }else{

			return response(array('message'=>'Data not found.'), 403);

        }

    }

	public function getAllLanguageFolderFile(Request $request){
		
		try {
			
			if($request->json()->get('lang') == 'fr'){

				$result=[
					'app'=>\File::getRequire(base_path().'/resources/lang/fr/web/app.php'),
					'change-password'=>\File::getRequire(base_path().'/resources/lang/fr/web/change-password.php'),
					'contact-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/contact-details.php'),
					'faq'=>\File::getRequire(base_path().'/resources/lang/fr/web/faq.php'),
					'group-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/group-details.php'),
					'help'=>\File::getRequire(base_path().'/resources/lang/fr/web/help.php'),
					'home'=>\File::getRequire(base_path().'/resources/lang/fr/web/home.php'),
					'ministry-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/ministry-details.php'),
					'payment'=>\File::getRequire(base_path().'/resources/lang/fr/web/payment.php'),
					'pricing'=>\File::getRequire(base_path().'/resources/lang/fr/web/pricing.php'),
					'reset-password'=>\File::getRequire(base_path().'/resources/lang/fr/web/reset-password.php'),
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/fr/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/fr/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/fr/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/profile-details.php'),
					'profile'=>\File::getRequire(base_path().'/resources/lang/fr/web/profile.php'),
					'commonHelperValue'=>\File::getRequire(base_path().'/resources/lang/fr/web/common-helper-value.php'),
					'wizard'=>\File::getRequire(base_path().'/resources/lang/fr/web/wizard.php'),
					
				];


			}elseif($request->json()->get('lang') == 'pt'){

				$result=[
					'app'=>\File::getRequire(base_path().'/resources/lang/pt/web/app.php'),
					'change-password'=>\File::getRequire(base_path().'/resources/lang/pt/web/change-password.php'),
					'contact-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/contact-details.php'),
					'faq'=>\File::getRequire(base_path().'/resources/lang/pt/web/faq.php'),
					'group-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/group-details.php'),
					'help'=>\File::getRequire(base_path().'/resources/lang/pt/web/help.php'),
					'home'=>\File::getRequire(base_path().'/resources/lang/pt/web/home.php'),
					'ministry-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/ministry-details.php'),
					'payment'=>\File::getRequire(base_path().'/resources/lang/pt/web/payment.php'),
					'pricing'=>\File::getRequire(base_path().'/resources/lang/en/web/pricing.php'),
					'reset-password'=>\File::getRequire(base_path().'/resources/lang/pt/web/reset-password.php'),
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/pt/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/pt/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/pt/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/profile-details.php'),
					'profile'=>\File::getRequire(base_path().'/resources/lang/pt/web/profile.php'),
					'commonHelperValue'=>\File::getRequire(base_path().'/resources/lang/pt/web/common-helper-value.php'),
					'wizard'=>\File::getRequire(base_path().'/resources/lang/pt/web/wizard.php'),

				];


			}elseif($request->json()->get('lang') == 'sp'){

				$result=[
					'app'=>\File::getRequire(base_path().'/resources/lang/sp/web/app.php'),
					'change-password'=>\File::getRequire(base_path().'/resources/lang/sp/web/change-password.php'),
					'contact-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/contact-details.php'),
					'faq'=>\File::getRequire(base_path().'/resources/lang/sp/web/faq.php'),
					'group-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/group-details.php'),
					'help'=>\File::getRequire(base_path().'/resources/lang/sp/web/help.php'),
					'home'=>\File::getRequire(base_path().'/resources/lang/sp/web/home.php'),
					'ministry-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/ministry-details.php'),
					'payment'=>\File::getRequire(base_path().'/resources/lang/sp/web/payment.php'),
					'pricing'=>\File::getRequire(base_path().'/resources/lang/sp/web/pricing.php'),
					'reset-password'=>\File::getRequire(base_path().'/resources/lang/sp/web/reset-password.php'),
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/sp/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/sp/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/sp/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/profile-details.php'),
					'profile'=>\File::getRequire(base_path().'/resources/lang/sp/web/profile.php'),
					'commonHelperValue'=>\File::getRequire(base_path().'/resources/lang/sp/web/common-helper-value.php'),
					'wizard'=>\File::getRequire(base_path().'/resources/lang/sp/web/wizard.php'),

				];

				
			}else{

				$result=[
					'app'=>\File::getRequire(base_path().'/resources/lang/en/web/app.php'),
					'change-password'=>\File::getRequire(base_path().'/resources/lang/en/web/change-password.php'),
					'contact-details'=>\File::getRequire(base_path().'/resources/lang/en/web/contact-details.php'),
					'faq'=>\File::getRequire(base_path().'/resources/lang/en/web/faq.php'),
					'group-details'=>\File::getRequire(base_path().'/resources/lang/en/web/group-details.php'),
					'help'=>\File::getRequire(base_path().'/resources/lang/en/web/help.php'),
					'home'=>\File::getRequire(base_path().'/resources/lang/en/web/home.php'),
					'ministry-details'=>\File::getRequire(base_path().'/resources/lang/en/web/ministry-details.php'),
					'payment'=>\File::getRequire(base_path().'/resources/lang/en/web/payment.php'),
					'pricing'=>\File::getRequire(base_path().'/resources/lang/en/web/pricing.php'),
					'reset-password'=>\File::getRequire(base_path().'/resources/lang/en/web/reset-password.php'),
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/en/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/en/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/en/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/en/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/en/web/profile-details.php'),
					'profile'=>\File::getRequire(base_path().'/resources/lang/en/web/profile.php'),
					'wizard'=>\File::getRequire(base_path().'/resources/lang/en/web/wizard.php'),

				];
			}


			return response(array("message"=>'language data fetch done.','result'=>$result), 200);
			
		} catch (\Exception $e) {
			
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function getApprovedUserSendEmail(Request $request){
		
		try {
			
			$results = \App\Models\User::where('profile_status','Approved')->where('stage','2')->get();
			// $results = \App\Models\User::where('email','gopalsaini.img@gmail.com')->get();

			if(count($results) > 0){

				foreach ($results as $key => $user) {
				
					$name = $user->salutation.' '.$user->name.' '.$user->last_name;

					if($user->language == 'sp'){
						
						$subject = 'El pago correspondiente al GProCongreso II ha vencido.';
						//When Paypal is active uncomment the commented line and comment the uncommented line
						// $msg = '<div>Estimado '.$name.',</div><div><br></div><div>El pago total de su inscripción al GProCongress II ha vencido.</div><div>&nbsp;</div><div>Usted puede efectuar el pago en nuestra página web (https://www.gprocongress.org/payment) utilizando cualquiera de los siguientes métodos de pago:</div><div><br></div><div><font color="#000000"><b>1.&nbsp; Pago en línea:</b></font></div><div><font color="#000000"><b><br></b></font></div><div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Pago con tarjeta de crédito:</i></b> puede realizar sus pagos con cualquiera de las principales tarjetas de crédito.</font></div><div style="margin-left: 25px;"><font color="#000000"><b>b.&nbsp;&nbsp;<i>Pago mediante Paypal -</i></b> si tiene una cuenta PayPal, puede hacer sus pagos a través de PayPal entrando en nuestra página web (https://www.gprocongress.or/paymentg).&nbsp; Por favor, envíe sus fondos a: david@rreach.org (esta es la cuenta de RREACH).&nbsp; En la transferencia debe anotar el nombre de la persona inscrita.</font></div><div><br></div><div>&nbsp;</div><div><font color="#000000"><b>2.&nbsp; Pago fuera de línea:</b> Si no puede pagar en línea, utilice una de las siguientes opciones de pago. Después de realizar el pago a través del modo fuera de línea, por favor registre su pago yendo a su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org/payment.</a></div><div><font color="#000000"><br></font></div><div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Transferencia bancaria:</i></b> puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá las instrucciones por ese medio.</font></div><div style="margin-left: 25px;"><font color="#000000"><b>b.&nbsp;&nbsp;<i>Western Union:</i></b> puede realizar sus pagos a través de Western Union. Por favor, envíe sus fondos a David Brugger, Dallas, Texas, USA.&nbsp; Junto con sus pagos, envíe la siguiente información a través de su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org/payment.</a></div><div style="margin-left: 25px;"><font color="#000000"><br></font></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">i. Su nombre completo</span></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">ii. País de procedencia del envío</span></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">iii. La cantidad enviada en USD</span></div><div style="margin-left: 75px;"><span style="background-color: transparent; color: rgb(0, 0, 0);">iv. El código proporcionado por Western Union.</span></div><div>&nbsp;</div><div style="margin-left: 25px;"><font color="#000000"><b>c. <i>RIA:</i></b> Por favor, envíe sus fondos a David Brugger, Dallas, Texas, USA.&nbsp; Junto con sus fondos, por favor envíe la siguiente información yendo a su perfil en nuestro sitio web https://www.gprocongress.org.</font></div><div>&nbsp;</div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0);">i. Su nombre completo</span><br></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0);">ii. País de procedencia del envío</span><br></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0);">iii. La cantidad enviada en USD</span><br></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">iv. El código proporcionado por</span><font color="#000000">&nbsp;RIA</font></div><div>&nbsp;</div><div>POR FAVOR, TENGA EN CUENTA: Para poder aprovechar el descuento por “inscripción anticipada”, el pago en su totalidad tiene que recibirse a más tardar el 31 de mayo de 2023.</div><div>&nbsp;</div><div>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</div><div><br></div><div>Si tiene alguna pregunta sobre cómo hacer su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</div><div>&nbsp;</div><div>Únase a nuestra oración en favor de la cantidad y la calidad de los capacitadores de pastores.</div><div><br></div><div>Saludos cordiales,</div><div>El equipo del GProCongreso II</div>';
						$msg = '<div>Estimado '.$name.',</div><div><br></div><div>El pago total de su inscripción al GProCongress II ha vencido.</div><div>&nbsp;</div><div>Usted puede efectuar el pago en nuestra página web (https://www.gprocongress.org/payment) utilizando cualquiera de los siguientes métodos de pago:</div><div><br></div><div><font color="#000000"><b>1.&nbsp; Pago en línea:</b></font></div><div><font color="#000000"><b><br></b></font></div><div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Pago con tarjeta de crédito:</i></b> puede realizar sus pagos con cualquiera de las principales tarjetas de crédito.</font></div><div><br></div><div>&nbsp;</div><div><font color="#000000"><b>2.&nbsp; Pago fuera de línea:</b> Si no puede pagar en línea, utilice una de las siguientes opciones de pago. Después de realizar el pago a través del modo fuera de línea, por favor registre su pago yendo a su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org.</a></div><div><font color="#000000"><br></font></div><div style="margin-left: 25px;"><font color="#000000"><b>a.&nbsp;&nbsp;<i>Transferencia bancaria:</i></b> puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá las instrucciones por ese medio.</font></div><div style="margin-left: 25px;"><font color="#000000"><b>b.&nbsp;&nbsp;<i>Western Union:</i></b> puede realizar sus pagos a través de Western Union. Por favor, envíe sus fondos a David Brugger, Dallas, Texas, USA.&nbsp; Junto con sus pagos, envíe la siguiente información a través de su perfil en nuestro sitio web </font><a href="https://www.gprocongress.org/payment." target="_blank">https://www.gprocongress.org.</a></div><div style="margin-left: 25px;"><font color="#000000"><br></font></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">i. Su nombre completo</span></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">ii. País de procedencia del envío</span></div><div style="margin-left: 75px;"><span style="color: rgb(0, 0, 0); background-color: transparent;">iii. La cantidad enviada en USD</span></div><div style="margin-left: 75px;"><span style="background-color: transparent; color: rgb(0, 0, 0);">iv. El código proporcionado por Western Union.</span></div><div>&nbsp;</div>
							<div><p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p></div>
							<div>&nbsp;</div><div>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</div><div><br></div><div>Si tiene alguna pregunta sobre cómo hacer su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</div><div>&nbsp;</div><div>Únase a nuestra oración en favor de la cantidad y la calidad de los capacitadores de pastores.</div><div><br></div><div>Saludos cordiales,</div><div>El equipo del GProCongreso II</div>';
					
					}elseif($user->language == 'fr'){
					
						$subject = 'Votre paiement GProCongress II est maintenant dû.';
						//When Paypal is active uncomment the commented line and comment the uncommented line
						// $msg = '<p><font color="#242934" face="Montserrat, sans-serif">Cher '.$name.',</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">Le paiement de votre participation au GProCongress II est maintenant dû en totalité.</span></p><p><font color="#242934" face="Montserrat, sans-serif">Vous pouvez payer vos frais sur notre site Web (https://www.gprocongress.org) en utilisant l’un des modes de paiement suivants:</font></p><p><font color="#242934" face="Montserrat, sans-serif"><b>1. Paiement en ligne :</b></font></p><p style="margin-left: 25px;"><span style="background-color: transparent; font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">a.</span><span style="background-color: transparent; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">&nbsp;</span><span style="font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;"><i>Paiement par carte de credit-</i></span><span style="background-color: transparent;"><font color="#999999"><b><i>&nbsp;</i></b></font><b style="font-style: italic; letter-spacing: inherit;">&nbsp;</b></span><font color="#242934" face="Montserrat, sans-serif" style="letter-spacing: inherit; background-color: transparent;">vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>b.</b> <b><i>Paiement par PayPal -</i></b> si vous avez un compte PayPal, vous pouvez payer vos frais via PayPal en vous rendant sur notre site Web (https://www.gprocongress.org). Veuillez envoyer vos fonds à : david@rreach.org (c’est le compte de RREACH). Dans le transfert, vous devez noter le nom du titulaire.</font></p><p><font color="#242934" face="Montserrat, sans-serif"><b>&nbsp;2. Paiement hors ligne :</b> Si vous ne pouvez pas payer en ligne, veuillez utiliser l’une des options de paiement suivantes. Après avoir effectué le paiement en mode hors ligne, veuillez enregistrer votre paiement en accédant à votre profil sur notre site Web https://www.gprocongress.org/payment.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>a. <i style="">Virement bancaire –</i></b> vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org. Vous recevrez des instructions par réponse de l’e-mail.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>b. <i>Western Union –</i> </b>vous pouvez payer vos frais via Western Union. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes en accédant à votre profil sur notre site Web https://www.gprocongress.org.</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; i. Votre nom complet</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ii. Le pays à partir duquel vous envoyez</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iii. Le montant envoyé en dollars</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iv. Le code qui vous a été donné par Western Union.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>c. <i>RIA –</i></b> vous pouvez payer vos frais par RIA. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes en accédant à votre profil sur notre site Web https://www.gprocongress.org.</font></p><p style=""><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;i. Votre nom complet</font></p><p style=""><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ii. Le pays à partir duquel vous envoyez</font></p><p style=""><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iii. Le montant envoyé en dollars</font></p><p style=""><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iv. Le code qui vous a été donné par RIA.</font></p><p><font color="#242934" face="Montserrat, sans-serif">VEUILLEZ NOTER : Afin de bénéficier de la réduction de « l’inscription anticipée », le paiement intégral doit être reçu au plus tard le 31 mai 2023.</font></p><p><font color="#242934" face="Montserrat, sans-serif">VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Si vous avez des questions concernant votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Cordialement</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">L’équipe GProCongress II</span></p>';
						$msg = '<p><font color="#242934" face="Montserrat, sans-serif">Cher '.$name.',</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">Le paiement de votre participation au GProCongress II est maintenant dû en totalité.</span></p><p><font color="#242934" face="Montserrat, sans-serif">Vous pouvez payer vos frais sur notre site Web (https://www.gprocongress.org/payment) en utilisant l’un des modes de paiement suivants:</font></p><p><font color="#242934" face="Montserrat, sans-serif"><b>1. Paiement en ligne :</b></font></p><p style="margin-left: 25px;"><span style="background-color: transparent; font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">a.</span><span style="background-color: transparent; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;">&nbsp;</span><span style="font-weight: bolder; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;"><i>Paiement par carte de credit-</i></span><span style="background-color: transparent;"><font color="#999999"><b><i>&nbsp;</i></b></font><b style="font-style: italic; letter-spacing: inherit;">&nbsp;</b></span><font color="#242934" face="Montserrat, sans-serif" style="letter-spacing: inherit; background-color: transparent;">vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</font></p><p><font color="#242934" face="Montserrat, sans-serif"><b>&nbsp;2. Paiement hors ligne :</b> Si vous ne pouvez pas payer en ligne, veuillez utiliser l’une des options de paiement suivantes. Après avoir effectué le paiement en mode hors ligne, veuillez enregistrer votre paiement en accédant à votre profil sur notre site Web https://www.gprocongress.org/payment.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>a. <i style="">Virement bancaire –</i></b> vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org. Vous recevrez des instructions par réponse de l’e-mail.</font></p><p style="margin-left: 25px;"><font color="#242934" face="Montserrat, sans-serif"><b>b. <i>Western Union –</i> </b>vous pouvez payer vos frais via Western Union. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes en accédant à votre profil sur notre site Web https://www.gprocongress.org/payment.</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; i. Votre nom complet</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ii. Le pays à partir duquel vous envoyez</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iii. Le montant envoyé en dollars</font></p><p><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iv. Le code qui vous a été donné par Western Union.</font></p>
							<p style="background-color:yellow; display: inline;"><font color="#242934" face="Montserrat, sans-serif"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></font></p>
							<p ><font color="#242934" face="Montserrat, sans-serif">VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Si vous avez des questions concernant votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Cordialement</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">L’équipe GProCongress II</span></p>';
					
					}elseif($user->language == 'pt'){
					
						$subject = 'O seu pagamento para o II CongressoGPro em aberto';
						//When Paypal is active uncomment the commented line and comment the uncommented line
						// $msg = '<p>Prezado '.$name.',</p><p>O pagamento para sua participação no II CongressoGPro está com o valor total em aberto.</p><p>Pode pagar a sua inscrição no nosso website (https://www.gprocongress.org) utilizando qualquer um dos vários métodos de pagamento:</p><p><b>1. Pagamento online:</b></p><p style="margin-left: 25px;"><b>a.&nbsp; <i>Pagamento com cartão de crédito -</i></b> pode pagar as suas taxas utilizando qualquer um dos principais cartões de crédito.</p><p style="margin-left: 25px;"><b>b.&nbsp; <i>Pagamento usando Paypal -</i></b> se tiver uma conta PayPal, pode pagar as suas taxas via PayPal, indo ao nosso site na web (https://www.gprocongress.org).&nbsp; Por favor envie o seu valor para: david@rreach.org (esta é a conta da RREACH).&nbsp; Na transferência, deve anotar o nome do inscrito.</p><p><b>2.&nbsp; Pagamento off-line:</b> Se não puder pagar on-line, por favor utilize uma das seguintes opções de pagamento. Após efetuar o pagamento através do modo offline, por favor registe o seu pagamento indo ao seu perfil no nosso website https://www.gprocongress.org.</p><p style="margin-left: 25px;"><b>a.&nbsp; <i>Transferência bancária -</i></b> pode pagar através de transferência bancária do seu banco. Se quiser fazer uma transferência bancária, envie por favor um e-mail para david@rreach.org. Receberá instruções através de e-mail de resposta.</p><p style="margin-left: 25px;"><b>b.&nbsp; <i>Western Union -&nbsp;</i> </b>pode pagar as suas taxas através da Western Union. Por favor envie os seus fundos para David Brugger, Dallas, Texas, EUA.&nbsp; Juntamente com os seus fundos, envie por favor as seguintes informações, indo ao seu perfil no nosso website https://www.gprocongress.org.</p><p style="margin-left: 50px;">I. O seu nome completo</p><p style="margin-left: 50px;">ii. O país de onde vai enviar</p><p style="margin-left: 50px;">iii. O montante enviado em USD</p><p style="margin-left: 50px;">iv. O código que lhe foi dado pela Western Union.</p><p style="margin-left: 25px;"><b>c.&nbsp; <i>RIA -</i></b> pode pagar a sua taxa através do RIA. Por favor envie o seu valor para David Brugger, Dallas, Texas, EUA.&nbsp; Juntamente com o seu valor, envie por favor as seguintes informações, indo ao seu perfil no nosso website https://www.gprocongress.org/payment.</p><p style="margin-left: 50px;">&nbsp;i. O seu nome completo</p><p style="margin-left: 50px;">&nbsp;ii. O país de onde vai enviar</p><p style="margin-left: 50px;">&nbsp;iii. O valor enviado em USD</p><p style="margin-left: 50px;">&nbsp;iv. O código que lhe foi dado por RIA.</p><p>POR FAVOR NOTE: A fim de poder beneficiar do desconto de "adiantamento", o pagamento integral deve ser recebido até 31 de Maio de 2023.</p><p>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor&nbsp; previamente pagos por si serão retidos.</p><p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p><p>Ore conosco no sentido de multiplicar a quantidade e qualidade dos formadores de pastores.</p><p>Com muito carinho,</p><p>Equipe do II CongressoGPro</p>';
						$msg = '<p>Prezado '.$name.',</p><p>O pagamento para sua participação no II CongressoGPro está com o valor total em aberto.</p><p>Pode pagar a sua inscrição no nosso website (https://www.gprocongress.org/payment) utilizando qualquer um dos vários métodos de pagamento:</p><p><b>1. Pagamento online:</b></p><p style="margin-left: 25px;"><b>a.&nbsp; <i>Pagamento com cartão de crédito -</i></b> pode pagar as suas taxas utilizando qualquer um dos principais cartões de crédito.</p><p><b>2.&nbsp; Pagamento off-line:</b> Se não puder pagar on-line, por favor utilize uma das seguintes opções de pagamento. Após efetuar o pagamento através do modo offline, por favor registe o seu pagamento indo ao seu perfil no nosso website https://www.gprocongress.org/payment.</p><p style="margin-left: 25px;"><b>a.&nbsp; <i>Transferência bancária -</i></b> pode pagar através de transferência bancária do seu banco. Se quiser fazer uma transferência bancária, envie por favor um e-mail para david@rreach.org. Receberá instruções através de e-mail de resposta.</p><p style="margin-left: 25px;"><b>b.&nbsp; <i>Western Union -&nbsp;</i> </b>pode pagar as suas taxas através da Western Union. Por favor envie os seus fundos para David Brugger, Dallas, Texas, EUA.&nbsp; Juntamente com os seus fundos, envie por favor as seguintes informações, indo ao seu perfil no nosso website https://www.gprocongress.org/payment.</p><p style="margin-left: 50px;">I. O seu nome completo</p><p style="margin-left: 50px;">ii. O país de onde vai enviar</p><p style="margin-left: 50px;">iii. O montante enviado em USD</p><p style="margin-left: 50px;">iv. O código que lhe foi dado pela Western Union.</p>
							<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
							<p>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor&nbsp; previamente pagos por si serão retidos.</p><p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p><p>Ore conosco no sentido de multiplicar a quantidade e qualidade dos formadores de pastores.</p><p>Com muito carinho,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Your GProCongress II payment is now due.';
						//When Paypal is active uncomment the commented line and comment the uncommented line
						// $msg = '<p><font color="#242934" face="Montserrat, sans-serif">Dear '.$name.',</font></p><p><font color="#242934" face="Montserrat, sans-serif">Payment for your attendance at GProCongress II is now due in full.</font></p><p><font color="#242934" face="Montserrat, sans-serif">You may pay your fees on our website (https://www.gprocongress.org/payment) using any of several payment methods:</font></p><p><font color="#242934" face="Montserrat, sans-serif">1. <span style="white-space:pre">	</span><b>Online Payment:</b></font></p><p style="margin-left: 50px;"><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">a. </span><span style="font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent; white-space: pre;">	</span><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;"><b><i style="">Payment using credit card</i> –</b> you can pay your fees using any major credit card.</span></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">b. <span style="white-space:pre">	</span>Payment using Paypal - if you have a PayPal account, you can pay your fees via PayPal by going to our website&nbsp; &nbsp; (https://www.gprocongress.org/payment).&nbsp; Please send your funds to: david@rreach.org (this is RREACH’s account).&nbsp;</font><span style="color: rgb(153, 153, 153); letter-spacing: inherit; background-color: transparent;">In</span><span style="letter-spacing: inherit; background-color: transparent; color: rgb(36, 41, 52); font-family: Montserrat, sans-serif;"> the transfer it should note the name of the registrant.</span></p><p><font color="#242934" face="Montserrat, sans-serif">2. <b>Offline Payment:</b> If you cannot pay online, then please use one of the following payment options. After making the payment via offline mode, please register your payment by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">a.&nbsp; &nbsp; <b><i>Bank transfer –</i></b> you can pay via wire transfer from your bank. If you want to make a wire transfer, please emai david@rreach.org. You will receive instructions via reply email.&nbsp;</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">b.&nbsp; &nbsp; <b><i>Western Union –</i></b> you can pay your fees via Western Union. Please send your funds to David Brugger, Dallas Texas, USA.&nbsp; Along with your funds, please submit the following information by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;i.&nbsp; Your full name</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ii.&nbsp;&nbsp;The country you are sending from</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iii.&nbsp; The amount sent in USD</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iv.&nbsp; The code given to you by Western Union.</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">c. <span style="white-space:pre">	</span><b><i>RIA –</i> </b>you can pay your fees via RIA. Please send your funds to David Brugger, Dallas, Texas,&nbsp; USA.&nbsp; Along with your funds, please submit the following information by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp;</font><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">&nbsp;</span><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">&nbsp; &nbsp; &nbsp; &nbsp; i.&nbsp; &nbsp;&nbsp;</span><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">Your full name</span></p><p style="letter-spacing: 0.5px; margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ii.&nbsp; &nbsp;</font><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">The country you are sending from</span></p><p style="letter-spacing: 0.5px; margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iii.&nbsp;&nbsp;</font><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">The amount sent in USD</span></p><p style="letter-spacing: 0.5px; margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;iv.&nbsp;&nbsp;</font><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; background-color: transparent;">The code given to you by RIA.</span></p><p><font color="#242934" face="Montserrat, sans-serif">PLEASE NOTE: In order to qualify for the “early bird” discount, full payment must be received on or before May 31, 2023</font></p><p><font color="#242934" face="Montserrat, sans-serif">PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</font></p><p><font color="#242934" face="Montserrat, sans-serif">If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Pray with us toward multiplying the quantity and quality of pastor-trainers.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Warmly,</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">GProCongress II Team</span></p><div><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; font-weight: 600;"><br></span></div>';
						$msg = '<p><font color="#242934" face="Montserrat, sans-serif">Dear '.$name.',</font></p><p><font color="#242934" face="Montserrat, sans-serif">Payment for your attendance at GProCongress II is now due in full.</font></p><p><font color="#242934" face="Montserrat, sans-serif">You may pay your fees on our website (https://www.gprocongress.org/payment) using any of several payment methods:</font></p><p><font color="#242934" face="Montserrat, sans-serif">1. <span style="white-space:pre">	</span><b>Online Payment:</b></font></p><p style="margin-left: 50px;"><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">a. </span><span style="font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent; white-space: pre;">	</span><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;"><b><i style="">Payment using credit card</i> –</b> you can pay your fees using any major credit card.</span></p><p><font color="#242934" face="Montserrat, sans-serif">2. <b>Offline Payment:</b> If you cannot pay online, then please use one of the following payment options. After making the payment via offline mode, please register your payment by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">a.&nbsp; &nbsp; <b><i>Bank transfer –</i></b> you can pay via wire transfer from your bank. If you want to make a wire transfer, please emai david@rreach.org. You will receive instructions via reply email.&nbsp;</font></p><p style="margin-left: 50px;"><font color="#242934" face="Montserrat, sans-serif">b.&nbsp; &nbsp; <b><i>Western Union –</i></b> you can pay your fees via Western Union. Please send your funds to David Brugger, Dallas Texas, USA.&nbsp; Along with your funds, please submit the following information by going to your profile in our website https://www.gprocongress.org/payment.</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;i.&nbsp; Your full name</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ii.&nbsp;&nbsp;The country you are sending from</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iii.&nbsp; The amount sent in USD</font></p><p style="margin-left: 75px;"><font color="#242934" face="Montserrat, sans-serif">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; iv.&nbsp; The code given to you by Western Union.</font></p>
								<p><font color="#242934" face="Montserrat, sans-serif"><p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p></font></p>
								<p><font color="#242934" face="Montserrat, sans-serif">PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</font></p><p><font color="#242934" face="Montserrat, sans-serif">If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Pray with us toward multiplying the quantity and quality of pastor-trainers.</font></p><p><font color="#242934" face="Montserrat, sans-serif">Warmly,</font></p><p><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; letter-spacing: inherit; background-color: transparent;">GProCongress II Team</span></p><div><span style="color: rgb(36, 41, 52); font-family: Montserrat, sans-serif; font-weight: 600;"><br></span></div>';
					
					}

					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your GProCongress II payment is now due.');
				
					
				}
				
				return response(array('message'=>' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function paypalWebhookResponse(Request $request){ 

		
		$payload = file_get_contents('php://input');

		$event = json_decode($payload, true);
		
		$console=new \App\Models\PaymentConsole();

		$console->value=$payload;
		$console->order_id=$event['resource']['transactions'][0]['description'];

		$console->save();

		try{
			
			if (isset($event)) {

				if ($event['event_type'] == 'PAYMENTS.PAYMENT.CREATED') {

					if(isset($event['resource']['transactions'][0]['description'])){

						$transaction=\App\Models\Transaction::where('order_id',$event['resource']['transactions'][0]['description'])->first();
			
						if($transaction){

							$transaction->razorpay_order_id=$event['id'];
							$transaction->razorpay_paymentid=$event['resource']['id'];
							$transaction->card_id=$event['resource']['cart'];
							$transaction->bank_transaction_id=$event['resource']['id'];
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

								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

								$subject='User submit travel information reminder';
								$msg='User submit  travel information reminder';
								\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'User submit travel information reminder');
							

							}

						}
						
					}
					
				}else{

					$paymentIntent = $event['resource']['transactions'][0]['description']; 
					
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
				}
			}

		}catch (\Exception $e){
						
			$console=new \App\Models\PaymentConsole();

			$console->value=$e->getMessage();
	
			$console->save();
		
		}
		echo 'done';
	}

	public function userEmailUpdateData(Request $request){
		
		try {
			
			$emailData = [
				'henrylitala24@gmail.com'=>'250.00',
				'abraham_getachew@yahoo.com'=>'1500.00',
				'suneera@msn.com'=>'1500.00',
				'german@gprocommission.org'=>'775.00',
				'wanyamajoe@yahoo.com'=>'250.00',
				'hakizajean1982@gmail.com'=>'250.00',
				'benjaminnkusi@gmail.com'=>'650.00',
				'ecmesiah@yahoo.fr'=>'250.00',
				'akhillare399@gmail.com'=>'250.00',
				'larry@leadershipvistas.org'=>'1375.00',
				'napohaggai@gmail.com'=>'375.00',
				'jjprovider@gmail.com'=>'250.00',
				'josueestrada243@gmail.com'=>'250.00',
				'jfmadeira@yahoo.com.br'=>'250.00',
				'sorchansim@yahoo.com'=>'650.00',
				'mukeshvkumar@gmail.com'=>'250.00',
				'dawdenkabo6@gmail.com'=>'250.00',
				'pradeep4js@gmail.com'=>'250.00',
				'georgeoncall@gmail.com'=>'250.00',
				'dburke@christcollege.edu.au'=>'1375.00',
				'juancito538@hotmail.com'=>'375.00',
				'manandaye@yahoo.fr'=>'250.00',
				'bickson18@gmail.com'=>'650.00',
				'praveen@gcin.in'=>'250.00',
				'ajitmose@hotmail.com'=>'250.00',
				'samson.jlife@gmail.com'=>'650.00',
				'gamolik@yahoo.fr'=>'975.00',
				'presleydimina13@gmail.com'=>'650.00',
				'calistusosuru220@gmail.com'=>'250.00',
				'oikosindia2050@gmail.com'=>'250.00',
				'potchurch@gmail.com'=>'250.00',
				'rayneu@spoken.org'=>'975.00',
				'jccwaruigeorge@gmail.com'=>'250.00',
				'vengoor@gmail.com'=>'250.00',
				'dieudo.irambona@gmail.com'=>'250.00',
				'julianakavitha@gmail.com'=>'250.00',
				'paul@re-forma.global'=>'375.00',
				'reubenvanrensburg1@gmail.com'=>'375.00',
				'manfred@overseas.org'=>'1375.00',
				'ministerjohnson@ymail.com'=>'1375.00',
				'balibangakatambu@gmail.com'=>'250.00',
				'elishainfo@gmail.com'=>'250.00',
				'titusketer@gmail.com'=>'250.00',
				'mathewmaiyo@yahoo.com'=>'650.00',
				'hounkpecodjogeorges@gmail.com'=>'650.00',
				'agadifred@gmail.com'=>'250.00',
				'rudenulpe@gmail.com'=>'250.00',
				'kiokomwangangi@gmail.com'=>'975.00',
				'pmbandi@yahoo.com'=>'250.00',
				'shadracksm.sm@gmail.com'=>'250.00',
				'musaudaniel724@gmail.com'=>'650.00',
				'mbuvi4@yahoo.com'=>'650.00',
				'dawpeterje@gmail.com'=>'250.00',
				'nchassim@yahoo.com'=>'250.00',
				'sammycarino74@gmail.com'=>'250.00',
				'kishorebendukuri@gmail.com'=>'250.00',
				'kounmahervedossa@gmail.com'=>'250.00',
				'pastordanish@gmail.com'=>'250.00',
				'delvinforde@yahoo.com'=>'775.00',
				'ricardoivan79@gmail.com'=>'250.00',
				'jenson_jj2021@outlook.com'=>'775.00',
				'bothniella@gmail.com'=>'250.00',
				'nathanaelmatary@gmail.com'=>'250.00',
				'versecr@gmail.com'=>'975.00',
				'hiheko31@gmail.com'=>'650.00',
				'kcj1980@hotmail.com'=>'250.00',
				'amanyudza@gmail.com'=>'650.00',
				'jndiformumbe@gmail.com'=>'250.00',
				'anishag4j@gmail.com'=>'650.00',
				'kadek.badeng@gmail.com'=>'650.00',
				'mhdumitrascu@gmail.com'=>'975.00',
				'derejegete@gmail.com'=>'650.00',
				'nnaqash97@yahoo.com'=>'250.00',
				'haroonmassey@gmail.com'=>'250.00',
				'alenbala50@yahoo.com'=>'250.00',
				'masih_hidayat@yahoo.com'=>'250.00',
				'samuelwilliam.pak@gmail.com'=>'250.00',
				'wohu3@yahoo.com'=>'775.00',
				'nkolofanga@fateb.net'=>'650.00',
				'maniesron@gmail.com'=>'975.00',
				'jacquesbadjam@gmail.com'=>'650.00',
				'kanjojuliusnformi@gmail.com'=>'250.00',
				'esauds@yahoo.com'=>'375.00',
				'jay2xbarza@gmail.com'=>'650.00',
				'zifusjames@hotmail.com'=>'375.00',
				'mmaguero@gmail.com'=>'575.00',
				'romainkomia@gmail.com'=>'250.00',
				'mreano2@gmail.com'=>'775.00',
				'gnanaprakashmula@gmail.com'=>'975.00',
				'ericroberts888@yahoo.com'=>'250.00',
				'pixandkay@yahoo.com'=>'250.00',
				'augustinedee@gmail.com'=>'250.00',
				'albertokinyash@gmail.com'=>'650.00',
				'monenjd@gmail.com'=>'250.00',
				'emmanuelchess@gmail.com'=>'650.00',
				'pclaverhayo@yahoo.fr'=>'250.00',
				'cbtspresident@gmail.com'=>'250.00',
				'ao74yankee@gmail.com'=>'250.00',
				'nbbc13@gmail.com'=>'250.00',
				'meyuba@gmail.com'=>'250.00',
				'Comidosh@gmail.com'=>'250.00',
				'kwamesika2012@gmail.com'=>'250.00',
				'chogslg@gmail.com'=>'250.00',
				'jachinvictor@yahoo.com'=>'250.00',
				'bantsimba.anath@gmail.com'=>'975.00',
				'rencombatz@yahoo.com'=>'250.00',
				'venuforchrist@gmail.com'=>'250.00',
				'tanigolden54@gmail.com'=>'250.00',
				'musilicapetown@gmail.com'=>'250.00',
				'aadgministry@gmail.com'=>'250.00',
				'tbbaisanrafaeloriente@gmail.com'=>'375.00',
				'Jyzkwilliams@yahoo.com'=>'250.00',
				'dominionkollie1438@gmail.com'=>'975.00',
				'kaylebdee@gmail.com'=>'975.00',
				'eburklin@chinapartner.org'=>'1375.00',
				'atuhangirwea@gmail.com'=>'975.00',
				'edkirm@gmail.com'=>'975.00',
				'rajancharles@gmail.com'=>'1375.00',
				'josuehugofernandez@gmail.com'=>'975.00',
				'simiyucheri@gmail.com'=>'975.00',

			];

			if(count($emailData) > 0){

				foreach ($emailData as $key => $val) {
				
					$results = \App\Models\User::where('email',$key)->first();

					if($results){
						$results->amount = $val;
						$results->early_bird ='Yes';
						$results->save();
					}
					
					
				}
				
				return response(array('message'=>' update success.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function userUpdatePaymentCountry(Request $request){
		
		try {
			
			$users = \App\Models\User::where('payment_country','0')->get();

			if(count($users) > 0){

				foreach ($users as $key => $user) {
				
					$results = \App\Models\User::where('id',$user->id)->first();
					
					if($results){

						$results->payment_country = $results->citizenship;
						$results->save();

					}
					
					
				}
				
				return response(array('message'=>' update success.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function setdateandSpouseReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where('spouse_confirm_token','!=','')->where('spouse_confirm_token','!=',null)->get();

			if(count($results) > 0){

				foreach ($results as $key => $existSpouse) {
				
					$userData = \App\Models\User::where('id',$existSpouse->id)->first();
					if($userData){

						$reminderData = [
							'type'=>'spouse_reminder',
							'date'=>date('Y-m-d'),
							'reminder'=>'0',

						];
						
						$userData->spouse_confirm_reminder_email = json_encode($reminderData);
						$userData->save();
					}

						
				}

				$results = \App\Models\User::orderBy('id','desc')->get();

				foreach ($results as $key => $existSpouse) {
				
					$userData = \App\Models\User::where('id',$existSpouse->id)->first();
					if($userData){

						$userData->status_change_at = date('Y-m-d H:i:s');
						$userData->save();
					}
	
				}
				
				return response(array('message'=>'Data update successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

    public function AppologiesLetter(Request $request){

        $emails = array('jjustinenos@gmail.com',
							'asodestrom@newinternational.org',
							'arpita.j.singh@gmail.com',
							'zinashabt@yahoo.com',
							'dinukallimel@gmail.com',
							'kiranisha.das@gmail.com',
							'pastorsamba@gmail.com',
							'regesa@kcca.go.ug',
							'carlospazt065@gmail.com',
							'artist_ph@yahoo.com',
							'abelayneh@gmail.com',
							'f.rtstml@gmail.com',
							'bishopisrael.madueke@gmail.com',
							'mvsj01@yahoo.com',
							'johnlindabaryogar@gmail.com',
							'abrahampradeepkumar@yahoo.com',
							'sudiptananda2012@gmail.com',
							'philipsahawneh@gmail.com',
							'merlinsam02@gmail.com',
							'angelatony83@gmail.com',
							'enjenga2015@gmail.com',
							'beskatanga@gmail.com',
							'eliomartiz@gmail.com',
							'fssempijja@ymail.com',
							'grace.kajuna@gmail.com',
							'vinet4jsm@gmail.com',
							'lwmindia2011@gmail.com',
							'Amit.mondal@live.com',
							'paul39ldh@gmail.com',
							'pcmondolo@yahoo.com',
							'cremeygreene@gmail.com',
							'rubywesley@gmail.com',
							'pascadet9@gmail.com',
							'shellyrestan@gmail.com',
							'modic12@hotmail.com',
							'josemadeira078@gmail.com',
							'Laurie.goree@gmail.com',
							'doyen@hepacademy.com',
							'kevinpapasmart@gmail.com',
							'feliciafodorean@gmail.com',
							'tseggy@gmail.com',
							'cmagda@outlook.fr',
							'kavitaisrael@gmail.com',
							'gnanchouchiepoacsa@gmail.com',
							'calvaryenglishpastor@gmail.com',
							'geethajoel0107@gmail.com',
							'd4greatness12@gmail.com',
							'courtneymk99@yahoo.com',
							'tvs.student@gmail.com',
							'pmunderu@gmail.com',
							'jamesiccc@yahoo.com',
							'afeya.jeff@gmail.com',
							'joseanvaya@gmail.com',
							'urvashi.olm@gmail.com');

	
		// $emails = array('gopalsaini.img@gmail.com');
			foreach ($emails as $email) {
			
				$user = \App\Models\User::where('email',$email)->first();
				if($user){

					$name = $user->salutation.' '.$user->name.' '.$user->last_name;
					
					if($user->language == 'sp'){

						$faq = '<a href="'.url('profile').'">aqui</a>';

						$subject = "Nuestras Disculpas ";
						$msg = '<p>Estimado '.$name.',&nbsp;</p><p><br></p><p><br></p><p>Recientemente usted recibió un correo electrónico de nuestra parte con el asunto "Confirme el estado de su inscripción” on.  Ese correo electrónico fue enviado por equivocación.  Acepte nuestras disculpas por el error y las molestias que le haya podido causar.</p><p><br></p><p>Si tiene alguna pregunta sobre el estado de su inscripción, responda a este correo electrónico y uno de los miembros de nuestro equipo se pondrá en contacto con usted en pronto.  De lo contrario, continúe con el proceso de inscripción haciendo click en :'.$faq.'.</p><p>¡Esperamos con gran anticipación verlos en Panamá este noviembre!</p><p>Calurosamente,</p><p><br></p><p>El Equipo GProCongress II</p>';
					
					}elseif($user->language == 'pt'){

						$faq = '<a href="'.url('profile').'">link</a>';

						$subject = "Nossas Desculpas";
						$msg = '<p>Caro '.$name.',</p><p><br></p><p>Recentemente, você recebeu um e-mail nosso com o assunto "Confirme seu status de inscrição". Esse e-mail foi enviado por engano. Por favor, aceite nossas desculpas pelo erro e por qualquer inconveniente que isso possa ter causado.</p><p><br></p><p>Se você tiver alguma dúvida sobre o status do seu inscrição, responda a este e-mail e um dos membros de nossa equipe entrará em contato com você em breve. Caso contrário, continue o processo de inscrição acessando '.$faq.'.</p><p>Estamos ansiosos para vê-lo no Panamá em novembro!!</p><p><br></p><p>Calorosamente,</p><p>Equipe GProCongress II</p>';
					
					}elseif($user->language == 'fr'){
					
						$faq = '<a href="'.url('profile').'">lien</a>';

						$subject = "Nos Excuses";
						$msg = '<p>Cher/Chère '.$name.',</p><p><br></p><p><br></p><p>Vous avez récemment reçu un e-mail de notre part avec pour sujet "Veuillez confirmer votre statut d` enregistrement". Ce courriel a été envoyé par erreur. Veuillez accepter nos excuses pour l` erreur et pour tout inconvénient qu` elle aurait pu vous causer. </p><p><br></p><p>Si vous avez des questions sur le statut de votre inscription, veuillez répondre à cet e-mail et l`un des membres de notre équipe vous contactera sous peu. Sinon, veuillez continuer le processus d`inscription en allant sur '.$faq.'. </p><p>Nous attendons avec impatience de vous voir au Panama en novembre !!</p><p><br></p><p></p><p>Cordialement</p><p>L` équipe du GProCongress II</p>';
					
					}else{
					
						$faq = '<a href="'.url('profile').'">Click here</a>';

						$subject = "Our Apologies";
						$msg = '<p>Dear '.$name.',</p><p><br></p><p>You recently received an email from us with the subject, "Please confirm your registration status."  That email was sent out by mistake. Please accept our apologies for the mistake and for any inconvenience It may have caused you. </p><p><br></p><p>If you have any questions about your registration status, please reply to this email, and one of our team members will be in touch with you shortly. Otherwise, please continue the registration process by going to '.$faq.'.</p><p>We look forward with great anticipation to seeing you in Panama this November!!</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p><div><br></div>';
					
					}
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Our Apologies');

				}
				
			}
				
			return response(array('message'=>'Reminders has been sent successfully. All Emails : '.print_r($emails)), 200);


    }
	
	public function SendEmailsToAppsAvailable(Request $request){
			
			$users = \App\Models\User::where('stage','>=','2')->where('profile_status','Approved')->get();
			
			if(!empty($users) && count($users)>0){
				
				foreach ($users as $val) {
					
					$user = \App\Models\User::where('id',$val->id)->first();
					
					if($user){
	
						$name = $user->salutation.' '.$user->name.' '.$user->last_name;
						$android = '<a href="https://play.google.com/store/apps/details?id=org.gprocommision">Google Play</a>';
						$ios = '<a href="https://apps.apple.com/us/app/gpro-commission/id1664828059">App Store</a>';
	
						if($user->language == 'sp'){
	
							$subject = "¡Manténgase al día de todo lo que ocurre en el GProCongress!";
							$msg = '<p>Estimado '.$name.',&nbsp;</p><p><br></p><p><br></p>
									<p>Descargue hoy nuestra nueva app - es la app oficial del Segundo Congreso de Proclamación Global para Capacitadores de Pastores (GProCongress II), que se celebrará (Dios mediante) en Ciudad de Panamá, Panamá del 12 al 17 de noviembre de 2023. Con la app de GProCommission, podrá completar el proceso de inscripción al Congreso, efectuar los pagos correspondientes, interactuar antes del evento, y eso es sólo el comienzo. Pronto habrá más funcionalidades: temas, encuestas, chats, ¡y mucho más!</p><p><br></p>
									<p>Puede encontrar la aplicación GProCommission en la '.$ios.', o en '.$android.'. ¡Consíguala hoy!</p>
									<p>Calurosamente,</p><p><br></p><p>El Equipo GProCongress II</p>';
						
						}elseif($user->language == 'pt'){
	
							$subject = "Fique por dentro de tudo o que está acontecendo sobre o CongressoGPro!";
							$msg = '<p>Caro '.$name.',</p><p><br></p>
									<p>Baixe hoje o nosso novo aplicativo - é o aplicativo oficial do 2º Congresso de Proclamação Global para Formadores de Pastores (II CongressoGPro), que acontecerá (se Deus quiser) na Cidade do Panamá, Panamá, de 12 a 17 de novembro de 2023. Com o aplicativo GProCommission, você poderá completar o processo de inscrição para o Congresso, pagar a sua taxa de inscrição para o Congresso, interagir antes do evento, e isso é apenas o início. Uma maior funcionalidade está para funcionar em  breve: tópicos, sondagens, chats, e muito mais!</p><p><br></p>
									<p>Pode encontrar o aplicativo GProCommission na '.$ios.', ou no '.$android.'. Adquira-o hoje!</p><p><br></p>
									<p>Calorosamente,</p><p>Equipe GProCongress II</p>';
						
						}elseif($user->language == 'fr'){
						
							$subject = "Restez informés de tout ce qui se passe avec le GProCongrès !";
							$msg = "<p>Cher/Chère '.$name.',</p><p><br></p><p><br></p>
									<p>Téléchargez notre nouvelle application dès aujourd'hui c'est l'application officielle du 2e Congrès mondial de  la Proclamation pour les formateurs de pasteurs (GProCongress II),qui se tiendra (si le Seigneur le veut) dans la ville de Panama, au Panama, du 12 au 17 novembre 2023. Avec l'application GProCommission, vous pouvez compléter le processus d'inscription au Congrès, payer vos frais pour le Congrès, interagir avant l'événement, et ce n'est que le début. De plus amples fonctionnalités  seront bientôt disponibles : sujets, sondages, chats, et bien plus encore ! </p><p><br></p>
									<p>Vous pouvez trouver l'application GProCommission dans l' ".$ios.", ou sur  ".$android.". Obtenez-la dès aujourd'hui !</p><p><br></p><p></p>
									<p>Cordialement</p><p>L` équipe du GProCongress II</p>";
							
						}else{
						
							$subject = "Stay up to date on everything happening with the GProCongress!";
							$msg = '<p>Dear '.$name.',</p><p><br></p>
									<p>Download our new app today – it’s the official app of the 2nd Global Proclamation Congress for Trainers of Pastors (GProCongress II), to be held (Lord willing) in Panama City, Panama on November 12-17, 2023. With the GProCommission app, you can complete the registration process for the Congress, pay your fees for the Congress, interact before the event, and that’s just the beginning. Greater functionality is coming soon: topics, polls, chats, and much more!</p><p><br></p>
									<p>You can find the GProCommission app in the  '.$ios.', or on  '.$android.'. Get it today!!</p><p><br></p>
									<p>Warmly,</p><p>&nbsp;The GProCongress II Team</p><div><br></div>';
						
						}
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
	
						\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Stay up to date on everything happening with the GProCongress!');
	
					}
					
				}
				return response(array('message'=>'Reminders has been sent successfully. All Emails'), 200);	
				
			}
			


    }

	public function sendEarlyBirdReminderNewEmail(Request $request){
		
		try {

			$results = \App\Models\User::where([['stage', '=', '2'], ['amount', '>', 0], ['early_bird', '=', 'Yes']])->get();
			
			if(count($results) > 0){
				$emails= [];$name = '';
				foreach ($results as $key => $user) {
					
					if(\App\Helpers\commonHelper::getTotalPendingAmount($user->id) > 0) {

						$emails[]= $user->email;
						$name = $user->salutation.' '.$user->name.' '.$user->last_name;
                    
                    
						if($user->language == 'sp'){
							
							$url = '<a href="'.url('payment').'" target="_blank">enlace</a>';
							$subject = 'RECORDATORIO: ¡El descuento por inscripción anticipada está por vencer pronto MAYO 31, 2023! ';
							$msg = '<p>Estimado '.$name.',</p>
							<p></p>
							<p>El pago de su asistencia al GProCongress II vence ahora. Vaya a '.$url.' y realice su pago en este momento.</p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b><i>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de mayo de 2023, perderá su descuento por "inscripción anticipada" y tendrá que pagar 100 dólares adicionales para asistir al Congreso.</i></b></p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b>TAMBIÉN TOME NOTA: ¡Si no paga antes del 31 de agosto de 2023, el costo aumentará mucho más! </b></p>
							<p></p>
							<p>Si tiene alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico. </p>
							<p></p>
							<p><i>Ore con nosotros para multiplicar la cantidad y calidad de capacitadores de pastores. </i></p>
							<p><br></p>
							<p>Cordialmente,</p>
							<p>&nbsp;Equipo GProCongress II</p><div><br></div>';


						}elseif($user->language == 'fr'){
						
							$url = '<a href="'.url('payment').'" target="_blank">lien</a>';
							$subject = 'Rappel- Votre rabais de “ l’inscription anticipée ” expire bientôt   31 MAI 2023! ';
							$msg = '<p>Cher '.$name.',</p>
							<p></p>
							<p>Le paiement de votre participation au GProCongress II est maintenant dû.  Prière d’aller sur '.$url.', et effectuer votre paiement maintenant. </p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b><i>VEUILLEZ NOTER: Si le paiement total n’est pas reçu avant le 31 mai 2023, vous perdrez votre rabais « inscription anticipée » et vous devrez payer 100 $ de plus pour participer au Congrès. </i></b></p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b>NOTEZ AUSSI: Si vous ne payez pas avant le 31 août 2023, votre coût augmentera beaucoup plus!  </b></p>
							<p></p>
							<p>Si vous avez des questions sur votre paiement, ou si vous souhaitez parler à l’un des membres de notre équipe, répondez simplement à cet e-mail. </p>
							<p></p>
							<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs. </i></p>
							<p><br></p>
							<p>Cordialement,</p>
							<p>&nbsp;L’équipe GProCongress II</p><div><br></div>';

						}elseif($user->language == 'pt'){
						
							$url = '<a href="'.url('payment').'" target="_blank">link</a>';
							$subject = 'LEMBRETE - O seu desconto " antecipado " está a expirar em breve  31 de maio de 2023! ';
							$msg = '<p>Caro  '.$name.',</p>
							<p></p>
							<p>O pagamento da sua participação no GProCongress II está a vencer.  Por favor, vá ao '.$url.', e efetue o seu pagamento agora.</p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b><i>POR FAVOR NOTE: Se o pagamento total não for recebido até 31 de Maio de 2023, perderá o seu desconto "antecipado" e terá de pagar mais 100 dólares para participar no Congresso. </i></b></p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b>NOTA TAMBÉM: Se não pagar até 31 de Agosto de 2023, o seu custo aumentará muito mais! </b></p>
							<p></p>
							<p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipa, basta responder a este e-mail.</p>
							<p></p>
							<p><i>Ore conosco para multiplicar a quantidade e a qualidade dos pastores-formadores. </i></p>
							<p><br></p>
							<p>Cordialmente,</p>
							<p>&nbsp;Equipe do GProCongress II.</p><div><br></div>';

						}else{
						
						    $url = '<a href="'.url('payment').'" target="_blank">link</a>';

							$subject = 'REMINDER – Your “EARLY BIRD” discount is expiring on MAY 31, 2023!';
							$msg = '<p>Dear '.$name.',</p>
							<p></p>
							<p>Payment for your attendance at GProCongress II is now due.  Please go to '.$url.', and make your payment now.</p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b><i>PLEASE NOTE: If full payment is not received by May 31, 2023, you will lose your “early bird” discount, and you will have to pay an additional $100 to attend the Congress. </i></b></p>
							<p></p>
							<p style="background-color:yellow; display: inline;"><b>ALSO NOTE: If you don’t pay by August 31, 2023, your cost will go up a lot more! </b></p>
							<p></p>
							<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email. </p>
							<p></p>
							<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p>
							<p><br></p>
							<p>Warmly,</p>
							<p>&nbsp;The GProCongress II Team</p><div><br></div>';
						
						}

						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

						\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'REMINDER – Your “<b>EARLY BIRD</b>” discount is expiring on <b>MAY 31, 2023!</b>');
					}
					
				}

				echo "<pre>";
				print_r($emails); 
				
				return response(array('message'=>' Reminders has been sent successfully.All Emails'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function speakerList(Request $request){
		
		try{

			$speakerResult=\App\Models\Speaker::where([['status','=','1']])->orderBy('id','ASC')->get();
		
			if($speakerResult->count()==0){

				return response(array("error"=>true,"message" => 'Speaker Not found.'),200);

			}else{
				
				$result=[];
				
				foreach($speakerResult as $speaker){
					
					$result[]=[
						'image'=>asset('/uploads/speaker/'.$speaker->image),
						'name'=>$speaker->name,
						'description'=>$speaker->description,
					];
				}
				return response(array("error"=>false,"message" => 'Speaker fetched successfully.','result'=>$result),200); 
			}
		
		}catch (\Exception $e){
			
			return response(array("error"=>true,"message" => $e->getMessage()),403); 
		
		} 
		
	}	
	
	public function PreRecordedVideoList(Request $request){
		
		try{

			$preRecordedVideoResult=\App\Models\PreRecordedVideo::where([['status','=','1']])->orderBy('id','ASC')->get();
		
			if($preRecordedVideoResult->count()==0){

				return response(array("error"=>true,"message" => 'PreRecordedVideo Not found.'),200);

			}else{
				
				$result=[];
				
				foreach($preRecordedVideoResult as $preRecordedVideoVal){
					
					$result[]=[
						'video'=>asset('/uploads/pre-recorded-video/'.$preRecordedVideoVal->video),
						'name'=>$preRecordedVideoVal->name,
					];
				}
				return response(array("error"=>false,"message" => 'PreRecordedVideo fetched successfully.','result'=>$result),200); 
			}
		
		}catch (\Exception $e){
			
			return response(array("error"=>true,"message" => $e->getMessage()),403); 
		
		} 
		
	}

	public function exhibitorsRegistration(Request $request){
	
		$rules['name']='required';
		$rules['email']='required';
		$rules['mobile']='required';
		$rules['category']='required';
		
		// $messages = array(
		// 	'is_group.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'is_group_required'),
		// 	'user_whatsup_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'user_whatsup_code_required'),
		// 	'contact_whatsapp_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_whatsapp_number_required'), 
		// 	'user_mobile_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'user_mobile_code_required'), 
		// 	'contact_business_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_business_number_required'), 
		// 	'contact_business_number.unique' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_business_number_unique'),  
			
		// );

		$validator = \Validator::make($request->json()->all(), $rules);
		 
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
					
				//check email address
				$tempArr = array_unique(array_column($request->json()->get('group_list'), 'email'));
				$uniqueGroupUsers=array_intersect_key($request->json()->get('group_list'), $tempArr);

				if(count($uniqueGroupUsers) != count($request->json()->get('group_list'))){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Wehave-duplicate-email-group-users');
					return response(array( "message"=>$message), 403);
				
				}else{

					$groupEmails = $names = array_column($request->json()->get('group_list'), 'email'); 
					$checkExistUsers=\App\Models\User::whereIn('email',$groupEmails)->get();

					if($checkExistUsers->count()>0){

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Wehave-duplicate-email-group-users');
						return response(array("message"=>$checkExistUsers[0]['email'].$message), 403);

					}
				}

				$users=[];

				foreach($request->json()->get('group_list') as $group){

					$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

					$password = substr(str_shuffle($chars),0,8);

					$users[]=array(
						'name'=>$group['name'],
						'email'=>$group['email'],
						'mobile'=>$group['mobile'],
						// 'category'=>$group['category'],
						'reg_type'=>'email',
						'designation_id'=>'4',
						'password'=>\Hash::make($password),
						'otp_verified'=>'No',
						'system_generated_password'=>'1',
					);

					
					$to = $group['email'];
					$name = $group['name'];
					$language = $group['lang'];

					if($language == 'sp'){
						$url = '<a href="'.url('profile-update').'">aqui</a>';
						$faq = '<a href="'.url('faq').'">aqui</a>';

						$subject = "¡Su inscripción al GproCongress II ha iniciado!";
						$msg = '<p>Estimado '.$group['name'].',</p><p>&nbsp;</p><p>'.$name.' ha inicado el proceso de inscripción al GproCongress II al ingresar tu nombre.</p><p>Quedamos a la espera de recibir su solicitud completa.</p><p><br></p><p>Por favor, utilice este enlace haga click '.$url.' para acceder, editar y completer su cuenta en cualquier momento.&nbsp;</p><p><br><div><br>Dirección de correo electrónico: '.$to.'<br>Contraseña: '.$password.'<br></div></p><p>Si usted desea más información sobre los criterios de admisibilidad para candidatos potenciales al congreso, antes de continuar, haga click, '.$faq.'</p><p><br></p><p>Para hablar con uno de los miembros de nuestro equipo, usted solo tiene que responder a este email. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($language == 'fr'){
					
						$url = '<a href="'.url('profile-update').'">aqui</a>';
						$faq = '<a href="'.url('faq').'">cliquez ici</a>';

						$subject = "Votre inscription au GProCongrès II a commencé!";
						$msg = '<p>Cher '.$group['name'].',&nbsp;</p><p>'.$name.' a commencé le processus d’inscription au GProCongrès II, en soumettant votre nom!&nbsp;</p><p><br></p><p><br></p><p><br></p><p>Nous sommes impatients de recevoir votre demande complète. Veuillez utiliser ce lien '.$url.' pour accéder, modifier et compléter votre compte à tout moment.&nbsp;</p><p><br><div><br>E-mail: '.$to.'<br>Mot de passe: '.$password.'<br></div></p><p>Si vous souhaitez plus d’informations sur les critères d’éligibilité pour les participants potentiels au Congrès, avant de continuer,  '.$faq.'.</p><p><br></p><p>Pour parler à l’un des membres de notre équipe, vous pouvez simplement répondre à ce courriel. Nous sommes là pour vous aider !</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
					
					}elseif($language == 'pt'){
					
						$url = '<a href="'.url('profile-update').'">aqui</a>';
						$faq = '<a href="'.url('faq').'">clique aqui</a>';

						$subject = "A sua inscrição para o II CongressoGPro já Iniciou!";
						$msg = '<p>Prezado '.$group['name'].',</p><p><br></p><p>'.$name.' iniciou com o processo de inscrição para o II CongressoGPro, por submeter o teu nome!&nbsp;</p><p><br></p><p>Nós esperamos receber a sua inscrição complete.</p><p>Por favor use este '.$url.' para aceder, editar e terminar a sua conta a qualquer momento.</p><p><br><div><br>Eletrónico: '.$to.'<br>Senha: '.$password.'<br></div></p><p>Se você precisa de mais informações sobre o critério de elegibilidade para participantes potenciais ao Congresso, antes de continuar,  '.$faq.'</p><p><br></p><p>Para falar com um dos nossos membros da equipe, você pode simplesmente responder a este e-mail. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, a medida que nos esforçamos para multiplicar o número, e desenvolvemos a capacidade dos treinadores de pastores&nbsp;</p><p><br></p><p>Calorosamente,</p><p><br></p><p>A Equipe do II CongressoGPro</p>';
					
					}else{
					
						$url = '<a href="'.url('profile-update').'">link</a>';
						$faq = '<a href="'.url('faq').'">click here</a>';

						$subject = "Your registration for GProCongress II has begun!";
						$msg = '<p>Dear '.$group['name'].',</p><p><br></p><p>'.$name.' has begun the registration process for the GProCongress II, by submitting your name!&nbsp;</p><p><br></p><p>We look forward to receiving your full application.</p><p>Please use this  '.$url.' to access, edit, and complete your account at any time.&nbsp;</p><p><br><div>Your registered email and password are:</div><div><br>Email: '.$to.'<br>Password: '.$password.'<br></div></p><p>If you want more information about the eligibility criteria for potential Congress attendees, before proceeding,  '.$faq.'.</p><p><br></p><p>To speak with one of our team members, you can simply respond to this email. We are here to help!</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
						
					}

					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					// \App\Helpers\commonHelper::sendSMS($group['mobile']);

				}

				\App\Models\User::insert($users);

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($language,'GroupInfo-updated-successfully');
				return response(array("error"=>true, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function getUserData(Request $request) {
 
		$type = 'Pending';
	
		
		$columns = \Schema::getColumnListing('users');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		
		$query = \App\Models\User::where([['id', '!=', '1'],['parent_id', '=', null]])->orderBy('id', 'desc');

		if (request()->has('email')) {
			$query->where(function ($query1) {
				$query1->where('email', 'like', "%" . request('email') . "%")
					  ->orWhere('name', 'like', "%" . request('email') . "%")
					  ->orWhere('last_name', 'like', "%" . request('email') . "%");
			});
			
		}

		$data = $query->get();
		
		$totalData1 = \App\Models\User::where([['id', '!=', '1'],['parent_id', '=', null]])->orderBy('id', 'desc');
		
		if (request()->has('email')) {

			$totalData1->where(function ($query) {
				$query->where('email', 'like', "%" . request('email') . "%")
					  ->orWhere('name', 'like', "%" . request('email') . "%")
					  ->orWhere('last_name', 'like', "%" . request('email') . "%");
			});

		}

		$totalData = $totalData1->count();
		
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		// ->setOffset($start)

		->addColumn('name', function($data){
			return $data->name.' '.$data->last_name;
		})
		->addColumn('user_name', function($data){

			if(\App\Helpers\commonHelper::checkGroupUsers($data->email)){
				return '<a href="javascript:void(0)" class="group-user-list" data-email="'.$data->email.'"></a> '.$data->email ;
			} else {
				return $data->email;
			}

		})

		->addColumn('stage0', function($data){
			return \App\Helpers\commonHelper::getCountryNameById($data->id); 
		})

		->addColumn('stage1', function($data){
			return ucfirst($data->business_name);
		})

		->addColumn('stage2', function($data){
			return ucfirst($data->business_identification_no);
		})

		->addColumn('stage3', function($data){
			return ($data->mobile);
		})

		->addColumn('stage4', function($data){
			return ($data->passport_number);
		})

		->addColumn('stage5', function($data){
			return '<a target="_blank" href="'.asset('uploads/passport/'.$data->passport_copy).'" >View</a>'; 
		})

		->addColumn('payment', function($data){
			if($data->parent_id != null){
				return '<div class="span badge rounded-pill pill-badge-secondary">N/A</div>';
			}
			if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})

		->addColumn('action', function($data){

			if($data->profile_status == 'Approved'){

				return '
					<div style="display:flex">
					<a href="'.env('Admin_URL').'/admin/exhibitor/profile/'.$data->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
					</div>
				';
				
			}else{

				return '
					<div style="display:flex">
					<a data-id="'.$data->id.'" title="Send Sponsorship letter" class="btn btn-sm btn-outline-success m-1 sendSponsorshipLetter">Send</a>
					<a href="'.env('Admin_URL').'/admin/exhibitor/profile/'.$data->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
					<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
					</div>
				';
			}
			

		})

		

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getGroupUsersList(Request $request) {


		$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
		$results = \App\Models\User::where([['parent_id', $id]])->get();

		$html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"> 
					<thead> 
					<tr> 
					<th class="text-center">'. \Lang::get('admin.id') .'</th> 
					<th class="text-center">Added As</th> 
					<th class="text-center">Name</th> 
					<th class="text-center">Email</th> 
					<th class="text-center">Mobile</th> 
					<th class="text-center">Citizenship</th> 
					<th class="text-center">Passport Number</th> 
					<th class="text-center">DOB</th> 
					<th class="text-center">Gender</th> 
					<th class="text-center">Passport Copy</th> 
					<th class="text-center">'. \Lang::get('admin.action') .'</th> </tr> </thead><tbody>';
		
		if (count($results) > 0) {
			foreach ($results as $key=>$result) {

				$spouse = \App\Models\User::where([['parent_id', $result->id]])->first();

				$key += 1;
				$html .= '<tr>';
				$html .= '<td class="text-center">'.$key.'.</td>';

				$html .= '<td class="text-center">'.$result->added_as;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->added_as.'</p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center">'.$result->name;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->name.'</p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center">'.$result->email;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->email.'</p>' : '';
				$html .= '</td>';

				
				$html .= '<td class="text-center">'.$result->mobile;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->mobile.'</p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getCountryNameById($result->id);
				$html .= $spouse ? '<hr><p class="text-danger">'.\App\Helpers\commonHelper::getCountryNameById($spouse->id).'</p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center">'.$result->passport_number;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->passport_number.'</p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center">'.$result->dob;
				$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->dob.'</p>' : '';
				$html .= '</td>';

				$gender = 'Male';
				if($spouse && $spouse->gender == '2'){
					$gender = 'Female';
				}

				$resultGender = 'Male';
				if($result && $result->gender == '2'){
					$resultGender = 'Female';
				}

				$html .= '<td class="text-center">'.$resultGender ;
				$html .= $spouse ? '<hr><p class="text-danger">'.$gender : '</p>';
				$html .= '</td>';

				$html .= '<td class="text-center"><a target="_blank" href="'.asset('uploads/passport/'.$result->passport_copy).'" >View</a></p>';
				$html .= $spouse ? '<hr><p class="text-danger"><a target="_blank" href="'.asset('uploads/passport/'.$spouse->passport_copy).'" >View</a></p>' : '';
				$html .= '</td>';

				$html .= '<td class="text-center"><a href="'.env('Admin_URL').'/admin/exhibitor/profile/'.$result->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></p>';
				$html .= $spouse ? '<hr><p class="text-danger"><a href="'.env('Admin_URL').'/admin/exhibitor/profile/'.$spouse->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></p>' : '';
				$html .= '</td>';

				$html .= '</tr>';
			}
		} else {
			$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
		}
		$html .= '</tbody></table>';

		return response()->json(array('html'=>$html));
		

	}

	public function userProfile(Request $request) {

		$id = '';
		if(isset($_GET['id']) && $_GET['id'] != ''){
			$id = $_GET['id'];
		}

		$data = [];

		$result = \App\Models\User::where([['id', '=', $id]])->first();
		
		if (!$result) {

			return response(array("error"=>true, "message"=>'data not found', "result"=>[]), 403);
		}

		$data = [
			'id'=>$result->id,
			'parent_id'=>$result->parent_id,
			'business_owner'=>$result->parent_id,
			'added_as'=>$result->added_as,
			'salutation'=>$result->salutation,
			'last_name'=>$result->last_name,
			'name'=>$result->name,
			'gender'=>$result->gender,
			'last_name'=>$result->last_name,
			'email'=>$result->email,
			'phone_code'=>$result->phone_code,
			'mobile'=>$result->mobile,
			'reg_type'=>$result->reg_type,
			'status'=>$result->status,
			'profile_status'=>$result->profile_status,
			'dob'=>$result->dob,
			'citizenship'=>$result->citizenship,
			'amount'=>$result->amount,
			'payment_status'=>$result->payment_status,
			'room'=>$result->room,
			'business_name'=>$result->business_name,
			'business_identification_no'=>$result->business_identification_no,
			'website'=>$result->website,
			'logo'=>'<a target="_blank" href="'.asset('uploads/logo/'.$result->logo).'">View</a>',
			'passport_number'=>$result->passport_number,
			'passport_copy'=>'<a target="_blank" href="'.asset('uploads/passport/'.$result->passport_copy).'">View</a>',
			'any_one_coming_with_along'=>$result->any_one_coming_with_along,
			'coming_with_spouse'=>$result->coming_with_spouse,
			'number_of_room'=>$result->number_of_room,
			'qrcode'=>$result->qrcode,
			'spouse_id'=>$result->spouse_id,
			'language'=>$result->language,
			'spouse'=>\App\Models\User::where('parent_id',$result['id'])->where('added_as','Spouse')->first(),
			'parent_name'=>\App\Helpers\commonHelper::getUserNameById($result['parent_id']),
			'AmountInProcess'=>\App\Helpers\commonHelper::getTotalAmountInProcess($result['id']),
			'AcceptedAmount'=>\App\Helpers\commonHelper::getTotalAcceptedAmount($result['id']),
			'PendingAmount'=>\App\Helpers\commonHelper::getTotalPendingAmount($result['id']),
			'WithdrawalBalance'=>\App\Helpers\commonHelper::userWithdrawalBalance($result['id']),
			'RejectedAmount'=>\App\Helpers\commonHelper::getTotalRejectedAmount($result['id']),
		];

		return response(array("error"=>false, "message"=>'user data found', "result"=>$data), 200);

	}

	public function getExhibitorPaymentHistory(Request $request, $id) {
		
		$columns = \Schema::getColumnListing('transactions');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '1')->orderBy($order,$dir);

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '1')->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)

		->addColumn('user_name', function($data){
			return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		})
		
		->addColumn('created_at', function($data){
			return date('d-M-Y H:i:s',strtotime($data->created_at));
		})

		
		->addColumn('transaction', function($data){
			return $data->order_id;
		})

		->addColumn('utr', function($data){
			return $data->bank_transaction_id;
		})

		->addColumn('bank', function($data){
			return $data->bank." Transfer";
		})

		->addColumn('type', function($data){
			
			return 'Credit';
			
		})


		->addColumn('mode', function($data){
			return $data->method;
		})

		->addColumn('amount', function($data){
			return '$'.$data->amount;
		})

		->addColumn('payment_status', function($data){

			if($data->payment_status == '0'){

				return "Pending";

			}elseif($data->payment_status == '2'){

				return "Accepted";
				
			}else{
				return "Failed";
			}
			
		})

		->addColumn('updated_at', function($data){
			return date('d-M-Y H:i:s',strtotime($data->updated_at));
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);

	}

	public function getExhibitorCommentHistory(Request $request) {
		
		$columns = \Schema::getColumnListing('comments');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->orderBy('id', 'desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)

		->addColumn('comment_by', function($data){

			return 'Admin';
		})

		->addColumn('comment', function($data){
			return $data->comment;
		})

		->addColumn('created_at', function($data){
			return date('Y-m-d h:i', strtotime($data->created_at));
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);

	}

	public function getExhibitorActionHistory(Request $request) {
		
		$columns = \Schema::getColumnListing('user_histories');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\UserHistory::where('user_id', $request->input('user_id'))->orderBy('id', 'desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\UserHistory::where('user_id', $request->input('user_id'))->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)
		->addColumn('action', function($data){
			return $data->action;
		})
		->addColumn('admin', function($data){

			if($data->action_id){

				
				return \App\Helpers\commonHelper::getUserNameById($data->action_id);
			}else{

				return \App\Helpers\commonHelper::getUserNameById($data->user_id);
			}
			
		})
		
		->addColumn('date', function($data){
			return date('d M Y', strtotime($data->created_at));
		})

		->addColumn('time', function($data){
			return date('H:i a', strtotime($data->created_at));
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);

	}

	public function getExhibitorMailTriggerList(Request $request) {
		
		$columns = \Schema::getColumnListing('user_mail_triggers');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\UserMailTrigger::where('user_id', $request->input('user_id'))->orderBy('id', 'desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\UserMailTrigger::where('user_id', $request->input('user_id'))->count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)
		
		->addColumn('subject', function($data){
			return $data->subject;
		})

		->addColumn('date', function($data){
			return date('d M Y', strtotime($data->created_at));
		})

		->addColumn('time', function($data){
			return date('H:i a', strtotime($data->created_at));
		})

		->addColumn('action', function($data){
				
			
				return '<div >
							<button type="button" style="width:41px" title="View message" class="btn btn-sm btn-primary px-3 m-1 text-white messageGet" data-id="'.$data->id.'" ><i class="fas fa-eye"></i></button>
						</div>';			
			
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);

	}

	public function exhibitorCommentSubmit(Request $request) {
		
		if($request->isMethod('post')){
			
			$rules = [
				'user_id' => 'required|numeric|exists:users,id',
				'comment' => 'required',
			];

			$validator = \Validator::make($request->all(), $rules);
			
			if ($validator->fails()){
				$message = "";
				$messages_l = json_decode(json_encode($validator->messages()), true);
				foreach ($messages_l as $msg) {
					$message= $msg[0];
					break;
				}
				
				return response(array('message'=>$message),403);
				
			} else {

				try {

					$data=new \App\Models\Comment();
					$data->sender_id = $request->post('admin_id');
					$data->receiver_id = $request->post('user_id');
					$data->comment = $request->post('comment');
					$data->save();

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->action_id=$request->post('admin_id');
					$UserHistory->user_id=$request->post('user_id');
					$UserHistory->action='Comment';
					$UserHistory->save();

					return response(array('reset'=>true, 'comment' => true, 'message'=>'Comment has been sent successfully.'), 200);

				} catch (\Throwable $th) {
					return response(array('message'=>'Something went wrong, please try again'), 500);
				}
			
			}


		} else if($request->isMethod('get')) {
			
			$columns = \Schema::getColumnListing('comments');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->orderBy('id', 'desc');

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Comment::where('receiver_id', $request->input('user_id'))->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('comment_by', function($data){

				return 'Admin';
			})

			->addColumn('comment', function($data){
				return $data->comment;
			})

			->addColumn('created_at', function($data){
				return date('Y-m-d h:i', strtotime($data->created_at));
			})

			->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
			->make(true);

		}

	}

	public function exhibitorMailTriggerListModel(Request $request) {

		$UserMailTrigger = \App\Models\UserMailTrigger::where('id', $request->id)->first();

		return response(array('message'=>$UserMailTrigger->message), 200);


	}

	public function exhibitorTransactionList(Request $request) {

		$columns = \Schema::getColumnListing('transactions');
			
		$limit = $request->input('length');
		$start = $request->input('start');
		// $order = $columns[$request->input('order.0.column')];
		// $dir = $request->input('order.0.dir');

		$query = \App\Models\Transaction::orderBy('created_at','desc');

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData = \App\Models\Transaction::count();
		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));  
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)

		->addColumn('user_name', function($data){
			return '<a style="color: blue !important;" href="'.url('admin/user/user-profile/'.$data->user_id).'" target="_blank" title="User Profile">'.\App\Helpers\commonHelper::getUserNameById($data->user_id).'</a>';
			
		})

		->addColumn('payment_by', function($data){
			return $data->bank;
		})

		->addColumn('method', function($data){
			return $data->method;
		})
		->addColumn('transaction_id', function($data){
			return $data->order_id;
		})
		->addColumn('bank_transaction_id', function($data){
			return $data->bank_transaction_id;
		})


		->addColumn('amount', function($data){
			return '$'.$data->amount;
		})

		->addColumn('payment_status', function($data){
			if($data->payment_status=='0' || $data->payment_status=='1' || $data->payment_status=='7' || $data->payment_status=='8' || $data->payment_status=='9') {
				return '<div class="span badge rounded-pill pill-badge-danger">'.\App\Helpers\commonHelper::getPaymentStatusName($data->payment_status).'</div>';
			} else if($data->payment_status=='3' || $data->payment_status=='4' || $data->payment_status=='6') {
				return '<div class="span badge rounded-pill pill-badge-orange">'.\App\Helpers\commonHelper::getPaymentStatusName($data->payment_status).'</div>';
			} else if($data->payment_status=='2' || $data->payment_status=='5') {
				return '<div class="span badge rounded-pill pill-badge-success">'.\App\Helpers\commonHelper::getPaymentStatusName($data->payment_status).'</div>';
			}
		})

		->addColumn('created_at', function($data){
			return date('d-M-Y H:i:s',strtotime($data->created_at));
		})
		->addColumn('decline_remark', function($data){
			return $data->decline_remark ?? '-';
		})

		->addColumn('action', function($data){
			$msg = "' Are you sure to delete this transaction ?'";

			if ($data->status == '1') {
				return '<div class="badge rounded-pill pill-badge-success">Approved</div>';
			} else if ($data->status == '2') {
				return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';
			} else if ($data->status == '0' && $data->method != 'Online') {

				return '<div style="display:flex"><a data-id="'.$data->id.'" data-type="1" title="Transaction Approve" class="btn btn-sm btn-outline-success m-1 -change">Approve</a>
				<a data-id="'.$data->id.'" data-type="2" title="Transaction Decline" class="btn btn-sm btn-outline-danger m-1 declineRemark">Decline</a></div>';
			}

		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getExhibitorQrcodeData(Request $request) {

		$columns = \Schema::getColumnListing('users');
			
		$limit = $request->input('length');
		$start = $request->input('start');
		// $order = $columns[$request->input('order.0.column')];
		// $dir = $request->input('order.0.dir');

		$query = \App\Models\User::where([['stage', '=', '3']])->orderBy('updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::where([['stage', '=', '3']]);
		
		if (request()->has('email')) {
			$totalData1->where('email', 'like', "%" . request('email') . "%");
		}

		$totalData = $totalData1->count();


		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)

		->addColumn('user_name', function($data){
			return $data->name;
		})

		->addColumn('profile', function($data){
			if ($data->profile_update == '1') {
				return '<div class="span badge rounded-pill pill-badge-success">Updated</div>';
			} else if ($data->user_status == '0') {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			}
		})

		->addColumn('payment', function($data){
			if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})

		->addColumn('session_info', function($data){
			if (count($data->SessionInfo) > 0) {
				if ($data->SessionInfo[0]->admin_status == '1') {
					return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
				} else if ($data->SessionInfo[0]->user_status == '0') {
					return '<div class="span badge rounded-pill pill-badge-danger">Reject</div>';
				} else if ($data->SessionInfo[0]->user_status === null) {
					return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
				}
			}else {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			}
		})

		->addColumn('user_type', function($data){
			
			if($data->parent_id != Null){

				if($data->added_as == 'Group'){

					return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
					
				}elseif($data->added_as == 'Spouse'){

					return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
					
				}

			}else {

				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
			
				if($groupName){

					return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
					
				}else if($spouseName){

					return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

				}else{

					return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
				}
					

			}
			
		})

		->addColumn('group_owner_name', function($data){
			
			$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
			
			if($data->parent_id != Null && $data->added_as == 'Group'){

				return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
				
			}else if(count($groupName) > 0) {

				return ucfirst($data->name.' '.$data->last_name);

			}else{
				return 'N/A';
			}
			
		})

		->addColumn('spouse_name', function($data){
			
			$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
			
			if($data->parent_id != Null && $data->added_as == 'Spouse'){

				return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

			}else if($spouseName) {

				return ucfirst($spouseName->name.' '.$spouseName->last_name);

			}else{

				return 'N/A';
			}
			
		})
		->addColumn('action', function($data){
			$msg = "' Are you sure to delete this user ?'";

			return '<a href="'.route('admin.user.details', ['id' => $data->id] ).'" title="View user details" class="btn btn-sm btn-primary px-3" ><i class="fas fa-eye"></i></a>';
			
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getExhibitorSponsorshipData(Request $request) {

		$columns = \Schema::getColumnListing('users');
			
		$limit = $request->input('length');
		$start = $request->input('start');
		// $order = $columns[$request->input('order.0.column')];
		// $dir = $request->input('order.0.dir');

		$query = \App\Models\User::where([['stage', '=', '2']])->orderBy('updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::where([['stage', '=', '2']]);
		
		if (request()->has('email')) {
			$totalData1->where('email', 'like', "%" . request('email') . "%");
		}

		$totalData = $totalData1->count();


		$totalFiltered = $query->count();

		$draw = intval($request->input('draw'));
		$recordsTotal = intval($totalData);
		$recordsFiltered = intval($totalFiltered);

		return \DataTables::of($data)
		->setOffset($start)

		->addColumn('name', function($data){
			return $data->name;
		})

		->addColumn('email', function($data){
			return $data->email;
		})

		->addColumn('mobile', function($data){
			return $data->mobile;
		})

		->addColumn('status', function($data){
			if ($data->profile_status == 'Approved') {
				return '<div class="span badge rounded-pill pill-badge-success">Approved</div>';
			} else if ($data->profile_status == 'Pending') {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			}
		})

		->addColumn('payment', function($data){
			if($data->parent_id != null){
				return '<div class="span badge rounded-pill pill-badge-secondary">N/A</div>';
			}
			if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})

		->addColumn('sponsorship', function($data){
			
			return '<a target="_blank" href="'.asset('uploads/sponsorship/'.$data->sponsorship_letter).'" title="View Letter" class="btn btn-sm btn-success px-3" ><i class="fas fa-eye" style="color:#fff"></i></a>';
			
		})


		->addColumn('user_type', function($data){
			
			if($data->parent_id != Null){

				if($data->added_as == 'Group'){

					return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
					
				}elseif($data->added_as == 'Spouse'){

					return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
					
				}

			}else {

				$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->first();
				$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
			
				if($groupName){

					return '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
					
				}else if($spouseName){

					return '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

				}else{

					return '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
				}
					

			}
			
		})

		->addColumn('group_owner_name', function($data){
			
			$groupName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Group')->get();
			
			if($data->parent_id != Null && $data->added_as == 'Group'){

				return \App\Helpers\commonHelper::getUserNameById($data->parent_id);
				
			}else if(count($groupName) > 0) {

				return ucfirst($data->name.' '.$data->last_name);

			}else{
				return 'N/A';
			}
			
		})

		->addColumn('spouse_name', function($data){
			
			$spouseName = \App\Models\user::where('parent_id', $data->id)->where('added_as','Spouse')->first();
			
			if($data->parent_id != Null && $data->added_as == 'Spouse'){

				return \App\Helpers\commonHelper::getUserNameById($data->parent_id);

			}else if($spouseName) {

				return ucfirst($spouseName->name.' '.$spouseName->last_name);

			}else{

				return 'N/A';
			}
			
		})
		->addColumn('action', function($data){
			
			return '
					<div style="display:flex">
						<a data-id="'.$data->id.'" title="Send Sponsorship letter" class="btn btn-sm btn-outline-success m-1 sendSponsorshipLetter">Send</a>
						<a href="'.env('Admin_URL').'/admin/exhibitor/profile/'.$data->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
					</div>
				';
				
			
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function exhibitorProfileBasePrice(Request $request) {

		$basePrice = 0; $Spouse = [];

		$user = \App\Models\User::where('id', $request->get('id'))->first();

		if($user){

			$additionalPerson = \App\Models\User::where('business_owner_id', $request->get('id'))->count();

			if($user->room == 'No'){

				if($additionalPerson > 0){

					$basePrice = 850+($additionalPerson*350);

				}else{

					$basePrice = 850;
				}
				
			}else if($user->room == 'Yes'){

				$basePrice = 1500;

				$Spouse = \App\Models\User::where('business_owner_id', $request->get('id'))->where('added_as', 'Spouse')->count();
				if($Spouse>0){
					$basePrice+= $Spouse*350;
				}

				$additionalPerson = \App\Models\User::where('business_owner_id', $request->get('id'))->where('added_as', 'Group')->count();
				if($additionalPerson>0){
					$basePrice+= $additionalPerson*1000;
				}
				
			}

			$html=view('admin.user.stage.stage_one_profile_status_model',compact('basePrice','user'))->render();

			return response()->json(array('html'=>$html));
			

		}
			
	}

	public function exhibitorProfileStatus(Request $request) {

		$result = \App\Models\User::find($request->post('user_id'));
		
		if ($result) {

			$to = $result->email;

			if ($request->post('status') == 'Approved') {

				$resultSpouse = \App\Models\User::where('business_owner_id',$result->id)->get();
				
				if(!empty($resultSpouse)){
					
					foreach($resultSpouse as $user){

						$additionalUser = \App\Models\User::find($user->id);
						if($additionalUser){

							$additionalUser->profile_status = $request->post('status');
							$additionalUser->stage = 2;
							$additionalUser->save();

							$name= $additionalUser->name.' '.$additionalUser->last_name;

							$doNotRequireVisa = ['82','6','10','194','11','12','14','15','17','20','22','23','21','255','27','28','29','31','33','34','26','37','39','44','57','238','48','53','55','59','61','64','66','231','200','201','207','233','69','182','73','74','75','79','81','87','90','94','97','98','99','232','105','100','49','137','202','106','107','108','109','114','117','120','125','126','127','251','130','132','133','135','140','142','143','144','145','146','147','152','153','159','165','158','156','168','171','172','173','176','177','179','58','256','252','116','181','191','185','192','188','253','196','197','199','186','204','213','214','219','216','222','223','225','228','230','235','237','240']; 
							$diplomaticPassportNotRequireVisa = [56,62,95,102,174,45,239]; 
							$authorizedVisa = [1,3,4,16,18,19,24,35,36,43,50,60,65,68,70,67,80,92,93,95,102,103,104,54,111,112,248,118,119,121,122,123,124,134,139,149,150,151,154,160,161,166,167,169,116,183,195,198,203,208,209,210,215,217,218,224,226,229,236,245,246]; 
							$stampedVisa = [38,42,56,62,83,101,131,174,45,51,212,220,239,247];
						

							// if($additionalUser && $additionalUser->diplomatic_passport == 'No'){

							// 	if(in_array($additionalUser->citizenship,$doNotRequireVisa)){

							// 		\App\Helpers\commonHelper::sendFinancialLetterMailSend($additionalUser->user_id);

							// 	}else{

							// 		\App\Helpers\commonHelper::sendFinancialLetterMailSend($additionalUser->user_id,);
							// 		\App\Helpers\commonHelper::sendSponsorshipLetterMailSend($additionalUser->user_id);
							// 	}

							// }elseif($additionalUser && $additionalUser->diplomatic_passport == 'Yes'){
								
							// 	if(in_array($additionalUser->citizenship,$doNotRequireVisa)){

							// 		\App\Helpers\commonHelper::sendFinancialLetterMailSend($additionalUser->user_id);

							// 	}elseif(in_array($additionalUser->citizenship,$diplomaticPassportNotRequireVisa)){

							// 		\App\Helpers\commonHelper::sendFinancialLetterMailSend($additionalUser->user_id);

							// 	}else{

							// 		\App\Helpers\commonHelper::sendFinancialLetterMailSend($additionalUser->user_id);
							// 		\App\Helpers\commonHelper::sendSponsorshipLetterMailSend($additionalUser->user_id);
							// 	}
								
							// }
								
							
							if($additionalUser->language == 'sp'){

								$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
								$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>¡Nos da gran alegría confirmar que su solicitud para asistir al GProCongress II ha sido aceptada! Esperamos verle en Ciudad de Panamá en noviembre de 2023, Dios mediante.</p><p><br></p><p><br></p><p>Mientras usted se prepara para venir, por favor, únasenos en oración por los demás participantes.&nbsp;</p><p><br></p><p><br></p><p>¿Todavía tiene preguntas o necesita ayuda? Responda a este correo electrónico y nuestro equipo se pondrá en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
							
							}elseif($additionalUser->language == 'fr'){
							
								$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
								$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>C’est avec une grande joie que nous confirmons l’acceptation de votre candidature pour assister au GProCongrès II ! Nous avons hâte de vous voir à Panama City en novembre 2023, si le Seigneur le veut.&nbsp;</p><p><br></p><p>Pendant que vous vous préparez, joignez-vous à nous pour prier pour les autres participants.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
							
							}elseif($additionalUser->language == 'pt'){
							
								$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
								$msg = '<p>Prezado '.$name.',</p><p><br></p><p>É para nós um grande prazer confirmar a aceitação do seu pedido de participar no II CongressoGPro. Nós esperamos lhe ver na Cidade de Panamá em Novembro de 2023, se o Senhor permitir.</p><p><br></p><p>A medida que se prepara, por favor junte-se a nós em oração pelos outros participantes.</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assistência? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'Congratulations, '.$name.', your application has been approved!';
								$msg = '<p>Dear '.$name.',</p><p><br></p><p>It gives us great joy to confirm the acceptance of your application to attend the GProCongress II! We look forward to seeing you in Panama City in November 2023, the Lord willing.</p><p><br></p><p>As you prepare, please join us in praying for the other attendees.</p><p><br></p><p>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';	
							
							}

							$to = $additionalUser->email;
							
							// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
							\App\Helpers\commonHelper::userMailTrigger($additionalUser->id,$msg,$subject);
						}
						
					}

				}

				$result->profile_status = $request->post('status');
				$result->remark = $request->post('remark');
				$result->amount = $request->post('amount');
				$result->stage = '2';
				$name= $result->name.' '.$result->last_name;

				if($result->language == 'sp'){

					$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>¡Nos da gran alegría confirmar que su solicitud para asistir al GProCongress II ha sido aceptada! Esperamos verle en Ciudad de Panamá en noviembre de 2023, Dios mediante.</p><p><br></p><p><br></p><p>Mientras usted se prepara para venir, por favor, únasenos en oración por los demás participantes.&nbsp;</p><p><br></p><p><br></p><p>¿Todavía tiene preguntas o necesita ayuda? Responda a este correo electrónico y nuestro equipo se pondrá en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->language == 'fr'){
				
					$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
					$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>C’est avec une grande joie que nous confirmons l’acceptation de votre candidature pour assister au GProCongrès II ! Nous avons hâte de vous voir à Panama City en novembre 2023, si le Seigneur le veut.&nbsp;</p><p><br></p><p>Pendant que vous vous préparez, joignez-vous à nous pour prier pour les autres participants.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d’aide ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->language == 'pt'){
				
					$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>É para nós um grande prazer confirmar a aceitação do seu pedido de participar no II CongressoGPro. Nós esperamos lhe ver na Cidade de Panamá em Novembro de 2023, se o Senhor permitir.</p><p><br></p><p>A medida que se prepara, por favor junte-se a nós em oração pelos outros participantes.</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assistência? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Congratulations, '.$name.', your application has been approved!';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>It gives us great joy to confirm the acceptance of your application to attend the GProCongress II! We look forward to seeing you in Panama City in November 2023, the Lord willing.</p><p><br></p><p>As you prepare, please join us in praying for the other attendees.</p><p><br></p><p>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';	
				
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

			}else if ($request->post('status') == 'Decline') {

				$result->profile_status = 'Rejected';

				$faq = '<a href="'.url('faq').'">Click here</a>';
				
				$name= $result->name.' '.$result->last_name;
				
				if($result->language == 'sp'){

					$url = '<a href="'.url('profile-update').'">clic aquí</a>';
				
					$subject = "Estado de su Solicitud para el GProCongress II";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>Gracias por registrarse para participar del GProCongress II.</p><p><br></p><p>Hemos evaluado muchas aplicaciones con varios nivels de participación en la capacitación de pastores, pero lamentablemente sentimos informale que su solicitud ha sido rechazada en esta ocación.&nbsp;</p><p><br></p><p><br></p><p>Sin embargo, esto no significa el fin de nuestra relación.&nbsp;</p><p><br></p><p>Por favor, manténgase conectado a la comunidad GProCommission haciendo : '.$url.'. Recibirá aliento continuo, ideas, apoyo en oración y mucho más mientras usted forma líderes pastorales.</p><p><br></p><p>Si todavía tiene preguntas, simplemente responda a este correo y nuestro equipo se conectará con usted.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->language == 'fr'){
				
					$url = '<a href="'.url('profile-update').'">cliquant ici</a>';
				
					$subject = "Statut de votre demande GProCongrès II";
					$msg = '<p>Cher '.$name.',</p><p><br></p><p><br></p><p>Merci d’avoir postulé pour assister au GProCongrès II.</p><p>Nous avons évalué de nombreuses candidatures avec différents niveaux d’implication de la formation des pasteurs, mais nous avons malheureusement le regret de vous informer que votre candidature a été refusée, cette fois-ci.&nbsp;&nbsp;</p><p><br></p><p>Cependant, ce n’est pas la fin de notre relation.&nbsp;</p><p>Veuillez rester connecté à la communauté GProCommission en : '.$url.'. Vous recevrez des encouragements continus, des idées, un soutien à la prière et autres alors que vous préparez les responsables pastoraux.&nbsp;</p><p><br></p><p>Avez-vous encore des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->language == 'pt'){
				
					$url = '<a href="'.url('profile-update').'">aqui</a>';
				
					$subject = "Estado do seu pedido para o II CongressoGPro";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos pelo seu pedido para participar no II CongressoGPro.</p><p>Nós avaliamos muitos pedidos com vários níveis de envolvimento no treinamento pastoral, mas infelizmente lamentamos informar que o seu pedido foi declinado esta vez.&nbsp;</p><p><br></p><p>Contudo, este não é o fim do nosso relacionamento.</p><p>&nbsp;</p><p>Por favor se mantenha conectado com a nossa ComunidadeGPro clicando : '.$url.'. Você continuará recebendo encorajamento contínuo, ideias, suporte em oração e muito mais, à medida que prepara os líderes pastorais.</p><p><br></p><p>Ainda tem perguntas? Simplesmente responda este e-mail, e nossa equipe irá se conectar com você.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$url = '<a href="'.url('profile-update').'">Click here</a>';
				
					$subject = 'Your GProCongress II application status';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for applying to attend the GProCongress II.</p><p>We have evaluated many applications with various levels of pastor training involvement, but sadly regret to inform you that your application has been declined, this time.&nbsp;</p><p><br></p><p>However, this is not the end of our relationship.&nbsp;</p><p>Please stay connected to the GProCommission community by : '.$url.'. You will receive ongoing encouragement, ideas, prayer support, and more as you prepare pastoral leaders.&nbsp;</p><p><br></p><p>Do you still have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
					
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

			}
			
			$result->save();

			$UserHistory=new \App\Models\UserHistory();
			$UserHistory->user_id=$result->id;
			$UserHistory->action_id=$request->post('admin_id');
			$UserHistory->action='User Profile '.$request->post('status');
			$UserHistory->save();

			if ($request->post('status') == 'Approved') {

				$name = $result->name.' '.$result->last_name;

				\App\Helpers\commonHelper::sendPaymentReminderMailSend($result->id,$result->email,$name);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

			}

			return response(array('error'=>false, 'reload'=>true, 'message'=>'Profile status change successful'), 200);
		
		} else {
			return response(array('error'=>true, 'reload'=>false, 'message'=>'Something went wrong. Please try again.'), 403);
		}
			
	}

	public function exhibitorUploadSponsorshipLetter(Request $request) {

		$rules = [
			'file' => 'required|mimes:pdf',
			'id' => 'numeric|required',
			
		];

		$validator = \Validator::make($request->all(), $rules);
			
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

				$user= \App\Models\User::where('id',$request->post('id'))->first();

				if($request->hasFile('file')){
					$imageData = $request->file('file');
					$image = 'image_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
					$destinationPath = public_path('/uploads/sponsorship');
					$imageData->move($destinationPath, $image);

					$user->sponsorship_letter = $image;
				}


				$user->save();

				$to = $user->email;
				$subject = 'Please verify your sponsorship letter.';
				$msg = '<p>Thank you for submitting your sponsorship letter.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link:  to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->admin_id,$subject,$msg,'Sponsorship letter upload');
				
				$name = $user->name.' '.$user->last_name;

				if($user->language == 'sp'){

					$subject = "Por favor, verifique su información de viaje";
					$msg = '<p>Estimado '.$name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su información de viaje.&nbsp;</p><p><br></p><p>A continuación, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la información recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace:  para verificar que la información es correcta.</p><p><br></p><p>Gracias por su colaboración.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
				
				}elseif($user->language == 'fr'){
				
					$subject = "Veuillez vérifier vos informations de voyage";
					$msg = "<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d’avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons rédigée basée sur les informations reçues.&nbsp;</p><p><br></p><p>Pourriez-vous s’il vous plaît examiner la lettre, puis cliquer sur ce lien:  pour vérifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe du GProCongrès II</p><div><br></div>";
		
				}elseif($user->language == 'pt'){
				
					$subject = "Por favor verifique sua Informação de Viagem";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos por submeter sua informação de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informação que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e daí clicar neste link:  para verificar que a informação esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p><div><br></div>';
				
				}else{
				
					$subject = 'Please verify your sponsorship letter.';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link:  to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
										
				}

				$file = public_path('uploads/sponsorship/'.$user->sponsorship_letter);

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, false, $file);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				
				return response(array('message'=>'Sponsorship letter upload successfully'),200);
				
			}catch (\Exception $e){
				
				return response(array('message'=>$e->getMessage()),403);
			}
		}
			
	}

	public function exhibitorPaymentReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['designation_id', '=', '14'], ['profile_status','=','Approved'], ['added_as', '=', null]])
									->whereDate('status_change_at', '=', now()->subDays(5)->setTime(0, 0, 0)->toDateTimeString())
									->get();

									
			if(count($results) > 0){
				foreach ($results as $key => $result) {
				
					\App\Helpers\commonHelper::sendExhibitorPaymentReminderMailSend($result->id);

				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function getTotalMemberForCommunity(Request $request){
		
		try {
			
			$query = \App\Models\User::where('profile_status','=','Approved')->limit(20)->orderBy('id','desc');

			if(isset($_GET['search']) && $_GET['search'] != ''){

				$query->where(function ($query1) {

					$query1->where('email', 'like', "%" . $_GET['search'] . "%")
						  ->orWhere('name', 'like', "%" . $_GET['search'] . "%")
						  ->orWhere('last_name', 'like', "%" . $_GET['search'] . "%");
				});
			}

			

			$results = $query->get();
			
			$data = [];
									
			if(count($results) > 0){

				foreach ($results as $key => $result) {
				
					$data[] = [
						'id'=>$result->id,
						'salutation'=>$result->salutation,
						'last_name'=>$result->last_name,
						'name'=>$result->name,
						'gender'=>$result->gender,
						'last_name'=>$result->last_name,
						'email'=>$result->email,
						'phone_code'=>$result->phone_code,
						'mobile'=>$result->mobile,
					];
			
				}
				
				return response(array("error"=>false, "message"=>'user Result', "result"=>$data), 200);

			}else{
				return response(array("error"=>true, "message"=>'No results found.', "result"=>$data), 200);
			}

		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 200);
		}

    }

	public function sendcheckUserDetailsPendingAmount() {
		
		$results = \App\Models\User::where([['user_type', '=', '2'],['designation_id', '!=', '14'], ['profile_status','=','Approved'],['stage', '=', '2'],['amount', '!=', '0']])->get();
		if(!empty($results)){
			$resultData = [];
			foreach($results as $val){
				
				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($val->id, true);

				if($totalPendingAmount==0){

					$resultData[] = [
						'id'=>$val->id,
						'name'=>$val->name,
						'email'=>$val->email,
						'amount'=>$val->amount,
						'stage'=>$val->stage,
						'totalPendingAmount'=>$totalPendingAmount,
						'TotalAcceptedAmount'=>\App\Helpers\commonHelper::getTotalAcceptedAmount($val->id, true),
					];

				}
			}

			echo "<pre>";
			print_r($resultData); die;
			
			
		}		

	}

	public function SendEmailsToPaymentCompleteDone(Request $request){
		
		$emails = array('jesusperezreina@gmail.com',
					'nour_challita@wycliffeassociates.org',);

		// $emails = array('gopalsaini.img@gmail.com');
		foreach ($emails as $email) {
		
			$user = \App\Models\User::where('email',$email)->first();
			if($user){

				$name = $user->salutation.' '.$user->name.' '.$user->last_name;
				$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user->id, true);
				$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($user->id, true);
				$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($user->id, true);
				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, true);


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
				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Payment received in full. Thank you!');

			}
			
		}
			
		return response(array('message'=>'Reminders has been sent successfully. All Emails : '.print_r($emails)), 200);

	}

	public function takingApplicationsForExhibitors(Request $request){
		
		try {
			
			$results = \App\Models\User::where('profile_status','Approved')->where('stage','3')->get();
			
			if(count($results) > 0){

				foreach ($results as $key => $user) {
				
					if($user->language == 'sp'){

						$url = '<a href="'.url('exhibitor-index').'" target="_blank">sitio web</a>';
						$subject = 'GProCongress II está aceptando solicitudes para ser Exhibidor.';
						$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Usted está recibiendo este correo electrónico porque se ha inscrito y ha pagado la totalidad de la inscripción al GProCongress II.  Ahora tiene la oportunidad de solicitar ser Exhibidor en el Congreso.</p>
						<p>Si usted tiene un producto o servicio que podría beneficiar a capacitadores de pastores alrededor del mundo, entonces por favor ingrese a su cuenta en nuestro '.$url.'.  Después de iniciar sesión, vaya a la barra de menú en la parte superior de la página, haga clic en "Exhibidores" y complete la solicitud hoy mismo.</p>
						<p>Le rogamos que presente su solicitud lo antes posible, ya que el espacio es limitado y los expositores se eligen por orden de inscripción.  En consecuencia, si espera demasiado para presentar su solicitud y efectuar el pago, podría quedarse fuera del Congreso como exhibidor, ya que todas las plazas podrían estar ya completas.						</p>
						<p><i>Únase a nosotros en oración en pro de multiplicar la cantidad y calidad de capacitadores de pastores. </i></p>
						<p>Cordialmente,</p><p>Equipo GProCongress II</p>';

					}elseif($user->language == 'fr'){
					
						$url = '<a href="'.url('exhibitor-index').'" target="_blank">site Web</a>';
						$subject = "GProCongress II accepte désormais les demandes d'exposants.";
						$msg = '<p>Cher  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Vous recevez cet e-mail parce que vous vous êtes inscrit et que vous avez payé en totalité pour le GProCongress II.  Maintenant, vous avez la possibilité de postuler pour être exposant au Congrès!</p>
						<p>Si vous avez un produit ou un service qui profiterait aux formateurs de pasteurs du monde entier, veuillez vous connecter à votre compte sur notre '.$url.'.  Après vous être connecté, allez dans la barre de menu en haut de la page, cliquez sur « Exposants » et remplissez la demande dès aujourd’hui.						</p>
						<p>Nous vous demandons de vous inscrire le plus tôt possible, car les places sont limitées et les exposants sont choisis selon le principe du « premier à payer, premier arrivé ».  Par conséquent, si vous attendez trop longtemps pour vous inscrire et effectuer le paiement, vous pourriez être exclu du Congrès en tant qu’exposant, car toutes les places pourraient déjà être prises.						</p>
						<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</i></p>
						<p>Cordialement,</p><p>L’équipe GProCongress II</p>';

					}elseif($user->language == 'pt'){
					
						$url = '<a href="'.url('exhibitor-index').'" target="_blank">site</a>';
						$subject = 'O GProCongresso II já está recebendo inscrições para Expositores.';
						$msg = '<p>Caro '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Você está recebendo este e-mail porque se inscreveu e pagou integralmente a sua inscrição para o GProCongresso II.  Agora você tem a oportunidade de se candidatar como Expositor do Congresso!</p>
						<p>Se você tem um produto ou serviço que beneficiaria os treinadores de pastores em todo o mundo, faça o login em sua conta em nosso '.$url.'. Depois de fazer o login, vá para a barra de menu no topo da página, clique em “Expositores” e preencha o formulário hoje mesmo.</p>
						<p>Pedimos que você se inscreva o mais rápido possível, pois as vagas são limitadas e os expositores são escolhidos na base de “quem chegar primeiro  e pagar primeiro”. Assim, se você demorar muito para se inscrever e efetuar o pagamento, poderá ficar de fora do Congresso como expositor, pois todas as vagas  poderão estar preenchidas.</p>
						<p><i>Ore conosco para multiplicar a quantidade e qualidade de pastores-treinadores.</i></p>
						<p>Calorosamente,</p><p>Equipe do GProCongresso II</p>';

					}else{
					
						$url = '<a href="'.url('exhibitor-index').'" target="_blank">website</a>';
						$subject = 'GProCongress II is now taking applications for Exhibitors.';
						$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>You are receiving this email because you have registered and fully paid for GProCongress II.  Now you have the opportunity to apply to be an Exhibitor at the Congress!</p>
						<p>If you have a product or service that would benefit pastor trainers around the world, then please login to your account on our '.$url.'.  After you login, go to the menu bar at the top of the page, click on “Exhibitors” and fill out the application today.</p>
						<p>We ask that you apply as soon as possible, because space is limited, and exhibitors are chosen on a “first pay, first come” basis.  Accordingly, if you wait too long to apply and make payment, you could be left out of the Congress as an exhibitor, because all slots could already be full.</p>
						<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers.</i></p>
						<p>Warmly,</p><p>GProCongress II Team</p>';
		
					}

					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II is now taking applications for Exhibitors.');
				
				}
				
				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function registrationFeeUntilJune30(Request $request){
		
		try {
			
			$results = \App\Models\User::where('designation_id', '!=', '14')->where('user_type', '=', '2')->where('email_reminder','1')->where('email','!=','aamillogo@gmail.com')->where('stage','<=','2')->get();
			// echo "<pre>";
			// print_r($results->toArray()); die;
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					
					$Spouse = \App\Models\User::where('parent_id',$user->id)->where('added_as','Spouse')->first(); 

					$SpouseParent = \App\Models\User::where('id',$user->parent_id)->first();
			
					if($Spouse){
						$amount = $user->amount;
			
					}elseif($SpouseParent && $user->added_as == 'Spouse'){
			
						$amount = $SpouseParent->amount;
			
					}else{
			
						$amount = $user->amount;
					}

					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$amount.','.$user->email.','.$user->stage.','.$user->parent_id.'<br>';

					if($user->language == 'sp'){

						$url = '<a href="'.url('payment').'" target="_blank">enlace</a>';
						$subject = 'RECORDATORIO: ¡$100 de descuento en lel costo de inscripción para el GProCongress II hasta el 30 de junio!';
						$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>El pago de su asistencia al GProCongress II vence ahora. Vaya a '.$url.' y realice su pago ahora.</p>
						<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
						<p>Su nuevo monto de pago es $'.$amount.' (debe pagarse en su totalidad antes del 30 de junio).</p>
						<p>Si tiene alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
						<p><i>Ore con nosotros para multiplicar la cantidad y calidad de capacitadores de pastores.</i></p>
						<p>Cordialmente,</p><p>Equipo GProCongress II</p>';

					}elseif($user->language == 'fr'){
					
						$url = '<a href="'.url('payment').'" target="_blank">lien</a>';
						$subject = "RAPPEL - 100 $ de rabais sur les frais d’inscription au GProCongress II jusqu’au 30 juin!";
						$msg = '<p>Cher  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Le paiement de votre participation au GProCongress II est maintenant dû. Prière d’aller sur '.$url.', et effectuer votre paiement maintenant.</p>
						<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
    					<p>Le montant de votre nouveau paiement est de '.$amount.' $ (doit être payé en totalité au plus tard le 30 juin).</p>
						<p>Si vous avez des questions sur votre paiement, ou si vous souhaitez parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</p>
						<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</i></p>
						<p>Cordialement,</p><p>L’équipe GProCongress II</p>';

					}elseif($user->language == 'pt'){
					
						$url = '<a href="'.url('payment').'" target="_blank">link</a>';
						$subject = 'LEMBRETE – $100 de desconto na taxa de inscrição do GProCongresso II até 30 de junho!';
						$msg = '<p>Caro '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>O pagamento da sua participação no GProCongresso II está vencido. Acesse '.$url.' e faça seu pagamento agora.</p>
						<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
    					<p>Seu novo valor de pagamento é $'.$amount.' (deve ser pago integralmente até 30 de junho).</p>
						<p>Se você tiver alguma dúvida sobre como fazer seu pagamento ou se precisar falar com um dos membros de nossa equipe, basta responder a este e-mail.</p>
						<p><i>Ore conosco para multiplicar a quantidade e qualidade de pastores-treinadores.</i></p>
						<p>Calorosamente,</p><p>Equipe do GProCongresso II</p>';

					}else{
					
						$url = '<a href="'.url('payment').'" target="_blank">link</a>';
						$subject = 'REMINDER – $100 off GProCongress II registration fee until June 30!';
						$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Payment for your attendance at GProCongress II is now due. Please go to '.$url.', and make your payment now.</p>
						<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
						<p>Your new payment amount is $'.$amount.' (must be paid in full by June 30).</p>
						<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
						<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers.</i></p>
						<p>Warmly,</p><p>GProCongress II Team</p>';
		
					}

					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'REMINDER – $100 off GProCongress II registration fee until June 30!');
				
				}

				echo "<pre>";
				print_r($resultData); die;
				
				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function juneOfferStage01(Request $request){
		
		try {
			
			$results = \App\Models\User::where('designation_id', '!=', '14')->where('profile_status', '!=', 'ApprovedNotComing')->where('user_type', '=', '2')->where('email_reminder','1')->where('stage','<=','1')->get();
			// echo "<pre>";
			// print_r($results->toArray()); die;
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';

					if($user->language == 'sp'){

						$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
						$subject = 'Candidatos aprobados  ¡Descuento de USD 100 en el costo de la inscripción al GProCongress II hasta el 30 de junio!';
						$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Ya ha oído hablar del descuento de 100 dólares en el costo de inscripción regular hasta el 30 de junio.</p>
						<p>Sin embargo, queremos animarle a que siga adelante y complete el formulario de inscripción con todos sus datos para que nuestro equipo los pueda revisar.</p>
						<p>Pasos sencillos a seguir:</p>
						<p>&nbsp;&nbsp;&nbsp;1. Ya ha creado una cuenta con nosotros.</p>
						<p>&nbsp;&nbsp;&nbsp;2. Inicie sesión y complete su solicitud respondiendo a todas las preguntas. Si no recuerda la antigua contraseña, puede restablecerla.</p>
						<p>&nbsp;&nbsp;&nbsp;3. Nuestro equipo revisará y aprobará su solicitud. Le informaremos por correo electrónico si está aprobada o no.</p>
						<p>&nbsp;&nbsp;&nbsp;4. Una vez aprobada su solicitud, puede proceder al pago y beneficiarse del descuento de USD 100 sobre el precio de inscripción regular hasta el 30 de junio.</p>
						<p>Haga clic aquí para comprobar el precio para su país:  '.$url.' </p>
						<p>Si tienes alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
						<p><i>Ore con nosotros para multiplicar la cantidad y calidad de capacitadores de pastores.</i></p>
						<p>Atentamente, </p><p>Equipo GProCongreso II</p>';

					}elseif($user->language == 'fr'){
					
						$url = '<a href="'.url('pricing').'" target="_blank">lien</a>';
						$subject = "Les candidats approuvés bénéficient d'une réduction de 100 $ sur les frais d'inscription à GProCongress II jusqu'au 30 juin !";
						$msg = "<p>Cher  ".$user->name." ".$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Vous avez déjà entendu la nouvelle de la réduction de 100 $ sur les frais d'inscription normaux jusqu'au 30 juin.</p>
						<p>Cependant, nous souhaitons vous encourager à remplir le formulaire d'inscription complet avec toutes les informations nécessaires pour que notre équipe puisse l'examiner.</p>
						<p>Les étapes simples à suivre :</p>
						<p>&nbsp;&nbsp;&nbsp;1. Vous avez déjà créé un compte avec nous.</p>
						<p>&nbsp;&nbsp;&nbsp;2. Veuillez vous connecter et compléter votre demande en répondant à toutes les questions. Vous pouvez réinitialiser votre mot de passe si vous ne vous souvenez plus de l'ancien.</p>
						<p>&nbsp;&nbsp;&nbsp;3. Notre équipe examinera et approuvera votre demande en conséquence. Nous vous informerons par e-mail de l'acceptation ou du rejet de votre demande.</p>
						<p>&nbsp;&nbsp;&nbsp;4. Une fois votre candidature approuvée, vous pouvez procéder au paiement et bénéficier d'une réduction de 100$ sur les frais d'inscription jusqu'au 30 juin.</p>
						<p>Cliquez ici pour vérifier le prix de votre pays : ".$url." </p>
						<p>Si vous avez des questions concernant le paiement, ou si vous souhaitez parler à l'un des membres de notre équipe, répondez simplement à cet e-mail.</p>
						<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</i></p>
						<p>Chaleureusement,</p><p>L'équipe de GProCongress II</p>";

					}elseif($user->language == 'pt'){
					
						$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
						$subject = 'Candidatos aprovados qualificados para desconto de $100 dólares na taxa de inscrição do GProCongresso II até 30 de Junho!';
						$msg = '<p>Prezado '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>Já ouviu falar do desconto de 100 dólares na taxa de inscrição regular até 30 de Junho.</p>
						<p>No entanto, queremos incentivá-lo a preencher o formulário de inscrição completo com todas as informações para que a nossa equipa possa analisar suas informções.  </p>
						<p>Passos simples a seguir:</p>
						<p>&nbsp;&nbsp;&nbsp;1. Você já criou uma conta connosco.</p>
						<p>&nbsp;&nbsp;&nbsp;2. Inicie sessão, preencha o seu formulário de inscrição e responda a todas as perguntas. Pode redefinir a sua senha se não se lembrar da antiga.</p>
						<p>&nbsp;&nbsp;&nbsp;3. A nossa equipa irá analisar e aprovar a sua inscrição em conformidade. Vamos lhe informar por e-mail se foi ou não aprovado.</p>
						<p>&nbsp;&nbsp;&nbsp;4. Assim que a sua inscrição for aprovada, pode prsseguir para o pagamento e se beneficiar com o desconto de 100 dólares no custo da inscrição regular até 30 de Junho.</p>
						<p>Clique aqui para verificar o valor para o seu país: '.$url.' </p>
						<p>Se tiver alguma dúvida sobre como efetuar o pagamento ou se precisar falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
						<p><i>Ore connosco para multiplicar a quantidade e a qualidade dos pastores-formadores.</i></p>
						<p>Cordialmente,</p><p>Equipe do GProCongresso II</p>';

					}else{
					
						$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
						$subject = 'Approved applicants qualify for $100 off GProCongress II registration fee until June 30!';
						$msg = '<p>Dear '.$user->name.' '.$user->last_name.' ,&nbsp;</p><p><br></p>
						<p>You already heard about the $100 off the Regular Registration rate until June 30.</p>
						<p>However, we want to encourage you to go ahead and fill out the full registration form with all the information so our team can review it.</p>
						<p>Simple steps to follow:</p>
						<p>&nbsp;&nbsp;&nbsp;1. You already created an account with us.</p>
						<p>&nbsp;&nbsp;&nbsp;2. Please login and complete your application and answer all the questions. You can reset your password if you do not remember the old one.</p>
						<p>&nbsp;&nbsp;&nbsp;3. Our team will review and approve your application accordingly. We will inform you via email if it’s approved or not.</p>
						<p>&nbsp;&nbsp;&nbsp;4. Once your application is approved, you can proceed with the payment and avail $100 off the Regular Registration cost until June 30.</p>
						<p>Click here to check the price for your country: '.$url.' </p>
						<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email. </p>
						<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers.</i></p>
						<p>Warmly,</p><p>GProCongress II Team</p>';
		
					}

					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Approved applicants | Qualify for $100 off GProCongress II registration fee until June 30!');
				
				}

				echo "<pre>";
				print_r($resultData); die;
				
				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function passportInfoSubmitEmail(Request $request){
		
		try {
			
			$results = \App\Models\User::where('designation_id', '!=', '14')->where('profile_status', '!=', 'ApprovedNotComing')->where('user_type', '=', '2')->where('stage','=','3')->get();
			// echo "<pre>";
			// print_r($results->toArray()); die;
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';

					if($user->language == 'sp'){

						$url = '<a href="'.url('pricing').'" target="_blank">enlace</a>';
						$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
						$msg = "<p>Estimado ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
						<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
						<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
						<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
						<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
						<p>Atentamente,</p><p>Equipo de GProCongress II</p>";

					}elseif($user->language == 'fr'){
					
						$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
						$msg = "<p>Cher  ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
						<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
						<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
						<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
						<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
						<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";

					}elseif($user->language == 'pt'){
					
						$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
						$msg = "<p>Caro ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
						<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
						<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
						<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
						<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
						<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
						<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
						<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";

					}else{
					
						$url = '<a href="'.url('pricing').'" target="_blank">link</a>';
						$subject = 'GProCongress II registration!  Please login and submit your passport information.';
						$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
						<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
						<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
						<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
						<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
						<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
						<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
						<p>Warmly,</p><p>GProCongress II Team</p>";
		
					}

					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';


					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
				
				}

				echo "<pre>";
				print_r($resultData); die;
				
				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function PassportInfoUpdateUsersApi(Request $request){
		
		try {
			
			
			$passportInfoUsers =  \App\Models\PassportInfo::where('visa_granted',NULL)->get();

			if(!empty($passportInfoUsers) && count($passportInfoUsers)>0){

				foreach($passportInfoUsers as $val){

					$passportInfo =  \App\Models\PassportInfo::where('id',$val->id)->first();

					$doNotRequireVisa = ['82','6','10','194','11','12','14','15','17','20','22','23','21','255','27','28','29','31','33','34','26','37','39','44','57','238','48','53','55','59','61','64','66','231','200','201','207','233','69','182','73','74','75','79','81','87','90','94','97','98','99','232','105','100','49','137','202','106','107','108','109','114','117','120','125','126','127','251','130','132','133','135','140','142','143','144','145','146','147','152','153','159','165','158','156','168','171','172','173','176','177','179','58','256','252','116','181','191','185','192','188','253','196','197','199','186','204','213','214','219','216','222','223','225','228','230','235','237','240','170']; 
					$RequireVisa = ['1','3','4','7','16','18','19','24','35','36','43','115','54','65','68','70','80','93','67','102','103','104','40','111','112','118','248','119','122','121','123','124','134','149','139','150','151','154','160','161','166','167','169','51','183','195','198','215','203','208','209','210','217','218','224','226','229','236','113','245','254','246','2','5','8','9','13','25','30','32','41','46','47','52','60','63','71','72','76','77','78','84','85','86','88','89','91','92','96','110','128','129','136','138','141','148','155','157','162','163','164','175','178','180','184','187','189','190','193','205','206','211','221','227','234','241','242','243','244','249','250']; 
					$restricted = ['38','45','56','62','174','83','95','101','131','42','50','212','220','239','247']; 
					$visa_category = 'No Visa Needed';
					
					if(in_array($passportInfo->country_id,$doNotRequireVisa)){
	
						if($passportInfo->diplomatic_passport == 'Yes'){
	
							$visa_category = 'No Visa Needed';
	
						}else{
	
							if($passportInfo->visa_residence == 'Yes'){
								
								if($passportInfo->multiple_entry_visa == 'Yes'){
								
									if($passportInfo->multiple_entry_visa_country == 'Yes'){
								
										if($passportInfo->passport_valid == 'Yes'){
								
											$visa_category = 'No Visa Needed';
								
										}else{
	
											$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
										}
								
									}else{
	
										$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
									}
								
								}else{
	
									$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
								}
	
							}else{
	
								$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
	
							}
							
						}
						
	
					}else{
	
						if($passportInfo->visa_residence == 'Yes'){
								
							if($passportInfo->multiple_entry_visa == 'Yes'){
							
								if($passportInfo->multiple_entry_visa_country == 'Yes'){
							
									if($passportInfo->passport_valid == 'Yes'){
							
										$visa_category = 'No Visa Needed';
							
									}else{
	
										$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
									}
							
								}else{
	
									$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
								}
							
							}else{
	
								$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
							}
	
						}else{
	
							$visa_category = \App\Helpers\commonHelper::checkUserPassPortCategoryacordingToCountry($passportInfo->country_id);
						}
						
					}
	
					$passportInfo->visa_category = $visa_category;
					$passportInfo->save();

				}
				

			}

			return response(array("message"=>'Data Saved Success'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function PassportInfoUpdateAdminProvideUserNameApi(Request $request){
		
		try {
			
			$passportInfoUsers =  \App\Models\PassportInfo::where('admin_status','Approved')->
			where([['id','=','16']])
			->orwhere('id','=','24')
			->orwhere('id','=','30')
			->orwhere('id','=','37')
			->orwhere('id','=','45')
			->orwhere('id','=','47')
			->orwhere('id','=','53')
			->orwhere('id','=','62')
			->get();
			// echo "<pre>";
			// print_r($passportInfoUsers->toArray()); die;
			if(!empty($passportInfoUsers) && count($passportInfoUsers)>0){

				foreach($passportInfoUsers as $val){

					$passportInfo =  \App\Models\PassportInfo::where('id',$val->id)->first();

					$passportInfo->admin_provide_name = null;
					$passportInfo->admin_provide_email = null;
					$passportInfo->save();

					// $user= \App\Models\User::where('id',$passportInfo->user_id)->first();


					// if($passportInfo->visa_category == 'No Visa Needed'){

					// 	\App\Helpers\commonHelper::sendFinancialLetterMailSend($passportInfo->user_id,$val->id,'financial');  // 2 letter acc, bank

					// 	if($user->language == 'sp'){

					// 		$subject = 'Por favor, envíe la información de su vuelo para GProCongress II.';
					// 		$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					// 		<p>Inicie sesión en su cuenta en el sitio web de GProCongress lo antes posible y responda las preguntas de la Etapa 3 para brindarnos la información de su vuelo para su viaje a Panamá.</p>
					// 		<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, responda a este correo electrónico.</p><p><br></p>
					// 		<p>Atentamente,</p><p>Equipo GProCongress II&nbsp; &nbsp;&nbsp;</p>';
				
					// 	}elseif($user->language == 'fr'){
						
					// 		$subject = 'Veuillez soumettre les informations relatives à votre vol pour le GProCongress II.';
					// 		$msg = '<p>Cher  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					// 		<p>Veuillez vous connecter à votre compte sur le site Web du GProCongress dès que possible et répondre aux questions de l’étape 3 afin de nous fournir les informations relatives à votre vol pour votre voyage au Panama.</p>
					// 		<p>Si vous avez des questions ou si vous souhaitez parler à l’un des membres de notre équipe, veuillez répondre à cet e-mail.&nbsp;</p><p><br></p>
					// 		<p>Cordialement,</p><p>L’équipe GProCongress II&nbsp; &nbsp;&nbsp;</p>';
				
					// 	}elseif($user->language == 'pt'){
						
					// 		$subject = 'Por favor, envie suas informações de voo para o GProCongresso II.';
					// 		$msg = '<p>Caro '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					// 		<p>Faça login em sua conta no site do GProCongresso o mais rápido possível e responda às perguntas da Etapa 3, para nos fornecer suas informações de voo para sua viagem ao Panamá.</p>
					// 		<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.&nbsp;</p><p><br></p>
					// 		<p>Calorosamente,</p><p>Equipe GProCongresso  II&nbsp; &nbsp;&nbsp;</p>';
				
					// 	}else{
						
					// 		$subject = 'Please submit your flight information for GProCongress II';
					// 		$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					// 		<p>Please login to your account at the GProCongress website as soon as possible, and answer the questions under Stage 3, to give us your flight information for your trip to Panama.</p>
					// 		<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.&nbsp;</p><p><br></p>
					// 		<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
											
					// 	}

					// 	\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

					// 	\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					// 	\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id, $subject, $msg, 'Please submit your flight information for GProCongress II');
				

					// }elseif($passportInfo->visa_category == 'Visa Needed'){

					// 	\App\Helpers\commonHelper::sendSponsorshipLetterMailSend($passportInfo->user_id,$val->id);  // 4 letter

					// }elseif($passportInfo->visa_category == 'Restricted Country'){

					// 	\App\Helpers\commonHelper::sendSponsorshipLetterRestrictedMailSend($passportInfo->user_id,$val->id);  // 4 letter

					// }
				}
				

			}

			return response(array("message"=>'Data Saved Success'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function testingEmailSend(Request $request){
		
		try {
			
			$results = \App\Models\User::where('email', 'gopalsaini.img@gmail.com')->get();
			// echo "<pre>";
			// print_r($results->toArray()); die;
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					if($user->language == 'sp'){

						$subject = 'Su reembolso del GProCongress II ha sido procesado.';
						$msg = "<p>Estimado ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Sentimos mucho que no pueda asistir al GProCongress este noviembre en Panamá.   Hemos recibido su solicitud de reembolso, y ha sido procesada por nuestro equipo de administración.   Pronto recibirás tu reembolso.</p>
						<p>Si tiene alguna pregunta, o si necesita hablar con un miembro de nuestro equipo, por favor responda a este correo electrónico.</p>
						<p>Un saludo cordial,</p><p>Equipo GProCongress II</p>";

					}elseif($user->language == 'fr'){
					
						$subject = "Votre remboursement GProCongress II a été traité.";
						$msg = "<p>Cher  ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Nous sommes désolés que vous ne puissiez pas vous rendre à Panama pour le GProCongress de novembre. Nous avons reçu votre demande de remboursement, qui a été traitée par notre équipe administrative. Vous recevrez votre remboursement très bientôt.</p>
						<p>Si vous avez des questions ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet e-mail.</p>
						<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";

					}elseif($user->language == 'pt'){
					
						$subject = 'Seu reembolso do GProCongresso II foi processado.';
						$msg = "<p>Caro ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Lamentamos muito que você não possa ir ao Panamá para o GProCongresso neste Novembro. Recebemos sua solicitação de reembolso e ela foi processada por nossa equipe administrativa. Você receberá seu reembolso em breve.</p>
						<p>Se você tiver alguma dúvida ou precisar falar com um dos membros de nossa equipe, responda a este e-mail.</p>
						<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";

					}else{
					
						$subject = 'Your GProCongress II refund has been processed.';
						$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>We are very sorry that you cannot make it to Panama for the GProCongress this November.   We have received your request for a refund, and it has been processed by our admin team.   You will be receiving your refund shortly.</p>
						<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
						<p>Warmly,</p><p>GProCongress II Team</p>";
		
					}


					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
				
				}

				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	//dashboard API

	public function getPayments(){
          
		$prices = array(
		  'Fully Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '1']])->count() ?? 0,
		  'Partially Paid' => \App\Models\User::where([['user_type', '!=', '1'], ['payment_status', '0']])->count() ?? 0
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getPayments = json_encode($prices);
			$chartData->save();		
		}
		
		$country = [["Element", "No. of Users" ]];
		
		$userCountry = \App\Models\User::selectRaw('count(*) as count, region')
			->where([['users.user_type', '!=', '1'], ['users.designation_id', 2]])
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
		
		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getUserByContinents = json_encode($country);
			$chartData->save();		
		}
		
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

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getUserByUserAge = json_encode($stages);
			$chartData->save();		
		}
		
		
        $stages1 = array(
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

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getStages = json_encode($stages1);
			$chartData->save();		
		}
		
		
		$totalGroup1 = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group']])->groupBy('parent_id')->count();
		$totalGroup = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group']])->get();
		$totalUser = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group']])->count();
	   
		if(!empty($totalGroup) && count($totalGroup)>0){
		  $array =[];

			foreach($totalGroup as $key=>$Groups){

			  $array[$key]= $Groups->parent_id;

		  }

		  $array = count(array_unique($array));

		}

		$stages2 = array(
			  'Total Group' => $array,
			  'Total Group Candidates' => $totalUser+$array,
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getGroupRegisteredChartAjax = json_encode($stages2);
			$chartData->save();		
		}
		
		
		$stages3 = array(
			  'Single' => \App\Models\User::where([['designation_id', 2], ['room', 'Single']])->count(),
			  'Twin Sharing' => \App\Models\User::where([['designation_id', 2], ['room', 'Sharing']])->orWhere('room','Twin Sharing Deluxe Room')->count(),
			  'Suite' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Suite']])->count(),
			  'Club Floor' => \App\Models\User::where([['designation_id', 2], ['room', 'Upgrade to Club Floor']])->count(),
			  'Double Deluxe' => \App\Models\User::where([['designation_id', 2],['added_as', 'Spouse'], ['room', null]])->count(),
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getSingleMarriedWSChartAjax = json_encode($stages3);
			$chartData->save();		
		}
		
		
		$Singles = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['marital_status', 'Married']])->get();
		if(!empty($Singles) && count($Singles)>0){

		  $BothTotal = 0; $singleTotal = 0;$nonTrainerCount = 0; $AspirationalBothTotal = 0;  $PastoralAndAspirational = 0; $singleAspirationalTotal=0;
			foreach($Singles as $Single){

				$user = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['parent_id', $Single->id]])->first();

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

		$stages4 = array(
			  'Pastoral Trainer - Both' => $BothTotal,
			  'Aspirational Trainer- Both' => $AspirationalBothTotal,
			  'Pastoral Trainer and Aspirational Trainer' => $PastoralAndAspirational,
			  'Pastoral Trainer and Not a Trainer' => $singleTotal,
			  'Aspirational Trainer and Not a Trainer' => $singleAspirationalTotal,
			  'Not Trainers - Both' => $nonTrainerCount,
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getMarriedWSChartAjax = json_encode($stages4);
			$chartData->save();		
		}
		
		
		$Pastoral = 0; $Aspirational = 0; $Not = 0;
		$Singles = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->get();
		if(isset($Singles)){

			foreach($Singles as $Single){

				$user = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse'], ['parent_id', $Single->id]])->first();
				
				if(!$user && $Single->ministry_pastor_trainer == 'Yes'){

					$Pastoral++;

				}else if(!$user && $Single->doyouseek_postoral == 'Yes'){
				  
					$Aspirational++;

				}else if(!$user &&  $Single->doyouseek_postoral == 'No'){

					$Not++;
				}else if($Single->ministry_pastor_trainer == 'No'){

				  $Not++;
			  }
			}

		}
	  
		$stages5 = array(
			  'Pastoral Trainer' => $Pastoral,
			  'Aspirational trainer' => $Aspirational,
			  'Not a Trainer' => $Not,
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getPastoralTrainersChartAjax = json_encode($stages5);
			$chartData->save();		
		}
		
		
        $totalPendingAmount = 0;

        $results = \App\Models\User::where('profile_status','Approved')->where('stage','2')->get(); 
        if($results){

            foreach($results as $val){
                $totalPendingAmount +=\App\Helpers\commonHelper::getTotalPendingAmount($val->id);
            }
        }
        
        $stages6 = array(
                'Pending' => round($totalPendingAmount),
                'Declined' => round(\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Failed']])->sum('amount')),
                'In Process' => round(\App\Models\Transaction::where([['status', '=', Null]])->sum('amount')),
                'Accepted' => round(\App\Models\Wallet::where([['type', '=', 'Cr'], ['status', '=', 'Success']])->sum('amount')),
                
        );

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getPaymentChartAjax = json_encode($stages6);
			$chartData->save();		
		}
		
		
        $stages7 = array(
                'Credit/Debit Card' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'Card']])->count(),
                'Western Union' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'WU']])->count(),
                'MG' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'MG']])->count(),
                'RIA' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'RIA']])->count(),
                'Bank Wire Transfer' => \App\Models\Transaction::where([['status', '=', '1'],['bank', '=', 'Wire']])->count(),
        );

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getPaymentTypeChartAjax = json_encode($stages7);
			$chartData->save();		
		}
		
		
        $yes = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'No'],  ['doyouseek_postoral', 'Yes']])->count();
        
        $no = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['ministry_pastor_trainer', 'No'], ['doyouseek_postoral', 'No']])->count();
        
        $stages8 = array(
              'Yes' => $yes,
              'No' => $no,
        );

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->getDoYouSeekPastoralTraining = json_encode($stages8);
			$chartData->save();		
		}
		
		
		$totalGroup = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group']])->get();
		$totalUser = \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Group']])->count();
	  
		if(!empty($totalGroup) && count($totalGroup)>0){
		  $array =[];

			foreach($totalGroup as $key=>$Groups){

			  $array[$key]= $Groups->parent_id;

		  }

		  $array = count(array_unique($array));

		}

		$stages9 = array(
			  'Total Group' => $array,
			  'Total Group Candidates' => $totalUser+$array,
		);

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->TotalGroupRegistration = json_encode($stages9);
			$chartData->save();		
		}
		
		
        $stages10 = array(
              'Total Married couples' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse']])->count(),
              'Total Married Candidates' => (\App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['added_as', 'Spouse']])->count())*2,
        );

		
		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->TotalMarriedCouples = json_encode($stages10);
			$chartData->save();		
		}
		
		
		$marriedWithoutSpouseCount = 0;
		$unmarriedCount = 0;
		$users = \App\Models\User::where('user_type', '!=', 1)
			->where('designation_id', 2)
			->whereIn('marital_status', ['Married', 'Unmarried'])
			->get();

		foreach ($users as $user) {
			if ($user->marital_status === 'Married' && $user->added_as !== 'Spouse') {
				$marriedWithoutSpouseCount++;
			} else if ($user->marital_status === 'Unmarried') {
				$unmarriedCount++;
			}
		}

		$stages11 = [
			'Single' => $unmarriedCount,
			'Married coming without Spouse' => $marriedWithoutSpouseCount,
		];

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->SingleMarriedComing = json_encode($stages11);
			$chartData->save();		
		}
		
		
		$userPaid = [];
		$candidatesWSPaid = 0;
		$candidatesPartiallyPaid = 0;
		$candidatesWSPartiallyPaid = 0;
		$candidatesNotPaid = 0;
		$totalUser = 0;

		$results = \App\Models\User::where([
				['user_type', '!=', '1'],
				['designation_id', 2],
			])
			->where('profile_status', 'Approved')
			->where(function ($query) {
				$query->where('added_as', null)
					->orWhere('added_as', '=', 'Group')
					->orWhere('parent_spouse_stage', '>=', '2');
			})
			->get();

		if ($results) {
			foreach ($results as $val) {
				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($val->id);
				
				if ($totalPendingAmount <= 0) {
					$candidatesWSPaid++;
				} elseif ($totalPendingAmount < $val->amount) {
					$candidatesPartiallyPaid++;
					$candidatesWSPartiallyPaid++;
				} else {
					$candidatesNotPaid++;
				}

				if (!in_array($val->id, $userPaid)) {
					$userPaid[] = $val->id;
				}

				$spouse = \App\Models\User::where([
					['parent_id', '=', $val->id],
					['added_as', '=', 'Spouse'],
				])->first();

				if ($spouse && !in_array($spouse->id, $userPaid)) {
					$userPaid[] = $spouse->id;
					if ($totalPendingAmount <= 0) {
						$candidatesWSPaid++;
					} elseif ($totalPendingAmount < $val->amount) {
						$candidatesWSPartiallyPaid++;
					} else {
						$candidatesNotPaid++;
					}
				}
			}
		}

		$stages12 = [
			'candidatesWSPaid' => $candidatesWSPaid,
			'candidatesPartiallyPaid' => $candidatesPartiallyPaid,
			'candidatesWSPartiallyPaid' => $candidatesWSPartiallyPaid,
			'candidatesNotPaid' => $candidatesNotPaid,
			'TotalCandidatesRegisteredWS' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count(),
			'STAGEALL' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2]])->count(),
			'STAGE0' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '0']])->count(),
			'STAGE1' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1']])->where(function ($query) {
								$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
							})->count(),

			'STAGE1Pending' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Review']])->where(function ($query) {
								$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
							})->count(),
			
			'STAGE1Waiting' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Waiting']])->where(function ($query) {
				$query->where('added_as',null)
				  ->orWhere('added_as', '=', 'Group');
			  })->count(),
			
			'STAGE1Declined' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'Rejected']])->where(function ($query) {
				$query->where('added_as',null)
				  ->orWhere('added_as', '=', 'Group');
			  })->count(),
			
			'STAGE1ApproveNotComing' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '1'], ['profile_status', 'ApprovedNotComing']])->where(function ($query) {
				$query->where('added_as',null)
				  ->orWhere('added_as', '=', 'Group');
			  })->count(),
			
			'STAGE2WOSpouse' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])
			->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			})->count(),

			'STAGE2WithSpouse' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '2']])->count(),
			'STAGE3' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '3']])->count(),
			'STAGE4' => \App\Models\User::where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '4']])->count(),
			'STAGE5' => \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['user_type', '!=', '1'], ['designation_id', 2], ['stage', '=', '5']])->count(),
		];

		$chartData = \App\Models\ChartDataDB::where('id','1')->first();
		if($chartData){
			$chartData->DashBoardData = json_encode($stages12);
			$chartData->save();		
		}
		echo "Done";

	}

	public function CountryList2(Request $request){
 
		try{

			$result=[];
			
			$countries=\DB::table('countries_2')->orderBy('name','Asc')->get();
			
			if(!empty($countries) && count($countries)>0){

				foreach($countries as $country){

					
					$result[]=[

						'id'=>$country->id,
						'name'=>$country->name2,
					];
				}
				
			}
			
			
			return response(array("message" => 'data fetched successfully.','result'=>$result,'error'=>false),200); 
			
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}

	public function visaProcessingEmailSend(Request $request){
		
		try {
			
			$results = \App\Models\User::where('designation_id', '!=', '14')->where('profile_status', '!=', 'ApprovedNotComing')->where('user_type', '=', '2')->where('stage','=','3')->where('email_reminder','1')->get();
			// echo "<pre>";
			// print_r($results->toArray()); die;
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					if($user->language == 'sp'){

						$subject = 'Tramitación de visados';
						$msg = '<p >Estimados Participantes,
									<br>
								</p>
						<p>A medida que se acerca el GProCongress II, necesitamos aclarar un asunto importante relacionado con el trámite de visas para aquellos que planean asistir desde el exterior a Panamá. Queremos enfatizar que subir la información de su pasaporte en la página web gprocongress.org NO significa que RREACH esté tramitando la solicitud de visa para Panamá en su nombre.  Solamente necesitamos esa información sobre su llegada programada al Congreso para ayudarnos en los preparativos de dicha asistencia.  <b><u>Para obtener la visa, se le pedirá que inicie y complete el proceso de visado usted mismo.</u></b></p>
						<p>Para iniciar el trámite de visado, deberá seguir los siguientes pasos:</br>
							<p>&nbsp;&nbsp;1. Póngase en contacto con la embajada o consulado panameño en su país o región lo antes posible. Ellos le guiarán a través del proceso de solicitud de visa y le proporcionarán el procedimiento que debe seguir. Visite https://mire.gob.pa/ministerio/embajadasyconsulados para conocer la ubicación e información de contacto de las embajadas panameñas en todo el mundo.</p>
							<p>&nbsp;&nbsp;2. Aunque no podemos tramitar directamente su visa, le proporcionamos dos documentos de apoyo que le ayudarán con su solicitud. Estos documentos servirán como prueba para respaldar su solicitud de visa y demostrar su participación en el GProCongress.  También le hemos proporcionado una lista de los documentos necesarios para solicitar su visa, así como el formulario de solicitud de visa que debe entregar a la embajada.</p>
							<p>&nbsp;&nbsp;3. Si viaja desde un país con visado restringido, el departamento de inmigración le concertará una cita para que presente su solicitud de visa y realice los trámites necesarios. Por favor, esté preparado para seguir todas sus instrucciones y normas. Cuando nos envíe su pasaporte y sus datos personales, un miembro de nuestro equipo se pondrá en contacto con usted para guiarle en el proceso.</p>
						<p><u>Entendemos que el proceso de tramitación de la visa puede ser complejo y llevar mucho tiempo. Por ello, le recomendamos que inicie los trámites de solicitud con suficiente antelación para garantizar un proceso fluido y sin demoras. Si tiene alguna pregunta o necesita ayuda durante el proceso de solicitud de visado, póngase en contacto con nuestro equipo de asistencia. Estamos aquí para orientarle y ayudarle a que su viaje al GProCongress sea lo más sencillo posible. Envíe un correo electrónico a Nadine a nadine@gprocongress.org, si necesita ayuda o tiene alguna pregunta.</u> </p>
						<p>Apreciamos su atención y esperamos darle la bienvenida al GProCongreso en Panamá. Juntos, busquemos al Señor con miras al GProCongress II, a fin de fortalecer y multiplicar a los capacitadores de pastores para décadas de impacto evangélico.</p>
						<p>Saludos cordiales,</p>
						<p><img src="'.asset('images/rajiv_richard.png').'" style="width:100px;"></p><br>
						<p>Rajiv Richard</p> <p>GProCongress II Team</p>';

					}elseif($user->language == 'pt'){
					
						$subject = "Processamento de vistos";
						$msg = '<p >Prezados Participantes,
									<br>
								</p>
						<p>À medida que o GProCongresso II se aproxima, precisamos esclarecer uma questão importante sobre o processamento de vistos para aqueles que pretendem participar de fora do Panamá. Queremos enfatizar que o upload das informações do seu passaporte no site gprocongress.org NÃO significa que a RREACH está processando o pedido de visto para o Panamá em seu nome. Precisamos dessas informações sobre sua chegada planejada ao Congresso para nos ajudar na preparação para sua participação. <b><u>Para obter um visto, você deverá iniciar e concluir o processo de visto por conta própria</u></b></p>
						<p>Para iniciar o processo de visto, você deve seguir os seguintes passos:</br>
							<p>&nbsp;&nbsp;1. Entre em contato com a embaixada ou consulado do Panamá em seu país ou região o mais rápido possível. Eles irão guiá-lo através do processo de solicitação de visto e fornecer-lhe o procedimento que você precisa seguir. Visite https://mire.gob.pa/ministerio/embajadasyconsulados para obter os locais e informações de contato das embaixadas panamenhas em todo o mundo. </p>
							<p>&nbsp;&nbsp;2. Embora não possamos processar seu visto diretamente, estamos fornecendo dois documentos de apoio que o ajudarão com sua solicitação. Esses documentos servirão como prova para apoiar seu pedido de visto e demonstrar sua participação no GProCongresso. Também fornecemos uma lista dos documentos necessários para solicitar seu visto, bem como o formulário de solicitação de visto que deve ser entregue na embaixada.</p>
							<p>&nbsp;&nbsp;3. Se você estiver viajando de um país com visto restrito, o departamento de imigração agendará um horário para você enviar seu pedido de visto e concluir as formalidades necessárias. Por favor, esteja preparado para seguir todas as suas instruções e orientações. Quando nos enviar o seu passaporte e os seus dados pessoais, um membro da nossa equipe entrará em contato com você para lhe orientar no processo.</p>
						<p><u>Entendemos que o processamento de vistos pode ser um processo complexo e demorado. Portanto, recomendamos iniciar o procedimento de inscrição com bastante antecedência para garantir um processo tranquilo e oportuno. Se você tiver dúvidas ou precisar de assistência durante o processo de solicitação de visto, entre em contato com nossa equipe de suporte dedicada. Estamos aqui para fornecer orientação e suporte para tornar sua jornada para o GProCongresso  mais tranquila possível. Envie um e-mail para Nadine em nadine@gprocongress.org, se precisar de ajuda ou tiver alguma dúvida.</u> </p>
						<p>Agradecemos sua atenção a este assunto e esperamos recebê-lo no GProCongresso no Panamá. Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os treinadores de pastores por décadas de impacto no evangelho.</p>
						<p>Atenciosamente,</p>
						<p><img src="'.asset('images/rajiv_richard.png').'" style="width:100px;"></p><br>
						<p>Rajiv Richard</p> <p>GProCongress II Team</p>';

					}elseif($user->language == 'fr'){
					
						$subject = 'Traitement des visas';
						$msg = "<p >Chers participants,
									<br>
								</p>
						<p>À l'approche de GProCongress II, nous devons clarifier un aspect important concernant le traitement des visas pour ceux qui envisagent de participer de l'extérieur du Panama. Nous tenons à souligner que le fait de télécharger les informations relatives à votre passeport sur le site web gprocongress.org NE signifie PAS que RREACH traite la demande de visa pour le Panama pour vous. Nous avons besoin de ces informations concernant votre arrivée prévue au Congrès pour nous aider à préparer votre participation. <b><u>Pour obtenir un visa, vous devrez initier et compléter la procédure de visa vous-même.</u></b></p>
						<p>Pour entamer la procédure de visa, vous devez suivre les étapes suivantes :</p>
							<p>&nbsp;&nbsp;1. Contactez l'ambassade ou le consulat du Panama dans votre pays ou région dès que possible. Ils vous guideront dans la procédure de demande de visa et vous indiqueront la procédure à suivre. Pour connaître l'emplacement et les coordonnées des ambassades panaméennes dans le monde, veuillez consulter le site https://mire.gob.pa/ministerio/embajadasyconsulados. </br>
							<p>&nbsp;&nbsp;2. Bien que nous ne puissions pas traiter directement votre demande de visa, nous vous fournissons deux documents d'appui qui vous aideront dans votre démarche. Ces documents serviront de preuves pour appuyer votre demande de visa et démontrer votre participation au GProCongrès. Nous vous avons également fourni une liste des documents nécessaires pour demander votre visa, ainsi que le formulaire de demande de visa que vous devez remettre à l'ambassade.</br>
							<p>&nbsp;&nbsp;3. Si vous venez d'un pays à visa restreint, le service d'immigration vous fixera un rendez-vous pour déposer votre demande de visa et remplir les formalités nécessaires. Soyez prêt à suivre toutes les instructions et directives. Lorsque vous nous aurez envoyé votre passeport et vos données personnelles, un membre de notre équipe vous contactera pour vous guider dans la procédure.</p>
						<p><u>Nous comprenons que le traitement des demandes de visa peut être un processus complexe et long. C'est pourquoi nous vous recommandons d'entamer la procédure de demande bien à l'avance afin de garantir un traitement rapide et sans heurts. Si vous avez des questions ou si vous avez besoin d'aide au cours de la procédure de demande de visa, n'hésitez pas à contacter notre équipe d'assistance dévouée. Nous sommes là pour vous guider et vous aider à faire en sorte que votre voyage vers le GProCongrès se déroule de la manière la plus agréable possible. Si vous avez besoin d'aide ou si vous avez des questions, envoyez un e-mail à Nadine à l'adresse nadine@gprocongress.org .</u></p>
						<p>Nous vous remercions de l'attention que vous portez à cette initiative et nous nous réjouissons de vous accueillir au GProCongress de Panama. Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les formateurs de pasteurs pour des décennies d'impact de l'Evangile.</p>
						<p>Cordialement,</p>
						<p><img src='".asset('images/rajiv_richard.png')."' style='width:100px;'></p><br>
						<p>Rajiv Richard</p> <p>GProCongress II Team</p>";

					}else{
					
						$subject = 'Visa Processing';
						
						$msg = '<p >Dear  participants,
									<br>
								</p>
						<p>As GProCongress II approaches, we need to clarify an important matter regarding visa processing for those planning to attend from outside Panama. We want to emphasize that uploading your passport information on the gprocongress.org website DOES NOT mean that RREACH is processing the visa application to Panama on your behalf.  We need that information regarding your planned arrival at the Congress to assist us in preparing for your attendance.  <b><u>To obtain a visa, you will be required to initiate and complete the visa process yourself.</u></b></p>
						<p>To initiate the visa process, you must take the following steps:</br></p>
							<p>&nbsp;&nbsp;1. Contact the Panamanian embassy or consulate in your country or region as soon as possible. They will guide you through the visa application process and provide you with the procedure you need to follow. Please visit https://mire.gob.pa/ministerio/embajadasyconsulados for the locations and contact information of Panamanian embassies around the world. </p>
							<p>&nbsp;&nbsp;2. While we cannot directly process your visa, we are providing two supporting documents that will help you with your application. These documents will serve as evidence to support your visa application and demonstrate your participation in the GProCongress.  We have also given you a list of the documents needed to apply for your visa, as well as the visa form application you must give to the embassy.</p>
							<p>&nbsp;&nbsp;3. If you are traveling from a restricted visa country, the immigration department will schedule an appointment for you to submit your visa application and complete the necessary formalities. Please be prepared to follow all of their instructions and guidelines. When you send us your passport and personal details, a member of our team will contact you to guide you in the process.</p></br>
						<p><u>We understand that visa processing can be a complex and time-consuming process. Therefore, we recommend initiating the application procedure well in advance to ensure a smooth and timely process. If you have questions or need assistance during the visa application process, please reach out to our dedicated support team. We are here to provide guidance and support to make your journey to the GProCongress as seamless as possible. Please email Nadine at nadine@gprocongress.org, if you need assistance or have any questions.</u> </p>
						<p>We appreciate your attention to this matter and look forward to welcoming you to the GProCongress in Panama. Together, let’s seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
						<p>Best regards,</p>
						<p><img src="'.asset('images/rajiv_richard.png').'" style="width:100px;"></p><br>
						<p>Rajiv Richard</p> <p>GProCongress II Team</p>';

						
		
					}

					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Visa processing');
				}

				echo "<pre>";
				print_r($resultData); die;

				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function PaymenDeadlineMissed100OFF(Request $request){
		
		try {
			
			// $results = \App\Models\User::where([['user_type', '=', '2'], ['stage', '=', '2'], ['amount', '>', 0], ['early_bird', '=', 'Yes']])->get();
			
			$results = [
				4189,4192,4196,4197,4203,4207,4216,4218,4219,4225,4238,4260,4263,4264,4272,4277,4284,4285,4289,4296,4318,4332,4339,4359,4366,4368,4372,4385,4387,4391,4398,4406,4408,4416,4421,4422,4431,4441,4442,4446,4456,4458,4460,4463,4466,4470,4477,4494,4510,4513,4574,4585,4595,4597,4601,4615,4621,4624,4647,4713,4714,4721,4731,4734,4738,4744,4770,4773,4780,4784,4795,4805,4808,4814,4819,4836,4854,4880,4909,4917,4918,4939,4943,4958,4959,4966,4997,4999,5011,5031,5043,5050,5068,5074,5075,5086,5096,5101,5103,5113,5114,5117,5152,5157,5165,5167,5168,5170,5177,5193,5196];
			if(count($results) > 0)
			{
				$resultData = '';
				foreach ($results as $result) {
				
					// if(\App\Helpers\commonHelper::getTotalPendingAmount($result->id) > 0) 
					// {

						$user = \App\Models\User::where('id', $result)->first();
						
						if($user){

							// $Spouse = \App\Models\User::where('parent_id', $user->id)->where('added_as', 'Spouse')->first();
							
							// if($user->marital_status == 'Unmarried'){

							// 	$trainer = 'No';

							// }else if($user->marital_status == 'Married' && !$Spouse){

							// 	$trainer = 'No';

							// }
							// else if($user->marital_status == 'Married' && $Spouse){

							// 	if($user->parent_spouse_stage >= 2){

							// 		$trainer = 'No';

							// 	}else{

							// 		$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWSpouse($user->doyouseek_postoral,$Spouse->doyouseek_postoral,$user->ministry_pastor_trainer,$Spouse->ministry_pastor_trainer,$user->amount);

							// 		$trainer = $data ['trainer'];
							// 	}
								
							// }
							
							// if($trainer == 'Yes'){
							// 	$amount = $result->amount+200;
							// }else{
							// 	$amount = $result->amount+100;
							// }
							$amount = $user->amount;
							$resultData.=$user->id.','.$user->email.','.$amount.','.$user->stage.','.$user->parent_id.','.$user->added_as.'<br>';

							// $user->amount = $amount;
							// $user->early_bird = 'No';
							// $user->save();

							

							if($user->language == 'sp'){

								$url = '<a href="'.url('payment').'" target="_blank">link</a>';
								$subject = 'RECORDATORIO: El pago para asistir al GProCongress II ha vencido.';
								$msg = '<p>Estimado '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>El pago para asistir al GProCongress II ha vencido.  Por favor, vaya a '.$url.', y realice su pago ahora.</p>
								<p ><i><b>POR FAVOR, TENGA EN CUENTA: Debido a que no recibió su pago el 30 de junio de 2023 o antes, ha perdido su descuento de "$100 de descuento" y ahora tendrá que pagar $100 más para poder asistir al Congreso.</b></i></p>
								<p><i><b>Su nuevo monto de pago es $'.$amount.'  ATENCIÓN: Este importe debe pagarse antes del 31 de agosto de 2023, o aumentará aún más su costo.</b></i></p>
								<p>Si tiene alguna pregunta sobre cómo realizar su pago, o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
								<p><i>Ore con nosotros para multiplicar la cantidad y calidad de capacitadores de pastores</i></p>
								<p>Cordialmente,</p><p>Equipo GProCongress II</p>';
		
							}elseif($user->language == 'fr'){
							
								$url = '<a href="'.url('payment').'" target="_blank">lien</a>';
								$subject = "RAPPEL - Votre paiement GProCongress II est maintenant dû";
								$msg = '<p>Cher  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Le paiement de votre participation au GProCongress II est maintenant dû.  Prière d’aller sur '.$url.', et effectuer votre paiement maintenant.</p>
								<p ><i><b>VEUILLEZ NOTER : Parce que le paiement n’a pas été reçu de votre part avant le 30 juin 2023, vous avez perdu votre rabais « 100$ de réduction » et vous devrez payer 100 $ de plus pour assister au Congrès.</b></i></p>
								<p><i><b>Le montant de votre nouveau paiement est de $'.$amount.' VEUILLEZ NOTER: Ce montant doit être payé avant le 31 août 2023, sinon votre coût augmentera encore plus.</b></i></p>
								<p>Si vous avez des questions sur votre paiement, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à cet e-mail.</p>
								<p><i>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</i></p>
								<p>Cordialement,</p><p>L’équipe GProCongress II</p>';
		
							}elseif($user->language == 'pt'){
							
								$url = '<a href="'.url('payment').'" target="_blank">link</a>';
								$subject = 'LEMBRETE - O pagamento do GProCongress II está ainda em aberto.';
								$msg = '<p>Prezado  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>O pagamento da sua Inscrição para o GProCongress II está em aberto. Por favor, visite o '.$url.', e faça o seu pagamento já.</p>
								<p ><i><b>Caso o seu pagamento não for recebido até 30 de Junho de 2023, perderá o desconto de "$100 Dólares" e terá de pagar a mais, $100 Dólares para participar no Congresso.</b></i></p>
								<p><i><b>O seu novo saldo a pagar é de $'.$amount.' Dólares. ATENÇÃO: Esse valor deve ser pago até 31 de Agosto de 2023, ou a sua taxa de inscrição aumentará ainda mais</b></i></p>
								<p>Se tiver alguma dúvida sobre como efetuar o pagamento, ou se precisar de falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
								<p><i>Ore conosco para multiplicar a quantidade e a qualidade dos treinadores de Pastores.</i></p>
								<p>Cordialmente,</p><p>Equipe do GProCongress II</p>';
		
							}else{
							
								$url = '<a href="'.url('payment').'" target="_blank">link</a>';
								$subject = 'REMINDER – Your GProCongress II payment is now due';
								$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Payment for your attendance at GProCongress II is now due.  Please go to '.$url.', and make your payment now.</p>
								<p ><i><b>Because payment was not received from you by June 30, 2023, you have lost your “$100 off” discount, and you must now pay an additional $100 to attend the Congress.</b></i></p>
								<p><i><b>Your new payment amount is $'.$amount.' PLEASE NOTE: This amount must be paid by August 31, 2023, or your cost will go up even more.</b></i></p>
								<p>If you have any questions about making your payment, or if you need to speak to one of our team members, simply reply to this email.</p>
								<p><i>Pray with us toward multiplying the quantity and quality of pastor-trainers. </i></p>
								<p>Warmly,</p><p>GProCongress II Team</p>';
				
							}
		
							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
		
							\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
		
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'July1st - Early Bird - Marked No');
						
						}
					// }
				}
				
				echo "<pre>";
				print_r($resultData); die;

				return response(array('message'=>' Email has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	


	public function PassportInfoEmailSendToVisaNeededKenya(Request $request){
		
		try {
			
			$passportInfoUsers =  [69, 64, 122, 192, 216, 108, 111, 123, 5, 70, 90, 43, 72, 61, 19];

			if(!empty($passportInfoUsers) && count($passportInfoUsers)>0){

				foreach($passportInfoUsers as $val){

					$passportInfo =  \App\Models\PassportInfo::where('id',$val)->first();

					if($passportInfo){
	
						if($passportInfo->visa_category == 'Visa Needed'){
	
							\App\Helpers\commonHelper::sendSponsorshipLetterMailSend($passportInfo->user_id,$passportInfo->id);
							
							$user= \App\Models\User::where('id',$passportInfo->user_id)->first();

							if($user->language == 'sp'){

								$subject = 'Usted necesita una visa para GProCongress II.';
								$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Earlier this week, we were informed by the Panamanian government that travelers from the countries of Angola, Cape Verde, and Kenya will now need to have a visa to enter Panama, in accordance with Resolution #16024 from the Ministry of Public Security and the National Immigration Service of the Republic of Panama.  These three countries were previously in the “no visa needed” category but have now been moved to the “visa needed” category.</p>
								<p>Based on this new information, if you are attending the Congress from Angola, Cape Verde, or Kenya, you will need to obtain an appointment/interview with your local Panamanian embassy to obtain a visa and go to the interview with all documents needed.  We will assist you with the documents, by sending you the following documentation as soon you provide us your passport information at <a href="www.gprocongress.org">www.gprocongress.org</a>:</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	List of documents required for Panamanian visa.</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Official form for Panamanian visa (in Spanish and English).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Acceptance Letter from RREACH (in Spanish and the language you select as communication language).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Letter RREACH’s financial support of the Congress (in Spanish and English).</p>
								<p>Once you receive the documents, you must send the full information requested by email to the embassy assigned to you and proceed with the instruction received by them. Your personal ID and the full copy of the passport must be notarized when you send the information by email.</p>
								<p>If you have any questions, please contact nadine@gprocongress.org.</p><p><br></p>
								<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
					
							}elseif($user->language == 'fr'){
							
								$subject = 'Vous avez besoin d’un visa pour GProCongress II.';
								$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Earlier this week, we were informed by the Panamanian government that travelers from the countries of Angola, Cape Verde, and Kenya will now need to have a visa to enter Panama, in accordance with Resolution #16024 from the Ministry of Public Security and the National Immigration Service of the Republic of Panama.  These three countries were previously in the “no visa needed” category but have now been moved to the “visa needed” category.</p>
								<p>Based on this new information, if you are attending the Congress from Angola, Cape Verde, or Kenya, you will need to obtain an appointment/interview with your local Panamanian embassy to obtain a visa and go to the interview with all documents needed.  We will assist you with the documents, by sending you the following documentation as soon you provide us your passport information at <a href="www.gprocongress.org">www.gprocongress.org</a>:</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	List of documents required for Panamanian visa.</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Official form for Panamanian visa (in Spanish and English).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Acceptance Letter from RREACH (in Spanish and the language you select as communication language).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Letter RREACH’s financial support of the Congress (in Spanish and English).</p>
								<p>Once you receive the documents, you must send the full information requested by email to the embassy assigned to you and proceed with the instruction received by them. Your personal ID and the full copy of the passport must be notarized when you send the information by email.</p>
								<p>If you have any questions, please contact nadine@gprocongress.org.</p><p><br></p>
								<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
					
							}elseif($user->language == 'pt'){
							
								$subject = 'Você precisa de um visto para o GProCongresso II';
								$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Earlier this week, we were informed by the Panamanian government that travelers from the countries of Angola, Cape Verde, and Kenya will now need to have a visa to enter Panama, in accordance with Resolution #16024 from the Ministry of Public Security and the National Immigration Service of the Republic of Panama.  These three countries were previously in the “no visa needed” category but have now been moved to the “visa needed” category.</p>
								<p>Based on this new information, if you are attending the Congress from Angola, Cape Verde, or Kenya, you will need to obtain an appointment/interview with your local Panamanian embassy to obtain a visa and go to the interview with all documents needed.  We will assist you with the documents, by sending you the following documentation as soon you provide us your passport information at <a href="www.gprocongress.org">www.gprocongress.org</a>:</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	List of documents required for Panamanian visa.</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Official form for Panamanian visa (in Spanish and English).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Acceptance Letter from RREACH (in Spanish and the language you select as communication language).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Letter RREACH’s financial support of the Congress (in Spanish and English).</p>
								<p>Once you receive the documents, you must send the full information requested by email to the embassy assigned to you and proceed with the instruction received by them. Your personal ID and the full copy of the passport must be notarized when you send the information by email.</p>
								<p>If you have any questions, please contact nadine@gprocongress.org.</p><p><br></p>
								<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
					
							}else{
							
								$subject = 'URGENT TRAVEL INFORMATION for GProCongress delegates from Angola, Cape Verde, and Kenya!';
								$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
								<p>Earlier this week, we were informed by the Panamanian government that travelers from the countries of Angola, Cape Verde, and Kenya will now need to have a visa to enter Panama, in accordance with Resolution #16024 from the Ministry of Public Security and the National Immigration Service of the Republic of Panama.  These three countries were previously in the “no visa needed” category but have now been moved to the “visa needed” category.</p>
								<p>Based on this new information, if you are attending the Congress from Angola, Cape Verde, or Kenya, you will need to obtain an appointment/interview with your local Panamanian embassy to obtain a visa and go to the interview with all documents needed.  We will assist you with the documents, by sending you the following documentation as soon you provide us your passport information at <a href="www.gprocongress.org">www.gprocongress.org</a>:</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	List of documents required for Panamanian visa.</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Official form for Panamanian visa (in Spanish and English).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Acceptance Letter from RREACH (in Spanish and the language you select as communication language).</p>
								<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Letter RREACH’s financial support of the Congress (in Spanish and English).</p>
								<p>Once you receive the documents, you must send the full information requested by email to the embassy assigned to you and proceed with the instruction received by them. Your personal ID and the full copy of the passport must be notarized when you send the information by email.</p>
								<p>If you have any questions, please contact nadine@gprocongress.org.</p><p><br></p>
								<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
												
							}

							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
							\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'URGENT TRAVEL INFORMATION for GProCongress delegates from Angola, Cape Verde, and Kenya!');

						}
					}

				}
				
			}

			return response(array("message"=>'Data Saved Success'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }

	public function paidBeforeJuly1AddEB(Request $request){
		
		try {
			
			$emailAddresses = [
				'jijeekoo@yahoo.com',
				'benjojo795@gmail.com',
				'victoeguine@gmail.com',
				'georgeomenafrancisco@gmail.com',
				'muluneh.shamebog@gmail.com',
				'djomakou@gmail.com',
				'loidenemelisiamayubawilliams@gmail.com',
				'samsundersingh73@gmail.com',
				'bences2003@yahoo.com',
				'mosesannekamano10@gmail.com',
				'richfortdee@yahoo.com',
				'togbecomlanalphonse@gmail.com',
				'domenyaavonyo@gmail.com',
				'revcadet@gmail.com'
			];

			$userArray = [];
			
			if(count($emailAddresses) > 0){

				foreach ($emailAddresses as $result) {
				
					$user = \App\Models\User::where('email', $result)->first();
					
					if($user){

						$Spouse = \App\Models\User::where('parent_id', $user->id)->where('added_as', 'Spouse')->first();
						
						if($user->marital_status == 'Unmarried'){

							$trainer = 'No';

						}else if($user->marital_status == 'Married' && !$Spouse){

							$trainer = 'No';

						}else if($user->marital_status == 'Married' && $Spouse){

							if($user->parent_spouse_stage >= 2){

								$trainer = 'No';

							}else{

								$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWSpouse($user->doyouseek_postoral,$Spouse->doyouseek_postoral,$user->ministry_pastor_trainer,$Spouse->ministry_pastor_trainer,$user->amount);

								$trainer = $data ['trainer'];
							}
							
						}
						

						if($trainer == 'Yes'){
							$amount = $user->amount-200;
						}else{
							$amount = $user->amount-100;
						}

						$comment = 'User Anount, Early Bird as Yes -  has been updated on request from Admin team by IT Team and user is moved to Stage3';

						$user->amount = $amount;
						$user->early_bird = 'Yes';
						$user->stage = '3';
						$user->status_change_at = date('Y-m-d H:i:s');
						$user->save();

						
						if($user->language == 'sp'){

							$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
							$msg = "<p>Estimado ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
							<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
							<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
							<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
							<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
							<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
							<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
	
						}elseif($user->language == 'fr'){
						
							$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
							$msg = "<p>Cher  ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
							<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
							<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
							<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
							<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
							<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
							<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
	
						}elseif($user->language == 'pt'){
						
							$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
							$msg = "<p>Caro ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
							<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
							<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
							<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
							<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
							<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
							<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
							<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
							<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
	
						}else{
						
							$subject = 'GProCongress II registration!  Please login and submit your passport information.';
							$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
							<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
							<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
							<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
							<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
							<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
							<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
							<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
							<p>Warmly,</p><p>GProCongress II Team</p>";
			
						}
	
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
						\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
	
						if($Spouse){

							$Spouse->stage = '3';
							$Spouse->payment_status = '2';
							$Spouse->status_change_at = date('Y-m-d H:i:s');
							$Spouse->save();

							$data=new \App\Models\Comment();
							$data->sender_id = 1;
							$data->receiver_id = $Spouse->id;
							$data->comment = $comment;
							$data->save();

							if($Spouse->language == 'sp'){

								$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
								$msg = "<p>Estimado ".$Spouse->name.' '.$Spouse->last_name." ,&nbsp;</p><p><br></p>
								<p>Ahora que ha pagado por completo, ha llegado a la siguiente etapa. Por favor, diríjase a nuestra nuestra pagina web e inicie sesión en su cuenta.  Usted ahora puede enviar la información de su pasaporte y verificar si necesitará  visa para ingresar a Panamá este noviembre.</p>
								<p>Para aquellos que NO necesitan una visa para ingresar a Panamá, pueden enviar la información de su vuelo, una vez que lo hayan reservado. Para que su entrada sea sin problemas y con autorización de inmigración a Panamá, RREACH enviará su nombre y detalles de pasaporte a las Autoridades de Inmigratorias de Panamá.</p>
								<p>Para aquellos que SÍ necesitan visa para entrar a Panamá, les solicitamos que primero obtengan la visa aprobada y/o sellada <b>antes de reservar su vuelo.</b></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH está tratando de facilitar el proceso de visa; sin embargo, la decisión final corresponde a las Autoridades de Inmigración de Panamá.</b></p><p></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH no es responsable de:</b></p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	La aprobación de la Visa.</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Pasajes aéreos de ida y vuelta a/desde Ciudad de Panamá; ni</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Los gastos de pasaporte y/o visa en los que incurra en relación con su asistencia al Congreso.</p>
								<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miemebros de nuestro equipo, solo responda a este correo.  </p>
								<p>Juntos busquemos al Señor en pro del GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio</p>
								<p>Atentamente,</p><p>Equipo de GProCongress II</p>";
		
							}elseif($Spouse->language == 'fr'){
							
								$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
								$msg = "<p>Cher  ".$Spouse->name.' '.$Spouse->last_name." ,&nbsp;</p><p><br></p>
								<p>Maintenant que vous avez payé l'intégralité de votre inscription, vous avez atteint l'étape suivante ! Veuillez vous rendre sur notre site web et vous connecter à votre compte. À Info voyage, vous pouvez soumettre les informations de votre passeport et vérifier si vous avez besoin d'un visa pour entrer au Panama en novembre.</p>
								<p>Pour ceux qui n'ont pas besoin de visa pour entrer au Panama, vous pouvez également soumettre les informations relatives à votre vol, une fois que vous avez réservé votre vol. Pour que votre entrée au Panama se fasse en douceur, RREACH soumettra votre nom et les détails de votre passeport aux autorités panaméennes de l'immigration.</p>
								<p>Pour ceux qui ont besoin d'un visa pour entrer au Panama, nous vous demandons de faire approuver et/ou <b>timbrer le visa avant de réserver votre vol</b></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH s'efforce de faciliter le processus d'obtention du visa ; cependant, la décision finale revient aux autorités panaméennes de l'immigration.</b></p><p></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH n'est pas responsable de:</b></p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	L'approbation du visa.</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Le billet d’avion aller-retour vers/depuis Panama City ; ou</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Tous les frais de passeport et/ou de visa que vous encourez en lien avec votre venue au Congrès</p>
								<p>Si vous avez des questions, ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet email.</p>
								<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
								<p>Cordialement,</p><p>L'équipe de GProCongress II</p>";
		
							}elseif($Spouse->language == 'pt'){
							
								$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
								$msg = "<p>Caro ".$Spouse->name.' '.$Spouse->last_name." ,&nbsp;</p><p><br></p>
								<p>Agora que sua taxa de inscrição para o Congresso  foi paga integralmente, você atingiu o próxima etapa! Por favor, vá ao nosso site e faça o login na sua conta. No Informações de viagem, você pode enviar as informações do seu passaporte e verificar se precisará de visto para entrar no Panamá em Novembro.</p>
								<p>Para aqueles que NÃO precisam de visto para entrar no Panamá, você também pode enviar suas informações de voo, depois de reservar seu voo. Para sua entrada tranquila e autorização de imigração no Panamá, a  RREACH enviará seu nome e detalhes do passaporte às autoridades de imigração panamenhas.</p>
								<p>Para aqueles que precisam de visto para entrar no Panamá, solicitamos que você primeiro obtenha o visto aprovado e/ou carimbado antes de reservar seu voo.</p>
								<p style='background-color:yellow; display: inline;'><b>A RREACH está tentando facilitar o processo de visto; no entanto, a decisão final cabe às Autoridades de Imigração do Panamá.</b></p><p></p>
								<p style='background-color:yellow; display: inline;'><b>a RREACH não é responsável:</b></p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Pela aprovação do visto</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Bilhete de ida e volta para e da Cidade de Panamá, ou</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Qualquer taxa de visto ou de emissão de passaporte ligada a viagem para o Congresso</p>
								<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
								<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
								<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";
		
							}else{
							
								$subject = 'GProCongress II registration!  Please login and submit your passport information.';
								$msg = "<p>Dear ".$Spouse->name.' '.$Spouse->last_name." ,&nbsp;</p><p><br></p>
								<p>Now that you are paid in full, you have reached Next stage!  Please go to our website and login to your account.  Under Travel info, you can submit your passport information, and check to see if you will need a visa to enter Panama this November. </p>
								<p>For those who DO NOT need a visa to enter Panama, you can also submit your flight information, once you have booked your flight. For your smooth entry and immigration clearance into Panama, RREACH will submit your name and passport details to the Panamanian Immigration Authorities.</p>
								<p>For those who DO need a visa to enter Panama, we request you first get the visa approved and/or stamped <b>before you book your flight.</b></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH is trying to facilitate the visa process. The final decision is up to the Panamanian Immigration Authorities.</b></p><p></p>
								<p style='background-color:yellow; display: inline;'><b>RREACH is not responsible for:</b></p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;1. 	Any visa approval;</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;2. 	Round-trip airfare to/from Panama City; or</p><br>
								<p style='background-color:yellow; display: inline;'>&nbsp;&nbsp;&nbsp;3. 	Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
								<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
								<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
								<p>Warmly,</p><p>GProCongress II Team</p>";
				
							}
		
							\App\Helpers\commonHelper::userMailTrigger($Spouse->id,$msg,$subject);
							\App\Helpers\commonHelper::emailSendToUser($Spouse->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($Spouse->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
		
						}

						$data=new \App\Models\Comment();
						$data->sender_id = 1;
						$data->receiver_id = $user->id;
						$data->comment = $comment;
						$data->save();


						$userArray[] = [
							'email'=>$user->email,
							'satge'=>$user->stage,
							'amount'=>$user->amount,
							'new'=>$amount,
						];

					}
				}

				echo "<pre>"; print_r($userArray); die;
				
				
			}

			return response(array("message"=>'Data set.'), 200);
			
		} catch (\Exception $e) {

			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }



}
