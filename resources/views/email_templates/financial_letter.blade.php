<!DOCTYPE html>
<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">

    <head>
    <title></title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width,initial-scale=1" name="viewport" />
  
    <style>
	@page {
		margin: 100px 25px;
	}

	header {
		position: fixed;
		top: -90px;
		left: 0px;
		right: 0px;
		font-size: 20px !important;
		text-align: left;
		line-height: 55px;
		height: 80px; 
	}

	footer {
		position: fixed; 
		bottom: -60px; 
		left: 0px; 
		right: 0px;
		height: 50px; 
		font-size: 20px !important;
		color: '#000';
		line-height: 35px;
	}
</style>
</head>
	<header>
		<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/pdf_logo.png')))}}" style="width:200px;">
	</header>

	<footer style="width: 100%;">
		<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/pdf_footer.png')))}}" style="display: flex; justify-content: end;">
		<p style="text-align: center;font-size:10px">17110 Dallas Parkway    |    Suite 230    |    Dallas, Texas 75248    |    972.528.6100 phone    |    www.rreach.org </p>  
	</footer>

	<div class="row">
		@if($lang == 'sp' )

				<p >Estimado {{$salutation}} {{$name}},</p>
				<p>Número de Pasaporte: {{$passport_no}}</p>
				<p>País: {{$citizenship}}</p>
				<p>Esta nota es para confirmar que su registro al GProCongress II ha sido aceptado(a), y que usted está invitado(a) a asistir al Congreso en Ciudad de Panamá, Panamá, del 12 al 17 de noviembre, 2023</p>
				<p>RREACH le ofrece una importante ayuda financiera para que pueda asistir al Congreso. En primer lugar, su cuota de inscripción se ha rebajado a ___________. (El costo real para RREACH para que cada delegado asista al Congreso es de unos U$A ${{$amount}} – mil setecientos cincuenta dólares americanos.) Además, RREACH cubrirá los siguientes gastos</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Transporte a/desde el aeropuerto al hotel</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Un cuarto por cinco noches en el hotel Westin Playa Bonita Panamá</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Todas las comidas durante el congreso; y</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Todos los materiales del Congreso</p>
				<p>Esta es una oportunidad única para que usted se conecte con compañeros de ideas afines, que sirven a pastores sin formación en todas partes, y para construir nuevas relaciones con los líderes actuales y emergentes en la formación de pastores.  En segundo lugar, es un momento maravilloso para que reflexione sobre su vocación ministerial, visualice la próxima temporada de su vida y ministerio, piense estratégicamente sobre la implementación y evalúe la eficacia de su ministerio. En tercer lugar, es un gran lugar para que usted encuentre recursos para su futuro ministerio - aprender a recibir la fuerza del Señor Jesucristo para continuar su trabajo, encontrar socios comprometidos para el estímulo mutuo en los próximos años, y recopilar ideas funcionales de nuevos modelos al relacionarse y apoyarse mutuamente con otros capacitadores pastorales.</p>
				<p>Esperamos que venga a Panamá este noviembre y forme parte de este encuentro mundial de formadores de pastores, diseñado para mejorar la salud pastoral y promover la salud de la iglesia en más de 200 naciones y, en última instancia, para llegar a los próximos mil millones de personas con salud espiritual.</p>
				<p>Para la gloria de Cristo, y para la belleza de su Prometida, </p>
				<p>For the glory of Christ, and for the beauty of His Bride,</p>
				<p><img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/rajiv_richard.png')))}}" style="width:150px;"></p><br>
				<p>Rajiv Richard</p>
				<p>GProCongress II Coordinator</p>
				<p>Email: info@gprocongress.org</p>

		@elseif($lang == 'fr' )

			<p >Cher  {{$salutation}} {{$name}},</p>
				<p>Numéro de passeport : {{$passport_no}}</p>
				<p>Pays : {{$citizenship}}</p>
				<p>Cette lettre confirme que votre candidature au GProCongress II a été acceptée et que vous êtes invité à participer au congrès qui se tiendra à Panama City, au Panama, du 12 au 17 novembre 2023.</p>
				<p>RREACH vous apporte une aide financière considérable pour vous permettre de participer au congrès. Tout d'abord, vos frais d'inscription ont été réduits à ___________. (Le coût de la participation de chaque délégué au congrès est d'environ 1 ${{$amount}}.) En plus, RREACH couvrira les dépenses suivantes </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Transferts de l'aéroport vers/depuis l'hôtel.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Une chambre pour cinq nuits à l'hôtel Westin Playa Bonita Panama.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Tous les repas pendant le congrès ; et</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Tout le matériel nécessaire pour le congrès.</p>
				<p>En conséquent, les seuls frais à votre charge sont les frais d'inscription mentionnés ci-dessus et votre billet d'avion pour le Panama. Tous les autres frais secondaires et les frais de visa (le cas échéant) sont à votre charge</p>
				<p>Il s'agit d'une occasion unique pour vous d'entrer en contact avec des pairs partageant les mêmes idées, qui servent des pasteurs sous-formés partout dans le monde, et de bâtir de nouvelles relations avec des leaders actuels et émergents dans le domaine de la formation des pasteurs. Deuxièmement, c'est un moment merveilleux pour vous de réfléchir à votre appel au ministère, d'envisager la prochaine saison de votre vie et de votre ministère, de penser stratégiquement à la mise en œuvre et d'évaluer l'efficacité de votre ministère. Troisièmement, c'est un endroit idéal pour trouver des ressources pour votre futur ministère - apprendre comment recevoir la force du Seigneur Jésus-Christ pour continuer votre travail, trouver des partenaires engagés pour un encouragement mutuel dans les années à venir, et rassembler des idées éprouvées et de nouveaux modèles dans les partenariats, le soutien et l'exécution de la formation des pasteurs.</p>
				<p>Nous espérons que vous viendrez au Panama en novembre et que vous participerez à ce rassemblement mondial de formateurs de pasteurs, conçu pour améliorer la santé pastorale et faire progresser la santé de l'Église dans plus de 200 pays et, en fin de compte, pour atteindre le prochain milliard d'individus avec la santé spirituelle. </p>
				<p>Pour la gloire du Christ et la beauté de son épouse, </p>
				<p><img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/rajiv_richard.png')))}}" style="width:150px;"></p><br>
				<p>Rajiv Richard</p>
				<p>Coordinateur de GProCongress II</p>
				<p>Email : info@gprocongress.org</p>

		@elseif($lang == 'pt' )

			<p >Caro nome {{$salutation}} {{$name}},</p>
				<p>Número do passaporte: {{$passport_no}}</p>
				<p>País: {{$citizenship}}</p>
				<p>Esta carta confirma que a sua candidatura ao GProCongresso II foi aceita e que está convidado a participar no Congresso na Cidade do Panamá, Panamá, de 12 a 17 de Novembro de 2023.</p>
				<p>A RREACH está a lhe fornecer uma ajuda financeira significativa para que possa participar no Congresso. Em primeiro lugar, a sua taxa de inscrição foi reduzida para ___________. (O custo da RREACH para cada delegado participar no Congresso é de cerca de ${{$amount}}) Além disso, a RREACH cobrirá as seguintes despesas: </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Transferências do aeroporto para o hotel e vice-versa</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	Um quarto para cinco noites no Westin Playa Bonita Panama Hotel.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	Todas as refeições durante o Congresso; e</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	Todos os materiais para o Congresso.</p>
				<p>Por conseguinte, os únicos custos pelos quais é responsável são a taxa de inscrição acima indicada e as passagens  de avião para o Panamá. Todas as outras despesas e a taxa de visto (se aplicável) serão custeadas pelo participante</p>
				<p>Esta é uma oportunidade única para se relacionar com colegas que pensam da mesma forma, que servem pastores sem formação em todo o mundo, e para construir novas relações com líderes atuais em exercício e emergentes na formação de pastores.  Em segundo lugar, é um momento maravilhoso para refletir sobre a sua vocação ministerial, para planejar a próxima época da sua vida e do seu ministério, para pensar estrategicamente sobre a implementação e para avaliar a eficácia do seu ministério. Em terceiro lugar, é um ótimo lugar para encontrar recursos para o seu futuro ministério - aprender como receber força do Senhor Jesus Cristo para continuar o seu trabalho, encontrar parceiros empenhados para encorajamento mútuo nos próximos anos; e reunir ideias comprovadas e novos modelos em parcerias de formação de pastores, apoio e entrega</p>
				<p>Esperamos que venha ao Panamá em Novembro e faça parte deste encontro global de formadores de pastores, concebido para melhorar a saúde pastoral e fazer avançar a saúde da igreja em mais de 200 nações e, em última análise, para alcançar os próximos mil milhões de indivíduos com saúde espiritual. Pela glória de Cristo, e beleza da Sua Noiva,</p>
				<p><img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/rajiv_richard.png')))}}" style="width:150px;"></p><br>
				<p>Rajiv Richard</p>
				<p>GProCongress II Coordinator</p>
				<p>Email: info@gprocongress.org</p>
		@else
			<p >Dear {{$salutation}} {{$name}},</p>
				<p>Passport Number: {{$passport_no}}</p>
				<p>Country: {{$citizenship}}</p>
				<p>This letter will confirm that your application to GProCongress II has been accepted, and that you are invited to attend the Congress in Panama City, Panama, from November 12-17, 2023.</p>
				<p>RREACH is providing you with significant financial assistance, so that you can attend the Congress. First, your registration fee has been discounted to ___________. (RREACH’s cost for each delegate to attend the Congress is around ${{$amount}}.) In addition, RREACH will cover the following expenses: </p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Airport transfe	rs to/from the hotel.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;2.	A room for five nights at the Westin Playa Bonita Panama Hotel.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;3.	All meals during the Congress; and</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;4.	All materials for the Congress.</p>
				<p>Therefore, the only costs you are responsible for are your registration fee listed above, and your airfare to Panama. All other incidental expenses and visa fee (if applicable) will be borne by you.</p>
				<p>This is a unique opportunity for you to <b>connect with like-minded peers</b>, who serve undertrained pastors everywhere, and to <b>build new relationships</b> with current and emerging leaders in pastor training.  Secondly, it’s a wonderful time for you to <b>reflect</b> on your ministry calling, to <b>envision</b> the next season of your life and ministry, to <b>think</b> strategically about implementation, and to assess your ministry effectiveness. Thirdly, it’s a great place for you to <b>find resources</b> for your future ministry – learn how to receive <b>strength from the Lord Jesus Christ</b> to continue your work, find committed partners for mutual encouragement in coming years; and gather <b>proven ideas and new models</b> in pastor training partnerships, support, and delivery.</p>
				<p>We hope that you will come to Panama this November, and be part of this global gathering of pastor trainers, designed to enhance pastoral health and advance church health in 200+ nations, and ultimately to reach the <b>next billion individuals</b> with spiritual health.  </p>
				<p>For the glory of Christ, and for the beauty of His Bride,</p>
				<p><img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/rajiv_richard.png')))}}" style="width:150px;"></p><br>
				<p>Rajiv Richard</p>
				<p>GProCongress II Coordinator</p>
				<p>Email: info@gprocongress.org</p>

		@endif
		
	</div>

</html>