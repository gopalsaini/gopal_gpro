@extends('exhibitors/layouts/app')

@section('title',__('Exhibitor Policy'))

@section('content')

@push('custom_css')
<style>
    .loader{
        z-index: 1;
    }
    .loader .spinner-border{
        background: white;
    }
    .fs-dropdown {
        width: 94.3% !important;
    }
    .fs-label-wrap .fs-label {
        padding: 14px 22px 14px 16px !important;
    }
    .fs-label-wrap .fs-arrow {
        display: none;
    }

    .list-group-item.active {
        z-index: 2;
        color: #fff;
        background-color: #ffcd34;
        border-color: #ffcd34;
    }
</style>
@endpush

@if(App::getLocale() == 'pt')
    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">GProCongresso II – Política do Expositor</h4>
                <h5  style="text-align:center;padding-top: 50px;"></h5>
                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">A RREACH elaborou a
                    seguinte Política do Expositor para explicar quem pode ser expositor no
                    Congresso, como se tornar expositor e o custo a ser pago pelos expositores.</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">P: Quem pode ser expositor?</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">R: Somente delegados do Congresso e suas organizações
                    com produtos e serviços que irão capacitar e/ou aprimorar o treinamento de
                    pastores.</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">P: Como faço para me tornar um expositor?<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">R: Use este <a href="{{url('exhibitor-register')}}" style="color:red">LINK</a> para preencher sua inscrição. Assim
                    que sua inscrição for aprovada ou recusada pela equipe do GProCongresso II,
                    você será notificado e receberá mais instruções.<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><b><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">ATENÇÃO</font></span></b><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">: A última data para se
                    registrar como expositor é </font><b><font color="#000000">29 de
                    setembro de 2023.</font><o:p></o:p></b></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">P: Qual o valor a ser pago pelos expositores?<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">R: Cada expositor deve pagar a taxa de inscrição
                    padrão para o Congresso, mais uma taxa de exibição adicional de US$ 800.</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">OUTRAS POLÍTICAS</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%">1<font color="#000000">. A RREACH não será responsável por qualquer perda ou
                    dano ao estande do expositor, materiais de exibição ou produtos a qualquer
                    momento.<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">2. A RREACH não é responsável pela transferência do
                    estande do expositor, materiais de exibição ou produtos de ou para o Congresso
                    diariamente.</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">3. Não haverá transporte para o aeroporto (devido aos
                    itens grandes que os expositores podem estar carregando).<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:
                    12.0pt;line-height:107%"><font color="#000000">4. Nenhum expositor terá tempo para promover do palco
                    durante o Congresso.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt"><span style="font-size: 12pt; background-color: transparent;"><font color="#000000">5. Somente pessoas solteiras do mesmo sexo ou
                    casadas poderão dividir o quarto no Congresso. Se você estiver trazendo alguém
                    que não seja seu cônjuge do sexo oposto, terá que reservar um quarto separado
                    para essa pessoa.</font></span></p>
            </div>
                
        </div>
    </div>

