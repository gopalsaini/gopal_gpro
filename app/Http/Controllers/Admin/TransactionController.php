<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TransactionController extends Controller {

    public function list(Request $request) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::orderBy('id','desc');

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


			// ->addColumn('razorpay_order_id', function($data){
			// 	if($data['payment_by']=='1') {
			// 		return $data->razorpay_order_id;
			// 	} else if($data['payment_by']=='2') {
			// 		return $data->paypal_payerid;
			// 	} else {
			// 		return 'N/A';
			// 	}
		    // })

			// ->addColumn('transaction_id', function($data){
			// 	return $data->transaction_id;
		    // })

			->addColumn('country', function($data){
				return $data->country_of_sender ?? '-';
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

			->addColumn('image', function($data){
				if($data->file){
					return '<a style="color: blue !important;" href="'.asset('uploads/transaction/'.$data->file).'" target="_blank">Open</a>';
				}else{
					return "-";
				}
				
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

				if (\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '13') {
					if ($data->status == '1') {
						return '<div class="badge rounded-pill pill-badge-success">Approved</div>';
					} else if ($data->status == '2') {
						return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';
					} else if ($data->status == '0' && $data->method != 'Online') {

						return '<div style="display:flex"><a data-id="'.$data->id.'" data-type="1" title="Transaction Approve" class="btn btn-sm btn-outline-success m-1 -change">Approve</a>
						<a data-id="'.$data->id.'" data-type="2" title="Transaction Decline" class="btn btn-sm btn-outline-danger m-1 declineRemark">Decline</a></div>';
					}
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();

        return view('admin.transaction.list');

	}

	public function status(Request $request) {

		$result = \App\Models\Transaction::with('User')->find($request->post('id'));

		if ($result) {
			
			$name= $result->User->name.' '.$result->User->last_name;
			$to = $result->User->email;
			if ((int)$request->post('status') === 1) {

				
				$result->status = $request->post('status');
				$result->payment_status = '2';
				$result->save();

				$wallet = \App\Models\Wallet::where('transaction_id',$result->id)->where('user_id',$result->User->id)->first();
				$wallet->status = 'Success';
				$wallet->save();

				$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($result->User->id, true);
				$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($result->User->id, true);
				$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($result->User->id, true);
				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($result->User->id, true);

				if(\App\Helpers\commonHelper::getTotalPendingAmount($result->User->id)) {

					if($result->User->language == 'sp'){

						$subject = "Pago parcial recibido. ??Gracias!";
						$msg = '<p>Estimado '.$result->User->name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aqu?? tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a m??s tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripci??n quedar?? sin efecto y se ceder?? su lugar a otra persona.</p><p><br></p><p>??Tiene preguntas? Simplemente responda a este correo electr??nico y nuestro equipo estar?? encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p><div><br></div>';
					
					}elseif($result->User->language == 'fr'){
					
						$subject = "Paiement partiel re??u.  Merci !";
						$msg = "<p>Cher ".$result->User->name.",&nbsp;</p><p><br></p><p>Un montant de&nbsp; '.$result->amount.'$ a ??t?? re??u sur votre compte.&nbsp;</p><p>Merci d???avoir effectu?? ce paiement partiel.&nbsp;</p><p>Voici un r??sum?? de l?????tat actuel de vos paiements :</p><p><br></p><p>MONTANT TOTAL ?? PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS D??J?? EFFECTU??S ET ACCEPT??S : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p><p>SOLDE RESTANT D?? : ".$totalPendingAmount."</p><p><br></p><p>Veuillez payer le solde au plus tard le 31st August 2023.&nbsp;</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n???est pas re??u avant le 31st August 2023, votre inscription sera annul??e et votre place sera donn??e ?? quelqu???un d???autre.&nbsp;</p><p><br></p><p>Avez-vous des questions concernant votre paiement ou d???autres probl??mes ? R??pondez simplement ?? cet e-mail et notre ??quipe sera ravi d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L?????quipe du GProCongr??s II</p>";
			
					}elseif($result->User->language == 'pt'){
					
						$subject = "Pagamento parcial recebido. Obrigado";
						$msg = '<p>Prezado'.$result->User->name.',</p><p><br></p><p>Uma quantia de $'.$result->amount.' foi recebido na sua conta.</p><p>Obrigado por ter efetuado esse pagamento parcial.</p><p>Aqui est?? o resumo do estado atual do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM D??VIDA: '.$totalPendingAmount.'</p><p><br></p><p>Por favor pague o saldo at?? o dia a seguir ou antes de 31st August 2023.</p><p><br></p><p>POR FAVOR NOTE: Se seu pagamento n??o for recebido at?? o dia 31st August 2023, a sua inscri????o ser?? cancelada, e a sua vaga ser?? atribu??da a outra pessoa.</p><p><br></p><p>Tens perguntas sobre o seu pagamento ou qualquer outra quest??o? Simplesmente responda a este e-mail, e nossa equipe estar?? muito feliz para se conectar com voc??.&nbsp;</p><p><br></p><p>Ore conosco a medida que nos esfor??amos para multiplicar os n??meros e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Partial payment received. Thank you!';
						$msg = '<div>Dear '.$result->User->name.',</div><div><br></div><div>An amount of $'.$result->amount.' has been received on your account.&nbsp;</div><div>Thank you for making this partial payment.&nbsp;</div><div>Here is a summary of your current payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions about your payment or any other issues? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
												
					}
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					\App\Helpers\commonHelper::userMailTrigger($result->User->id,$msg,$subject);

					// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
				} else {

					$user = \App\Models\User::find($result->User->id);
					$user->stage = 3;
					$user->status_change_at = date('Y-m-d H:i:s');
					$user->save();

					$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
				
					if($resultSpouse){

						$resultSpouse->stage = 3;
						$resultSpouse->payment_status = '2';
						$resultSpouse->status_change_at = date('Y-m-d H:i:s');
						$resultSpouse->save();
					}

					if($result->User->language == 'sp'){

						$subject = "PENDIENTE: Pago de saldo para GProCongress II";
						$msg = '<p>Estimado '.$result->User->name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aqu?? tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR : '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a m??s tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripci??n quedar?? sin efecto y se ceder?? su lugar a otra persona.</p><p><br></p><p>??Tiene preguntas? Simplemente responda a este correo electr??nico y nuestro equipo estar?? encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p>Si tiene alguna pregunta sobre su pago, simplemente responda a este correo electr??nico, y nuestro equipo se comunicar?? con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el n??mero de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($result->User->language == 'fr'){
					
						$subject = "Paiement int??gral re??u.  Merci !";
						$msg = "<p>Cher ".$result->User->name.",&nbsp;</p><p><br></p><p>Un montant de&nbsp; '.$result->amount.' $ a ??t?? re??u sur votre compte.&nbsp;</p><p>Vous avez maintenant pay?? la somme totale pour le GProCongr??s II.&nbsp; Merci !&nbsp;</p><p>Voici un r??sum?? de l?????tat de votre paiement :</p><p><br></p><p>MONTANT TOTAL ?? PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS D??J?? EFFECTU??S ET ACCEPT??S : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS : ".$totalAmountInProcess."</p><p>SOLDE RESTANT D?? : ".$totalPendingAmount."</p><p><br></p><p>Si vous avez des questions concernant votre paiement, r??pondez simplement ?? cet e-mail et notre ??quipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous effor??ons de multiplier les nombres et de renforcer les capacit??s des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L?????quipe du GProCongr??s II</p>";
			
					}elseif($result->User->language == 'pt'){
					
						$subject = "Pagamento recebido na totalidade. Obrigado!";
						$msg = '<p>Prezado '.$result->User->name.',</p><p><br></p><p>Uma quantia de $ '.$result->amount.' foi recebido na sua conta.</p><p>Voc?? agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p><br></p><p>Aqui est?? o resumo do estado do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM D??VIDA: '.$totalPendingAmount.'</p><p><br></p><p>Se voc?? tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com voc??.&nbsp;</p><p><br></p><p>Ore conosco a medida que nos esfor??amos para multiplicar os n??meros e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Partial payment received. Thank you!';
						$msg = '<div>Dear '.$result->User->name.',</div><div><br></div><div>An amount of $'.$result->amount.' has been received on your account.&nbsp;</div><div>Thank you for making this partial payment.&nbsp;</div><div>Here is a summary of your current payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions about your payment or any other issues? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
												
					}
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					
					if($user->language == 'sp'){

						$subject = "Por favor, env??e su informaci??n de viaje.";
						$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp; &nbsp;&nbsp;</p>';
					
					}elseif($user->language == 'fr'){
					
						$subject = "Veuillez soumettre vos informations de voyage.";
						$msg = "<p>Cher '.$user->name.',&nbsp;</p><p>Nous sommes ravis de vous voir au GProCongr??s ?? Panama City, au Panama !</p><p><br></p><p>Pour aider les d??l??gu??s ?? obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</p><p><br></p><p>Veuillez r??pondre ?? cet e-mail avec vos informations de vol.&nbsp; D??s r??ception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons re??ues sont correctes.&nbsp;</p><p><br></p><p>Cordialement,</p><p>L?????quipe du GProCongr??s II</p>";
			
					}elseif($user->language == 'pt'){
					
						$subject = "Por favor submeta sua informa????o de viagem";
						$msg = '<p>Prezado '.$user->name.',&nbsp;</p><p><br></p><p>N??s estamos emocionados em ver voc?? no CongressoGPro na Cidade de Panam??, Panam??!</p><p><br></p><p>Para ajudar os delegados na obten????o de vistos, n??s estamos pedindo que submetam a n??s sua informa????o de viagem.&nbsp;</p><p><br></p><p>Por favor responda este e-mail com informa????es do seu voo. Depois de recebermos, iremos lhe enviar um e-mail confirmando que a informa????o que recebemos ?? correta.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro&nbsp; &nbsp; &nbsp;&nbsp;</p>';
					
					}else{
					
						$subject = 'Please submit your travel information.';
						$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
											
					}
					// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
					// \App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);


				}

			} else if ((int)$request->post('status') === 2) {

				$result->status = $request->post('status');
				$result->payment_status = '9';
				$result->decline_remark = $request->post('remark');
				$result->save();

				$wallet = \App\Models\Wallet::where('transaction_id',$result->id)->where('user_id',$result->User->id)->first();
				$wallet->type = 'Cr';
				$wallet->status = 'Failed';
				$wallet->save();

				$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($result->User->id, true);
				$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($result->User->id, true);
				$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($result->User->id, true);
				$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($result->User->id, true);

				if($result->User->language == 'sp'){

					$subject = "Su pago ha sido rechazado";
					$msg = '<p>Estimado '.$result->User->name.'</p><p><br></p><p>Su pago reciente a GProCongress ha sido rechazado.</p><p>Este es un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$result->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripci??n quedar?? sin efecto y se ceder?? su lugar a otra persona.</p><p><br></p><p>??Necesita asesoramiento mientras intenta pagar de nuevo? Responda a este correo electr??nico y los miembros de nuestro equipo le ayudar??n sin lugar a dudas.</p><p><br></p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->User->language == 'fr'){
				
					$subject = "Votre paiement a ??t?? refus??";
					$msg = '<p>Cher '.$result->User->name.',&nbsp;</p><p>Votre paiement r??cent pour le GProCongr??s a ??t?? refus??.&nbsp;</p><p><br></p><p>Voici un r??sum?? actuel de l?????tat de votre paiement :</p><p><br></p><p>MONTANT TOTAL ?? PAYER : '.$result->amount.'</p><p>PAIEMENTS D??J?? EFFECTU??S ET ACCEPT??S : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT D?? : '.$totalPendingAmount.'</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n???est pas re??u avant 31st August 2023, votre inscription sera annul??e et votre place sera donn??e ?? quelqu???un d???autre.&nbsp;</p><p><br></p><p>Avez-vous besoin d???aide pendant que vous tentez ?? nouveau de payer ?&nbsp; Veuillez r??pondre ?? cet e-mail et les membres de notre ??quipe vous aideront ?? coup s??r.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L?????quipe GProCongr??s II</p>';
				
				}elseif($result->User->language == 'pt'){
				
					$subject = "Seu pagamento foi declinado";
					$msg = '<p>Prezado '.$result->User->name.',&nbsp;</p><p><br></p><p>O seu recente pagamento para o CongressoGPro foi declinado.</p><p>Aqui est?? o resumo do estado atual do seu pagamento:</p><p><br></p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$result->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM D??VIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento n??o for feito at?? 31st August 2023 , a sua inscri????o ser?? cancelada, e a sua vaga ser?? atribu??da a outra pessoa.</p><p><br></p><p>Precisa de ajuda enquanto voc?? tenta terminar fazer o pagamento? Por favor responda a este e-mail e membro da nossa equipe vai lhe ajudar com certeza.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Your payment has been declined';
					$msg = '<p>Dear '.$result->User->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
				
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->User->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
			}

			return response(array('message'=>'Transaction status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function delete(Request $request, $id) {

		$result = \App\Models\Transaction::find($id);
		
		if ($result) {

			\App\Models\Transaction::where('id', $id)->delete();
			$request->session()->flash('success','Transaction deleted successfully.');
		} else {
			$request->session()->flash('error','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }
	
}
