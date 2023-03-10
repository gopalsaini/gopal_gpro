<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;
use DB;
use Dompdf\Helpers;
use Validator;
use Hash;

class PostLoginController extends Controller {

    public function userProfile(Request $request){

		return response(array("result"=>$request->user()), 200);

	}


	public function GroupLeader(Request $request){
	
		
		if($request->json()->get('is_group')==''){

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Please-select-YesNo');
			return response(array("error"=>true, 'message'=>$message), 403);
		}else{
			$rules = [
				'is_group' => 'required',
			];
		}

		if($request->json()->get('is_group')=='Yes'){

			$rules['user_whatsup_code']='required';
			$rules['contact_whatsapp_number']='required';
			$rules['user_mobile_code']='required';
			$rules['contact_business_number']='required|unique:users,contact_business_number,'.$request->user()->id;
		}

		$messages = array(
			'is_group.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'is_group_required'),
			'user_whatsup_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'user_whatsup_code_required'),
			'contact_whatsapp_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_whatsapp_number_required'), 
			'user_mobile_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'user_mobile_code_required'), 
			'contact_business_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_business_number_required'), 
			'contact_business_number.unique' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_business_number_unique'),  
			
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
				
				
				if($request->json()->get('is_group')=='Yes'){

					if(count($request->json()->get('group_list'))==0){

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Please-Group-Users');
						return response(array("error"=>true, "message"=>$message), 403);

					}else{

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


						//check mobile no
						$tempArr = array_unique(array_column($request->json()->get('group_list'), 'mobile'));
						$uniqueGroupUsers=array_intersect_key($request->json()->get('group_list'), $tempArr);
		
						if(count($uniqueGroupUsers) != count($request->json()->get('group_list'))){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Wehave-found-duplicate-mobile-Group-users');
							return response(array("message"=>$message), 403);
						
						}else{

							$groupMobile = $names = array_column($request->json()->get('group_list'), 'mobile'); 
							$checkExistUsers=\App\Models\User::whereIn('mobile',$groupMobile)->get();

							if($checkExistUsers->count()>0){

								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'isAlreadyExist-withusSo-please-use-another-mobile-number');
								return response(array("message"=>$checkExistUsers[0]['mobile'].$message), 403);

							}
						}

						//check whats up mobile no
						$tempArr = array_unique(array_column($request->json()->get('group_list'), 'whatsapp_number'));
						$uniqueGroupUsers=array_intersect_key($request->json()->get('group_list'), $tempArr);
		
						if(count($uniqueGroupUsers) != count($request->json()->get('group_list'))){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'We-have-found-duplicateWhatsAppmobile-numberin-groupusers');
							return response(array("error"=>true, "message"=>$message), 403);
						
						}else{

							$groupWhatsMobile = $names = array_column($request->json()->get('group_list'), 'whatsapp_number'); 
							$checkExistUsers=\App\Models\User::whereIn('contact_whatsapp_number',$groupWhatsMobile)->get();

							if($checkExistUsers->count()>0){

								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'isAlreadyExist-withusSo-please-use-another-mobile-number');
								return response(array("error"=>true, "message"=>$checkExistUsers[0]['contact_whatsapp_number'].$message), 403);

							}
						}

					}
				}   
				
				$loginUser=\App\Models\User::where('id',$request->user()->id)->first();
				$loginUser->phone_code=$request->json()->get('user_mobile_code');
				$loginUser->contact_business_number=$request->json()->get('contact_business_number');
				$loginUser->contact_whatsapp_codenumber=$request->json()->get('user_whatsup_code');
				$loginUser->contact_whatsapp_number=$request->json()->get('contact_whatsapp_number');
				$loginUser->save();

				if($request->json()->get('is_group')=='Yes'){

					$users=[];

					foreach($request->json()->get('group_list') as $group){

						$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
   
						$password = substr(str_shuffle($chars),0,8);

						$users[]=array(
							'parent_id'=>$request->user()->id,
							'added_as'=>'Group',
							'name'=>$group['name'],
							'email'=>$group['email'],
							'phone_code'=>$group['mobile_code'],
							'mobile'=>$group['mobile'],
							'contact_whatsapp_codenumber'=>$group['whatsapp_code'],
							'contact_whatsapp_number'=>$group['whatsapp_number'],
							'reg_type'=>'email',
							'designation_id'=>'2',
							'password'=>\Hash::make($password),
							'otp_verified'=>'No',
							'system_generated_password'=>'1',
						);

						
						$to = $group['email'];

						if($request->user()->language == 'sp'){
							$url = '<a href="'.url('profile-update').'">aqui</a>';
							$faq = '<a href="'.url('faq').'">aqui</a>';

							$subject = "??Su inscripci??n al GproCongress II ha iniciado!";
							$msg = '<p>Estimado '.$group['name'].',</p><p>&nbsp;</p><p>'.$request->user()->name.' '.$request->user()->last_name.' ha inicado el proceso de inscripci??n al GproCongress II al ingresar tu nombre.</p><p>Quedamos a la espera de recibir su solicitud completa.</p><p><br></p><p>Por favor, utilice este enlace haga click '.$url.' para acceder, editar y completer su cuenta en cualquier momento.&nbsp;</p><p><br><div><br>Direcci??n de correo electr??nico: '.$to.'<br>Contrase??a: '.$password.'<br></div></p><p>Si usted desea m??s informaci??n sobre los criterios de admisibilidad para candidatos potenciales al congreso, antes de continuar, haga click, '.$faq.'</p><p><br></p><p>Para hablar con uno de los miembros de nuestro equipo, usted solo tiene que responder a este email. ??Estamos aqu?? para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($request->user()->language == 'fr'){
						
							$url = '<a href="'.url('profile-update').'">aqui</a>';
							$faq = '<a href="'.url('faq').'">cliquez ici</a>';
	
							$subject = "Votre inscription au GProCongr??s II a commenc??!";
							$msg = '<p>Cher '.$group['name'].',&nbsp;</p><p>'.$request->user()->name.' '.$request->user()->last_name.' a commenc?? le processus d???inscription au GProCongr??s II, en soumettant votre nom!&nbsp;</p><p><br></p><p><br></p><p><br></p><p>Nous sommes impatients de recevoir votre demande compl??te. Veuillez utiliser ce lien '.$url.' pour acc??der, modifier et compl??ter votre compte ?? tout moment.&nbsp;</p><p><br><div><br>E-mail: '.$to.'<br>Mot de passe: '.$password.'<br></div></p><p>Si vous souhaitez plus d???informations sur les crit??res d?????ligibilit?? pour les participants potentiels au Congr??s, avant de continuer,  '.$faq.'.</p><p><br></p><p>Pour parler ?? l???un des membres de notre ??quipe, vous pouvez simplement r??pondre ?? ce courriel. Nous sommes l?? pour vous aider !</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L?????quipe GProCongr??s II</p>';
						
						}elseif($request->user()->language == 'pt'){
						
							$url = '<a href="'.url('profile-update').'">aqui</a>';
							$faq = '<a href="'.url('faq').'">clique aqui</a>';
	
							$subject = "A sua inscri????o para o II CongressoGPro j?? Iniciou!";
							$msg = '<p>Prezado '.$group['name'].',</p><p><br></p><p>'.$request->user()->name.' '.$request->user()->last_name.' iniciou com o processo de inscri????o para o II CongressoGPro, por submeter o teu nome!&nbsp;</p><p><br></p><p>N??s esperamos receber a sua inscri????o complete.</p><p>Por favor use este '.$url.' para aceder, editar e terminar a sua conta a qualquer momento.</p><p><br><div><br>Eletr??nico: '.$to.'<br>Senha: '.$password.'<br></div></p><p>Se voc?? precisa de mais informa????es sobre o crit??rio de elegibilidade para participantes potenciais ao Congresso, antes de continuar,  '.$faq.'</p><p><br></p><p>Para falar com um dos nossos membros da equipe, voc?? pode simplesmente responder a este e-mail. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, a medida que nos esfor??amos para multiplicar o n??mero, e desenvolvemos a capacidade dos treinadores de pastores&nbsp;</p><p><br></p><p>Calorosamente,</p><p><br></p><p>A Equipe do II CongressoGPro</p>';
						
						}else{
						
							$url = '<a href="'.url('profile-update').'">link</a>';
							$faq = '<a href="'.url('faq').'">click here</a>';
	
							$subject = "Your registration for GProCongress II has begun!";
							$msg = '<p>Dear '.$group['name'].',</p><p><br></p><p>'.$request->user()->name.' '.$request->user()->last_name.' has begun the registration process for the GProCongress II, by submitting your name!&nbsp;</p><p><br></p><p>We look forward to receiving your full application.</p><p>Please use this  '.$url.' to access, edit, and complete your account at any time.&nbsp;</p><p><br><div>Your registered email and password are:</div><div><br>Email: '.$to.'<br>Password: '.$password.'<br></div></p><p>If you want more information about the eligibility criteria for potential Congress attendees, before proceeding,  '.$faq.'.</p><p><br></p><p>To speak with one of our team members, you can simply respond to this email. We are here to help!</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
							
						}

						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Add Group');
						\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);
						
					
						// \App\Helpers\commonHelper::sendSMS($group['mobile']);

					}

					\App\Models\User::insert($users);

				} 

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'GroupInfo-updated-successfully');
				return response(array("error"=>true, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }


	public function spouseAdd(Request $request){ 

		
		$rules = [
            'is_spouse' => 'required|in:Yes,No',
            'is_spouse_registered' => 'required|in:Yes,No',
            'id' => 'required',
		];

		if($request->json()->get('is_spouse_registered')=='Yes'){

			$rules['email'] = 'required|email';

		}elseif($request->json()->get('is_spouse_registered')=='No'){
 
			$rules['email'] = 'required|email|unique:users,email,'.$request->json()->get('id');
			$rules['first_name'] = 'required';
			$rules['last_name'] = 'required';
			$rules['gender'] = 'required|in:1,2';
			$rules['date_of_birth'] = 'required|date';
			$rules['citizenship'] = 'required';
			$rules['salutation'] = 'required';
			
		}

		$messages = array(
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_email'),
			'email.unique' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_unique'),
			'first_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'first_name_required'),  
			'last_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'last_name_required'),
			'gender.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'gender_required'),
			'date_of_birth.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'date_of_birth_required'),
			'date_of_birth.date' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'date_of_birth_date'),
			'citizenship.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'citizenship_required'),
			'salutation.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'salutation_required'),
			
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
				
				$dob=date('Y-m-d',strtotime($request->json()->get('date_of_birth')));

				if((int)$request->json()->get('id') == 0){


					if($request->json()->get('is_spouse_registered')=='Yes'){

						$users = \App\Models\User::where([
							['email', '=', $request->json()->get('email')],
							['id', '!=', $request->user()->id]
							])->first();

						if(!$users){
							
							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-not-found');
							return response(array("error"=>true, 'message'=>$message), 403);
						
						}elseif($users->added_as == 'Spouse'){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-already-associated-withother-user');
							return response(array("error"=>true, "message"=>$message), 403);
						}

						$spouseName = \App\Models\user::where('parent_id', $users->id)->where('added_as','Spouse')->first();
						if($spouseName){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-already-associated-withother-user');
							return response(array("error"=>true, "message"=>$message), 403);
						}

						$reminderData = [
							'type'=>'spouse_reminder',
							'date'=>date('Y-m-d'),
							'reminder'=>'0',
						];
						
						if($users->stage >= 2){

							$usersP = \App\Models\User::where('id',$request->user()->id)->first();
							$usersP->parent_spouse_stage = $users->stage;
							$usersP->room = 'Sharing';
							$users->parent_spouse_stage = $users->stage;
							$usersP->save(); 
						}
						
						$users->parent_id = $request->user()->id;
						$users->added_as = 'Spouse';
						
						$users->spouse_confirm_token =md5(rand(1111,4444));
						$users->spouse_confirm_reminder_email =json_encode($reminderData);
						$users->save(); 

						$spouse_id = $users->id;

					}else if($request->json()->get('is_spouse_registered')=='No'){
						
						$existSpouse = \App\Models\User::where([
							['parent_id', '=', $request->user()->id],
							['added_as', '=', 'Spouse']
							])->first();
		
						if($existSpouse){
		
							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youhave-already-updated-spouse-detail');
							return response(array("error"=>true, 'message'=>$message), 200);
		
						}else{

							$users = \App\Models\User::where('email', $request->json()->get('email'))->first();

							if($users && $users->added_as == 'Spouse'){
								
								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-already-associated-withother-user');
								return response(array("error"=>true, "message"=>$message), 200);
							}

							$date1 = $dob;
							$date2 = date('Y-m-d');
							$diff = abs(strtotime($date2) - strtotime($date1));
							$years = floor($diff / (365*60*60*24));
						
							if($years<18){

								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'DateOfBirthyear-mustbemore-than-18years');
								return response(array("error"=>true, "message"=>$message), 200);
							
							}else{
								
								
								$token = md5(rand(1111,4444));
								$reminderData = [
									'type'=>'spouse_reminder',
									'date'=>date('Y-m-d'),
									'reminder'=>'0',
		
								];
								
								$users = array(
									'parent_id'=> $request->user()->id,
									'added_as'=>'Spouse',
									'salutation'=>$request->json()->get('salutation'),
									'name'=>$request->json()->get('first_name'),
									'last_name'=>$request->json()->get('last_name'),
									'email'=>$request->json()->get('email'),
									'gender'=>$request->json()->get('gender'),
									'dob'=>$dob,
									'citizenship'=>$request->json()->get('citizenship'),
									'reg_type'=>'email',
									'designation_id'=>'2',
									'otp_verified'=>'No',
									'system_generated_password'=>'1',
									'spouse_confirm_token'=>$token,
									'spouse_confirm_reminder_email'=>json_encode($reminderData),
								);
			
								$user =  \App\Models\User::insert($users);

								$spouse_id = \DB::getPdo()->lastInsertId();

								// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

							}
						}

					}

					\App\Models\User::where('id',$request->user()->id)->update(['spouse_id' => $spouse_id]);


				}else{

					$users = array(
						'parent_id'=> $request->user()->id,
						'added_as'=>'Spouse',
						'salutation'=>$request->json()->get('salutation'),
						'name'=>$request->json()->get('first_name'),
						'last_name'=>$request->json()->get('last_name'),
						'email'=>$request->json()->get('email'),
						'gender'=>$request->json()->get('gender'),
						'dob'=>$dob,
						'citizenship'=>$request->json()->get('citizenship'),
					);
			
					\App\Models\User::where('id',$request->json()->get('id'))->update($users);

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-update-successful');
					return response(array("error"=>false, "message"=>$message), 200);

				}


				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Spouse-added-successful');
				return response(array("error"=>false, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }


	public function stayRooms(Request $request){
	
		
		$rules = [
            'room' => 'required|in:Single,Sharing',
		];

		$messages = array(
			'room.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'room_required'),
			
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
				
				$users = \App\Models\User::where('id',$request->user()->id)->first();
				
				$users->room = $request->json()->get('room');
				$users->save();
				
				$subject='User Stay room update';
				$msg='User Stay room update';

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Stay room update');
				
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Stay-room-update-successful');
				return response(array("error"=>true, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function changePassword(Request $request){
	
		
		$rules = [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
		];

		$messages = array(
			'old_password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'old_password_required'),
			'new_password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'new_password_required'),
			'confirm_password.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'confirm_password_required'),
			'confirm_password.same' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'password_confirmed'),
			
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

			try {
				
				$users = \App\Models\User::where('id',$request->user()->id)->first();
				
				$users->password = \Hash::make($request->json()->get('new_password'));
				$users->system_generated_password = '0';
				$users->save();

				$name = $users->name.' '.$users->last_name;

				if($users->language == 'sp'){

					$subject = "????xito! Su contrase??a ha sido cambiada.";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p>Usted ha cambiado correctamente su contrase??a.</p><p><br></p><p>??A??n tiene preguntas o necesita ayuda? Simplemente, responda a este correo electr??nico y nuestro equipo se pondr?? en contacto con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($users->language == 'fr'){
				
					$subject = "Succ??s! Votre mot de passe a ??t?? modifi??";
					$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Vous avez chang?? votre mot de passe avec succ??s.&nbsp;&nbsp;</p><p><br></p><p>Avez-vous encore des questions ou avez-vous besoin d???aide ? R??pondez simplement ?? cet e-mail et notre ??quipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L?????quipe GProCongr??s II</p>';
				
				}elseif($users->language == 'pt'){
				
					$subject = "Sucesso! Sua senha foi alterada";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Voc?? mudou sua senha com sucesso.&nbsp;</p><p><br></p><p>Ainda tem perguntas, ou precisa de alguma assist??ncia? Simplesmente responda a este e-mail, e nossa equipe ir?? se conectar com voc??.</p><p><br></p><p>Ore conosco, ?? medida que nos esfor??amos para multiplicar os n??meros, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Success! Your password has been changed';
					$msg = '<div>Dear '.$name.',</div><div><br></div><div>You have successfully changed your password.&nbsp;</div><div><br></div><div>Do you still have questions, or require any assistance? Simply respond to this email, and our team will connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div>';
					
				}

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User change password');
				\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

				\App\Helpers\commonHelper::emailSendToUser($request->user()->email, $subject, $msg);

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'NewPassword-update-successful');
				return response(array("error"=>true, "message"=>$message, "result"=>$users->toArray()), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function updateProfile(Request $request){ 

		$designation = \App\Models\StageSetting::where('designation_id',$request->user()->designation_id)->first();

		if($designation->stage_zero == '1' && ($request->user()->stage == '0' || $request->user()->stage == '1')){

			$rules = [
				'salutation' => 'required|string',
				'first_name' => 'required|string',
				'last_name' => 'required|string',
				'gender' => 'required|in:1,2',
				'dob' => 'required|date',
				'citizenship' => 'required|string',
				'marital_status' => 'required|string|in:Married,Unmarried',
			];

			if($request->json()->get('marital_status')=='Unmarried'){

				$rule['room']="required|in:Single,Sharing";
			}
			
			$messages = array(
				'salutation.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'salutation_required'),
				'first_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'first_name_required'),
				'last_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'last_name_required'),
				'gender.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'gender_required'),
				'dob.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'date_of_birth_required'),
				'dob.date' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'date_of_birth_date'),
				'citizenship.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'citizenship_required'),
				'marital_status.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'marital_status_required'),
				'room.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'room_required'),
				
			);
	
			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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

					$dob=date('Y-m-d',strtotime($request->json()->get('dob')));
					$date1 = $dob;
					$date2 = date('Y-m-d');
					$diff = abs(strtotime($date2) - strtotime($date1));
					$years = floor($diff / (365*60*60*24));
					
					if($years<18){

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'DateOfBirthyear-mustbemore-than-18years');
						return response(array("error"=>true, 'message'=>$message), 200);
					}else{

						$user = \App\Models\User::find($request->user()->id);

						$user->salutation = $request->json()->get('salutation');
						$user->name = $request->json()->get('first_name');
						$user->last_name = $request->json()->get('last_name');
						$user->gender = $request->json()->get('gender');
						$user->dob = $dob;
						$user->marital_status = $request->json()->get('marital_status');

						if($request->json()->get('marital_status')=='Unmarried'){

							$user->room=$request->json()->get('room');
						}

						$subject='User Profile updated';
						$msg='User Profile updated';

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Profile updated');
						

						// if($user->profile_update == '0'){

						// 	$existSpouse = \App\Models\User::where([
						// 		['parent_id', '=', $request->user()->id],
						// 		['added_as', '=', 'Spouse'],
						// 		['spouse_confirm_token', '!=', null],
						// 		])->first();
							
						// 	if($existSpouse){
	
								
						// 		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			
						// 		$password = substr(str_shuffle($chars),0,8);
	
						// 		$existSpouse->password = \Hash::make($password);
						// 		$existSpouse->save();
	
							
						// 		$url = '<a href="'.url('profile-update').'">Click here</a>';
						// 		$faq = '<a href="'.url('faq').'">Click here</a>';
						// 		// $confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';
		
						// 		$UserHistory=new \App\Models\UserHistory();
						// 		$UserHistory->user_id=$request->user()->id;
						// 		$UserHistory->action='User Add spouse';
						// 		$UserHistory->save();
								
						// 		$name = $request->json()->get('salutation').' '.$request->json()->get('first_name').' '.$request->json()->get('last_name');
		
						// 		$to = $request->user()->email;
						// 		$subject = 'Your spouse has started your GProCongress II registration!';
						// 		$msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</div><div><br></div><div>'.$name.' has begun your registration for the GProCongress II! Please use this link '.$url.' to edit and complete your application at any time. Your registered email and password are:</div><div><br>Email: '.$existSpouse->email.'<br>Password: '.$password.'<br></div><div><br></div><div><br><div><br></div><div>You may change the password once you log in to the account. We recommend timely completion so that we can place you in the same room.</div><div><br></div><div>To find out more about the criteria to attend the Congress, '.$faq.'.</div><div><br></div><div>'.$name.', We are here to help! To talk with one of our team members, simply respond to this email.</div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div><br></div><div>Warmly,</div><div>GProCongress II Team</div>';
								
						// 		\App\Helpers\commonHelper::emailSendToUser($existSpouse->email, $subject, $msg);
		
						// 		// $subject = 'Please confirm your registration status';
						// 		// $msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</div><div><br></div><div>'.$name.' has registered for the GProCongress II as your spouse. Would you please confirm that '.$name.' is your spouse and that you both plan to attend?</div><div><br></div><div>Please click here to confirm:'.$confLink.'</div><div><br></div><div>To talk with one of our team members, simply respond to this email.</div><div></div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div><br></div><div>Warmly,</div><div>GProCongress II Team</div>';
								
						// 		// \App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
		
	
						// 	}else{

						// 		$existSpouse = \App\Models\User::where([
						// 			['parent_id', '=', $request->user()->id],
						// 			['added_as', '=', 'Spouse'],
						// 			['spouse_confirm_token', '=', null],
						// 			])->first();

						// 		if($existSpouse){

						// 			$token = md5(rand(1111,4444));

						// 			$existSpouse->spouse_confirm_token = $token;
						// 			$existSpouse->save();
		
						// 			$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';
						// 			$name = $request->user()->salutation.' '.$request->user()->name.' '.$request->user()->last_name;
		
						// 			$subject = 'Please confirm your registration status';
						// 			$msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</div><div><br></div><div>'.$name.' has registered for the GProCongress II as your spouse. Would you please confirm that '.$name.' is your spouse and that you both plan to attend?</div><div><br></div><div>Please click here to confirm:'.$confLink.'</div><div><br></div><div>To talk with one of our team members, simply respond to this email.</div><div></div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div><br></div><div>Warmly,</div><div>GProCongress II Team</div>';
									
						// 			\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
	
						// 		}
		
						// 	}
						// }

						$user->citizenship = $request->json()->get('citizenship');
						$user->payment_country = $request->json()->get('citizenship');
						$user->profile_update = '1';
						$user->profile_updated_at = date('Y-m-d H:i:s'); 
						$user->save(); 
						
						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Profile-updated-successfully');
						return response(array("error"=>true, 'message'=> $message), 200);

					} 

				}catch (\Exception $e){
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
					return response(array("error"=>true, "message" => $message), 403); 
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Profile-updated-successfully');
			return response(array("error"=>true, 'message'=> $message), 200);
		}
        
	}

	
	public function contactDetails(Request $request){

		$designation = \App\Models\StageSetting::where('designation_id',$request->user()->designation_id)->first();

		if($designation->stage_zero == '1' && ($request->user()->stage == '0' || $request->user()->stage == '1')){

			
			$rules = [
				'contact_address' => 'required',
				'contact_zip_code' => 'nullable',
				'contact_country_id' => 'required|numeric',
				'contact_state_id' => 'required|numeric',
				'contact_city_id' => 'required|numeric',
				'user_mobile_code' => 'required',
				'mobile' => 'required|numeric',
				'contact_business_codenumber' => 'nullable',
				'contact_business_number' => 'nullable|numeric',
				'whatsapp_number_same_mobile' => 'required|in:Yes,No',
			];

			if($request->json()->get('whatsapp_number_same_mobile')=='No'){

				$rules['contact_whatsapp_number'] = 'required|numeric';

			}
			if($request->json()->get('contact_state_id')=='0'){

				$rules['contact_state_name'] = 'required|string';

			}
			
			if($request->json()->get('contact_city_id')=='0'){

				$rules['contact_city_name'] = 'required|string';

			}
	
			$messages = array(
				'contact_address.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_address_required'),
				'contact_zip_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_zip_code_required'),
				'contact_country_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_country_id_required'),
				'contact_state_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_state_id_required'),
				'contact_city_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_city_id_required'),
				'user_mobile_code.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'user_mobile_code_required'),
				'mobile.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'mobile_required'),
				'whatsapp_number_same_mobile.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'whatsapp_number_same_mobile_required'),
				'contact_whatsapp_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_whatsapp_number_required'),
				'contact_state_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_state_name_required'),
				'contact_city_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'contact_city_name'),
				
			);

			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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
					
					$user = \App\Models\User::find($request->user()->id);

					$user->contact_address = $request->json()->get('contact_address');
					$user->contact_zip_code = $request->json()->get('contact_zip_code');
					$user->contact_country_id = $request->json()->get('contact_country_id');
					$user->contact_state_id = $request->json()->get('contact_state_id');
					$user->contact_city_id = $request->json()->get('contact_city_id');
					$user->phone_code = $request->json()->get('user_mobile_code');
					$user->mobile = $request->json()->get('mobile');
					$user->terms_and_condition = $request->json()->get('terms_and_condition');
					$user->contact_business_codenumber = $request->json()->get('contact_business_codenumber');
					$user->contact_business_number = $request->json()->get('contact_business_number');
					
					if($request->json()->get('whatsapp_number_same_mobile')=='Yes'){

						$user->contact_whatsapp_codenumber = $request->json()->get('user_mobile_code');
						$user->contact_whatsapp_number = $request->json()->get('mobile');
						
					}else{

						$user->contact_whatsapp_codenumber = $request->json()->get('contact_whatsapp_codenumber');
						$user->contact_whatsapp_number = $request->json()->get('contact_whatsapp_number');
		
					}

					if($request->json()->get('contact_state_id')=='0'){

						$user->contact_state_name = $request->json()->get('contact_state_name');
		
					}
					if($request->json()->get('contact_city_id')=='0'){

						$user->contact_city_name = $request->json()->get('contact_city_name');
		
					}


					$user->save();

					$subject='User Contact Details updated';
					$msg='User Contact Details updated';
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Contact Details updated');
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Contact-Details-updated-successfully');
					return response(array('message'=>$message), 200);
						
				}catch (\Exception $e){
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
					return response(array("error"=>true, "message" => $message), 403); 
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youare-not-allowedto-update-profile');
			return response(array("error"=>true, "message" => $message), 200); 
		}
        
	}

	public function ministryDetails(Request $request){

		$designation = \App\Models\StageSetting::where('designation_id',$request->user()->designation_id)->first();

		if($designation->stage_zero == '1' && ($request->user()->stage == '0' || $request->user()->stage == '1')){

			$rules = [
				'type' => 'required|in:preview,submit',
			];

			if($request->json()->get('type')=='preview'){

				$rules['ministry_address'] = 'required';
				$rules['ministry_zip_code'] = 'nullable';
				$rules['ministry_country_id'] = 'required|numeric';
				$rules['ministry_state_id'] = 'required|numeric';
				$rules['ministry_city_id'] = 'required|numeric';
				$rules['ministry_pastor_trainer'] = 'required|in:Yes,No';
				
				if($request->json()->get('ministry_state_id')=='0'){
	
					$rules['ministry_state_name'] = 'required|string';
	
				}
				if($request->json()->get('ministry_city_id')=='0'){
	
					$rules['ministry_city_name'] = 'required|string';
	
				}

			}
			
			$messages = array(
				'type.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'type'),
				'ministry_address.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_address'),
				'ministry_country_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_country_id'),
				'ministry_state_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_state_id'),
				'ministry_city_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_city_id'),
				'ministry_pastor_trainer.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_pastor_trainer'),
				'ministry_state_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_state_name'),
				'ministry_city_name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_city_name'),
				
				
			);
	
			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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
					
					if($request->json()->get('type')=='preview'){

						if($request->json()->get('ministry_pastor_trainer')=='Yes' && $request->user()->get('ministry_pastor_trainer_detail')==''){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Pastor-detail-not-found');
							return response(array("error"=>true, "message" => $message), 403); 
						}

						if($request->json()->get('ministry_pastor_trainer')=='No' && $request->user()->get('doyouseek_postoral')==''){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Pastor-detail-not-found');
							return response(array("error"=>true, "message" => $message), 403); 
						}
					}

					$user = \App\Models\User::find($request->user()->id);

					if($request->json()->get('type')=='preview'){

						$user->ministry_name = $request->json()->get('ministry_name') ?? '';
						$user->ministry_address = $request->json()->get('ministry_address');
						$user->ministry_zip_code = $request->json()->get('ministry_zip_code');
						$user->ministry_country_id = $request->json()->get('ministry_country_id');
						$user->ministry_state_id = $request->json()->get('ministry_state_id');
						$user->ministry_city_id = $request->json()->get('ministry_city_id'); 
						$user->ministry_pastor_trainer = $request->json()->get('ministry_pastor_trainer');
						$user->profile_submit_type = $request->json()->get('type');

						
						if($request->json()->get('ministry_state_id')=='0'){

							$user->ministry_state_name = $request->json()->get('ministry_state_name');
			
						}
						if($request->json()->get('ministry_city_id')=='0'){

							$user->ministry_city_name = $request->json()->get('ministry_city_name');
			
						}

						if($request->json()->get('ministry_pastor_trainer')=='No'){
							$user->ministry_pastor_trainer_detail = Null;
						}else{
							$user->doyouseek_postoral = Null; 
							$user->doyouseek_postoralcomment = Null; 
						}

					}else{

						
						$user->profile_submit_type = $request->json()->get('type');
						$user->profile_status = 'Review';
						$user->stage = '1';
					}

					
					$user->save();

					$subject='User Ministry details updated';
					$msg='User Ministry details updated';
					
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Ministry details updated');
					
					if($request->json()->get('type')=='submit'){

						if($user->profile_status == 'Review'){

							$existSpouse = \App\Models\User::where([
								['parent_id', '=', $request->user()->id],
								['added_as', '=', 'Spouse'],
								['spouse_confirm_token', '!=', null],
								])->first();
							
							if($existSpouse){
	
								$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			
								$password = substr(str_shuffle($chars),0,8);
	
								$existSpouse->password = \Hash::make($password);
								$existSpouse->save();
	
							
								
								$name = $request->user()->salutation.' '.$request->user()->name.' '.$request->user()->last_name;
		
								$to = $request->user()->email;

								if($existSpouse->language == 'sp'){

									$url = '<a href="'.url('profile-update').'">aqui</a>';
									$faq = '<a href="'.url('faq').'">aqui</a>';
									$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">aqui</a>';
		
									$subject = "??Su c??nyuge ha iniciado la inscripci??n al GproCongress II!";
									$msg = '<p>Estimado '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>'.$name.' ha iniciado el proceso de inscripci??n al GproCongress II al ingresar su nombre.&nbsp;</p><p><br></p><p>Quedamos a la espera de recibir su solicitud completa.</p><p><br></p><p>Por favor, utilice este enlace haga click'.$url.' para acceder, editar y completer su cuenta en cualquier momento. Completar su solicitud a tiempo nos ayudara para ubicarles en la misma habitaci??n. </div><div><br>Direcci??n de correo electr??nico: '.$existSpouse->email.'<br>Contrase??a: '.$password.'<br></div> </p><p><br></p><p>Por favor, utilice este enlace &lt;ink&gt; para acceder, editar y completer su cuenta en cualquier momento. haga click'.$faq.'</p><p><br></p><p>Si usted desea m??s informaci??n sobre los criterios de admisibilidad para candidatos potenciales al congreso, antes de continuar, haga click aqu??, &lt;Link&gt;</p><p><br></p><p>Para hablar con uno de los miembros de nuestro equipo, usted solo tiene que responder a este email. ??Estamos aqu?? para ayudarle!&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
								
								}elseif($existSpouse->language == 'fr'){
								
									$url = '<a href="'.url('profile-update').'">aqui</a>';
									$faq = '<a href="'.url('faq').'">Click here</a>';
									$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';
		
									$subject = "Votre conjoint/e a commenc?? votre inscription au GProCongr??s II!";
									$msg = '<p>Cher '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p>'.$name.' a commenc?? le processus d???inscription au GProCongr??s II, en soumettant votre nom !&nbsp;</p><p><br></p><p><br></p><p>Nous sommes impatients de recevoir votre demande compl??te. Veuillez utiliser ce lien '.$url.' pour acc??der, modifier et compl??ter votre compte ?? tout moment. L???ach??vement en temps opportun nous aidera ?? vous placer dans la m??me chambre. </div><div><br>Adresse e-mail: '.$existSpouse->email.'<br>Mot de passe: '.$password.'<br></div> </p><p><br></p><p>Si vous souhaitez plus d???informations sur les crit??res d?????ligibilit?? pour les participants potentiels au Congr??s, avant de continuer, cliquez ici '.$faq.'.</p><p><br></p><p><br></p><p><br></p><p>Pour parler ?? l???un des membres de notre ??quipe, vous pouvez simplement r??pondre ?? ce courriel. Nous sommes l?? pour vous aider !</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L?????quipe GProCongr??s II</p>';
								
								}elseif($existSpouse->language == 'pt'){
								
									$url = '<a href="'.url('profile-update').'">aqui</a>';
									$faq = '<a href="'.url('faq').'">Click here</a>';
									$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';
		
									$subject = "Seu c??njuge iniciou o seu processo de inscri????o para o II CongressoGPro!";
									$msg = '<p>Prezado/a '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p>'.$name.' iniciou com o processo de inscri????o para o II CongressoGPro, po submeter o seu nome!</p><p><br></p><p>N??s esperamos receber a sua inscri????o completa.</p><p>Por favor use este '.$url.' para aceder, editar e terminar a sua conta a qualquer momento. Ao completar a tempo, vai nos ajudar a colocar voc??s no mesmo quarto. </div><div><br>Endere??o Eletr??nico: '.$existSpouse->email.'<br>Senha: '.$password.'<br></div> </p><p><br></p><p>Se voc?? precisa de mais informa????es sobre o crit??rio de elegibilidade para participantes potenciais ao Congresso, antes de continuar, clique aqui '.$faq.'</p><p><br></p><p>Para falar com um dos nossos membros da equipe, voc?? pode simplesmente responder a este e-mail. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, a medida que nos esfor??amos para multiplicar o n??mero, e desenvolvemos a capacidade dos treinadores de pastores&nbsp;</p><p><br></p><p>Calorosamente,</p><p><br></p><p>A Equipe do II CongressoGPro</p>';
								
								}else{
								
									$url = '<a href="'.url('profile-update').'">link</a>';
									$faq = '<a href="'.url('faq').'"> click here</a>';
									$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'"> click here</a>';
		
									$subject = 'Your spouse has started your GProCongress II registration!';
									$msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.' </div><div><br></div><div>'.$name.' has begun the registration process for the GProCongress II, by submitting your name!&nbsp;</div><div>We look forward to receiving your full application.</div><div>Please use this  '.$url.' to access, edit, and complete your account at any time. Your registered email and password are:</div><div><br>Email: '.$existSpouse->email.'<br>Password: '.$password.'<br></div> Timely completion will help us place you in the same room.</div><div><br></div><div>If you want more information about the eligibility criteria for potential Congress attendees, before proceeding, '.$faq.'.</div><div><br></div><div>To speak with one of our team members, you can simply respond to this email. We are here to help!</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div><div><br></div>';
									
								}

								\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Add spouse');
								\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

								\App\Helpers\commonHelper::emailSendToUser($existSpouse->email, $subject, $msg);
		
								if($existSpouse->language == 'sp'){

									$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", ??usted tambi??n viene?";
									$msg = '<p>Estimado(a) '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p><br></p><p><br></p><p>Hemos recibido la solicitud de '.$name.' para el GproCongress II.</p><p><br></p><p>'.$name.' nos ha indicado que ustedes estar??n asistiendo al congreso juntos.&nbsp;</p><p><br></p><p>Su aplicaci??n ha sido recibida, para avanzar con el proceso de inscripci??n, necesitamos verificar algunos datos.</p><p>Por favor, responda a este correo para confirmar que usted y '.$confLink.', est??n casados, y que asistiran juntos al GProCongress II.</p><p><br></p><p>Una vez que recibamos su respuesta '.$name.', por favor, espere un correo de nuestra parte, solicitando informaci??n adicional y/o confirmando su inscripci??n.&nbsp;</p><p><br></p><p><br></p><p>??Les esperamos a usted y a '.$name.' en Ciudad de Panam??, Panam??, del 12 al 17 de noviembre de 2023!&nbsp;</p><p><br></p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p>&nbsp;</p><p>El Equipo de GProCongress II</p>';
								
								}elseif($existSpouse->language == 'fr'){
								
									$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", venez-vous aussi ?";
									$msg = '<p>Cher/Ch??re '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',&nbsp;</p><p><br></p><p>Nous avons re??u la demande de '.$name.' pour le GProCongr??s II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqu?? que vous assisterez au Congr??s ensemble.&nbsp;&nbsp;</p><p>Votre demande a ??galement ??t?? re??ue, mais pour poursuivre le processus de votre inscription, nous devons v??rifier certaines informations.&nbsp;</p><p><br></p><p>Veuillez r??pondre ?? cet e-mail pour confirmer que vous et '.$confLink.' ??tes mari??s et que vous assisterez ensemble au GProCongr??s II.&nbsp;&nbsp;</p><p><br></p><p>Une fois que nous aurons re??u votre r??ponse '.$name.', attendez-vous ?? recevoir des courriels de notre part, demandant des informations suppl??mentaires et / ou confirmant votre inscription.&nbsp;</p><p><br></p><p>Nous avons h??te de vous voir, vous et '.$name.', ?? Panama City, au Panama du 12 au 17 novembre 2023 !&nbsp;&nbsp;</p><p><br></p><p>&nbsp;Cordialement,&nbsp;</p><p>L?????quipe GProCongr??s II</p>';
								
								}elseif($existSpouse->language == 'pt'){
								
									$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", voc?? tamb??m vem?";
									$msg = '<p>Prezado/a '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>N??s recebemos o pedido do '.$name.' para o II CongressoGPro.&nbsp; &nbsp;</p><p><br></p><p>'.$name.' indicou que voc??s iriam participar do Congresso juntos.</p><p>O seu pedido tamb??m foi recebido, mas para darmos continuidade com a sua inscri????o, n??s precisamos de verificar algumas informa????es.</p><p><br></p><p>Por favour responda a este email para confirmar que voc?? e '.$confLink.' s??o casados e que voc??s v??o participar do II CongressoGPro juntos.&nbsp;</p><p><br></p><p><br></p><p>Assim que recebermos sua resposta '.$name.', por favor, aguarde e-mails vindos da nossa parte, solicitando informa????es adicionais e/ou confirmando sua inscri????o.</p><p><br></p><p>N??s esperamos ver voc??s juntos, voc?? e '.$name.', na cidade de Panam??, Panam?? nos dias 12 a 17 de Novembro de 2023!</p><p>&nbsp;</p><p>Calorosamente,</p><p>Equipe do CongressoGPro&nbsp;</p>';
								
								}else{
								
									$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', are you coming, too?';
									$msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</div><div><br></div><div>We have received '.$name.'???s application for GProCongress II.&nbsp;&nbsp;</div><div><br></div><div>'.$name.' has indicated that you will be attending the Congress, together.&nbsp;&nbsp;</div><div>Your application has also been received, but to further process your registration, we need to verify some information.</div><div><br></div><div>Please reply to this email to confirm&nbsp;<span style="color: rgb(0, 0, 0); background-color: transparent;">'.$confLink.'</span><span style="color: rgb(0, 0, 0); background-color: transparent;">&nbsp;that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</span></div><div><br></div><div>Once we receive your reply, please expect emails from us, asking for additional information, and/or confirming your registration.</div><div><br></div><div>We look forward to seeing, both, you and '.$name.', in Panama City, Panama on November 12-17, 2023!&nbsp;</div><div>&nbsp;</div><div>Warmly,</div><div>GProCongress Team&nbsp;<br></div>';
									
								}

								\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);
		
	
							}else{

								$existSpouse = \App\Models\User::where([
									['parent_id', '=', $request->user()->id],
									['added_as', '=', 'Spouse'],
									['spouse_confirm_token', '=', null],
									])->first();

								if($existSpouse){

									$token = md5(rand(1111,4444));

									$existSpouse->spouse_confirm_token = $token;
									$existSpouse->save();
		
									$confLink = '<a href="'.url('spouse-confirm-registration/'.$existSpouse->spouse_confirm_token).'">Click here</a>';
									$name = $request->user()->salutation.' '.$request->user()->name.' '.$request->user()->last_name;
		
									if($existSpouse->language == 'sp'){

										$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", ??usted tambi??n viene?";
										$msg = '<p>Estimado(a) '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.'</p><p><br></p><p><br></p><p><br></p><p>Hemos recibido la solicitud de '.$name.' para el GproCongress II.</p><p><br></p><p>'.$name.' nos ha indicado que ustedes estar??n asistiendo al congreso juntos.&nbsp;</p><p><br></p><p>Su aplicaci??n ha sido recibida, para avanzar con el proceso de inscripci??n, necesitamos verificar algunos datos.</p><p>Por favor, responda a este correo para confirmar que usted y '.$confLink.', est??n casados, y que asistiran juntos al GProCongress II.</p><p><br></p><p>Una vez que recibamos su respuesta '.$name.', por favor, espere un correo de nuestra parte, solicitando informaci??n adicional y/o confirmando su inscripci??n.&nbsp;</p><p><br></p><p><br></p><p>??Les esperamos a usted y a '.$name.' en Ciudad de Panam??, Panam??, del 12 al 17 de noviembre de 2023!&nbsp;</p><p><br></p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p>&nbsp;</p><p>El Equipo de GProCongress II</p>';
									
									}elseif($existSpouse->language == 'fr'){
									
										$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", venez-vous aussi ?";
										$msg = '<p>Cher/Ch??re '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',&nbsp;</p><p><br></p><p>Nous avons re??u la demande de '.$name.' pour le GProCongr??s II.&nbsp;&nbsp;</p><p><br></p><p>'.$name.' a indiqu?? que vous assisterez au Congr??s ensemble.&nbsp;&nbsp;</p><p>Votre demande a ??galement ??t?? re??ue, mais pour poursuivre le processus de votre inscription, nous devons v??rifier certaines informations.&nbsp;</p><p><br></p><p>Veuillez r??pondre ?? cet e-mail pour confirmer que vous et '.$confLink.' ??tes mari??s et que vous assisterez ensemble au GProCongr??s II.&nbsp;&nbsp;</p><p><br></p><p>Une fois que nous aurons re??u votre r??ponse '.$name.', attendez-vous ?? recevoir des courriels de notre part, demandant des informations suppl??mentaires et / ou confirmant votre inscription.&nbsp;</p><p><br></p><p>Nous avons h??te de vous voir, vous et '.$name.', ?? Panama City, au Panama du 12 au 17 novembre 2023 !&nbsp;&nbsp;</p><p><br></p><p>&nbsp;Cordialement,&nbsp;</p><p>L?????quipe GProCongr??s II</p>';
									
									}elseif($existSpouse->language == 'pt'){
									
										$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.", voc?? tamb??m vem?";
										$msg = '<p>Prezado/a '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</p><p><br></p><p>N??s recebemos o pedido do '.$name.' para o II CongressoGPro.&nbsp; &nbsp;</p><p><br></p><p>'.$name.' indicou que voc??s iriam participar do Congresso juntos.</p><p>O seu pedido tamb??m foi recebido, mas para darmos continuidade com a sua inscri????o, n??s precisamos de verificar algumas informa????es.</p><p><br></p><p>Por favour responda a este email para confirmar que voc?? e '.$confLink.' s??o casados e que voc??s v??o participar do II CongressoGPro juntos.&nbsp;</p><p><br></p><p><br></p><p>Assim que recebermos sua resposta '.$name.', por favor, aguarde e-mails vindos da nossa parte, solicitando informa????es adicionais e/ou confirmando sua inscri????o.</p><p><br></p><p>N??s esperamos ver voc??s juntos, voc?? e '.$name.', na cidade de Panam??, Panam?? nos dias 12 a 17 de Novembro de 2023!</p><p>&nbsp;</p><p>Calorosamente,</p><p>Equipe do CongressoGPro&nbsp;</p>';
									
									}else{
									
										$subject = $existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.', are you coming, too?';
										$msg = '<div>Dear '.$existSpouse->salutation.' '.$existSpouse->name.' '.$existSpouse->last_name.',</div><div><br></div><div>We have received '.$name.'???s application for GProCongress II.&nbsp;&nbsp;</div><div><br></div><div>'.$name.' has indicated that you will be attending the Congress, together.&nbsp;&nbsp;</div><div>Your application has also been received, but to further process your registration, we need to verify some information.</div><div><br></div><div>Please reply to this email to confirm&nbsp;<span style="color: rgb(0, 0, 0); background-color: transparent;">'.$confLink.'</span><span style="color: rgb(0, 0, 0); background-color: transparent;">&nbsp;that you and '.$name.' are married, and that you will be attending GProCongress II, together.&nbsp;&nbsp;</span></div><div><br></div><div>Once we receive your reply, please expect emails from us, asking for additional information, and/or confirming your registration.</div><div><br></div><div>We look forward to seeing, both, you and '.$name.', in Panama City, Panama on November 12-17, 2023!&nbsp;</div><div>&nbsp;</div><div>Warmly,</div><div>GProCongress Team&nbsp;<br></div>';
										
									}


									\App\Helpers\commonHelper::emailSendToUser($existSpouse->email,$subject, $msg);
									\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

								}
		
							}
						}

						$to = $user->email;
						$name = $user->name.' '.$user->last_name;

						if($request->user()->language == 'sp'){

							$subject = $name.", hemos recibido su solicitud para el GproCongress II";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>Gracias por enviar su solicitud completa para asistir al GProCongress II. Nuestro equipo revisar?? dicha solicitud y le informar?? sobre el estado de esta lo antes posible.</p><p><br></p><p>Mientras tanto ??tendr?? usted alguna pregunta? Simplemente responda a este correo para conectarse con uno de los miembros de nuestro equipo. Estamos aqu?? para ayudarle.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
						
						}elseif($request->user()->language == 'fr'){
						
							$subject = $name.", nous avons re??u votre candidature du GProCongr??s II";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d???avoir soumis votre candidature compl??te pour assister au GProCongr??s II !&nbsp;</p><p>Notre ??quipe examinera le document et vous r??pondra avec des informations concernant le statut de la demande, d??s que possible.&nbsp;</p><p><br></p><p>Avez-vous des questions entre-temps ? Il suffit de r??pondre ?? cet e-mail pour communiquer avec un membre de l?????quipe.&nbsp; Nous sommes l?? pour vous aider !</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L?????quipe GProCongr??s II</p>';
						
						}elseif($request->user()->language == 'pt'){
						
							$subject = $name.", n??s recebemos a sua inscri????o para o II CongressoGPro.";
							$msg = '<p>Prezado '.$name.',</p><p><br></p><p><br></p><p>Agradecemos por submeter sua inscri????o completa para participar do II CongressoGPro!</p><p>Nossa equipe vai rever o documento, e ir?? responder com informa????es relacionadas ao estado da inscri????o, o mais cedo poss??vel.</p><p><br></p><p><br></p><p>Tem alguma pergunta por agora? Simplesmente responda a este e-mail para se conectar com membro da nossa equipe. Estamos aqui para ajudar!</p><p><br></p><p>Ore conosco, ?? medida que nos esfor??amos para multiplicar os n??meros, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = $name.', we have received your GProCongress II application';
							$msg = '<div><div>Dear '.$name.',</div><div><br></div><div>Thank you for submitting your complete application to attend the GProCongress II!&nbsp;</div><div>Our team will review the document, and respond with information regarding the status of the application, as soon as possible.</div><div><br></div><div>Do you have questions in the meantime? Simply respond to this email to connect with a team member. We are here to help!</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div><br></div><div>The GProCongress II Team</div></div>';
							
						}

						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, 'profile_update', $user);
						\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

						$subject = '[GProCongress II Admin] User Profile Updated';
						$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has completed registration for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Please review the candidature.</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
						
						\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg, 'profile_update', $user);

						// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Profile-details-submit-successfully');
						return response(array("error"=>true, "message" => $message), 200); 
					
					}else{

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Please-verify-ministry-details');
						return response(array("error"=>true, "message" => $message), 200); 

					}

				}catch (\Exception $e){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
					return response(array("error"=>true, "message" => $message), 403); 
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youare-not-allowedto-update-profile');
			return response(array("error"=>true, "message" => $message), 200);
		}
        
	}

	public function updatePastoralLeader(Request $request){
 
		if($request->json()->get('ministry_pastor_trainer')=='Yes'){

			$rules = [
				'ministry_pastor_trainer' => 'required|in:Yes,No',
				'non_formal_trainor' => 'required', 
				'informal_personal' => 'required', 
				'howmany_pastoral' => 'required', 
				'howmany_futurepastor' => 'required',
				'comment' => 'required',
				'willing_to_commit' => 'required'
			];

		}else{

			$rules = [
				'ministry_pastor_trainer' => 'required|in:Yes,No',
				'pastorno' => 'required|in:Yes,No', 
				'comment' => 'required'
			];

		}

		$messages = array(
			'ministry_pastor_trainer.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_pastor_trainer'),
			'non_formal_trainor.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'non_formal_trainor'),
			'informal_personal.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'informal_personal'),
			'howmany_pastoral.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'howmany_pastoral'),
			'howmany_futurepastor.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'howmany_futurepastor'),
			'comment.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'comment_required'),
			'ministry_pastor_trainer.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'ministry_pastor_trainer'),
			'pastorno.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'pastorno'),
				
		);

		$validator = \Validator::make($request->json()->all(), $rules,$messages);
			
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
				
				$user = \App\Models\User::find($request->user()->id); 

				if($request->json()->get('ministry_pastor_trainer')=='Yes'){

					$data=array(
						'non_formal_trainor'=>$request->json()->get('non_formal_trainor'),
						'formal_theological'=>$request->json()->get('formal_theological'),
						'informal_personal'=>$request->json()->get('informal_personal'),
						'howmany_pastoral'=>$request->json()->get('howmany_pastoral'),
						'howmany_futurepastor'=>$request->json()->get('howmany_futurepastor'), 
						'comment'=>$request->json()->get('comment') ?? '', 
						'willing_to_commit'=>$request->json()->get('willing_to_commit') ?? '', 
					);

					$user->ministry_pastor_trainer_detail = json_encode($data); 
					$user->doyouseek_postoral = null; 
					$user->doyouseek_postoralcomment = null; 

				}else{
 
					$user->ministry_pastor_trainer_detail = null; 
					$user->doyouseek_postoral = $request->json()->get('pastorno'); 
					$user->doyouseek_postoralcomment = $request->json()->get('comment'); 
					
				}

				$subject='Ministry Pastor detail updated';
				$msg='Ministry Pastor detail updated';
				
				\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'Ministry Pastor detail updated');
				
				$user->save();

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Ministry-Pastor-detail-updated-successfully');
				return response(array("error"=>true, "message" => $message), 200);
					
			}catch (\Exception $e){
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
				return response(array("error"=>true, "message" => $message), 403);
			}
		} 
        
	}


	public function travelInfo(Request $request){

		$designation = \App\Models\StageSetting::where('designation_id', $request->user()->designation_id)->first();

		if($designation->stage_three == '1' && $request->user()->stage == '3'){

			$rules = [
				'arrival_flight_number' => 'required',
				'arrival_start_location' => 'required',
				'arrival_date_departure' => 'required',
				'arrival_date_arrival' => 'required',
				'departure_flight_number' => 'required',
				'departure_start_location' => 'required',
				'departure_date_departure' => 'required',
				'departure_date_arrival' => 'required',
				'logistics_dropped' => 'required',
				'logistics_picked' => 'required',
				'mobile' => 'required',
				'name' => 'required',
			];

			if($request->json()->get('spouse_arrival_flight_number')){

				$rules = [
					
					'spouse_arrival_flight_number' => 'required',
					'spouse_arrival_start_location' => 'required',
					'spouse_arrival_date_departure' => 'required',
					'spouse_arrival_date_arrival' => 'required',
					'spouse_departure_flight_number' => 'required',
					'spouse_departure_start_location' => 'required',
					'spouse_departure_date_departure' => 'required',
					'spouse_departure_date_arrival' => 'required',
				];
			}
			
			$messages = array(
				'arrival_flight_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'arrival_flight_number'),
				'arrival_start_location.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'arrival_start_location'),
				'arrival_date_departure.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'arrival_date_departure'),
				'arrival_date_arrival.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'arrival_date_arrival'),
				'departure_flight_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'departure_flight_number'),
				'departure_start_location.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'departure_flight_number'),
				'departure_date_departure.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'departure_date_departure'),
				'departure_date_arrival.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'departure_date_arrival'),
				'logistics_dropped.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'logistics_dropped'),
				'logistics_picked.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'logistics_picked'),
				'mobile.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'mobile_required'),
				'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'name_required'),
				'spouse_arrival_flight_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_arrival_flight_number'),
				'spouse_arrival_start_location.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_arrival_start_location'),
				'spouse_arrival_date_departure.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_arrival_date_departure'),
				'spouse_arrival_date_arrival.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_arrival_date_arrival'),
				'spouse_departure_flight_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_departure_flight_number'),
				'spouse_departure_start_location.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_departure_start_location'),
				'spouse_departure_date_departure.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_departure_date_departure'),
				'spouse_departure_date_arrival.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'spouse_departure_date_arrival'),
				
					
			);
	
			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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

					$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();

					if ($result) {
						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Your-travelinfo-hasbeenalready-added');
						return response(array("error"=>true, "message" => $message), 403);
					}

					$return_flight_details = '';

					$flight_details = [
						'arrival_flight_number'=>$request->json()->get('arrival_flight_number'),
						'arrival_start_location'=>$request->json()->get('arrival_start_location'),
						'arrival_date_arrival'=>$request->json()->get('arrival_date_arrival'),
						'arrival_date_departure'=>$request->json()->get('arrival_date_departure'),
						'departure_flight_number'=>$request->json()->get('departure_flight_number'),
						'departure_start_location'=>$request->json()->get('departure_start_location'),
						'departure_date_departure'=>$request->json()->get('departure_date_departure'),
						'departure_date_arrival'=>$request->json()->get('departure_date_arrival'),
						
					];
				
					if($request->json()->get('spouse_arrival_flight_number')){

						$return_flight_details = [
							'spouse_arrival_flight_number'=>$request->json()->get('spouse_arrival_flight_number'),
							'spouse_arrival_start_location'=>$request->json()->get('spouse_arrival_start_location'),
							'spouse_arrival_date_departure'=>$request->json()->get('spouse_arrival_date_departure'),
							'spouse_arrival_date_arrival'=>$request->json()->get('spouse_arrival_date_arrival'),
							'spouse_departure_flight_number'=>$request->json()->get('spouse_departure_flight_number'),
							'spouse_departure_start_location'=>$request->json()->get('spouse_departure_start_location'),
							'spouse_departure_date_departure'=>$request->json()->get('spouse_departure_date_departure'),
							'spouse_departure_date_arrival'=>$request->json()->get('spouse_departure_date_arrival'),
						];
					}

					$travelinfo = new \App\Models\TravelInfo();

					$travelinfo->user_id = $request->user()->id;
					$travelinfo->flight_details = json_encode($flight_details);
					$travelinfo->hotel_information = '';
					$travelinfo->return_flight_details = json_encode($return_flight_details);
					$travelinfo->logistics_dropped = $request->json()->get('logistics_dropped');
					$travelinfo->logistics_picked = $request->json()->get('logistics_picked');
					$travelinfo->mobile = $request->json()->get('mobile');
					$travelinfo->name = $request->json()->get('name');
					$travelinfo->save();

					if($request->json()->get('share_your_room_with')){

						$user= \App\Models\User::where('id',$request->user()->id)->first();
						$user->share_your_room_with=$request->json()->get('share_your_room_with');
						$user->save();
					}
					$url = '<a href="'.url('profile-update').'">Click here</a>';
					$to = $request->user()->email;
					$subject = 'Please verify your travel Information.';
					$msg = '<p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'Travel information completed');
					
					$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$request->user()->id)->first();
		
					if ($result) {
						$to = $result->email;
						
						if($result->language == 'sp'){

							$subject = "Por favor, verifique su informaci??n de viaje";
							$msg = '<p>Estimado '.$result->name.' '.$result->last_name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su informaci??n de viaje.&nbsp;</p><p><br></p><p>A continuaci??n, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la informaci??n recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: '.$url.' para verificar que la informaci??n es correcta.</p><p><br></p><p>Gracias por su colaboraci??n.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
						
						}elseif($result->language == 'fr'){
						
							$subject = "Veuillez v??rifier vos informations de voyage";
							$msg = "<p>Cher '.$result->name.' '.$result->last_name.',&nbsp;</p><p><br></p><p>Merci d???avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons r??dig??e bas??e sur les informations re??ues.&nbsp;</p><p><br></p><p>Pourriez-vous s???il vous pla??t examiner la lettre, puis cliquer sur ce lien: '.$url.' pour v??rifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L?????quipe du GProCongr??s II</p><div><br></div>";
				
						}elseif($result->language == 'pt'){
						
							$subject = "Por favor verifique sua Informa????o de Viagem";
							$msg = '<p>Prezado '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Agradecemos por submeter sua informa????o de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informa????o que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e da?? clicar neste link: '.$url.' para verificar que a informa????o esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p><div><br></div>';
						
						}else{
						
							$subject = 'Please verify your travel Information.';
							$msg = '<p>Dear '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
												
						}
						$pdf = \PDF::loadView('email_templates.travel_info', $result->toArray());
						$pdf->setPaper('L');
						$pdf->output();
						$canvas = $pdf->getDomPDF()->getCanvas();
						
						$height = $canvas->get_height();
						$width = $canvas->get_width();
						$canvas->set_opacity(.2,"Multiply");
						$canvas->page_text($width/5, $height/2, 'Draft', null,
						70, array(0,0,0),2,2,-30);

						$fileName = strtotime("now").rand(11,99).'.pdf';

						$path = public_path('uploads/file/');
						
						$pdf->save($path . '/' . $fileName);
						
						$model = \App\Models\TravelInfo::find($travelinfo->id);

						$model->draft_file = $fileName;
						$model->save();


						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
						\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($result->mobile);

					} 
					$name = $request->user()->name.' '.$request->user()->last_name;
					$subject = '[GProCongress II Admin]  Travel Info Submitted by User';
					$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has updated Travel Info for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Please review the updated info.</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
					\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

					// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
					

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Travel-information-hasbeen-successfully-completed');
					return response(array("error"=>true, "message" => $message), 200);
						
				}catch (\Exception $e){
					return response(array("error"=>true, "message" => "Something went wrong.please try again"), 403); 
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youarenot-allowedto-updateTravelInformation');
			return response(array("error"=>true, "message" => $message), 403);
		}
        
	}

	public function travelInfoVerify(Request $request){

		$designation = \App\Models\StageSetting::where('designation_id', $request->user()->designation_id)->first();

		if($designation->stage_three == '1' && $request->user()->stage == '3'){

			$rules = [
				'status' => 'required|in:0,1',
			];
	
			$messages = array(
				'status.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'status_required_validation'),
					
			);

			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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

					$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();

					if (!$result) {

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'TravelInfo-doesnot-exist');
						return response(array("error"=>true, "message" => $message), 403);
					}

					$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->update(['user_status' => $request->json()->get('status')]);

					$to = $request->user()->email;
					$name = $request->user()->name.' '.$request->user()->last_name;
					$subject = '[GProCongress II Admin] Preliminary Visa Letter verified by User';
					$msg = 'Your travel info has been verified successfully';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'Preliminary Visa Letter verified by User');
					\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

					$subject = '[GProCongress II Admin] Preliminary Visa Letter verified by User';
					$msg = '<p><span style="font-size: 14px;"><font color="#424242">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#424242">'.$name.' has verified Visa Invitation letter for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#424242">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#424242">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#424242">Please review and take next steps.</font></span></p><p><span style="font-size: 14px;"><font color="#424242"><br></font></span></p><p><span style="font-size: 14px;"><font color="#424242">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#424242">Team GPro</font></span></p>';
					\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

					// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
					

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Preliminary-Visa-Letter-successfully-verified');
					return response(array("error"=>true, "message" => $message), 200);
						
				}catch (\Exception $e){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
					return response(array("error"=>true, "message" => $message), 200);
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youarenot-allowedto-updateTravelInformation');
			return response(array("error"=>true, "message" => $message), 403);
		}
        
	}

	public function travelInfoRemark(Request $request){

		$designation = \App\Models\StageSetting::where('designation_id', $request->user()->designation_id)->first();

		if($designation->stage_three == '1' && $request->user()->stage == '3'){

			$rules = [
				'remark' => 'required',
			];
	
			$messages = array(
				'remark.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'remark_required_validation'),
					
			);

			$validator = \Validator::make($request->json()->all(), $rules,$messages);
			 
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

					$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();

					if (!$result) {

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'TravelInfo-doesnot-exist');
						return response(array("error"=>true, "message" => $message), 403);

					}

					$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->update(['remark' => $request->json()->get('remark')]);

					$subject = '[GProCongress II Admin] Remark added by User for Travel Information';
					$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$request->user()->name.' '.$request->user()->last_name.' has added a remark for travel Info for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$request->user()->name.' '.$request->user()->last_name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$request->user()->email.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Please review and revert.</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Travel Info Remark');
					
					\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

					// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'TravelInformation-remarksubmit-successful');
					return response(array("error"=>true, "message" => $message), 200);
						
				}catch (\Exception $e){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
					return response(array("error"=>true, "message" => $message), 403);
				}
			}

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'TravelInfo-doesnot-exist');
			return response(array("error"=>true, "message" => $message), 403);
		}
        
	}

	public function sessionInfo(Request $request){

		$rules = [
			'session_id' => 'required',
			'session_date' => 'required',
			'session' => 'required',
		];

		$messages = array(
			'session_id.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'session_id'),
			'session_date.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'session_date'),
			'session.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'session_required_validation'),
				
		);

		$validator = \Validator::make($request->json()->all(), $rules,$messages);
			
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
				
				$sessions = [];
				$sessionData = \App\Models\SessionInfo::where('user_id',$request->user()->id)->first();

				if($sessionData && $sessionData->submit_status == '1'){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Youarenot-allowedto-updatesession-information');
					return response(array("error"=>true, "message" => $message), 403);

				}else{

					// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

					$inputData = [];
					if(count($request->json()->get('session_date')) > 0 && count($request->json()->get('session_id')) == count($request->json()->get('session'))) {
						foreach ($request->json()->get('session_date') as $key => $session_date) {
							
							// if($request->json()->get('session')[$key] == 'Yes') {

								if(!array_key_exists($session_date, $inputData)) {
									$inputData[$session_date] = [];
								}
		
								$session = [ 
									'session_id' => $request->json()->get('session_id')[$key],
									'session' => $request->json()->get('session')[$key]
								];
			
								array_push($inputData[$session_date], $session);
							// }
						}
					}

					if(count($inputData) > 0) {
						foreach ($inputData as $key => $data) {
							
							$date = $key;
							if(count($data) > 0) {
								$sessioninfo = \App\Models\SessionInfo::where('user_id', $request->user()->id)->whereDate('day', $date)->first();
								
								if(!$sessioninfo){
									$sessioninfo = new \App\Models\SessionInfo();
									$sessioninfo->user_id = $request->user()->id;
									$sessioninfo->day = $date;
								}
							}

							$sessions = [];
							$session_ids = [];

							if(count($data) > 0) {
								foreach ($data as $value) {
			
									if($value['session'] == 'Yes'){
										
										$sessionsData = \App\Models\DaySession::select('id')->whereDate('date', $date)->where('id', $value['session_id'])->first();
										if($sessionsData){
			
											$session_ids[] = $value['session_id'];
											$sessions[] = $value['session'];
			
										}
									}else{
										$sessions = [];
										$session_ids = [];
									}
								
								}
							}

							if(count($data) > 0) {
								$sessioninfo->session = implode(',', $sessions);
								$sessioninfo->session_id = implode(',', $session_ids);
								$sessioninfo->user_status = '0';
								$sessioninfo->admin_status = '0';
								$sessioninfo->submit_status = '0';
								$sessioninfo->save();
							}

						}
					}
	
					$to = $request->user()->email;
					$subject = 'Session Added';
					$msg = 'Your session has been added successfully';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Session Added');
					
					$subject = '[GProCongress II Admin] Session Info Updated By User';
					$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$request->user()->name.' '.$request->user()->last_name.' has updated session Info for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$request->user()->name.' '.$request->user()->last_name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$request->user()->email.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Please review and revert.</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
					\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);
					

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Session-information-hasbeen-successfully-completed');
					return response(array("error"=>true, "message" => $message), 200);
				}
					
			}catch (\Exception $e){
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
				return response(array("error"=>true, "message" => $message), 403);
			}
		}

        
	}

	public function sessionInfoVerify(Request $request){

		
		if($request->user()->stage == '4'){

			try{

				$result = \App\Models\SessionInfo::where('user_id', $request->user()->id)->get();

				if (empty($result)) {

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Sessioninfo-doesnot-exists');
					return response(array("error"=>true, "message" => $message), 403);
				}

				$resultData = \App\Models\SessionInfo::where('user_id', $request->user()->id)->get();

				foreach($resultData as $key=>$infoData){

					$sessioninfo = \App\Models\SessionInfo::where('id',$infoData->id)->first();

					$sessioninfo->user_status = '1';
					$sessioninfo->admin_status = '1';
					$sessioninfo->submit_status = '1';
					$sessioninfo->save();
				}

				$user = \App\Models\User::find($request->user()->id);
				$user->stage = 5;
				$user->qrcode = \QrCode::size(300)->generate(url('/'));
				$user->save();

				$to = $request->user()->email;
				$subject = 'Session Info Verify';
				$msg = 'Your session info has been verifed successfully';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

				$subject = '[GProCongress II Admin] User Session Info Verify';
				$msg = 'Session info verified by user on Gpro.';
				\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

				// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
				

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Session-information-hasbeen-successfullyverified');
				return response(array("error"=>true, "message" => $message), 200);
					
			}catch (\Exception $e){
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again');
				return response(array("error"=>true, "message" => $message), 403);
			}
			

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Sessioninfo-doesnot-exists');
			return response(array("error"=>true, "message" => $message), 403);
		}
        
	}

	
	public function sponsorPaymentsSubmit(Request $request){
	
		
		$rules = [
            'amount' => 'required|numeric',
            'name' => 'required',
            'email' => 'required|email',
		];

		$messages = array(
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'amount_required'),
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'name_required'),
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_required'),
			'email.email' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_email'),
				
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

			try {

				if(\App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id) == 0){
					

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'No-payment-due');
					return response(array("error"=>true, "message" => $message), 403);

				}else{

					$token = md5(rand(1111,4444));

					$users = new \App\Models\SponsorPayment();
					$users->user_id = $request->user()->id;
					$users->name = $request->json()->get('name');
					$users->email = $request->json()->get('email');
					$users->amount = $request->json()->get('amount');
					$users->token = $token;
					$users->save();

					$to = $request->json()->get('email');

					$userEmail = $request->user()->email;
					$name = $request->json()->get('name');
					$userName = $request->user()->name.' '.$request->user()->last_name;

					$msg = 'Payment link send successful';
					$subject = 'Payment link send successful';
					
					
					\Mail::send('email_templates.sponsor_payments', compact('to', 'token', 'subject','name','userName'), function($message) use ($to, $subject) {
						$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
						$message->subject($subject);
						$message->to($to);
					});

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'Requested Sponsor Payment');
					\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

					if($request->user()->language == 'sp'){

						$subject = "El enlace de pago de su patrocinador fue enviado con ??xito.";
						$msg = '<p>Estimado '.$request->user()->name.' '.$request->user()->last_name.',</p><p><br></p><p>Gracias por enviar la informaci??n de su patrocinador.&nbsp;</p><p><br></p><p>Les hemos enviado con ??xito el enlace de pago, y quedamos a la espera del pago.</p><p><br></p><p>Por favor, espere una notificaci??n nuestra una vez que hayamos recibido el pago de su patrocinador.&nbsp;&nbsp;</p><p><br></p><p>Mientras tanto, si usted tiene alguna pregunta, o desea hablar con uno de los miembros de nuestro equipo, por favor, responda a este correo.&nbsp;</p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GProCongress II</p>';
					
					}elseif($request->user()->language == 'fr'){
					
						$subject = "Le lien de paiement de votre sponsor a ??t?? envoy?? avec succ??s.";
						$msg = "<p>Cher ".$request->user()->name." ".$request->user()->last_name.",&nbsp;</p><p>Merci d???avoir soumis les informations de votre sponsor.</p><p><br></p><p>Nous leur avons envoy?? avec succ??s le lien de paiement et attendons maintenant le montant.&nbsp;&nbsp;</p><p><br></p><p>Veuillez attendre ?? recevoir une notification de notre part une fois que le paiement de votre sponsor soit re??u.&nbsp;&nbsp;</p><p><br></p><p>En attendant, si vous avez des questions, ou si vous souhaitez parler ?? l'un des membres de notre ??quipe, veuillez r??pondre ?? cet e-mail.</p><p>Cordialement,&nbsp;</p><p>L?????quipe du GProCongr??s II</p>";
			
					}elseif($request->user()->language == 'pt'){
					
						$subject = "O link de pagamento do seu patrocinador foi enviado com sucesso.";
						$msg = '<p>Prezado '.$request->user()->name.' '.$request->user()->last_name.',</p><p><br></p><p>Agradecemos por submeter a informa????o do seu patrocinador</p><p>&nbsp;</p><p>N??s enviamos com sucesso o link de pagamento para eles, e estamos agora aguardando o pagamento.</p><p><br></p><p>Por favor aguarde nossa notifica????o assim que o pagamento do seu patrocinador for recebido.</p><p><br></p><p>Por enquanto, se voc?? tiver alguma pergunta, ou se quiser falar com um dos membros da nossa equipe, por favor responda a este e-mail.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do CongressoGPro</p>';
					
					}else{
					
						$subject = 'Your sponsor payment link was successfully sent.';
						$msg = '<p style="letter-spacing: 0.5px;">Dear '.$request->user()->name.' '.$request->user()->last_name.',</p><p style="letter-spacing: 0.5px;"><font color="#24695c"><br></font></p><p style="letter-spacing: 0.5px;">Thank you for submitting your sponsor??? information.&nbsp;</p><p style="letter-spacing: 0.5px;"><font color="#24695c"><br></font></p><p style="letter-spacing: 0.5px;">We have successfully sent the payment link to them, and are now awaiting payment.&nbsp;</p><p style="letter-spacing: 0.5px;"><font color="#24695c"><br></font></p><p style="letter-spacing: 0.5px;">Please expect a notification from us once your sponsors payment is received.&nbsp;&nbsp;</p><p style="letter-spacing: 0.5px;"><font color="#24695c"><br></font></p><p style="letter-spacing: 0.5px;">In the meantime, if you have any questions, or wish to speak with one of our team members, please reply to this email.</p><p style="letter-spacing: 0.5px;"><font color="#24695c"><br></font></p><p style="letter-spacing: 0.5px;">Warmly,</p><p style="letter-spacing: 0.5px;">GProCongress II Team</p>';
											
					}

					\Mail::send('email_templates.mail', compact('to', 'token', 'subject','msg'), function($message) use ($userEmail, $subject) {
						$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
						$message->subject($subject);
						$message->to($userEmail);
					});

					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Sponsor-Submitted-Payment');
					return response(array("error"=>false, "message"=>$message, "result"=>$users->toArray()), 200);


				}
				
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function fullPaymentOfflineSubmit(Request $request){
		
		$rules = [
			'mode' => 'required|string|in:WU,MG,Wire,RAI',
			'reference_number' => 'required',
            'amount' => 'required|numeric',
            'name' => 'required',
            'file' => 'required',
            'country_of_sender' => 'required',
            'type' => 'required|in:Offline,Online',
		];

		$messages = array(
			'mode.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'mode_required'),
			'reference_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'reference_number'),
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'amount_required'),
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'name_required'),
			'country_of_sender.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'country_of_sender'),
			'type.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'type'),
				
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

				$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
				if($referenceNumberCheck){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Transaction-already-exists');
					return response(array("error"=>true, "message"=>$message), 403);

				}else{

					$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id, false); 
					if($request->post('amount') <= $totalPendingAmount){

						$transactionId=strtotime("now").rand(11,99);

						$orderId=strtotime("now").rand(11,99);
		
						$transaction = new \App\Models\Transaction();
						$transaction->user_id = $request->user()->id;
						$transaction->bank = $request->post('mode');
						$transaction->order_id = $orderId;
						$transaction->transaction_id = $transactionId;
						$transaction->method = $request->post('type');
						$transaction->amount = $request->post('amount');
						$transaction->country_of_sender = $request->post('country_of_sender');
						$transaction->name = $request->post('name');
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

						if($request->post('file')){ 
							$transaction->file = $request->post('file');
						}

						$transaction->save();

						$Wallet = new \App\Models\Wallet();
						$Wallet->user_id = $request->user()->id;
						$Wallet->type  = 'Cr';
						$Wallet->amount = $request->post('amount');
						$Wallet->transaction_id = $transaction->id;
						$Wallet->status = 'Pending';
						$Wallet->save();
		
						$to = $request->user()->email;
						$name = $request->user()->name.' '.$request->user()->last_name;
						$amount = $request->post('amount');
						$subject = 'Transaction Complete';
						$msg = 'Your '.$request->post('amount').' transaction has been send successfully';
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User Offline payment added');
						\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

						$subject = '[GProCongress II Admin]  Payment Received';
						$msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Refund has been initiated for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Refund Amount : '.$amount.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
						\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

						// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Offline-payment-successful');
						return response(array("error"=>true, "message"=>$message), 200);
		
					}else{

						$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Payment-Successful');
						return response(array("error"=>true, "message"=>$message), 403);
		
					}
				}

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function PartialPaymentOfflineSubmit(Request $request){
	
		$rules = [
            'amount' => 'required',
			'mode' => 'required|string|in:WU,MG,Wire,RAI',
            'type' => 'required!in:Offline,Online',
            'reference_number' => 'required',
		];

		$messages = array(
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'amount_required'),
			'mode.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'mode_required'),
			'type.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'type'),
			'reference_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'reference_number'),
				
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

			try {

				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id, true);
				if($totalPendingAmount > $request->json()->get('amount')){

					$transactionId=strtotime("now").rand(11,99);

					$orderId=strtotime("now").rand(11,99);
	
					$transaction = new \App\Models\Transaction();
					$transaction->user_id = $request->user()->id;
					$transaction->order_id = $orderId;
					$transaction->transaction_id = $transactionId;
					$transaction->bank = $request->json()->get('mode');
					$transaction->method = $request->json()->get('type');
					$transaction->amount = $request->json()->get('amount');
					$transaction->bank_transaction_id = $request->json()->get('reference_number');
					$transaction->status = '0';
					$transaction->particular_id = '1';
					$transaction->save();
	
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Offline-payment-successful');
					return response(array("error"=>false, "message"=>$message), 200);
	
				}else{

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Payment-Successful');
					return response(array("error"=>true, "message"=>$message), 403);
	
				}
				
			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

    public function stageZero(Request $request) {
		
		$value = \App\Models\User::where('parent_id', $request->user()->id)->where('added_as', 'Group')->first();
		$result=[];
	
		if($value){

			$value = \App\Models\User::where('id', $request->user()->id)->first();
		
			$gender = 'Male';
			if($value['gender'] == '2'){
				$gender = 'Female';
			}
	
			$data = \App\Models\User::where('parent_id', $value->id)->first();
	
			$user = [];
	
			if($data && $data->added_as == 'Spouse'){
	
				if($data['gender'] == '2'){
					$gender = 'Female';
				}
	
				$user = array(
					'id'=> $data['id'],
					'added_as'=>'Spouse',
					'salutation'=>$data->salutation,
					'name'=>$data->name,
					'last_name'=>$data->last_name,
					'email'=>$data['email'],
					'phone_code'=>$data['phone_code'],
					'mobile'=>$data['mobile'],
					'status'=>$data['status'],
					'profile_status'=>$data['profile_status'],
					'remark'=>$data['remark'],
					'gender'=>$gender,
					'dob'=>$data['dob'],
					'citizenship'=>$data['citizenship'],
					'marital_status'=>$data['marital_status'],
					'contact_address'=>$data['contact_address'],
					'contact_zip_code'=>$data['contact_zip_code'],
					'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['contact_country_id']),
					'contact_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['contact_state_id']),
					'contact_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['contact_city_id']),
					'contact_business_codenumber'=>$data['contact_business_codenumber'],
					'contact_business_number'=>$data['contact_business_number'],
					'contact_whatsapp_codenumber'=>$data['contact_whatsapp_codenumber'],
					'contact_whatsapp_number'=>$data['contact_whatsapp_number'],
					'ministry_name'=>$data['ministry_name'],
					'ministry_address'=>$data['ministry_address'],
					'ministry_zip_code'=>$data['ministry_zip_code'],
					'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['ministry_country_id']),
					'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['ministry_state_id']),
					'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['ministry_city_id']),
					'ministry_pastor_trainer'=>$data['ministry_pastor_trainer'],
					'ministry_pastor_trainer_detail'=>$data['ministry_pastor_trainer_detail'],
					'doyouseek_postoral'=>$data['doyouseek_postoral'],
					'doyouseek_postoralcomment'=>$data['doyouseek_postoralcomment'],
				);
			}
			
			
			$result=[
				'id'=>$value['id'],
				'parent_id'=>$value['parent_id'],
				'added_as'=>$value['added_as'],
				'salutation'=>$value['salutation'],
				'first_name'=>$value['name'],
				'last_name'=>$value['last_name'],
				'email'=>$value['email'],
				'phone_code'=>$value['phone_code'],
				'mobile'=>$value['mobile'],
				'status'=>$value['status'],
				'profile_status'=>$value['profile_status'],
				'remark'=>$value['remark'],
				'gender'=>$gender,
				'dob'=>$value['dob'],
				'citizenship'=>$value['citizenship'],
				'marital_status'=>$value['marital_status'],
				'contact_address'=>$value['contact_address'],
				'contact_zip_code'=>$value['contact_zip_code'],
				'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($value['contact_country_id']),
				'contact_state_id'=>\App\Helpers\commonHelper::getStateNameById($value['contact_state_id']),
				'contact_city_id'=>\App\Helpers\commonHelper::getCityNameById($value['contact_city_id']),
				'contact_business_codenumber'=>$value['contact_business_codenumber'],
				'contact_business_number'=>$value['contact_business_number'],
				'contact_whatsapp_codenumber'=>$value['contact_whatsapp_codenumber'],
				'contact_whatsapp_number'=>$value['contact_whatsapp_number'],
				'ministry_name'=>$value['ministry_name'],
				'ministry_address'=>$value['ministry_address'],
				'ministry_zip_code'=>$value['ministry_zip_code'],
				'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($value['ministry_country_id']),
				'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($value['ministry_state_id']),
				'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($value['ministry_city_id']),
				'ministry_pastor_trainer'=>$value['ministry_pastor_trainer'],
				'ministry_pastor_trainer_detail'=>$value['ministry_pastor_trainer_detail'],
				'doyouseek_postoral'=>$value['doyouseek_postoral'],
				'doyouseek_postoralcomment'=>$value['doyouseek_postoralcomment'],
				'Spouse'=>$user,
				
			];
			
			
			return response(array("error"=>false, "message"=>'Stage zero data fetch done', "result"=>$result), 200);


		
		}else{

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Nothing-Found');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}
		
	}

    public function stageOne(Request $request) {
		
		$value = \App\Models\User::where('id', $request->user()->id)->where('parent_id', NULL)->where('added_as', NULL)->where('stage', '1')->where('profile_status', 'Review')->first();

		$result=[];

		if($value){

			$gender = 'Male';
			if($value['gender'] == '2'){
				$gender = 'Female';
			}

			$data = \App\Models\User::where('parent_id', $value->id)->first();

			$user = [];

			if($data && $data->added_as == 'Spouse'){

				if($data['gender'] == '2'){
					$gender = 'Female';
				}

				$user = array(
					'id'=> $data['id'],
					'added_as'=>'Spouse',
					'salutation'=>$data->salutation,
					'name'=>$data->name,
					'last_name'=>$data->last_name,
					'email'=>$data['email'],
					'phone_code'=>$data['phone_code'],
					'mobile'=>$data['mobile'],
					'status'=>$data['status'],
					'profile_status'=>$data['profile_status'],
					'remark'=>$data['remark'],
					'gender'=>$gender,
					'dob'=>$data['dob'],
					'citizenship'=>$data['citizenship'],
					'marital_status'=>$data['marital_status'],
					'contact_address'=>$data['contact_address'],
					'contact_zip_code'=>$data['contact_zip_code'],
					'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['contact_country_id']),
					'contact_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['contact_state_id']),
					'contact_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['contact_city_id']),
					'contact_business_codenumber'=>$data['contact_business_codenumber'],
					'contact_business_number'=>$data['contact_business_number'],
					'contact_whatsapp_codenumber'=>$data['contact_whatsapp_codenumber'],
					'contact_whatsapp_number'=>$data['contact_whatsapp_number'],
					'ministry_name'=>$data['ministry_name'],
					'ministry_address'=>$data['ministry_address'],
					'ministry_zip_code'=>$data['ministry_zip_code'],
					'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['ministry_country_id']),
					'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['ministry_state_id']),
					'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['ministry_city_id']),
					'ministry_pastor_trainer'=>$data['ministry_pastor_trainer'],
					'ministry_pastor_trainer_detail'=>$data['ministry_pastor_trainer_detail'],
					'doyouseek_postoral'=>$data['doyouseek_postoral'],
					'doyouseek_postoralcomment'=>$data['doyouseek_postoralcomment'],
				);
			}
			
			
			$result=[
				
				'profile_status'=>$value['profile_status'],
				'stage'=>$value['stage'],
				'id'=>$value['id'],
				'parent_id'=>$value['parent_id'],
				'added_as'=>$value['added_as'],
				'salutation'=>$value['salutation'],
				'first_name'=>$value['name'],
				'last_name'=>$value['last_name'],
				'email'=>$value['email'],
				'phone_code'=>$value['phone_code'],
				'mobile'=>$value['mobile'],
				'status'=>$value['status'],
				'remark'=>$value['remark'],
				'gender'=>$gender,
				'dob'=>$value['dob'],
				'citizenship'=>$value['citizenship'],
				'marital_status'=>$value['marital_status'],
				'contact_address'=>$value['contact_address'],
				'contact_zip_code'=>$value['contact_zip_code'],
				'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($value['contact_country_id']),
				'contact_state_id'=>\App\Helpers\commonHelper::getStateNameById($value['contact_state_id']),
				'contact_city_id'=>\App\Helpers\commonHelper::getCityNameById($value['contact_city_id']),
				'contact_business_codenumber'=>$value['contact_business_codenumber'],
				'contact_business_number'=>$value['contact_business_number'],
				'contact_whatsapp_codenumber'=>$value['contact_whatsapp_codenumber'],
				'contact_whatsapp_number'=>$value['contact_whatsapp_number'],
				'ministry_name'=>$value['ministry_name'],
				'ministry_address'=>$value['ministry_address'],
				'ministry_zip_code'=>$value['ministry_zip_code'],
				'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($value['ministry_country_id']),
				'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($value['ministry_state_id']),
				'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($value['ministry_city_id']),
				'ministry_pastor_trainer'=>$value['ministry_pastor_trainer'],
				'ministry_pastor_trainer_detail'=>$value['ministry_pastor_trainer_detail'],
				'doyouseek_postoral'=>$value['doyouseek_postoral'],
				'doyouseek_postoralcomment'=>$value['doyouseek_postoralcomment'],
				'Spouse'=>$user,
				
			];
			
			
			return response(array("error"=>false, "message"=>'Stage One data fetch done', "result"=>$result), 200);

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Nothing-Found');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}
	}

	public function stageTwo(Request $request) {
		
		$value = \App\Models\User::where('id', $request->user()->id)->where('parent_id', NULL)->where('added_as', NULL)->where('stage', '2')->where('profile_status', 'Approved')->first();

		$result=[];

		if($value){

			$gender = 'Male';
			if($value['gender'] == '2'){
				$gender = 'Female';
			}

			$result=[
				'id'=>$value['id'],
				'change_room_type'=>$value['change_room_type'],
				'upgrade_category'=>$value['upgrade_category'],
				'early_bird'=>$value['early_bird'],
				'amount'=>$value['amount'],
			];
			
			
			return response(array("error"=>false, "message"=>'Stage Two data fetch done', "result"=>$result), 200);

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Nothing-Found');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}

	}

	public function travelInfoDetails(Request $request) {
		
		$result = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();

		if (!$result) {
			$result = [];

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'TravelInfo-doesnot-exist');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 403);

		}else{

			$flight_details = json_decode($result->flight_details);
        	$return_flight_details = json_decode($result->return_flight_details);

			$result=[
				'id'=>$result['id'],
				'arrival_flight_number'=>$flight_details->arrival_flight_number,
				'arrival_start_location'=>$flight_details->arrival_start_location,
				'arrival_date_departure'=>$flight_details->arrival_date_departure,
				'arrival_date_arrival'=>$flight_details->arrival_date_arrival,
				'departure_flight_number'=>$flight_details->departure_flight_number,
				'departure_start_location'=>$flight_details->departure_start_location,
				'departure_date_departure'=>$flight_details->departure_date_departure,
				'departure_date_arrival'=>$flight_details->departure_date_arrival,					
				'spouse_arrival_flight_number'=>$return_flight_details->spouse_arrival_flight_number ?? '',
				'spouse_arrival_start_location'=>$return_flight_details->spouse_arrival_start_location ?? '',
				'spouse_arrival_date_departure'=>$return_flight_details->spouse_arrival_date_departure ?? '',
				'spouse_arrival_date_arrival'=>$return_flight_details->spouse_arrival_date_arrival ?? '',
				'spouse_departure_flight_number'=>$return_flight_details->spouse_departure_flight_number ?? '',
				'spouse_departure_start_location'=>$return_flight_details->spouse_departure_start_location ?? '',
				'spouse_departure_date_departure'=>$return_flight_details->spouse_departure_date_departure ?? '',
				'spouse_departure_date_arrival'=>$return_flight_details->spouse_departure_date_arrival ?? '',

				'mobile'=>$result->mobile,
				'name'=>$result->name,
				'logistics_picked'=>$result->logistics_picked,
				'logistics_dropped'=>$result->logistics_dropped,
				'remark'=>$result->remark,
			];

			
			
			return response(array("error"=>false, "message"=>'Stage three data fetch done', "result"=>$result), 200);
		}
		

	}

	public function sessionInfoDetails(Request $request) {
		
		$result = \App\Models\SessionInfo::where('user_id', $request->user()->id)->get();
		
		if (!$result) {

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Sessioninfo-doesnot-exists');
			return response(array("error"=>true, "message" => $message), 403);


		}else{

			$results=[];

			foreach ($result as $dayValue) {
						
				$sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
				if($sessionInfo){

					$results[]=[
						'date'=>$dayValue->day,
						'name'=>$sessionInfo->name,
						'session'=>$dayValue->session,
						'start_time'=>$sessionInfo->start_time,
						'end_time'=>$sessionInfo->end_time,
					];

				}
				
			}

			return response(array("error"=>false, "message"=>'Stage four data fetch done', "result"=>$results), 200);
		}
		

	}

    public function donatePaymentsSubmit(Request $request){
 
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'reference_number' => 'required',
		];

		$messages = array(
			'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'name_required'),
			'email.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'email_required'),
			'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'amount_required'),
			'reference_number.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'reference_number'),
				
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

			try {

				$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
				if($referenceNumberCheck){

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Transaction-already-exists');
					return response(array("error"=>true, "message"=>$message), 403);

				}else{

					$Donation = new \App\Models\Donation();
					$Donation->user_id = $request->user()->id;
					$Donation->name = $request->json()->get('name');
					$Donation->email = $request->json()->get('email');
					$Donation->amount = $request->json()->get('amount');
					$Donation->save();

					$transactionId=strtotime("now").rand(11,99);

					$orderId=strtotime("now").rand(11,99);

					$transaction = new \App\Models\Transaction();
					$transaction->user_id = $request->user()->id;
					$transaction->order_id = $orderId;
					$transaction->transaction_id = $transactionId;
					$transaction->method = 'Online';
					$transaction->amount = $request->get('amount');
					$transaction->bank_transaction_id = $request->get('reference_number');
					$transaction->status = '2';
					$transaction->save();

					$subject = 'Donation payments';
					$msg = 'User donate payments';

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'User donate payments');
					
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'payment-added-successful');
					return response(array("error"=>false, "message"=>$message), 200);
				}

			} catch (\Exception $e) {

				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

        
    }

	public function QrCode(Request $request) {
		
		$value = \App\Models\User::where('id', $request->user()->id)->where('stage', '5')->where('profile_status', 'Approved')->first();

		$result=[];

		if($value){

			$result=[
				'id'=>$value['id'],
				'QrCode'=>$value['qrcode'],
			];
			
			
			return response(array("error"=>false, "message"=>'Stage Five data fetch done', "result"=>$result), 200);

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Data-not-available');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}

	}
	
	public function getContactDetails(Request $request) {
		
		$value = \App\Models\User::where('id', $request->user()->id)->where('profile_update', '1')->first();

		$result=[];

		if($value){

			$result=[
				'id'=>$value['id'],
				'contact_address'=>$value['contact_address'],
				'contact_zip_code'=>$value['contact_zip_code'],
				'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($value['contact_country_id']),
				'contact_state_id'=>\App\Helpers\commonHelper::getStateNameById($value['contact_state_id']),
				'contact_city_id'=>\App\Helpers\commonHelper::getCityNameById($value['contact_city_id']),
				'phone_code'=>$value['phone_code'],
				'contact_business_codenumber'=>$value['contact_business_codenumber'],
				'contact_business_number'=>$value['contact_business_number'],
				'contact_whatsapp_codenumber'=>$value['contact_whatsapp_codenumber'],
				'contact_whatsapp_number'=>$value['contact_whatsapp_number'],
				'terms_and_condition'=>$value['terms_and_condition'],
			];
			
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Done');
			return response(array("error"=>false, "message"=>'Contact Details', "result"=>$result), 200);

		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Data-not-available');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}

	}

	public function getMinistryDetails(Request $request) {
		
		$data = \App\Models\User::where('id', $request->user()->id)->where('contact_address','!=','')->first();

		$result=[];

		if($data){

			$result=[
				
				'ministry_name'=>$data['ministry_name'],
				'ministry_address'=>$data['ministry_address'],
				'ministry_zip_code'=>$data['ministry_zip_code'],
				'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['ministry_country_id']),
				'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['ministry_state_id']),
				'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['ministry_city_id']),
				'ministry_pastor_trainer'=>$data['ministry_pastor_trainer'],
				'ministry_pastor_trainer_detail'=>$data['ministry_pastor_trainer_detail'],
				'doyouseek_postoral'=>$data['doyouseek_postoral'],
				'doyouseek_postoralcomment'=>$data['doyouseek_postoralcomment'],
			];
			
			return response(array("error"=>false, "message"=>'Ministry Details', "result"=>$result), 200);


		}else{
			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Data-not-available');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);

		}

	}

	
    public function onlinePaymentByMobile(Request $request){
 
		
			$rules = [
				'amount' => 'required|numeric|gt:0',
				'type' => 'required|in:stripe,mobile_paypal',
			];

			$messages = array(
				'amount.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'amount_required'),
					
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

			try {

				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id, false); 
				if($request->json()->get('amount') <= $totalPendingAmount){
					
					$data = \App\Helpers\commonHelper::paymentGateway($request->user()->id,$request->json()->get('amount'),1,$request->json()->get('type'));
					
					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'payment-added-successful');

					if($request->json()->get('type') == 'stripe'){

						$client_secret = $data['intent'];
						$order_id = $data['order_id'];
						$payment_intent = $data['payment_intent'];

						return response(array("error"=>false, "message"=>$message,'client_secret'=>$client_secret,'order_id'=>$order_id,'payment_intent'=>$payment_intent), 200);


					}elseif($request->json()->get('type') == 'mobile_paypal'){

						$PAYPAL_CLIENT_ID = env('PAYPAL_CLIENT_ID');
						$PAYPAL_CLIENT_SECRET = env('PAYPAL_CLIENT_SECRET');
						$response = $data['response'];

						return response(array("error"=>false, "message"=>$message,'PAYPAL_CLIENT_ID'=>$PAYPAL_CLIENT_ID,'PAYPAL_CLIENT_SECRET'=>$PAYPAL_CLIENT_SECRET,'response'=>$response), 200);

					}

				
				}else{

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'No-payment-due');
					return response(array("error"=>true, "message"=>$message), 403);
	
				}

			} catch (\Exception $e) {

				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

        
	}

	public function PaymentDetails(Request $request) {
		
		$value = \App\Models\User::where('id', $request->user()->id)->where('parent_id', NULL)->where('added_as', NULL)->where('stage', '2')->where('profile_status', 'Approved')->first();

		$result=[];

		if($value){

			$transactions = \App\Models\Transaction::where('user_id',$request->user()->id)->orderBy('updated_at','desc')->get();

			$result=[
				'AcceptedAmount'=>\App\Helpers\commonHelper::getTotalAcceptedAmount($request->user()->id, true),
				'Process'=>\App\Helpers\commonHelper::getTotalAmountInProcess($request->user()->id, true),
				'RejectedAmount'=>\App\Helpers\commonHelper::getTotalRejectedAmount($request->user()->id, true),
				'PendingAmount'=>\App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id, true),
				'transactions'=>$transactions,
			];
			
			
			return response(array("error"=>false, "message"=>'Stage Two data fetch done', "result"=>$result), 200);

		}else{

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Data-not-available');
			return response(array("error"=>true, "message"=>$message, "result"=>$result), 200);


		}

	}

	public function getUserAllStageProfileData(Request $request) {
		
		$groupInfo=[]; $QRInfo=[]; $SessionInfo=[]; $TravelInfo=[]; $RegisterData=[]; $paymentInfo=[];

		// group 
		$groupInfoResult=\App\Models\User::where('parent_id',$request->user()->id)->where('added_as','Group')->get();
 
		$SpouseInfo = [];
		if(!empty($groupInfoResult) && count($groupInfoResult)>0){

			foreach($groupInfoResult as $key=>$data){

				$val=\App\Models\User::where('parent_id',$data->id)->where('added_as','Spouse')->first();

				if($val){

					if($val->stage == 1){
						$processStage1 = 'In Process';
					}elseif($val->stage > 1){
						$processStage1 = 'Completed';
					}else{
						$processStage1 = 'Pending';
					}
	
					if($val->stage == 2){
						$processStage2 = 'In Process';
					}elseif($val->stage > 2){
						$processStage2 = 'Completed';
					}else{
						$processStage2 = 'Pending';
					}
	
					
					if($val->stage == 3){
						$processStage3 = 'In Process';
					}elseif($val->stage > 3){
						$processStage3 = 'Completed';
					}else{
						$processStage3 = 'Pending';
					}
	
					if($val->stage == 4){
						$processStage4 = 'In Process';
					}elseif($val->stage > 4){
						$processStage4 = 'Completed';
					}else{
						$processStage4 = 'Pending';
					}
	
					if($val->stage == 5){
						$processStage5 = 'In Process';
					}elseif($val->stage > 5){
						$processStage5 = 'Completed';
					}else{
						$processStage5 = 'Pending';
					}

					$SpouseInfo=[

						'name'=>$val->name.' '.$val->last_name,
						'processStage1'=>$processStage1,
						'processStage2'=>$processStage2,
						'processStage3'=>$processStage3,
						'processStage4'=>$processStage4,
						'processStage5'=>$processStage5,
					]; 

				}
					

				if($data->stage == 1){
					$processStage1 = 'In Process';
				}elseif($data->stage > 1){
					$processStage1 = 'Completed';
				}else{
					$processStage1 = 'Pending';
				}

				if($data->stage == 2){
					$processStage2 = 'In Process';
				}elseif($data->stage > 2){
					$processStage2 = 'Completed';
				}else{
					$processStage2 = 'Pending';
				}

				
				if($data->stage == 3){
					$processStage3 = 'In Process';
				}elseif($data->stage > 3){
					$processStage3 = 'Completed';
				}else{
					$processStage3 = 'Pending';
				}

				if($data->stage == 4){
					$processStage4 = 'In Process';
				}elseif($data->stage > 4){
					$processStage4 = 'Completed';
				}else{
					$processStage4 = 'Pending';
				}

				if($data->stage == 5){
					$processStage5 = 'In Process';
				}elseif($data->stage > 5){
					$processStage5 = 'Completed';
				}else{
					$processStage5 = 'Pending';
				}

				$spouse = false;
				if($val){
					$spouse = true;
				}
				
				$groupInfo[]=[

					'S.N.'=>($key+1),
					'name'=>$data->name.' '.$data->last_name,
					'processStage1'=>$processStage1,
					'processStage2'=>$processStage2,
					'processStage3'=>$processStage3,
					'processStage4'=>$processStage4,
					'processStage5'=>$processStage5,
					'spouse'=>$spouse,
					'spouseInfo'=>$SpouseInfo,
				]; 

			}
		}
		

		//register Data
		$registerUser = \App\Models\User::where('id', $request->user()->id)->where('stage','>', '0')->first();

		if($registerUser){

			$gender = 'Male';
			if($registerUser['gender'] == '2'){
				$gender = 'Female';
			}

			if($registerUser['gender'] == '2'){
				$gender = 'Female';
			}

			if($registerUser['gender'] == '2'){
				$gender = 'Female';
			}

			$data = \App\Models\User::where('parent_id', $registerUser->id)->where('added_as', 'Spouse')->first();
			 
			$coming_along_with = 'No';
			$spouse_payment_status  = '';
			$spouse_travel_status = '';
			$Spouse_confirmation_received = 'Pending';
			$stay_room = '';
			if($data){
				$coming_along_with = 'Yes';
				$spouse_payment_status  = 'No';
				$spouse_travel_status  = 'No';
			}
				
			if(!$data && $registerUser['room'] !=null){
				$stay_room = $registerUser['room'];
			}
				
			if($data && $data->spouse_confirm_status=='Approve'){
				$Spouse_confirmation_received = $data->spouse_confirm_status;
			}
			$history = \App\Models\SpouseStatusHistory::where([['spouse_id', $request->user()->spouse_id], ['parent_id', $request->user()->id]])->first();

			if($history && $history->status=='Reject'){
				$Spouse_confirmation_received = $history->status;
			}

			$user = Null;

			$SpouseParent = \App\Models\User::where('id',$request->user()->parent_id)->first();
			
			if($SpouseParent){

				$coming_along_with = 'Yes';
				if($SpouseParent['gender'] == '2'){
					$gender = 'Female';
				}

				$user = array(
					'id'=> $SpouseParent['id'],
					'added_as'=>'Spouse',
					'salutation'=>$SpouseParent->salutation,
					'name'=>$SpouseParent->name,
					'last_name'=>$SpouseParent->last_name,
					'email'=>$SpouseParent['email'],
					'phone_code'=>$SpouseParent['phone_code'],
					'mobile'=>$SpouseParent['mobile'],
					'status'=>$SpouseParent['status'],
					'profile_status'=>$SpouseParent['profile_status'],
					'remark'=>$SpouseParent['remark'],
					'gender'=>$gender,
					'dob'=>$SpouseParent['dob'],
					'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($SpouseParent['citizenship']),
					'marital_status'=>$SpouseParent['marital_status'],
					'contact_address'=>$SpouseParent['contact_address'],
					'contact_zip_code'=>$SpouseParent['contact_zip_code'],
					'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($SpouseParent['contact_country_id']),
					'contact_state_id'=> $SpouseParent['contact_state_id'] != 0 ?  \App\Helpers\commonHelper::getStateNameById($SpouseParent['contact_state_id']) : $SpouseParent['contact_state_name'],
					'contact_city_id'=> $SpouseParent['contact_city_id'] != 0 ? \App\Helpers\commonHelper::getCityNameById($SpouseParent['contact_city_id']) : $SpouseParent['contact_city_name'],
					'contact_business_codenumber'=>$SpouseParent['contact_business_codenumber'],
					'contact_business_number'=>$SpouseParent['contact_business_number'],
					'contact_whatsapp_codenumber'=>$SpouseParent['contact_whatsapp_codenumber'],
					'contact_whatsapp_number'=>$SpouseParent['contact_whatsapp_number'],
					'ministry_name'=>$SpouseParent['ministry_name'],
					'ministry_address'=>$SpouseParent['ministry_address'],
					'ministry_zip_code'=>$SpouseParent['ministry_zip_code'],
					'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($SpouseParent['ministry_country_id']),
					'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($SpouseParent['ministry_state_id']),
					'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($SpouseParent['ministry_city_id']),
					'ministry_pastor_trainer'=>$SpouseParent['ministry_pastor_trainer'],
					'ministry_pastor_trainer_detail'=>$SpouseParent['ministry_pastor_trainer_detail'],
					'doyouseek_postoral'=>$SpouseParent['doyouseek_postoral'],
					'doyouseek_postoralcomment'=>$SpouseParent['doyouseek_postoralcomment'],
					'willing_to_commit'=>$SpouseParent['willing_to_commit'] ?? '',
					'envision_training'=>$SpouseParent['envision_training'],
				);
				

			}else{

				if($data){

					if($data['gender'] == '2'){
						$gender = 'Female';
					}
	
					$user = array(
						'id'=> $data['id'],
						'added_as'=>'Spouse',
						'salutation'=>$data->salutation,
						'name'=>$data->name,
						'last_name'=>$data->last_name,
						'email'=>$data['email'],
						'phone_code'=>$data['phone_code'],
						'mobile'=>$data['mobile'],
						'status'=>$data['status'],
						'profile_status'=>$data['profile_status'],
						'remark'=>$data['remark'],
						'gender'=>$gender,
						'dob'=>$data['dob'],
						'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($data['citizenship']),
						'marital_status'=>$data['marital_status'],
						'contact_address'=>$data['contact_address'],
						'contact_zip_code'=>$data['contact_zip_code'],
						'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['contact_country_id']),
						'contact_state_id'=> $data['contact_state_id'] != 0 ?  \App\Helpers\commonHelper::getStateNameById($data['contact_state_id']) : $data['contact_state_name'],
						'contact_city_id'=> $data['contact_city_id'] != 0 ? \App\Helpers\commonHelper::getCityNameById($data['contact_city_id']) : $data['contact_city_name'],
						'contact_business_codenumber'=>$data['contact_business_codenumber'],
						'contact_business_number'=>$data['contact_business_number'],
						'contact_whatsapp_codenumber'=>$data['contact_whatsapp_codenumber'],
						'contact_whatsapp_number'=>$data['contact_whatsapp_number'],
						'ministry_name'=>$data['ministry_name'],
						'ministry_address'=>$data['ministry_address'],
						'ministry_zip_code'=>$data['ministry_zip_code'],
						'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($data['ministry_country_id']),
						'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($data['ministry_state_id']),
						'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($data['ministry_city_id']),
						'ministry_pastor_trainer'=>$data['ministry_pastor_trainer'],
						'ministry_pastor_trainer_detail'=>$data['ministry_pastor_trainer_detail'],
						'doyouseek_postoral'=>$data['doyouseek_postoral'],
						'doyouseek_postoralcomment'=>$data['doyouseek_postoralcomment'],
						'willing_to_commit'=>$data['willing_to_commit'] ?? '',
						'envision_training'=>$data['envision_training'],
						'spouse_payment_status'=>$spouse_payment_status,
						'spouse_travel_status'=>$spouse_travel_status,
					);
				}

			}


		
			
			
			$RegisterData=[
				
				'profile_status'=>$registerUser['profile_status'],
				'stage'=>$registerUser['stage'],
				'id'=>$registerUser['id'],
				'parent_id'=>$registerUser['parent_id'],
				'added_as'=>$registerUser['added_as'],
				'salutation'=>$registerUser['salutation'],
				'first_name'=>$registerUser['name'],
				'last_name'=>$registerUser['last_name'],
				'email'=>$registerUser['email'],
				'phone_code'=>$registerUser['phone_code'],
				'mobile'=>$registerUser['mobile'],
				'status'=>$registerUser['status'],
				'remark'=>$registerUser['remark'],
				'gender'=>$gender,
				'dob'=>$registerUser['dob'],
				'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($registerUser['citizenship']),
				'marital_status'=>$registerUser['marital_status'],
				'contact_address'=>$registerUser['contact_address'],
				'contact_zip_code'=>$registerUser['contact_zip_code'],
				'contact_country_id'=>\App\Helpers\commonHelper::getCountryNameById($registerUser['contact_country_id']),
				'contact_state_id'=>$registerUser['contact_state_id'] != 0 ? \App\Helpers\commonHelper::getStateNameById($registerUser['contact_state_id']) : $registerUser['contact_state_name'],
				'contact_city_id'=> $registerUser['contact_city_id'] != 0 ? \App\Helpers\commonHelper::getCityNameById($registerUser['contact_city_id'])  : $registerUser['contact_city_name'],
				'contact_business_codenumber'=>$registerUser['contact_business_codenumber'],
				'contact_business_number'=>$registerUser['contact_business_number'],
				'contact_whatsapp_codenumber'=>$registerUser['contact_whatsapp_codenumber'],
				'contact_whatsapp_number'=>$registerUser['contact_whatsapp_number'],
				'ministry_name'=>$registerUser['ministry_name'],
				'ministry_address'=>$registerUser['ministry_address'],
				'ministry_zip_code'=>$registerUser['ministry_zip_code'],
				'ministry_country_id'=>\App\Helpers\commonHelper::getCountryNameById($registerUser['ministry_country_id']),
				'ministry_state_id'=>\App\Helpers\commonHelper::getStateNameById($registerUser['ministry_state_id']),
				'ministry_city_id'=>\App\Helpers\commonHelper::getCityNameById($registerUser['ministry_city_id']),
				'ministry_pastor_trainer'=>$registerUser['ministry_pastor_trainer'],
				'ministry_pastor_trainer_detail'=>$registerUser['ministry_pastor_trainer_detail'],
				'doyouseek_postoral'=>$registerUser['doyouseek_postoral'],
				'doyouseek_postoralcomment'=>$registerUser['doyouseek_postoralcomment'],
				'envision_training'=>$registerUser['envision_training'] ?? '',
				'willing_to_commit'=>$registerUser['willing_to_commit'],
				'coming_along_with'=>$coming_along_with,
				'stay_room'=>$stay_room,
				'Spouse_confirmation_received'=>$Spouse_confirmation_received,
				'Spouse'=>$user,
				
				
			];
			

		}

		//payment
		$valuePaymentHistory = \App\Models\User::where('id', $request->user()->id)->where('stage','>','1')->where('profile_status', 'Approved')->first();

		if($valuePaymentHistory){

			$transactions = \App\Models\Transaction::where('user_id',$request->user()->id)->orderBy('updated_at','desc')->get();

			$paymentInfo=[
				'AcceptedAmount'=>\App\Helpers\commonHelper::getTotalAcceptedAmount($request->user()->id, true),
				'Process'=>\App\Helpers\commonHelper::getTotalAmountInProcess($request->user()->id, true),
				'RejectedAmount'=>\App\Helpers\commonHelper::getTotalRejectedAmount($request->user()->id, true),
				'PendingAmount'=>\App\Helpers\commonHelper::getTotalPendingAmount($request->user()->id, true),
				'transactions'=>$transactions,
			]; 
			
		}

		//TravelInfo
		$getTravelInfo = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();

		if ($getTravelInfo) {
			
			$flight_details = json_decode($getTravelInfo->flight_details);
        	$return_flight_details = json_decode($getTravelInfo->return_flight_details);
			$user_status = 'Pending';
			$admin_status = 'Pending';

			if($getTravelInfo->user_status == '1'){
				$user_status = 'Approve';
			}
			if($getTravelInfo->admin_status == '1'){
				$admin_status = 'Approve';
			}
			$TravelInfo=[
				'id'=>$getTravelInfo['id'],
				'arrival_flight_number'=>$flight_details->arrival_flight_number,
				'arrival_start_location'=>$flight_details->arrival_start_location,
				'arrival_date_departure'=>$flight_details->arrival_date_departure,
				'arrival_date_arrival'=>$flight_details->arrival_date_arrival,
				'departure_flight_number'=>$flight_details->departure_flight_number,
				'departure_start_location'=>$flight_details->departure_start_location,
				'departure_date_departure'=>$flight_details->departure_date_departure,
				'departure_date_arrival'=>$flight_details->departure_date_arrival,					
				'spouse_arrival_flight_number'=>$return_flight_details->spouse_arrival_flight_number ?? '',
				'spouse_arrival_start_location'=>$return_flight_details->spouse_arrival_start_location ?? '',
				'spouse_arrival_date_departure'=>$return_flight_details->spouse_arrival_date_departure ?? '',
				'spouse_arrival_date_arrival'=>$return_flight_details->spouse_arrival_date_arrival ?? '',
				'spouse_departure_flight_number'=>$return_flight_details->spouse_departure_flight_number ?? '',
				'spouse_departure_start_location'=>$return_flight_details->spouse_departure_start_location ?? '',
				'spouse_departure_date_departure'=>$return_flight_details->spouse_departure_date_departure ?? '',
				'spouse_departure_date_arrival'=>$return_flight_details->spouse_departure_date_arrival ?? '',

				'mobile'=>$getTravelInfo->mobile,
				'name'=>$getTravelInfo->name,
				'logistics_picked'=>$getTravelInfo->logistics_picked,
				'logistics_dropped'=>$getTravelInfo->logistics_dropped,
				'remark'=>$getTravelInfo->remark,
				'user_status'=>$user_status,
				'admin_status'=>$admin_status,
			];

		}


		//session info
		$GetSessionInfo = \App\Models\SessionInfo::where('user_id', $request->user()->id)->get();
		
		if ($GetSessionInfo) {

			foreach ($GetSessionInfo as $dayValue) {
						
				$DaySession = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
				if($DaySession){

					$SessionInfo[]=[
						'id'=>$DaySession->id,
						'date'=>$dayValue->day,
						'name'=>$DaySession->name,
						'session'=>$dayValue->session,
						'start_time'=>$DaySession->start_time,
						'end_time'=>$DaySession->end_time,
					];

				}
				
			}

			
		}


		//QRcode 
		$stage5 = \App\Models\User::where('id', $request->user()->id)->where('stage','>', '4')->where('profile_status', 'Approved')->first();

		if($stage5){

			$QRInfo=[
				'id'=>$stage5['id'],
				'QrCode'=>$stage5['qrcode'],
			];
		}
		
		return response(array("error"=>false, "message"=>'Data fetch done', "group_info"=>$groupInfo, "registerData"=>$RegisterData, "paymentInfo"=>$paymentInfo, "travelInfo"=>$TravelInfo, "SessionInfo"=>$SessionInfo, "QRInfo"=>$QRInfo), 200);


	}

	public function travelInfoLetter(Request $request) {
		
		$resultData = \App\Models\TravelInfo::where('user_id', $request->user()->id)->first();
		$file = null;
		if (!$resultData) {
			$result = [];

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Visa-letter-info-doesnot-exist');
			return response(array("error"=>true, "message" => $message,"result"=>$result), 403);

		}else{

			if($resultData->admin_status == '1'){

				if($resultData->final_file != ''){

					$file = asset('uploads/file/'.$resultData->final_file);
				}else{

				}
				
			}elseif($resultData->draft_file != ''){

				$file = asset('uploads/file/'.$resultData->draft_file);
			}

			$result=[
				'id'=>$resultData['id'],
				'remark'=>$file,
			];

			$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Visaletter-file-fetche-succesully');
			return response(array("error"=>false, "message"=>$message, "result"=>$result), 200);
		}
		

	}

	public function NotificationList(Request $request){
 
		try{

			$Result=\App\Models\Notification::where('status','1')->where('user_id',$request->user()->id)->get();
			
			if(!$Result){
				
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Data-not-available');
				return response(array("message" => $message,'error'=>true),404); 
			}else{
				
				$result=[];
				
				foreach($Result as $val){
					
					$result[]=[
						
						'title'=>$val->title,
						'message'=>$val->message,
					];
				}

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Notification-fetched-successfully');
				return response(array("message" =>$message,'result'=>$result,'error'=>false),200); 
				
			}
			
		}catch (\Exception $e){
			
			return response(array("message" => $e->getMessage(),'error'=>true),403); 
		
		} 	 
		
	}

	
	public function changeUserLanguage(Request $request){
	
		
		$rules = [
            'language' => 'required|in:en,sp,fr,pt',
		];

		$messages = array(
			'language.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'language_required'),
				
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

			try {
				
				$users = \App\Models\User::where('id',$request->user()->id)->first();
				
				$users->language = $request->json()->get('language');
				$users->save();

				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Done');
				return response(array("error"=>true, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function passportInfo(Request $request){

			
            $rules = [
                'name' => 'required',
                'passport_no' => 'required|unique:passport_infos,passport_no,'.$request->user()->id,
                'dob' => 'required|date',
                'citizenship' => 'required',
                'country_id' => 'required',
            ];

            $messages = array(
                'name.required' => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'name_required'), 
                
            );

            $validator = \Validator::make($request->json()->all(), $rules, $messages);
            
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
                   
                    $passportInfo = new \App\Models\PassportInfo;
                    $passportInfo->name = $request->json()->get('name');
                    $passportInfo->passport_no = $request->json()->get('passport_no');
                    $passportInfo->dob =  $request->json()->get('dob');
                    $passportInfo->citizenship = $request->json()->get('citizenship');
                    $passportInfo->country_id = $request->json()->get('country_id');
                    $passportInfo->user_id = $request->user()->id;
                    $passportInfo->save();

                    $url = '<a href="'.url('sponsorship-letter-approve').'">Click here</a>';
					$to = $request->user()->email;
					$subject = 'Please verify your sponsorship letter Information.';
					$msg = '<p>Thank you for submitting your sponsorship letter information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($request->user()->id,$subject,$msg,'sponsorship information completed');
					
					$name = $request->user()->name.' '.$request->user()->last_name;

					if($request->user()->language == 'sp'){

						$subject = "Por favor, verifique su informaci??n de viaje";
						$msg = '<p>Estimado '.$name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su informaci??n de viaje.&nbsp;</p><p><br></p><p>A continuaci??n, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la informaci??n recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: '.$url.' para verificar que la informaci??n es correcta.</p><p><br></p><p>Gracias por su colaboraci??n.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
					
					}elseif($request->user()->language == 'fr'){
					
						$subject = "Veuillez v??rifier vos informations de voyage";
						$msg = "<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d???avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons r??dig??e bas??e sur les informations re??ues.&nbsp;</p><p><br></p><p>Pourriez-vous s???il vous pla??t examiner la lettre, puis cliquer sur ce lien: '.$url.' pour v??rifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L?????quipe du GProCongr??s II</p><div><br></div>";
			
					}elseif($request->user()->language == 'pt'){
					
						$subject = "Por favor verifique sua Informa????o de Viagem";
						$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Agradecemos por submeter sua informa????o de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informa????o que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e da?? clicar neste link: '.$url.' para verificar que a informa????o esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p><div><br></div>';
					
					}else{
					
						$subject = 'Please verify your travel Information.';
						$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for submitting your travel information.&nbsp;&nbsp;</p><p><br></p><p>Please find a visa letter attached, that we have drafted based on the information received.&nbsp;</p><p><br></p><p>Would you please review the letter, and then click on this link: '.$url.' to verify that the information is correct.</p><p><br></p><p>Thank you for your assistance.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
											
					}

					$passportInfo = \App\Models\PassportInfo::where('user_id',$request->user()->id)->first();

					$pdf = \PDF::loadView('email_templates.sponsorship_info_show', $passportInfo->toArray());
					$pdf->setPaper('L');
					$pdf->output();
					$canvas = $pdf->getDomPDF()->getCanvas();
					
					$height = $canvas->get_height();
					$width = $canvas->get_width();
					$canvas->set_opacity(.2,"Multiply");
					$canvas->page_text($width/5, $height/2, 'Draft', null,
					70, array(0,0,0),2,2,-30);

					$fileName = strtotime("now").rand(11,99).'.pdf';

					$path = public_path('uploads/file/');
					
					$pdf->save($path . '/' . $fileName);
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
					\App\Helpers\commonHelper::userMailTrigger($request->user()->id,$msg,$subject);

					return response(array("error" => false, "message" =>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Your_submission_has_been_sent')), 200);
					
                }catch (\Exception $e){
                    
                    return response(array("error" => true, "message" => \App\Helpers\commonHelper::ApiMessageTranslaterLabel($request->user()->language,'Something-went-wrongPlease-try-again')), 403);
                
                }

        }

	}

    

	public function PassportInfoApprove(Request $request){
	
		try{
			$passportApprove= \App\Models\PassportInfo::where('user_id',$request->user()->id)->first();

			$passportApprove->status='Approve';
			
			$passportApprove->save();
			
			return response(array('message'=>'Status Approved Successfully.'),200);
				
			
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
		}
	}

	public function PassportInfoReject(Request $request){
	
		$rules = [
			'remark' => 'required',
			
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

			$passportReject= \App\Models\PassportInfo::where('user_id',$request->user()->id)->first();

			$passportReject->status='Reject';
			$passportReject->remark=$request->json()->get('remark');
			
			$passportReject->save();
			
			return response(array('message'=>'PassportInfo status changed successfully'),200);
				
			
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
			}
		}
	
	}
}
