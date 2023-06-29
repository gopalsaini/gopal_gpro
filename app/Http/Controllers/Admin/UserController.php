<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UserController extends Controller {

    public function add(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){

			if((int) $request->post('id') > 0) {

				$rules = [
					'id' => 'numeric|required',
					'first_name' => 'required|string',
					'last_name' => 'required|string',
					'contact_zip_code' => 'required',
					'gender' => 'required|in:1,2',
					'dob' => 'required|date',
					'contact_country_id' => 'required',
					'contact_state_id' => 'required',
					'contact_city_id' => 'required',
				];

				$result=\App\Models\User::find($request->post('id'));

				if($result && $result->designation_id != '4' && $result->designation_id != '6'){

					$rules['ministry_name'] = 'required';
					$rules['ministry_zip_code'] = 'required';
					$rules['ministry_address'] = 'required';
					$rules['ministry_country_id'] = 'required';
					$rules['ministry_state_id'] = 'required';
					$rules['ministry_city_id'] = 'required';
					$rules['language'] = 'required|in:en,sp,fr,pt';
					
					

				}else{
					$rules['marital_status'] = 'required';
					$rules['citizenship'] = 'required';
					
				}


			} else {

				$rules = [
					'id' => 'numeric|required',
					'name' => 'string|required',
					'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->where(function ($query) use($request) {
						return $query->where('id', '!=', $request->id)->where('deleted_at', NULL);
					})],
					'designation_id'=>'required|exists:designations,id'
				];

			}

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

				$password = \Str::random(10);
				
				if ((int) $request->post('id') > 0) {

					$dob=date('Y-m-d',strtotime($request->post('dob')));
					$date1 = $dob;
					$date2 = date('Y-m-d');
					$diff = abs(strtotime($date2) - strtotime($date1));
					$years = floor($diff / (365*60*60*24));
				
					if ($years < 18) {

						return response(array("error"=>true, 'message'=>'Birth year must be more than 18 years'), 403);

					}

					$data=\App\Models\User::find($request->post('id'));

					$data->salutation = $request->post('salutation');

					$data->name = $request->post('first_name');
					$data->last_name = $request->post('last_name');
					$data->gender = $request->post('gender');
					$data->citizenship = $request->post('citizenship');
					$data->dob = $dob;
					
					$data->mobile = $request->post('mobile');
					$data->phone_code = $request->post('user_mobile_code');
					$data->contact_business_codenumber = $request->post('contact_business_codenumber');
					$data->contact_whatsapp_codenumber = $request->post('contact_whatsapp_codenumber');
					$data->contact_business_number = $request->post('contact_business_number');
					$data->contact_whatsapp_number = $request->post('contact_whatsapp_number');
					$data->contact_zip_code = $request->post('contact_zip_code');
					$data->contact_country_id = $request->post('contact_country_id');
					$data->contact_state_id = $request->post('contact_state_id');
					$data->contact_city_id = $request->post('contact_city_id');
					$data->contact_city_name = $request->post('contact_city_name');
					$data->contact_state_name = $request->post('contact_state_name');
					

					if($data && $data->designation_id != '4' && $data->designation_id != '6'){

						$data->ministry_name = $request->post('ministry_name');
						$data->ministry_zip_code = $request->post('ministry_zip_code');
						$data->ministry_address = $request->post('ministry_address');
						$data->ministry_country_id = $request->post('ministry_country_id');
						$data->ministry_state_id = $request->post('ministry_state_id');
						$data->ministry_state_name = $request->post('ministry_state_name');
						$data->ministry_city_id = $request->post('ministry_city_id');
						$data->ministry_city_name = $request->post('ministry_city_name');
						$data->doyouseek_postoralcomment = $request->post('doyouseek_postoral_comment');
						$data->language = $request->post('language');
						
						$dataMin=array(
							'non_formal_trainor'=>$request->post('non_formal_trainor'),
							'formal_theological'=>$request->post('formal_theological'),
							'informal_personal'=>$request->post('informal_personal'),
							'howmany_pastoral'=>$request->post('howmany_pastoral'),
							'howmany_futurepastor'=>$request->post('howmany_futurepastor'), 
							'comment'=>$request->post('comment') ?? '', 
							'willing_to_commit'=>$request->post('willing_to_commit') ?? '', 
						);

						$data->ministry_pastor_trainer_detail = json_encode($dataMin); 
	
					}else{

						if($request->post('marital_status') == 'Unmarried'){
							
							$existSpouse = \App\Models\User::where([
								['parent_id', '=', $request->post('id')],
								['added_as', '=', 'Spouse']
								])->first();
			
							if($existSpouse){
								
								$existSpouse->added_as = null;
								$existSpouse->parent_id = null;
								$existSpouse->save();
				
							}
						}
						

						if($data && $data->stage == '0'){

							$data->profile_status = 'Approved';
							$data->profile_update = 'Approved';
							$data->amount = '0.00';
							$data->stage = '3';
							$data->payment_status = '2';
							$data->marital_status = $request->post('marital_status');
							$data->room = $request->post('room') ?? null;
							$data->profile_submit_type = 'submit';
							$data->status_change_at = date('Y-m-d H:i:s');
							$data->password = \Hash::make($password);

							
							$name = $request->post('first_name').' '.$request->post('last_name');
							$to = $data->email;
	
							if($data->language == 'sp'){
	
								$url = '<a href="'.url('/').'" target="_blank">www.gprocongress.org</a>';
								$link = '<a href="'.url('visa-eligibility-wizard').'" target="_blank">enlace</a>';
	
								$subject = 'Este es su nombre de usuario y contraseña para el GProCongress II.';
								$msg = "<p>Estimado ".$name.",&nbsp;</p><p><br></p>
								<p>Gracias por su participación en el GProCongress II, y por su compromiso con la causa vital de la capacitación de pastores. Nuestro equipo de administración le ha inscrito para asistir al GProCongress II.</p>
								<p>Su nombre de usuario y contraseña son los siguientes:</p>
								<p >&nbsp;&nbsp;&nbsp;<b>Login ID : ".$to."</b></p><p></p>
								<p >&nbsp;&nbsp;&nbsp;<b>Contraseña : ".$password."</b></p><p></p>
								<p>Por favor, utilice estas credenciales para iniciar sesión en ".$url.", y cambie su contraseña después de haber iniciado sesión por primera vez.</p>
								<p>Por favor, también proporcione la información de su pasaporte en nuestro sitio web tan pronto como sea posible (Su nombre completo; País de residencia; Número de pasaporte). También puede utilizar nuestro Asistente de Visa (".$link.") para ver si necesita visa para entrar a Panamá este noviembre.</p>
								<p>Si tiene alguna pregunta, o si necesita hablar con uno de los miembros de nuestro equipo, por favor responda a este correo electrónico.</p>
								<p>Atentamente,</p><p>Equipo del GProCongreso II</p>";
		
							}elseif($data->language == 'fr'){
							
								$url = '<a href="'.url('/').'" target="_blank">www.gprocongress.org</a>';
								$link = '<a href="'.url('visa-eligibility-wizard').'" target="_blank">lien</a>';
	
								$subject = 'Voici votre identifiant et mot de passe GProCongress II.';
								$msg = "<p>Cher ".$name.",&nbsp;</p><p><br></p>
								<p>Merci pour votre participation au GProCongress II, et pour votre engagement pour la cause vitale de la formation des pasteurs.  Vous êtes maintenant inscrit pour assister au GProCongress II par notre équipe d'administration.</p>
								<p>Votre ID de connexion et votre mot de passe sont les suivants :</p>
								<p >&nbsp;&nbsp;&nbsp;<b>ID de connexion : ".$to."</b></p><p></p>
								<p >&nbsp;&nbsp;&nbsp;<b>Mot de passe : ".$password."</b></p><p></p>
								<p>Veuillez utiliser ces informations d'identification pour vous connecter sur ".$url.", et modifier votre mot de passe après vous être connecté pour la première fois.</p>
								<p>Veuillez également fournir les informations de votre passeport sur notre site Web dès que possible (Votre nom complet; Pays de résidence; Passeport #). Vous pouvez également utiliser notre assistant de visa (".$link.") pour voir si vous aurez besoin d'un visa pour entrer au Panama en novembre.</p>
								<p>Si vous avez des questions ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à ce courriel.</p>
								<p>Cordialement,</p><p>L'équipe GProCongress II</p>";
		
							}elseif($data->language == 'pt'){
							
								$url = '<a href="'.url('/').'" target="_blank">www.gprocongress.org</a>';
								$link = '<a href="'.url('visa-eligibility-wizard').'" target="_blank">link</a>';
	
								$subject = 'Aqui estão  os seus dados de  login e senha do GProCongress II';
								$msg = "<p>Caro ".$name.",&nbsp;</p><p><br></p>
								<p>Obrigado por sua participação no GProCongress II e por seu compromisso com a causa vital do treinamento de pastores. Agora que você foi inscrito  para participar do GProCongress II por nossa equipe de administração.</p>
								<p>Seu ID de login e senha são os seguintes:</p>
								<p >&nbsp;&nbsp;&nbsp;<b>Identificação de usuário : ".$to."</b></p><p></p>
								<p >&nbsp;&nbsp;&nbsp;<b>Senha : ".$password."</b></p><p></p>
								<p>Use essas credenciais para fazer login em ".$url.", e altere sua senha depois de fazer login pela primeira vez.</p>
								<p>Por favor, forneça também as informações do seu passaporte em nosso site o mais rápido possível (seu nome completo; país de residência; número do passaporte). Você também pode usar nosso Assistente de Visto (".$link.") para verificar se precisará de visto para entrar no Panamá em Novembro. </p>
								<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.</p>
								<p>Calorosamente,</p><p>Equipe do GProCongress II</p>";
		
							}else{
							
								$url = '<a href="'.url('/').'" target="_blank">www.gprocongress.org</a>';
								$link = '<a href="'.url('visa-eligibility-wizard').'" target="_blank">link</a>';
	
								$subject = 'Here is your GProCongress II login and password';
								$msg = "<p>Dear ".$name.",&nbsp;</p><p><br></p>
								<p>Thank you for your participation in GProCongress II, and for your commitment to the vital cause of pastor training.  You have now been registered to attend GProCongress II by our admin team.   </p>
								<p>Your Login ID and Password are as follows:</p>
								<p >&nbsp;&nbsp;&nbsp;<b>Login ID : ".$to."</b></p><p></p>
								<p >&nbsp;&nbsp;&nbsp;<b>Password : ".$password."</b></p><p></p>
								<p>Please use these credentials to login at ".$url.", and change your password after you have logged in for the first time.</p>
								<p>Please also provide your passport information at our website as soon as possible (Your full name; Country of residence; Passport #). You can also use our Visa Wizard (".$link.") to see if you will need a visa to enter Panama this November.  </p>
								<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
								<p>Warmly,</p><p>GProCongress II Team</p>";
				
							}
		
							\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);
							\App\Helpers\commonHelper::emailSendToUser($data->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($data->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
						
							if($data->language == 'sp'){

								$subject = '¡GProCongress II! Inicie sesión y envíe la información de su pasaporte.';
								$msg = "<p>Estimado ".$name.",&nbsp;</p><p><br></p>
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
		
							}elseif($data->language == 'fr'){
							
								$subject = "GProCongress II ! Veuillez vous connecter et soumettre les informations de votre passeport";
								$msg = "<p>Cher  ".$name.",&nbsp;</p><p><br></p>
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
		
							}elseif($data->language == 'pt'){
							
								$subject = 'GProCongresso II! Faça o login e envie as informações do seu passaporte';
								$msg = "<p>Caro ".$name.",&nbsp;</p><p><br></p>
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
								$msg = "<p>Dear ".$name.",&nbsp;</p><p><br></p>
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

							\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);
							\App\Helpers\commonHelper::emailSendToUser($data->email, $subject, $msg);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($data->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
						
						
						}
						
					}


				} else {

					$data=new \App\Models\User();
					$data->email = $request->post('email');
					$data->name = $request->post('name');
					$data->reg_type = 'email';
					$data->designation_id = $request->post('designation_id');
					$data->parent_id = null;
					$data->password = \Hash::make($password);
					$data->language = $request->post('language');


				}

				$data->save();
				
				if ((int) $request->post('id') == 0) {

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->user_id=$data->id;
					$UserHistory->action_id = \Auth::user()->id;
					$UserHistory->action='Your registration for GProCongress II has started!';
					$UserHistory->save();

					$url = '<a href="'.url('profile-update').'">Click here</a>';
					$faq = '<a href="'.url('faq').'">Click here</a>';

					$to = $request->post('email');

					if($request->post('designation_id') == '4' || $request->post('designation_id') == '6'){
						
						$userUpdate = url('admin/user/edit/'.$data->id);
	
					}else{

						$subject = 'Your registration for GProCongress II has started!';
						$msg = '<div>Dear '.$request->post('name').',</div><div><br></div><div>Based on your discussion with '.\Auth::user()->name.' your registration for the GProCongress II has been initiated. Please use this link '.$url.' to edit and complete your application at any time.<br> Your registered email and password are:</div><div><br>Email: '.$to.'<br>Password: '.$password.'<br></div><div>To find out more about the criteria to attend the Congress, '.$faq.'</div><div><br></div><div>'.$request->post('name').', We are here to help! To talk with one of our team members, simply respond to this email.</div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div><br></div><div>Warmly,</div><div>GProCongress II Team</div>';
						$userUpdate = '';

						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);
						\App\Helpers\commonHelper::sendNotificationAndUserHistory(\Auth::user()->id,$subject,$msg,'GProCongress II registration!  Please login and submit your passport information.');
						
					}

					return response(array('message'=>'User added successfully.', 'userUpdateUrl'=>$userUpdate, 'reset'=>true), 200);

				} else {

					return response(array('message'=>'User updated successfully.', 'reset'=>false), 200);
				}

			}

			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		$designations = \App\Models\Designation::where('slug', '!=', 'admin')->get();

		$country=\App\Models\Country::get();
		
		$result = array();

		
        return view('admin.user.add', compact('result', 'designations','country'));

    }

    public function list(Request $request, $designation) {

        return view('admin.user.list', compact('designation'));
	}

	public function stageAll(Request $request, $type) {
 
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			
			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['id', '!=', '1'],['designation_id', $designation_id],['designation_id', '!=', '14']])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('email', 'like', "%" . request('email') . "%")
						  ->orWhere('name', 'like', "%" . request('email') . "%")
						  ->orWhere('last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['id', '!=', '1'],['designation_id', $designation_id],['designation_id', '!=', '14']])->orderBy('updated_at', 'desc');
			
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
			->setOffset($start)

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

			->addColumn('mobile', function($data){
				if($data->mobile){

					return '+'.$data->phone_code.' '.$data->mobile ?? '-';
				}else{
					return '-';

				}
				
		    })
			
			->addColumn('stage0', function($data){
				if($data->stage == 0){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 0){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}  
			})

			->addColumn('stage1', function($data){
				if($data->stage == 1){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 1){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage2', function($data){
				if($data->stage == 2){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 2){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage3', function($data){
				if($data->stage == 3){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 3){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage4', function($data){
				if($data->stage == 4){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 4){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })

			->addColumn('stage5', function($data){
				if($data->stage == 5){
					return '<div class="span badge rounded-pill pill-badge-secondary">In Process</div>';
				}elseif($data->stage > 5){
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}else{
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} 
		    })
 

			->addColumn('action', function($data){
				$msg = "' Are you sure to delete this user ?'";
				
				if (\Auth::user()->designation_id == '1' ) {

					return '
						<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						<a href="'.route('admin.user.edit', ['id' => $data->id] ).'" title="Edit User" class="btn btn-sm btn-success px-3 m-1 text-white "><i class="fas fa-pencil-alt"></i></a>
					';
				}else{
					
					return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></div>';

				}
			})

			
			->addColumn('reminder', function($data){

				if(\Auth::user()->designation_id == 1){

					if ($data->email_reminder=='1') { 
						$checked = "checked";
					} else {
						$checked = " ";
					}
	
					return '<div class="media-body icon-state switch-outline">
								<label class="switch">
									<input type="checkbox" class="reminderChange" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
								</label>
							</div>';
				}else{
					return '-';
				}
				
		    })

			

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		
        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$stageno = 'all';
        return view('admin.user.stage.stage-all', compact('type', 'setting', 'stageno'));

	}


    public function stageZero(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '0']])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '0']]);
			
			
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
				return $data->name.' '.$data->last_name;
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				if($data->mobile){

					return '+'.$data->phone_code.' '.$data->mobile ?? '-';
				}else{
					return '-';

				}
				
		    })
			->addColumn('profile', function($data){
				if ($data->profile_status == 'Review') {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
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
				
				$msg = "' Are you sure you want to delete this user ??'";

				if (\Auth::user()->designation_id == '11' || \Auth::user()->designation_id == '16') {
					return '<div style="display:flex"><a class="btn btn-sm btn-dark px-3 m-1 text-white sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope"></i></a>
					<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></a>
					</div>';

				}elseif(\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '12'){

					return '<div style="display:flex"><a class="btn btn-sm btn-dark px-3 m-1 text-white sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></a>
						<a href="'.route('admin.user.archiveUserDelete', ['id' => $data->id] ).'" title="user delete" class="btn btn-sm btn-danger px-3 m-1 text-white" onclick="return confirm('.$msg.')"><i class="fas fa-trash"></i></a></a>
						</div>';

				}
			})

			->addColumn('created_at', function($data){
				return date('Y-m-d h:i', strtotime($data->created_at));
		    })

			->addColumn('updated_at', function($data){
				return date('Y-m-d h:i', strtotime($data->updated_at));
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$stageno = '0';
        return view('admin.user.stage.stage-zero', compact('type', 'setting', 'stageno'));

	}

    public function stageOne(Request $request, $type) {
		
		if ($request->ajax()) { 

			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['designation_id', '!=', '14'],['designation_id', $designation_id],['stage', '=', '1'], ['profile_status', $request->input('status')]])
						->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group')
								->orWhere('parent_spouse_stage', '>=', '2');
						})->orderBy('updated_at', 'desc');

			

			if (request()->has('email')) {
				$query->where('users.email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', '!=', '14'],['designation_id', $designation_id],['stage', '=', '1'], ['profile_status', $request->input('status')]])
						->where(function ($query) {
							$query->where('added_as',null)
								->orWhere('added_as', '=', 'Group');
						});

						
			
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
				return $data->name.' '.$data->last_name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('profile', function($data){
				if ($data->profile_update) {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
				}
		    })

			// ->addColumn('status', function($data){
			// 	if($data->status=='1'){ 
			// 		$checked = "checked";
			// 	}else{
			// 		$checked = " ";
			// 	}

			// 	return '<div class="media-body icon-state switch-outline">
			// 				<label class="switch">
			// 					<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
			// 				</label>
			// 			</div>';
		    // })

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

			->addColumn('action', function($data) use($request){

				$Url = '';
				if($request->input('status') == 'ApprovedNotComing'){

					$Url = '<a href="'.route('admin.user.move-stage-1', ['id' => $data->id] ).'" title="Move To Stage 1" class="btn btn-sm btn-success px-3 m-1 text-white" ><i class="fas fa-undo"></i></a>';
					
				}

				if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '11' || \Auth::user()->designation_id == '16' ||  \Auth::user()->designation_id == '12') && $data->profile_status == 'Review' ) {
					
					if (\Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '16') {

						return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';

					}
					
						return '<div style="display:flex">
						<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
						<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Decline" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a>
						<a href="javascript:void(0)" title="Waiting Profile" data-id="'.$data->id.'" data-status="Waiting" class="btn btn-sm btn-warning px-3 m-1 text-white profile-status"><i class="fas fa-pause"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>'.$Url;
				
				} else if ((\Auth::user()->designation_id == '1' ||  \Auth::user()->designation_id == '16' || \Auth::user()->designation_id == '12' || \Auth::user()->designation_id == '11') && $data->profile_status == 'Waiting' ) {
					
					if (\Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '16') {
						
						return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';

					}
					return '<div style="display:flex">
					<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
					<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Decline" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a>
					<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a>'.$Url.'
					</div>';
				
				} else if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '12' || \Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '16') && $data->profile_status == 'Rejected') {
					
					return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
				}else if ((\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '16' ||  \Auth::user()->designation_id == '12') && $data->profile_status == 'ApprovedNotComing' ) {
					
					if (\Auth::user()->designation_id == '11' ||  \Auth::user()->designation_id == '16') {

						return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a></div>';

					}
					
					return '<div style="display:flex">
					<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
					<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Decline" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a>
					<a href="javascript:void(0)" title="Waiting Profile" data-id="'.$data->id.'" data-status="Waiting" class="btn btn-sm btn-warning px-3 m-1 text-white profile-status"><i class="fas fa-pause"></i></a>
					<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a>'.$Url.'
					</div>';
				
				}
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

        \App\Helpers\commonHelper::setLocale();
		$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
		$offers = \App\Models\Offer::get();
		$stageno = 1;
        return view('admin.user.stage.stage-one', compact('type', 'setting', 'offers', 'stageno'));

	}

	public function stageTwo(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '2']])
			->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group')
					->orWhere('parent_spouse_stage', '>=', '2');
			})->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', $designation_id], ['stage', '=', '2']])
			->where(function ($query) {
				$query->where('added_as',null)
					->orWhere('added_as', '=', 'Group');
			});

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$totalData = $totalData1->count();
			
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));  
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('amount', function($data){
				return '$'.number_format($data->amount, 2) ?? '-';
		    })


			->addColumn('amount_in_process', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalAmountInProcess($data->id, true);
		    })
			
			->addColumn('accepted_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalAcceptedAmount($data->id, true);
			})

			->addColumn('rejected_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalRejectedAmount($data->id, true);
		    })

			->addColumn('pending_amount', function($data){
				return '$'.\App\Helpers\commonHelper::getTotalPendingAmount($data->id, true);
		    })

			->addColumn('payment_status', function($data){
				if(\App\Helpers\commonHelper::getTotalPendingAmount($data->id)) {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				} else {
					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
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
				
				if (\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '13') {
					return '<div style="display:flex"><a class="btn btn-sm btn-dark m-1 sendEmail" data-id="'.$data->id.'"><i class="fas fa-envelope" style="color:#fff"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1" ><i class="fas fa-eye" style="color:#fff"></i></a>
						<a href="'.route('admin.user.payment.history', ['id' => $data->id] ).'" title="User payment history" class="btn btn-sm btn-warning m-1"><i class="fas fa-list" style="color:#fff"></i></a></div>
						<a href="#" data-id="'.$data->id.'" title="User Cash payment " class="btn btn-sm btn-warning m-1 cashPayment">Cash Payment</a></div>';
				}
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1 || \Auth::user()->designation_id == 13){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 2;
			return view('admin.user.stage.stage-two', compact('type', 'setting', 'stageno'));

		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}
        

	}

	public function stageThree(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::select('passport_infos.*','users.id as uid')->join('passport_infos','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('users.stage', 3)->where('passport_infos.admin_status', 'Approved')->orderBy('updated_at', 'desc');

			$query->where(function ($query2) {

				$query2->where('passport_infos.visa_granted', 'Yes')
						->orWhere('passport_infos.visa_granted', null);
			});


			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::select('passport_infos.*')->join('passport_infos','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('users.stage', 3)->where('passport_infos.admin_status', 'Approved')->orderBy('updated_at', 'desc');
			
			$totalData1->where(function ($query2) {
				$query2->where('passport_infos.visa_granted', 'Yes')
						->orWhere('passport_infos.visa_granted', null);
			});

			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}
			
			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->salutation.' '.$data->name;
		    })

			->addColumn('passport', function($data){
				return $data->passport_no;
			})

			->addColumn('country', function($data){
				return \App\Helpers\commonHelper::getCountry2NameById($data->country_id);
		    })

			->addColumn('category', function($data) {

				return $data->visa_category;

		    })

			->addColumn('visa_doc', function($data){
				
				if ($data->visa_granted == null) {
					return '-';
					
				} else if ($data->visa_granted == 'Yes') {
					return '<a style="color:blue!important" href="'.asset('uploads/visa_file/'.$data->visa_granted_docs).'" target="_blank" class="text-blue"> File</a>';
				}
				
		    })
			->addColumn('action', function($data){
				
				if (\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == '16') {
					return '<div style="display:flex"><a href="'.route('admin.user.travel.info', ['id' => $data->user_id] ).'" title="User travel info" class="btn btn-sm btn-warning m-1"><i class="fas fa-plane"></i></a>
						<a href="'.route('admin.user.profile', ['id' => $data->user_id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1 || \Auth::user()->designation_id == 12 || \Auth::user()->designation_id == 16){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 3;
			return view('admin.user.stage.stage-three', compact('type', 'setting', 'stageno'));
	
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}


	public function stageFour(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '4']])->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '4']]);
			
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

			->addColumn('day', function($data){
				if (count($data->SessionInfo) > 0) {
					$day = '';
					foreach ($data->SessionInfo as $dayValue) {
						
						$sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
						if($sessionInfo){
							$day .= 'Date :'.$dayValue->day.', ';
							$day .= 'Name :'.$sessionInfo->session_name.', ';
							$day .= 'Session Join :'.$dayValue->session.', ';
							$day .= 'Start Time :'.$sessionInfo->start_time.', ';
							$day .= 'End Time :'.$sessionInfo->end_time;
							$day .= '<br>';
						}
						
					}
					return $day;
				}else {
					return '-';
				}
		    })

			->addColumn('user_status', function($data){
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

			->addColumn('admin_status', function($data){

				if (count($data->SessionInfo) > 0) {

					if ($data->SessionInfo[0]->admin_status == '1') {
						return '<div class="badge rounded-pill pill-badge-success">Approved</div>';
					} else if ($data->SessionInfo[0]->admin_status == '0') {
						return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';
					} else if ($data->SessionInfo[0]->status === null) {
						return '<div style="display:flex"><a data-id="'.$data->id.'" data-type="1" title="Session Info Approve" class="btn btn-sm btn-outline-success m-1 -change">Approve</a>
						<a data-id="'.$data->id.'" data-type="0" title="Session Info Reject" class="btn btn-sm btn-outline-danger m-1 -change">Reject</a></div>';
					}
					
				} else {

					return '<a class="btn btn-sm btn-dark px-3 sendEmail" data-id="'.$data->id.'"><span style="color:#fff">Send Mail</span></a>';
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
				
				if (\Auth::user()->designation_id == '1') {
					return '<a href="'.route('admin.user.session.info', ['id' => $data->id] ).'" title="User session info" class="btn btn-sm btn-warning px-3"><i class="fas fa-list"></i></a><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white" ><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 4;
			return view('admin.user.stage.stage-four', compact('type', 'setting', 'stageno'));
		
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}

	public function stageFive(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			// $order = $columns[$request->input('order.0.column')];
			// $dir = $request->input('order.0.dir');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '5']])->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['designation_id', $designation_id], ['stage', '=', '5']]);
			
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

			->addColumn('travel_info', function($data){
				if ($data->TravelInfo) {
					if ($data->TravelInfo->user_status == '1') {
						return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
					} else if ($data->TravelInfo->user_status == '0') {
						return '<div class="span badge rounded-pill pill-badge-danger">Reject</div>';
					} else if ($data->TravelInfo->user_status === null) {
						return '<div class="span badge rounded-pill pill-badge-warning">In Process</div>';
					}
				}else {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
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

				if (\Auth::user()->designation_id == '1') {
					return '<a href="'.route('admin.user.details', ['id' => $data->id] ).'" title="View user details" class="btn btn-sm btn-primary px-3" ><i class="fas fa-eye"></i></a>';
				}
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

		if(\Auth::user()->designation_id == 1){

			\App\Helpers\commonHelper::setLocale();
			$setting = \App\Models\Designation::With('StageSetting')->where('slug', $type)->first();
			$stageno = 5;
			return view('admin.user.stage.stage-five', compact('type', 'setting', 'stageno'));
			
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);
		}

	}

    public function edit(Request $request,$id) {
		
		$result = \App\Models\User::find($id);

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();
		$designations = \App\Models\Designation::where('slug', '!=', 'admin')->get();
		$country  = \App\Models\Country::get();
		

		
		$SpouseDetails =\App\Models\User::where('parent_id',$id)->where('added_as','Spouse')->first();
		if(!$SpouseDetails){
			$SpouseDetails = [];
		}

		return view('admin.user.edit', compact('result', 'designations','country','SpouseDetails'));

	}

    public function delete(Request $request, $id) {

		
		$result = \App\Models\User::find($id);
		
		if ($result) {

			\App\Models\User::where('id', $id)->forceDelete();
			\App\Models\User::where('parent_id', $id)->forceDelete();
			
			$request->session()->flash('5fernsadminsuccess','User deleted successfully.');
		} else {
			$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
		}
		
		return redirect()->back();

    }

	public function status(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {
			$result->status = $request->post('status');
			$result->status_change_at = date('Y-m-d H:i:s');
			$result->amount = $request->post('amount');
			$result->payment_status = 0;

			$to = $result->email;
			if ((int)$request->post('status') === 1) {
				$result->stage = 2;

				
				$subject = 'Profile Approved';
				$msg = 'Your profile has been approved successfully, please pay this amount '.$request->post('amount').' and your offer code is '.$request->post('offer_code');
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);

				$resultSpouse = \App\Models\User::where('parent_id',$request->post('id'))->get();
				if(!empty($resultSpouse) && count($resultSpouse) >0){

					foreach($resultSpouse as $val){

						$resultSpouseFirst = \App\Models\User::find($val->id);

						$resultSpouseFirst->status = $request->post('status');
						$resultSpouseFirst->status_change_at = date('Y-m-d H:i:s');
						$resultSpouseFirst->amount = $request->post('amount');
						$resultSpouseFirst->payment_status = 0;

						$to = $resultSpouseFirst->email;

						$resultSpouseFirst->stage = 2;
						$resultSpouseFirst->save();

						$subject = 'Profile Approved';
						$msg = 'Your profile has been approved successfully, please pay this amount '.$request->post('amount').' and your offer code is '.$request->post('offer_code');
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($resultSpouseFirst->mobile);
					}
					
				}


			} else if ((int)$request->post('status') === 0) {
				$subject = 'Profile Rejected';
				$msg = 'Your profile has been rejected';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);
			}

			$result->save();

			return response(array('message'=>'User status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function reminderStatus(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {

			$result->email_reminder = $request->post('status');
			$result->save();

			return response(array('message'=>'Email Reminder status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendProfileUpdateReminder(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {

			$url = '<a href="'.url('profile-update').'" target="_blank">Click here</a>';
			
			$to = $result->email;
			$name= $result->name.' '.$result->last_name;
			$subject = 'Friendly reminder: Your GProCongress II application needs to be completed';
			$msg = '<div>Dear '.$name.',</div><div><br></div><div>We have not received your completed application to attend the GProCongress II. Please use this link '.$url.' to edit and complete your application at any time. We recommend timely completion to secure a spot.</div><div><br></div><div>As always, to talk with one of our team members, simply respond to this email.</div><div><br></div><div>Pray with us toward multiplying the quantity and quality of trainers of pastors.</div><div>Warmly,</div><div>GProCongress II Team</div>';
			
			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Profile update reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendPaymentReminder(Request $request) {
		
		$result = \App\Models\User::find($request->post('id'));

		if ($result) {

			$name = $result->name.' '.$result->last_name;

			\App\Helpers\commonHelper::sendPaymentReminderMailSend($request->post('id'),$result->email,$name);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Payment pending reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function stageSetting(Request $request) {
			
		if($request->ajax() && $request->isMethod('post')){
			
			$rules = [
				'id' => 'numeric|required',
				'stage_zero'=>'required|in:0,1',
				'stage_one'=>'required|in:0,1',
				'stage_two'=>'required|in:0,1',
				'stage_three'=>'required|in:0,1',
				'stage_four'=>'required|in:0,1',
				'stage_five'=>'required|in:0,1',
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

                $data=\App\Models\StageSetting::find($request->post('id'));

				if (!$data) {
					return response(array('message'=>'Data not found.'),403);
				}
                
                $data->stage_zero = $request->post('stage_zero');
                $data->stage_one = $request->post('stage_one');
                $data->stage_two = $request->post('stage_two');
                $data->stage_three = $request->post('stage_three');
                $data->stage_four = $request->post('stage_four');
                $data->stage_five = $request->post('stage_five');
                $data->save();
                
            	return response(array('message'=>'Stage updated successfully.'),200);

			}
			return response(array('message'=>'Data not found.'),403);
		}
		
        \App\Helpers\commonHelper::setLocale();
		
		$results = \App\Models\StageSetting::With('Designation')->get();
        return view('admin.user.stage-setting', compact('results'));

    }

	public function userProfile(Request $request, $id) {

		$result = \App\Models\User::with('TravelInfo')->where([['id', '=', $id]])->first();
		
		if (!$result) {

			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.profile', compact('id', 'result'));

	}

	public function travelInfo(Request $request, $id) {

		
		$result = \App\Models\User::with('TravelInfo')->where([['id', '=', $id]])->first();
		
		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.travel-info', compact('id', 'result'));

	}

	public function sessionInfo(Request $request, $id) {

		$result = \App\Models\User::with('SessionInfo')->where([['id', '=', $id]])->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.session-info', compact('id', 'result'));

	}

	public function paymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
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

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function sponsoredPaymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '2')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '2')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('created_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->created_at));
		    })

			->addColumn('user_name', function($data){
				return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		    })
			
			->addColumn('transaction', function($data){
				return $data->transaction_id;
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
				return $data->amount;
		    })

			->addColumn('payment_status', function($data){

				if($data->status == '0'){

					return "Pending";

				}elseif($data->status == '1'){

					return "Accepted";
					
				}else{
					return "decline";
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

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function donatePaymentHistory(Request $request, $id) {
		
		if ($request->ajax()) {
			
			
			$columns = \Schema::getColumnListing('transactions');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$order = $columns[$request->input('order.0.column')];
			$dir = $request->input('order.0.dir');

			$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '3')->orderBy($order,$dir);

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '3')->count();
			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('created_at', function($data){
				return date('d-M-Y H:i:s',strtotime($data->created_at));
		    })

			->addColumn('user_name', function($data){
				return \App\Helpers\commonHelper::getDataById('User', $data->user_id, 'name');
		    })
			
			->addColumn('transaction', function($data){
				return $data->transaction_id;
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
				return $data->amount;
		    })

			->addColumn('payment_status', function($data){

				if($data->status == '0'){

					return "Pending";

				}elseif($data->status == '1'){

					return "Accepted";
					
				}else{
					return "decline";
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

        \App\Helpers\commonHelper::setLocale();

        return view('admin.user.payment-history', compact('id'));

	}

	public function travelInfoStatus(Request $request) {
		
		$result = \App\Models\TravelInfo::find($request->post('id'));

		if ($result) {
			$result->admin_status = $request->post('status');
			$result->save();

			$user = \App\Models\User::with('TravelInfo')->find($result->user_id);
			
			$pdfData = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$result->user_id)->first();

			$to = $user->email;
			$pdf = \PDF::loadView('email_templates.travel_info', $pdfData->toArray());

			if ((int)$request->post('status') === 1) {

				
				$user->stage = 4;
				$user->save();

				$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
			
				if($resultSpouse){

					$resultSpouse->stage = 4;
					$resultSpouse->save();

				}

				$subject = 'Travel Info Approved';
				$msg = 'Your travel info has been approved successfully';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
			
				// \App\Helpers\commonHelper::sendSMS($user->mobile);

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Travel Info Approved',\Auth::user()->id);
				
				$subject = 'Session information ';
				$msg = 'Your Travel Information has been approved successfully, Please session information can be updated now';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Session information',\Auth::user()->id);
				

			} else if ((int)$request->post('status') === 0) {

				$subject = 'Travel Info Rejected';
				$msg = 'Your travel info has been rejected';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
				// \App\Helpers\commonHelper::sendSMS($user->mobile);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

			}

			return response(array('message'=>'Travel info status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendTravelInfoReminder(Request $request) {
		
		$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$request->post('id'))->first();
		
		if ($result) {
			$to = $result->email;
			
			if($result->language == 'sp'){

				$subject = "Por favor, envíe su información de viaje.";
				$msg = '<p>Dear '.$result->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp; &nbsp;&nbsp;</p>';
			
			}elseif($result->language == 'fr'){
			
				$subject = "Veuillez soumettre vos informations de voyage.";
				$msg = "<p>Cher '.$result->name.',&nbsp;</p><p>Nous sommes ravis de vous voir au GProCongrès à Panama City, au Panama !</p><p><br></p><p>Pour aider les délégués à obtenir des visas, nous leur demandons de nous soumettre leurs informations de voyage.&nbsp;</p><p><br></p><p>Veuillez répondre à cet e-mail avec vos informations de vol.&nbsp; Dès réception, nous vous enverrons un e-mail pour confirmer que les informations que nous avons reçues sont correctes.&nbsp;</p><p><br></p><p>Cordialement,</p><p>L’équipe du GProCongrès II</p>";
	
			}elseif($result->language == 'pt'){
			
				$subject = "Por favor submeta sua informação de viagem";
				$msg = '<p>Prezado '.$result->name.',&nbsp;</p><p><br></p><p>Nós estamos emocionados em ver você no CongressoGPro na Cidade de Panamá, Panamá!</p><p><br></p><p>Para ajudar os delegados na obtenção de vistos, nós estamos pedindo que submetam a nós sua informação de viagem.&nbsp;</p><p><br></p><p>Por favor responda este e-mail com informações do seu voo. Depois de recebermos, iremos lhe enviar um e-mail confirmando que a informação que recebemos é correta.&nbsp;</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro&nbsp; &nbsp; &nbsp;&nbsp;</p>';
			
			}else{
			
				$subject = 'Please submit your travel information.';
				$msg = '<p>Dear '.$result->name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
									
			}
			// echo "<pre>"; print_r($result->toArray());die;
			
			$pdf = \PDF::loadView('email_templates.travel_info', $result->toArray());
			$pdf->setPaper('L');
			$pdf->output();
			$canvas = $pdf->getDomPDF()->getCanvas();
			
			$height = $canvas->get_height();
			$width = $canvas->get_width();
			$canvas->set_opacity(.2,"Multiply");
			$canvas->page_text($width/5, $height/2, 'Draft', null,
			70, array(0,0,0),2,2,-30);

			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Travel info reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sessionInfoStatus(Request $request) {
		
		$result = \App\Models\SessionInfo::where('user_id', $request->post('id'))->get();

		if (count($result) > 0) {

			\App\Models\SessionInfo::where('user_id', $request->post('id'))->update(['admin_status' => $request->post('status'),'user_status' => $request->post('status')]);
			$user = \App\Models\User::find($request->post('id'));
			$to = $user->email;
			if ((int)$request->post('status') === 1) {
				
				
				$user->stage = 5;
				$user->qrcode = \QrCode::size(300)->generate(url('/'));
				$user->save();

				$subject = 'Session Info Approved';
				$msg = 'Your session info has been approved successfully';
				$msg = '<br>';

				$data = \App\Models\User::with('SessionInfo')->where([['stage', '=', '5'], ['id', '=', $request->post('id')]])->first();
				
				foreach ($data->SessionInfo as $dayValue) {
					
					$sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
					if($sessionInfo){
						$msg .= 'Date :'.$dayValue->day.', ';
						$msg .= 'Name :'.$sessionInfo->session_name.', ';
						$msg .= 'Session Join :'.$dayValue->session.', ';
						$msg .= 'Start Time :'.$sessionInfo->start_time.', ';
						$msg .= 'End Time :'.$sessionInfo->end_time;
						$msg .= '<br>';
					}
					
				}
				
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($data->id,$msg,$subject);
				// \App\Helpers\commonHelper::sendSMS($user->mobile);
			} else if ((int)$request->post('status') === 0) {

				$subject = 'Session Info Decline';
				$msg = 'Your session info has been decline';
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($user->mobile);
			}

			return response(array('message'=>'Session info status changed successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function sendSessionInfoReminder(Request $request) {
		
		$result = \App\Models\User::with('SessionInfo')->find($request->post('id'));

		if ($result) {
			$to = $result->email;
			$subject = 'Verify Session Info';
			$msg = 'Please verify your session information';

			\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
			\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

			// \App\Helpers\commonHelper::sendSMS($result->mobile);

			return response(array('message'=>'Session info reminder has been sent successfully.'), 200);
		} else {
			return response(array('message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function userDetails(Request $request, $id) {
		
		$result = \App\Models\User::with('TravelInfo')->with('SessionInfo')->where([['id', '=', $id], ['stage', '=', '5']])->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        \App\Helpers\commonHelper::setLocale();

		return view('admin.user.user-details', compact('id', 'result'));

	}
	
	public function groupUsersList(Request $request) {

		if ($request->ajax()) {

			$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
			$results = \App\Models\User::where([['parent_id', $id]])->get();

			$html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"> <thead> <tr> <th class="text-center">'. \Lang::get('admin.id') .'</th> <th class="text-center">'. \Lang::get('admin.addedas') .'</th> <th class="text-center">'. \Lang::get('admin.user') .'</th> <th class="text-center">'. \Lang::get('admin.stage') .' 0 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 1 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 2 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 3 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 4 </th> <th class="text-center">'. \Lang::get('admin.stage') .' 5 </th> <th class="text-center">'. \Lang::get('admin.action') .'</th> </tr> </thead><tbody>';
			
			if (count($results) > 0) {
				foreach ($results as $key=>$result) {

					$spouse = \App\Models\User::where([['parent_id', $result->id]])->first();

					$key += 1;
					$html .= '<tr>';
					$html .= '<td class="text-center">'.$key.'.</td>';

					$html .= '<td class="text-center">'.$result->added_as;
					$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->added_as.'</p>' : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->email;
					$html .= $spouse ? '<hr><p class="text-danger">'.$spouse->email.'</p>' : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('0', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('0', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('1', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('1', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('2', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('2', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('3', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('3', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('4', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('4', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getStateStatus('5', $result->stage);
					$html .= $spouse ? '<hr>'.\App\Helpers\commonHelper::getStateStatus('5', $spouse->stage) : '';
					$html .= '</td>';

					$html .= '<td class="text-center"><a href="'.route('admin.user.profile', ['id' => $result->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>';
					$html .= $spouse ? '<hr><a href="'.route('admin.user.profile', ['id' => $spouse->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>' : '';
					$html .= '</td>';

					$html .= '</tr>';
				}
			} else {
				$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
			}
			$html .= '</tbody></table>';

			return response()->json(array('html'=>$html));
			
        }

	}
	

	public function groupUsersListEdit(Request $request) {

		if ($request->ajax()) {

			$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
			$results = \App\Models\User::where([['parent_id', $id],['added_as', 'Group']])->get();

			$html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"> <thead> <tr> <th class="text-center">'. \Lang::get('admin.id') .'</th> <th class="text-center">'. \Lang::get('admin.addedas') .'</th> <th class="text-center">Name</th>  <th class="text-center">'. \Lang::get('admin.action') .'</th> </tr> </thead><tbody>';
			
			if (count($results) > 0) {
				foreach ($results as $key=>$result) {
					
					$key += 1;
					$html .= '<tr>';
					$html .= '<td class="text-center">'.$key.'.</td>';

					$html .= '<td class="text-center">'.$result->added_as;
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->name.' '.$result->last_name;
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->email;
					$html .= '</td>';

					$html .= '<td class="text-center"><a href="'.route('admin.user.profile', ['id' => $result->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>';
					$html .= '</td>';

					$html .= '</tr>';
				}
			} else {
				$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
			}
			$html .= '</tbody></table>';

			return response()->json(array('html'=>$html));
			
        }

	}
	
	public function ProfileApproved(Request $request, $id) {
		
		$result = \App\Models\User::where('id', $id)->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        $result->profile_update = '1';
        $result->profile_updated_at = date('Y-m-d');
        $result->save();

		$request->session()->flash('error','Profile Approved Successfully.');
		return redirect()->back();

	}
	
	public function userProfileMoveToStage1(Request $request, $id) {
		
		$result = \App\Models\User::where('id', $id)->first();

		if (!$result) {
			$request->session()->flash('5fernsadminerror','Something went wrong.please try again.');
			return redirect()->back();
		}

		$userSpouse = \App\Models\User::where('parent_id',$result->id)->where('added_as','Spouse')->first();

		if($userSpouse){

			$userSpouse->profile_status='Review';
			$userSpouse->stage= '1';
			$userSpouse->amount= '0';
			$userSpouse->save();

			\App\Helpers\commonHelper::sendNotificationAndUserHistory($userSpouse->id,'Move user from ApproveNOTComing to Stage-1','Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1');

		}

        $result->profile_status = 'Review';
        $result->stage = '1';
		$result->amount= '0';
        $result->save();

		\App\Helpers\commonHelper::sendNotificationAndUserHistory($result->id,'User Move to Stage-1 by admin','User Move to Stage-1  by admin','User Move to Stage-1 by admin');

		$request->session()->flash('5fernsadminsuccess','User Move to Stage-1 Successfully.');
		return redirect()->back();

	}

	public function profileReject(Request $request, $id) {
		
		$result = \App\Models\User::where('id', $id)->first();

		if (!$result) {
			$request->session()->flash('error','Something went wrong.please try again.');
			return redirect()->back();
		}

        $result->profile_update = '2';
        $result->profile_updated_at = date('Y-m-d');
        $result->save();

		$request->session()->flash('error','Profile Reject Successfully.');
		return redirect()->back();

	}

	public function profileStatus(Request $request) {
		
		$result = \App\Models\User::find($request->post('user_id'));
		
		if ($result) {

			$result->profile_status = $request->post('status');
			$result->remark = $request->post('remark');
			$to = $result->email;

			if ($request->post('status') == 'Approved') {

				$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$result->id)->first();
				
				if($resultSpouse){
					
					if($resultSpouse->profile_status == 'Review'){

						if($request->post('room_type') == 'Yes' && $request->post('category') != ''){
							$result->room = $request->post('category');
						}

						$result->change_room_type = $request->post('room_type');
						$result->upgrade_category = $request->post('category');
						$result->early_bird = $request->post('early_bird');
						$result->offer_id = $request->post('offer_id');
						$result->amount = $request->post('amount');
						$result->payment_country = $request->post('citizenship');
						$result->cash_payment_option = $request->post('cash_payment');
						$result->role_id = $request->post('role_id');
						$result->status_change_at = date('Y-m-d H:i:s');
						$result->stage = '2';
						$result->profile_update = '1';
						$name= $result->name.' '.$result->last_name;

						if($request->post('amount')>0){

							$android = '<a href="https://play.google.com/store/apps/details?id=org.gprocommision">Google Play</a>';
							$ios = '<a href="https://apps.apple.com/us/app/gpro-commission/id1664828059">App Store</a>';
							$website = '<a href="https://www.gprocongress.org/payment">website</a>';
		
							if($result->language == 'sp'){

								$website = '<a href="https://www.gprocongress.org/payment">sitio web</a>';

								$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
								$msg = '<p>Estimado '.$name.'</p><p><br></p><p>
									<br></p><p>¡Felicidades! Su aplicación para el GPorCongress II ha sido aprobada. Dios mediante esperamos verle en Panamá ciudad de Panamá, del 12 al 17 de noviembre del 2023.</p><p><br></p><p>
									<br></p><p>¡Inscríbase en nuestra aplicación GProCongress! Querrá tener acceso a toda la información sobre el Congreso, y ahí es donde entra el app. Puede recibir notificaciones, completar su inscripción e incluso pagar en la aplicación. ¡Simplemente vaya a '.$ios.' o a '.$android.' y ¡descárguelo hoy!</p><p><br></p><p>
									<br></p><p>Su pago por el Congreso vence ahora, y puede hacerse en cualquier momento. Siga las instrucciones que se detallan a continuación para realizar su pago.</p><p><br></p><p>Puede realizar los pagos en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.' usando cualquiera de los distintos métodos de pago:</p><p>
									<br></p><p>1. Pago en línea con tarjeta de crédito: puede pagar su inscripción con cualquiera de las tarjetas de crédito más importantes.</p><p><br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
									<br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
									<br></p><p>3. Western Union – Puedes pagar su inscripción a través de Western Union en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.'. Envíe sus fondos a David Brugger, Dallas, Texas, EE.UU.  Junto con los fondos, envíe la siguiente información: </p><p>&nbsp;&nbsp;&nbsp;(1) su nombre completo, </p><p>&nbsp;&nbsp;&nbsp;(2) el país desde el que realiza el envío, </p><p>&nbsp;&nbsp;&nbsp;(3) la cantidad enviada en USD, y </p><p>&nbsp;&nbsp;&nbsp;(4) el código que Western Union le haya dado.</p><p>
									<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
									<br></p><p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p>
									<br></p><p>Si tiene alguna pregunta sobre GProCongress II, responda a este correo electrónico para conectarse con uno de los miembros de nuestro equipo. ¡Le damos la bienvenida con alegría al GProCongress II, y estamos a la expectativa de todo lo que Dios va a hacer en y a través de nosotros para desarrollaruna comunidad, explorar oportunidades, descubrir recursos e brindarse ánimo mutuo con capacitadores de pastores en todo el mundo!</p><p>
									<br></p><p>Atentamente</p><p><br></p><p>Equipo GProCongress II </p>';
							
							}elseif($result->language == 'fr'){

								$website = '<a href="https://www.gprocongress.org/payment">site Web</a>';
		
								$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
								$msg = '<p>Cher '.$name.',&nbsp;</p><p>
								<br></p><p>Félicitations! Votre candidature pour GProCongress II a été approuvée ! Nous sommes impatients de vous voir à Panama City, au Panama, du 12 au 17 novembre 2023, si le Seigneur le veut.</p><p>
								<br></p><p>Inscrivez-vous à notre application GProCongress ! Vous voudrez avoir accès à toutes les informations sur le Congrès, et c’est là que l’application entre en jeu. Vous pouvez recevoir des notifications, terminer votre inscription et même payer vos frais d’inscription sur l’application. Il suffit d’aller sur l’'.$ios.' ou sur '.$android.' et de la télécharger dès aujourd’hui!</p><p>
								<br></p><p>Votre paiement pour le Congrès est maintenant dû et peut être effectué à tout moment. Veuillez suivre les instructions ci-dessous pour effectuer votre paiement.</p><p>
								<br></p><p>Vous pouvez payer vos frais sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.' en utilisant l’un des différents modes de paiement:</p><p>
								<br></p><p>1. Paiement en ligne par carte de crédit – vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</p><p>
								<br></p><p>2. Virement bancaire – vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org . Vous recevrez des instructions par réponse de l’e-mail.</p><p>
								<br></p><p>3. Western Union – vous pouvez payer vos frais par Western Union en allant sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.'. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes : </p><p>&nbsp;&nbsp;&nbsp;(1) votre nom complet, </p><p>&nbsp;&nbsp;&nbsp;(2) le pays à partir duquel vous envoyez, </p><p>&nbsp;&nbsp;&nbsp;(3) le montant envoyé en USD et </p><p>&nbsp;&nbsp;&nbsp;(4) le code qui vous a été donné par Western Union.</p><p>
								<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
								<br></p><p>VEUILLEZ NOTER: Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</p><p>
								<br></p><p>Si vous avez des questions concernant GProCongress II, veuillez répondre à cet e-mail pour communiquer avec l’un des membres de notre équipe. Nous vous accueillons avec joie au GProCongress II, et nous attendons avec impatience tout ce que Dieu va faire en nous et à travers nous pour construire une communauté, explorer les opportunités, découvrir des ressources et échanger des encouragements avec les formateurs de pasteurs du monde entier!</p><p>
								<br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
							
							}elseif($result->language == 'pt'){

								$website = '<a href="https://www.gprocongress.org/payment">site</a>';
		
								$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
								$msg = '<p>Prezado '.$name.',</p><p>
								<br></p><p>Parabéns! A sua inscrição para o GProCongress II foi aprovada! Esperamos vê-lo na Cidade do Panamá, Panamá, de 12 a 17 de novembro de 2023, se o Senhor permitir.</p><p>
								<br></p><p>Por favor, inscreva-se no nosso aplicativo GProCongresso! Você vai querer ter acesso a todas as informações sobre o Congresso, e é aí que entra o app. Você pode receber notificações, fazer sua inscrição e até pagar sua inscrição no app. Basta ir na '.$ios.' ou ao '.$android.' e fazer o download hoje mesmo!</p><p>
								<br></p><p>O pagamento do Congresso está vencido e pode ser feito a qualquer momento. Siga as instruções listadas abaixo para efetuar o pagamento.</p><p>
								<br></p><p>Você pode pagar suas taxas em nosso '.$website.' ou em nosso aplicativo '.$ios.' ou ao '.$android.' usando qualquer um dos vários métodos de pagamento:</p><p>
								<br></p><p>1. Pagamento online usando cartão de crédito – você pode pagar suas taxas usando qualquer cartão de crédito.</p><p>
								<br></p><p>2. Transferência bancária – você pode pagar por transferência bancária do seu banco. Se você quiser fazer uma transferência eletrônica, envie um e-mail para david@rreach.org. Você receberá instruções por e-mail de resposta.</p><p>
								<br></p><p>3. Western Union – você pode pagar suas taxas via Western Union acessando nosso '.$website.' ou nosso aplicativo '.$ios.' ou ao '.$android.'. Por favor, envie seus fundos para David Brugger, Dallas, Texas, EUA. Juntamente com seus recursos, envie as seguintes informações: </p><p>&nbsp;&nbsp;&nbsp;(1) seu nome completo, </p><p>&nbsp;&nbsp;&nbsp;(2) o país de onde você está enviando, </p><p>&nbsp;&nbsp;&nbsp;(3) o valor enviado em USD e </p><p>&nbsp;&nbsp;&nbsp;(4) o código fornecido a você pela Western Union.</p><p>
								<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
								<br></p><p>ATENÇÃO: Se o pagamento integral não for recebido até 31st August 2023, sua inscrição será cancelada, sua vaga será cedida a outra pessoa e quaisquer valores pagos anteriormente por você serão perdidos.</p><p>
								<br></p><p>Se você tiver alguma dúvida sobre o GProCongress II, responda a este e-mail para entrar em contato com um dos membros de nossa equipe. Damos as boas-vindas ao GProCongress II com alegria e esperamos tudo o que Deus fará em nós e através de nós para construir uma comunidade, explorar oportunidades, descobrir recursos e trocar encorajamento com treinadores de pastores em todo o mundo!</p><p>
								<br></p><p>Calorosamente,</p><p>
								<br></p><p>Equipe do II CongressoGPro</p>';
							
							}else{
							
								$subject = 'Congratulations, '.$name.', your application has been approved!';
								$msg = '<p>Dear '.$name.',</p><p><br></p>
								<p>Congratulations!  Your application for GProCongress II has been approved!  We look forward to seeing you in Panama City, Panama on November 12-17, 2023, the Lord willing.</p>
								<p><br></p><p>Please sign up for our GProCongress app!  You will want to have access to all information about the Congress, and that’s where the app comes in.  You can receive notifications, complete your registration, and even pay your registration fees on the app.  Just go to the '.$ios.' or to '.$android.' and download it today!</p><p>
								<br></p><p>Your payment for the Congress is now due, and can be made anytime.  Please follow the instructions listed below to make your payment.</p><p>
								<br></p><p>You may pay your fees on our '.$website.' or on our app '.$ios.' or to '.$android.' using any of several payment methods:</p><p><br>
								</p><p>1. Online payment using credit card – you can pay your fees using any major credit card.</p><p><br>
								</p><p>2. Bank transfer – you can pay via wire transfer from your bank. If you want to make a wire transfer, please email david@rreach.org. You will receive instructions via reply email.</p><p><br>
								</p><p>3. Western Union – you can pay your fees via Western Union by going to our '.$website.', or to our app '.$ios.' or to '.$android.'. Please send your funds to David Brugger, Dallas, Texas, USA.  Along with your funds, please submit the following information: </p><p>&nbsp;&nbsp;&nbsp;(1) your full name, </p><p>&nbsp;&nbsp;&nbsp;(2) the country you are sending from, </p><p>&nbsp;&nbsp;&nbsp;(3) the amount sent in USD, and </p><p>&nbsp;&nbsp;&nbsp;(4) the code given to you by Western Union. </p><p><br>
								<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
								</p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited. </p><p><br>
								</p><p>If you have any questions about GProCongress II, please reply to this email to connect with one of our team members. We welcome you with joy to GProCongress II, and we look forward to all that God is going to do in and through us to build community, explore opportunities, discover resources, and exchange encouragement with trainers of pastors worldwide! </p><p>
								<br></p><p>Warmly,</p><p>
								<br></p><p>The GProCongress II Team</p>';	
							
							}

							\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
							\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

							$resultSpouse->profile_status = $request->post('status');
							$resultSpouse->stage = 2;
							$resultSpouse->save();

						}else{

							$result->stage = '3';

							$resultSpouse->profile_status = $request->post('status');
							$resultSpouse->stage = 3;
							$resultSpouse->save();

						}
						
						$name= $resultSpouse->name.' '.$resultSpouse->last_name;
						$android = '<a href="https://play.google.com/store/apps/details?id=org.gprocommision">Google Play</a>';
						$ios = '<a href="https://apps.apple.com/us/app/gpro-commission/id1664828059">App Store</a>';
	
						if($resultSpouse->language == 'sp'){

							$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p>
									<p>¡Nos da gran alegría confirmar que su solicitud para asistir al GProCongress II ha sido aceptada! Esperamos verle en Ciudad de Panamá en noviembre de 2023, Dios mediante.</p><p><br></p><p><br></p>
									<p>¡Inscríbase en nuestra aplicación GProCongress! Querrá tener acceso a toda la información sobre el Congreso, y ahí es donde entra el app. Puede recibir notificaciones, completar su inscripción e incluso pagar en la aplicación. ¡Simplemente vaya a '.$ios.' o a '.$android.' y ¡descárguelo hoy!</p><p><br></p><p><br></p>
									<p>Si tiene alguna pregunta sobre GProCongress II, responda a este correo electrónico para conectarse con uno de los miembros de nuestro equipo. ¡Le damos la bienvenida con alegría al GProCongress II, y estamos a la expectativa de todo lo que Dios va a hacer en y a través de nosotros para desarrollaruna comunidad, explorar oportunidades, descubrir recursos e brindarse ánimo mutuo con capacitadores de pastores en todo el mundo!</p><p><br></p>
									<p>Atentamente,</p><p><br></p>
									<p>El equipo del GProCongress II</p>';
						
						}elseif($resultSpouse->language == 'fr'){
						
							$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p>
									<p>C’est avec une grande joie que nous confirmons l’acceptation de votre candidature pour assister au GProCongrès II ! Nous avons hâte de vous voir à Panama City en novembre 2023, si le Seigneur le veut.&nbsp;</p><p><br></p>
									<p>Inscrivez-vous à notre application GProCongress ! Vous voudrez avoir accès à toutes les informations sur le Congrès, et c’est là que l’application entre en jeu. Vous pouvez recevoir des notifications, terminer votre inscription et même payer vos frais d’inscription sur l’application. Il suffit d’aller sur l’'.$ios.' ou sur '.$android.' et de la télécharger dès aujourd’hui!</p><p><br></p>
									<p>Si vous avez des questions concernant GProCongress II, veuillez répondre à cet e-mail pour communiquer avec l’un des membres de notre équipe. Nous vous accueillons avec joie au GProCongress II, et nous attendons avec impatience tout ce que Dieu va faire en nous et à travers nous pour construire une communauté, explorer les opportunités, découvrir des ressources et échanger des encouragements avec les formateurs de pasteurs du monde entier!</p><p><br></p>
									<p>Cordialement,</p><p><br></p>
									<p>L’équipe GProCongrès II</p>';
						
						}elseif($resultSpouse->language == 'pt'){
						
							$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
							$msg = '<p>Prezado '.$name.',</p><p><br></p>
									<p>É para nós um grande prazer confirmar a aceitação do seu pedido de participar no II CongressoGPro. Nós esperamos lhe ver na Cidade de Panamá em Novembro de 2023, se o Senhor permitir.</p><p><br></p>
									<p>Por favor, inscreva-se no nosso aplicativo GProCongresso! Você vai querer ter acesso a todas as informações sobre o Congresso, e é aí que entra o app. Você pode receber notificações, fazer sua inscrição e até pagar sua inscrição no app. Basta ir na '.$ios.' ou ao '.$android.' e fazer o download hoje mesmo!</p><p><br></p>
									<p>Se você tiver alguma dúvida sobre o GProCongress II, responda a este e-mail para entrar em contato com um dos membros de nossa equipe. Damos as boas-vindas ao GProCongress II com alegria e esperamos tudo o que Deus fará em nós e através de nós para construir uma comunidade, explorar oportunidades, descobrir recursos e trocar encorajamento com treinadores de pastores em todo o mundo!</p><p><br></p>
									<p>Calorosamente,</p><p><br></p>
									<p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Congratulations, '.$name.', your application has been approved!';
							$msg = '<p>Dear '.$name.',</p><p><br></p>
									<p>Congratulations!  Your application for GProCongress II has been approved!  We look forward to seeing you in Panama City, Panama on November 12-17, 2023, the Lord willing. </p><p><br></p>
									<p>Please sign up for our GProCongress app!  You will want to have access to all information about the Congress, and that’s where the app comes in.  You can receive notifications, complete your registration, and even pay your registration fees on the app.  Just go to the '.$ios.' or to '.$android.' and download it today! </p><p><br></p>
									<p>If you have any questions about GProCongress II, please reply to this email to connect with one of our team members. We welcome you with joy to GProCongress II, and we look forward to all that God is going to do in and through us to build community, explore opportunities, discover resources, and exchange encouragement with trainers of pastors worldwide!</p><p><br></p>
									<p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';	
						
						}


						$to = $resultSpouse->email;
						
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($resultSpouse->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($resultSpouse->mobile);

					}elseif($resultSpouse->profile_status == 'Approved'){

						if($resultSpouse->spouse_confirm_token){

							return response(array('error'=>false, 'reload'=>true, 'message'=>'Spouse confirmation pending'), 403);

						}else{

							if($request->post('room_type') == 'Yes'  && $request->post('category') != ''){
								$result->room = $request->post('category');
							}
							$result->change_room_type = $request->post('room_type');
		
							$result->upgrade_category = $request->post('category');
							$result->early_bird = $request->post('early_bird');
							$result->offer_id = $request->post('offer_id');
							$result->amount = $request->post('amount');
							$result->payment_country = $request->post('citizenship');
							$result->cash_payment_option = $request->post('cash_payment');
							$result->role_id = $request->post('role_id');
							$result->status_change_at = date('Y-m-d H:i:s');
							$result->stage = '2';
							$result->profile_update = '1';
							
							$name= $result->name.' '.$result->last_name;
							
							if($request->post('amount')>0){
							
								$android = '<a href="https://play.google.com/store/apps/details?id=org.gprocommision">Google Play</a>';
								$ios = '<a href="https://apps.apple.com/us/app/gpro-commission/id1664828059">App Store</a>';
								$website = '<a href="https://www.gprocongress.org/payment">website</a>';

								if($result->language == 'sp'){

									$website = '<a href="https://www.gprocongress.org/payment">sitio web</a>';

									$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
									$msg = '<p>Estimado '.$name.'</p><p><br></p><p>
										<br></p><p>¡Felicidades! Su aplicación para el GPorCongress II ha sido aprobada. Dios mediante esperamos verle en Panamá ciudad de Panamá, del 12 al 17 de noviembre del 2023.</p><p><br></p><p>
										<br></p><p>¡Inscríbase en nuestra aplicación GProCongress! Querrá tener acceso a toda la información sobre el Congreso, y ahí es donde entra el app. Puede recibir notificaciones, completar su inscripción e incluso pagar en la aplicación. ¡Simplemente vaya a '.$ios.' o a '.$android.' y ¡descárguelo hoy!</p><p><br></p><p>
										<br></p><p>Su pago por el Congreso vence ahora, y puede hacerse en cualquier momento. Siga las instrucciones que se detallan a continuación para realizar su pago.</p><p><br></p><p>Puede realizar los pagos en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.' usando cualquiera de los distintos métodos de pago:</p><p>
										<br></p><p>1. Pago en línea con tarjeta de crédito: puede pagar su inscripción con cualquiera de las tarjetas de crédito más importantes.</p><p><br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
										<br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
										<br></p><p>3. Western Union – Puedes pagar su inscripción a través de Western Union en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.'. Envíe sus fondos a David Brugger, Dallas, Texas, EE.UU.  Junto con los fondos, envíe la siguiente información: </p><p>&nbsp;&nbsp;&nbsp;(1) su nombre completo, </p><p>&nbsp;&nbsp;&nbsp;(2) el país desde el que realiza el envío, </p><p>&nbsp;&nbsp;&nbsp;(3) la cantidad enviada en USD, y </p><p>&nbsp;&nbsp;&nbsp;(4) el código que Western Union le haya dado.</p><p>
										<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
										<br></p><p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p>
										<br></p><p>Si tiene alguna pregunta sobre GProCongress II, responda a este correo electrónico para conectarse con uno de los miembros de nuestro equipo. ¡Le damos la bienvenida con alegría al GProCongress II, y estamos a la expectativa de todo lo que Dios va a hacer en y a través de nosotros para desarrollaruna comunidad, explorar oportunidades, descubrir recursos e brindarse ánimo mutuo con capacitadores de pastores en todo el mundo!</p><p>
										<br></p><p>Atentamente</p><p><br></p><p>Equipo GProCongress II </p>';
								
								}elseif($result->language == 'fr'){

									$website = '<a href="https://www.gprocongress.org/payment">site Web</a>';

									$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
									$msg = '<p>Cher '.$name.',&nbsp;</p><p>
									<br></p><p>Félicitations! Votre candidature pour GProCongress II a été approuvée ! Nous sommes impatients de vous voir à Panama City, au Panama, du 12 au 17 novembre 2023, si le Seigneur le veut.</p><p>
									<br></p><p>Inscrivez-vous à notre application GProCongress ! Vous voudrez avoir accès à toutes les informations sur le Congrès, et c’est là que l’application entre en jeu. Vous pouvez recevoir des notifications, terminer votre inscription et même payer vos frais d’inscription sur l’application. Il suffit d’aller sur l’'.$ios.' ou sur '.$android.' et de la télécharger dès aujourd’hui!</p><p>
									<br></p><p>Votre paiement pour le Congrès est maintenant dû et peut être effectué à tout moment. Veuillez suivre les instructions ci-dessous pour effectuer votre paiement.</p><p>
									<br></p><p>Vous pouvez payer vos frais sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.' en utilisant l’un des différents modes de paiement:</p><p>
									<br></p><p>1. Paiement en ligne par carte de crédit – vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</p><p>
									<br></p><p>2. Virement bancaire – vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org . Vous recevrez des instructions par réponse de l’e-mail.</p><p>
									<br></p><p>3. Western Union – vous pouvez payer vos frais par Western Union en allant sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.'. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes : </p><p>&nbsp;&nbsp;&nbsp;(1) votre nom complet, </p><p>&nbsp;&nbsp;&nbsp;(2) le pays à partir duquel vous envoyez, </p><p>&nbsp;&nbsp;&nbsp;(3) le montant envoyé en USD et </p><p>&nbsp;&nbsp;&nbsp;(4) le code qui vous a été donné par Western Union.</p><p>
									<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
									<br></p><p>VEUILLEZ NOTER: Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</p><p>
									<br></p><p>Si vous avez des questions concernant GProCongress II, veuillez répondre à cet e-mail pour communiquer avec l’un des membres de notre équipe. Nous vous accueillons avec joie au GProCongress II, et nous attendons avec impatience tout ce que Dieu va faire en nous et à travers nous pour construire une communauté, explorer les opportunités, découvrir des ressources et échanger des encouragements avec les formateurs de pasteurs du monde entier!</p><p>
									<br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
								
								}elseif($result->language == 'pt'){

									$website = '<a href="https://www.gprocongress.org/payment">site</a>';

									$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
									$msg = '<p>Prezado '.$name.',</p><p>
									<br></p><p>Parabéns! A sua inscrição para o GProCongress II foi aprovada! Esperamos vê-lo na Cidade do Panamá, Panamá, de 12 a 17 de novembro de 2023, se o Senhor permitir.</p><p>
									<br></p><p>Por favor, inscreva-se no nosso aplicativo GProCongresso! Você vai querer ter acesso a todas as informações sobre o Congresso, e é aí que entra o app. Você pode receber notificações, fazer sua inscrição e até pagar sua inscrição no app. Basta ir na '.$ios.' ou ao '.$android.' e fazer o download hoje mesmo!</p><p>
									<br></p><p>O pagamento do Congresso está vencido e pode ser feito a qualquer momento. Siga as instruções listadas abaixo para efetuar o pagamento.</p><p>
									<br></p><p>Você pode pagar suas taxas em nosso '.$website.' ou em nosso aplicativo '.$ios.' ou ao '.$android.' usando qualquer um dos vários métodos de pagamento:</p><p>
									<br></p><p>1. Pagamento online usando cartão de crédito – você pode pagar suas taxas usando qualquer cartão de crédito.</p><p>
									<br></p><p>2. Transferência bancária – você pode pagar por transferência bancária do seu banco. Se você quiser fazer uma transferência eletrônica, envie um e-mail para david@rreach.org. Você receberá instruções por e-mail de resposta.</p><p>
									<br></p><p>3. Western Union – você pode pagar suas taxas via Western Union acessando nosso '.$website.' ou nosso aplicativo '.$ios.' ou ao '.$android.'. Por favor, envie seus fundos para David Brugger, Dallas, Texas, EUA. Juntamente com seus recursos, envie as seguintes informações: </p><p>&nbsp;&nbsp;&nbsp;(1) seu nome completo, </p><p>&nbsp;&nbsp;&nbsp;(2) o país de onde você está enviando, </p><p>&nbsp;&nbsp;&nbsp;(3) o valor enviado em USD e </p><p>&nbsp;&nbsp;&nbsp;(4) o código fornecido a você pela Western Union.</p><p>
									<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
									<br></p><p>ATENÇÃO: Se o pagamento integral não for recebido até 31st August 2023, sua inscrição será cancelada, sua vaga será cedida a outra pessoa e quaisquer valores pagos anteriormente por você serão perdidos.</p><p>
									<br></p><p>Se você tiver alguma dúvida sobre o GProCongress II, responda a este e-mail para entrar em contato com um dos membros de nossa equipe. Damos as boas-vindas ao GProCongress II com alegria e esperamos tudo o que Deus fará em nós e através de nós para construir uma comunidade, explorar oportunidades, descobrir recursos e trocar encorajamento com treinadores de pastores em todo o mundo!</p><p>
									<br></p><p>Calorosamente,</p><p>
									<br></p><p>Equipe do II CongressoGPro</p>';
								
								}else{
								
									$subject = 'Congratulations, '.$name.', your application has been approved!';
									$msg = '<p>Dear '.$name.',</p><p><br></p>
									<p>Congratulations!  Your application for GProCongress II has been approved!  We look forward to seeing you in Panama City, Panama on November 12-17, 2023, the Lord willing.</p>
									<p><br></p><p>Please sign up for our GProCongress app!  You will want to have access to all information about the Congress, and that’s where the app comes in.  You can receive notifications, complete your registration, and even pay your registration fees on the app.  Just go to the '.$ios.' or to '.$android.' and download it today!</p><p>
									<br></p><p>Your payment for the Congress is now due, and can be made anytime.  Please follow the instructions listed below to make your payment.</p><p>
									<br></p><p>You may pay your fees on our '.$website.' or on our app '.$ios.' or to '.$android.' using any of several payment methods:</p><p><br>
									</p><p>1. Online payment using credit card – you can pay your fees using any major credit card.</p><p><br>
									</p><p>2. Bank transfer – you can pay via wire transfer from your bank. If you want to make a wire transfer, please email david@rreach.org. You will receive instructions via reply email.</p><p><br>
									</p><p>3. Western Union – you can pay your fees via Western Union by going to our '.$website.', or to our app '.$ios.' or to '.$android.'. Please send your funds to David Brugger, Dallas, Texas, USA.  Along with your funds, please submit the following information: </p><p>&nbsp;&nbsp;&nbsp;(1) your full name, </p><p>&nbsp;&nbsp;&nbsp;(2) the country you are sending from, </p><p>&nbsp;&nbsp;&nbsp;(3) the amount sent in USD, and </p><p>&nbsp;&nbsp;&nbsp;(4) the code given to you by Western Union. </p><p><br>
									<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
									</p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited. </p><p><br>
									</p><p>If you have any questions about GProCongress II, please reply to this email to connect with one of our team members. We welcome you with joy to GProCongress II, and we look forward to all that God is going to do in and through us to build community, explore opportunities, discover resources, and exchange encouragement with trainers of pastors worldwide! </p><p>
									<br></p><p>Warmly,</p><p>
									<br></p><p>The GProCongress II Team</p>';	
								
								}
			
								\App\Helpers\commonHelper::emailSendToUser($result->email, $subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

							}else{

								$result->stage = '3';
							}
						}

					}else{

						return response(array('error'=>false, 'reload'=>true, 'message'=>'Spouse profile pending'), 403);
		
					}
					
				}else{

					if($request->post('room_type') == 'Yes'  && $request->post('category') != ''){
						$result->room = $request->post('category');
					}
					$result->change_room_type = $request->post('room_type');

					$result->upgrade_category = $request->post('category');
					$result->early_bird = $request->post('early_bird');
					$result->offer_id = $request->post('offer_id');
					$result->amount = $request->post('amount');
					$result->payment_country = $request->post('citizenship');
					$result->cash_payment_option = $request->post('cash_payment');
					$result->role_id = $request->post('role_id');
					$result->status_change_at = date('Y-m-d H:i:s');
					$result->stage = '2';
					$result->profile_update = '1';

					$name= $result->name.' '.$result->last_name;
					
					if($request->post('amount')>0){

						$android = '<a href="https://play.google.com/store/apps/details?id=org.gprocommision">Google Play</a>';
						$ios = '<a href="https://apps.apple.com/us/app/gpro-commission/id1664828059">App Store</a>';
						$website = '<a href="https://www.gprocongress.org/payment">website</a>';

						if($result->language == 'sp'){

							$website = '<a href="https://www.gprocongress.org/payment">sitio web</a>';

							$subject = "¡Felicidades, ".$name.", su solicitud ha sido aprobada!";
							$msg = '<p>Estimado '.$name.'</p><p><br></p><p>
								<br></p><p>¡Felicidades! Su aplicación para el GPorCongress II ha sido aprobada. Dios mediante esperamos verle en Panamá ciudad de Panamá, del 12 al 17 de noviembre del 2023.</p><p><br></p><p>
								<br></p><p>¡Inscríbase en nuestra aplicación GProCongress! Querrá tener acceso a toda la información sobre el Congreso, y ahí es donde entra el app. Puede recibir notificaciones, completar su inscripción e incluso pagar en la aplicación. ¡Simplemente vaya a '.$ios.' o a '.$android.' y ¡descárguelo hoy!</p><p><br></p><p>
								<br></p><p>Su pago por el Congreso vence ahora, y puede hacerse en cualquier momento. Siga las instrucciones que se detallan a continuación para realizar su pago.</p><p><br></p><p>Puede realizar los pagos en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.' usando cualquiera de los distintos métodos de pago:</p><p>
								<br></p><p>1. Pago en línea con tarjeta de crédito: puede pagar su inscripción con cualquiera de las tarjetas de crédito más importantes.</p><p><br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
								<br></p><p>2. Transferencia bancaria: puede pagar mediante transferencia bancaria desde su banco. Si desea realizar una transferencia bancaria, envíe un correo electrónico a david@rreach.org. Recibirá instrucciones a través del correo electrónico de respuesta.</p><p>
								<br></p><p>3. Western Union – Puedes pagar su inscripción a través de Western Union en nuestro '.$website.' o en nuestra aplicación '.$ios.' o a '.$android.'. Envíe sus fondos a David Brugger, Dallas, Texas, EE.UU.  Junto con los fondos, envíe la siguiente información: </p><p>&nbsp;&nbsp;&nbsp;(1) su nombre completo, </p><p>&nbsp;&nbsp;&nbsp;(2) el país desde el que realiza el envío, </p><p>&nbsp;&nbsp;&nbsp;(3) la cantidad enviada en USD, y </p><p>&nbsp;&nbsp;&nbsp;(4) el código que Western Union le haya dado.</p><p>
								<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
								<br></p><p>TENGA EN CUENTA: Si no se recibe el pago completo antes del 31 de Agosto, 2023, se cancelará su inscripción, se le dará su lugar a otra persona, y perderá todos los fondos que usted haya pagado previamente.</p><p>
								<br></p><p>Si tiene alguna pregunta sobre GProCongress II, responda a este correo electrónico para conectarse con uno de los miembros de nuestro equipo. ¡Le damos la bienvenida con alegría al GProCongress II, y estamos a la expectativa de todo lo que Dios va a hacer en y a través de nosotros para desarrollaruna comunidad, explorar oportunidades, descubrir recursos e brindarse ánimo mutuo con capacitadores de pastores en todo el mundo!</p><p>
								<br></p><p>Atentamente</p><p><br></p><p>Equipo GProCongress II </p>';
						
						}elseif($result->language == 'fr'){

							$website = '<a href="https://www.gprocongress.org/payment">site Web</a>';

							$subject = "Félicitations, ".$name.", votre demande a été approuvée !";
							$msg = '<p>Cher '.$name.',&nbsp;</p><p>
							<br></p><p>Félicitations! Votre candidature pour GProCongress II a été approuvée ! Nous sommes impatients de vous voir à Panama City, au Panama, du 12 au 17 novembre 2023, si le Seigneur le veut.</p><p>
							<br></p><p>Inscrivez-vous à notre application GProCongress ! Vous voudrez avoir accès à toutes les informations sur le Congrès, et c’est là que l’application entre en jeu. Vous pouvez recevoir des notifications, terminer votre inscription et même payer vos frais d’inscription sur l’application. Il suffit d’aller sur l’'.$ios.' ou sur '.$android.' et de la télécharger dès aujourd’hui!</p><p>
							<br></p><p>Votre paiement pour le Congrès est maintenant dû et peut être effectué à tout moment. Veuillez suivre les instructions ci-dessous pour effectuer votre paiement.</p><p>
							<br></p><p>Vous pouvez payer vos frais sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.' en utilisant l’un des différents modes de paiement:</p><p>
							<br></p><p>1. Paiement en ligne par carte de crédit – vous pouvez payer vos frais en utilisant n’importe quelle carte de crédit principale.</p><p>
							<br></p><p>2. Virement bancaire – vous pouvez payer par virement bancaire depuis votre banque. Si vous souhaitez effectuer un virement bancaire, veuillez envoyer un e-mail à david@rreach.org . Vous recevrez des instructions par réponse de l’e-mail.</p><p>
							<br></p><p>3. Western Union – vous pouvez payer vos frais par Western Union en allant sur notre '.$website.' ou sur notre application '.$ios.' ou sur '.$android.'. Veuillez envoyer vos fonds à David Brugger, Dallas, Texas, États-Unis. En plus de vos fonds, veuillez soumettre les informations suivantes : </p><p>&nbsp;&nbsp;&nbsp;(1) votre nom complet, </p><p>&nbsp;&nbsp;&nbsp;(2) le pays à partir duquel vous envoyez, </p><p>&nbsp;&nbsp;&nbsp;(3) le montant envoyé en USD et </p><p>&nbsp;&nbsp;&nbsp;(4) le code qui vous a été donné par Western Union.</p><p>
							<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
							<br></p><p>VEUILLEZ NOTER: Si le paiement complet n’est pas reçu avant 31st August 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</p><p>
							<br></p><p>Si vous avez des questions concernant GProCongress II, veuillez répondre à cet e-mail pour communiquer avec l’un des membres de notre équipe. Nous vous accueillons avec joie au GProCongress II, et nous attendons avec impatience tout ce que Dieu va faire en nous et à travers nous pour construire une communauté, explorer les opportunités, découvrir des ressources et échanger des encouragements avec les formateurs de pasteurs du monde entier!</p><p>
							<br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
						
						}elseif($result->language == 'pt'){

							$website = '<a href="https://www.gprocongress.org/payment">site</a>';

							$subject = "Parabéns, ".$name.", sua inscrição foi aprovada!";
							$msg = '<p>Prezado '.$name.',</p><p>
							<br></p><p>Parabéns! A sua inscrição para o GProCongress II foi aprovada! Esperamos vê-lo na Cidade do Panamá, Panamá, de 12 a 17 de novembro de 2023, se o Senhor permitir.</p><p>
							<br></p><p>Por favor, inscreva-se no nosso aplicativo GProCongresso! Você vai querer ter acesso a todas as informações sobre o Congresso, e é aí que entra o app. Você pode receber notificações, fazer sua inscrição e até pagar sua inscrição no app. Basta ir na '.$ios.' ou ao '.$android.' e fazer o download hoje mesmo!</p><p>
							<br></p><p>O pagamento do Congresso está vencido e pode ser feito a qualquer momento. Siga as instruções listadas abaixo para efetuar o pagamento.</p><p>
							<br></p><p>Você pode pagar suas taxas em nosso '.$website.' ou em nosso aplicativo '.$ios.' ou ao '.$android.' usando qualquer um dos vários métodos de pagamento:</p><p>
							<br></p><p>1. Pagamento online usando cartão de crédito – você pode pagar suas taxas usando qualquer cartão de crédito.</p><p>
							<br></p><p>2. Transferência bancária – você pode pagar por transferência bancária do seu banco. Se você quiser fazer uma transferência eletrônica, envie um e-mail para david@rreach.org. Você receberá instruções por e-mail de resposta.</p><p>
							<br></p><p>3. Western Union – você pode pagar suas taxas via Western Union acessando nosso '.$website.' ou nosso aplicativo '.$ios.' ou ao '.$android.'. Por favor, envie seus fundos para David Brugger, Dallas, Texas, EUA. Juntamente com seus recursos, envie as seguintes informações: </p><p>&nbsp;&nbsp;&nbsp;(1) seu nome completo, </p><p>&nbsp;&nbsp;&nbsp;(2) o país de onde você está enviando, </p><p>&nbsp;&nbsp;&nbsp;(3) o valor enviado em USD e </p><p>&nbsp;&nbsp;&nbsp;(4) o código fornecido a você pela Western Union.</p><p>
							<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
							<br></p><p>ATENÇÃO: Se o pagamento integral não for recebido até 31st August 2023, sua inscrição será cancelada, sua vaga será cedida a outra pessoa e quaisquer valores pagos anteriormente por você serão perdidos.</p><p>
							<br></p><p>Se você tiver alguma dúvida sobre o GProCongress II, responda a este e-mail para entrar em contato com um dos membros de nossa equipe. Damos as boas-vindas ao GProCongress II com alegria e esperamos tudo o que Deus fará em nós e através de nós para construir uma comunidade, explorar oportunidades, descobrir recursos e trocar encorajamento com treinadores de pastores em todo o mundo!</p><p>
							<br></p><p>Calorosamente,</p><p>
							<br></p><p>Equipe do II CongressoGPro</p>';
						
						}else{
						
							$subject = 'Congratulations, '.$name.', your application has been approved!';
							$msg = '<p>Dear '.$name.',</p><p><br></p>
							<p>Congratulations!  Your application for GProCongress II has been approved!  We look forward to seeing you in Panama City, Panama on November 12-17, 2023, the Lord willing.</p>
							<p><br></p><p>Please sign up for our GProCongress app!  You will want to have access to all information about the Congress, and that’s where the app comes in.  You can receive notifications, complete your registration, and even pay your registration fees on the app.  Just go to the '.$ios.' or to '.$android.' and download it today!</p><p>
							<br></p><p>Your payment for the Congress is now due, and can be made anytime.  Please follow the instructions listed below to make your payment.</p><p>
							<br></p><p>You may pay your fees on our '.$website.' or on our app '.$ios.' or to '.$android.' using any of several payment methods:</p><p><br>
							</p><p>1. Online payment using credit card – you can pay your fees using any major credit card.</p><p><br>
							</p><p>2. Bank transfer – you can pay via wire transfer from your bank. If you want to make a wire transfer, please email david@rreach.org. You will receive instructions via reply email.</p><p><br>
							</p><p>3. Western Union – you can pay your fees via Western Union by going to our '.$website.', or to our app '.$ios.' or to '.$android.'. Please send your funds to David Brugger, Dallas, Texas, USA.  Along with your funds, please submit the following information: </p><p>&nbsp;&nbsp;&nbsp;(1) your full name, </p><p>&nbsp;&nbsp;&nbsp;(2) the country you are sending from, </p><p>&nbsp;&nbsp;&nbsp;(3) the amount sent in USD, and </p><p>&nbsp;&nbsp;&nbsp;(4) the code given to you by Western Union. </p><p><br>
							<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
							</p><p>PLEASE NOTE: If full payment is not received by 31st August 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited. </p><p><br>
							</p><p>If you have any questions about GProCongress II, please reply to this email to connect with one of our team members. We welcome you with joy to GProCongress II, and we look forward to all that God is going to do in and through us to build community, explore opportunities, discover resources, and exchange encouragement with trainers of pastors worldwide! </p><p>
							<br></p><p>Warmly,</p><p>
							<br></p><p>The GProCongress II Team</p>';	
						
						}
						
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

						// \App\Helpers\commonHelper::sendSMS($result->mobile);
					}else{

						$result->stage = '3';
					}
				}

				

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

			}else if ($request->post('status') == 'Waiting') {

				$name= $result->name.' '.$result->last_name;

				if($result->language == 'sp'){

					$subject = "Estado de su solicitud para el GProCongress II";
					$msg = '<p>Estimado '.$name.'</p><p><br></p><p><br></p><p>Gracias por registrarse para participar del GProCongress II.</p><p><br></p><p>Dado que evaluamos muchas solicitudes con diversos niveles de participación en la capacitación de pastores, su solicitud para asistir al GProCongress II ha sido colocada en lista de espera.</p><p><br></p><p>Usted debería recibir una actualización de nuestra parte, Dios mediante, (pronto/fecha específica/en un par de meses).&nbsp;</p><p><br></p><p><br></p><p>¿Tiene preguntas? Simplemente responda a este correo para conectarse con algún miembro de nuestro equipo.&nbsp;</p><p><br></p><p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p>Atentamente,</p><p><br></p><p>El equipo del GProCongress II</p>';
				
				}elseif($result->language == 'fr'){
				
					$subject = "Statut de votre demande GProCongrès II";
					$msg = '<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Merci d’avoir postulé pour assister au GProCongrès II.</p><p>Comme nous évaluons de nombreuses candidatures avec différents niveaux d’implication de la formation des pasteurs, votre candidature pour assister au GProCongrès II a été placée sur une liste d’attente.&nbsp;</p><p>Vous devriez recevoir une mise à jour de notre part, si le Seigneur le veut, (bientôt/par date précise/dans quelques mois).&nbsp;</p><p><br></p><p>Vous avez des questions ? Il suffit de répondre à cet e-mail pour communiquer avec un membre de l’équipe.&nbsp;</p><p><br></p><p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongrès II</p>';
				
				}elseif($result->language == 'pt'){
				
					$subject = "Estado da sua inscrição ao II CongressoGPro";
					$msg = '<p>Prezado, '.$name.',</p><p><br></p><p><br></p><p>Agradecemos pelo seu pedido para participar no II CongressoGPro.</p><p>A medida que avaliamos muitos pedidos com vários níveis de envolvimento no treinamento pastoral, o seu pedido de participação no II CongressoGPro foi colocado na lista de espera.</p><p>Você irá receber uma atualização da nossa parte, se o Senhor quiser, (brevemente/até data específica/ dentro de alguns meses).&nbsp;</p><p><br></p><p>Tem perguntas? Simplesmente responda este e-mail para se conectar com nosso membro da equipe.</p><p><br></p><p>Ore conosco, à medida que nos esforçamos para multiplicar os números, e desenvolvemos a capacidade de treinadores de pastores.</p><p><br></p><p>Calorosamente,</p><p><br></p><p>Equipe do II CongressoGPro</p>';
				
				}else{
				
					$subject = 'Your GProCongress II application status';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>Thank you for applying to attend the GProCongress II.</p><p>As we evaluate many applications with various levels of pastor training involvement, your application to attend the GProCongress II has been placed on a waiting list.&nbsp;</p><p>You should receive an update from us, the Lord willing, (soon/by specific date/ in a couple of months).</p><p><br></p><p>Have questions? Simply respond to this email to connect with a team member.</p><p><br></p><p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p><div><br></div>';
					
				}
				
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);

				// \App\Helpers\commonHelper::sendSMS($result->mobile);
			}
			

			$result->save();

			$UserHistory=new \App\Models\UserHistory();
			$UserHistory->user_id=$result->id;
			$UserHistory->action_id=\Auth::user()->id;
			$UserHistory->action='User Profile '.$request->post('status');
			$UserHistory->save();

			return response(array('error'=>false, 'reload'=>true, 'message'=>'Profile status change successful'), 200);
		
		} else {
			return response(array('error'=>true, 'reload'=>false, 'message'=>'Something went wrong. Please try again.'), 403);
		}

	}

	public function commentToUser(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){
			
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
					$data->sender_id = \Auth::user()->id;
					$data->receiver_id = $request->post('user_id');
					$data->comment = $request->post('comment');
					$data->save();

					$UserHistory=new \App\Models\UserHistory();
					$UserHistory->action_id=\Auth::user()->id;
					$UserHistory->user_id=$request->post('user_id');
					$UserHistory->action='Comment';
					$UserHistory->save();
	
					return response(array('reset'=>true, 'comment' => true, 'message'=>'Comment has been sent successfully.'), 200);

				} catch (\Throwable $th) {
					return response(array('message'=>'Something went wrong, please try again'), 500);
				}
			
			}
		} else if($request->ajax() && $request->isMethod('get')) {
			
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

				return \App\Helpers\commonHelper::getUserNameById($data->sender_id);;
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

	public function userHistoryList(Request $request) {
	
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
	
	public function getProfileBasePrice(Request $request) {

		if ($request->ajax()) {

			$basePrice = 0; $Spouse = []; $category = []; $trainer = '';

			$user = \App\Models\User::where('id', $request->post('id'))->where('stage','1')->first();

			if($user){

				$citizenship = $user->citizenship;

				if($request->post('citizenship')){

					$citizenship = $request->post('citizenship');

				}
				
				$countryPrice=\App\Models\Pricing::where('country_id',$citizenship)->first();

				$Spouse = \App\Models\User::where('parent_id', $request->post('id'))->where('added_as', 'Spouse')->first();
				
				if($user->marital_status == 'Unmarried'){

					$data = \App\Helpers\commonHelper::getBasePriceOfUnmarried($user->room,$countryPrice->base_price);
					
					$basePrice = $data ['basePrice'];
					$category = $data ['category'];


				}else if($user->marital_status == 'Married' && !$Spouse){

					$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWOSpouse($user->room,$countryPrice->base_price);

					$basePrice = $data ['basePrice'];
					$category = $data ['category'];

				}else if($user->marital_status == 'Married' && $Spouse){

					if($user->parent_spouse_stage >= 2){

						$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWOSpouse($user->room,$countryPrice->base_price);
						$basePrice = $data ['basePrice'];
						$category = $data ['category'];

					}else{

						$data = \App\Helpers\commonHelper::getBasePriceOfMarriedWSpouse($user->doyouseek_postoral,$Spouse->doyouseek_postoral,$user->ministry_pastor_trainer,$Spouse->ministry_pastor_trainer,$countryPrice->base_price);

						$basePrice = $data ['basePrice'];
						$category = $data ['category'];
						$trainer = $data ['trainer'];
					}
					
				}

				$Offers = \App\Models\Offer::where([
					['status','=','1'],
					['start_date','<=',date('Y-m-d')],
					['end_date','>=',date('Y-m-d')]
					])->orderBy('id','desc')->get();

				$country = \App\Models\Pricing::orderBy('country_name', 'asc')->get();

				$html=view('admin.user.stage.stage_one_profile_status_model',compact('basePrice','Offers','category','trainer','country','user','citizenship'))->render();

				return response()->json(array('html'=>$html));
				

			}
			
        }

	}
	
	public function getOfferPrice(Request $request) {

		if ($request->ajax()) {

			$couponResult=\App\Models\Offer::where([
				['id','=',$request->id],
				['status','=','1'],
				['start_date','<=',date('Y-m-d')],
				['end_date','>=',date('Y-m-d')]
				])->first();

			if(!$couponResult){

				return response()->json(array('error'=>true,'message'=>'This Offer limit has been exceed.'));

			}else{

				$amount = \App\Helpers\commonHelper::getOfferDiscount($request->id,$request->amount);
			
				return response()->json(array('error'=>false,'amount'=>$amount));
			}
			
        }

	}

    public function spousePending(Request $request) {
		
		if ($request->ajax()) { 

			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			
			$query = \App\Models\User::where([['designation_id', 2], ['spouse_confirm_status', 'Pending'], ['spouse_confirm_token','!=','']])
						->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where('email', 'like', "%" . request('email') . "%");
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::where([['designation_id', 2], ['spouse_confirm_status', 'Pending'], ['spouse_confirm_token','!=','']]);


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
				return $data->name.' '.$data->last_name ?? '-';
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('mobile', function($data){
				return '+'.$data->phone_code.' '.$data->mobile ?? '-';
		    })

			->addColumn('profile', function($data){
				if ($data->spouse_confirm_status == 'Pending') {
					
					return '<div class="span badge rounded-pill pill-badge-danger">Pending</div>';
					
				} else if ($data->spouse_confirm_status == 'Approve') {

					return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
				}
		    })

			// ->addColumn('status', function($data){
			// 	if($data->status=='1'){ 
			// 		$checked = "checked";
			// 	}else{
			// 		$checked = " ";
			// 	}

			// 	return '<div class="media-body icon-state switch-outline">
			// 				<label class="switch">
			// 					<input type="checkbox" class="-change" data-id="'.$data->id.'" '.$checked.'><span class="switch-state bg-primary"></span>
			// 				</label>
			// 			</div>';
		    // })

			->addColumn('action', function($data){
				
				
					
				return '<div style="display:flex"><a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></div>';

				
			})

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }


	}

	public function stageAllDownloadExcelFile(Request $request){

		// try{

			$result = \App\Models\User::with('TravelInfo')->where([['designation_id', 2], ['parent_id', NULL], ['added_as', NULL]])->orderBy('updated_at', 'desc')->get();
			
			if($result->count()==0){

				return response(array('error'=>true,'message'=>'Data not found.'),200);

			}else{

				
					$delimiter = ",";  
					$filename = "User-".date('d-m-Y').rand(111,999).".csv";
					
					$f = fopen('php://memory', 'w'); 
					
					//$f = fopen('php://memory', 'w');
					$fields = array('Id', 'Current Stage', 
										'Candidate Name', 
										'Candidate email Address', 
										'Candidate Mobile Number', 
										'Country', 
										'Citizenship', 
										'Spouse Name', 
										'Spouse Email Address', 
										'Group Leader Name', 
										'Group Leader Email Address', 
										'Room Type', 
										'Pastor Trainer(yes/No)', 
										'Ministry Name', 
										'Total Payable Amount', 
										'Pending Amount', 
										'Accepted Amount', 
										'Payment in process', 
										'Payment Declined', 
										'Early Bird', 
										'Arrival date', 
										'Departure Date', 
										'Cab needed on Arrival', 
										'Cab needed on Departure', 
										'Stage 0', 
										'Stage 1', 
										'Stage 2', 
										'Stage 3', 
										'Stage 4', 
										'Stage 5'); 
					fputcsv($f, $fields, $delimiter); 

					$i=1;
					foreach($result as $row){
						
						if($row['stage'] == 0){
							$stage0 = "In Process";
						}elseif($row['stage'] > 0){
							$stage0 = "Completed";
						}else{
							$stage0 = "Pending";
						}  

						if($row['stage'] == 1){
							$stage1 = "In Process";
						}elseif($row['stage'] > 1){
							$stage1 = "Completed";
						}else{
							$stage1 = "Pending";
						} 

						if($row['stage'] == 2){
							$stage2 = "In Process";
						}elseif($row['stage'] > 2){
							$stage2 = "Completed";
						}else{
							$stage2 = "Pending";
						} 

						if($row['stage'] == 3){
							$stage3 = "In Process";
						}elseif($row['stage'] > 3){
							$stage3 = "Completed";
						}else{
							$stage3 = "Pending";
						} 

						if($row['stage'] == 4){
							$stage4 = "In Process";
						}elseif($row['stage'] > 4){
							$stage4 = "Completed";
						}else{
							$stage4 = "Pending";
						} 

						if($row['stage'] == 5){
							$stage5 = "In Process";
						}elseif($row['stage'] > 5){
							$stage5 = "Completed";
						}else{
							$stage5 = "Pending";
						} 

						$userSpouse = \App\Models\User::with('TravelInfo')->where([['parent_id', $row['id']], ['added_as', 'Spouse']])->first();
						$userGroup = \App\Models\User::where([['parent_id', $row['id']],['added_as', 'Group']])->first();
						$spouseName = '';
						$spouseEmail = '';
						$arrivalDate = '';
						$departureDate = '';
						$cabNeededOnArrival = '';
						$cabNeededOnDeparture = '';
						$mobile = '';
						$groupLeaderEmail= '';
						$groupLeaderName= '';
						if($userGroup){
							$groupLeaderName= $row['name'].' '.$row['last_name'];
							$groupLeaderEmail= $row['email'];
						}if($userSpouse){
							$spouseName= $userSpouse->name.' '.$userSpouse->last_name;
							$spouseEmail= $userSpouse->email;
						}

						
						if($row['TravelInfo'] && $row['TravelInfo']['flight_details']) {
							$flight_details = json_decode($row['TravelInfo']['flight_details']);
							$cabNeededOnArrival = $row['TravelInfo']['logistics_dropped'];
							$cabNeededOnDeparture = $row['TravelInfo']['logistics_picked'];
							if(!empty($flight_details)){
								$arrivalDate = date('d-m-Y',strtotime($flight_details->arrival_date_arrival));
								$departureDate = date('d-m-Y',strtotime($flight_details->departure_date_departure));
							}
							
						}

						if($row['mobile']){
							$mobile = '+'.$row['phone_code'].' '.$row['mobile'];
						}

						$lineData = array(($i), 'Stage '.$row['stage'], 
						$row['name'].' '.$row['last_name'], 
						$row['email'], 
						$mobile, 
						\App\Helpers\commonHelper::getCountryNameById($row['contact_country_id']), 
						\App\Helpers\commonHelper::getCountryNameById($row['citizenship']), 
						$spouseName,
						$spouseEmail,
						$groupLeaderName,
						$groupLeaderEmail,
						$row['room'] ?? 'Double Deluxe',
						$row['ministry_pastor_trainer'],
						$row['ministry_name'],
						$row['amount'],
						\App\Helpers\commonHelper::getTotalPendingAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAcceptedAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAmountInProcess($row['id']),
						\App\Helpers\commonHelper::getTotalRejectedAmount($row['id']),
						$row['early_bird'],
						$arrivalDate,
						$departureDate,
						$cabNeededOnArrival,
						$cabNeededOnDeparture,
						$stage0, $stage1, 
						$stage2, $stage3, $stage4, $stage5); 
						
						fputcsv($f, $lineData, $delimiter); 
						
						$results = \App\Models\User::with('TravelInfo')->where([['parent_id', $row['id']]])->get();
						$groupLeaderName = '';
						$groupLeaderEmail = '';
						$spouseLeaderName = '';
						$spouseLeaderEmail = '';
						$arrivalDate = '';
						$departureDate = '';
						$cabNeededOnDeparture = '';
						$cabNeededOnArrival = '';
						
						if(!empty($results) && count($results)>0){
								$j = 1;
							foreach($results as $val){

								if(isset($val['TravelInfo']) && $val['TravelInfo']['flight_details']) {
									$flight_details = json_decode($val['TravelInfo']['flight_details']);
									$cabNeededOnArrival = $val['TravelInfo']['logistics_dropped'];
									$cabNeededOnDeparture = $val['TravelInfo']['logistics_picked'];
									if(!empty($flight_details)){
										$arrivalDate = date('d-m-Y',strtotime($flight_details->arrival_date_arrival));
										$departureDate = date('d-m-Y',strtotime($flight_details->departure_date_departure));
									}
								}


								if($val && $val->added_as == 'Group'){

									$groupLeaderName= $row['name'].' '.$row['last_name'];
									$groupLeaderEmail= $row['email'];
								}
								if($val && $val->added_as == 'Spouse'){
									
									$spouseLeaderName= $row['name'].' '.$row['last_name'];
									$spouseLeaderEmail= $row['email'];
									$groupLeaderName = '';
									$groupLeaderEmail = '';
								}


								if($val['stage'] == 0){
									$stage0 = "In Process";
								}elseif($val['stage'] > 0){
									$stage0 = "Completed";
								}else{
									$stage0 = "Pending";
								}  

								if($val['stage'] == 1){
									$stage1 = "In Process";
								}elseif($val['stage'] > 1){
									$stage1 = "Completed";
								}else{
									$stage1 = "Pending";
								} 

								if($val['stage'] == 2){
									$stage2 = "In Process";
								}elseif($val['stage'] > 2){
									$stage2 = "Completed";
								}else{
									$stage2 = "Pending";
								} 

								if($val['stage'] == 3){
									$stage3 = "In Process";
								}elseif($val['stage'] > 3){
									$stage3 = "Completed";
								}else{
									$stage3 = "Pending";
								} 

								if($val['stage'] == 4){
									$stage4 = "In Process";
								}elseif($val['stage'] > 4){
									$stage4 = "Completed";
								}else{
									$stage4 = "Pending";
								} 

								if($val['stage'] == 5){
									$stage5 = "In Process";
								}elseif($val['stage'] > 5){
									$stage5 = "Completed";
								}else{
									$stage5 = "Pending";
								} 


								if($val['mobile']){
									$mobile = '+'.$val['phone_code'].' '.$val['mobile'];
								}

								$lineData = array(($i), 'Stage '.$val['stage'], 
									$val['name'].' '.$val['last_name'], 
									$val['email'], 
									$mobile, 
									\App\Helpers\commonHelper::getCountryNameById($val['contact_country_id']), 
									\App\Helpers\commonHelper::getCountryNameById($val['citizenship']), 
									$spouseLeaderName,
									$spouseLeaderEmail,
									$groupLeaderName,
									$groupLeaderEmail,
									$val['room'] ?? 'Double Deluxe',
									$val['ministry_pastor_trainer'],
									$val['ministry_name'],
									$val['amount'],
									\App\Helpers\commonHelper::getTotalPendingAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAcceptedAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAmountInProcess($val['id']),
									\App\Helpers\commonHelper::getTotalRejectedAmount($val['id']),
									$val['early_bird'],
									$arrivalDate,
									$departureDate,
									$cabNeededOnArrival,
									$cabNeededOnDeparture,
									$stage0, $stage1, 
									$stage2, $stage3, $stage4, $stage5); 
								fputcsv($f, $lineData, $delimiter);

								$j++;
							}
						}

						$i++;
					}
					
					fseek($f, 0); 
					
					header('Content-Encoding: UTF-8');
					header('Content-type: text/csv; charset=UTF-8');
					header("Content-Disposition: attachment; filename=".$filename);
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					echo "\xEF\xBB\xBF"; // UTF-8 BOM
					
					fpassthru($f);
					

					fclose($f);
					
					//readfile ($filename);

				
				exit; 

				return response(array('error'=>false,"message" => "File downloaded success"),200); 
				
			}
			
		// }catch (\Exception $e){
		
		// 	return response(array('error'=>true,"message" => "Something went wrong.please try again"),200); 
		
		// }

	}

	public function stageDownloadExcelGovernmentPassport(Request $request){

		try{

			$query = \App\Models\PassportInfo::select('passport_infos.*','users.language')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', '2')->orderBy('updated_at', 'desc');

			$result = $query->get();
			
			if($result->count()==0){

				return response(array('error'=>true,'message'=>'Data not found.'),200);

			}else{

				
					$delimiter = ",";  
					$filename = "Government Report.csv";
					
					$f = fopen('php://memory', 'w'); 
					
					//$f = fopen('php://memory', 'w');
					$fields = array('Id', 'Full name', 
										'Passport number', 
										'Passport Issued by (Country)', 
										'Current Residence/Travelling From (Country)',
										'Status'
									); 
					fputcsv($f, $fields, $delimiter); 

					$i=1;
					foreach($result as $row){
						
						$name= $row['salutation'].' '.$row['name'];
						$countryPass= \App\Helpers\commonHelper::getCountry2NameById($row['country_id']);
						$country= \App\Helpers\commonHelper::getCountryNameById($row['citizenship']);
						
						if($row){

							if ($row['admin_status'] == 'Pending') {
		
								$admin_status = 'Passport info Submitted';
			
							} else if ($row['admin_status'] == 'Approved') {
			
								if($row['visa_granted'] == 'Yes'){
									$admin_status = 'Visa Granted';
								}elseif($row['visa_granted'] == 'No'){
		
									if($row['reference_number'] != null){
										$admin_status = 'Money Refunded';
									}
		
									$admin_status = 'Visa Not Granted';
		
								}else{
									$admin_status = 'Documents Issued';
								}
								
							} else if ($row['admin_status'] === 'Decline') {
			
								$admin_status = 'Passport Info Returned';
								
							}else{
			
								$admin_status = 'Passport Info Pending';
							}
		
						}else{
							$admin_status = 'Passport Info Pending';
						}

						$lineData = array(
							($i), 
							$name,
							$row['passport_no'], 
							$countryPass,
							$country,
							$admin_status
						); 
						
						fputcsv($f, $lineData, $delimiter); 
						
						$i++;
					}
					
					fseek($f, 0); 
					
					header('Content-Encoding: UTF-8');
					header('Content-type: text/csv; charset=UTF-8');
					header("Content-Disposition: attachment; filename=".$filename);
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					echo "\xEF\xBB\xBF"; // UTF-8 BOM

					fpassthru($f);
					

					fclose($f);
					
				exit; 

				return response(array('error'=>false,"message" => "File downloaded success"),200); 
				
			}
			
		}catch (\Exception $e){
		
			return response(array('error'=>true,"message" => "Something went wrong.please try again"),200); 
		
		}

	}

	public function stageDownloadExcelPassport(Request $request){

		try{

			$query = \App\Models\PassportInfo::select('passport_infos.*','users.language','users.citizenship as citizenship')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', '2')->orderBy('updated_at', 'desc');

			$result = $query->get();
			
			if($result->count()==0){

				return response(array('error'=>true,'message'=>'Data not found.'),200);

			}else{

				$delimiter = ",";  
				$filename = "Reach Report.csv";
				
				$f = fopen('php://memory', 'w'); 
				
				//$f = fopen('php://memory', 'w');
				$fields = array('Id', 'Full name', 
									'Passport number', 
									'Passport Copy', 
									'Passport Issued by (Country)', 
									'Current Residence/Travelling From (Country)',
									'Is this a diplomatic passport?',
									"Do you have a valid Visa or Residence, duly issued by Canada, the United States of America, the Commonwealth of Australia, the Republic of Korea, the State of Japan, the United Kingdom of Great Britain and Northern Ireland, Republic of Singapore, or any of the States that make up the European Union?",
									"Is your visa from one of the countries identified in the previous step a multiple entry visa, and is 6 month visa validity?",
									"Have you used your multiple entry visa at least once before to enter the country that granted it? ",
									"Is your passport valid until May 31, 2024?",
									'What countries among Canada, the United States of America, the Commonwealth of Australia, the Republic of Korea, the State of Japan, the United Kingdom of Great Britain and Northern Ireland, Republic of Singapore, or any of the States that make up the European Union you hold the Valid Visa?',
									'Is your Visa Granted ?',
								); 
				fputcsv($f, $fields, $delimiter); 

				$i=1;
				foreach($result as $row){
					
					$name= $row['salutation'].' '.$row['name']; $passportCopy = ''; $ResidenceProof = '';
					$countryPass= \App\Helpers\commonHelper::getCountry2NameById($row['country_id']);
					$country= \App\Helpers\commonHelper::getCountryNameById($row['citizenship']);
					
					if($row['passport_copy'] != ''){
						foreach(explode(",",rtrim($row['passport_copy'], ',')) as $key=>$img){
							$passportCopy .= asset('/uploads/passport/'.$img);
						}
					}

					if($row['valid_residence_country'] != Null){

						if($row['valid_residence_country'] != ''){

							$countryDoc = json_decode($row['valid_residence_country'],true); 

							foreach($countryDoc as $key=>$img){

								if($img['id'] == '15'){
									$ResidenceProof.= 'Visa/Residence Proof for European Union, '.asset('/uploads/passport/'.$img['file']).', ';

								}else{

									$ResidenceProof.= 'Visa/Residence Proof for '.\App\Helpers\commonHelper::getCountry2NameById($img['id']).' '.asset('/uploads/passport/'.$img['file']).', ';
									
								}
								
							}
						}

					}else{
						$ResidenceProof = 'N/A';
					}
					
					$lineData = array(
						($i), 
						$name,
						$row['passport_no'], 
						$passportCopy, 
						$countryPass,
						$country,
						$row['diplomatic_passport'],
						$row['visa_residence'] == Null ? 'N/A' : $row['visa_residence'],
						$row['multiple_entry_visa_country'] == Null ? 'N/A' : $row['multiple_entry_visa_country'],
						$row['multiple_entry_visa'] == Null ? 'N/A' : $row['multiple_entry_visa'],
						$row['passport_valid'] == Null ? 'N/A' : $row['passport_valid'],
						$ResidenceProof,
						$row['visa_granted'] == Null ? 'N/A' : $row['visa_granted'],
					); 
					
					fputcsv($f, $lineData, $delimiter); 
					
					$i++;
				}
				
				fseek($f, 0); 
				
				header('Content-Encoding: UTF-8');
				header('Content-type: text/csv; charset=UTF-8');
				header("Content-Disposition: attachment; filename=".$filename);
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				echo "\xEF\xBB\xBF"; // UTF-8 BOM
				
				fpassthru($f);
				

				fclose($f);
				
				exit; 

				return response(array('error'=>false,"message" => "File downloaded success"),200); 
				
			}	
			
		}catch (\Exception $e){
		
			return response(array('error'=>true,"message" => "Something went wrong.please try again"),200); 
		
		}
		

	}

    public function refundAmount(Request $request){
 
        if($request->ajax()){

			$rules = [
				'reference_number' => 'required',
				'amount' => 'required|numeric',
				'user_id' => 'required|numeric',
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

					$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
					if($referenceNumberCheck){

						return response(array("error"=>true, "message"=>'Transaction already exists'), 403);

					}else{

						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($request->post('user_id'));

						if($request->post('amount') <= $totalAcceptedAmount){
		
							$transactionId=strtotime("now").rand(11,99);
		
							$orderId=strtotime("now").rand(11,99);
			
							$transaction = new \App\Models\Transaction();
							$transaction->user_id = $request->post('user_id');
							$transaction->bank = 'Wire';
							$transaction->order_id = $orderId;
							$transaction->transaction_id = $transactionId;
							$transaction->method = 'Manual';
							$transaction->amount = $request->post('amount');
							$transaction->bank_transaction_id = $request->post('reference_number');
							$transaction->payment_status = '3';
							$transaction->status = '3';
							$transaction->particular_id = '4';
							$transaction->save();
		
							$Wallet = new \App\Models\Wallet();
							$Wallet->user_id = $request->post('user_id');
							$Wallet->type  = 'Dr';
							$Wallet->amount = $request->post('amount');
							$Wallet->transaction_id = $transaction->id;
							$Wallet->status = 'Success';
							$Wallet->save();
			
							$user = \App\Models\User::where('id',$request->post('user_id'))->first();
							$user->status = '1';
							$user->refund_amount = $request->post('amount');
							$user->reference_number = $request->post('reference_number');
							$user->save();

							$to = $user->email;
							$name = $user->name.' '.$user->last_name;
							$amount = $request->post('amount');

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


							\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
							\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Your GProCongress II refund has been processed.');

							$subject = '[GProCongress II Admin]  Payment refund';
							$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
							\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);
		
							// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
		
							return response(array("reset"=>true, "error"=>false, "message"=>'Manual payment refund successful'), 200);
			
						}else{
		
							return response(array("error"=>true, "message"=>'Payment not refund'), 403);
			
						}
						
					}
	
					
					
				} catch (\Exception $e) {
					return response(array("error"=>true, "message"=>$e->getMessage()), 403);
				}
			}

            
        }
        
    }

    public function sponsoredRefundAmount(Request $request){
 
        if($request->ajax()){

			$rules = [
				
				'amount' => 'required|numeric',
				'user_id' => 'required|numeric',
				'other_user' => 'required|in:Yes,No',
			];

			if($request->post('other_user') == 'Yes'){

				$rules['other_user_id'] = 'required|numeric';

			}else{

				$rules['reference_number'] = 'required';
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

					if($request->post('other_user') == 'Yes'){

						$transactionId=strtotime("now").rand(11,99);
	
						$orderId=strtotime("now").rand(11,99);
		
						$transaction = new \App\Models\Transaction();
						$transaction->user_id = $request->post('user_id');
						$transaction->bank = 'Wire';
						$transaction->order_id = $orderId;
						$transaction->transaction_id = $transactionId;
						$transaction->method = 'Manual';
						$transaction->amount = $request->post('amount');
						$transaction->payment_status = '3';
						$transaction->status = '3';
						$transaction->particular_id = '4';
						$transaction->save();
	
						$Wallet = new \App\Models\Wallet();
						$Wallet->user_id = $request->post('user_id');
						$Wallet->type  = 'Dr';
						$Wallet->amount = $request->post('amount');
						$Wallet->transaction_id = $transaction->id;
						$Wallet->status = 'Success';
						$Wallet->save();
		
						$user = \App\Models\User::where('id',$request->post('user_id'))->first();
						$user->status = '1';
						$user->save();

						$to = $user->email;
						$name = $user->name.' '.$user->last_name;
						$amount = $request->post('amount');
						$subject = 'Payment refund';
						$msg = 'Your '.$request->post('amount').' Payment refund  successfully';
						\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
	
						$subject = '[GProCongress II Admin]  Payment refund';
						$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
						\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);


						$transactionId=strtotime("now").rand(11,99);
	
						$orderId=strtotime("now").rand(11,99);
						
						$transaction = new \App\Models\Transaction();
						$transaction->user_id = $request->post('other_user_id');
						$transaction->bank = 'Wire';
						$transaction->order_id = $orderId;
						$transaction->transaction_id = $transactionId;
						$transaction->method = 'Manual';
						$transaction->amount = $request->post('amount');
						$transaction->payment_status = '2';
						$transaction->status = '1';
						$transaction->particular_id = '2';
						$transaction->save();
	
						$Wallet = new \App\Models\Wallet();
						$Wallet->user_id = $request->post('other_user_id');
						$Wallet->type  = 'Cr';
						$Wallet->amount = $request->post('amount');
						$Wallet->transaction_id = $transaction->id;
						$Wallet->status = 'Success';
						$Wallet->save();

					}else{

						$referenceNumberCheck = \App\Models\Transaction::where('bank_transaction_id',$request->post('reference_number'))->first();
						if($referenceNumberCheck){

							return response(array("error"=>true, "message"=>'Transaction already exists'), 403);

						}else{

							if($request->post('amount') > 0){
			
								$transactionId=strtotime("now").rand(11,99);
			
								$orderId=strtotime("now").rand(11,99);
				
								$transaction = new \App\Models\Transaction();
								$transaction->user_id = $request->post('user_id');
								$transaction->bank = 'Wire';
								$transaction->order_id = $orderId;
								$transaction->transaction_id = $transactionId;
								$transaction->method = 'Manual';
								$transaction->amount = $request->post('amount');
								$transaction->bank_transaction_id = $request->post('reference_number');
								$transaction->payment_status = '3';
								$transaction->status = '3';
								$transaction->particular_id = '4';
								$transaction->save();
			
								$Wallet = new \App\Models\Wallet();
								$Wallet->user_id = $request->post('user_id');
								$Wallet->type  = 'Dr';
								$Wallet->amount = $request->post('amount');
								$Wallet->transaction_id = $transaction->id;
								$Wallet->status = 'Success';
								$Wallet->save();
				
								$user = \App\Models\User::where('id',$request->post('user_id'))->first();
								$user->status = '1';
								$user->save();

								$to = $user->email;
								$subject = 'Payment refund';
								$msg = 'Your '.$request->post('amount').' Payment refund  successfully';
								\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
								\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
			
								$to = $user->email;
								$name = $user->name.' '.$user->last_name;
								$amount = $request->post('amount');
								
								$subject = '[GProCongress II Admin]  Payment refund';
								$msg = '<p><span style="font-size: 14px;">Hi,&nbsp;</span></p><p><span style="font-size: 14px;">Refund has been initiated for GProCongress II. Here are the candidate details:</span></p><p><span style="font-size: 14px;">Name: '.$name.'</span></p><p><span style="font-size: 14px;">Email: '.$to.'</span></p><p><span style="font-size: 14px;">Refund Amount : '.$amount.'</span></p><p><span style="font-size: 14px;">Regards,</span></p><p><span style="font-size: 14px;">Team GPro</span></p>';
								\App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

			
								// \App\Helpers\commonHelper::sendSMS($request->user()->mobile);
			
								return response(array("error"=>false, "message"=>'Manual payment refund successful'), 200);
				
							}else{
			
								return response(array("error"=>true, "message"=>'Amount should be grater then to zeo'), 403);
				
							}
							
						}
	
					}
					
				} catch (\Exception $e) {

					return response(array("error"=>true, "message"=>$e->getMessage()), 403);
				}
			}

            
        }
        
    }

	public function uploadDraftInformation(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){

			$rules = [
				'id' => 'numeric|required',
				'file' => 'required',
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

				$TravelInfo = \App\Models\TravelInfo::where('id',$request->post('id'))->first();

				if (!$TravelInfo) {

					return response(array("error"=>true, 'message'=>'Data not found'), 403);

				} else {

					$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.id',$request->post('id'))->first();
		
		
					if($request->hasFile('file')){
						$imageData = $request->file('file');
						$image = 'draft_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
						$destinationPath = public_path('/uploads/file');
						$imageData->move($destinationPath, $image);

						$TravelInfo->draft_file = $image;
					} 
					
					$TravelInfo->save();
					
					$to = $result->email;
					
					if($result->language == 'sp'){

						$subject = "Por favor, verifique su información de viaje";
						$msg = '<p>Estimado '.$result->name.' '.$result->last_name.' ,</p><p><br></p><p><br></p><p>Gracias por enviar su información de viaje.&nbsp;</p><p><br></p><p>A continuación, le adjuntamos una carta de solicitud de visa que hemos redactado a partir de la información recibida.&nbsp;</p><p><br></p><p>Por favor, revise la carta y luego haga clic en este enlace: &lt;enlace&gt; para verificar que la información es correcta.</p><p><br></p><p>Gracias por su colaboración.</p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
					
					}elseif($result->language == 'fr'){
					
						$subject = "Veuillez vérifier vos informations de voyage";
						$msg = "<p>Cher '.$result->name.' '.$result->last_name.',&nbsp;</p><p><br></p><p>Merci d’avoir soumis vos informations de voyage.&nbsp;&nbsp;</p><p><br></p><p>Veuillez trouver ci-joint une lettre de visa que nous avons rédigée basée sur les informations reçues.&nbsp;</p><p><br></p><p>Pourriez-vous s’il vous plaît examiner la lettre, puis cliquer sur ce lien: &lt;lien&gt; pour vérifier que les informations sont correctes.&nbsp;</p><p><br></p><p>Merci pour votre aide.</p><p><br></p><p>Cordialement,&nbsp;</p><p>L’équipe du GProCongrès II</p>";
			
					}elseif($result->language == 'pt'){
					
						$subject = "Por favor verifique sua Informação de Viagem";
						$msg = '<p>Prezado '.$result->name.' '.$result->last_name.',</p><p><br></p><p>Agradecemos por submeter sua informação de viagem</p><p><br></p><p>Por favor, veja a carta de pedido de visto em anexo, que escrevemos baseando na informação que recebemos.</p><p><br></p><p>Poderia por favor rever a carta, e daí clicar neste link: &lt;link&gt; para verificar que a informação esteja correta.&nbsp;</p><p><br></p><p>Agradecemos por sua ajuda.</p><p><br></p><p>Calorosamente,</p><p>Equipe do II CongressoGPro</p>';
					
					}else{
					
						$subject = 'Please submit your travel information.';
						$msg = '<p>Dear '.$result->name.' '.$result->last_name.',&nbsp;</p><p><br></p><p>We are excited to see you at the GProCongress at Panama City, Panama!</p><p><br></p><p>To assist delegates with obtaining visas, we are requesting they submit their travel information to&nbsp; us.&nbsp;</p><p><br></p><p>Please reply to this email with your flight information.&nbsp; Upon receipt, we will send you an email to confirm that the information we received is correct.</p><p><br></p><p>Warmly,</p><p>GProCongress II Team</p>';
											
					}

					$attachment = public_path('/uploads/file/'.$TravelInfo->draft_file);

					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, false, $attachment);
					\App\Helpers\commonHelper::userMailTrigger($result->id,$msg,$subject);
					
					return response(array('message'=>'Draft file has been sent successfully.'), 200);
				}

			}
			
		}

	}

	public function uploadFinalInformation(Request $request) {
		
		if($request->ajax() && $request->isMethod('post')){

			$rules = [
				'id' => 'numeric|required',
				'type' => 'numeric|required|in:1,2',
			];

			if($request->post('type') == '2'){

				$rules['file'] = 'required';
			}

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

				$TravelInfo = \App\Models\TravelInfo::where('id',$request->post('id'))->first();
				$attachment = '';
				$pdf = '';

				if (!$TravelInfo) {

					return response(array("error"=>true, 'message'=>'Data not found'), 403);

				} else {

					$result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.id',$request->post('id'))->first();
		
					$user = \App\Models\User::with('TravelInfo')->find($result->user_id);

					if($request->post('type') == '2'){

						if($request->hasFile('file')){
							$imageData = $request->file('file');
							$image = 'draft_'.strtotime(date('Y-m-d H:i:s')).'.'.$imageData->getClientOriginalExtension();
							$destinationPath = public_path('/uploads/file');
							$imageData->move($destinationPath, $image);

							$TravelInfo->final_file = $image;
						} 

						
						$attachment = public_path('/uploads/file/'.$TravelInfo->final_file);


					}else{

						$user = \App\Models\User::with('TravelInfo')->find($result->user_id);
						
						$pdfData = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$result->user_id)->first();

						
						$pdf = \PDF::loadView('email_templates.travel_info', $pdfData->toArray());

					}

					$user->stage = 4;
					$user->save();
					$to = $user->email;
					
					$resultSpouse = \App\Models\User::where('added_as','Spouse')->where('parent_id',$user->id)->first();
					
					if($resultSpouse){

						$resultSpouse->stage = 4;
						$resultSpouse->save();
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($resultSpouse->id,'Travel Info Approved','Travel Info Approved','Travel Info Approved',\Auth::user()->id);
						
					}

					$subject = 'Travel Info Approved';
					$msg = 'Your travel info has been approved successfully';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg, false, false, $pdf, $attachment);
					// \App\Helpers\commonHelper::sendSMS($user->mobile);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Travel Info Approved',\Auth::user()->id);
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					
					$subject = 'Session information ';
					$msg = 'Your Travel Information has been approved successfully, Please session information can be updated now';
					\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Session information',\Auth::user()->id);
					\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
					
					$TravelInfo->admin_status = '1';
					
					$TravelInfo->save();
					
					return response(array('message'=>'Visa letter approved successfully'), 200);
				}

			}
			
		}

	}

	public function cashPaymentSubmit(Request $request){
 
        if($request->ajax()){

            $rules = [
                'amount' => 'required|numeric',
                'user_id' => 'required|numeric',
                'remark' => 'string|required',
                'file' => 'required',
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

                    $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($request->post('user_id'), false); 
                    if($request->post('amount') <= $totalPendingAmount){

                        $transactionId=strtotime("now").rand(11,99);

                        $orderId=strtotime("now").rand(11,99); $image = '';
        
                        $transaction = new \App\Models\Transaction();
                        $transaction->user_id = $request->post('user_id');
                        $transaction->bank = 'Cash';
                        $transaction->order_id = $orderId;
                        $transaction->transaction_id = $transactionId;
                        $transaction->method = 'Offline';
                        $transaction->amount = $request->post('amount');
                        $transaction->description = $request->post('remark');
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
                        $Wallet->user_id = $request->post('user_id');
                        $Wallet->type  = 'Cr';
                        $Wallet->amount = $request->post('amount');
                        $Wallet->transaction_id = $transaction->id;
                        $Wallet->status = 'Pending';
                        $Wallet->save();
        
                        $user = \App\Models\User::where('id',$request->post('user_id'))->first();

                        $to = $user->email;
                        $name = $user->name.' '.$user->last_name;
                        $amount = $request->post('amount');
                        
						$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($user->id, true);
						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($user->id, true);
						$totalAmountInProcess = \App\Helpers\commonHelper::getTotalAmountInProcess($user->id, true);
						$totalRejectedAmount = \App\Helpers\commonHelper::getTotalRejectedAmount($user->id, true);
						
						if($user->language == 'sp'){
			
							$subject = 'Pago recibido. ¡Gracias!';
							$msg = '<p>Estimado  '.$name.' ,&nbsp;</p><p><br></p>
									<p>Se ha recibido la cantidad de $'.$amount.' en su cuenta.  </p><p><br></p>
									<p>Gracias por hacer este pago.</p><p> <br></p>
									<p>Le notificaremos tan pronto como el pago sea aprobado en nuestro sistema. Hasta entonces, este pago se reflejará como Pago en proceso.</p><p> <br></p>
									<p>Aquí tiene un resumen actual del estado de su pago:</p>
									<p>IMPORTE TOTAL A PAGAR:'.$user->amount.'</p>
									<p>PAGOS REALIZADOS Y ACEPTADOS ANTERIORMENTE:'.$totalAcceptedAmount.'</p>
									<p>PAGOS ACTUALMENTE EN PROCESO:'.$totalAmountInProcess.'</p>
									<p>SALDO PENDIENTE DE PAGO:'.$totalPendingAmount.'</p><p><br></p>
									<p style="background-color:yellow; display: inline;"> <i><b>El descuento por “inscripción anticipada” finalizó el 31 de mayo. Si paga la totalidad antes del 30 de junio, aún puede aprovechar $100 de descuento en el costo regular de inscripción. Desde el 1 de julio hasta el 31 de agosto, se utilizará la tarifa de inscripción regular completa, que es $100 más que la tarifa de reserva anticipada.</b></i></p>
									<p style="color"red"><i>IMPORTANTE: Si el pago en su totalidad no se recibe antes del 31 de agosto de 2023, se cancelará su inscripción, su lugar será cedido a otra persona y perderá los fondos que haya abonado con anterioridad.</i></p><p><br></p>
									<p>Si tiene alguna pregunta sobre el proceso de la visa, responda a este correo electrónico para hablar con uno de los miembros de nuestro equipo.</p>
									<p>Por favor, ore con nosotros en nuestro esfuerzo por multiplicar el número de capacitadores de pastores y desarrollar sus competencias.</p><p><br></p>
									<p>Atentamente,</p>
									<p>El equipo del GProCongress II</p>';
			
						}elseif($user->language == 'fr'){
						
							$subject = 'Paiement intégral.  Merci !';
							$msg = '<p>Cher '.$name.' ,&nbsp;</p><p><br></p>
							<p>Un montant de '.$amount.'$ a été reçu sur votre compte.  </p><p><br></p>
							<p>Merci d’avoir effectué ce paiement.</p><p> <br></p>
							<p>Nous vous informerons dès que le paiement sera approuvé dans notre système. Jusqu’à ce moment-là, ce paiement apparaîtra comme un paiement en cours de traitement.</p>
							<p>Voici un résumé de l’état de votre paiement :</p>
							<p>MONTANT TOTAL À PAYER:'.$user->amount.'</p>
							<p>PAIEMENTS DÉJÀ EFFECTUÉS ET ACCEPTÉS:'.$totalAcceptedAmount.'</p>
							<p>PAIEMENTS EN COURS:'.$totalAmountInProcess.'</p>
							<p>SOLDE RESTANT DÛ:'.$totalPendingAmount.'</p><p><br></p>
							<p style="background-color:yellow; display: inline;"><i><b>Le rabais de « l’inscription anticipée » a pris fin le 31 mai. Si vous payez en totalité avant le 30 juin, vous pouvez toujours profiter de 100 $ de rabais sur le tarif d’inscription régulière. Du 1er juillet au 31 août, le plein tarif d’inscription régulière sera expiré, soit 100 $ de plus que le tarif d’inscription anticipée.</b></i></p>
								<p style="color"red"><i>VEUILLEZ NOTER : Si le paiement complet n’est pas reçu avant le 31 août 2023, votre inscription sera annulée, votre place sera donnée à quelqu’un d’autre et tous les fonds que vous auriez déjà payés seront perdus.</i></p><p><br></p>
							<p>Si vous avez des questions concernant votre paiement, répondez simplement à cet e-mail et notre équipe communiquera avec vous.   </p>
							<p>Priez avec nous, alors que nous nous efforçons de multiplier les nombres et de renforcer les capacités des formateurs de pasteurs.</p><p><br></p>
							<p>Cordialement,</p><p>L’équipe du GProCongrès II</p>';
			
						}elseif($user->language == 'pt'){
						
							$subject = 'Pagamento recebido. Obrigado!';
							$msg = '<p>Prezado  '.$name.' ,&nbsp;</p><p><br></p>
									<p>Uma quantia de $'.$amount.' foi recebido na sua conta.  </p><p><br></p>
									<p>Obrigado por ter efetuado esse pagamento.</p><p> <br></p>
									<p>Iremos notificá-lo assim que o pagamento for aprovado no nosso sistema. Até lá, este pagamento reflectirá como Pagamento em Processo.</p><p> <br></p>
									<p>Aqui está o resumo do estado do seu pagamento:</p>
									<p>VALOR TOTAL A SER PAGO:'.$user->amount.'</p>
									<p>PAGAMENTO PREVIAMENTE FEITO E ACEITE:'.$totalAcceptedAmount.'</p>
									<p>PAGAMENTO ATUALMENTE EM PROCESSO:'.$totalAmountInProcess.'</p>
									<p>SALDO REMANESCENTE EM DÍVIDA:'.$totalPendingAmount.'</p><p><br></p>
									<p style="background-color:yellow; display: inline;"><i><b>O desconto “early bird” terminou em 31 de maio. Se você pagar integralmente até 30 de junho, ainda poderá aproveitar o desconto de $100 na taxa de registro regular. De 1º de julho a 31 de agosto, será  cobrado o valor de inscrição regular completa, que é $100 a mais do que a taxa de inscrição antecipada.</b></i></p>
									<p ><i>POR FAVOR NOTE: Se o pagamento integral não for recebido até 31 de Agosto de 2023, a sua inscrição será cancelada, o seu lugar será dado a outra pessoa, e quaisquer valor  previamente pagos por si serão retidos.</i></p><p><br></p>
									<p>Se você tem alguma pergunta sobre o seu pagamento, Simplesmente responda a este e-mail, e nossa equipe ira se conectar com você. </p>
									<p>Ore conosco a medida que nos esforçamos para multiplicar os números e desenvolvemos a capacidade dos treinadores de pastores.</p>
									<p><br></p><p>Calorosamente,</p>
									<p>A Equipe do II CongressoGPro</p>';
			
						}else{
						
							$subject = 'Payment received. Thank you!';
							$msg = '<p>Dear '.$name.' ,&nbsp;</p><p><br></p>
									<p>An amount of $'.$amount.' has been received on your account.  </p><p><br></p>
									<p>Thank you for making this payment.</p><p> <br></p>
									<p>We will notify you as soon as the payment is approved in our system. Until then, this payment will reflect as Payment in Process.</p><p> <br></p>
									<p>Here is a summary of your payment status:</p>
									<p>TOTAL AMOUNT TO BE PAID:'.$user->amount.'</p>
									<p>PAYMENTS PREVIOUSLY MADE AND ACCEPTED:'.$totalAcceptedAmount.'</p>
									<p>PAYMENTS CURRENTLY IN PROCESS:'.$totalAmountInProcess.'</p>
									<p>REMAINING BALANCE DUE:'.$totalPendingAmount.'</p><p><br></p>
									<p style="background-color:yellow; display: inline;"><i><b>The “early bird” discount ended on May 31. If you pay in full by June 30, you can still take advantage of $100 off the Regular Registration rate. From July 1 to August 31, the full Regular Registration rate will be used, which is $100 more than the early bird rate.</b></i></p>
									<p ><i>PLEASE NOTE: If full payment is not received by August 31, 2023, your registration will be cancelled, your spot will be given to someone else, and any funds previously paid by you will be forfeited.</i></p><p><br></p>
									<p>If you have any questions about your payment, simply respond to this email, and our team will connect with you.  </p>
									<p>Pray with us, as we endeavour to multiply the numbers, and build the capacities of pastor trainers.</p><p><br></p>
									<p>Warmly,</p>
									<p>GProCongress II Team</p>';
			
						}
			
						$files = [
							public_path('uploads/transaction/'.$image),
						];
				
						\Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files) {
							$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
							$message->subject($subject);
							$message->to($to);
							
							foreach ($files as $file){
								$message->attach($file);
							}
							
						});
			
						\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
						\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id, $subject, $msg,  $subject,);
			

                        $subject = '[GProCongress II Admin] || Payment Received';
                        $msg = '<p><span style="font-size: 14px;"><font color="#000000">Hi,&nbsp;</font></span></p><p><span style="font-size: 14px;"><font color="#000000">'.$name.' has made Payment of&nbsp; '.$amount.' for GProCongress II. Here are the candidate details:</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Name: '.$name.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Email: '.$to.'</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Payment Mode: Cash</font></span></p><p><span style="font-size: 14px;"><font color="#000000"><br></font></span></p><p><span style="font-size: 14px;"><font color="#000000">Regards,</font></span></p><p><span style="font-size: 14px;"><font color="#000000">Team GPro</font></span></p>';
                        \App\Helpers\commonHelper::emailSendToAdmin($subject, $msg);

                        // \App\Helpers\commonHelper::sendSMS($request->user()->mobile);

                        
                        return response(array("error"=>false, "message"=>'Cash payment added successful'), 200);
        
                    }else{

                        return response(array("error"=>true, "message"=>'payment already paid'), 403);
        
                    }
                      
                } catch (\Exception $e) {
                    return response(array("error"=>true, "message"=>$e->getMessage()), 403);
                }
            }

        }
        
    }

	public function archiveUser(Request $request,$id){
	
		try{

			$exitsUser = \App\Models\User::find($id);

			$userSpouse = \App\Models\User::where('parent_id',$id)->get();

			if(!empty($userSpouse) && count($userSpouse)>0){

				$request->session()->flash('5fernsadminerror','User not delete! this user comming with spouse/ group');
				return redirect()->back();

			}else{

				$user = new \App\Models\ArchiveUser();

				$user->user_id=$exitsUser->id;
				$user->parent_id=$exitsUser->parent_id;
				$user->added_as=$exitsUser->added_as;
				$user->salutation=$exitsUser->salutation;
				$user->name=$exitsUser->name;
				$user->last_name=$exitsUser->last_name;
				$user->email=$exitsUser->email;
				$user->phone_code=$exitsUser->phone_code;
				$user->mobile=$exitsUser->mobile;
				$user->reg_type=$exitsUser->reg_type;
				$user->status=$exitsUser->status;
				$user->status_change_at=$exitsUser->status_change_at;
				$user->profile_status=$exitsUser->profile_status;
				$user->remark=$exitsUser->remark;
				$user->user_type=$exitsUser->user_type;
				$user->otp_verified=$exitsUser->otp_verified;
				$user->otp=$exitsUser->otp;
				$user->password=$exitsUser->password;
				$user->gender=$exitsUser->gender;
				$user->dob=$exitsUser->dob;
				$user->citizenship=$exitsUser->citizenship;
				$user->marital_status=$exitsUser->marital_status;
				$user->contact_address=$exitsUser->contact_address;
				$user->contact_zip_code=$exitsUser->contact_zip_code;
				$user->contact_country_id=$exitsUser->contact_country_id;
				$user->contact_state_id=$exitsUser->contact_state_id;
				$user->contact_city_id=$exitsUser->contact_city_id;
				$user->contact_business_codenumber=$exitsUser->contact_business_codenumber;
				$user->contact_business_number=$exitsUser->contact_business_number;
				$user->contact_whatsapp_codenumber=$exitsUser->contact_whatsapp_codenumber;
				$user->contact_whatsapp_number=$exitsUser->contact_whatsapp_number;
				$user->ministry_name=$exitsUser->ministry_name;
				$user->ministry_address=$exitsUser->ministry_address;
				$user->ministry_zip_code=$exitsUser->ministry_zip_code;
				$user->ministry_country_id=$exitsUser->ministry_country_id;
				$user->ministry_state_id=$exitsUser->ministry_state_id;
				$user->ministry_city_id=$exitsUser->ministry_city_id;
				$user->ministry_pastor_trainer=$exitsUser->ministry_pastor_trainer;
				$user->ministry_pastor_trainer_detail=$exitsUser->ministry_pastor_trainer_detail;
				$user->doyouseek_postoral=$exitsUser->doyouseek_postoral;
				$user->doyouseek_postoralcomment=$user->doyouseek_postoralcomment;
				$user->stage=$exitsUser->stage;
				$user->designation_id=$exitsUser->designation_id;
				$user->profile_update=$exitsUser->profile_update;
				$user->profile_updated_at=$exitsUser->profile_updated_at;
				$user->terms_and_condition=$exitsUser->terms_and_condition;
				$user->amount=$exitsUser->amount;
				$user->payment_status=$exitsUser->payment_status;
				$user->social_id=$exitsUser->social_id;
				$user->room=$exitsUser->room;
				$user->system_generated_password=$exitsUser->system_generated_password;
				$user->change_room_type=$exitsUser->change_room_type;
				$user->upgrade_category=$exitsUser->upgrade_category;
				$user->early_bird=$exitsUser->early_bird;
				$user->offer_id=$exitsUser->offer_id;
				$user->remember_token=$exitsUser->remember_token;
				$user->created_at=$exitsUser->created_at;
				$user->updated_at=$exitsUser->updated_at;
				$user->deleted_at=$exitsUser->deleted_at;
				$user->qrcode=$exitsUser->qrcode;
				$user->ministry_city_name=$exitsUser->ministry_city_name;
				$user->ministry_state_name=$exitsUser->ministry_state_name;
				$user->contact_city_name=$exitsUser->contact_city_name;
				$user->profile_submit_type=$exitsUser->profile_submit_type;
				$user->spouse_confirm_token=$exitsUser->spouse_confirm_token;
				$user->spouse_confirm_status=$exitsUser->spouse_confirm_status;
				$user->willing_to_commit=$exitsUser->willing_to_commit;
				$user->comment=$exitsUser->comment;
				$user->envision_training=$exitsUser->envision_training;
				$user->spouse_confirm_reminder_email=$exitsUser->spouse_confirm_reminder_email;
				$user->share_your_room_with=$exitsUser->share_your_room_with;
				$user->language=$exitsUser->language;
				$user->cash_payment_option=$exitsUser->cash_payment_option;
				$user->spouse_id=$exitsUser->spouse_id;

				$user->save();
				
				if ($exitsUser) {

					\App\Models\User::where('id',$id)->forceDelete();
					
					$request->session()->flash('5fernsadminsuccess','User deleted successfully.');

				}else{

					$request->session()->flash('5fernsadminerror','Something went wrong. Please try again.');
				}
					
				return redirect()->back();
				
			}
			
				
			
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
		}
	}

	public function userMailTriggerList(Request $request) {
	
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
				
			if (\Auth::user()->designation_id == '1') {
				return '<div >
							<button type="button" style="width:41px" title="View message" class="btn btn-sm btn-primary px-3 m-1 text-white messageGet" data-id="'.$data->id.'" ><i class="fas fa-eye"></i></button>
						</div>';			
			}
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);
	
	}
	
	public function userMailTriggerListModel(Request $request) {
	
		if($request->ajax()){

			$UserMailTrigger = \App\Models\UserMailTrigger::where('id', $request->id)->first();

			return response(array('message'=>$UserMailTrigger->message), 200);

		}

		
	
	}

	public function userRecover(Request $request){
	
		try{

			if($request->ajax()){

				if($request->post('type') == '2'){

					$usertable = \App\Models\User::where('email','=',$request->post('email'))->first();
					if($usertable){

						if($usertable->stage != '2'){

							return response(array('message'=>'User stage moved not allowed'),403);
						}

						$userSpouse = \App\Models\User::where('parent_id',$usertable->id)->where('added_as','Spouse')->first();

						if($userSpouse){

							$userSpouse->profile_status='Review';
							$userSpouse->stage= '1';
							$userSpouse->amount= '0';
							$userSpouse->save();

							\App\Helpers\commonHelper::sendNotificationAndUserHistory($userSpouse->id,'Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1');


						}

						
						$userHus = \App\Models\User::where('id',$usertable->parent_id)->first();

						if($userHus){

							$userHus->profile_status='Review';
							$userHus->stage= '1';
							$userHus->amount= '0';
							$userHus->save();

							\App\Helpers\commonHelper::sendNotificationAndUserHistory($userHus->id,'Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1');


						}

						$usertable->profile_status='Review';
						$usertable->stage= '1';
						$usertable->amount= '0';
						$usertable->save();

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($usertable->id,'Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1','Move user from Stage2 to Stage-1');

						return response(array('message'=>'User stage move successfully','reset'=>true),200);

					}else{

						return response(array('message'=>'User does not exist'),403);
					}

				}elseif($request->post('type') == '1'){

					$usertable = \App\Models\User::where('email','=',$request->post('email'))->first();

					if($usertable){

						return response(array('message'=>'User already exist'),403);


					}else{

						$exitsUser = \App\Models\ArchiveUser::where('email',$request->post('email'))->first();

						if($exitsUser){

							$user = new \App\Models\User();

							$user->parent_id=$exitsUser->parent_id;
							$user->added_as=$exitsUser->added_as;
							$user->salutation=$exitsUser->salutation;
							$user->name=$exitsUser->name;
							$user->last_name=$exitsUser->last_name;
							$user->email=$exitsUser->email;
							$user->phone_code=$exitsUser->phone_code;
							$user->mobile=$exitsUser->mobile;
							$user->reg_type=$exitsUser->reg_type;
							$user->status=$exitsUser->status;
							$user->status_change_at=$exitsUser->status_change_at;
							$user->profile_status=$exitsUser->profile_status;
							$user->remark=$exitsUser->remark;
							$user->user_type=$exitsUser->user_type;
							$user->otp_verified=$exitsUser->otp_verified;
							$user->otp=$exitsUser->otp;
							$user->password=$exitsUser->password;
							$user->gender=$exitsUser->gender;
							$user->dob=$exitsUser->dob;
							$user->citizenship=$exitsUser->citizenship;
							$user->marital_status=$exitsUser->marital_status;
							$user->contact_address=$exitsUser->contact_address;
							$user->contact_zip_code=$exitsUser->contact_zip_code;
							$user->contact_country_id=$exitsUser->contact_country_id;
							$user->contact_state_id=$exitsUser->contact_state_id;
							$user->contact_city_id=$exitsUser->contact_city_id;
							$user->contact_business_codenumber=$exitsUser->contact_business_codenumber;
							$user->contact_business_number=$exitsUser->contact_business_number;
							$user->contact_whatsapp_codenumber=$exitsUser->contact_whatsapp_codenumber;
							$user->contact_whatsapp_number=$exitsUser->contact_whatsapp_number;
							$user->ministry_name=$exitsUser->ministry_name;
							$user->ministry_address=$exitsUser->ministry_address;
							$user->ministry_zip_code=$exitsUser->ministry_zip_code;
							$user->ministry_country_id=$exitsUser->ministry_country_id;
							$user->ministry_state_id=$exitsUser->ministry_state_id;
							$user->ministry_city_id=$exitsUser->ministry_city_id;
							$user->ministry_pastor_trainer=$exitsUser->ministry_pastor_trainer;
							$user->ministry_pastor_trainer_detail=$exitsUser->ministry_pastor_trainer_detail;
							$user->doyouseek_postoral=$exitsUser->doyouseek_postoral;
							$user->doyouseek_postoralcomment=$user->doyouseek_postoralcomment;
							$user->stage=$exitsUser->stage;
							$user->designation_id=$exitsUser->designation_id;
							$user->profile_update=$exitsUser->profile_update;
							$user->profile_updated_at=$exitsUser->profile_updated_at;
							$user->terms_and_condition=$exitsUser->terms_and_condition;
							$user->amount=$exitsUser->amount;
							$user->payment_status=$exitsUser->payment_status;
							$user->social_id=$exitsUser->social_id;
							$user->room=$exitsUser->room;
							$user->system_generated_password=$exitsUser->system_generated_password;
							$user->change_room_type=$exitsUser->change_room_type;
							$user->upgrade_category=$exitsUser->upgrade_category;
							$user->early_bird=$exitsUser->early_bird;
							$user->offer_id=$exitsUser->offer_id;
							$user->remember_token=$exitsUser->remember_token;
							$user->created_at=$exitsUser->created_at;
							$user->updated_at=$exitsUser->updated_at;
							$user->deleted_at=$exitsUser->deleted_at;
							$user->qrcode=$exitsUser->qrcode;
							$user->ministry_city_name=$exitsUser->ministry_city_name;
							$user->ministry_state_name=$exitsUser->ministry_state_name;
							$user->contact_city_name=$exitsUser->contact_city_name;
							$user->profile_submit_type=$exitsUser->profile_submit_type;
							$user->spouse_confirm_token=$exitsUser->spouse_confirm_token;
							$user->spouse_confirm_status=$exitsUser->spouse_confirm_status;
							$user->willing_to_commit=$exitsUser->willing_to_commit;
							$user->comment=$exitsUser->comment;
							$user->envision_training=$exitsUser->envision_training;
							$user->spouse_confirm_reminder_email=$exitsUser->spouse_confirm_reminder_email;
							$user->share_your_room_with=$exitsUser->share_your_room_with;
							$user->language=$exitsUser->language;
							$user->cash_payment_option=$exitsUser->cash_payment_option;
							$user->spouse_id=$exitsUser->spouse_id;

							$user->save();
						
							if ($exitsUser) {

								\App\Models\ArchiveUser::where('id',$exitsUser->id)->forceDelete();
								
								return response(array('message'=>'User Recover successfully.','reset'=>true),200);


							}else{

								return response(array('message'=>'Something went wrong. Please try again'),403);
							}

						}else{

							return response(array('message'=>'User does not exist'),403);

						}
						
					}
				}elseif($request->post('type') == '3'){

					$usertable = \App\Models\User::where('email','=',$request->post('email'))->first();
					if($usertable){

						if($usertable->stage > '1'){

							return response(array('message'=>'User shall be in Stage 1'),403);
						}

						$userSpouse = \App\Models\User::where('parent_id',$usertable->id)->where('added_as','Spouse')->first();

						if($userSpouse){

							$userSpouse->parent_id=null;
							$userSpouse->added_as= null;
							$userSpouse->room= 'Sharing';
							$userSpouse->spouse_confirm_token= null;
							$userSpouse->spouse_confirm_status= 'Pending';
							$userSpouse->spouse_confirm_reminder_email= '';
							$userSpouse->save();

							\App\Helpers\commonHelper::sendNotificationAndUserHistory($userSpouse->id,'Separating Couples','Separating Couples','Separating Couples');


							$usertable->parent_id=null;
							$usertable->added_as= null;
							$usertable->room= 'Sharing';
							$usertable->spouse_confirm_token= null;
							$usertable->spouse_confirm_status= 'Pending';
							$usertable->spouse_confirm_reminder_email= '';
							$usertable->save();

							\App\Helpers\commonHelper::sendNotificationAndUserHistory($usertable->id,'Separating Couples','Separating Couples','Separating Couples');

							
						}

						$userHus = \App\Models\User::where('id',$usertable->parent_id)->first();

						if($userHus){

							$userHus->parent_id=null;
							$userHus->added_as= null;
							$userHus->room= 'Sharing';
							$userHus->spouse_confirm_token= null;
							$userHus->spouse_confirm_status= 'Pending';
							$userHus->spouse_confirm_reminder_email= '';
							$userHus->save();
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($userHus->id,'Separating Couples','Separating Couples','Separating Couples');

							$usertable->parent_id=null;
							$usertable->added_as= null;
							$usertable->room= 'Sharing';
							$usertable->spouse_confirm_token= null;
							$usertable->spouse_confirm_status= 'Pending';
							$usertable->spouse_confirm_reminder_email= '';
							$usertable->save();
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($usertable->id,'Separating Couples','Separating Couples','Separating Couples');

						}

						return response(array('message'=>'User update successfully','reset'=>true),200);

					}else{

						return response(array('message'=>'User does not exist'),403);
					}

				}elseif($request->post('type') == '4'){

					if($request->isMethod('post')){

						$rules = [
							'ministry_name' => 'required',
							'ministry_zip_code' => 'required',
							'ministry_address' => 'required',
							'ministry_country_id' => 'required',
							'ministry_state_id' => 'required',
							'ministry_city_id' => 'required',
							'ministry_pastor_trainer' => 'required|in:Yes,No',
						];

						if($request->post('ministry_state_id')=='0'){
	
							$rules['ministry_state_name'] = 'required|string';
			
						}
						if($request->post('ministry_city_id')=='0'){
			
							$rules['ministry_city_name'] = 'required|string';
			
						}

						if($request->post('ministry_pastor_trainer')=='Yes'){

							$rules['non_formal_trainor'] = 'required';
							$rules['formal_theological'] = 'required|string';
							$rules['informal_personal'] = 'required|string';
							$rules['howmany_pastoral'] = 'required|string';

						}else{

							$rules['pastorno'] = 'required|in:Yes,No';
						}

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
		
							$data=\App\Models\User::where('email','=',$request->post('email'))->first();
		
							if(!$data){

								return response(array('message'=>'User not found.'),403);
							}
							$data->ministry_name = $request->post('ministry_name');
							$data->ministry_zip_code = $request->post('ministry_zip_code');
							$data->ministry_address = $request->post('ministry_address');
							$data->ministry_country_id = $request->post('ministry_country_id');
							$data->ministry_state_id = $request->post('ministry_state_id');
							$data->ministry_city_id = $request->post('ministry_city_id');
							$data->ministry_pastor_trainer = $request->post('ministry_pastor_trainer');
							
							if($request->post('ministry_state_id')=='0'){

								$data->ministry_state_name = $request->post('ministry_state_name');
				
							}
							if($request->post('ministry_city_id')=='0'){
	
								$data->ministry_city_name = $request->post('ministry_city_name');
				
							}

							if($request->post('ministry_pastor_trainer')=='No'){

								$data->ministry_pastor_trainer_detail = Null;
								$data->doyouseek_postoral = $request->post('pastorno');
								$data->doyouseek_postoralcomment = $request->post('doyouseek_postoral_comment'); 

							}else{

								$dataMin=array(
									'non_formal_trainor'=>$request->post('non_formal_trainor'),
									'formal_theological'=>$request->post('formal_theological'),
									'informal_personal'=>$request->post('informal_personal'),
									'howmany_pastoral'=>$request->post('howmany_pastoral'),
									'howmany_futurepastor'=>$request->post('howmany_futurepastor'), 
									'comment'=>$request->post('comment') ?? '', 
									'willing_to_commit'=>$request->post('willing_to_commit') ?? '', 
								);
			
								$data->ministry_pastor_trainer_detail = json_encode($dataMin); 

							}

							$data->save();
							\App\Helpers\commonHelper::sendNotificationAndUserHistory($data->id,'Ministry details upadted by admin ','Ministry details upadted by admin','Ministry details upadted by admin');

							
							return response(array('message'=>'Ministry details upadted successfull.','ministryUpdate'=>true),200);
						}
			
					}
					
				}elseif($request->post('type') == '5'){

					$approvedNotComing = \App\Models\User::where('email','=',$request->post('email'))->first();

					
					if(!$approvedNotComing){

						return response(array('message'=>'User not found.'),403);
					}

					$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($approvedNotComing->id, true);
					
					if($totalAcceptedAmount>0){

						return response(array('message'=>'Not Allowed this process .'),403);
					}

					$userSpouse = \App\Models\User::where('parent_id',$approvedNotComing->id)->where('added_as','Spouse')->first();

					if($userSpouse){
						
						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($userSpouse->id, true);
					
						if($totalAcceptedAmount>0){

							return response(array('message'=>'Not Allowed this process .'),403);
						}
						$userSpouse->parent_id=null;
						$userSpouse->added_as= null;
						$userSpouse->room= 'Sharing';
						$userSpouse->spouse_confirm_token= null;
						$userSpouse->spouse_confirm_status= 'Pending';
						$userSpouse->spouse_confirm_reminder_email= '';
						$userSpouse->profile_status='ApprovedNotComing';
						$userSpouse->stage='1';
						$userSpouse->save();

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($userSpouse->id,'Approved Not Coming','Approved Not Coming','Approved Not Coming');

					}

					$userHus = \App\Models\User::where('id',$approvedNotComing->parent_id)->first();

					if($userHus){
						
						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($userHus->id, true);
					
						if($totalAcceptedAmount>0){

							return response(array('message'=>'Not Allowed this process .'),403);
						}
						$userHus->parent_id=null;
						$userHus->added_as= null;
						$userHus->room= 'Sharing';
						$userHus->spouse_confirm_token= null;
						$userHus->spouse_confirm_status= 'Pending';
						$userHus->spouse_confirm_reminder_email= '';
						$userHus->profile_status='ApprovedNotComing';
						$userHus->stage='1';
						$userHus->save();

						\App\Helpers\commonHelper::sendNotificationAndUserHistory($userHus->id,'Approved Not Coming','Approved Not Coming','Approved Not Coming');

					}

					$approvedNotComing->parent_id=null;
					$approvedNotComing->added_as= null;
					$approvedNotComing->room= 'Sharing';
					$approvedNotComing->spouse_confirm_token= null;
					$approvedNotComing->spouse_confirm_status= 'Pending';
					$approvedNotComing->spouse_confirm_reminder_email= '';
					$approvedNotComing->profile_status='ApprovedNotComing';
					$approvedNotComing->stage='1';
					$approvedNotComing->save();

					\App\Helpers\commonHelper::sendNotificationAndUserHistory($approvedNotComing->id,'Approved Not Coming','Approved Not Coming','Approved Not Coming');

					return response(array('message'=>'Approved Not Coming status chnage successfull.','ministryUpdate'=>true),200);

				}

			}
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
		}

		if(\Auth::user()->email ==  'german@gprocongress.org' || \Auth::user()->email == 'admin@gmail.com'){

			$result=[];
        	return view('admin.user.user_recover',compact('result'));
		}else{
			return redirect()->back()->with(['5fernsadminerror'=>"Access Denied"]);

		}
		

	}

	public function TransationDataExport(Request $request){
	
		try{
			
			$delimiter = ","; 
			$filename = "transactiondata-data_" . date('Y-m-d') . ".csv"; 
			
			$f = fopen('php://memory', 'w'); 
			
			$fields = array('ID', 'Name', 'Payment By', 'Country of Sender', 'Payment Type', 'Transaction Id', 'UTR No', 'Amount' , 'Payment Status','Date & Time','Decline Remark'); 
			fputcsv($f, $fields, $delimiter); 
			
			$query = \App\Models\Transaction::orderBy('id','desc');

			$data = $query->get();
			foreach($data as $key=>$data){
				
				$lineData = array($key+1, \App\Helpers\commonHelper::getUserNameById($data->user_id), $data->bank, $data->country_of_sender, $data->method, $data->order_id, $data->bank_transaction_id, $data->amount,\App\Helpers\commonHelper::getPaymentStatusName($data->payment_status),$data->created_at,$data->decline_remark); 
				fputcsv($f, $lineData, $delimiter); 
			}
			
			fseek($f, 0); 
			
			header('Content-Type: text/csv'); 
			header('Content-Disposition: attachment; filename="' . $filename . '";'); 
			
			fpassthru($f); 

									
		}catch (\Exception $e){
			
			return response(array("error"=>true, "message" => $e->getMessage()),200); 
		}


	}

    public function getMinistryData(Request $request) {
		
		if($request->ajax()){
			
			$result = \App\Models\User::where('email',$request->get('emailId'))->where('stage','1')->first();
			if($result){

				$country  = \App\Models\Country::get();

				$html = view('admin.user.ministery_update_render', compact('result','country'))->render();
				
				return response(array("error"=>false, 'message'=>'Data fetch success','html'=>$html), 200);
	
			}else{

				return response(array("error"=>true, 'message'=>'Candidate not found in Stage-1','html'=>''), 403);
	
			}


		}
		

	}

	public function passportList(Request $request, $countryType, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('passport_infos');
			
			$limit = $request->input('length');
			$start = $request->input('start');

			
			
			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\PassportInfo::select('passport_infos.*','users.language')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','!=','Approved')->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			if($countryType  == 'no-visa-needed'){
			
				$query->where('passport_infos.visa_category','No Visa Needed');

			}elseif($countryType  == 'visa-needed'){

				$query->where('passport_infos.visa_category', 'Visa Needed');

			}elseif($countryType  == 'restricted'){

				$query->where('passport_infos.visa_category', 'Restricted Country');

			}

			$data = $query->offset($start)->limit($limit)->get();
			$totalData1 = \App\Models\PassportInfo::select('passport_infos.*','users.language')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','!=','Approved')->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			if($countryType  == 'no-visa-needed'){

				$totalData1->where('passport_infos.visa_category','No Visa Needed');

			}elseif($countryType  == 'visa-needed'){

				$totalData1->where('passport_infos.visa_category', 'Visa Needed');

			}elseif($countryType  == 'restricted'){

				$totalData1->where('passport_infos.visa_category', 'Restricted Countr');

			}
			
			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->salutation.' '.$data->name;
		    })

			->addColumn('passport_no', function($data){
				return $data->passport_no;
			})

			->addColumn('country_id', function($data){
				return \App\Helpers\commonHelper::getCountry2NameById($data->country_id);
		    })

			->addColumn('passport_valid', function($data){
				return $data->passport_valid;
		    })


			->addColumn('passport_copy', function($data){

				if($data->passport_copy!= ''){
					$html = '';
					foreach(explode(",",$data->passport_copy) as $key=>$img){

						$html.='<a style="color:blue !important" href="'.asset('/uploads/passport/'.$img).'" target="_blank"> 
								View '.($key+1).' </span>
							</a>,</br>';

					}
				}

				$html = rtrim($html, ",</br>");
				
				return $html;

		    })

			->addColumn('valid_residence_country', function($data){

				$countryDoc = json_decode($data->valid_residence_country,true);
				$html = '';

				foreach($countryDoc as $key=>$img){

					if($img['id'] == '15'){
						$html.='<a style="color:blue !important" href="'.asset('/uploads/passport/'.$img['file']).'" target="_blank"> 
						European Union
							</a>,</br>';
					}else{
						$html.='<a style="color:blue !important" href="'.asset('/uploads/passport/'.$img['file']).'" target="_blank"> 
								'.\App\Helpers\commonHelper::getCountryNameById($img['id']).'
							</a>,</br>';
					}
				}
					
				$html = rtrim($html, ",</br>");
				return $html;
		    })

			->addColumn('remark', function($data){

				return '<button type="button" class="btn btn-sm btn ViewPassportRemark" data-remark="'.$data->admin_remark.'"> View </button>';
				
		    })

			
			->addColumn('status', function($data) use($countryType){

				if ($data->admin_status == 'Approved') {

					return '<div class="badge rounded-pill pill-badge-success">Passport Info Approved</div>';

				} else if ($data->admin_status == 'Decline') {

					return '<div class="badge rounded-pill pill-badge-danger">Passport Info Returned</div>';

				} else if ($data->admin_status === 'Pending') {

					return '<div class="badge rounded-pill pill-badge-warning">Passport Info Submitted</div>';
					
				}


		    })

			->addColumn('admin_status', function($data) use($countryType){

				$lang = 'English';
				if($data->language == 'sp'){
					$lang = 'Spanish';
				}elseif($data->language == 'fr'){
					$lang = 'French';
				}elseif($data->language == 'pt'){
					$lang = 'French';
				}else{
					$lang = 'English';
				}
						
				if ($data->admin_status == 'Approved') {

					return '<div class="badge rounded-pill pill-badge-success">Approved</div>';

				} else if ($data->admin_status == 'Decline') {

					return '<div class="badge rounded-pill pill-badge-danger">Decline</div>';

				} else if ($data->admin_status === 'Pending') {

					if(\Auth::user()->designation_id == '1' || \Auth::user()->designation_id == 16){

						if($countryType  == 'restricted'){

							return '<div style="display:flex">
							<a data-id="'.$data->id.'" data-type="1" title="Passport Approve" class="btn btn-sm btn-outline-success m-1 passportApproveRestricted">Approve</a>
									<a data-lang="'.$lang.'" data-id="'.$data->id.'" data-type="1" title="Passport Decline" class="btn btn-sm btn-outline-danger m-1 passportReject">Decline</a>
								</div>';
						}else{

							return '<div style="display:flex">
									<a href="'.url('admin/user/passport/approve/'.$data->id).'" data-type="1" title="Passport Approve" class="btn btn-sm btn-outline-success m-1 ">Approve</a>
									<a data-lang="'.$lang.'" data-id="'.$data->id.'" data-type="1" title="Passport Decline" class="btn btn-sm btn-outline-success m-1 passportReject">Decline</a>
								</div>';
						}

					}else{

						return '<div class="badge rounded-pill pill-badge-warning">Pending</div>';

					}
					
				}


		    })

			->addColumn('action', function($data){
				
					return '<div style="display:flex">
						<a href="'.route('admin.user.profile', ['id' => $data->user_id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }


	}
	
	public function passportListAll(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('users');
			
			$limit = $request->input('length');
			$start = $request->input('start');

			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\User::with('passportUserData')->select('users.*')->where('users.designation_id', $designation_id)->where('users.stage', 3)->orderBy('updated_at', 'desc');

			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\User::with('passportUserData')->where('users.designation_id', $designation_id)->where('users.stage', 3)->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}
			
			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->name.' '.$data->last_name;
		    })

			->addColumn('email', function($data){
				return $data->email;
		    })

			->addColumn('passport_no', function($data){
				if($data['passportUserData']){
					return $data['passportUserData']->passport_no;
				}else{
					return '-';
				}
				
			})

			->addColumn('country_id', function($data){

				if($data['passportUserData']){
					return \App\Helpers\commonHelper::getCountry2NameById($data['passportUserData']->country_id);
				}else{
					return '-';
				}
				
		    })

			->addColumn('category', function($data) {

				if($data['passportUserData']){

					return $data['passportUserData']->visa_category;
	
				}else{

					return '-';
				}

				
				
		    })

			->addColumn('status', function($data) {

				if($data['passportUserData']){

					if ($data['passportUserData']->admin_status == 'Pending') {

						return '<div class="badge rounded-pill pill-badge-success">Passport info Submitted</div>';
	
					} else if ($data['passportUserData']->admin_status == 'Approved') {
	
						if($data['passportUserData']->visa_granted == 'Yes'){
							return '<div class="badge rounded-pill pill-badge-success">Visa Granted</div>';
						}elseif($data['passportUserData']->visa_granted == 'No'){

							if($data['reference_number'] != null){
								return '<div class="badge rounded-pill pill-badge-success">Money Refunded</div>';
							}

							return '<div class="badge rounded-pill pill-badge-danger">Visa Not Granted</div>';

						}else{
							return '<div class="badge rounded-pill pill-badge-success">Documents Issued</div>';
						}
						
					} else if ($data['passportUserData']->admin_status === 'Decline') {
	
						return '<div class="badge rounded-pill pill-badge-danger">Passport Info Returned</div>';
						
					}else{
	
						return '<div class="badge rounded-pill pill-badge-warning">Passport Info Pending</div>';
					}

				}else{
					return '<div class="badge rounded-pill pill-badge-warning">Passport Info Pending</div>';
				}


		    })

			->addColumn('action', function($data){
				
				
					return '<div style="display:flex">
						<a href="'.route('admin.user.profile', ['id' => $data->id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a></div>';
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }


	}
	
	public function sponsorshipList(Request $request, $countryType, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('passport_infos');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			
			$query = \App\Models\PassportInfo::select('passport_infos.*','users.language')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			if($countryType  == 'no-visa-needed'){
			
				$query->where('passport_infos.visa_category','No Visa Needed');

			}elseif($countryType  == 'visa-needed'){

				$query->where('passport_infos.visa_category', 'Visa Needed');

			}elseif($countryType  == 'restricted'){

				$query->where('passport_infos.visa_category', 'Restricted Country');

			}
			
			$query->where(function ($query1) {
				$query1->where('passport_infos.visa_granted','!=','No')
					  ->orWhere('passport_infos.visa_granted','=',null);
			});

			
			
			$data = $query->offset($start)->limit($limit)->get();

			$totalData1 = \App\Models\PassportInfo::select('passport_infos.*','users.language')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}
			if($countryType  == 'no-visa-needed'){

				$totalData1->where('passport_infos.visa_category','No Visa Needed');

			}elseif($countryType  == 'visa-needed'){

				$totalData1->where('passport_infos.visa_category', 'Visa Needed');

			}elseif($countryType  == 'restricted'){

				$totalData1->where('passport_infos.visa_category', 'Restricted Country');

			}

			$totalData1->where(function ($query1) {
				$query1->where('passport_infos.visa_granted','!=','No')
					  ->orWhere('passport_infos.visa_granted','=',null);
			});

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->salutation.' '.$data->name;
		    })

			->addColumn('passport_no', function($data){
				return $data->passport_no;
			})

			->addColumn('country_id', function($data){
				return \App\Helpers\commonHelper::getCountry2NameById($data->country_id);
		    })

			->addColumn('financial_letter', function($data){
				
				if($data->language == 'en'){

						return '<a style="color:blue !important" href="'.asset('uploads/file/'.$data->financial_letter).'" target="_blank" class="text-blue"> Acceptance Letter English</a>,
								<a style="color:blue!important" href="'.asset('uploads/file/'.$data->financial_spanish_letter).'" target="_blank" class="text-blue"> Acceptance Letter Spanish</a>';
					
				}else{

						return '<a style="color:blue!important" href="'.asset('uploads/file/'.$data->financial_spanish_letter).'" target="_blank" class="text-blue"> Acceptance Letter Spanish</a>';
					
				}
				
			})
			
			->addColumn('valid_residence_country', function($data){

				if($data->visa_category == 'No Visa Needed'){

					return '<a style="color:blue!important" href="'.asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf').'" target="_blank" class="text-blue"> Bank Letter  </a>';
						
					
				}else{

					return '<a style="color:blue!important" href="'.asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf').'" target="_blank" class="text-blue"> Bank</a>,
						<a style="color:blue!important" href="'.asset('uploads/file/Visa_Request_Form.pdf').'" target="_blank" class="text-blue"> Visa Request Form</a>,
						<a style="color:blue!important" href="'.asset('uploads/file/DOCUMENTS_REQUIRED_FOR_VISA_PROCESSING.pdf').'" target="_blank" class="text-blue"> Documents Required for Visa Processing</a>';
					
				}
				
		    })

			->addColumn('status', function($data){
				
				if ($data->status == 'Approve') {
					return '<div class="span badge rounded-pill pill-badge-success">Documents Issued</div>';
				} else if ($data->status == 'Reject') {
					return '<div class="span badge rounded-pill pill-badge-danger">Decline</div>';
				} else if ($data->status === 'Pending') {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
				
		    })
			->addColumn('visa_status', function($data){
				
				if ($data->visa_granted == null) {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
					
				} else if ($data->visa_granted == 'No') {
					return '<div class="span badge rounded-pill pill-badge-danger">Visa Not Granted</div>';
				} else if ($data->visa_granted === 'Yes') {
					return '<div class="span badge rounded-pill pill-badge-success">Visa Granted</div>';
				}
				
		    })


			->addColumn('action', function($data){
				
					return '<div style="display:flex">
								<a href="'.route('admin.user.profile', ['id' => $data->user_id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a>
							</div>';
				

				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

	}
	
	public function passportListRestrictedList(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('passport_infos');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			
			$query = \App\Models\PassportInfo::select('passport_infos.*')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$query->where('passport_infos.visa_category', 'Restricted Country');

			$query->where(function ($query1) {
				$query1->where('passport_infos.visa_granted','!=','No')
					  ->orWhere('passport_infos.visa_granted','=',null);
			});

			$data = $query->offset($start)->limit($limit)->get();
			
			$totalData1 = \App\Models\PassportInfo::select('passport_infos.*')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$totalData1->where('passport_infos.visa_category', 'Restricted Country');

			$totalData1->where(function ($query2) {
				$query2->where('passport_infos.visa_granted','!=','No')
					  ->orWhere('passport_infos.visa_granted','=',null);
			});
			
			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->salutation.' '.$data->name;
		    })

			->addColumn('passport_no', function($data){
				return $data->passport_no;
			})

			->addColumn('country_id', function($data){
				return \App\Helpers\commonHelper::getCountry2NameById($data->country_id);
		    })

			->addColumn('financial_letter', function($data){
				
				if($data->language == 'en'){

						return '<a style="color:blue !important" href="'.asset('uploads/file/'.$data->financial_letter).'" target="_blank" class="text-blue"> Acceptance Letter English</a>,
								<a style="color:blue!important" href="'.asset('uploads/file/'.$data->financial_spanish_letter).'" target="_blank" class="text-blue"> Acceptance Letter Spanish</a>';
					
				}else{

						return '<a style="color:blue!important" href="'.asset('uploads/file/'.$data->financial_spanish_letter).'" target="_blank" class="text-blue"> Acceptance Letter Spanish</a>';
					
				}
				
			})
			
			->addColumn('valid_residence_country', function($data){

				if($data->visa_category == 'No Visa Needed'){

					return '<a style="color:blue!important" href="'.asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf').'" target="_blank" class="text-blue"> Bank Letter  </a>';
						
					
				}else{

					return '<a style="color:blue!important" href="'.asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf').'" target="_blank" class="text-blue"> Bank</a>,
						<a style="color:blue!important" href="'.asset('uploads/file/Visa_Request_Form.pdf').'" target="_blank" class="text-blue"> Visa Request Form</a>,
						<a style="color:blue!important" href="'.asset('uploads/file/DOCUMENTS_REQUIRED_FOR_VISA_PROCESSING.pdf').'" target="_blank" class="text-blue"> Documents Required for Visa Processing</a>';
					
				}
				
		    })

			->addColumn('support_name', function($data){
				
				return $data->admin_provide_name;
		    })

			->addColumn('support_email', function($data){
				
				return $data->admin_provide_email;
		    })

			->addColumn('status', function($data){
				
				if ($data->status == 'Approve') {
					return '<div class="span badge rounded-pill pill-badge-success">Documents Issued</div>';
				} else if ($data->status == 'Reject') {
					return '<div class="span badge rounded-pill pill-badge-danger">Decline</div>';
				} else if ($data->status === 'Pending') {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
				
		    })
			->addColumn('visa_status', function($data){
				
				if ($data->status == 'Approve') {
					return '<div class="span badge rounded-pill pill-badge-success">Verify</div>';
				} else if ($data->status == 'Reject') {
					return '<div class="span badge rounded-pill pill-badge-danger">Decline</div>';
				} else if ($data->status === 'Pending') {
					return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
				}
				
		    })


			->addColumn('action', function($data){
				
					return '<div style="display:flex">
								<a href="'.route('admin.user.profile', ['id' => $data->user_id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a>
							</div>';
				

				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

	}
	
	public function visaIsNotGranted(Request $request, $type) {
		
		if ($request->ajax()) {
			
			$columns = \Schema::getColumnListing('passport_infos');
			
			$limit = $request->input('length');
			$start = $request->input('start');
			$designation_id = \App\Helpers\commonHelper::getDesignationId($type);

			$query = \App\Models\PassportInfo::select('passport_infos.*','users.reference_number','users.refund_amount')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			if (request()->has('email')) {
				$query->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$query->where('passport_infos.visa_granted','No');

			$data = $query->offset($start)->limit($limit)->get();

			$totalData1 = \App\Models\PassportInfo::select('passport_infos.*','users.reference_number','users.refund_amount')->join('users','users.id','=','passport_infos.user_id')->where('users.designation_id', $designation_id)->where('passport_infos.admin_status','Approved')->orderBy('updated_at', 'desc');
			
			if (request()->has('email')) {
				$totalData1->where(function ($query1) {
					$query1->where('users.email', 'like', "%" . request('email') . "%")
						  ->orWhere('users.name', 'like', "%" . request('email') . "%")
						  ->orWhere('users.last_name', 'like', "%" . request('email') . "%");
				});
				
			}

			$totalData1->where('passport_infos.visa_granted','No');

			$totalData = $totalData1->count();

			$totalFiltered = $query->count();

			$draw = intval($request->input('draw'));
			$recordsTotal = intval($totalData);
			$recordsFiltered = intval($totalFiltered);

			return \DataTables::of($data)
			->setOffset($start)

			->addColumn('name', function($data){
				return $data->salutation.' '.$data->name;
		    })

			->addColumn('passport_no', function($data){
				return $data->passport_no;
			})

			->addColumn('country_id', function($data){
				return \App\Helpers\commonHelper::getCountry2NameById($data->country_id);
		    })
			->addColumn('category', function($data) {

				return $data->visa_category;

				
		    })

			
			->addColumn('visa_not_granted_docs', function($data){
				
				return '<a style="color:blue !important" href="'.asset('/uploads/visa_file/'.$data->visa_not_granted_docs).'" target="_blank"> View Doc</a>';
				
		    })

			->addColumn('remark', function($data){
				
					return $data->visa_not_ranted_comment;
				
		    })

			->addColumn('Refund', function($data){
				
				if (\Auth::user()->designation_id == '1' || Auth::user()->designation_id == 16) {

					if($data->reference_number == null){

						$totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($data->user_id, true);
						$totalAcceptedAmount = \App\Helpers\commonHelper::getTotalAcceptedAmount($data->user_id, true);

						return '<div style="display:flex">
								<a data-pending_amount="'.$totalPendingAmount.'" data-accepted_mount="'.$totalAcceptedAmount.'" data-id="'.$data->user_id.'" data-type="1" title="Refund Money" class="btn btn-sm btn-outline-success m-1 moneyRefunded" id="staticBackdropButton">Refund Money</a>
								</div>';
					}else{

						return '<div class="span badge rounded-pill pill-badge-success">Money Refunded</div>';
					}

					
				}
				
		    })

			
			->addColumn('refund_amount', function($data){
				
				return $data->refund_amount;
			
			})

			->addColumn('reference_number', function($data){
				
				return $data->reference_number;
			
			})

			->addColumn('action', function($data){
				
					return '<div style="display:flex">
								<a href="'.route('admin.user.profile', ['id' => $data->user_id] ).'" title="View user profile" class="btn btn-sm btn-primary m-1 text-white" ><i class="fas fa-eye"></i></a>
								</div>';
				
				
		    })

		    ->escapeColumns([])	
			->setTotalRecords($totalData)
			->with('draw','recordsTotal','recordsFiltered')
		    ->make(true);

        }

	}

	public function PassportInfoApprove(Request $request,$id){
	
		try{

			$passportApprove= \App\Models\PassportInfo::where('id',$id)->first();

			$passportApprove->admin_status='Approved';
			
			$passportApprove->save();
			
			$user= \App\Models\User::where('id',$passportApprove->user_id)->first();


			if($passportApprove->visa_category == 'No Visa Needed'){

				\App\Helpers\commonHelper::sendFinancialLetterMailSend($passportApprove->user_id,$id,'financial');  // 2 letter acc, bank

				if($user->language == 'sp'){

					$subject = 'Por favor, envíe la información de su vuelo para GProCongress II.';
					$msg = '<p>Estimado  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Inicie sesión en su cuenta en el sitio web de GProCongress lo antes posible y responda las preguntas de la Etapa 3 para brindarnos la información de su vuelo para su viaje a Panamá.</p>
					<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, responda a este correo electrónico.</p><p><br></p>
					<p>Atentamente,</p><p>Equipo GProCongress II&nbsp; &nbsp;&nbsp;</p>';
		
				}elseif($user->language == 'fr'){
				
					$subject = 'Veuillez soumettre les informations relatives à votre vol pour le GProCongress II.';
					$msg = '<p>Cher  '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Veuillez vous connecter à votre compte sur le site Web du GProCongress dès que possible et répondre aux questions de l’étape 3 afin de nous fournir les informations relatives à votre vol pour votre voyage au Panama.</p>
					<p>Si vous avez des questions ou si vous souhaitez parler à l’un des membres de notre équipe, veuillez répondre à cet e-mail.&nbsp;</p><p><br></p>
					<p>Cordialement,</p><p>L’équipe GProCongress II&nbsp; &nbsp;&nbsp;</p>';
		
				}elseif($user->language == 'pt'){
				
					$subject = 'Por favor, envie suas informações de voo para o GProCongresso II.';
					$msg = '<p>Caro '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Faça login em sua conta no site do GProCongresso o mais rápido possível e responda às perguntas da Etapa 3, para nos fornecer suas informações de voo para sua viagem ao Panamá.</p>
					<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, responda a este e-mail.&nbsp;</p><p><br></p>
					<p>Calorosamente,</p><p>Equipe GProCongresso  II&nbsp; &nbsp;&nbsp;</p>';
		
				}else{
				
					$subject = 'Please submit your flight information for GProCongress II';
					$msg = '<p>Dear '.$user->name.' '.$user->last_name.',&nbsp;</p><p><br></p>
					<p>Please login to your account at the GProCongress website as soon as possible, and answer the questions under Stage 3, to give us your flight information for your trip to Panama.</p>
					<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.&nbsp;</p><p><br></p>
					<p>Warmly,</p><p>GProCongress II Team&nbsp; &nbsp;&nbsp;</p>';
									
				}

				\App\Helpers\commonHelper::emailSendToUser($user->email, $subject, $msg);

				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				\App\Helpers\commonHelper::sendNotificationAndUserHistory(\Auth::user()->id, $subject, $msg, 'Passport Info Approved');
		

			}elseif($passportApprove->visa_category == 'Visa Needed'){

				\App\Helpers\commonHelper::sendSponsorshipLetterMailSend($passportApprove->user_id,$id);  // 4 letter

			}elseif($passportApprove->visa_category == 'Restricted Country'){

				\App\Helpers\commonHelper::sendSponsorshipLetterRestrictedMailSend($passportApprove->user_id,$id);  // 4 letter

			}

			return redirect()->back()->with(['5fernsadminsuccess'=>"Passport information approved successfully."]);
			
		}catch (\Exception $e){
			
			return redirect()->back()->with(['5fernsadminsuccess'=>$e->getMessage()]);
			
		}
	}
	
	public function uploadSponsorshipLetter(Request $request){
	
		$rules = [
			'file' => 'required|mimes:pdf',
			'financial_english_letter' => 'required|mimes:pdf',
			'financial_spanish_letter' => 'required|mimes:pdf',
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

			// try{

				$passportApprove= \App\Models\PassportInfo::where('id',$request->post('id'))->first();

				$passportApprove->status='Pending';
				
				if($request->hasFile('file')){
					$imageData = $request->file('file');
					$image = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(1111,9999).'.'.$imageData->getClientOriginalExtension();
					$destinationPath = public_path('/uploads/file');
					$imageData->move($destinationPath, $image);

					$passportApprove->sponsorship_letter = $image;
				}
				if($request->hasFile('financial_english_letter')){
					$imageData = $request->file('financial_english_letter');
					$financialImage = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(1111,9999).'.'.$imageData->getClientOriginalExtension();
					$destinationPath = public_path('/uploads/file');
					$imageData->move($destinationPath, $financialImage);

					$passportApprove->financial_letter = $financialImage;
				}
				if($request->hasFile('financial_spanish_letter')){
					$imageData = $request->file('financial_spanish_letter');
					$financialSpanish = 'image_'.strtotime(date('Y-m-d H:i:s')).rand(1111,9999).'.'.$imageData->getClientOriginalExtension();
					$destinationPath = public_path('/uploads/file');
					$imageData->move($destinationPath, $financialSpanish);

					$passportApprove->financial_spanish_letter = $financialSpanish;
				}


				$passportApprove->save();

				$user= \App\Models\User::where('id',$passportApprove->user_id)->first();

				$to = $user->email;
				
				$name = $user->name.' '.$user->last_name;

				if($user->language == 'sp'){

					$subject = "Se adjunta una carta de visado corregida para su revisión";
					$msg = '<p>Estimado '.$name.' ,</p><p><br></p><p><br></p><p>Adjuntamos una carta de visa corregida para su revisión. Lea atentamente esta carta y asegúrese de que toda la información contenida en ella sea correcta. Si es así, envíenos un correo electrónico confirmando su aprobación de la carta de visa. Si es necesario realizar cambios adicionales, envíenos un correo electrónico identificando esos cambios.&nbsp;</p><p><br></p><p>Si tiene alguna pregunta, o si necesita hablar con algún miembro de nuestro equipo, por favor, responda este correo. </p><p><br></p><p><br></p><p>Atentamente,&nbsp;</p><p><br></p><p><br></p><p>El Equipo GproCongress II</p>';
				
				}elseif($user->language == 'fr'){
				
					$subject = "Une lettre de visa révisée est jointe pour votre revue.";
					$msg = "<p>Cher '.$name.',&nbsp;</p><p><br></p><p>Nous joignons une lettre de visa révisée pour votre revue.  Veuillez l'examiner attentivement et vous assurer que toutes les informations qu'elle contient sont correctes.  Si c'est le cas, veuillez nous envoyer un courriel confirmant votre approbation de la lettre de visa.  Si des modifications supplémentaires doivent être apportées, veuillez nous envoyer un courriel indiquant ces modifications.&nbsp;</p><p><br></p><p>Si vous avez des questions ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à ce courriel.&nbsp;</p><p><br></p><p><br></p><p>Chaleureusement,&nbsp;</p><p>L’équipe du GProCongrès II</p><div><br></div>";
		
				}elseif($user->language == 'pt'){
				
					$subject = "Uma carta de visto revisada está anexada para sua revisão.";
					$msg = '<p>Prezado '.$name.',</p><p><br></p><p>Estamos anexando uma carta de visto revisada para sua análise. Por favor, leia esta carta cuidadosamente e certifique-se de que todas as informações contidas nela estão corretas. Em caso afirmativo, envie-nos um e-mail confirmando sua aprovação da carta de visto. Se quaisquer alterações adicionais precisarem ser feitas, envie-nos um e-mail identificando essas alterações.</p><p><br></p><p>Se você tiver alguma dúvida ou precisar falar com um dos membros de nossa equipe, responda a este e-mail.&nbsp;</p><p><br></p><p>Atenciosamente,</p><p>Equipe GProCongresso II</p><div><br></div>';
				
				}else{
				
					$subject = 'A revised visa letter is attached for your review.';
					$msg = '<p>Dear '.$name.',</p><p><br></p><p>We are attaching a revised visa letter for your review.  Please review this letter carefully and make sure that all the information contained in it is correct.  If so, please send us an email confirming your approval of the visa letter.  If any additional changes need to be made, please send us an email identifying those changes.&nbsp;</p><p><br></p><pIf you have any questions, or if you need to speak with one of our team members, please reply to this email.</p><p></p><p>Warmly,</p><p>GProCongress II Team</p><div><br></div>';
										
				}

				$file = public_path('uploads/file/'.$passportApprove->sponsorship_letter);

				$files = [
					public_path('uploads/file/'.$image),
					public_path('uploads/file/'.$financialSpanish),
					public_path('uploads/file/'.$financialImage),
				];
		
				\Mail::send('email_templates.mail', compact('to', 'subject', 'msg'), function($message) use ($to, $subject,$files) {
					$message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
					$message->subject($subject);
					$message->to($to);
					
					foreach ($files as $file){
						$message->attach($file);
					}
					
				});
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				
				\App\Helpers\commonHelper::sendNotificationAndUserHistory(\Auth::user()->id,$subject,$msg,'A revised visa letter is attached for your review.');
				
				return response(array('message'=>'Sponsorship letter upload successfully'),200);
				
			// }catch (\Exception $e){
				
			// 	return response(array('message'=>$e->getMessage()),403);
			// }
		}

	}

	public function PassportInfoReject(Request $request){
	
		$rules = [
			'remark' => 'required',
			'id' => 'required',
			
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

				$passportReject= \App\Models\PassportInfo::where('id',$request->post('id'))->first();
				$remarkarray = '';
				$remarkarray.= $passportReject->admin_remark ? ''.$passportReject->admin_remark.'<br>' : '';
				$remarkarray.= '<br>'.$request->post('remark');
				$passportReject->admin_status='Decline';
				$passportReject->admin_remark=$remarkarray;
				
				$passportReject->save();

				$user= \App\Models\User::where('id',$passportReject->user_id)->first();

				$url = '<a href="'.url('passport-info').'">Click here</a>';
				$to = $user->email;

				$remark = $request->post('remark');
				
				$name = $user->name.' '.$user->last_name;

				if($user->language == 'sp'){

					$subject = "Por favor vuelva a enviar su información para GProCongress II.";
					$msg = "<p>Estimado ".$name.",</p><p><br></p>
					<p>Recibimos la información que envió. Sin embargo, hay un problema con lo que envió. A continuación siga leyendo para obtener una descripción del problema. Le pedimos que vuelva a enviar su información de acuerdo con el párrafo a continuación.</p>
					<p style='background-color:yellow; display: inline;'>".$remark."</p>
					<p>Si tiene alguna pregunta o si necesita hablar con alguno de los miembros de nuestro equipo, por favor responda a este correo.</p>
					<p>Por favor continúe orando junto con nosotros por el proceso de su visa. Dios primero, nos vemos en Panamá este noviembre.</p>
					<p>Juntos busquemos al Señor por el GProCongress II, para fortalecer y multiplicar los capacitadores de pastores, para décadas de impacto en el evangelio.</p>
					<p>Atentamente,</p><p>Equipo GProCongress II</p><div><br></div>";
				
				}elseif($user->language == 'fr'){
				
					$subject = "Veuillez soumettre à nouveau vos informations pour GProCongress II.";
					$msg = "<p>Cher ".$name.",</p><p><br></p>
					<p>Nous avons reçu les informations que vous avez soumises. Cependant, il y a un problème avec ce que vous avez soumis. Vous trouverez ci-dessous une description du problème. Nous vous demandons de soumettre à nouveau vos informations conformément au paragraphe ci-dessous.</p>
					<p style='background-color:yellow; display: inline;'>".$remark."</p>
					<p>Si vous avez des questions ou si vous souhaitez parler à l'un des membres de notre équipe, veuillez répondre à cet e-mail.</p>
					<p>Continuez à prier avec nous pour la procédure d'obtention de votre visa. Si Dieu le veut, nous vous verrons au Panama en novembre.</p>
					<p>Ensemble, cherchons le Seigneur pour GProCongress II, afin de renforcer et de multiplier les pasteurs formateurs pour des décennies d'impact sur l'Evangile.</p>
					<p>Cordialement,</p><p>L'équipe de GProCongress II</p><div><br></div>";

				}elseif($user->language == 'pt'){
				
					$subject = "Por favor, reenvie suas informações para o GProCongresso II.";
					$msg = "<p>Caro ".$name.",</p><p><br></p>
					<p>Recebemos as informações que você enviou. No entanto, há um problema com o que você enviou. Veja abaixo uma descrição do problema. Pedimos que reenvie suas informações de acordo com o parágrafo abaixo.</p>
					<p style='background-color:yellow; display: inline;'>".$remark."</p>
					<p>Se você tiver alguma dúvida ou precisar falar com um dos membros de nossa equipe, responda a este e-mail.</p>
					<p>Por favor, continue orando conosco pelo seu processo de visto. Se Deus quiser, nos veremos no Panamá em Novembro.</p>
					<p>Juntos, vamos buscar o Senhor para o GProCongresso II, para fortalecer e multiplicar os pastores treinadores por décadas de impacto no evangelho.</p>
					<p>Calorosamente,</p><p>Equipe GProCongresso II</p><div><br></div>";

				}else{
				
					$subject = 'Please resubmit your information for GProCongress II';
					$msg = "<p>Dear ".$name.",</p><p><br></p>
					<p>We received the information that you submitted.  However, there is a problem with what you submitted.  See below for a description of the problem.  We are asking you to resubmit your information according to the paragraph below.</p>
					<p style='background-color:yellow; display: inline;'>".$remark."</p>
					<p>If you have any questions, or if you need to speak with one of our team members, please reply to this email.</p>
					<p>Please continue to pray along with us for your visa process. God willing, we will see you in Panama this November.</p>
					<p>Together let's seek the Lord for GProCongress II, to strengthen and multiply pastor trainers for decades of gospel impact.</p>
					<p>Warmly,</p><p>GProCongress II Team</p><div><br></div>";
										
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				\App\Helpers\commonHelper::sendNotificationAndUserHistory(\Auth::user()->id,$subject,$msg,'Passport info Rejected');

				return response(array('message'=>'Passport Information declined successfully'),200);
					
				
			}catch (\Exception $e){
			
				return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
			}
		}
	
	}

	public function PassportApproveRestricted(Request $request){
	
		$rules = [
			'name' => 'required',
			'email' => 'required',
			'id' => 'required',
			
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

				$passportApprove= \App\Models\PassportInfo::where('id',$request->post('id'))->first();
				
				$passportApprove->admin_status='Approved';
				$passportApprove->admin_provide_name=$request->post('name');
				$passportApprove->admin_provide_email=$request->post('email');
				
				$passportApprove->save();

				$user= \App\Models\User::where('id',$passportApprove->user_id)->first();

				\App\Helpers\commonHelper::sendSponsorshipLetterRestrictedMailSend($passportApprove->user_id,$passportApprove->id);  // 4 letter
				\App\Helpers\commonHelper::sendNotificationAndUserHistory(\Auth::user()->id, 'Passport Info Approved', 'Passport Info Approved', 'Passport Info Approved');
		
				return response(array('message'=>'Passport Information Approved successfully'),200);
				
			}catch (\Exception $e){
			
				return response(array("error"=>true, "message" => $e->getMessage()),200); 
			
			}
		}
	
	}

	public function ExhibitorUser(Request $request) {
 
		$type = 'Pending';
		
        \App\Helpers\commonHelper::setLocale();
		$stageno = 'all';
        return view('admin.exhibitors.stage.stage-all', compact('type','stageno'));

	}

	public function ExhibitorSponsorship(Request $request) {
 
        \App\Helpers\commonHelper::setLocale();
		$stageno = 'sponsorship';
        return view('admin.exhibitors.stage.stage-sponsorship', compact('stageno'));

	}

	public function ExhibitorPaymentPending(Request $request) {
 
        \App\Helpers\commonHelper::setLocale();
		$stageno = 'Payment-Pending';
        return view('admin.exhibitors.stage.payment_pending', compact('stageno'));

	}

	public function ExhibitorQrCode(Request $request) {
 
        \App\Helpers\commonHelper::setLocale();
		$stageno = 'qrcode';
        return view('admin.exhibitors.stage.stage-five', compact('stageno'));

	}

	public function exhibitorProfile(Request $request, $id) {

		$results = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.id', '=', $id]])->first();
		
		$result = [
			'id'=>$results->id,
			'parent_id'=>$results->parent_id,
			'business_owner'=>$results->parent_id,
			'added_as'=>$results->added_as,
			'salutation'=>$results->salutation,
			'last_name'=>$results->last_name,
			'name'=>$results->name,
			'gender'=>$results->gender,
			'last_name'=>$results->last_name,
			'email'=>$results->email,
			'phone_code'=>$results->phone_code,
			'mobile'=>$results->mobile,
			'reg_type'=>$results->reg_type,
			'status'=>$results->status,
			'profile_status'=>$results->profile_status,
			'dob'=>$results->dob,
			'citizenship'=>$results->citizenship,
			'amount'=>$results->amount,
			'payment_status'=>$results->payment_status,
			'room'=>$results->room,
			'business_name'=>$results->business_name,
			'business_identification_no'=>$results->business_identification_no,
			'website'=>$results->website,
			'logo'=>'<a target="_blank" href="'.asset('uploads/logo/'.$results->logo).'">View</a>',
			'passport_number'=>$results->passport_number,
			'passport_copy'=>'<a target="_blank" href="'.asset('uploads/passport/'.$results->passport_copy).'">View</a>',
			'any_one_coming_with_along'=>$results->any_one_coming_with_along,
			'coming_with_spouse'=>$results->coming_with_spouse,
			'number_of_room'=>$results->number_of_room,
			'qrcode'=>$results->qrcode,
			'spouse_id'=>$results->spouse_id,
			'language'=>$results->language,
			'spouse'=>\App\Models\User::where('parent_id',$results['id'])->where('added_as','Spouse')->first(),
			'parent_name'=>\App\Helpers\commonHelper::getUserNameById($results['parent_id']),
			'AmountInProcess'=>\App\Helpers\commonHelper::getTotalAmountInProcess($results['id']),
			'AcceptedAmount'=>\App\Helpers\commonHelper::getTotalAcceptedAmount($results['id']),
			'PendingAmount'=>\App\Helpers\commonHelper::getTotalPendingAmount($results['id']),
			'WithdrawalBalance'=>\App\Helpers\commonHelper::userWithdrawalBalance($results['id']),
			'RejectedAmount'=>\App\Helpers\commonHelper::getTotalRejectedAmount($results['id']),
		];

		\App\Helpers\commonHelper::setLocale();
		return view('admin.exhibitors.profile', compact('result','id'));

	}

	public function exhibitorTransaction(Request $request, $id) {

       
		$result=\App\Helpers\commonHelper::callExhibitorAPI('GET', '/get-exhibitor-profile?id='.$id, array());
        
		$resultData=json_decode($result->content, true);
		$result=$resultData['result'];
		if(empty($resultData['result'])){
			return redirect('admin/exhibitor/user');
		}

		\App\Helpers\commonHelper::setLocale();
		return view('admin.exhibitors.payment-history', compact('result','id'));

	}

	// exhibitor

	public function getUserData(Request $request) {
 
		$limit = $request->input('length');
		$start = $request->input('start');
		
		$query = \App\Models\Exhibitors::orderBy('id', 'desc');

		if (request()->has('email')) {
			$query->where(function ($query1) {
				$query1->where('email', 'like', "%" . request('email') . "%")
					  ->orWhere('name', 'like', "%" . request('email') . "%")
					  ->orWhere('last_name', 'like', "%" . request('email') . "%");
			});
			
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\Exhibitors::orderBy('id', 'desc');
		
		if (request()->has('email')) {

			$totalData1->where(function ($query) {
				$query->where('email', 'like', "%" . request('email') . "%")
					  ->orWhere('.name', 'like', "%" . request('email') . "%")
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

			return $data->email;
			
		})

		->addColumn('stage0', function($data){
			return \App\Helpers\commonHelper::getCountryNameById($data->citizenship); 
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
			return ($data->website);
		})

		->addColumn('stage5', function($data){
			return $data->comment; 
		})

		->addColumn('payment', function($data){
			if($data->parent_id != null){
				return '<div class="span badge rounded-pill pill-badge-secondary">N/A</div>';
			}
			if(\App\Helpers\commonHelper::getTotalPendingAmount($data->user_id)) {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})

		->addColumn('action', function($data){

			if(\App\Helpers\commonHelper::countExhibitorPaymentSuccess()  == false){

				if($data->profile_status != 'Pending'){

					return '
						<div style="display:flex">
						<a href="'.url('admin/user/user-profile/'.$data->user_id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						</div>
					';
					
				}else{

					return '
						<div style="display:flex">
						<a href="'.url('admin/user/user-profile/'.$data->user_id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						<a href="javascript:void(0)" title="Approve Profile" data-id="'.$data->id.'" data-status="Approved" class="btn btn-sm btn-success px-3 m-1 text-white profile-status"><i class="fas fa-check"></i></a>
						<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Declined" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a></div>
					';
				}

			}else{

				if($data->profile_status != 'Pending'){

					return '
						<div style="display:flex">
						<a href="'.url('admin/user/user-profile/'.$data->user_id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						</div>
					';
					
				}else{

					return '
						<div style="display:flex">
						<a href="'.url('admin/user/user-profile/'.$data->user_id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
						<a href="javascript:void(0)" title="Decline Profile" data-id="'.$data->id.'" data-status="Declined" class="btn btn-sm btn-danger px-3 m-1 text-white profile-status"><i class="fas fa-ban"></i></a></div>
					';
				}
				
				
			}
			

		})

		

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getGroupUsersList(Request $request) {

		$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
		$results = \App\Models\User::select('users.*','exhibitors.passport_number','exhibitors.passport_copy')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.parent_id', $id]])->get();
		
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

				$spouse = \App\Models\User::select('users.*','exhibitors.passport_number','exhibitors.passport_copy')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.parent_id', $result->id]])->first();
				
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

				$html .= '<td class="text-center">'.\App\Helpers\commonHelper::getCountryNameById($result->citizenship);
				$html .= $spouse ? '<hr><p class="text-danger">'.\App\Helpers\commonHelper::getCountryNameById($spouse->citizenship).'</p>' : '';
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

				$html .= '<td class="text-center"><a href="'.url('admin/exhibitor/profile/'.$result->id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></p>';
				$html .= $spouse ? '<hr><p class="text-danger"><a href="'.url('admin/exhibitor/profile/'.$spouse->id).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a></p>' : '';
				$html .= '</td>';

				$html .= '</tr>';
			}
		} else {
			$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
		}
		$html .= '</tbody></table>';

		return response()->json(array('html'=>$html));
		

	}

	public function getExhibitorPaymentHistory(Request $request, $id) {
		
		$columns = \Schema::getColumnListing('transactions');
		
		$limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
		$dir = $request->input('order.0.dir');

		$query = \App\Models\Transaction::where('user_id', $id)->where('particular_id', '1')->orderBy('id','desc');

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
		
		$totalData = \App\Models\Transaction::orderBy('created_at','desc')->count();
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

		$query = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy')->join('exhibitors','users.id','=','exhibitors.user_id')->where('users.designation_id', '=', '14')->where([['users.stage', '=', '3']])->orderBy('users.updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('users.email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.stage', '=', '3']]);
		
		if (request()->has('email')) {
			$totalData1->where('users.email', 'like', "%" . request('email') . "%");
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

		$query = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.financial_letter','exhibitors.business_identification_no')->join('exhibitors','users.id','=','exhibitors.user_id')->where('exhibitors.profile_status','Approved')->where('exhibitors.payment_status','Pending')->orderBy('users.updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('users.email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy','exhibitors.sponsorship_letter')->join('exhibitors','users.id','=','exhibitors.user_id');
		
		if (request()->has('email')) {
			$totalData1->where('users.email', 'like', "%" . request('email') . "%");
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
			
			if($data->payment_status == 'Pending') {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})


		->addColumn('action', function($data){
			
			return '
					<div style="display:flex">
						<a href="'.env('Admin_URL').'/admin/user/user-profile/'.$data->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
					</div>
				';
				
			
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getExhibitorPaymentSuccess(Request $request) {

		$columns = \Schema::getColumnListing('users');
			
		$limit = $request->input('length');
		$start = $request->input('start');
		// $order = $columns[$request->input('order.0.column')];
		// $dir = $request->input('order.0.dir');

		$query = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.payment_status','exhibitors.profile_status')->join('exhibitors','users.id','=','exhibitors.user_id')->where('exhibitors.profile_status','Approved')->where('exhibitors.payment_status','Success')->orderBy('users.updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('users.email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.payment_status','exhibitors.profile_status')->join('exhibitors','users.id','=','exhibitors.user_id')->where('exhibitors.profile_status','Approved')->where('exhibitors.payment_status','Success');
		
		if (request()->has('email')) {
			$totalData1->where('users.email', 'like', "%" . request('email') . "%");
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

		->addColumn('sponsorship', function($data){
			
			return $data->business_name;
			
		})
		->addColumn('Citizenship', function($data){
			
			return \App\Helpers\commonHelper::getCountryNameById($data->citizenship); 
			
		})
		->addColumn('financial', function($data){
				
			return $data->business_identification_no;
		})
		->addColumn('status', function($data){
			if ($data->profile_status == 'Approved') {
				return '<div class="span badge rounded-pill pill-badge-success">Approved</div>';
			} else if ($data->profile_status == 'Pending') {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			}
		})

		->addColumn('payment', function($data){
			
			if($data->payment_status == 'Pending') {
				return '<div class="span badge rounded-pill pill-badge-warning">Pending</div>';
			} else {
				return '<div class="span badge rounded-pill pill-badge-success">Completed</div>';
			}
		})


		->addColumn('action', function($data){
			
			return '
					<div style="display:flex">
						<a href="'.env('Admin_URL').'/admin/user/user-profile/'.$data->id.'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>
					</div>
				';
				
			
		})

		->escapeColumns([])	
		->setTotalRecords($totalData)
		->with('draw','recordsTotal','recordsFiltered')
		->make(true);


	}

	public function getExhibitorPaymentPending(Request $request) {

		$columns = \Schema::getColumnListing('users');
			
		$limit = $request->input('length');
		$start = $request->input('start');
		// $order = $columns[$request->input('order.0.column')];
		// $dir = $request->input('order.0.dir');

		$query = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.financial_letter','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy','exhibitors.sponsorship_letter')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.stage', '=', '2'],['users.parent_id', '=', null]])->orderBy('users.updated_at', 'desc');

		if (request()->has('email')) {
			$query->where('users.email', 'like', "%" . request('email') . "%");
		}

		$data = $query->offset($start)->limit($limit)->get();
		
		$totalData1 = \App\Models\User::select('users.*','exhibitors.business_name','exhibitors.business_identification_no','exhibitors.passport_number','exhibitors.passport_copy','exhibitors.sponsorship_letter')->join('exhibitors','users.id','=','exhibitors.user_id')->where([['users.stage', '=', '2'],['users.parent_id', '=', null]]);
		
		if (request()->has('email')) {
			$totalData1->where('users.email', 'like', "%" . request('email') . "%");
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

		$user = \App\Models\User::select('users.*')->join('exhibitors','users.id','=','exhibitors.user_id')->where('exhibitors.id', $request->get('id'))->first();

		if($user){

			$basePrice = 800;
			
			$html=view('admin.exhibitors.stage.stage_one_profile_status_model',compact('basePrice','user'))->render();

			return response()->json(array('html'=>$html));
			
		}
			
	}

	public function exhibitorProfileStatus(Request $request) {

		$result = \App\Models\Exhibitors::where('id',$request->post('user_id'))->where('profile_status','Pending')->first();
		
		if ($result) {

			$user = \App\Models\User::where('id',$result->user_id)->first();
			$to = $result->email;

			if ($request->post('status') == 'Approved') {

				$token =md5(rand(1111,4444));
				$result->profile_status = $request->post('status');
				$result->remark = $request->post('remark');
				$result->amount = $request->post('amount');
				$result->payment_token = $token;
				$name= $user->name.' '.$user->last_name;

				$website = '<a href="'.url('exhibitor-payment/'.$token).'">Website</a>';

				if($user->language == 'sp'){

					$subject = '¡Felicidades! ¡Has sido aprobado! Por favor, realice su pago ahora.';
					$msg = '<p>Estimado '.$name.',</p>
					<p>Nuestro equipo ha revisado su solicitud y nos complace informarle que ha sido aprobado como exhibidor para el GProCongress II. ¡Esperamos tenerle con nosotros en Panamá este noviembre! </p>
					<p>Le pedimos que realice su pago lo antes posible. Le recordamos que los exhibidores se eligen por orden de llegada, “primero en pagar, primero en entrar”. En consecuencia, si espera demasiado para realizar su pago, podría quedar fuera del Congreso como exhibidor, debido a que todos los cupos de exhibidores ya podrían estar llenos.</p>
					<p>Puede pagar su inscripción como exhibidor de $800 USD utilizando este enlace en el que puede hacer clic una sola '.$website.', mediante cualquiera de las tarjetas de crédito permitidas.&nbsp;</p>
					<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p>
					<p>Ore con nosotros para que se multiplique la cantidad y calidad de capacitadores de pastores.</p>
					<p>Cordialmente,</p>
					<p>Equipo GProCongress II</p>';

				}elseif($user->language == 'fr'){

					$subject = 'Félicitations!  Vous avez été approuvé !  Prière d’effectuer votre paiement maintenant.';
					$msg = '<p>Cher  '.$name.',</p>
					<p>Notre équipe a examiné votre demande et nous sommes heureux de vous informer que vous avez été approuvé en tant qu’exposant pour GProCongress II.  Nous sommes impatients de vous avoir avec nous au Panama en novembre!</p>
					<p>Nous vous demandons d’effectuer votre paiement dès que possible.  Nous vous rappelons que les exposants sont choisis selon le principe du « premier à payer, premier arrivé».  Par conséquent, si vous attendez trop longtemps pour effectuer votre paiement, vous pourriez être exclu du Congrès en tant qu’exposant, car tous les créneaux d’exposants pourraient déjà être pris.</p>
					<p>Vous pouvez payer vos frais d’exposition de 800 USD en utilisant une seule fois ce lien cliquable '.$website.', par l’intermédiaire de n’importe quelle carte de crédit majeure.&nbsp;</p>
					<p>Si vous avez des questions, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à ce courriel.</p>
					<p>Priez avec nous pour multiplier la quantité et la qualité des pasteurs-formateurs.</p>
					<p>Cordialement,</p>
					<p>L’équipe GProCongress II</p>';

				}elseif($user->language == 'pt'){

					$subject = 'Parabéns! Você foi aprovado! Por favor, faça seu pagamento agora.';
					$msg = "<p>Caro  '.$name.',</p>
					<p>Nossa equipe revisou sua inscrição e temos o prazer de informar que você foi aprovado como Expositor do GProCongresso II. Estamos ansiosos para tê-lo conosco no Panamá em novembro! </p>
					<p>Pedimos que efetue seu pagamento o quanto antes. Lembramos que os expositores são escolhidos na base do “primeiro a pagar, primeiro a chegar”. Dessa forma, se você demorar muito para efetuar o pagamento, poderá ficar de fora do Congresso como expositor, pois todas as vagas de expositor já podem estar preenchidas</p>
					<p>Você pode pagar sua taxa de exibição de $ 800 USD usando este '.$website.', usando qualquer cartão de crédito.'&nbsp;</p>
					<p>Se você tiver alguma dúvida ou precisar falar com um dos membros de nossa equipe, basta responder a este e-mail</p>
					<p>Ore conosco para multiplicar a quantidade e qualidade de pastores-treinadores.</p>
					<p>Calorosamente,,</p>
					<p>Equipe GProCongresso  II</p>";

				}else{
			
					$subject = 'Congratulations!  You have been approved!  Please make your payment now';
					$msg = '<p>Dear '.$name.',</p>
					<p>Our team has reviewed your submission, and we are pleased to inform you that you have been approved as an Exhibitor for GProCongress II.  We are looking forward to having you with us in Panama this November!  </p>
					<p>We ask that you make your payment as soon as possible.  We would remind you that exhibitors are chosen on a “first pay, first come” basis.  Accordingly, if you wait too long to make your payment, you could be left out of the Congress as an exhibitor, because all exhibitor slots could already be full.</p>
					<p>You may pay your $800 USD exhibition fee using this one time clickable '.$website.', using any major credit card.&nbsp;</p>
					<p>If you have any questions, or if you need to speak to one of our team members, simply reply to this email.</p>
					<p>Pray with us toward multiplying the quantity and quality of pastor-trainers. </p>
					<p>Warmly,</p>
					<p>The GProCongress II Team</p>';

				}
				
				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				\App\Helpers\commonHelper::emailSendToAdminExhibitor($subject,$msg);
				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id,$subject,$msg,'Exhibitor information approved');
				
				// \App\Helpers\commonHelper::sendSMS($result->mobile);
				$result->save();

				return response(array('error'=>false, 'reload'=>true, 'message'=>'Exhibitor information approved successful'), 200);
			
			}else if ($request->post('status') == 'Declined') {

				$result->profile_status = 'Declined';

				$faq = '<a href="'.url('faq').'">Click here</a>';
				
				$name= $user->name.' '.$user->last_name;
				
				if($user->language == 'sp'){

					$subject = 'Su solicitud de Exhibidor ha sido rechazada.';
					$msg = '<p>Estimado  '.$name.',</p><p><br></p>
					<p>Nuestro equipo ha revisado su presentación y lamentamos informarle que su solicitud para ser exhibidor en GProCongress II ha sido rechazada. Solo tenemos unos pocos espacios disponibles, por lo que no podemos aceptar a todos los que solicitan. Sin embargo, estamos agradecidos por su interés y su deseo de ser parte de este evento como exhibidor.</p>
					<p>Si tiene alguna pregunta o si necesita hablar con uno de los miembros de nuestro equipo, simplemente responda a este correo electrónico.</p><p><br></p><p><br></p>
					<p>Cordialmente,</p><p><br></p>
					<p>Equipo GProCongress II</p>';
				
				}elseif($user->language == 'fr'){
				
					$subject = "Votre demande de participation en tant qu'exposant a été rejetée.";
					$msg = '<p>Cher '.$name.',</p><p><br></p>
					<p>Notre équipe a examiné votre demande, et nous regrettons de vous informer que votre candidature pour être exposant au GProCongress II a été rejetée.  Nous n’avons que quelques places disponibles, et nous ne pouvons donc pas accepter tous ceux qui postulent.  Cependant, nous vous sommes reconnaissants de votre intérêt et de votre désir de faire partie de cet événement en tant qu’exposant. </p>
					<p>Si vous avez des questions, ou si vous avez besoin de parler à l’un des membres de notre équipe, répondez simplement à ce courriel.</p><p><br></p><p>Cordialement,</p><p><br></p><p>L’équipe GProCongress II</p>';
				
				}elseif($user->language == 'pt'){
				
					$subject = 'Sua inscrição de Expositor foi recusada.';
					$msg = '<p>Caro  '.$name.',</p><p><br></p>
					<p>Nossa equipe revisou sua inscrição e lamentamos informar que sua inscrição para ser Expositor no GProCongresso II foi recusada. Temos apenas algumas vagas disponíveis e, portanto, não podemos aceitar todos que se inscreverem. No entanto, agradecemos seu interesse e desejo de fazer parte deste evento como expositor.</p><p><br></p>
					<p>Se você tiver alguma dúvida ou precisar falar com um dos membros da nossa equipe, basta responder a este e-mail.</p>
					<p>Calorosamente,</p><p><br></p><p>Equipe GProCongresso II</p>';
				
				}else{
				
					$subject = 'Your Exhibitor application has been declined.';
					$msg = '<p>Dear '.$name.',</p><p><br></p>
					<p>Our team has reviewed your submission, and we are sorry to inform you that your application to be an Exhibitor at GProCongress II has been declined.  We only have a few spaces available, and so we cannot accept everyone who applies.  However, we are grateful for your interest in, and your desire to be a part of, this event as an exhibitor.</p>
					<p>If you have any questions, or if you need to speak to one of our team members, simply reply to this email.</p><p><br></p>
					<p><br></p><p>Warmly,</p><p><br></p><p>The GProCongress II Team</p>';
					
				}

				\App\Helpers\commonHelper::emailSendToUser($to, $subject, $msg);
				\App\Helpers\commonHelper::userMailTrigger($user->id,$msg,$subject);
				\App\Helpers\commonHelper::emailSendToAdminExhibitor($subject,$msg);
				\App\Helpers\commonHelper::sendNotificationAndUserHistory($user->id, $subject, $msg, 'Your application to be an Exhibitor has now been declined.');

				// \App\Helpers\commonHelper::sendSMS($result->mobile);
				$result->save();

				return response(array('error'=>false, 'reload'=>true, 'message'=>'Exhibitor information declined successful'), 200);
			
			}
			
			
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


	public function spouseUpdate(Request $request){ 

		$usersMain = \App\Models\User::where('id', $request->post('user_id'))->first();

		$rules = [
            'is_spouse' => 'required|in:Yes,No',
            'is_spouse_registered' => 'required|in:Yes,No',
            'id' => 'required',
		];

		if($request->post('is_spouse_registered')=='Yes'){

			$rules['email'] = 'required|email';

		}elseif($request->post('is_spouse_registered')=='No'){
 
			$rules['email'] = 'required|email|unique:users,email,'.$request->post('id');
			$rules['first_name'] = 'required';
			$rules['last_name'] = 'required';
			$rules['gender'] = 'required|in:1,2';
			$rules['date_of_birth'] = 'required|date';
			$rules['citizenship'] = 'required';
			$rules['salutation'] = 'required';
			$rules['spouse_contact_country_id'] = 'required';
			$rules['spouse_contact_state_id'] = 'required';
			$rules['spouse_contact_city_id'] = 'required';
			$rules['spouse_user_mobile_code'] = 'required';
			$rules['spouse_mobile'] = 'required';
			$rules['spouse_contact_business_codenumber'] = 'required';
			$rules['spouse_contact_business_number'] = 'required';
			$rules['spouse_contact_whatsapp_codenumber'] = 'required';
			$rules['spouse_contact_whatsapp_number'] = 'required';
			$rules['spouse_contact_zip_code'] = 'required';
			
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
				
				$dob=date('Y-m-d',strtotime($request->post('date_of_birth')));

				if((int)$request->post('id') == 0){
					

					if($request->post('is_spouse_registered')=='Yes'){

						$users = \App\Models\User::where([
							['email', '=', $request->post('email')],
							['id', '!=', $request->post('id')]
							])->first();

						if(!$users){
							
							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Spouse-not-found');
							return response(array("error"=>true, 'message'=>$message), 403);
						
						}elseif($users->added_as == 'Spouse'){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Spouse-already-associated-withother-user');
							return response(array("error"=>true, "message"=>$message), 403);
						}

						$spouseName = \App\Models\user::where('parent_id', $users->id)->where('added_as','Spouse')->first();
						if($spouseName){

							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Spouse-already-associated-withother-user');
							return response(array("error"=>true, "message"=>$message), 403);
						}
						if($spouseName && $spouseName->stage < 3){

							return response(array("error"=>true, "message"=>'Spouse Profile not complete'), 403);
						}

						$reminderData = [
							'type'=>'spouse_reminder',
							'date'=>date('Y-m-d'),
							'reminder'=>'0',
						];
						
						if($users->stage >= 2){

							$usersP = \App\Models\User::where('id',$request->post('id'))->first();
							$usersP->parent_spouse_stage = $users->stage;
							$usersP->room = 'Sharing';
							$users->parent_spouse_stage = $users->stage;
							$usersP->save(); 
						}
						
						$users->parent_id = $request->post('user_id');
						$users->added_as = 'Spouse';
						
						$users->spouse_confirm_token =md5(rand(1111,4444));
						$users->spouse_confirm_reminder_email =json_encode($reminderData);
						$users->save(); 

						$spouse_id = $users->id;

					}else if($request->post('is_spouse_registered')=='No'){
						
						$existSpouse = \App\Models\User::where([
							['parent_id', '=', $request->post('user_id')],
							['added_as', '=', 'Spouse']
							])->first();
		
						if($existSpouse){
		
							$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Youhave-already-updated-spouse-detail');
							return response(array("error"=>true, 'message'=>$message), 200);
		
						}else{

							$users = \App\Models\User::where('email', $request->post('email'))->first();

							if($users && $users->added_as == 'Spouse'){
								
								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Spouse-already-associated-withother-user');
								return response(array("error"=>true, "message"=>$message), 200);
							}

							$date1 = $dob;
							$date2 = date('Y-m-d');
							$diff = abs(strtotime($date2) - strtotime($date1));
							$years = floor($diff / (365*60*60*24));
						
							if($years<18){

								$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'DateOfBirthyear-mustbemore-than-18years');
								return response(array("error"=>true, "message"=>$message), 200);
							
							}else{
								
								
								$token = md5(rand(1111,4444));
								$reminderData = [
									'type'=>'spouse_reminder',
									'date'=>date('Y-m-d'),
									'reminder'=>'0',
		
								];
								
								$users = array(
									'parent_id'=> $request->post('user_id'),
									'added_as'=>'Spouse',
									'salutation'=>$request->post('salutation'),
									'name'=>$request->post('first_name'),
									'last_name'=>$request->post('last_name'),
									'email'=>$request->post('email'),
									'gender'=>$request->post('gender'),
									'dob'=>$dob,
									'citizenship'=>$request->post('citizenship'),
									'reg_type'=>'email',
									'designation_id'=>'2',
									'otp_verified'=>'No',
									'system_generated_password'=>'1',
									'spouse_confirm_token'=>$token,
									'spouse_confirm_reminder_email'=>json_encode($reminderData),
									'contact_country_id'=>$request->post('spouse_contact_country_id'),
									'contact_state_id'=>$request->post('spouse_contact_state_id'),
									'contact_city_id'=>$request->post('spouse_contact_city_id'),
									'phone_code'=>$request->post('spouse_user_mobile_code'),
									'mobile'=>$request->post('spouse_mobile'),
									'contact_business_codenumber'=>$request->post('spouse_contact_business_codenumber'),
									'contact_business_number'=>$request->post('spouse_contact_business_number'),
									'contact_whatsapp_codenumber'=>$request->post('spouse_contact_whatsapp_codenumber'),
									'contact_whatsapp_number'=>$request->post('spouse_contact_whatsapp_number'),
									'contact_zip_code'=>$request->post('spouse_contact_zip_code'),
								);

								$user =  \App\Models\User::insert($users);

								$spouse_id = \DB::getPdo()->lastInsertId();

							}
						}

					}

					\App\Models\User::where('id',$request->post('id'))->update(['spouse_id' => $spouse_id]);


				}else{

					$users = array(
						'parent_id'=> $request->post('user_id'),
						'added_as'=>'Spouse',
						'salutation'=>$request->post('salutation'),
						'name'=>$request->post('first_name'),
						'last_name'=>$request->post('last_name'),
						'email'=>$request->post('email'),
						'gender'=>$request->post('gender'),
						'dob'=>$dob,
						'citizenship'=>$request->post('citizenship'),
					);
			
					\App\Models\User::where('id',$request->post('id'))->update($users);

					$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel('en','Spouse-update-successful');
					return response(array("error"=>false, "message"=>$message), 200);

				}


				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Spouse-added-successful');
				return response(array("error"=>false, "message"=>$message), 200);

			} catch (\Exception $e) {
				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function roomUpdate(Request $request){
	
		$usersMain = \App\Models\User::where('id', $request->post('user_id'))->first();

		$rules = [
            'room' => 'required|in:Single,Sharing',
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
				
				$existSpouse = \App\Models\User::where([
					['parent_id', '=', $request->post('user_id')],
					['added_as', '=', 'Spouse']
					])->first();

				if($existSpouse){
					
					$existSpouse->added_as = null;
					$existSpouse->parent_id = null;
					$existSpouse->save();
	
				}
				$users = \App\Models\User::where('id',$request->post('user_id'))->first();
				
				$users->room = $request->post('room');
				$users->save();
				
				$subject='User Stay room update';
				$msg='User Stay room update';

				\App\Helpers\commonHelper::sendNotificationAndUserHistory($usersMain->id,$subject,$msg,'User Stay room update');
				
				$message = \App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Stay-room-update-successful');
				return response(array("error"=>true, "message"=>$message), 200);

			} catch (\Exception $e) {

				return response(array("error"=>true, "message"=>\App\Helpers\commonHelper::ApiMessageTranslaterLabel($usersMain->language,'Something-went-wrongPlease-try-again')), 403);
			}
		}

    }

	public function stageTwoDownloadExcelPassport(Request $request){

		try{

			$result = \App\Models\User::where([['designation_id', 2], ['parent_id', NULL], ['added_as', NULL], ['stage','>=',2]])->orderBy('updated_at', 'desc')->get();
			
			if($result->count()==0){

				return response(array('error'=>true,'message'=>'Data not found.'),200);

			}else{

				
					$delimiter = ",";  
					$filename = "Reach Report.csv";
					
					$f = fopen('php://memory', 'w'); 
					
					//$f = fopen('php://memory', 'w');
					$fields = array('Id', 'Current Stage', 
										'Candidate Name', 
										'Candidate email Address', 
										'Candidate Mobile Number', 
										'Country', 
										'Citizenship', 
										'Spouse Name', 
										'Spouse Email Address', 
										'Group Leader Name', 
										'Group Leader Email Address', 
										'Room Type', 
										'Pastor Trainer(yes/No)', 
										'Ministry Name', 
										'Total Payable Amount', 
										'Pending Amount', 
										'Accepted Amount', 
										'Payment in process', 
										'Payment Declined',
										'Stage 0', 
										'Stage 1', 
										'Stage 2', 
										'Stage 3', 
										'Stage 4', 
										'Stage 5',
										'Non-formal Pastoral Training', 
										'Formal Theological Education', 
										'Informal Personal Mentoring', 
										'Are you willing to commit to train one trainer of pastors per year for the next 7 years?', 
										'Comment', 
										'How many pastoral leaders are you involved in strengthening each year?', 
										'How many of them can serve as future pastor trainers?', 
										'Do you seek to add Pastoral Training to your ministry?',
										'Comment',
									); 
					fputcsv($f, $fields, $delimiter); 

					$i=1;
					foreach($result as $row){
						
						if($row['stage'] == 0){
							$stage0 = "In Process";
						}elseif($row['stage'] > 0){
							$stage0 = "Completed";
						}else{
							$stage0 = "Pending";
						}  

						if($row['stage'] == 1){
							$stage1 = "In Process";
						}elseif($row['stage'] > 1){
							$stage1 = "Completed";
						}else{
							$stage1 = "Pending";
						} 

						if($row['stage'] == 2){
							$stage2 = "In Process";
						}elseif($row['stage'] > 2){
							$stage2 = "Completed";
						}else{
							$stage2 = "Pending";
						} 

						if($row['stage'] == 3){
							$stage3 = "In Process";
						}elseif($row['stage'] > 3){
							$stage3 = "Completed";
						}else{
							$stage3 = "Pending";
						} 

						if($row['stage'] == 4){
							$stage4 = "In Process";
						}elseif($row['stage'] > 4){
							$stage4 = "Completed";
						}else{
							$stage4 = "Pending";
						} 

						if($row['stage'] == 5){
							$stage5 = "In Process";
						}elseif($row['stage'] > 5){
							$stage5 = "Completed";
						}else{
							$stage5 = "Pending";
						} 

						$userSpouse = \App\Models\User::with('TravelInfo')->where([['parent_id', $row['id']], ['added_as', 'Spouse']])->first();
						$userGroup = \App\Models\User::where([['parent_id', $row['id']],['added_as', 'Group']])->first();
						$spouseName = '';
						$spouseEmail = '';
						$mobile = '';
						$groupLeaderEmail= '';
						$groupLeaderName= '';
						$non_formal_trainor = '';
						$formal_theological = '';
						$informal_personal = '';
						$willing_to_commit = '';
						$comment = '';
						$howmany_pastoral = '';
						$howmany_futurepastor = '';
						$doyouseek_postoral = '';
						$doyouseek_postoralcomment = '';


						if($userGroup){
							$groupLeaderName= $row['name'].' '.$row['last_name'];
							$groupLeaderEmail= $row['email'];
						}if($userSpouse){
							$spouseName= $userSpouse->name.' '.$userSpouse->last_name;
							$spouseEmail= $userSpouse->email;
						}


						if($row['mobile']){
							$mobile = '+'.$row['phone_code'].' '.$row['mobile'];
						}

						

						if($row['ministry_pastor_trainer_detail']){

							$ministryPastorDetail=json_decode($row['ministry_pastor_trainer_detail'],true);

							$non_formal_trainor = $ministryPastorDetail['non_formal_trainor'] ?? '';
							$formal_theological = $ministryPastorDetail['formal_theological'] ?? '';
							$informal_personal = $ministryPastorDetail['informal_personal'] ?? '';
							$willing_to_commit = $ministryPastorDetail['willing_to_commit'] ?? '';
							$comment = $ministryPastorDetail['comment'] ?? '';
							$howmany_pastoral = $ministryPastorDetail['howmany_pastoral'] == '1-10' ? '01 to 10' : $ministryPastorDetail['howmany_pastoral'] ?? '';
							$howmany_futurepastor = $ministryPastorDetail['howmany_futurepastor'] == '1-10' ? '01 to 10' : $ministryPastorDetail['howmany_futurepastor'] ?? '';
						}

						if($row['doyouseek_postoral']){
							
							$doyouseek_postoral = $row['doyouseek_postoral'] ?? '';
							$doyouseek_postoralcomment = $row['doyouseek_postoralcomment'] ?? '';
						}

						$lineData = array(($i), 'Stage '.$row['stage'], 
						$row['name'].' '.$row['last_name'], 
						$row['email'], 
						$mobile, 
						\App\Helpers\commonHelper::getCountryNameById($row['contact_country_id']), 
						\App\Helpers\commonHelper::getCountryNameById($row['citizenship']), 
						$spouseName,
						$spouseEmail,
						$groupLeaderName,
						$groupLeaderEmail,
						$row['room'] ?? 'Double Deluxe',
						$row['ministry_pastor_trainer'],
						$row['ministry_name'],
						$row['amount'],
						\App\Helpers\commonHelper::getTotalPendingAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAcceptedAmount($row['id']),
						\App\Helpers\commonHelper::getTotalAmountInProcess($row['id']),
						\App\Helpers\commonHelper::getTotalRejectedAmount($row['id']),
						$stage0, $stage1, 
						$stage2, $stage3, $stage4, 
						$stage5,
						$non_formal_trainor,
						$formal_theological,
						$informal_personal,
						$willing_to_commit,
						$comment,
						$howmany_pastoral,
						$howmany_futurepastor,
						$doyouseek_postoral,
						$doyouseek_postoralcomment,

					); 
						
						fputcsv($f, $lineData, $delimiter); 
						
						$results = \App\Models\User::where([['parent_id', $row['id']],['stage','>=',2]])->get();
						$groupLeaderName = '';
						$groupLeaderEmail = '';
						$spouseLeaderName = '';
						$spouseLeaderEmail = '';
						
						$non_formal_trainor = '';
						$formal_theological = '';
						$informal_personal = '';
						$willing_to_commit = '';
						$comment = '';
						$howmany_pastoral = '';
						$howmany_futurepastor = '';
						$doyouseek_postoral = '';
						$doyouseek_postoralcomment = '';
						
						if(!empty($results) && count($results)>0){
								$j = 1;
							foreach($results as $val){

								
								if($val && $val->added_as == 'Group'){

									$groupLeaderName= $row['name'].' '.$row['last_name'];
									$groupLeaderEmail= $row['email'];
								}
								if($val && $val->added_as == 'Spouse'){
									
									$spouseLeaderName= $row['name'].' '.$row['last_name'];
									$spouseLeaderEmail= $row['email'];
									$groupLeaderName = '';
									$groupLeaderEmail = '';
								}


								if($val['stage'] == 0){
									$stage0 = "In Process";
								}elseif($val['stage'] > 0){
									$stage0 = "Completed";
								}else{
									$stage0 = "Pending";
								}  

								if($val['stage'] == 1){
									$stage1 = "In Process";
								}elseif($val['stage'] > 1){
									$stage1 = "Completed";
								}else{
									$stage1 = "Pending";
								} 

								if($val['stage'] == 2){
									$stage2 = "In Process";
								}elseif($val['stage'] > 2){
									$stage2 = "Completed";
								}else{
									$stage2 = "Pending";
								} 

								if($val['stage'] == 3){
									$stage3 = "In Process";
								}elseif($val['stage'] > 3){
									$stage3 = "Completed";
								}else{
									$stage3 = "Pending";
								} 

								if($val['stage'] == 4){
									$stage4 = "In Process";
								}elseif($val['stage'] > 4){
									$stage4 = "Completed";
								}else{
									$stage4 = "Pending";
								} 

								if($val['stage'] == 5){
									$stage5 = "In Process";
								}elseif($val['stage'] > 5){
									$stage5 = "Completed";
								}else{
									$stage5 = "Pending";
								} 


								if($val['mobile']){
									$mobile = '+'.$val['phone_code'].' '.$val['mobile'];
								}

								if($val['ministry_pastor_trainer_detail']){

									$ministryPastorDetail=json_decode($val['ministry_pastor_trainer_detail'],true);
		
									$non_formal_trainor = $ministryPastorDetail['non_formal_trainor'] ?? '';
									$formal_theological = $ministryPastorDetail['formal_theological'] ?? '';
									$informal_personal = $ministryPastorDetail['informal_personal'] ?? '';
									$willing_to_commit = $ministryPastorDetail['willing_to_commit'] ?? '';
									$comment = $ministryPastorDetail['comment'] ?? '';
									$howmany_pastoral = $ministryPastorDetail['howmany_pastoral'] == '1-10' ? '01 to 10' : $ministryPastorDetail['howmany_pastoral'] ?? '';
									$howmany_futurepastor = $ministryPastorDetail['howmany_futurepastor'] == '1-10' ? '01 to 10' : $ministryPastorDetail['howmany_futurepastor'] ?? '';
								}
		
								if($val['doyouseek_postoral']){
									
									$doyouseek_postoral = $val['doyouseek_postoral'] ?? '';
									$doyouseek_postoralcomment = $val['doyouseek_postoralcomment'] ?? '';
								}

								$lineData = array(($i), 'Stage '.$val['stage'], 
									$val['name'].' '.$val['last_name'], 
									$val['email'], 
									$mobile, 
									\App\Helpers\commonHelper::getCountryNameById($val['contact_country_id']), 
									\App\Helpers\commonHelper::getCountryNameById($val['citizenship']), 
									$spouseLeaderName,
									$spouseLeaderEmail,
									$groupLeaderName,
									$groupLeaderEmail,
									$val['room'] ?? 'Double Deluxe',
									$val['ministry_pastor_trainer'],
									$val['ministry_name'],
									$val['amount'],
									\App\Helpers\commonHelper::getTotalPendingAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAcceptedAmount($val['id']),
									\App\Helpers\commonHelper::getTotalAmountInProcess($val['id']),
									\App\Helpers\commonHelper::getTotalRejectedAmount($val['id']),
									$stage0, $stage1, 
									$stage2, $stage3, $stage4, $stage5,
									$non_formal_trainor,
									$formal_theological,
									$informal_personal,
									$willing_to_commit,
									$comment,
									$howmany_pastoral,
									$howmany_futurepastor,
									$doyouseek_postoral,
									$doyouseek_postoralcomment,
								); 
								fputcsv($f, $lineData, $delimiter);

								$j++;
							}
						}

						$i++;
					}
					
					fseek($f, 0); 
					
					header('Content-Encoding: UTF-8');
					header('Content-type: text/csv; charset=UTF-8');
					header("Content-Disposition: attachment; filename=Reach Report.csv");
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					echo "\xEF\xBB\xBF"; // UTF-8 BOM
					
					fpassthru($f);

					fclose($f);
					
					//readfile ($filename);

				
				exit; 

				return response(array('error'=>false,"message" => "File downloaded success"),200); 
				
			}
			
		}catch (\Exception $e){
		
			return response(array('error'=>true,"message" => "Something went wrong.please try again"),200); 
		
		}

	}

	public function spouseUsersList(Request $request) {

		if ($request->ajax()) {

			$id = \App\Models\User::where('email', $request->post('email'))->first()->id;
			$results = \App\Models\User::where([['parent_id', $id],['added_as', 'Spouse']])->get();

			$html = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;"> <thead> <tr> <th class="text-center">'. \Lang::get('admin.id') .'</th> <th class="text-center">'. \Lang::get('admin.addedas') .'</th> <th class="text-center">Name</th> <th class="text-center">'. \Lang::get('admin.action') .'</th> </tr> </thead><tbody>';
			
			if (count($results) > 0) {
				foreach ($results as $key=>$result) {

					$spouse = \App\Models\User::where([['parent_id', $result->id]])->first();

					$key += 1;
					$html .= '<tr>';
					$html .= '<td class="text-center">'.$key.'.</td>';

					$html .= '<td class="text-center">'.$result->added_as;
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->name.' '.$result->last_name;
					$html .= '</td>';

					$html .= '<td class="text-center">'.$result->email;
					$html .= '</td>';

					$html .= '<td class="text-center"><a href="'.route('admin.user.profile', ['id' => $result->id] ).'" title="View user profile" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-eye"></i></a>';
					$html .= '</td>';

					$html .= '</tr>';
				}
			} else {
				$html .= '<tr colspan="9"><td class="text-center">No Group Users Found</td></tr>';
			}
			$html .= '</tbody></table>';

			return response()->json(array('html'=>$html));
			
        }

	}

	
}
