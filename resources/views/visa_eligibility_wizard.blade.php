@extends('layouts/app')

@section('title',__(Lang::get('web/app.donate')))

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
    form label {
        font-size: 25px !important;
    }
    footer {
        padding-top: 93px !important;
    }
    .wz-title {
       
        margin-top: 0em !important;
    }
    .wz_form-full {
        
        width: 100% !important;
    }
    #valid_visa_or_residence_yes_class ,#valid_visa_or_residence_class_remove{
        overflow-y: scroll;
        height: 263px;
    }
</style>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/wizard/css/main.css')}}" />
	
@endpush

<div class="inner-banner-wrapper">
    <div class="container">
        <div class="">
            <div class="section-wz ">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-md-12 ml-auto mr-auto">
                            <h2 id="ques" class="wz-title fadeInUp animated a-duration-5 a-delay-05 pr-settings-title">Gpro Visa
                                Eligibility Wizard</h2>

                            <br>
                            <div class="">
                                <div class="row">
                                    <div class="col-md-8 fadeInUp animated a-duration-2 a-delay-10">
                                        <div class="wz_form-wrap" id="wz_form-wrap">
                                            <form action="#" method="get" target="_blank" id="myform"
                                                class="wz_form wz_form-full" autocomplete="off">
                                                <ol class="wz_fields">

                                                    <!-- Question 1 -->
                                                    <li>
                                                        <label class="wz_field-label wz_anim-upper pr-settings-title"
                                                            for="q1">@lang('web/wizard.your_name')</label>

                                                        <span class="input input_wzqa">
                                                            <input class="wz_anim-lower wz_input_field wz_input_field_wzqa"
                                                                id="q1" name="q1" type="text" placeholder="David Smith..."
                                                                required />
                                                            <svg class="graphic graphic_wzqa" width="300%" height="100%"
                                                                viewBox="0 0 1200 60" preserveAspectRatio="none">
                                                                <path
                                                                    d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0" />
                                                            </svg>
                                                        </span>

                                                    </li>

                                                    <!-- / Question 1 -->

                                                    <!-- Question 3 -->
                                                    <li>
                                                        <label class="wz_field-label wz_anim-upper pr-settings-title"
                                                            for="q3">@lang('web/wizard.email_with_which_you_registered')</label>
                                                        <span class="input input_wzqa">
                                                            <input class="wz_anim-lower wz_input_field wz_input_field_wzqa"
                                                                id="q3" name="q3" type="email" placeholder="user@mail.com"
                                                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required />
                                                                 <p id="email-message" style="color:red"></p>
                                                            <svg class="graphic graphic_wzqa" width="300%" height="100%"
                                                                viewBox="0 0 1200 60" preserveAspectRatio="none">
                                                                <path
                                                                    d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0" />
                                                            </svg>
                                                        </span>
                                                    </li>
                                                    <!-- / Question 3 -->

                                                    <!-- Question 4 -->
                                                    <li data-input-trigger>
                                                        <label id="para1" class="wz_field-label wz_anim-upper pr-settings-title" >@lang('web/wizard.which_country_passport_will_you_use_to_come_to_panama')</label>
                                                        <div class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                            <select id="countryData" name="country" class="form-control" required>
                                                                <option value="0" selected>@lang('web/ministry-details.select') @lang('web/contact-details.country')</option>
                                                                
                                                                <option value="Afghanistan">@lang('web/wizard.Afghanistan')</option>
                                                                <option value="Albania">@lang('web/wizard.Albania')</option>
                                                                <option value="Germany">@lang('web/wizard.Germany')</option>
                                                                <option value="Andorra">@lang('web/wizard.Andorra')</option>
                                                                <option value="Angola">@lang('web/wizard.Angola')</option>
                                                                <option value="Antigua and Barbuda">@lang('web/wizard.Antigua_and_Barbuda')</option>
                                                                <option value="Saudi Arabia">@lang('web/wizard.Saudi_Arabia')</option>
                                                                <option value="Algeria">@lang('web/wizard.Algeria')</option>
                                                                <option value="Argentina">@lang('web/wizard.Argentina')</option>
                                                                <option value="Armenia">@lang('web/wizard.Armenia')</option>
                                                                <option value="Australia">@lang('web/wizard.Australia')</option>
                                                                <option value="Austria">@lang('web/wizard.Austria')</option>
                                                                <option value="Azerbaijan">@lang('web/wizard.Azerbaijan')</option>
                                                                <option value="Bahamas">@lang('web/wizard.Bahamas')</option>
                                                                <option value="Bahrain">@lang('web/wizard.Bahrain')</option>
                                                                <option value="Bangladesh">@lang('web/wizard.Bangladesh')</option>
                                                                <option value="Barbados">@lang('web/wizard.Barbados')</option>
                                                                <option value="Belgium">@lang('web/wizard.Belgium')</option>
                                                                <option value="Belize">@lang('web/wizard.Belize')</option>
                                                                <option value="Benin">@lang('web/wizard.Benin')</option>
                                                                <option value="Belarus">@lang('web/wizard.Belarus')</option>
                                                                <option value="BNO (Ciudadanos de Ultramar)">@lang('web/wizard.BNO_Ciudadanos_de_Ultramar')</option>
                                                                <option value="Bolivia">@lang('web/wizard.Bolivia')</option>
                                                                <option value="Bosnia and Herzegovina">@lang('web/wizard.Bosnia_and_Herzegovina')</option>
                                                                <option value="Botswana">@lang('web/wizard.Botswana')</option>
                                                                <option value="Brazil">@lang('web/wizard.Brazil')</option>
                                                                <option value="Brunei Darussalam">@lang('web/wizard.Brunei_Darussalam')</option>
                                                                <option value="Bulgaria">@lang('web/wizard.Bulgaria')</option>
                                                                <option value="Burkina Faso">@lang('web/wizard.Burkina_Faso')</option>
                                                                <option value="Burundi">@lang('web/wizard.Burundi')</option>
                                                                <option value="Bhutan">@lang('web/wizard.Bhutan')</option>
                                                                <option value="Cape Verde">@lang('web/wizard.Cape_Verde')</option>
                                                                <option value="Cambodia">@lang('web/wizard.Cambodia')</option>
                                                                <option value="Cameroon">@lang('web/wizard.Cameroon')</option>
                                                                <option value="Canada">@lang('web/wizard.Canada')</option>
                                                                <option value="Chad">@lang('web/wizard.Chad')</option>
                                                                <option value="Chile">@lang('web/wizard.Chile')</option>
                                                                <option value="China">@lang('web/wizard.China')</option>
                                                                <option value="Cyprus">@lang('web/wizard.Cyprus')</option>
                                                                <option value="Vatican City State (Holy See)">@lang('web/wizard.Vatican_City_State_Holy_See')</option>
                                                                <option value="Colombia">@lang('web/wizard.Colombia')</option>
                                                                <option value="North Korea">@lang('web/wizard.North_Korea')</option>
                                                                <option value="Ivory Coast">@lang('web/wizard.Ivory_Coast')</option>
                                                                <option value="Costa Rica">@lang('web/wizard.Costa_Rica')</option>
                                                                <option value="Croatia">@lang('web/wizard.Croatia')</option>
                                                                <option value="Cuba">@lang('web/wizard.Cuba')</option>
                                                                <option value="Denmark">@lang('web/wizard.Denmark')</option>
                                                                <option value="Dominica">@lang('web/wizard.Dominica')</option>
                                                                <option value="Dominican Republic">@lang('web/wizard.Dominican_Republic')</option>
                                                                <option value="Ecuador">@lang('web/wizard.Ecuador')</option>
                                                                <option value="Egypt">@lang('web/wizard.Egypt')</option>
                                                                <option value="El Salvador">@lang('web/wizard.El_Salvador')</option>
                                                                <option value="United Arab Emirates">@lang('web/wizard.United_Arab_Emirates')</option>
                                                                <option value="Eritrea">@lang('web/wizard.Eritrea')</option>
                                                                <option value="Slovakia (Slovak Republic)">@lang('web/wizard.Slovakia_Slovak_Republic')</option>
                                                                <option value="Slovenia">@lang('web/wizard.Slovenia')</option>
                                                                <option value="Spain">@lang('web/wizard.Spain')</option>
                                                                <option value="United States">@lang('web/wizard.United_States')</option>
                                                                <option value="Estonia">@lang('web/wizard.Estonia')</option>
                                                                <option value="Ethiopia">@lang('web/wizard.Ethiopia')</option>
                                                                <option value="Russia">@lang('web/wizard.Russia')</option>
                                                                <option value="Fiji">@lang('web/wizard.Fiji')</option>
                                                                <option value="Philippines">@lang('web/wizard.Philippines')</option>
                                                                <option value="Finland">@lang('web/wizard.Finland')</option>
                                                                <option value="France">@lang('web/wizard.France')</option>
                                                                <option value="Gabon">@lang('web/wizard.Gabon')</option>
                                                                <option value="Gambia">@lang('web/wizard.Gambia')</option>
                                                                <option value="Georgia">@lang('web/wizard.Georgia')</option>
                                                                <option value="Ghana">@lang('web/wizard.Ghana')</option>
                                                                <option value="Grenada">@lang('web/wizard.Grenada')</option>
                                                                <option value="Guatemala">@lang('web/wizard.Guatemala')</option>
                                                                <option value="Guinea Bissau">@lang('web/wizard.Guinea-Bissau')</option>
                                                                <option value="Equatorial Guinea">@lang('web/wizard.Equatorial_Guinea')</option>
                                                                <option value="Guyana">@lang('web/wizard.Guyana')</option>
                                                                <option value="Haiti">@lang('web/wizard.Haiti')</option>
                                                                <option value="Honduras">@lang('web/wizard.Honduras')</option>
                                                                <option value="Hong Kong">@lang('web/wizard.Hong_Kong')</option>
                                                                <option value="Hungary">@lang('web/wizard.Hungary')</option>
                                                                <option value="India">@lang('web/wizard.India')</option>
                                                                <option value="Indonesia">@lang('web/wizard.Indonesia')</option>
                                                                <option value="United Kingdom">@lang('web/wizard.United_Kingdom')</option>
                                                                <option value="Iran (Islamic Republic of)">@lang('web/wizard.Iran_Islamic_Republic_of')</option>
                                                                <option value="Iraq">@lang('web/wizard.Iraq')</option>
                                                                <option value="Ireland">@lang('web/wizard.Ireland')</option>
                                                                <option value="Iceland">@lang('web/wizard.Iceland')</option>
                                                                <option value="Comoras Island">@lang('web/wizard.Comoras_Island')</option>
                                                                <option value="Marshall Islands">@lang('web/wizard.Marshall_Islands')</option>
                                                                <option value="Solomon Islands">@lang('web/wizard.Solomon_Islands')</option>
                                                                <option value="Solomon Islands">@lang('web/wizard.Solomon_Islands')</option>
                                                                <option value="Israel">@lang('web/wizard.Israel')</option>
                                                                <option value="Israel">@lang('web/wizard.Israel')</option>
                                                                <option value="Italy">@lang('web/wizard.Italy')</option>
                                                                <option value="Jamaica">@lang('web/wizard.Jamaica')</option>
                                                                <option value="Japan">@lang('web/wizard.Japan')</option>
                                                                <option value="Jordan">@lang('web/wizard.Jordan')</option>
                                                                <option value="Kazakhstan">@lang('web/wizard.Kazakhstan')</option>
                                                                <option value="Kenya">@lang('web/wizard.Kenya')</option>
                                                                <option value="Kyrgyzstan">@lang('web/wizard.Kyrgyzstan')</option>
                                                                <option value="Kiribati">@lang('web/wizard.Kiribati')</option>
                                                                <option value="Kosovo">@lang('web/wizard.Kosovo')</option>
                                                                <option value="Kuwait">@lang('web/wizard.Kuwait')</option>
                                                                <option value="Lao">@lang('web/wizard.Lao')</option>
                                                                <option value="Lesotho">@lang('web/wizard.Lesotho')</option>
                                                                <option value="Latvia">@lang('web/wizard.Latvia')</option>
                                                                <option value="Lebanon">@lang('web/wizard.Lebanon')</option>
                                                                <option value="Liberia">@lang('web/wizard.Liberia')</option>
                                                                <option value="Libya">@lang('web/wizard.Libya')</option>
                                                                <option value="Liechtenstein">@lang('web/wizard.Liechtenstein')</option>
                                                                <option value="Lithuania">@lang('web/wizard.Lithuania')</option>
                                                                <option value="Luxembourg">@lang('web/wizard.Luxembourg')</option>
                                                                <option value="North Macedonia">@lang('web/wizard.North_Macedonia')</option>
                                                                <option value="Madagascar">@lang('web/wizard.Madagascar')</option>
                                                                <option value="Malaysia">@lang('web/wizard.Malaysia')</option>
                                                                <option value="Malawi">@lang('web/wizard.Malawi')</option>
                                                                <option value="Maldives">@lang('web/wizard.Maldives')</option>
                                                                <option value="Mali">@lang('web/wizard.Mali')</option>
                                                                <option value="Malta">@lang('web/wizard.Malta')</option>
                                                                <option value="Morocco">@lang('web/wizard.Morocco')</option>
                                                                <option value="Mauritius">@lang('web/wizard.Mauritius')</option>
                                                                <option value="Mauritania">@lang('web/wizard.Mauritania')</option>
                                                                <option value="Mexico">@lang('web/wizard.Mexico')</option>
                                                                <option value="Micronesia, Federated States of">@lang('web/wizard.Micronesia_Federated_States_of')</option>
                                                                <option value="Moldova, Republic of">@lang('web/wizard.Moldova_Republic_of')</option>
                                                                <option value="Monaco">@lang('web/wizard.Monaco')</option>
                                                                <option value="Mongolia">@lang('web/wizard.Mongolia')</option>
                                                                <option value="Montenegro">@lang('web/wizard.Montenegro')</option>
                                                                <option value="Mozambique">@lang('web/wizard.Mozambique')</option>
                                                                <option value="Myanmar, Burma">@lang('web/wizard.Myanmar_Burma')</option>
                                                                <option value="Namibia">@lang('web/wizard.Namibia')</option>
                                                                <option value="Nauru">@lang('web/wizard.Nauru')</option>
                                                                <option value="Nepal">@lang('web/wizard.Nepal')</option>
                                                                <option value="Nicaragua">@lang('web/wizard.Nicaragua')</option>
                                                                <option value="Niger">@lang('web/wizard.Niger')</option>
                                                                <option value="Nigeria">@lang('web/wizard.Nigeria')</option>
                                                                <option value="Norway">@lang('web/wizard.Norway')</option>
                                                                <option value="New Zealand">@lang('web/wizard.New_Zealand')</option>
                                                                <option value="Oman">@lang('web/wizard.Oman')</option>
                                                                <option value="Netherlands">@lang('web/wizard.Netherlands')</option>
                                                                <option value="Pakistan">@lang('web/wizard.Pakistan')</option>
                                                                <option value="Palau">@lang('web/wizard.Palau')</option>
                                                                <option value="Palestine">@lang('web/wizard.Palestine')</option>
                                                                <option value="Papua New Guinea">@lang('web/wizard.Papua_New_Guinea')</option>
                                                                <option value="Paraguay">@lang('web/wizard.Paraguay')</option>
                                                                <option value="Peru">@lang('web/wizard.Peru')</option>
                                                                <option value="Poland">@lang('web/wizard.Poland')</option>
                                                                <option value="Portugal">@lang('web/wizard.Portugal')</option>
                                                                <option value="Qatar">@lang('web/wizard.Qatar')</option>
                                                                <option value="Czech Republic">@lang('web/wizard.Czech_Republic')</option>
                                                                <option value="Hellenic Republic">@lang('web/wizard.Hellenic_Republic')</option>
                                                                <option value="Saharawi Rep">@lang('web/wizard.Saharawi_Rep')</option>
                                                                <option value="Central African Republic">@lang('web/wizard.Central_African_Republic')</option>
                                                                <option value="Congo, Republic of (Brazzaville)">@lang('web/wizard.Congo_Republic_of_Brazzaville')</option>
                                                                <option value="Democratic Republic of the Congo (Kinshasa)">@lang('web/wizard.Democratic_Republic_of_the_Congo_Kinshasa')</option>
                                                                <option value="Rwanda">@lang('web/wizard.Rwanda')</option>
                                                                <option value="Samoa">@lang('web/wizard.Samoa')</option>
                                                                <option value="San Marino">@lang('web/wizard.San_Marino')</option>
                                                                <option value="Saint Vincent and the Grenadines">@lang('web/wizard.Saint_Vincent_and_the_Grenadines')</option>
                                                                <option value="St. Thomas and Prince">@lang('web/wizard.St_Thomas_and_Prince')</option>
                                                                <option value="Senegal">@lang('web/wizard.Senegal')</option>
                                                                <option value="Serbia">@lang('web/wizard.Serbia')</option>
                                                                <option value="Seychelles">@lang('web/wizard.Seychelles')</option>
                                                                <option value="Sierra Leone">@lang('web/wizard.Sierra_Leone')</option>
                                                                <option value="Singapore">@lang('web/wizard.Singapore')</option>
                                                                <option value="Syria, Syrian Arab Republic">@lang('web/wizard.Syria_Syrian_Arab_Republic')</option>
                                                                <option value="Somalia">@lang('web/wizard.Somalia')</option>
                                                                <option value="Sri Lanka">@lang('web/wizard.Sri_Lanka')</option>
                                                                <option value="Saint Lucia">@lang('web/wizard.Saint_Lucia')</option>
                                                                <option value="Swaziland">@lang('web/wizard.Swaziland')</option>
                                                                <option value="South Africa">@lang('web/wizard.South_Africa')</option>
                                                                <option value="Sudan">@lang('web/wizard.Sudan')</option>
                                                                <option value="Sweden">@lang('web/wizard.Sweden')</option>
                                                                <option value="Switzerland">@lang('web/wizard.Switzerland')</option>
                                                                <option value="Suriname">@lang('web/wizard.Suriname')</option>
                                                                <option value="Tajikistan">@lang('web/wizard.Tajikistan')</option>
                                                                <option value="Thailand">@lang('web/wizard.Thailand')</option>
                                                                <option value="Taiwan">@lang('web/wizard.Taiwan')</option>
                                                                <option value="Tanzania">@lang('web/wizard.Tanzania')</option>
                                                                <option value="Togo">@lang('web/wizard.Togo')</option>
                                                                <option value="Tonga">@lang('web/wizard.Tonga')</option>
                                                                <option value="Trinidad and Tobago">@lang('web/wizard.Trinidad_and_Tobago')</option>
                                                                <option value="Tunisia">@lang('web/wizard.Tunisia')</option>
                                                                <option value="Turkmenistan">@lang('web/wizard.Turkmenistan')</option>
                                                                <option value="Turkey">@lang('web/wizard.Turkey')</option>
                                                                <option value="Tuvalu">@lang('web/wizard.Tuvalu')</option>
                                                                <option value="Ukraine">@lang('web/wizard.Ukraine')</option>
                                                                <option value="Uganda">@lang('web/wizard.Uganda')</option>
                                                                <option value="Uruguay">@lang('web/wizard.Uruguay')</option>
                                                                <option value="Uzbekistan">@lang('web/wizard.Uzbekistan')</option>
                                                                <option value="Vanuatu">@lang('web/wizard.Vanuatu')</option>
                                                                <option value="Venezuela">@lang('web/wizard.Venezuela')</option>
                                                                <option value="Vietnam">@lang('web/wizard.Vietnam')</option>
                                                                <option value="Yemen">@lang('web/wizard.Yemen')</option>
                                                                <option value="Yugoslavia">@lang('web/wizard.Yugoslavia')</option>
                                                                <option value="Zambia">@lang('web/wizard.Zambia')</option>
                                                                <option value="Zimbabwe">@lang('web/wizard.Zimbabwe')</option>
                                                            </select>

                                                            <div style="display: none;" id="constdata">
                                                                <p style="text-align: center; color: white; font-size: 22px;">
                                                                @lang('web/wizard.you_do_not_require_a_visa_to_enter_panama_90days')

                                                                </p>
                                                                <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                        Reset...</a></p>
                                                            </div>

                                                            <!-- <div id="elsecon"> -->

                                                            <p data-input-trigger id="elsecon"></p>
                                                        </div>
                                                    </li>
                                                    <!-- / Question 4 -->


                                                    <!-- Question 5 -->
                                                    <li style="font-size: 42px;">
                                                        <label id="para3" class="wz_field-label wz_anim-upper pr-settings-title"
                                                            data-info="">@lang('web/wizard.is_this_a_diplomatic_passport')</label>
                                                        <div style="display: none;" id="diplomatic_passport_yesdata">
                                                            <p style="text-align: center; color: white; font-size: 28px;">
                                                            @lang('web/wizard.you_do_not_require_a_visa_to_enter_panama_90days')
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>
                                                        <div style="display: none;" id="diplomatic_passport_nodata">
                                                            <!-- <p style="margin-top: 30px;">Next</p> -->
                                                        </div>

                                                        <div id="yesno"
                                                            class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                            <label><input type="radio" name="diplomatic_passport" required value="yes">@lang('web/wizard.yes')</label>
                                                            <label><input type="radio" name="diplomatic_passport" required value="no">@lang('web/wizard.no')</label>
                                                        </div>
                                                    </li>
                                                    <!-- Question 5 -->


                                                    <!-- Question 5 -->
                                                    <li data-input-trigger class="" style="font-size: 27px;" id="valid_visa_or_residence_class_remove">
                                                        <label id="valid_visa_or_residence" class="wz_field-label wz_anim-upper pr-settings-title"
                                                            data-info="">
                                                            @lang('web/wizard.do_you_have_a_valid_visa_or_residence')
                                                        </label>
                                                        <div style="display: none;" id="countryList3Yes">
                                                            <p style="font-size: 20px; text-align: center;">
                                                            @lang('web/wizard.you_must_obtain_an_authorized_visa_to_enter_panama')
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn"> Reset...</a></p>
                                                        </div>
                                                        <div style="display: none;" id="yesdata1">
                                                            <p style="font-size: 23px; text-align: center;">
                                                                @lang('web/wizard.you_do_not_require_a_visa_to_enter_panama_90days')
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>
                                                        <div style="display: none;" id="nodata1">
                                                            <p style="text-align: center; color: white; font-size: 20px;">
                                                                @lang('web/wizard.you_must_obtain_an_authorized_visa_to_enter_panama')
                                                               
                                                                
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>

                                                        <div style="display: none;" id="nodata2">
                                                            <p style="text-align: center; color: white; font-size: 27px;">
                                                                @lang('web/wizard.you_do_not_require_a_visa_to_enter_panama_90days')

                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>


                                                        </div>
                                                        <div id="yesno1"
                                                            class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                            <label><input type="radio" name="valid_visa_or_residence" required value="yes">@lang('web/wizard.yes')</label>

                                                            <label><input type="radio" name="valid_visa_or_residence" required value="no">@lang('web/wizard.no')</label>

                                                        </div>

                                                    </li>

                                                    
                                                    <!-- Question 6 -->
                                                    <li data-input-trigger class="" style="font-size: 27px;" id="valid_visa_or_residence_yes_class">
                                                        <div style="display: none;" id="valid_visa_or_residence_no">
                                                            <p style="text-align: center; color: white; font-size: 20px;">
                                                                @lang('web/wizard.you_must_obtain_an_authorized_visa_to_enter_panama')
                                                               
                                                                
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>
                                                        <div id="valid_visa_or_residence_yes">
                                                            <label id="" class="wz_field-label wz_anim-upper pr-settings-title"
                                                                data-info="">
                                                                @lang('web/wizard.do_you_have_a_valid_visa_or_residence_yes')
                                                            </label>
                                                            <div id=""
                                                                class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                                <label><input type="radio" name="valid_visa_or_residence_yes" required value="yes">@lang('web/wizard.yes')</label>
                                                                <label><input type="radio" name="valid_visa_or_residence_yes" required value="no">@lang('web/wizard.no')</label>

                                                            </div>
                                                        </div>
                                                    </li>

                                                    
                                                    <!-- Question 7 -->
                                                    <li data-input-trigger class="" style="font-size: 27px;" >
                                                        
                                                        <div style="display: none;" class="multiple_entry_visa_no">
                                                            <p style="text-align: center; color: white; font-size: 20px;">
                                                                @lang('web/wizard.you_must_obtain_an_authorized_visa_to_enter_panama')
                                                               
                                                                
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>

                                                        <div id="multiple_entry_visa_yes">
                                                            <label id="" class="wz_field-label wz_anim-upper pr-settings-title"
                                                                data-info="">
                                                                @lang('web/wizard.step_7_question')
                                                            </label>
                                                            <div id=""
                                                                class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                                <label><input type="radio" name="multiple_entry_visa" required value="yes">@lang('web/wizard.yes')</label>
                                                                <label><input type="radio" name="multiple_entry_visa" required value="no">@lang('web/wizard.no')</label>

                                                            </div>
                                                        </div>
                                                    </li>

                                                    
                                                    <!-- Question 7 -->
                                                    <li data-input-trigger class="" style="font-size: 27px;" id="step_8_message_li">
                                                        
                                                        <div style="display: none;" class="multiple_entry_visa_no">
                                                            <p style="text-align: center; color: white; font-size: 20px;">
                                                                @lang('web/wizard.you_must_obtain_an_authorized_visa_to_enter_panama')
                                                               
                                                                
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>
                                                        <div style="display: none;" id="step_8_message">
                                                            <p style="text-align: center; color: white; font-size: 20px;">
                                                                @lang('web/wizard.step_8_message')
                                                               
                                                                
                                                            </p>
                                                            <p><a href="{{URL('visa-eligibility-wizard')}}" class="main-btn">
                                                                    Reset...</a></p>
                                                        </div>
                                                        <div id="passport_valid_until_May_31" style="display: none;" >
                                                            <label id="" class="wz_field-label wz_anim-upper pr-settings-title"
                                                                data-info="">
                                                                @lang('web/wizard.step_8_question')
                                                            </label>
                                                            <div id=""
                                                                class="wz_radio-group wz_radio-custom clearfix wz_anim-lower">
                                                                <label><input type="radio" name="passport_valid_until_May_31" required value="yes">@lang('web/wizard.yes')</label>
                                                                <label><input type="radio" name="passport_valid_until_May_31" required value="no">@lang('web/wizard.no')</label>

                                                            </div>
                                                        </div>
                                                    </li>

                                                    

                                                </ol><!-- /wz_fields -->
                                                <button class="wz_submit" type="submit" value="Submit">Send answers</button>
                                            </form><!-- /wz_form -->
                                        </div><!-- /wz_form-wrap -->

                                    </div>
                                    <div id="myBox" class="col-md-4 my-md-5 p-4 fadeInUp animated a-duration-2 a-delay-12">
                                        <div class="wz_progress" style="position: relative; top: -10%;"></div>
                                        <!-- <h3 class="wz-numbers"></h3> -->
                                        <div class="wz-controls"></div>
                                        <p class="wz-description">


                                        </p>
                                        <p class="wz-shake-cont">
                                            <button class="wz-button"><i class="fa fa-long-arrow-right wz-shake-eff"
                                                    aria-hidden="true"></i></button>

                                                  
                                        </p>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
            
    </div>
