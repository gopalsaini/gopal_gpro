<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;

class PreLoginController extends Controller {

    public function registration(Request $request){
	
		$rules = [
            'email' => ['required', 'email','unique:users,email'],
			'password' => 'required|confirmed',
			'terms_and_condition' => 'required|in:0,1',
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'language' => 'required|in:en,sp,fr,pt',
		];

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
				
				$result = \App\Models\User::where([
										['email', '=', $request->json()->get('email')]
										])->first();
										
				if($result){

					return response(array("error"=>true, 'message'=>'Email is already exist with us. Please try another email id.'), 403);
				
				}else{
   
					$user=new \App\Models\User();
					$user->email=$request->json()->get('email');
					$user->language=$request->json()->get('language');
					$user->name=$request->json()->get('first_name');
					$user->last_name=$request->json()->get('last_name');
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
				

					$sendOtpResult = \App\Helpers\commonHelper::callAPI('POST','/send-otp', json_encode(array('email'=>$request->json()->get('email'))));
 
					return response((array)json_decode($sendOtpResult->content), $sendOtpResult->status);
					
				}
				
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>$e->getMessage()), 403);
			}
		}

    }

	public function sendOtp(Request $request){
		
		$rules = [
            'email' => 'required|email',
		];

		$validator = \Validator::make($request->json()->all(), $rules);
		 
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

				$link = '<a href="'.url('email-registration-confirm/'.$token).'">Click here</a>';

				if($userData->language == 'sp'){

					$subject = 'Se requiere verificación de correo electrónico para inscribirse en el GProCongress II';
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p>¡Felicidades! Ha creado con éxito su cuenta para el GProCongress II. Para verificar su dirección de correo electrónico y completar el proceso de inscripción, utilice este enlace: '.$link.'.</p><p><br></p><p>Need help, or have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p>¿Tiene preguntas? Simplemente responda a este correo para conectarse con algún miembro de nuestro equipo.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';

				}elseif($userData->language == 'fr'){

					$subject = 'Vérification des e-mails requise pour l’inscription au GProCongrès II';
					$msg = '<p>Cher '.$name.', Félicitations !&nbsp; Vous avez créé avec succès votre compte pour le GProCongrès II.&nbsp; Pour vérifier votre adresse e-mail et terminer le processus d’inscription, veuillez utiliser ce lien : '.$link.'.</p><p><br></p><p><br></p><p>Besoin d’aide ou vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';

				}elseif($userData->language == 'pt'){

					$subject = 'Pedido de verificação de e-mail para inscrição ao II CongressoGPro';
					$msg = '<p>Prezado '.$name.',&nbsp;</p><p><br></p><p><br></p><p>Parabéns! Você criou sua conta para o II CongressoGPro com sucesso. Para verificar o seu endereço eletrônico, e completar o processo de inscrição, por favor use este link: '.$link.'.&nbsp;</p><p><br></p><p><br></p><p>Precisa de ajuda, ou tem perguntas? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.&nbsp;</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';

				}else{

					$subject = 'Email verification required for GProCongress II registration';
					$msg = '<p>Dear '.$name.',&nbsp;</p><p><br></p><p>Congratulations!&nbsp; You have successfully created your account for the GProCongress II.&nbsp; To verify your email address, and complete the registration process, please use this link: '.$link.'.</p><p><br></p><p>Need help, or have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';

				}
				
				
				\Mail::send('email_templates.otp', compact('to', 'msg'), function($message) use ($to, $subject) {
					$message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
					$message->subject($subject);
					$message->to($to);
				});
				

				// $html = view('email_templates.otp', compact('to', 'otp'))->render();
				// $a = \App\Helpers\commonHelper::sendZeptoEmail($html);
				
				$message = "Email Verification link has been sent successfully on your email id : ".$to;
					 	
				return response(array('message'=>$message), 200);
				
			}catch (\Exception $e){

				return response(array("error"=>true, "message"=>$e->getMessage()), 403); 
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
		
		$rules = [
            'token' => 'required',
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

						$to = $userResult->email;
						$name = $userResult->name.' '.$userResult->last_name;
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						
						$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has shown interest in GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
						\App\Helpers\commonHelper::emailSendToAdmin('[GProCongress II Admin]  New User Registration', $msg);

						// \App\Helpers\commonHelper::sendSMS($userResult->mobile);

						return response(array('message'=>'Your registration has been completed successfully, Please update your profile.', "token"=>$userResult->createToken('authToken')->accessToken, "result"=>$userResult->toArray()), 200);
					} 

					$userResult->Save();

 
					return response(array('message'=>'Email verified successfully.'), '200');

				}else{
					return response(array("error"=>true, "message"=>"Email already verified, Please Login"), 403);
				}
				
			}catch (\Exception $e){
				return response(array("error"=>true, "message"=>$e->getMessage()), 403); 
			}
		}
	}

	public function login(Request $request){
		
		$rules = [
            'email' => 'required|email',
			'password' => 'required',
		];
		
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

			try{
				
				$userResult = \App\Models\User::where([['email','=',$request->json()->get('email')]])->first();
				
				if (!$userResult) {

					return response(array("error"=>true, 'message'=>"This account does not exist."), 403);

				} else if ($userResult->status == '1') {

					return response(array("error"=>true, 'message'=>"Your account has been blocked. Please contact Administrator."), 403);

				} else if (\Hash::check($request->json()->get('password'), $userResult->password) && $userResult->otp_verified=='Yes') {

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->user_id=$userResult->id;
					$UserHistory->action='User Login';
					$UserHistory->save();

					return response(array('message'=>"Login successfully.", "otp_verified"=>'Yes',"token"=>$userResult->createToken('authToken')->accessToken, "result"=>$userResult->toArray()), 200);
					
				} else if ($userResult->otp_verified=='No'){

					$sendOtpResult = \App\Helpers\commonHelper::callAPI('POST','/send-otp', json_encode(array('email'=>$request->json()->get('email'))));
					$response=(array)json_decode($sendOtpResult->content);
					$response['otp_verified']='No';


					return response($response, $sendOtpResult->status);
					
				}else{

					return response(array("error"=>true, 'message'=>"Invalid Password."), 403); 
				}

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>$e->getMessage()), 403); 
			}
		}
	}

	public function profileUpdateReminder(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['profile_update', '=', '0'], ['stage', '=', '0']])
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
						$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Su solicitud para asistir al GProCongress II no ha sido completada.</p><p><br></p><p>Por favor, utilice este enlace '.$url.' para acceder, editar y completer su cuenta en cualquier momento. Si completa el formulario a tiempo, nos ayudará a asegurarle su lugar.</p><p>¿Todavía tiene preguntas o necesita ayuda?</p><p><br></p><p>Simplemente responda a este correo, para conectarse con algún miembro nuestro equipo. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
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
						$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Su solicitud para asistir al GProCongress II no ha sido completada.</p><p><br></p><p>Por favor, utilice este enlace '.$url.' para acceder, editar y completer su cuenta en cualquier momento. Si completa el formulario a tiempo, nos ayudará a asegurarle su lugar.</p><p>¿Todavía tiene preguntas o necesita ayuda?</p><p><br></p><p>Simplemente responda a este correo, para conectarse con algún miembro nuestro equipo. ¡Estamos aquí para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
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
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['payment_status', '=', '0'], ['stage', '=', '2']])
									->whereDate('status_change_at', '=', now()->subDays(5)->setTime(0, 0, 0)->toDateTimeString())
									->get();

			if(count($results) > 0){
				foreach ($results as $key => $result) {
				
					$created_at = \Carbon\Carbon::parse(date('Y-m-d', strtotime($result->created_at)));
					$today =  \Carbon\Carbon::parse(date('Y-m-d'));
				
					$days = $today->diffInDays($created_at);

					$to = $result->email;

					$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($result->id, true);
					$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($result->id, true);
					$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($result->id, true);
					$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($result->id, true);

					if($result->language == 'sp'){

						$subject = "Su pago está pendiente";
						$msg = '<p>Estimado '.$result->name.',&nbsp;</p><p><br></p><p>¿Sabía que su pago reciente a GProCongress II sigue pendiente en este momento?</p><p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo se comunicará con usted.</p><p><br></p><p>Lo mantendremos informado sobre si su pago ha sido aceptado o rechazado.</p><p><br></p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($result->language == 'fr'){
					
						$subject = "Votre paiement est en attente";
						$msg = '<p>Cher '.$result->name.',&nbsp;</p><p><br></p><p>Saviez-vous que votre récent paiement pour le GProCongrès II est toujours en attente en ce moment ?&nbsp;</p><p>Voici un résumé actuel de l’état de votre paiement :&nbsp;</p><p><br></p><p>MONTANT TOTAL À PAYER : '.$totalAcceptedAmount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p>Vous avez des questions ? Répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Nous vous tiendrons au courant si votre paiement a été accepté ou refusé.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe GProCongrès II</p>';
					
					}elseif($result->language == 'pt'){
					
						$subject = "Seu pagamento está pendente";
						$msg = '<p>Prezado '.$result->name.',</p><p><br></p><p>Sabia que o seu recente pagamento para o II CongressoGPro ainda está pendente até agora?</p><p>Aqui está o resumo atual do estado do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento não for feito até 31st August 2023 , a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Tem perguntas? Simplesmente responda a este e-mail, e nossa equipe irá se conectar com você.</p><p>Nós vamos lhe manter informado sobre seu pagamento se foi aceite ou declinado.&nbsp;</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Your payment is pending';
						$msg = '<p>Dear '.$result->name.',</p><p><br></p><p>Did you know your recent payment to GProCongress II is still pending at this time?</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID: '.$totalAcceptedAmount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED: '.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS: '.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE: '.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by&nbsp; 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you have questions? Simply respond to this email, and our team will connect with you.&nbsp;</p><p><br></p><p>We will keep you updated about whether your payment has been accepted or declined.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
					
					}

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
		
		$rules['email'] = 'required|email|exists:users,email';
		
		$validator = \Validator::make($request->json()->all(), $rules);
		
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

				if(!$userResult){
					
					return response(array('message'=>"Invalid Email id."), 403);
					
				}else{

					
					return response(array('message'=>"Link created successfully.","token"=>md5(rand(1111,4444))), 200);

				}
								
			}catch (\Exception $e){
				
				return response(array("error"=>true, "message" => $e->getMessage()), 403);
			
			}
		}
	}

	public function resetPassword(Request $request){
		
		$rules = [
            'token' => 'required',
            'email' => 'required|email',    
            'password' => 'required|min:8|confirmed'
		];

		$validator = \Validator::make($request->json()->all(), $rules);
		 
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
					
					return response(array('message'=>"Invalid Email id."),403);
				
				}else{

					$tokenResult=\DB::table('password_resets')->where([
											['email','=',$request->json()->get('email')],
											['token','=',$request->json()->get('token')],
											])->first();

					if(!$tokenResult){
						return response(array('message'=>"Invalid token."), 403);
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

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($emailResult->id,$subject,$msg,'User Reset Password');
					
						\App\Helpers\commonHelper::emailSendToUser($request->json()->get('email'), $subject, $msg);

						return response(array('message'=>'Your password has been updated successfully.'), 200);

					}
					
				}
				
			}catch (\Exception $e){
				
				return response(array("error" => true, "message" => $e->getMessage()), 403);
			
			}
		}

    }

	public function help(Request $request){
		
		$rules = [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric|digits:10',
            'message' => 'required',
		];

		$validator = \Validator::make($request->json()->all(), $rules);
		 
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
				$message->name = $request->json()->get('name');
				$message->email = $request->json()->get('email');
				$message->mobile = $request->json()->get('mobile');
				$message->message = $request->json()->get('message');
				$message->save();

				return response(array('message'=>'Your submission has been sent successfully.',"error" => false), 200);
				
			}catch (\Exception $e){
				
				return response(array("error" => true, "message" => $e->getMessage()), 403);
			
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

								$subject = 'Payment Completed';
								$msg = 'Your '.$user->amount.'  amount has been accepted and payment has been completed successfully.<p><strong>Accepted Amount</strong> : '.$totalAcceptedAmount.'</p><p><strong>Amount In Process</strong> : '.$totalAmountInProcess.'</p><p><strong>Decline Amount</strong> : '.$totalRejectedAmount.'</p><p><strong>Pending Amount</strong> : '.$totalPendingAmount.'</p>';
								
								
								
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
								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

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

		$results = \App\Models\Transaction::where('payment_status','0')->get();
		
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
							// $transaction->customer_id=$event->data->object->charges->data[0]->customer;
							$transaction->payment_status='2';
							$transaction->status='1';
							$transaction->method='Online';
							$transaction->bank='Card';
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

								$subject = 'Payment Completed';
								$msg = 'Your '.$user->amount.'  amount has been accepted and payment has been completed successfully.<p><strong>Accepted Amount</strong> : '.$totalAcceptedAmount.'</p><p><strong>Amount In Process</strong> : '.$totalAmountInProcess.'</p><p><strong>Decline Amount</strong> : '.$totalRejectedAmount.'</p><p><strong>Pending Amount</strong> : '.$totalPendingAmount.'</p>';
								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

								// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
								$subject = 'Submit Travel Information';
								$msg = 'Your '.$user->amount.'  amount has been accepted and payment has been completed successfully. And Please Submit your Travel Information';
								\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

							}

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

					}
				}
				
				
			}
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
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['stage', '=', '3']])
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
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
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
	
				$Result = \App\Models\Testimonial::where('id', '3')->where('id', '4')->where('id', '5')->get();
			
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
					'Video1'=>asset('images/Gpromobile French-1.m4vv'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];

			}elseif($request->json()->get('lang') == 'pt'){

				$result=[
					'Video1'=>asset('images/Gpromobile Protugese-1.m4v'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];

			}elseif($request->json()->get('lang') == 'sp'){

				$result=[
					'Video1'=>asset('images/Gpromobile Spain-1.m4v'),
					'Video2'=>asset('assets/images/a_glimpse_of_the_gprocongress.mp4'),
				];
				
			}else{

				$result=[
					'Video1'=>asset('assets/images/Gpromobile-1.m4v'),
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
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['stage', '>', '1'], ['amount', '>', 0]])->get();
			
			if(count($results) > 0){

				foreach ($results as $result) {
				
					if($result->early_bird == 'Yes'){

						$amounts = \App\Models\Wallet::where([['user_id', '=', $result->id], ['type', '=', 'Cr'], ['status', '=', 'Success']])->whereDate('created_at','<=','2022-11-23')->sum('amount');
					
						if($amounts > 0){

							$percentage = (((int)$amounts / (int)($result->amount)) * 100);

							if($percentage < 20) {

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

	public function EarlyBird2Aug2023(Request $request){
		
		try {
			
			$results = \App\Models\User::where([['user_type', '=', '2'], ['stage', '>', '1'], ['amount', '>', 0]])->get();
			
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
			
			$results = \App\Models\User::where('spouse_confirm_token','!=',null)->get();

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
			
			$results = \App\Models\User::where('spouse_confirm_token','!=',null)->where('spouse_confirm_reminder_email','0')->get();

			if(count($results) > 0){
				foreach ($results as $key => $existSpouse) {
				
					$name = '';

					$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
					if($user){

						$name = $user->salutation.' '.$user->name.' '.$user->last_name;

					}

					$existSpouse->spouse_confirm_reminder_email = '1';
					$existSpouse->save();
					
					$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';

					if($user->language == 'sp'){

						$subject = "IMPORTANTE: Por favor, confirme el estado de su registro. (PRIMER RECORDATORIO)";
						$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p><br></p><p>Hemos recibido '.$name.' una solicitud&nbsp; para el GproCongress II</p><p><br></p><p>'.$name.' ha indicado que asistirán juntos al Congreso. También hemos recibido su solicitud, pero para seguir procesando su registro, necesitamos verificar cierta información.</p><p>Por favor, reponda este correo '.$confLink.' para confirmer que usted y '.$name.' estan casados y que asistirán al GproCongress II juntos.&nbsp;&nbsp;</p><p><br></p><p>TOME NOTA: Si no responde a este correo electrónico, su inscripción NO se habrá completado y NO podrá participar en el Congreso.</p><p><br></p><p>Los esperamos a usted y a '.$name.' en Ciudad de Panamá, Panamá, del 12 al 17 de noviembre de 2023.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($user->language == 'fr'){
					
						$subject = "IMPORTANT: Veuillez confirmer votre statut d’inscription. (PREMIER RAPPEL)";
						$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Nous avons reçu la demande de '.$name.' pour le GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqué que vous assisterez au Congrès ensemble.&nbsp; Votre demande a également été reçue, mais pour poursuivre le processus de votre inscription, nous devons vérifier certaines informations.</p><p><br></p><p>Veuillez répondre à cet e-mail '.$confLink.' pour confirmer que vous et '.$name.' êtes mariés et que vous assisterez ensemble au GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>VEUILLEZ NOTER: Si vous ne répondez pas à ce courriel, votre inscription ne sera PAS complétée et vous ne serez PAS admissible à participer au Congrès.</p><p><br></p><p>Nous avons hâte de vous voir, vous et '.$name.', à Panama City, au Panama du 12 au 17 novembre 2023!&nbsp;</p><p><br></p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe GProCongrès II</p>';
					
					}elseif($user->language == 'pt'){
					
						$subject = "IMPORTANTE: Por favor confirme o estado da sua inscrição. (PRIMEIRO LEMBRETE)";
						$msg = '<p>Prezado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p><br></p><p>Recebemos o pedido de '.$name.' para o II CongressoGPro.&nbsp;</p><p><br></p><p>'.$name.' afirmou que vocês iriam participar o Congresso juntos.&nbsp;</p><p>Seu pedido também foi recebido, mas para dar seguimento ao processo da sua inscrição, nós precisamos de verificar algumas informações.&nbsp;</p><p><br></p><p>Por favor responda a este e-mail '.$confLink.' para confirmar que você e '.$name.' estão casados, e que vocês irão participar do II CongressoGPro juntos.</p><p><br></p><p>POR FAVOR NOTE: Se você não responder a este e-mail, sua inscrição NÃO estará completa, e você NÃO será elegível a participar do Congresso.</p><p>Esperamos vos ver, ambos, você e '.$name.', na Cidade de Panamá, Panamá, de 12 a 17 de Novembro de 2023!</p><p><br></p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'IMPORTANT: Please confirm your registration status. (FIRST REMINDER)';
						$msg = '<p>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>We have received '.$name.'’s application for GProCongress II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' has indicated that you will be attending the Congress, together.&nbsp; Your application has also been received, but to further process your registration, we need to verify some information.</p><p><br></p><p>Please reply to this email '.$confLink.' to confirm that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</p><p><br></p><p>PLEASE NOTE: If you do not reply to this email, your registration will NOT be completed, and you will NOT be eligible to participate in the Congress.</p><p><br></p><p>We look forward to seeing, both, you and '.$name.', in Panama City, Panama on November 12-17, 2023!&nbsp;</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
					
					}

					\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
					
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- FIRST REMINDER');
					
					
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
			
			$results = \App\Models\User::where('spouse_confirm_token','!=',null)->where('spouse_confirm_reminder_email','1')->get();

			if(count($results) > 0){
				foreach ($results as $key => $existSpouse) {
				
					$name = '';

					$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
					if($user){

						$name = $user->salutation.' '.$user->name.' '.$user->last_name;

					}

					
					$existSpouse->spouse_confirm_reminder_email = '2';
					$existSpouse->save();
					
					$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';

					if($user->language == 'sp'){

						$subject = "URGENTE: Por favor, confirme su estado de inscripción. (SEGUNDO RECORDATORIO)";
						$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p>Hemos recibido la solicitud de '.$name.' para participar en el GProCongress II.</p><p><br></p><p>'.$name.' ha indicado que asistirán juntos al Congreso. También hemos recibido su solicitud, pero para seguir procesando su inscripción, necesitamos verificar cierta información.</p><p><br></p><p>Por favor responda a este correo para confirmar que usted y '.$name.' están casados, y que asistirán al GproCongress juntos.&nbsp;</p><p><br></p><p>TOME NOTA: Si no responde a este correo electrónico '.$confLink.', su inscripción NO se habrá completado y NO podrá participar en el Congreso.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($user->language == 'fr'){
					
						$subject = "URGENT: Veuillez confirmer votre statut d’inscription. (DEUXIÈME RAPPEL)";
						$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>Nous avons reçu la demande de '.$name.' pour le GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqué que vous assisterez au Congrès ensemble.&nbsp; Votre demande a également été reçue, mais pour poursuivre le processus de votre inscription, nous devons vérifier certaines informations.</p><p><br></p><p>Veuillez répondre à cet e-mail pour confirmer que vous et '.$name.' êtes mariés et que vous assisterez ensemble au GProCongrès II.&nbsp;&nbsp;</p><p><br></p><p>VEUILLEZ NOTER: Si vous ne répondez pas à ce courriel '.$confLink.', votre inscription ne sera PAS complétée et vous ne serez PAS admissible à participer au Congrès.</p><p><br></p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>L’équipe GProCongrès II</p>';
					
					}elseif($user->language == 'pt'){
					
						$subject = "URGENTE: Por favor confirme o estado da sua inscrição. (SEGUNDO LEMBRETE)";
						$msg = '<p>Prezado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p><br></p><p><br></p><p>Recebemos o pedido de '.$name.' para o II CongressoGPro.&nbsp;</p><p><br></p><p>'.$name.' afirmou que vocês iriam participar o Congresso juntos.&nbsp;</p><p>Seu pedido também foi recebido, mas para dar seguimento ao processo da sua inscrição, nós precisamos de verificar algumas informações.</p><p><br></p><p>Por favor responda a este e-mail para confirmar que você e '.$name.' estão casados, e que vocês irão participar do II CongressoGPro juntos.</p><p><br></p><p>POR FAVOR NOTE: Se você não responder a este e-mail '.$confLink.', sua inscrição NÃO estará completa, e você NÃO será elegível a participar do Congresso.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'URGENT: Please confirm your registration status. (SECOND REMINDER)';
						$msg = '<p>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>We have received '.$name.'’s application for GProCongress II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' has indicated that you will be attending the Congress, together.&nbsp; Your application has also been received, but to further process your registration, we need to verify some information.</p><p><br></p><p>Please reply to this email to confirm that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</p><p><br></p><p>PLEASE NOTE: If you do not reply to this email '.$confLink.', your registration will NOT be completed, and you will NOT be eligible to participate in the Congress.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p>&nbsp;The GProCongress II Team</p>';
					
					}

					\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
					
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- SECOND REMINDER');
					
					
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
			
			$results = \App\Models\User::where('spouse_confirm_token','!=',null)->where('spouse_confirm_reminder_email','2')->get();

			if(count($results) > 0){
				foreach ($results as $key => $existSpouse) {
				
					$name = '';

					$user = \App\Models\User::where('id',$existSpouse->parent_id)->first();
					if($user){

						$name = $user->salutation.' '.$user->name.' '.$user->last_name;

					}

					$existSpouse->spouse_confirm_reminder_email = '3';
					$existSpouse->save();
					
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

					\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
					
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($existSpouse->id,$subject,$msg,'Spouse Confirm your registration status- THIRD REMINDER');
					
					
				}
				
				return response(array('message'=>count($results).' Reminders has been sent successfully.'), 200);
			}

			return response(array("message"=>'No results found for reminder.'), 200);
			
		} catch (\Exception $e) {
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
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
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/fr/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/fr/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/fr/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/fr/web/profile-details.php'),
					
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
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/pt/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/pt/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/pt/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/pt/web/profile-details.php'),
					
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
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/sp/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/sp/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/sp/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/sp/web/profile-details.php'),
					
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
					'sponsor-payment'=>\File::getRequire(base_path().'/resources/lang/en/web/sponsor-payment.php'),
					'spouse-details'=>\File::getRequire(base_path().'/resources/lang/en/web/spouse-details.php'),
					'stripe'=>\File::getRequire(base_path().'/resources/lang/en/web/stripe.php'),
					'verification'=>\File::getRequire(base_path().'/resources/lang/en/web/verification.php'),
					'profile-details'=>\File::getRequire(base_path().'/resources/lang/en/web/profile-details.php'),
					
				];
			}


			return response(array("message"=>'language data fetch done.','result'=>$result), 200);
			
		} catch (\Exception $e) {
			
			return response(array("error"=>true, "message"=>$e->getMessage()), 403);
		}

    }



}