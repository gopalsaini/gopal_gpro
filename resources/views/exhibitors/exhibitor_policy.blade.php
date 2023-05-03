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
                <p class="MsoNormal"><span lang="EN-US"><font color="#000000">A RREACH criou a seguinte Política do
                        Expositor para explicar quem pode ser convidado para ser expositor no
                        Congresso, o custo a ser pago pelos expositores e o que é fornecido em troca
                        desse pagamento.</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000">Duas opções são oferecidas aos expositores
                        - uma inclui um quarto de hotel e a outra permite que o expositor faça seus
                        próprios arranjos de acomodação.</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000">P: Quem pode ser expositor?</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000">R: Somente pessoas/organizações com
                        produtos que irão possibilitar/aprimorar o treinamento de pastores</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><b>Opção 1 – Inclui quarto individual no
                        Dreams Hotel.</b></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><b><br></b><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">1.Custo para participar do Congresso –</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000">a. O custo para cada expositor é de $ 1.500
                        USD (inclui taxa de inscrição e ocupação de quarto individual por 5 noites).</font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000">b. Se o seu cônjuge o acompanhar, o custo
                        será de $ 1.850,00 (inclui taxa de inscrição para duas pessoas e ocupação de
                        quarto individual por 5 noites).</font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000">c. Se você trouxer um companheiro de equipe
                        que precise de um quarto adicional, o custo será de&nbsp;</font></span><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;">$ 2.500,00 (inclui taxa de inscrição para
                        duas pessoas e dois quartos individuais por 5 noites).</span></p><p class="MsoNormal" style="margin-left: 100px;"><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;"><br></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">2. A RREACH não será responsável por
                        qualquer perda ou dano ao estande do expositor, materiais de exibição ou
                        produtos.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">3. A RREACH não será responsável pela
                        transferência do estande do expositor, materiais de exibição ou produtos de ou
                        para o Congresso diariamente.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">4. Não serão fornecidos transfers do
                        aeroporto (devido a itens grandes que possam estar carregando). No entanto, os
                        expositores podem utilizar o transporte oferecido diariamente desde o Hotel
                        Dreams até o Hotel Westin Playa Bonita, onde será realizado o Congresso.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">5. Nenhum tempo de publicidade será dado a
                        nenhum expositor para promover a partir do palco.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">6. Os expositores poderão assistir aos
                        cultos e sessões plenárias do Congresso, mas não às salas de descanso.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">7. O café da manhã será servido todas as
                        manhãs no Dreams Hotel.&nbsp;</font></span><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;">O almoço e o jantar serão servidos todos os
                        dias no Westin Playa Bonita Hotel.</span></p><p class="MsoNormal" style="margin-left: 50px;"><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;"><br></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">8. Uma mesa e duas cadeiras serão
                        fornecidas a cada expositor para cada dia do Congresso.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000">&nbsp;</font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><b>Opção 2 – Os expositores fornecem suas
                        próprias acomodações.</b></font></span></p><p class="MsoNormal"><span lang="EN-US"><font color="#000000"><b><br></b><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">1. Custo para participar do Congresso –</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000">Taxa de inscrição – $ 750 USD para uma
                        pessoa; $ 300 USD para cada pessoa adicional.</font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 100px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">2. A RREACH não será responsável por
                        qualquer perda ou dano ao estande do expositor, materiais de exibição ou
                        produtos.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">3. A RREACH não será responsável pela
                        transferência do estande do expositor, materiais de exibição ou produtos de ou
                        para o Congresso diariamente.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">4. Não serão fornecidos transfers do
                        aeroporto (devido a itens grandes que possam estar carregando).</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">5. Nenhum tempo de antena será dado a
                        nenhum expositor para promover a partir do palco.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">6. Os expositores poderão assistir aos
                        cultos e sessões plenárias do Congresso, mas não às salas de descanso.</font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">7. O almoço e o jantar serão servidos
                        diariamente no Westin Playa Bonita Hotel.&nbsp;</font></span><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;">(O café da manhã não está incluído nesta
                        opção.)</span></p><p class="MsoNormal" style="margin-left: 50px;"><span style="color: rgb(0, 0, 0); letter-spacing: inherit; background-color: transparent;"><br></span></p><p>

                        </p><p class="MsoNormal" style="margin-left: 50px;"><span lang="EN-US"><font color="#000000">8. Uma mesa e duas cadeiras serão
                        fornecidas a cada expositor para cada dia do Congresso.</font><o:p></o:p></span></p>

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
                    ha creado la siguiente politica para los exhibidores con el fin de explicar
                    quien puede ser invitado a ser un exhibidor en el congreso, el costo que deben
                    pagar los mismos, y qué se dará a cambio por dicho pago.</font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"> <o:p></o:p></font></span></p>

                    <p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Se
                    ofrecen dos opciones para los Exhibidores: una incluye la habitación de hotel,
                    y la otra le permite a los exhibidores hacer sus propios arreglos para
                    hospedaje.</font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"> <o:p></o:p></font></span></p>

                    <p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">P:
                    ¿Quién puede ser un exhibidor?</font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font></span></p><p class="MsoNormal"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><o:p></o:p></font></span></p>

                    <p class="MsoNormal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">R:
                    Solo personas/organizaciones con productos que van a facilitar/mejorar la
                    capacitación de pastores.</font></span></font></p><p class="MsoNormal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font></span></font></p><p class="MsoNormal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"> </font></span><span lang="EN-US"><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><b><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000"><b>Opción 1 – Incluye habitación individual en Dreams Hotel.</b></font></span></b></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt"><b><span lang="EN-US" style="font-size:12.0pt;
                    line-height:107%"><font color="#000000"><b><br></b><o:p></o:p></font></span></b></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">1.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span></font><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Costo para asistir al
                    Congreso –</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">a.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span></font><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">El costo para cada
                    exhibidor es de $1,500 USD (incluye la inscripción y el alojamiento en
                    habitación individual por 5 noches).</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">b</font>.</span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp; </span></font><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Si lo acompaña su cónyuge,
                    el costo será de $1,850.00 (incluye la inscripción de dos personas y el
                    alojamiento en habitación habitación individual por 5 noches).</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">c.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000"> &nbsp;</font>&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Si trae un compañero de
                    equipo que necesita una habitación adicional, el costo será de $ 2,500.00
                    (incluye la inscripción para dos personas y dos habitaciones individuales por 5
                    noches).</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:56.65pt;margin-bottom:.0001pt;text-indent:-21.25pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">2.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000">&nbsp;</font></span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">RREACH no será responsable por ninguna
                    pérdida o daño al stand, materiales de exhibición o productos del exhibidor.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:56.65pt;margin-bottom:.0001pt;text-indent:-21.25pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">3.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">RREACH no será responsable
                    por el traslado diario del stand del exhibidor, materiales de exhibición o
                    productos hacia o desde el Congreso.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">4</font>.</span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">No se proporcionarán traslados
                    al aeropuerto (debido a los artículos de gran tamaño que puedan llevar). Sin
                    embargo, los exhibidores pueden utilizar el transporte provisto cada día desde
                    el Hotel Dreams hasta el Hotel Westin Playa Bonita, donde se desarrolla el
                    Congreso.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">5.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">No se dará tiempo para
                    promoción a ningún exhibidor desde el escenario.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">6</font>.</span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Los exhibidores podrán
                    asistir a las&nbsp; plenarias y los tiempos de
                    alabanza durante el Congreso, pero no a los talleres.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">7.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;<font color="#000000">&nbsp; </font></span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">El desayuno se brindará
                    todas las mañanas en el Dreams Hotel. El almuerzo y la cena se brindarán todos
                    los días en el Hotel Westin Playa Bonita</font>.</span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><br></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">8.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000"> &nbsp;</font>&nbsp;&nbsp; </span></font><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Se proporcionará una mesa
                    y dos sillas a cada exhibidor para cada día del Congreso.</font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><o:p><font color="#000000">&nbsp;</font></o:p></span></p>

                    <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><b><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"><b>Opción 2 – Los exhibidores se ocuparán de su propio
                    alojamiento.</b></font></span></b></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><b><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"><b><br></b><o:p></o:p></font></span></b></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">1.</font></span><span lang="EN-US" style="font-size:
                    7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000">
                    </font>&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Costo para asistir al Congreso</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"><br></font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"> </font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Costo de inscripción: $ 750 USD por persona; $300 USD
                    por cada persona adicional.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:72.0pt;margin-bottom:.0001pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000"><o:p></o:p></font></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:56.65pt;margin-bottom:.0001pt;text-indent:-21.25pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">2</font>.</span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000"> </font></span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">RREACH no será responsable por
                    ninguna pérdida o daño al stand, materiales de exhibición o productos del
                    exhibidor.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:56.65pt;margin-bottom:.0001pt;text-indent:-21.25pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">3.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">RREACH no será responsable
                    por el traslado diario del stand del exhibidor, materiales de exhibición o
                    productos hacia o desde el Congreso.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">4.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000"> &nbsp;</font>&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">No se proporcionarán
                    traslados al aeropuerto (debido a los artículos de gran tamaño que puedan
                    llevar).</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"> </font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">5</font>.</span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">No se dará tiempo para
                    promoción a ningún exhibidor desde el escenario.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">6.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"> &nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000">Los exhibidores podrán
                    asistir a las&nbsp; plenarias y los tiempos de
                    alabanza durante el Congreso, pero no a los talleres.</font></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:107%"><font color="#000000"><br></font><o:p></o:p></span></font></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="color: rgb(0, 0, 0); font-size: 12pt;"><font color="#000000">7.</font></span><span lang="EN-US" style="color: rgb(0, 0, 0); font-size: 7pt; font-family: &quot;Times New Roman&quot;, serif;"><font color="#000000">
                    </font>&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size: 12pt;"><font color="#000000" style="color: rgb(0, 0, 0);">El almuerzo y la cena se brindarán todos los días en
                    el Hotel Westin Playa Bonita.</font><font style="">&nbsp;</font></span><span style="color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit; text-indent: 35pt; background-color: transparent;">(El desayuno no está incluido en esta
                    opción).</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span style="color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit; text-indent: 35pt; background-color: transparent;"><br></span></p>

                    <p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">8.</font></span><span lang="EN-US" style="font-size:
                    7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;">
                    &nbsp;&nbsp;&nbsp; </span></font><span lang="EN-US" style="font-size:12.0pt"><font color="#000000">Se proporcionará una mesa y dos sillas a cada
                    exhibidor para cada día del Congreso.</font><o:p></o:p></span></p>
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
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">RREACH a créé la politique suivante
                        pour les exposants afin de clarifier qui peut être invité à être exposant au
                        congrès, le coût à payer par les exposants et ce qui est fourni en échange d’un
                        tel paiement.</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;
                        line-height:115%"><font color="#000000">Deux options sont offertes aux exposants – l’une comprend une
                        chambre d’hôtel et l’autre permet à l’exposant de prendre ses propres
                        dispositions pour l’hébergement.</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                        mso-fareast-font-family:&quot;Times New Roman&quot;">&nbsp;</span><span lang="EN-US" style="font-size:12.0pt;
                        line-height:115%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000"> Q : Qui peut être exposant ?</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <font color="#000000">R:
                        Uniquement les personnes / organisations avec des ressources qui permettront /
                        amélioreront la formation des pasteurs</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><b><span lang="EN-US" style="font-size:12.0pt;line-height:115%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                        mso-fareast-font-family:&quot;Times New Roman&quot;"><font color="#000000">&nbsp;</font></span><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">Option 1 – Comprend une
                        chambre simple au Dreams Hotel.</font></span></b></p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        0cm;margin-left:0cm;margin-bottom:.0001pt;line-height:115%"><b><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span></b><span lang="EN-US" style="letter-spacing: inherit; background-color: transparent; font-size: 12pt; line-height: 115%;"><font color="#000000">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;1.</font></span><span lang="EN-US" style="letter-spacing: inherit; background-color: transparent; font-size: 7pt; line-height: 115%;"><font color="#000000">&nbsp;&nbsp;</font>&nbsp;&nbsp;&nbsp;
                        </span><span lang="EN-US" style="letter-spacing: inherit; background-color: transparent; font-size: 12pt; line-height: 115%;"><font color="#000000">Coût
                        pour assister au Congrès –</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></font></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; a.
                        Le coût pour chaque exposant est de 1 500 USD (comprenant les frais
                        d’inscription et l’occupation d’une chambre individuelle pour 5 nuits).</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000">b.
                        Si votre conjoint vous accompagne, le coût sera de 1 850,00 $ (comprenant les
                        frais d’inscription pour deux personnes et l’occupation d’une chambre
                        individuelle pour 5 nuits).</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#000000">c.
                        Si vous amenez un coéquipier qui a besoin d’une chambre supplémentaire, le coût
                        sera de 2 500,00 $ (comprenant les frais d’inscription pour deux personnes et
                        deux chambres simples pour 5 nuits).</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">2.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RREACH n'est pas responsable de la perte
                        ou de l'endommagement du stand de l'exposant, de son matériel d'exposition ou
                        de ses produits</font>.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">3.&nbsp;&nbsp;&nbsp;
                        RREACH ne sera pas responsable du transfert du stand de l'exposant, de
                        son matériel d'exposition ou de ses produits vers ou depuis le lieu du congrès
                        chaque jour.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"> </font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">4. Aucun transfert aéroport ne sera
                        fourni (en raison des gros articles qu’ils peuvent transporter). Cependant, les
                        exposants peuvent utiliser le transport fourni chaque jour de l’hôtel Dreams à
                        l’hôtel Westin Playa Bonita, où se tient le congrès.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">5. Aucun temps d’antenne ne sera
                        accordé aux exposants pour promouvoir depuis la scène.</font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">6. Les exposants peuvent assister aux
                        séances de culte et plénières du Congrès, mais pas aux séances en petits
                        groupes.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">7. Le petit-déjeuner sera servi
                        chaque matin au Dreams Hotel.&nbsp;</font></span><span style="background-color: transparent; color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit;">Le
                        déjeuner et le dîner seront servis chaque jour à l’hôtel Westin Playa Bonita.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span style="background-color: transparent; color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit;"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">8. Une table et deux chaises seront
                        fournies à chaque exposant pour chaque jour du Congrès.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span></p><p style="margin-top: 0cm; margin-right: 0cm; margin-bottom: 0.0001pt; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span><span style="letter-spacing: inherit; background-color: transparent;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><b>Option 2 –
                        Les exposants fournissent leur propre hébergement.</b></font></span></span></p><p style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><b><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></b></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 47px; line-height: 115%;"><font color="#000000"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">1.</font></span><span lang="EN-US" style="font-size:7.0pt;line-height:115%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span></font><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">Coût
                        pour assister au Congrès –</font></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 47px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Frais d’inscription – 750 USD pour
                        une personne; 300 USD pour chaque personne supplémentaire.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:54.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;2. RREACH n'est pas responsable de la
                        perte ou de l'endommagement du stand de l'exposant, de son matériel
                        d'exposition ou de ses produits.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;3. RREACH ne sera pas responsable du
                        transfert quotidien du stand, du matériel d’exposition ou des ressources de
                        l’exposant vers ou depuis le Congrès.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;4. Aucun transfert aéroport ne sera
                        fourni (en raison des gros articles qu’ils peuvent transporter).</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;5. Aucun temps d’antenne ne sera
                        accordé aux exposants pour promouvoir depuis la scène.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font><o:p></o:p></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;6. Les exposants peuvent assister aux
                        séances de culte et plénières du Congrès, mais pas aux séances en petits
                        groupes.</font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><br></font></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000"><o:p></o:p></font></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;7. Le déjeuner et le dîner seront
                        servis chaque jour à l’hôtel Westin Playa Bonita.&nbsp;</font></span><span style="color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit; background-color: transparent;">(Le
                        petit-déjeuner n’est pas inclus avec cette option.)</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                        margin-left:36.0pt;margin-bottom:.0001pt;line-height:115%"><span style="color: rgb(0, 0, 0); font-size: 12pt; letter-spacing: inherit; background-color: transparent;"><br></span></p><p class="MsoNormal" style="margin: 0cm 0cm 0.0001pt 23px; line-height: 115%;"><span lang="EN-US" style="font-size:12.0pt;line-height:115%"><font color="#000000">&nbsp; &nbsp; &nbsp;8. Une table et deux chaises seront
                        fournies à chaque exposant pour chaque jour du Congrès.</font><o:p></o:p></span></p><p>


                        </p><p class="MsoNormal" style="margin-top:12.0pt;margin-right:0cm;margin-bottom:
                        12.0pt;margin-left:0cm;line-height:106%"><span lang="EN-US" style="font-size:
                        12.0pt;line-height:106%;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                        &quot;Times New Roman&quot;"><o:p>&nbsp;</o:p></span></p>

            </div>
                
        </div>
    </div>