</div>

@endsection



@push('custom_js')


	<script>
		// question 5
		// Get the radio button group
		var iceCreamGroup = document.getElementsByName("ice_cream1");

		// Add a change event listener to the radio button group
		for (var i = 0; i < iceCreamGroup.length; i++) {
			iceCreamGroup[i].addEventListener("change", function () {
				// Get the selected radio button
				var selectedButton = document.querySelector('input[name="ice_cream1"]:checked');

				// Check if a radio button is selected
				if (selectedButton) {
					// Get the selected value
					var selectedValue = selectedButton.value;

					// Check the selected value and execute code accordingly
					if (selectedValue === "yes1") {
                        
						var link = document.getElementById('yesdata1');

						link.style.display = 'block';
					} else {
						var link = document.getElementById('nodata1');

						link.style.display = 'block';
					}
				}
			});
		}
	</script>
	<script src="{{ asset('assets/wizard/js/modernizr.custom.js')}}"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

	<script src="{{ asset('assets/wizard/js/classie.js')}}"></script>
	<script src="{{ asset('assets/wizard/js/fullscreenForm.js')}}"></script>
	<script>
		(function () {
			var formWrap = document.getElementById('wz_form-wrap');

			[].slice.call(document.querySelectorAll('select.cs-select')).forEach(function (el) {
				new SelectFx(el, {
					stickyPlaceholder: false,
					onChange: function (val) {
						document.querySelector('span.cs-placeholder').style.backgroundColor = val;
					}
				});
			});

			new FForm(formWrap, {
				onReview: function () {
					classie.add(document.body, 'overview'); // for demo purposes only
				}
			});
		})();

		// Prepare the preview for profile picture
		$("#q2").change(function () {
			readURL(this);
		});

		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					$('#wzimagepreview').attr('src', e.target.result).fadeIn('slow');
				}
				reader.readAsDataURL(input.files[0]);
			}
		}

	</script>

	<script>
		function changeBodyColor(newBodyColor) {
			document.body.style.background = newBodyColor;
		}
		function changeTitleColor(newTitleColor) {
			var x, i;
			x = document.querySelectorAll(".pr-settings-title");
			for (i = 0; i < x.length; i++) {
				x[i].style.color = newTitleColor;
			}
		}
       
        // country
        function getSelectedValue(self) {
            fetch("{{ asset('assets/wizard/countries.json')}}")
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    const container = document.getElementById("countryData");
                    const selectedValue = container.value;
                    console.log(selectedValue)

                    var count = 0;
                    data.countries.forEach(country => {
                        if (country === selectedValue) {
                            count = 1;
                        }
                    });
                    if (count) {

                        var link = document.getElementById('constdata');
                        link.style.display = 'block';
                        
                        var box = document.getElementById("myBox");
                        box.style.display = "none";


                        var para = document.getElementById("para1");
                        para.style.display = "none";

                        var country = document.getElementById("countryData");
                        country.style.display = "none";


                    } else {
                        self._nextField();
                    }
                });
        }

        function getSelectedcon(self) {
            $("input[name$='diplomatic_passport']").click(function () {
                
                var e = document.getElementById("countryData");

                var text = e.options[e.selectedIndex].text;
                fetch("{{ asset('assets/wizard/countries.json')}}")
                    .then(response => response.json())
                    .then(data => {

                        var count = 0;
                        data.countries.forEach(country => {
                            if (country === text) {
                                count = 1;

                            }
                        });
                        if (count) {

                            if($(this).val() == 'yes'){

                                $('#diplomatic_passport_yesdata').css('display','block');
                                $('#nodata1').css('display','none');

                                var box = document.getElementById("myBox");
                                box.style.display = "none";

                                var para3 = document.getElementById("para3");
                                para3.style.display = "none";

                                var yesno = document.getElementById("yesno");
                                yesno.style.display = "none";

                            }else{

                                $('#diplomatic_passport_yesdata').css('display','block');
                                $('#valid_visa_or_residence').css('display','none');
                                $('#yesno1').css('display','none');
                                self._nextField();

                            }

                            


                        } else {

                            self._nextField();
                        }
                    });


            });
        }

        $("input[name$='valid_visa_or_residence']").click(function (self) {
            // alert($(this).val());
            var e = document.getElementById("countryData");
            var value = e.value;
            var text = e.options[e.selectedIndex].text;
            
            if($(this).val() == 'yes'){
                // self._nextField();
                // $('#valid_visa_or_residence_class_remove').removeClass('wz_current');
                // $('#valid_visa_or_residence_yes_class').addClass('wz_current');
                // $('#valid_visa_or_residence_yes').css('display','block');
                // $('#nodata1').css('display','none');
                
                
            }else{

                $('#yesno1').css('display','none');
                $('#valid_visa_or_residence').css('display','none');

                $('#countryList3Yes').css('display','none');
                $('#nodata1').css('display','block');
                $('#myBox').css('display','none');

            }

        });
        
        

        $("input[name$='valid_visa_or_residence_yes']").click(function (self) {
            // var test = $(this).val();
            var e = document.getElementById("countryData");
            var value = e.value;
            var text = e.options[e.selectedIndex].text;
               
            $('#nodata1').css('display','none');
                
            if($(this).val() == 'yes'){

                $('#multiple_entry_visa_yes').css('display','block');
                $('#nodata1').css('display','none');
                
            }else{

               
                $('#yesno1').css('display','none');
                $('#multiple_entry_visa_yes').css('display','none');

                $('#countryList3Yes').css('display','none');
                $('#valid_visa_or_residence_yes').css('display','none');
                $('#myBox').css('display','none');
                $('#valid_visa_or_residence_no').css('display','block');

            }

        });

        
        $("input[name$='multiple_entry_visa']").click(function (self) {
            // var test = $(this).val();
            var e = document.getElementById("countryData");
            var value = e.value;
            var text = e.options[e.selectedIndex].text;
               
            $('#nodata1').css('display','none');
            if($(this).val() == 'yes'){

                $('#passport_valid_until_May_31').css('display','block');
                
            }else{

               
                $('#yesno1').css('display','none');
                $('#multiple_entry_visa_yes').css('display','none');
                $('#countryList3Yes').css('display','none');
                $('#myBox').css('display','none');
                $('.multiple_entry_visa_no').css('display','block');

            }
           

        });

        
        
        $("input[name$='passport_valid_until_May_31']").click(function (self) {
            // var test = $(this).val();
            var e = document.getElementById("countryData");
            var value = e.value;
            var text = e.options[e.selectedIndex].text;
               
            $('#passport_valid_until_May_31').css('display','none');
            $('#nodata1').css('display','none');
            
            if($(this).val() == 'yes'){

                $('#step_8_message').css('display','block');
                
                
            }else{

               
                $('#yesno1').css('display','none');
                $('#multiple_entry_visa_yes').css('display','none');
                $('.multiple_entry_visa_no').css('display','block');

            }
              
            $('#valid_visa_or_residence_yes').css('display','none');
            $('#multiple_entry_visa_yes').css('display','none');
            $('#passport_valid_until_May_31').css('display','none');
            $('#countryList3Yes').css('display','none');
            
            $('#myBox').css('display','none');

        });
	</script>

    <script>
		$(document).ready(function() {

			$("#q3").keyup(function() {

                $('.wz-button').css('display','none');
                $("#countryData").val('0');
                $("#email-message").html('');
                $('.wz-button').css('display','block');

				// var email = $(this).val();
				// $.ajax({
                //     type: "Post",
                //     headers: {
                //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //     },
				// 	url: "{{url('wizard-email-check')}}",
				// 	method: "POST",
				// 	data: {email: email},
                //     error: function(xhr, textStatus) {

                //     if (xhr && xhr.responseJSON.message) {
                //        $("#email-message").html(xhr.responseJSON.message);
                //     } else {
                //         $("#email-message").html(xhr.statusText);
                //     }
                //     },
				// 	success: function(response) {
				// 		$("#email-message").html('');
                //         $('.wz-button').css('display','block');

				// 	}
				// });
			});
		});
    </script>
@endpush