@elseif(App::getLocale() == 'sp')

    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">GProCongress II - Politicas para Exhibidores</h4>
                <h5  style="text-align:center;padding-top: 50px;"></h5>
                <p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">RREACH
                    ha creado la siguiente Politica para Exhibidores para explicar quien puede ser
                    invitado para ser un exhibidor en el Congreso, como ser un exhibidor, el costo
                    que debe pagar un exhibidor. </font><o:p></o:p></span></p>

                    <p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">P: ¿Quién puede ser exibidor?</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">R: Solo
                    personas/organizaciones con productos y servicios que permitan/mejoren la
                    formación de pastores.<o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">&nbsp;</font></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">P: ¿Cómo me convierto en
                    exhibidor?</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">R: Utilice este</font> <a href="{{url('exhibitor-register')}}" style="color:red">ENLACE</a> <font color="#000000">para completar el formulario de solicitud. Una
                    vez que el equipo de GProCongress II apruebe o rechace su solicitud, se le
                    notificará y se le darán más instrucciones. <b>TENGA EN CUENTA</b>: La última fecha para registrarse como exhibidor es
                    el </font><b><font color="#000000">29 de septiembre de 2023.</font><o:p></o:p></b></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">P: ¿Cuál es el costo que
                    deben pagar los exhibidores?</font><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">R: Todo expositor deberá
                    pagar la cuota de inscripción estándar para el Congreso, más una cuota de
                    exhibición adicional de $800 USD:</font><o:p></o:p></span></p>

                    <p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal"><u><span lang="EN-US" style="font-size:12.0pt;line-height:
                    107%"><font color="#000000">OTRAS POLITICAS:</font></span></u><b><span lang="EN-US" style="font-size:12.0pt;line-height:107%"> </span></b><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><o:p></o:p></span></p>

                    <p class="MsoNormal" style="margin-left:70.85pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">&nbsp;</span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000">RREACH no será responsable por ninguna pérdida o daño al
                    stand del exhibidor, materiales de exposición o productos.</font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000">RREACH no será responsable por el traslado del stand del
                    exhibidor, materiales de exposición o productos hacia o desde el Congreso cada
                    dia.</font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000">No se brindarán traslados al aeropuerto (debido a los
                    artículos grandes que pueden llevar). </font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">4.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000">No se dará tiempo al aire a ningún exhibidor para promocionar
                    desde el escenario.</font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-left:72.0pt;text-indent:-18.0pt;mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%">5.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000">Solo podrán compartir habitación en el Congreso personas
                    solteras del mismo sexo o parejas casadas. Si trae a alguien que no sea su
                    cónyuge y que sea del sexo opuesto, deberá reservar una habitación separada
                    para ellos.o.</font><o:p></o:p></span></p>
            </div>
                
        </div>
    </div>

@elseif(App::getLocale() == 'fr')

    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">GProCongress II – Politique des exposants</h4>
                <h5  style="text-align:center;padding-top: 50px;"></h5>
                <p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                    0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">RREACH a élaboré la politique
                    suivante pour les exposants afin d’expliquer qui peut être invité à exposer au
                    congrès, comment devenir exposant, le coût à payer par les exposants.<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                    0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">Q : Qui peut
                    être exposant ?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">R : Uniquement
                    les délégués au Congrès et leurs organisations proposant des produits et des
                    services qui permettront et/ou amélioreront la formation des pasteurs.</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;<o:p></o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Q : Comment puis-je devenir
                    exposant ?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">A : Utilisez ce</font> <a style="color:red" href="{{url('exhibitor-register')}}">LIEN</a><font color="#000000"> pour remplir le formulaire de demande.&nbsp; Une fois votre demande serait approuvée ou
                    refusée par l’équipe GProCongress II, vous en serez informé et recevrez des
                    instructions supplémentaires.<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><b><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">VEUILLEZ NOTER :</font></span></b><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"> La date limite pour s’inscrire en tant qu’exposant
                    est le </font><b><font color="#000000">29 septembre 2023.</font><o:p></o:p></b></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;</span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Q : Quel est le coût à payer
                    par les exposants ?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">R : Chaque exposant doit
                    payer les frais d’inscription standard pour le Congrès, plus des frais
                    d’exposition supplémentaires de 800 USD.</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                    12.0pt;margin-left:0cm;line-height:normal"><span lang="EN-US" style="font-size:
                    12.0pt">&nbsp;<u><font color="#000000">AUTRES POLITIQUES<o:p></o:p></font></u></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                    0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000">&nbsp;<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">À tout moment, RREACH ne sera pas
                    responsable de toute perte ou dommage au stand de l’exposant, au matériel
                    d’exposition ou aux produits.</font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">2.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">RREACH ne sera pas responsable du transfert
                    quotidien du stand, du matériel d’exposition ou des produits de l’exposant vers
                    ou depuis le Congrès.</font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">3.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Aucun transfert aéroport ne sera fourni (en
                    raison des articles volumineux qu’ils peuvent transporter).</font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">4.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--><font color="#000000"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Aucun moment ne sera accordé aux exposants
                    pour faire de la promotion depuis la scène</font> <font color="#000000">pendant le Congrès.</font></span><span lang="EN-US" style="font-size:7.0pt"><font color="#000000">&nbsp;&nbsp; </font></span></font></font><span lang="EN-US" style="font-size:12.0pt"><o:p></o:p></span></font></p><p>

                    </p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1"><!--[if !supportLists]--><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">5.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; font-kerning: auto; font-optical-sizing: auto; font-feature-settings: normal; font-variation-settings: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></span><!--[endif]--></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Seules les personnes non mariées de même
                    sexe ou les couples mariés seront autorisés à partager une chambre au Congrès.
                    Si vous venez avec une personne du sexe opposé autre que votre conjoint/e, vous
                    devrez lui réserver une chambre séparée.</font><o:p></o:p></span></p>

            </div>
                
        </div>
    </div>

