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
				return \App\Helpers\commonHelper::getUserNameById($data->user_id);
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
					return '<a href="'.asset('uploads/transaction/'.$data->file).'" target="_blank">Open</a>';
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

						$subject = "Pago parcial recibido. ¡Gracias!";
						$msg = '<p>Estimado '.$result->User->name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a más tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo estará encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p><div><br></div>';
					
					}elseif($result->User->language == 'fr'){
					
						$subject = "Paiement partiel reçu.  Merci !";
						$msg = "<p>Cher ".$result->User->name.",&nbsp;</p><p><br></p><p>Un montant de&nbsp; '.$result->amount.'$ a été reçu sur votre compte.&nbsp;</p><p>Merci d’avoir effectué ce paiement partiel.&nbsp;</p><p>Voici un résumé de l’état actuel de vos paiements :</p><p><br></p><p>MONTANT TOTAL À PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p><p>SOLDE RESTANT DÛ : ".$totalPendingAmount."</p><p><br></p><p>Veuillez payer le solde au plus tard le 31st August 2023.&nbsp;</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous des questions concernant votre paiement ou d’autres problèmes ? Répondez simplement à cet e-mail et notre équipe sera ravi d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe du GProCongrès II</p>";
			
					}elseif($result->User->language == 'pt'){
					
						$subject = "Pagamento parcial recebido. Obrigado";
						$msg = '<p>Prezado'.$result->User->name.',</p><p><br></p><p>Uma quantia de $'.$result->amount.' foi recebido na sua conta.</p><p>Obrigado por ter efetuado esse pagamento parcial.</p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>Por favor pague o saldo até o dia a seguir ou antes de 31st August 2023.</p><p><br></p><p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Tens perguntas sobre o seu pagamento ou qualquer outra questão? Simplesmente responda a este e-mail, e nossa equipe estará muito feliz para se conectar com você.&nbsp;</p><p><br></p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Partial payment received. Thank you!';
						$msg = '<div>Dear '.$result->User->name.',</div><div><br></div><div>An amount of $'.$result->amount.' has been received on your account.&nbsp;</div><div>Thank you for making this partial payment.&nbsp;</div><div>Here is a summary of your current payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions about your payment or any other issues? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
												
					}
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
				} else {

					$user = \App\Models\User::find($result->User->id);
					$user->stage = 3;
					$user->save();

					$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
				
					if($resultSpouse){

						$resultSpouse->stage = 3;
						$resultSpouse->payment_status = '2';
						$resultSpouse->save();
					}

					if($result->User->language == 'sp'){

						$subject = "PENDIENTE: Pago de saldo para GProCongress II";
						$msg = '<p>Estimado '.$result->User->name.',</p><p><br></p><p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p><p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR : '.$totalAcceptedAmount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>Por favor, pague el saldo a más tardar en 31st August 2023.</p><p><br></p><p>POR FAVOR, TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo estará encantado de comunicarse con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p>Si tiene alguna pregunta sobre su pago, simplemente responda a este correo electrónico, y nuestro equipo se comunicará con usted.</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
					
					}elseif($result->User->language == 'fr'){
					
						$subject = "Paiement intégral reçu.  Merci !";
						$msg = "<p>Cher ".$result->User->name.",&nbsp;</p><p><br></p><p>Un montant de&nbsp; '.$result->amount.' $ a été reçu sur votre compte.&nbsp;</p><p>Vous avez maintenant payé la somme totale pour le GProCongrès II.&nbsp; Merci !&nbsp;</p><p>Voici un résumé de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : ".$totalAcceptedAmount."</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : ".$totalAcceptedAmount."</p><p>PAIEMENTS EN COURS : ".$totalAmountInProcess."</p><p>SOLDE RESTANT DÛ : ".$totalPendingAmount."</p><p><br></p><p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe du GProCongrès II</p>";
			
					}elseif($result->User->language == 'pt'){
					
						$subject = "Pagamento recebido na totalidade. Obrigado!";
						$msg = '<p>Prezado '.$result->User->name.',</p><p><br></p><p>Uma quantia de $ '.$result->amount.' foi recebido na sua conta.</p><p>Você agora pagou na totalidade para o II CongressoGPro. Obrigado!</p><p><br></p><p>Aqui está o resumo do estado do seu pagamento:</p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você.&nbsp;</p><p><br></p><p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>A Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Partial payment received. Thank you!';
						$msg = '<div>Dear '.$result->User->name.',</div><div><br></div><div>An amount of $'.$result->amount.' has been received on your account.&nbsp;</div><div>Thank you for making this partial payment.&nbsp;</div><div>Here is a summary of your current payment status:</div><div><br></div><div>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</div><div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div><div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div><div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div><div>Please pay the balance on or before 31st August 2023.&nbsp;</div><div><br></div><div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div><div>Do you have questions about your payment or any other issues? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div><div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div><div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
												
					}
					
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);


					if($user->language == 'sp'){

						$subject = "Por favor, envíe su información de viaje.";
						$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp; &nbsp;&nbsp;</p>';
					
					}elseif($user->language == 'fr'){
					
						$subject = "Veuillez soumettre vos informations de voyage.";
						$msg = "<p>Cher '.$user->name.',&nbsp;</p><p>Nous sommes ravis de vous voir au GProCongrès à Panama City, au Panama !</p><p><br></p><p>Pour aider les délégués à obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</p><p><br></p><p>Veuillez répondre à cet e-mail avec vos informations de vol.&nbsp; Dès réception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons reçues sont correctes.&nbsp;</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>";
			
					}elseif($user->language == 'pt'){
					
						$subject = "Por favor submeta sua informação de viagem";
						$msg = '<p>Prezado '.$user->name.',&nbsp;</p><p><br></p><p>Nós estamos emocionados em ver você no CongressoGPro na Cidade de Panamá, Panamá!</p><p><br></p><p>Para ajudar os delegados na obtenção de vistos, nós estamos pedindo que submetam a nós sua informação de viagem.&nbsp;</p><p><br></p><p>Por favor responda este e-mail com informações do seu voo. Depois de recebermos, iremos lhe enviar um e-mail confirmando que a informação que recebemos é correta.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro&nbsp; &nbsp; &nbsp;&nbsp;</p>';
					
					}else{
					
						$subject = 'Please submit your travel information.';
						$msg = '<p>Dear '.$user->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
											
					}
					// \App\Helpers\commonHelper::sendSMS($result->User->mobile);
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);


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
					$msg = '<p>Estimado '.$result->User->name.'</p><p><br></p><p>Su pago reciente a GProCongress ha sido rechazado.</p><p>Este es un resumen actual del estado de su pago:</p><p><br></p><p>IMPORTE TOTAL A PAGAR: '.$result->amount.'</p><p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p><p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p><p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR TENGA EN CUENTA: Si no se recibe el pago completo antes de 31st August 2023, su inscripción quedará sin efecto y se cederá su lugar a otra persona.</p><p><br></p><p>¿Necesita asesoramiento mientras intenta pagar de nuevo? Responda a este correo electrónico y los miembros de nuestro equipo le ayudarán sin lugar a dudas.</p><p><br></p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->User->language == 'fr'){
				
					$subject = "Votre paiement a été refusé";
					$msg = '<p>Cher '.$result->User->name.',&nbsp;</p><p>Votre paiement récent pour le GProCongrès a été refusé.&nbsp;</p><p><br></p><p>Voici un résumé actuel de l’état de votre paiement :</p><p><br></p><p>MONTANT TOTAL À PAYER : '.$result->amount.'</p><p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : '.$totalAcceptedAmount.'</p><p>PAIEMENTS EN COURS : '.$totalAmountInProcess.'</p><p>SOLDE RESTANT DÛ : '.$totalPendingAmount.'</p><p><br></p><p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p><p>Avez-vous besoin d’aide pendant que vous tentez à nouveau de payer ?&nbsp; Veuillez répondre à cet e-mail et les membres de notre équipe vous aideront à coup sûr.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->User->language == 'pt'){
				
					$subject = "Seu pagamento foi declinado";
					$msg = '<p>Prezado '.$result->User->name.',&nbsp;</p><p><br></p><p>O seu recente pagamento para o CongressoGPro foi declinado.</p><p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p><p><br></p><p>VALOR TOTAL A SER PAGO: '.$result->amount.'</p><p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p><p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p><p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p><p>POR FAVOR NOTE: Se o seu pagamento não for feito até 31st August 2023 , a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p><p>Precisa de ajuda enquanto você tenta terminar fazer o pagamento? Por favor responda a este e-mail e membro da nossa equipe vai lhe ajudar com certeza.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Your payment has been declined';
					$msg = '<p>Dear '.$result->User->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p><p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
				
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

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
