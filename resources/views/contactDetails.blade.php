
@extends('layouts/app')

@section('title',__(Lang::get('web/contact-details.contact').Lang::get('web/contact-details.details')))

@push('custom_css')
    <style>
        .common-select .fs-wrap{
            width: 100%;
        }
        .common-select .fs-dropdown{
            width: 25.2%;
        }
        .fs-label-wrap{
            height: 50.5px;
            background: #F9F9F9;
            border:0px;
        }
        .fs-wrap{
            width: 108px
        }
        .fs-label-wrap .fs-label{
            padding: 15px 15px 2px 7px;
        }
    </style>
@endpush

@section('content')

    <!-- banner-start -->
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form-wrap">
                <ul>
                    <li>
                        <a href="{{url('profile-update')}}" class="active">
                            <!-- //Vineet - 080123 -->
                            <!-- <span>01</span>@lang('web/profile-details.personal') @lang('web/profile-details.details') -->
                            <span>01</span>@lang('web/profile-details.personal-details-combined')
                            <!-- //Vineet - 080123 -->
                        </a>
                    </li>
                    <li>
                        <a href="{{url('contact-details')}}" class="active">
                            <!-- //Vineet - 080123 -->
                            <!-- <span>02</span>@lang('web/contact-details.contact') @lang('web/contact-details.details') -->
                            <span>02</span>@lang('web/contact-details.contact-details-combined')
                            <!-- //Vineet - 080123 -->
                        </a>
                    </li>
                    <li>
                        <a href="{{url('ministry-details')}}">
                            <!-- //Vineet - 080123 -->
                            <!-- <span>03</span>@lang('web/ministry-details.ministry') @lang('web/ministry-details.details') -->
                            <span>03</span>@lang('web/ministry-details.ministry-details-combined')
                            <!-- //Vineet - 080123 -->
                        </a>
                    </li>
                </ul>
            </div>
            <div class="step-form">
                <!-- //Vineet - 080123 -->
                <!-- <h4 class="inner-head">@lang('web/contact-details.contact') @lang('web/contact-details.details')</h4> -->
                <h4 class="inner-head">@lang('web/contact-details.contact-details-combined')</h4>
                <!-- //Vineet - 080123 -->
                <form id="formSubmit" action="{{ route('contact-details') }}" class="row" enctype="multipart/form-data"> 
                    <div class="col-lg-6">
                        <!-- //Vineet - 080123 -->
                        <!-- <label for="">@lang('web/contact-details.postal') @lang('web/contact-details.address') <span>*</span></label>
                        <input type="text" autocomplete="text" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.postal') @lang('web/contact-details.address')" name="contact_address" required class="active-input mt-2" value="{{$resultData['result']['contact_address']}}"> -->
                        <label for="">@lang('web/contact-details.postal-address') <span>*</span></label>
                        <input type="text" autocomplete="text" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.postal-address')" name="contact_address" required class="active-input mt-2" value="{{$resultData['result']['contact_address']}}">
                        <!-- //Vineet - 080123 -->
                    </div>
                    <div class="col-lg-6">
                        <!-- //Vineet - 080123 -->
                        <!-- <label for="">@lang('web/contact-details.zip')/@lang('web/contact-details.postal') @lang('web/contact-details.code')</label> -->
                        <label for="">@lang('web/contact-details.zip-postal-code')</label>
                        <!-- <input type="text" autocomplete="text" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.zip')/@lang('web/contact-details.postal') @lang('web/contact-details.code')" name="contact_zip_code" class="mt-2" value="{{$resultData['result']['contact_zip_code']}}"> -->
                        <input type="text" autocomplete="text" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.zip-postal-code')" name="contact_zip_code" class="mt-2" value="{{$resultData['result']['contact_zip_code']}}">
                        <!-- //Vineet - 080123 -->
                    </div>
                    <div class="col-lg-4">
                        <label for="country">@lang('web/contact-details.country') <span>*</span></label>
                        <div class="common-select">
                            <select id="country" placeholder="- @lang('web/app.select_code') -" data-state_id="{{$resultData['result']['contact_state_id']}}" data-city_id="{{$resultData['result']['contact_city_id']}}" class="mt-2 country selectbox test" name="contact_country_id">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($country as $con)
                                <option data-phoneCode="{{ $con['phonecode'] }}" @if($resultData['result']['contact_country_id']==$con['id']){{'selected'}}@endif value="{{ $con['id'] }}">{{ ucfirst($con['name']) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="alaska">@lang('web/contact-details.state')/@lang('web/contact-details.province') <span>*</span></label>
                        <div class="common-select">
                            <select id="alaska" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 statehtml test selectbox" name="contact_state_id">
                                
                            </select>
                        </div>
                        
                        <div style="display: @if($resultData['result']['contact_state_id'] == 0) block @else none @endif " id="OtherStateDiv">
                            <input type="text" autocomplete="off" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.state')/@lang('web/contact-details.province')" name="contact_state_name" id="ContactStateName" class="mt-2" value="{{$resultData['result']['contact_state_name']}}">
                        </div>
                    </div>
                   
                    <div class="col-lg-4">
                        <label for="Illinois">@lang('web/contact-details.city') <span>*</span></label>
                        <div class="common-select">
                            <select id="Illinois" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 cityHtml test selectbox" name="contact_city_id">
                                
                            </select>
                        </div>
                        <div style="display:@if($resultData['result']['contact_city_id'] == 0) block @else none @endif" id="OtherCityDiv">
                            <input type="text" autocomplete="off" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.city')" name="contact_city_name" class="mt-2" id="ContactCityName" value="{{$resultData['result']['contact_city_name']}}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">@lang('web/contact-details.phone') <span>*</span></label>
                        <select class="form-control test phoneCode" name="user_mobile_code"> 
                            <option value="" >--@lang('web/app.select_code')--</option>
                            @foreach($country as $con)
                                <option @if($resultData['result']['phone_code']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                            @endforeach
                        </select>
                        <input  style="margin-left:-4px;width: 70%;" type="tel" id="phone" class="mt-2" name="mobile" onkeypress="return /[0-9 ]/i.test(event.key)" required value="{{$resultData['result']['mobile']}}" autocomplete="off">
                    </div>
                    <div class="col-lg-4">
                        <label for="">@lang('web/contact-details.business') @lang('web/contact-details.or') @lang('web/contact-details.home')</label>
                        <select class="form-control test phoneCode" name="contact_business_codenumber"> 
                            <option value="" >--@lang('web/app.select_code')--</option>
                            @foreach($country as $con)
                                <option @if($resultData['result']['contact_business_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                            @endforeach
                        </select>
                        <input style="margin-left:-4px;width: 70%;" type="tel" id="home" class="mt-2" name="contact_business_number" onkeypress="return /[0-9 ]/i.test(event.key)"  value="{{$resultData['result']['contact_business_number']}}" autocomplete="off">
                    </div>
                    <div class="col-lg-4"  id="whatsup"  style="display:@if($resultData['result']['contact_whatsapp_number']==$resultData['result']['mobile']){{'none'}}@else{{'block'}}@endif">
                        <label for="">@lang('web/app.select_code') <span>*</span></label>
                        <select class="form-control test phoneCode" name="contact_whatsapp_codenumber"> 
                            <option value="" >--@lang('web/app.select_code')--</option>
                            @foreach($country as $con)
                                <option @if($resultData['result']['contact_whatsapp_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                            @endforeach
                        </select>
                        <input style="margin-left:-4px;width: 70%;" type="tel" id="whatsapp" class="mt-2 whatsupinput" name="contact_whatsapp_number" @if($resultData['result']['contact_whatsapp_number']!=$resultData['result']['mobile']){{'required'}}@endif value="{{$resultData['result']['contact_whatsapp_number']}}" autocomplete="off">
                    </div>
                    <div class="col-lg-12">
                        <ul class="unstyled centered">
                            <li>
                                <input class="styled-checkbox" id="styled-checkbox-1" type="checkbox" value="Yes" name="whatsapp_number_same_mobile"  @if($resultData['result']['contact_whatsapp_number']==$resultData['result']['mobile']){{'checked'}}@endif>
                                <label for="styled-checkbox-1">@lang('web/contact-details.whatsapp-same-phone')</label>
                              </li>
                              <li>
                                <input class="styled-checkbox" id="styled-checkbox-3" type="checkbox" value="Yes" name="update_whatsup" >
                                <label for="styled-checkbox-3">@lang('web/contact-details.receive-updates')</label>
                              </li>
                              <li>
                                <input class="styled-checkbox" id="styled-checkbox-2" type="checkbox" value="1" required name="terms_and_condition" @if($resultData['result']['terms_and_condition'] == '1'){{'checked'}}@endif>
                                <label for="styled-checkbox-2">@lang('web/contact-details.terms-and-conditions')</label>
                              </li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="formSubmit">@lang('web/contact-details.next')</button>
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

        

    $('#styled-checkbox-1').click(function(){
 
        if($(this).is(":checked")){
           
            $('#whatsup').css('display','none');
            $('.whatsupinput').prop('required',false); 

        }else{

            $('#whatsup').css('display','block');
            $('.whatsupinput').prop('required',true);
           
        }
    });

    $("form#formSubmit").submit(function(e) {
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        if (fSelectRequired(formId)) {
            return false;
        }

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                location.href = "{{ route('ministry-details') }}";
                showMsg('success', data.message); 
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $(document).ready(function(){

        $('.test').fSelect();
        
        $('.statehtml').fSelect({
            placeholder: "@lang('web/ministry-details.select')",
            numDisplayed: 3,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
        });

        $('.cityHtml').fSelect({
            placeholder: "@lang('web/ministry-details.select')",
            numDisplayed: 3,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
        });
    });


    $('.test').fSelect({
        placeholder: "-- @lang('web/ministry-details.select') -- ",
        numDisplayed: 5,
        overflowText: '{n} selected',
        noResultsText: 'No results found',
        searchText: 'Search',
        showSearch: true
    });

    
    $('.statehtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherStateDiv").css('display','block');
            $("#ContactStateName").attr('required',true);
        }else{
            $("#OtherStateDiv").css('display','none');
            $("#ContactStateName").attr('required',false);
        }
    }); 
    $('.cityHtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherCityDiv").css('display','block');
            $("#ContactCityName").attr('required',true);
        }else{
            $("#OtherCityDiv").css('display','none');
            $("#ContactCityName").attr('required',false);
        }
    }); 

</script>
@endpush