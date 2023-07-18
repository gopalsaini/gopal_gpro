<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\commonHelper;

class OneTimeJobsController extends Controller {


	public function airfareDiscount(Request $request){
		
		try {
			
			$results = \App\Models\User::where('profile_status', '!=', 'ApprovedNotComing')->where('stage','=','3')->get();
			
			if(count($results) > 0){
				$resultData = '';
				foreach ($results as $key => $user) {
				
					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';

					if($user->language == 'sp'){

						$subject = 'Información importante sobre el descuento en los pasajes aéreos para el GProCongress';
						$msg = "<p>Estimado ".$user->name.' '.$user->last_name.",&nbsp;</p><p><br></p>
						<p>¡Buenas noticias! Copa Airlines está ofreciendo un 10% de descuento en tarifas aéreas al aeropuerto PTY en la Ciudad de Panamá para todos los delegados del GProCongress. A partir del 7 de julio de 2023, puede reservar su vuelo en Copa utilizando el 'código de tour' 89243. Este descuento del 10% se aplica únicamente a la tarifa aérea y no incluye impuestos.  El viaje debe realizarse entre el 7 de noviembre de 2023 y el 22 de noviembre de 2023.</p>
						<p>Si desea comprar sus pasajes utilizando este descuento, puede reservar su vuelo en línea en <a href='https://www.copaair.com' >https://www.copaair.com</a>, o puede dirigirse a su Centro de Atención Telefónica, o a cualquiera de las oficinas de Copa Airlines.</p>
						<p>Cordialmente,</p><p>Equipo GProCongress II</p>";

					}elseif($user->language == 'fr'){
					
						$subject = "Informations importantes concernant la réduction des billets d'avion pour GProCongress!";
						$msg = "<p>Cher ".$user->name.' '.$user->last_name.",&nbsp;</p><p><br></p>
						<p>Bonne nouvelle ! Copa Airlines offre une réduction de 10% sur les billets d'avion à destination de l'aéroport PTY de Panama City pour tous les délégués du GProCongress. À partir du 7 juillet 2023, vous pouvez réserver votre vol sur Copa en utilisant le « code tour » 89243. Ce rabais de 10% s'applique uniquement au tarif du billet d'avion et n'inclut pas les taxes.  Le voyage doit être effectué entre le 7 novembre 2023 et le 22 novembre 2023.</p>
						<p>Si vous souhaitez acheter vos billets en utilisant cette réduction, vous pouvez réserver votre vol en ligne sur <a href='https://www.copaair.com' >https://www.copaair.com</a>ou vous pouvez vous rendre à leur centre d'appels, ou à n'importe quel bureau de Copa Airlines.</p>
						<p>Chaleureusement,</p><p>L'équipe GProCongress II</p>";

					}elseif($user->language == 'pt'){
					
						$subject = 'Informações importantes sobre desconto de passagens aéreas para o GProCongresso!';
						$msg = "<p>Caro ".$user->name.' '.$user->last_name.",&nbsp;</p><p><br></p>
						<p>Boas notícias! A Copa Airlines está oferecendo um desconto de 10% nas passagens aéreas para o aeroporto PTY na Cidade do Panamá para todos os delegados do GProCongresso. A partir de 7 de Julho de 2023, você pode reservar seu voo na Copa usando o “tour code” 89243. Este desconto de 10% aplica-se apenas à tarifa da passagem aérea e não inclui impostos. A viagem deve ser concluída entre 7 de Novembro de 2023 e 22 de Novembro de 2023.</p>
						<p>Se você deseja comprar suas passagens com este desconto, você pode reservar seu voo online em <a href='https://www.copaair.com' >https://www.copaair.com</a>, ou pode ir ao Call Center ou a qualquer escritório da Copa Airlines.</p>
						<p>Calorosamente,</p><p>Equipe GProCongresso II</p>";

					}else{
					
						$subject = 'Important information regarding airfare discount for GProCongress!';
						$msg = "<p>Dear ".$user->name.' '.$user->last_name." ,&nbsp;</p><p><br></p>
						<p>Great news! Copa Airlines is offering a 10% discount on airfares to PTY airport in Panama City for all GProCongress delegates. Starting on 7 July 2023, you can book your flight on Copa using the “tour code” 89243. This 10% discount applies to the airfare rate only, and does not include taxes.  Travel must be completed between 7 November 2023, and 22 November 2023.</p>
						<p>If you wish to purchase your tickets using this discount, you may book your flight online at <a href='https://www.copaair.com' >https://www.copaair.com</a>, or you can go to their Call Center, or to any Copa Airlines offices.</p>
						<p>Warmly,</p><p>GProCongress II Team</p>";
		
					}

					$resultData.=$user->id.','.$user->name.','.$user->added_as.','.$user->email.','.$user->stage.','.$user->amount.'<br>';


					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Important information regarding airfare discount for GProCongress!');
				
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

	public function onlinePaymentStatusAndStatusIs0(){ 

		$results = \App\Models\Transaction::where('payment_status','0')
											->where('status','0')
											->where('method','Online')
											->where('bank_transaction_id','=',null)
											->whereMonth('created_at','!=', date('m'))
											->get();

		$userDataSet = '';
		
		if(!empty($results)){

			foreach($results as $value){

				$transaction=\App\Models\Transaction::where('id',$value->id)->first();

				if($transaction){

					$transaction->description='Payment failed';
					$transaction->payment_status='7';
					$transaction->status='2';
					$transaction->save();

					$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
					$Wallet->status = 'Failed';
					$Wallet->save();

					$userDataSet .= 'user Id: ' . $transaction->user_id . ', Status: Failed, orderId : '.$value->order_id.'<br>';

				}
				
			}
		}
		
		print_r($userDataSet);
		echo 'done';
	}

	public function onlinePaymentStatus7AndStatusIs0(){ 

		$results = \App\Models\Transaction::where('payment_status','7')
											->where('status','0')
											->where('method','Online')
											->where('bank_transaction_id','=',null)
											->get();

		$userDataSet = '';
		
		if(!empty($results)){

			foreach($results as $value){

				$transaction=\App\Models\Transaction::where('id',$value->id)->first();

				if($transaction){

					$transaction->status='2';
					$transaction->save();

					$Wallet = \App\Models\Wallet::where('transaction_id',$transaction->id)->first();
					$Wallet->status = 'Failed';
					$Wallet->save();

					$userDataSet .= 'user Id: ' . $transaction->user_id . ', Status: Failed, orderId : '.$value->order_id.'<br>';

				}
				
			}
		}
		print_r($userDataSet);
		echo 'done';
	}

	public function acceptanceSendAccordingToLanguage(){ 

		$results = \App\Models\User::where('language','!=','en')
											->where('stage','4')
											->get();

		$userDataSet = '';
		
		if(!empty($results)){

			foreach($results as $user){

				$passportInfo =  \App\Models\PassportInfo::where('user_id',$user->id)->first();
				if($passportInfo){

					if($user->language != 'sp'){

						$userDataSet.='Email:'.$user->email.', Language:'.$user->language.', Stage:'.$user->stage.'<br>';

						$rajiv_richard = '<img src="'.asset('images/rajiv_richard.png').'">';

						$Spouse = \App\Models\User::where('parent_id',$user->id)->where('added_as','Spouse')->where('spouse_confirm_status','Approve')->first(); 

						$SpouseParent = \App\Models\User::where('id',$user->parent_id)->first();

						if($Spouse){

							$amount = $user->amount+$Spouse->amount;

							$amount = $amount/2;

						}elseif($SpouseParent && $user->added_as == 'Spouse' && $user->spouse_confirm_status == 'Approve'){

							$amount = $user->amount+$SpouseParent->amount;
							
							$amount = $amount/2;

						}else{

							$amount = $user->amount;
						}

						$passportApproveArray= [
							'salutation'=>$passportInfo->salutation,
							'name'=>$passportInfo->name,
							'passport_no'=>$passportInfo->passport_no,
							'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($passportInfo->country_id),
							'rajiv_richard'=>$rajiv_richard,
							'amount'=>$amount,
							'lang'=>$user->language,
						];

						if($user->language == 'sp'){
							$fileEnNameFl = 'acceptance_letter_spanish'.strtotime("now").rand(0000000,9999999).'.pdf';
						}elseif($user->language == 'fr'){
							$fileEnNameFl = 'acceptance_letter_french'.strtotime("now").rand(0000000,9999999).'.pdf';
						}elseif($user->language == 'pt'){
							$fileEnNameFl = 'acceptance_letter_portuguese'.strtotime("now").rand(0000000,9999999).'.pdf';
						}else{
							$fileEnNameFl = 'acceptance_letter_english'.strtotime("now").rand(0000000,9999999).'.pdf';
						}

						$pdf = \PDF::loadView('email_templates.financial_letter',$passportApproveArray);
						$pdf->setPaper('L');
						$pdf->output();
						
						$path = public_path('uploads/file/');
						
						$pdf->save($path . '/' . $fileEnNameFl);

						$passportInfo->financial_letter=$fileEnNameFl;

						$passportInfo->save();

					}
					
				}
				
			}
		}
		print_r($userDataSet);
		echo 'done';
	}

	public function acceptanceEmailSend(){ 

		$results = [203];

		$userDataSet = '';
		
		if(!empty($results)){

			foreach($results as $id){

				$passportApprove= \App\Models\PassportInfo::where('user_id',$id)->first();

				$user= \App\Models\User::where('id',$id)->first();
				
				$to = $user->email; $fileEnName = '';
				$rajiv_richard = '<img src="'.asset('images/rajiv_richard.png').'">';

				if($user->language == 'sp'){

					$subject = '¡Buenas noticias! No necesita una visa para GProCongress II';
					$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Según la información que envió, no necesita una visa para ingresar a Panamá en noviembre para el GProCongress II. Adjuntamos los siguientes documentos que deberá entregar a los funcionarios panameños a su llegada:</p>
					<p>&nbsp;&nbsp;&nbsp;1.	Carta de aceptación: confirmando la invitación de RREACH para que asista al Congreso; y</p>
					<p>&nbsp;&nbsp;&nbsp;2.	Carta de certificación bancaria: que confirma la intención y la capacidad de RREACH de pagar todos sus costos de viaje mientras usted esté en Panamá.</p>
					<p>También se pueden encontrar copias de estos dos documentos en su perfil de GProCongress en nuestro sitio web.</p>
					<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, responda a este correo electrónico.</p>
					<p>Atentamente,</p><p>Equipo GProCongress II&nbsp; &nbsp;&nbsp;</p>';

				}elseif($user->language == 'fr'){
				
					$subject = 'Bonne nouvelle!  Vous n’avez pas besoin de visa pour GProCongress II';
					$msg = '<p>Cher  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>D’après les informations que vous avez fournies, vous n’avez pas besoin de visa pour vous rendre au Panama en novembre pour le GProCongress II.  Nous avons joint les documents suivants, que vous devrez remettre aux autorités panaméennes à votre arrivée :</p>
					<p>&nbsp;&nbsp;&nbsp;1.	Lettre d’acceptation – confirmant l’invitation de RREACH à participer au Congrès ;et</p>
					<p>&nbsp;&nbsp;&nbsp;2.	Lettre de certification bancaire - confirmant l’intention et la capacité de RREACH  à payer tous vos frais de séjour au Panama.</p>
					<p>Des copies de ces deux documents peuvent également être trouvées dans votre profil GProCongress sur notre site Web.</p>
					<p>Si vous avez des questions ou si vous souhaitez parler à l’un des membres de notre équipe, veuillez répondre à cet e-mail.</p>
					<p>Cordialement,</p><p>L’équipe GProCongress II&nbsp; &nbsp;&nbsp;</p>';

				}elseif($user->language == 'pt'){
				
					$subject = 'Boas notícias! Você não precisa de visto para o GProCongresso II';
					$msg = '<p>Caro  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Com base nas informações que você enviou, você não precisa de visto para ir ao Panamá em novembro para o GProCongresso II. Anexamos os seguintes documentos, que você deverá entregar aos funcionários panamenhos na sua chegada: </p>
					<p>&nbsp;&nbsp;&nbsp;1.	Carta de aceitação – confirmando o convite da RREACH para você participar do Congresso; e</p>
					<p>&nbsp;&nbsp;&nbsp;2.	Carta de Certificação Bancária - confirmando a intenção e a capacidade da RREACH de pagar todos os seus custos de viagem enquanto estiver no Panamá.</p>
					<p>Cópias desses dois documentos também podem ser encontradas em seu perfil do GProCongresso em nosso site.</p>
					<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
					<p>Calorosamente,</p><p>Equipe GProCongresso II&nbsp; &nbsp;&nbsp;</p>';

				}else{
				
					$subject = 'Good news!  You don’t need a visa for GProCongress II.';
					$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Based on the information you submitted, you do not need a visa to go to Panama in November for GProCongress II.  We have attached the following documents, which you will need to give to the Panamanian officials upon your arrival: </p>
					<p>&nbsp;&nbsp;&nbsp;1.	Acceptance letter – confirming RREACH’s invitation to you to attend the Congress; and  </p>
					<p>&nbsp;&nbsp;&nbsp;2.	Bank Certification letter – confirming RREACH’s intention and ability to pay for all your travel costs while in Panama.</p>
					<p>Copies of these two documents can also be found in your GProCongress profile on our website.</p>
					<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
					<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
									
				}

				$Spouse = \App\Models\User::where('parent_id',$user->id)->where('added_as','Spouse')->where('spouse_confirm_status','Approve')->first(); 

				$SpouseParent = \App\Models\User::where('id',$user->parent_id)->first();

				if($Spouse){

					$amount = $user->amount+$Spouse->amount;

					$amount = $amount/2;

				}elseif($SpouseParent && $user->added_as == 'Spouse' && $user->spouse_confirm_status == 'Approve'){

					$amount = $user->amount+$SpouseParent->amount;
					
					$amount = $amount/2;

				}else{

					$amount = $user->amount;
				}
				$passportApproveArray= [
					'salutation'=>$passportApprove->salutation,
					'name'=>$passportApprove->name,
					'passport_no'=>$passportApprove->passport_no,
					'citizenship'=>\App\Helpers\commonHelper::getCountryNameById($passportApprove->country_id),
					'rajiv_richard'=>$rajiv_richard,
					'amount'=>$amount,
					'lang'=>$user->language,
				];

				if($user->language == 'sp'){
					$fileEnNameFl = 'acceptance_letter_spanish'.strtotime("now").rand(0000000,9999999).'.pdf';
				}elseif($user->language == 'fr'){
					$fileEnNameFl = 'acceptance_letter_french'.strtotime("now").rand(0000000,9999999).'.pdf';
				}elseif($user->language == 'pt'){
					$fileEnNameFl = 'acceptance_letter_portuguese'.strtotime("now").rand(0000000,9999999).'.pdf';
				}else{
					$fileEnNameFl = 'acceptance_letter_english'.strtotime("now").rand(0000000,9999999).'.pdf';
				}

				if($user->language != 'sp'){

					$pdf = \PDF::loadView('email_templates.financial_letter',$passportApproveArray);
					$pdf->setPaper('L');
					$pdf->output();
					
					$path = public_path('uploads/file/');
					
					$pdf->save($path . '/' . $fileEnNameFl);

					$passportApprove->financial_letter=$fileEnNameFl;

					$fileEnName = public_path('uploads/file/'.$fileEnNameFl);

				}
				
				$pdf = \PDF::loadView('email_templates.financial_sp_letter',$passportApproveArray);
				$pdf->setPaper('L');
				$pdf->output();
				$fileName = 'acceptance_letter_spanish'.strtotime("now").rand(0000000,9999999).'.pdf';
				$path = public_path('uploads/file/');
				
				$pdf->save($path . '/' . $fileName);

				$passportApprove->financial_spanish_letter=$fileName;
				$passportApprove->status='Approve';
				
				$passportApprove->save();

				$files = [
					public_path('uploads/file/'.$fileName),
				];

				\Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files,$fileEnName) {
					$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
					$message->subject($subject);
					$message->to($to);
					
					foreach ($files as $file){
						$message->attach($file);
					}

					if($fileEnName){
						$message->attach($fileEnName);
					}
					
				});
				
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Update Acceptance Letter');
				
				
			}
		}
		print_r($userDataSet);
		echo 'done';
	}



}
