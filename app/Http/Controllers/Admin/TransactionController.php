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

				$exb = \App\Models\Exhibitors::where('user_id',$data->user_id)->first();
				if($exb){
					$Exhibitors = 'Exhibitors';
				}else{
					$Exhibitors = '';
				}
				return '<a style="color: blue !important;" href="'.url('admin/user/user-profile/'.$data->user_id).'" target="_blank" title="User Profile">'.\App\Helpers\commonHelper::getUserNameById($data->user_id).'</a><br>'.$Exhibitors;
				
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

				if(\App\Helpers\commonHelper::getTotalPendingAmount($result->User->id)>0) {

					if($result->User->language == 'sp'){

						$subject = "Pago parcial Aprobado.. ¡Gracias!";
						$msg = '<p>Estimado '.$result->User->name.',</p><p><br></p>
						<p>Le escribimos para recordarle que tiene pagos pendientes para saldar el balance adeudado en su cuenta de GProCongress II.</p><p><br></p>
						<p>Aquí tiene un resumen actual del estado de su pago:</p><p><br></p>
						<p>IMPORTE TOTAL A PAGAR: '.$totalAcceptedAmount.'</p>
						<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE: '.$totalAcceptedAmount.'</p>
						<p>PAGOS ACTUALMENTE EN PROCESO: '.$totalAmountInProcess.'</p>
						<p>SALDO PENDIENTE DE PAGO: '.$totalPendingAmount.'</p><p><br></p>
						<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p><p></p>
						<p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p><br></p>
						<p>¿Tiene preguntas? Simplemente responda a este correo electrónico y nuestro equipo estará encantado de comunicarse con usted.</p><p><br></p>
						<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p>
						<p>Atentamente,</p><p><br></p>
						<p>El equipo del GProCongress II</p><div><br></div>';
					
					}elseif($result->User->language == 'fr'){
					
						$subject = "Paiement partiel Approuvé.  Merci !";
						$msg = "<p>Cher ".$result->User->name.",&nbsp;</p><p><br></p>
						<p>Un montant de '.$result->amount.'$ a été approuvé sur votre compte.</p>
						<p>Merci d’avoir effectué ce paiement partiel.&nbsp;</p><p>Voici un résumé de l’état actuel de vos paiements :</p><p><br></p>
						<p>MONTANT TOTAL À PAYER : ".$totalAcceptedAmount."</p>
						<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS : ".$totalAcceptedAmount."</p>
						<p>PAIEMENTS EN COURS DE TRAITEMENT : ".$totalAmountInProcess."</p>
						<p>SOLDE RESTANT DÛ : ".$totalPendingAmount."</p><p><br></p>
						<p style='background-color:yellow; display: inline;'><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p><p></p>
						<p>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31st August 2023, votre inscription sera annulée et votre place sera donnée à quelqu’un d’autre.&nbsp;</p><p><br></p>
						<p>Avez-vous des questions concernant votre paiement ou d’autres problèmes ? Répondez simplement à cet e-mail et notre équipe sera ravi d'entrer en contact avec vous.</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p>&nbsp;L’équipe du GProCongrès II</p>";
			
					}elseif($result->User->language == 'pt'){
					
						$subject = "Aprovado o pagamento parcial. Obrigado";
						$msg = '<p>Prezado'.$result->User->name.',</p><p><br></p>
						<p>Um montante de $'.$result->amount.' foi aprovado na sua conta.</p>
						<p>Obrigado por ter efetuado esse pagamento parcial.</p>
						<p>Aqui está o resumo do estado atual do seu pagamento:</p><p><br></p>
						<p>VALOR TOTAL A SER PAGO: '.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE: '.$totalAcceptedAmount.'</p>
						<p>PAGAMENTO ATUALMENTE EM PROCESSO: '.$totalAmountInProcess.'</p>
						<p>SALDO REMANESCENTE EM DÍVIDA: '.$totalPendingAmount.'</p><p><br></p>
						<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p><p></p>
						<p>POR FAVOR NOTE: Se seu pagamento não for recebido até o dia 31st August 2023, a sua inscrição será cancelada, e a sua vaga será atribuída a outra pessoa.</p><p><br></p>
						<p>Tens perguntas sobre o seu pagamento ou qualquer outra questão? Simplesmente responda a este e-mail, e nossa equipe estará muito feliz para se conectar com você.&nbsp;</p><p><br></p>
						<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.&nbsp;</p><p><br></p>
						<p>Calorosamente,</p>
						<p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Partial payment Approved. Thank you!';
						$msg = '<div>Dear '.$result->User->name.',</div><div><br></div>
						<div>An amount of $'.$result->amount.' has been Approved on your account. &nbsp;</div>
						<div>Thank you for making this partial payment.&nbsp;</div>
						<div>Here is a summary of your current payment status:</div><div><br></div>
						<div>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</div>
						<div>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</div>
						<div>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</div>
						<div>REMAINING BALANCE DUE:'.$totalPendingAmount.'</div><div><br></div>
						<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p><p></p>
						<div>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</div><div><br></div>
						<div>Do you have questions about your payment or any other issues? Simply respond to this email, and our team will be happy to connect with you.&nbsp;</div><div><br></div>
						<div>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</div><div><br></div>
						<div>Warmly,</div><div>&nbsp;The GProCongress II Team</div>';
												
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

						if($resultSpouse->language == 'sp'){

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
					
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);
					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Payment received in full. Thank you!');

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
					$msg = '<p>Dear '.$result->User->name.',&nbsp;</p><p><br></p><p>You recent payment to GProCongress was declined.</p><p>Here is a current summary of your payment status:</p><p><br></p><p>TOTAL AMOUNT TO BE PAID:'.$result->amount.'</p><p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p><p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p><p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
					<p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, and your spot will be given to someone else.</p><p><br></p>
					<p>Do you need assistance while you attempt payment again? Please reply to this email and our team members will help you for sure.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp;</p>';
				
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

	
    public function exhibitorList(Request $request) {
		
        \App\Helpers\commonHelper::setLocale();

        return view('admin.transaction.exhibitor_list');

	}
	
}
