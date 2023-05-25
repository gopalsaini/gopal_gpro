
@extends('layouts/app')

@section('title',__(Lang::get('web/help.help')))

@push('custom_css')
    <style>
        .fs-dropdown {
            width: 25.3%;
        }

        .fs-label-wrap{
            height: 50.5px;
            background-color: #FFFCF1;
            border: 1px solid #FFCD34;
        }

        .fs-label-wrap .fs-label{
            padding: 13px 22px 6px 8px;
        }

        form select.active-input {
            background-color: #fffcf1;
            border: 1px solid #ffcd34;
        }
        footer.footer-inner {
            padding-top: 366px  !important;
        }   
    </style>
@endpush

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container">
           
            <div class="step-form">
                <h4 class="inner-head">Passport Info</h4>
                <form id="Passport" action="{{ url('api/sponsorship-passport-info') }}" class="row" enctype="multipart/form-data">
                    @csrf

                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="input">Given name :</label>
                                <input type="text" name="name" placeholder="@lang('web/app.enter') @lang('web/help.name')" value="{{$resultData['result']['name']}}" class="active-input mt-2" required>

                            </div>
                            <div class="col-md-7">
                                <label for="">Surname <span>*</span></label>
                                <input type="text" name="surname" placeholder="@lang('web/app.enter') @lang('web/help.name')" value="{{$resultData['result']['last_name']}}" class="active-input mt-2" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <label for="">Passport Number<span>*</span></label>
                        <input type="text" name="passport_no" placeholder="Enter Passport Number" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Passport Copy<span>*</span></label>
                        <input type="file" name="passport_copy[]" placeholder="Upload passport copy" class="active-input mt-2" required accept="application/pdf, image/png,jpeg,jpg" multiple>
                    </div>

                    <!-- <div class="col-sm-4">
                        <div class="form-group">
                            <label for="input">@lang('admin.dob'):</label>
                            <input type="date" value="{{$resultData['result']['dob']}}" placeholder="DD/ MM/ YYYY" class="active-input mt-2" required name="dob" >
                        </div>
                    </div> -->
                   
                    <!-- <div class="col-lg-4">
                        <label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
                        <div class="common-select">
                            <select id="citizen" class="mt-2 test" name="citizenship" >
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($citizenship as $con)
                                    
                                    <option @if($resultData['result']['citizenship'] >0 && $resultData['result']['citizenship']==$con['country_id']){{'selected'}}@endif value="{{$con['country_id']}}">{{$con['country_name']}} </option>
                                   
                                @endforeach
                            </select>
                        </div>
                    </div> -->
                    
                    <div class="col-lg-4">
                        <label for="country">Which country's passport will you use to come to Panama? <span>*</span></label>
                        <div class="common-select">
                            <select id="country" class="mt-2 test"  name="country_id">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($country as $con)
                                    <option  data-phoneCode="{{ $con['phonecode'] }}" class="active-input mt-2" value="{{ $con['id'] }}">{{ ucfirst($con['name']) }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        
                        <label class="form-check-label">is this a diplomatic passport ?? <span>*</span></label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                                <input type="radio" name="diplomatic_passport" value="Yes" id="yes" class="form__radio-input">
                                <label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
                                    <span class="form__radio-button" style="border:1px solid #dc3545"></span> Yes
                                </label>
                            </div>
                            <div class="form__radio-group">
                                <input type="radio" name="diplomatic_passport" value="No" id="no" class="form__radio-input">
                                <label class="form__label-radio" for="no" class="form__radio-label">
                                    <span class="form__radio-button" style="border:1px solid #dc3545"></span> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 classHideDiv" id="validVisaResidence" style="display:none;">
                        
                        <label class="form-check-label">Do you have a valid Visa or Residence, duly issued by Canada, the United States of America, the Commonwealth of Australia, the Republic of Korea, the State of Japan, the United Kingdom of Great Britain and Northern Ireland, Republic of Singapore, or any of the States that make up the European Union?<span>*</span></label>
                        
                        <div class="form__radio-group">
                            <select id="VisaResidence" class="mt-2 form-control active-input"  name="visa_residence">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                <option  class="active-input mt-2" value="Yes">Yes </option>
                                <option  class="active-input mt-2" value="NO">No </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 classHideDiv" id="step6" style="display:none;">
                        
                        <label class="form-check-label">Is your visa from one of the countries identified in the previous step a multiple entry visa, and is it valid until May 31, 2024?<span>*</span></label>
                        
                        <div class="form__radio-group">
                            <select id="step6Value" class="mt-2 form-control active-input stepValue6"  name="multiple_entry_visa_country">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                
                                <option  class="active-input mt-2" value="Yes">Yes </option>
                                <option  class="active-input mt-2" value="NO">No </option>
                                
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 classHideDiv step7" id="multiple_entry_visa_granted" style="display:none;">
                       
                        <label class="form-check-label">Have you used your multiple entry visa at least once before to enter the country that granted it? <span>*</span></label>
                        
                        <div class="form__radio-group">
                            <select id="multipleEntryVisaGranted" class="mt-2 form-control active-input stepValue7"  name="multiple_entry_visa">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                <option  class="active-input mt-2" value="Yes">Yes </option>
                                <option  class="active-input mt-2" value="NO">No </option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 classHideDiv step8" id="passportValid" style="display:none;">
                       
                        <label class="form-check-label">Is your passport valid until May 31, 2024? <span>*</span></label>
                        
                        <select id="passportValidValue" class="mt-2 form-control active-input stepValue8"  name="passport_valid">
                            <option value="">--@lang('web/ministry-details.select')--</option>
                            <option  class="active-input mt-2" value="Yes">Yes </option>
                            <option  class="active-input mt-2" value="No">No </option>
                        </select>
                        
                    </div>


                    <div class="col-lg-12 classHideDiv step9" id="multiCountrySelect" style="display:none;">
                         <label for="">What countries among Canada, the United States of America, the Commonwealth of Australia, the Republic of Korea, the State of Japan, the United Kingdom of Great Britain and Northern Ireland, Republic of Singapore, or any of the States that make up the European Union you hold the Valid Visa?<span>*</span></label>
                         <div class="common-select">
                            <select id="country1" class="mt-2 test"  name="countries[]" multiple>
                                @foreach($country as $con)
                                    @php $visaResidenceArray = [233,39,14,116,109,232,105,199]; @endphp 
                                    @if(in_array($con['id'],$visaResidenceArray))
                                        <option  data-phoneCode="{{ $con['phonecode'] }}" class="active-input mt-2" value="{{ $con['id'] }}">{{ ucfirst($con['name']) }} </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row classHideDiv" id="country-info" style="display: none;">
                       
                    </div>


                    <div class="col-lg-12">
                        <div class="form-check">
                            <input class="form-check-input form-control" type="checkbox" value="Yes" id="flexCheckDefault" name="user_confirm">
                            <label class="form-check-label" for="flexCheckDefault">
                            &nbsp; &nbsp; &nbsp; I confirm the Details given above are accurate.
                            </label>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="Passport">@lang('web/help.submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- banner-end -->

@endsection



@push('custom_js')

<script>
var doNotRequireVisa = ['82','6','7','10','194','11','12','14','15','17','20','22','23','21','27','28','29','31','33','34','26','40','37','39','44','57','238','48','53','55','59','61','64','66','231','200','201','207','233','69','182','73','74','75','79','81','87','90','94','97','98','99','232','105','100','49','137','202','106','107','108','109','113','114','117','120','125','126','127','129','130','132','133','135','140','142','143','144','145','146','147','153','159','165','158','156','168','171','172','176','177','179','58','116','181','191','185','192','188','196','197','199','186','204','213','214','219','216','222','223','225','228','230','235','237','240']; 
		

$(document).ready(function() {
    $('.passportValid').hide();

    $('#yes').change(function() {

        if ($(this).is(':checked')) {

            $('.passportValid').hide();

            var countrySelected = $('#country').val();

            if (countrySelected === '') {

                alert("Please select a country first.");
                $(this).prop('checked', false);

            } else {
                
                if (doNotRequireVisa.includes(countrySelected)) {
                    
                    $('.classHideDiv').hide();

                }else {

                    $('#validVisaResidence').show();
                    $('#passportValid').css('display','none');
                   
                    
                }
            }

        } else {

            $('#multiCountrySelect').hide();
        }
        
    });

    $('#no').change(function() {

        if ($(this).is(':checked')) {

            var countrySelected = $('#country').val();

            if (countrySelected === '') {

                alert("Please select a country first.");
                $(this).prop('checked', false);

            } else {
                
                if (doNotRequireVisa.includes(countrySelected)) {
                    
                    $('.classHideDiv').hide();

                }else {

                   
                    $('#country-info').show();
                    $('#validVisaResidence').show();
                    $('#passportValid').css('display','none');
                    
                }
            }

        }
    });
});


    $('#VisaResidence').change(function() {

        $('#step6').hide();
        $('.step7').hide();
        $('.step8').hide();
        $('.step9').hide();
        $('.stepValue6').val(null);

        if ($(this).val() == 'Yes') {

            $('#step6').show();
        }
    });

    $('#step6Value').change(function() {
        
        $('#multiple_entry_visa_granted').hide();
        $('.step8').hide();
        $('.step9').hide();
        $('.stepValue7').val(null);

        if ($(this).val() == 'Yes') {

            $('#multiple_entry_visa_granted').show();
        }
    });

    $('#multipleEntryVisaGranted').change(function() {
        
        $('.step9').hide();
        $('#passportValid').hide();
        $('.stepValue8').val(null);
        $('#country-info').hide();

        if ($(this).val() == 'Yes') {

            $('#passportValid').show();
        }
    });

    $('#passportValidValue').change(function() {
        
        $('#multiCountrySelect').hide();
        $('#country-info').hide();

        if ($(this).val() == 'Yes') {

            $('#multiCountrySelect').show();
        }
    });

    $(document).ready(function() {
        $('#country1').change(function() {
            var selectedCountries = $(this).val();

            if (selectedCountries && selectedCountries.length > 0) {
                $('#country-info').empty(); // Clear previous content

                selectedCountries.forEach(function(countryId) {
                    var countryName = $('#country option[value="' + countryId + '"]').text();
                    var countryDiv = $('<div>').addClass('country-div col-lg-4').html('<label for="">Visa/Residence Proof for '+countryName+'<span>*</span></label><input type="file" name="countries_doc[]" placeholder="Upload countries doc" class="active-input mt-2" required accept="application/pdf, image/png,jpeg,jpg">');

                    $('#country-info').append(countryDiv);
                });

                $('#country-info').show();
            } else {
                $('#country-info').hide();
            }
        });
    });

    $(document).ready(function(){

        $('.test').fSelect({
            placeholder: "-- @lang('web/ministry-details.select') -- ",
            numDisplayed: 5,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
        });

    });

    $("form#Passport").submit(function(e) {

        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        var form_data = new FormData(this);

        var btnhtml = $("button[form=" + formId + "]").html();

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            headers: {
                "Authorization": "Bearer {{\Session::get('gpro_user')}}"
            },
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.status + ': ' + xhr.statusText);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                showMsg('success', data.message);
                location.href = "{{url('travel-information')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });


    $(document).ready(function() {
        $('#country').change(function() {
            
            var countrySelected = $(this).val();
            $('#VisaResidence').val(null);

            if (doNotRequireVisa.includes(countrySelected)) {
                 
                $('.classHideDiv').hide();

            }else{

                if ($('#yes').is(':checked')) {
                    $('#validVisaResidence').show();
                }
                if ($('#no').is(':checked')) {
                    $('#validVisaResidence').show();
                }
                
            }
        });
    });
</script>


@endpush