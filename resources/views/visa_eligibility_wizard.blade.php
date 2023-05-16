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
                                                                
                                                                <option value="Afghanistan">Afghanistan</option>
                                                                <option value="Åland Islands">Åland Islands</option>
                                                                <option value="Albania">Albania</option>
                                                                <option value="Algeria">Algeria</option>
                                                                <option value="American Samoa">American Samoa</option>
                                                                <option value="Andorra">Andorra</option>
                                                                <option value="Angola">Angola</option>
                                                                <option value="Anguilla">Anguilla</option>
                                                                <option value="Antarctica">Antarctica</option>
                                                                <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                                                <option value="Argentina">Argentina</option>
                                                                <option value="Armenia">Armenia</option>
                                                                <option value="Aruba">Aruba</option>
                                                                <option value="Australia">Australia</option>
                                                                <option value="Austria">Austria</option>
                                                                <option value="Azerbaijan">Azerbaijan</option>
                                                                <option value="Bahamas">Bahamas</option>
                                                                <option value="Bahrain">Bahrain</option>
                                                                <option value="Bangladesh">Bangladesh</option>
                                                                <option value="Barbados">Barbados</option>
                                                                <option value="Belarus">Belarus</option>
                                                                <option value="Belgium">Belgium</option>
                                                                <option value="Belize">Belize</option>
                                                                <option value="Benin">Benin</option>
                                                                <option value="Bermuda">Bermuda</option>
                                                                <option value="Bhutan">Bhutan</option>
                                                                <option value="Bolivia">Bolivia</option>
                                                                <option value="Bosnia and Herzegovina">Bosnia and Herzegovina </option>
                                                                <option value="Botswana">Botswana</option>
                                                                <option value="Bouvet Island">Bouvet Island</option>
                                                                <option value="Brazil">Brazil</option>
                                                                <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                                                                <option value="Brunei Darussalam">Brunei Darussalam</option>
                                                                <option value="Bulgaria">Bulgaria</option>
                                                                <option value="Burkina Faso">Burkina Faso</option>
                                                                <option value="Burundi">Burundi</option>
                                                                <option value="Cambodia">Cambodia</option>
                                                                <option value="Cameroon">Cameroon</option>
                                                                <option value="Canada">Canada</option>
                                                                <option value="Cape Verde">Cape Verde</option>
                                                                <option value="Cayman Islands">Cayman Islands</option>
                                                                <option value="Central African Republic">Central African Republic</option>
                                                                <option value="Chad">Chad</option>
                                                                <option value="Chile">Chile</option>
                                                                <option value="China">China</option>
                                                                <option value="Christmas Island">Christmas Island</option>
                                                                <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands </option>
                                                                <option value="Colombia">Colombia</option>
                                                                <option value="Comoros">Comoros</option>
                                                                <option value="Congo">Congo</option>
                                                                <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                                                                <option value="Cook Islands">Cook Islands</option>
                                                                <option value="Costa Rica">Costa Rica</option>
                                                                <option value="Cote D'ivoire">Cote D'ivoire</option>
                                                                <option value="Croatia">Croatia</option>
                                                                <option value="Cuba">Cuba</option>
                                                                <option value="Cyprus">Cyprus</option>
                                                                <option value="Czech Republic">Czech Republic</option>
                                                                <option value="Denmark">Denmark</option>
                                                                <option value="Djibouti">Djibouti</option>
                                                                <option value="Dominica">Dominica</option>
                                                                <option value="Dominican Republic">Dominican Republic</option>
                                                                <option value="Ecuador">Ecuador</option>
                                                                <option value="Egypt">Egypt</option>
                                                                <option value="El Salvador">El Salvador</option>
                                                                <option value="Equatorial Guinea">Equatorial Guinea</option>
                                                                <option value="Eritrea">Eritrea</option>
                                                                <option value="Estonia">Estonia</option>
                                                                <option value="Ethiopia">Ethiopia</option>
                                                                <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                                                                <option value="Faroe Islands">Faroe Islands</option>
                                                                <option value="Fiji">Fiji</option>
                                                                <option value="Finland">Finland</option>
                                                                <option value="France">France</option>
                                                                <option value="French Guiana">French Guiana</option>
                                                                <option value="French Polynesia">French Polynesia</option>
                                                                <option value="French Southern Territories">French Southern Territories</option>
                                                                <option value="Gabon">Gabon</option>
                                                                <option value="Gambia">Gambia</option>
                                                                <option value="Georgia">Georgia</option>
                                                                <option value="Germany">Germany</option>
                                                                <option value="Ghana">Ghana</option>
                                                                <option value="Gibraltar">Gibraltar</option>
                                                                <option value="Greece">Greece</option>
                                                                <option value="Greenland">Greenland</option>
                                                                <option value="Grenada">Grenada</option>
                                                                <option value="Guadeloupe">Guadeloupe</option>
                                                                <option value="Guam">Guam</option>
                                                                <option value="Guatemala">Guatemala</option>
                                                                <option value="Guernsey">Guernsey</option>
                                                                <option value="Guinea">Guinea</option>
                                                                <option value="Guinea-bissau">Guinea-bissau</option>
                                                                <option value="Guyana">Guyana</option>
                                                                <option value="Haiti">Haiti</option>
                                                                <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                                                                <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                                                                <option value="Honduras">Honduras</option>
                                                                <option value="Hong Kong">Hong Kong</option>
                                                                <option value="Hungary">Hungary</option>
                                                                <option value="India">India</option>
                                                                <option value="Iceland">Iceland</option>
                                                                <option value="Indonesia">Indonesia</option>
                                                                <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                                                                <option value="Iraq">Iraq</option>
                                                                <option value="Ireland">Ireland</option>
                                                                <option value="Isle of Man">Isle of Man</option>
                                                                <option value="Israel">Israel</option>
                                                                <option value="Italy">Italy</option>
                                                                <option value="Jamaica">Jamaica</option>
                                                                <option value="Japan">Japan</option>
                                                                <option value="Jersey">Jersey</option>
                                                                <option value="Jordan">Jordan</option>
                                                                <option value="Kazakhstan">Kazakhstan</option>
                                                                <option value="Kenya">Kenya</option>
                                                                <option value="Kiribati">Kiribati</option>
                                                                <option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option>
                                                                <option value="Korea, Republic of">Korea, Republic of</option>
                                                                <option value="Kuwait">Kuwait</option>
                                                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                                                <option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option>
                                                                <option value="Latvia">Latvia</option>
                                                                <option value="Lebanon">Lebanon</option>
                                                                <option value="Lesotho">Lesotho</option>
                                                                <option value="Liberia">Liberia</option>
                                                                <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya </option>
                                                                <option value="Liechtenstein">Liechtenstein</option>
                                                                <option value="Lithuania">Lithuania</option>
                                                                <option value="Luxembourg">Luxembourg</option>
                                                                <option value="Macao">Macao</option>
                                                                <option value="Macedonia, The Former Yugoslav Republic of"> Macedonia, The Former Yugoslav Republic of</option>
                                                                <option value="Madagascar">Madagascar</option>
                                                                <option value="Malawi">Malawi</option>
                                                                <option value="Malaysia">Malaysia</option>
                                                                <option value="Maldives">Maldives</option>
                                                                <option value="Mali">Mali</option>
                                                                <option value="Malta">Malta</option>
                                                                <option value="Marshall Islands">Marshall Islands</option>
                                                                <option value="Martinique">Martinique</option>
                                                                <option value="Mauritania">Mauritania</option>
                                                                <option value="Mauritius">Mauritius</option>
                                                                <option value="Mayotte">Mayotte</option>
                                                                <option value="Mexico">Mexico</option>
                                                                <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                                                                <option value="Moldova, Republic of">Moldova, Republic of </option>
                                                                <option value="Monaco">Monaco</option>
                                                                <option value="Mongolia">Mongolia</option>
                                                                <option value="Montenegro">Montenegro</option>
                                                                <option value="Montserrat">Montserrat</option>
                                                                <option value="Morocco">Morocco</option>
                                                                <option value="Mozambique">Mozambique</option>
                                                                <option value="Myanmar">Myanmar</option>
                                                                <option value="Namibia">Namibia</option>
                                                                <option value="Nauru">Nauru</option>
                                                                <option value="Nepal">Nepal</option>
                                                                <option value="Netherlands">Netherlands</option>
                                                                <option value="Netherlands Antilles">Netherlands Antilles </option>
                                                                <option value="New Caledonia">New Caledonia</option>
                                                                <option value="New Zealand">New Zealand</option>
                                                                <option value="Nicaragua">Nicaragua</option>
                                                                <option value="Niger">Niger</option>
                                                                <option value="Nigeria">Nigeria</option>
                                                                <option value="Niue">Niue</option>
                                                                <option value="Norfolk Island">Norfolk Island</option>
                                                                <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                                                                <option value="Norway">Norway</option>
                                                                <option value="Oman">Oman</option>
                                                                <option value="Pakistan">Pakistan</option>
                                                                <option value="Palau">Palau</option>
                                                                <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                                                                <option value="Panama">Panama</option>
                                                                <option value="Papua New Guinea">Papua New Guinea</option>
                                                                <option value="Paraguay">Paraguay</option>
                                                                <option value="Peru">Peru</option>
                                                                <option value="Philippines">Philippines</option>
                                                                <option value="Pitcairn">Pitcairn</option>
                                                                <option value="Poland">Poland</option>
                                                                <option value="Portugal">Portugal</option>
                                                                <option value="Puerto Rico">Puerto Rico</option>
                                                                <option value="Qatar">Qatar</option>
                                                                <option value="Reunion">Reunion</option>
                                                                <option value="Romania">Romania</option>
                                                                <option value="Russian Federation">Russian Federation</option>
                                                                <option value="Rwanda">Rwanda</option>
                                                                <option value="Saint Helena">Saint Helena</option>
                                                                <option value="Saint Kitts and Nevis">Saint Kitts and Nevis </option>
                                                                <option value="Saint Lucia">Saint Lucia</option>
                                                                <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                                                                <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                                                                <option value="Samoa">Samoa</option>
                                                                <option value="San Marino">San Marino</option>
                                                                <option value="Sao Tome and Principe">Sao Tome and Principe </option>
                                                                <option value="Saudi Arabia">Saudi Arabia</option>
                                                                <option value="Senegal">Senegal</option>
                                                                <option value="Serbia">Serbia</option>
                                                                <option value="Seychelles">Seychelles</option>
                                                                <option value="Sierra Leone">Sierra Leone</option>
                                                                <option value="Singapore">Singapore</option>
                                                                <option value="Slovakia">Slovakia</option>
                                                                <option value="Slovenia">Slovenia</option>
                                                                <option value="Solomon Islands">Solomon Islands</option>
                                                                <option value="Somalia">Somalia</option>
                                                                <option value="South Africa">South Africa</option>
                                                                <option value="South Georgia and The South Sandwich Islands"> South Georgia and The South Sandwich Islands</option>
                                                                <option value="Spain">Spain</option>
                                                                <option value="Sri Lanka">Sri Lanka</option>
                                                                <option value="Sudan">Sudan</option>
                                                                <option value="Suriname">Suriname</option>
                                                                <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen </option>
                                                                <option value="Swaziland">Swaziland</option>
                                                                <option value="Sweden">Sweden</option>
                                                                <option value="Switzerland">Switzerland</option>
                                                                <option value="Syrian Arab Republic">Syrian Arab Republic </option>
                                                                <option value="Taiwan">Taiwan</option>
                                                                <option value="Tajikistan">Tajikistan</option>
                                                                <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                                                                <option value="Thailand">Thailand</option>
                                                                <option value="Timor-leste">Timor-leste</option>
                                                                <option value="Togo">Togo</option>
                                                                <option value="Tokelau">Tokelau</option>
                                                                <option value="Tonga">Tonga</option>
                                                                <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                                                <option value="Tunisia">Tunisia</option>
                                                                <option value="Turkey">Turkey</option>
                                                                <option value="Turkmenistan">Turkmenistan</option>
                                                                <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                                                                <option value="Tuvalu">Tuvalu</option>
                                                                <option value="Uganda">Uganda</option>
                                                                <option value="Ukraine">Ukraine</option>
                                                                <option value="United Arab Emirates">United Arab Emirates</option>
                                                                <option value="United Kingdom">United Kingdom</option>
                                                                <option value="United States of America">United States of America</option>
                                                                <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                                                                <option value="Uruguay">Uruguay</option>
                                                                <option value="Uzbekistan">Uzbekistan</option>
                                                                <option value="Vanuatu">Vanuatu</option>
                                                                <option value="Venezuela">Venezuela</option>
                                                                <option value="Vietnam">Vietnam</option>
                                                                <option value="Virgin Islands, British">Virgin Islands, British </option>
                                                                <option value="Virgin Islands, U.S.">Virgin Islands, U.S. </option>
                                                                <option value="Wallis and Futuna">Wallis and Futuna</option>
                                                                <option value="Western Sahara">Western Sahara</option>
                                                                <option value="Yemen">Yemen</option>
                                                                <option value="Zambia">Zambia</option>
                                                                <option value="Zimbabwe">Zimbabwe</option>
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