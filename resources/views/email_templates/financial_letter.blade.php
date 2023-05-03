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
		
			<p >Dear {{$salutation}} {{$name}},</p>
			<p>Passport Number: {{$passport_no}}</p>
			<p>Country: {{$citizenship}}</p>
			<p>This letter will confirm that your application to GProCongress II has been accepted, and that you are invited to attend the Congress in Panama City, Panama, from November 12-17, 2023.</p>
			<p>RREACH is providing you with significant financial assistance, so that you can attend the Congress. First, your registration fee has been discounted to ___________. (RREACH’s cost for each delegate to attend the Congress is around ${{$amount}}.) In addition, RREACH will cover the following expenses: </p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;1.	Airport transfers to/from the hotel.</p>
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

	</div>

</html>