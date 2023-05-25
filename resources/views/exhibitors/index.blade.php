
@extends('layouts/app')

@section('title',__(Lang::get('web/app.home')))

@push('custom_css')
    <style>
        .requirement .requirement-wrapper {
            display:flex;
        }
        .testimonial-wrapper {
            padding: 0 0 100px 0;
            padding-bottom: 174px !important;
        }
        .gpro-wrapper {
            position: relative;
            padding-bottom: 72px !important;
        }
        .banner-wrapper{
            position: relative;
        }
        .banner-wrapper::after{
            position: absolute;
            content: "";
            background: #0006;
            width: 100%;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 5;
        }
        .ban-wrap{
            position: relative;
            z-index: 99;
        }
        .footer-box:hover{

            background-color: #ffcd34;
            
        }
    </style>
@endpush

@section('content')

    <!-- banner-start -->
    <div class="banner-wrapper" style="background-image: url({{ asset('images/exhibitor_home.jpg')}})">
        <div class="container ban-wrap">
            <div class="banner-head">
                <!-- <p>@lang('web/home.banner-description2')</p> -->
                <h1>@lang('web/home.banner-heading') </h1>
                <h2 style="color:white;margin-top:33px"> @lang('web/app.exhibitor_portal')</h2>
                <h3 style="color:white;margin-top:33px">@lang('web/home.date_heading') </h3>
                <h3 style="color:white">“@lang('web/home.banner-description1')”</h3> 
                
                <ul class="date-map">
				<!--
                    <li>
                        <a href="javascript:;"><i class="fas fa-map-marker-alt"></i>2108 Selah Way Brattleboro, VT
                            05301</a>
                    </li>
					-->
                    <!-- <li>
                        <a href="javascript:;"><i class="fas fa-calendar-alt"></i>@lang('web/home.november') 12-17, 2023 (D.V.)</a>
                    </li> -->
                </ul>
                <div class="timer-wrapper">
                    <div id="countdown">
                        <ul>
                            <li><span id="days"></span> @lang('web/home.days')</li>
                            <li><span id="hours"></span>@lang('web/home.hours')</li>
                            <li><span id="minutes"></span>@lang('web/home.minutes')</li>
                            <li><span id="seconds"></span>@lang('web/home.seconds')</li>
                        </ul>
                    </div>
                </div>
               
                <div class="banner-btn">
                    <ul>
                        <!-- <li><a href="{{url('exhibitor-register')}}" class="main-btn" >@lang('web/home.register') @lang('web/home.now')</a></li> -->
                        <li><a href="{{url('exhibitor-register')}}" class="main-btn" >@lang('web/home.register')</a></li>
                        
                        <li><a href="{{ url('exhibitor-policy') }}" class="main-btn">@lang('web/app.exhibitor_policy')</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- banner-end -->

    <!-- video section start -->
    
    
    <br>
    <footer style="padding-top: 66px;padding-bottom: 257px;">
        <div class="container">
            <div class="row">
                <!-- <h2 class="main-head">Become an Exhibitor</h2> -->
                <h2 class="main-head">@lang('web/app.become_exhibitor')</h2>
                <h5 style="text-align:center;padding-top: 50px;"></h5>
                <div class="col-lg-12">
                    @if(App::getLocale() == 'pt')
                        <div class="footer-box" style="text-align: left;">
                            <h4 style="font-size: 20px;">Ao clicar no Portal do Expositor, o delegado devidamente cadastrado e pago será direcionado para uma nova página com o seguinte formulário de inscrição:</h4>
                            <span style="text-align: left;font-size: 20px;">1.   Por favor, indique o nome da sua organização (obrigatório).</span><br>
                            <span style="text-align: left;font-size: 20px;">2.   Forneça o endereço do site da sua organização (opcional).</span><br>
                            <span style="text-align: left;font-size: 20px;">3.   Forneça uma descrição de seus produtos e/ou serviços na área de treinamento de pastores. Inclua em sua descrição: </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(1) uma lista dos produtos e recursos que você trará para o Congresso;</span><br> 
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(2) quaisquer depoimentos de pastores que se beneficiaram dos produtos e/ou serviços de sua organização; e </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(3) uma breve declaração de por que você quer ser um expositor.</span><br>
                            <span style="text-align: left;font-size: 20px;">4.    u li a  <a href="{{ url('exhibitor-policy') }}" >Política do Expositor</a> do GProCongresso II (LINK) e concordo em pagar a taxa de exibição de $ 800 USD após a aprovação desta inscrição. (Por favor, marque a caixa se você concorda.)  </span><br>
                        </div>
                    @elseif(App::getLocale() == 'sp')
                        
                        <div class="footer-box" style="text-align: left;">
                            <h4 style="font-size: 20px;">Cuando un delegado que se haya inscrito y pagado íntegramente hace clic en el Portal del Expositor, accederá a una nueva página con el siguiente formulario de solicitud:</h4>
                            <span style="text-align: left;font-size: 20px;">1.   Indique el nombre de su organización (obligatorio).</span><br>
                            <span style="text-align: left;font-size: 20px;">2.   Proporcione la dirección del sitio web de su organización (opcional).</span><br>
                            <span style="text-align: left;font-size: 20px;">3.   Proporcione una descripción de sus productos y/o servicios en el campo de la formación de pastores. Incluya en su descripción: </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(1) una lista de los productos y recursos que traerá al Congreso;</span><br> 
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(2) cualquier testimonio de pastores que se hayan beneficiado de los productos y/o servicios de su organización; y </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(3) una breve declaración de por qué quiere ser exhibidor.</span><br>
                            <span style="text-align: left;font-size: 20px;">4.    He leído la <a href="{{ url('exhibitor-policy') }}" >Política de exhibidores</a> de GProCongress II y acepto pagar la tarifa de exhibición de $800 USD una vez que se apruebe esta solicitud. (Marque la casilla si está de acuerdo). </span><br>
                        </div>
                    @elseif(App::getLocale() == 'fr')

                        <div class="footer-box" style="text-align: left;">
                            <h4 style="font-size: 20px;">Lorsqu’un délégué entièrement inscrit et payé clique sur le portail des exposants, il sera redirigé vers une nouvelle page avec le formulaire de demande suivant :</h4>
                            <span style="text-align: left;font-size: 20px;">1.   Veuillez indiquer le nom de votre organisation (obligatoire).</span><br>
                            <span style="text-align: left;font-size: 20px;">2.   Veuillez fournir l’adresse du site Web de votre organisation   (facultatif).</span><br>
                            <span style="text-align: left;font-size: 20px;">3.   Veuillez fournir une description de vos produits et/ou services dans le   domaine de la formation des pasteurs. Veuillez inclure dans votre description : </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(1) une liste des produits et ressources que vous apporterez au Congrès ;</span><br> 
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(2) tout témoignage de pasteurs qui ont bénéficié des produits et/ou services de votre organisation ; et</span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(3)  une brève déclaration expliquant pourquoi vous souhaitez être exposant.</span><br>
                            <span style="text-align: left;font-size: 20px;">4.     J’ai lu la <a href="{{ url('exhibitor-policy') }}" >politique des exposants</a> du GProCongress II (LIEN) et j’accepte de payer les frais d’exposition de 800 USD lors de l’approbation de cette demande. (Veuillez cocher la case si vous êtes d’accord.) </span><br>
                        </div>
                    @else

                        <div class="footer-box" style="text-align: left;">
                            <h4 style="font-size: 20px;">When a fully-registered-and-paid delegate clicks on the Exhibitor Portal, they will be taken to a new page with the following application form:</h4>
                            <span style="text-align: left;font-size: 20px;">1.   Please state the name of your organization (required).</span><br>
                            <span style="text-align: left;font-size: 20px;">2.   Please provide your organization’s website address (optional).</span><br>
                            <span style="text-align: left;font-size: 20px;">3.   Please provide a description of your products and/or services in the field of pastor training. Please include in your description: </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(1) a list of the products and resources you will bring to the Congress;</span><br> 
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(2) any testimonials from pastors who have benefited from your organization’s products and/or services; and </span><br>
                            <span style="text-align: left;font-size: 20px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(3) a brief statement of why you want to be an exhibitor.</span><br>
                            <span style="text-align: left;font-size: 20px;">4.    I have read the GProCongress II <a href="{{ url('exhibitor-policy') }}" >Exhibitor Policy</a>, and I agree to pay the $800 USD exhibition fee upon approval of this application. </span><br>
                        </div>
                    @endif

                    
                </div>
            </div>
           
        </div>
    </footer>
    <!-- testimonial-end -->

@endsection 

@push('custom_js')
    <script>
        countdownStart();
    </script>
@endpush
