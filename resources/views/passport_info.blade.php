
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
                <h4 class="inner-head">@lang('web/wizard.Passport_Info')</h4>
                <form id="Passport" action="{{ url('api/sponsorship-passport-info') }}" class="row" enctype="multipart/form-data">
                    @csrf

                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="input">@lang('web/wizard.Given_name') <span>*</span></label>
                                <input type="text" name="name" placeholder="@lang('web/app.enter') @lang('web/wizard.Given_name')" value="{{$resultData['result']['name']}}" class="active-input mt-2" required>

                            </div>
                            <div class="col-md-7">
                                <label for="">@lang('web/wizard.Surname') <span>*</span></label>
                                <input type="text" name="surname" placeholder="@lang('web/app.enter') @lang('web/wizard.Surname')" value="{{$resultData['result']['last_name']}}" class="active-input mt-2" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <label for="">@lang('web/wizard.Passport_Number')<span>*</span></label>
                        <input type="text" name="passport_no" placeholder="@lang('web/wizard.Enter_Passport_Number')" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="" class="d-flex">@lang('web/wizard.Passport_Copy') &nbsp;&nbsp;<label class="text-danger">(@lang('web/app.Accepted_File_Formats'))*</label></label>
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
                        <label for="country">@lang('web/wizard.which_country_passport_will_you_use_to_come_to_panama') <span>*</span></label>
                        <div class="common-select-data">
                            <select id="country" class="mt-2 test"  name="country_id">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($country as $con)
                                    <option  data-phoneCode="{{ $con->phonecode }}" class="active-input mt-2" value="{{ $con->id }}">{{ ucfirst($con->name2) }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 " >
                        
                        <label class="form-check-label">@lang('web/wizard.is_this_a_diplomatic_passport') <span>*</span></label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                                <input type="radio" name="diplomatic_passport" value="Yes" id="yes" class="form__radio-input">
                                <label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
                                    <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.yes') 
                                </label>
                            </div>
                            <div class="form__radio-group">
                                <input type="radio" name="diplomatic_passport" value="No" id="no" class="form__radio-input">
                                <label class="form__label-radio" for="no" class="form__radio-label">
                                    <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.no') 
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 classHideDiv" id="validVisaResidence" style="display:none;">
                        
                        <label class="form-check-label">@lang('web/wizard.do_you_have_a_valid_visa_or_residence') (<a id="EuropeanUnionCountryShow" href="javascript::void()">@lang('web/wizard.List_of_countries_that_make_European_Union') </a>)<span>*</span></label>
                        <div id="EuropeanUnionCountryDiv" style="display:none">
                            <br>
                            <p>Austria, Belgium, Bulgaria, Croatia, Cyprus, Czechia, Denmark, Estonia, Finland, France, Germany, Greece, Hungary, Ireland, Italy, Latvia, Lithuania, Luxembourg, Malta, Netherlands, Poland, Portugal, Romania, Slovakia, Slovenia, Spain and Sweden.</p>
                        </div>
                        <div class="form__radio-group">
                            <select id="VisaResidence" class="mt-2 form-control active-input"  name="visa_residence">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                <option  class="active-input mt-2" value="Yes">@lang('web/wizard.yes') </option>
                                <option  class="active-input mt-2" value="NO">@lang('web/wizard.no') </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 classHideDiv" id="step6" style="display:none;">
                        
                        <label class="form-check-label">@lang('web/wizard.do_you_have_a_valid_visa_or_residence_yes')<span>*</span></label>
                        
                        <div class="form__radio-group">
                            <select id="step6Value" class="mt-2 form-control active-input stepValue6"  name="multiple_entry_visa_country">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                
                                <option  class="active-input mt-2" value="Yes">@lang('web/wizard.yes')  </option>
                                <option  class="active-input mt-2" value="NO">@lang('web/wizard.no') </option>
                                
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 classHideDiv step7" id="multiple_entry_visa_granted" style="display:none;">
                       
                        <label class="form-check-label">@lang('web/wizard.step_7_question')<span>*</span></label>
                        
                        <div class="form__radio-group">
                            <select id="multipleEntryVisaGranted" class="mt-2 form-control active-input stepValue7"  name="multiple_entry_visa">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                <option  class="active-input mt-2" value="Yes">@lang('web/wizard.yes')  </option>
                                <option  class="active-input mt-2" value="NO">@lang('web/wizard.no') </option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-lg-12 classHideDiv step8" id="passportValid" style="display:none;">
                       
                        <label class="form-check-label">@lang('web/wizard.is_your_passport_valid_until')<span>*</span></label>
                        
                        <select id="passportValidValue" class="mt-2 form-control active-input stepValue8"  name="passport_valid">
                            <option value="">--@lang('web/ministry-details.select')--</option>
                            <option  class="active-input mt-2" value="Yes">@lang('web/wizard.yes') </option>
                            <option  class="active-input mt-2" value="No">@lang('web/wizard.no') </option>
                        </select>
                        
                    </div>


                    <div class="col-lg-12 classHideDiv step9" id="multiCountrySelect" style="display:none;">
                         <label for="">@lang('web/wizard.What_countries_among') (<a id="EuropeanUnionCountryShowData" href="javascript::void()">@lang('web/wizard.List_of_countries_that_make_European_Union')</a>)<span>*</span></label>
                         
                         <div id="EuropeanUnionCountryDataDiv" style="display:none">
                            <br>
                            <p>Austria, Belgium, Bulgaria, Croatia, Cyprus, Czechia, Denmark, Estonia, Finland, France, Germany, Greece, Hungary, Ireland, Italy, Latvia, Lithuania, Luxembourg, Malta, Netherlands, Poland, Portugal, Romania, Slovakia, Slovenia, Spain and Sweden.</p>
                        </div>
                         <div class="common-select">
                            <select id="country1" class="mt-2 test2"  name="countries[]" multiple>
                                @foreach($country as $con)
                                    @php $visaResidenceArray = [233,39,14,116,109,232,105,199,15]; @endphp 
                                    @if(in_array($con->id,$visaResidenceArray))
                                        @if($con->id != '15')
                                            <option  data-phoneCode="{{ $con->phonecode }}" class="active-input mt-2" value="{{ $con->id }}">{{ ucfirst($con->name2) }} </option>
                                        @else
                                            <option  data-phoneCode="European Union" class="active-input mt-2" value="{{ $con->id }}">European Union </option>
                                        @endif
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
                            &nbsp; &nbsp; &nbsp;  @lang('web/wizard.confirm_the_Details')
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

var doNotRequireVisa = ['82','6','7','10','194','11','12','14','15','17','20','22','23','21','255','27','28','29','31','33','34','26','40','37','39','44','57','238','48','53','55','59','61','64','66','231','200','201','207','233','69','182','73','74','75','79','81','87','90','94','97','98','99','232','105','100','49','137','202','106','107','108','109','113','114','117','120','125','126','127','251','130','132','133','135','140','142','143','144','145','146','147','152','153','159','165','158','156','168','171','172','173','176','177','179','58','256','252','116','181','191','185','192','188','253','196','197','199','186','204','213','214','219','216','222','223','225','228','230','235','237','240']; 

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
                    $('#validVisaResidence').hide();

                }else {

                    $('#validVisaResidence').show();
                    $('#country-info').show();
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
        }else{
            $('#country-info').html('');
            
            $('.test2').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
                $(this).removeClass('selected', false);
            });
            $('.common-select').find('.fs-label').html('--select--');

        }
    });

    $('#step6Value').change(function() {
        
        $('#multiple_entry_visa_granted').hide();
        $('.step8').hide();
        $('.step9').hide();
        $('.stepValue7').val(null);
        $('#country-info').html('');
        
        $('.test2').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
            $(this).removeClass('selected', false);
        });
        $('.common-select').find('.fs-label').html('--select--');
        
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
        }else{
            
            $('.test2').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
                $(this).removeClass('selected', false);
            });
            $('.common-select').find('.fs-label').html('--select--');
            
        }
    });

    $('#passportValidValue').change(function() {
        
        $('#multiCountrySelect').hide();
        $('#country-info').hide();

        if ($(this).val() == 'Yes') {

            $('#multiCountrySelect').show();
        }else{
            $('#country1').attr('required',false);
            
            $('.test2').prev(".fs-dropdown").find(".fs-options .fs-option").each(function() {
                $(this).removeClass('selected', false);
            });
            $('.common-select').find('.fs-label').html('--select--');
            
        }
    });

    $(document).ready(function() {
        $('#country1').change(function() {
            var selectedCountries = $(this).val();

            if (selectedCountries && selectedCountries.length > 0) {
                $('#country-info').empty(); // Clear previous content

                selectedCountries.forEach(function(countryId) {

                    if(countryId == '15'){
                        var countryName = $('#country option[value="' + countryId + '"]').text();
                        var countryDiv = $('<div>').addClass('country-div col-lg-4').html('<label for="" >@lang("web/wizard.Visa_Residence_Proof_for") European Union &nbsp;<label class="text-danger">(@lang("web/app.Accepted_File_Formats"))*</label></label><input type="file" name="countries_doc[]" placeholder="Upload countries doc" class="active-input mt-2" required accept="application/pdf, image/png,jpeg,jpg">');

                    }else{
                        var countryName = $('#country option[value="' + countryId + '"]').text();
                        var countryDiv = $('<div>').addClass('country-div col-lg-4').html('<label for="" >@lang("web/wizard.Visa_Residence_Proof_for") '+countryName+' &nbsp;<label class="text-danger">(@lang("web/app.Accepted_File_Formats"))*</label></label><input type="file" name="countries_doc[]" placeholder="Upload countries doc" class="active-input mt-2" required accept="application/pdf, image/png,jpeg,jpg">');

                    }

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

        $('.test2').fSelect({
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

    
    $('#EuropeanUnionCountryShow').click(function() {
        
        $('#EuropeanUnionCountryDiv').show();
    });
    
    $('#EuropeanUnionCountryShowData').click(function() {
        
        $('#EuropeanUnionCountryDataDiv').show();
    });

</script>


@endpush