@else

    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">GProCongress II – Exhibitor Policy</h4>
                <h5  style="text-align:center;padding-top: 50px;"></h5>
                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">RREACH
                    has created the following Exhibitor Policy to explain who can be an exhibitor
                    at the Congress, how to become an exhibitor, and the cost to be paid by
                    exhibitors.<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Q:&nbsp; &nbsp; &nbsp; Who
                    can be an exhibitor?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">A: &nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000">Only
                    Congress delegates and their organizations with products and services that will
                    enable and/or enhance the training of pastors.</font></span><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;"> <o:p></o:p></span></font></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><span lang="EN-US"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;text-indent:36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Q:&nbsp; &nbsp; &nbsp; How
                    do I become an exhibitor?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">A:&nbsp; &nbsp; Use
                    this <a href="{{url('exhibitor-register')}}" style="color:red">LINK</a> to fill out your application. Once
                    your application is approved or declined by the GProCongress II Team, you will
                    be notified and given further instructions. <o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><b><span lang="EN-US" style="font-size:12.0pt">PLEASE
                    NOTE:</span></b><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"> The last date to
                    register as an exhibitor is <b>September
                    29, 2023.</b></font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Q:&nbsp; &nbsp; &nbsp; What
                    is the cost to be paid by exhibitors?<o:p></o:p></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">A:&nbsp; &nbsp; &nbsp; Every
                    exhibitor must pay the standard registration fee for the Congress, plus an
                    additional exhibition fee of $800 USD.&nbsp; <o:p></o:p></font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;
                    font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><u><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">OTHER
                    POLICIES<o:p></o:p></font></span></u></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;text-align:
                    justify;line-height:normal"><span lang="EN-US" style="font-size:7.0pt;font-family:
                    &quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">1.&nbsp; &nbsp; &nbsp;&nbsp;<font color="#000000" style="">RREACH
                    will not be responsible for any loss or damage to the exhibitor’s booth,
                    display materials, or products at any time.</font></span><span lang="EN-US"><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">2.</span><span lang="EN-US" style="font-size:
                    7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;</span><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">RREACH is not responsible for the transfer of the
                    exhibitor’s booth, display materials, or products to or from the Congress
                    daily. </font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">3.</span><span lang="EN-US" style="font-size:
                    7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">No airport transfers will be provided (due to the
                    large items exhibitors may be carrying). </font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt">4.</span><span lang="EN-US" style="font-size:
                    7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">No time will be given to any exhibitors to promote
                    from the stage during the Congress.</font><o:p></o:p></span></font></p><p>
                    </p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-align:justify;line-height:normal"><font color="#000000"><a name="_heading=h.gjdgxs"></a></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">5.&nbsp; &nbsp; Only unmarried people of the same sex
                    or married couples will be allowed to share a room at the Congress. If you are
                    bringing someone other than your spouse who is of the opposite sex, you will
                    have to reserve a separate room for them.</font><o:p></o:p></span></p>

            </div>
                
        </div>
    </div>

@endif

@endsection



@push('custom_js')

    <script>

        $('.fSelect').fSelect({
			placeholder: 'Select some options',
			overflowText: '{n} selected',
			noResultsText: 'No results found',
			searchText: 'Search',
			showSearch: true
		});

    </script>

@endpush