@else

    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">GProCongress II – Exhibitor Policy</h4>
                <h5  style="text-align:center;padding-top: 50px;"></h5>
                <p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black">RREACH has
                    created the following Exhibitor Policy to explain who can be invited to be an
                    exhibitor at the Congress, the cost to be paid by exhibitors, and what is
                    provided in return for such payment.<o:p></o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p>&nbsp;</o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black">Two options are provided
                    for exhibitors – one includes a hotel room, and the other allows the exhibitor
                    to make their own arrangements for accommodations.<o:p></o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p>&nbsp;</o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">Q: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Who can be an exhibitor?</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:36.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">A: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Only people/organizations with products that will
                    enable/enhance the training of pastors</span><span lang="EN-US" style="font-size:
                    12.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;">
                    <o:p></o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US">&nbsp;</span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><b>Option 1 – Includes single room at Dreams
                    Hotel.</b></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><b><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></b><span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l1 level1 lfo2;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><!--[if !supportLists]--><span lang="EN-US" style="font-size:
                    12.0pt;color:black">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;
                    color:black">Cost to attend the Congress –</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l1 level1 lfo2;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;
                    color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l1 level1 lfo2;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;
                    color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">a.&nbsp; &nbsp;The cost for each exhibitor is $1,500 USD (includes
                    registration fee and single room occupancy for 5 nights).<o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal;
                    border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black">b.&nbsp; &nbsp;If your spouse accompanies you, the cost will be $1,850.00
                    (includes registration fee for two people, and single room occupancy for 5
                    nights).<o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal;
                    border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black">c.&nbsp; &nbsp;If you bring a teammate who needs an additional room, the
                    cost will be $2,500.00 (includes registration fee for two people, and two
                    single rooms for 5 nights).</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal;
                    border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:90.0pt;margin-bottom:.0001pt;text-indent:-36.0pt;line-height:normal;
                    border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">2.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">RREACH will not be responsible
                    for any loss or damage to the exhibitor’s booth, display materials, or
                    products.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;
                    mso-fareast-font-family:&quot;Times New Roman&quot;;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">3.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">RREACH will not be responsible
                    for the transfer of the exhibitor’s booth, display materials, or products to or
                    from the Congress daily.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"> <o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">4.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">No airport transfers will be
                    provided (due to large items they may be carrying). However, exhibitors can use
                    the transportation provided each day from the Dreams Hotel to the Westin Playa
                    Bonita Hotel, where the Congress is held.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span><span lang="EN-US" style="font-size:12.0pt"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">5.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">No air time will be given to
                    any exhibitors to promote from the stage.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span><span lang="EN-US" style="font-size:12.0pt"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">6.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">&nbsp;Exhibitors may attend worship and plenary
                    sessions at the Congress, but not breakout room sessions.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">7.&nbsp;&nbsp; Breakfast will be provided each morning at
                    Dreams Hotel.&nbsp;&nbsp;</span><span style="color: black; font-size: 12pt; letter-spacing: inherit; background-color: transparent;">Lunch and dinner will be provided each day
                    at the Westin Playa Bonita Hotel.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span style="color: black; font-size: 12pt; letter-spacing: inherit; background-color: transparent;"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">8.&nbsp;&nbsp; A table and two chairs will be provided to
                    each exhibitor for each day of the Congress.<o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p>&nbsp;</o:p></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><b>Option 2 – Exhibitors provide their own
                    accommodations.</b></span></p><p class="MsoNormal" style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:
                    normal"><b><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></b><span lang="EN-US" style="font-size:12.0pt;font-family:
                    &quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:&quot;Times New Roman&quot;"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><!--[if !supportLists]--><span lang="EN-US" style="font-size:
                    12.0pt;color:black">1.<span style="font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 7pt; line-height: normal; font-family: &quot;Times New Roman&quot;;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </span></span><!--[endif]--><span lang="EN-US" style="font-size:12.0pt;
                    color:black">Cost to attend the Congress –</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;
                    color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal;
                    mso-list:l0 level1 lfo1;border:none;mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;
                    mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;
                    color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;line-height:normal;border:none;
                    mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Registration fee – $750 USD for
                    one person; $300 USD for each additional person.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;line-height:normal;border:none;
                    mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;line-height:normal;border:none;
                    mso-padding-alt:31.0pt 31.0pt 31.0pt 31.0pt;mso-border-shadow:yes"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">2.&nbsp; &nbsp;RREACH will not
                    be responsible for any loss or damage to the exhibitor’s booth, display
                    materials, or products.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span><span lang="EN-US" style="font-size:12.0pt"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">3.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp;</span><span lang="EN-US" style="font-size:12.0pt;color:black">RREACH will not be responsible for the
                    transfer of the exhibitor’s booth, display materials, or products to or from
                    the Congress daily.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"> <o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">4.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp;</span><span lang="EN-US" style="font-size:12.0pt;color:black">No airport transfers will be provided (due
                    to large items they may be carrying).</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"> </span><span lang="EN-US" style="font-size:
                    12.0pt"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">5.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp;&nbsp;</span><span lang="EN-US" style="font-size:12.0pt;color:black">No air time will be given to any
                    exhibitors to promote from the stage.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span><span lang="EN-US" style="font-size:
                    12.0pt"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">6.</span><span lang="EN-US" style="font-size:7.0pt;font-family:&quot;Times New Roman&quot;,&quot;serif&quot;;mso-fareast-font-family:
                    &quot;Times New Roman&quot;;color:black">&nbsp;&nbsp;&nbsp; </span><span lang="EN-US" style="font-size:12.0pt;color:black">Exhibitors may attend worship and plenary
                    sessions at the Congress, but not breakout room sessions.</span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><br></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black"><o:p></o:p></span></p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
                    margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">7.&nbsp;&nbsp; Lunch and dinner will be provided each day
                    at the Westin Playa Bonita Hotel.&nbsp;</span><span style="color: black; font-size: 12pt; letter-spacing: inherit; background-color: transparent;">(Breakfast is not included with this
                    option.)</span></p><p>

</p><p class="MsoNormal" style="margin-top:0cm;margin-right:0cm;margin-bottom:0cm;
margin-left:54.0pt;margin-bottom:.0001pt;text-indent:-18.0pt;line-height:normal"><span lang="EN-US" style="font-size:12.0pt;color:black">8.&nbsp;&nbsp; A table and two chairs will be provided to
each exhibitor for each day of the Congress.<o:p></o:p></span></p>

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