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
		height: 60px; 
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
		<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/pdf_logo.png')))}}" style="width:150px;">
	</header>

	<footer style="width: 100%;">
		<img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/pdf_footer.png')))}}" style="display: flex; justify-content: end;">
		<p style="text-align: center;font-size:10px">17110 Dallas Parkway    |    Suite 230    |    Dallas, Texas 75248    |    972.528.6100 phone    |    www.rreach.org </p>  
	</footer>

	<div class="row">
    
		<p >Estimado {{$salutation}} {{$name}},
		<br>Número de Pasaporte: {{$passport_no}}
		<br>País: {{$citizenship}}</p>
		<p>Esta nota es para confirmar que su registro al GProCongress II ha sido aceptado(a), y que usted está invitado(a) a asistir al Congreso en Ciudad de Panamá, Panamá, del 12 al 17 de noviembre, 2023</p>
		<p>RREACH le ofrece una importante ayuda financiera para que pueda asistir al Congreso. En primer lugar, su cuota de inscripción se ha rebajado a ${{$amount}}. (El costo real para RREACH para que cada delegado asista al Congreso es de unos U$A $1,750.00 – mil setecientos cincuenta dólares americanos.) Además, RREACH cubrirá los siguientes gastos</p>
		<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Transporte a/desde el aeropuerto al hotel
		<br>&nbsp;&nbsp;&nbsp;&nbsp;2.	Un cuarto por cinco noches en el hotel Westin Playa Bonita Panamá
		<br>&nbsp;&nbsp;&nbsp;&nbsp;3.	Todas las comidas durante el congreso; y
		<br>&nbsp;&nbsp;&nbsp;&nbsp;4.	Todos los materiales del Congreso</p>
		<p>Esta es una oportunidad única para que usted se conecte con compañeros de ideas afines, que sirven a pastores sin formación en todas partes, y para construir nuevas relaciones con los líderes actuales y emergentes en la formación de pastores.  En segundo lugar, es un momento maravilloso para que reflexione sobre su vocación ministerial, visualice la próxima temporada de su vida y ministerio, piense estratégicamente sobre la implementación y evalúe la eficacia de su ministerio. En tercer lugar, es un gran lugar para que usted encuentre recursos para su futuro ministerio - aprender a recibir la fuerza del Señor Jesucristo para continuar su trabajo, encontrar socios comprometidos para el estímulo mutuo en los próximos años, y recopilar ideas funcionales de nuevos modelos al relacionarse y apoyarse mutuamente con otros capacitadores pastorales.</p>
		<p>Esperamos que venga a Panamá este noviembre y forme parte de este encuentro mundial de formadores de pastores, diseñado para mejorar la salud pastoral y promover la salud de la iglesia en más de 200 naciones y, en última instancia, para llegar a los próximos mil millones de personas con salud espiritual.</p>
		<p>Para la gloria de Cristo, y para la belleza de su Prometida, </p>
		<p>For the glory of Christ, and for the beauty of His Bride,</p>
		<p><img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/rajiv_richard.png')))}}" style="width:100px;"></p><br>
		<p>Rajiv Richard</br> GProCongress II Coordinator</br> Email: info@gprocongress.org</p>

</div>

</html>