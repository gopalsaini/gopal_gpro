
@extends('layouts/app')

@section('title',__(Lang::get('web/ministry-details.ministry')))

@push('custom_css')
    <style>
        .fs-dropdown {
            width: 25.3%;
        }

        .fs-label-wrap{
            height: 50.5px;
            background: #F9F9F9;
            border:0px;
        }

        .fs-label-wrap .fs-label{
            padding: 13px 22px 6px 8px;
        }
        .login-modal.minister-modal .modal .modal-dialog .modal-content .modal-body .input-box ul {
           
            column-gap: 12px !important;
        }
    </style>
@endpush

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form-wrap">
                <ul>
                    <li>
                        <a href="{{url('profile-update')}}" class="active">
                           
                            <span>01</span>@lang('web/profile-details.personal-details-combined')
                           
                        </a>
                    </li>
                    <li>
                        <a href="{{url('contact-details')}}" class="active">
                           
                            <span>02</span>@lang('web/contact-details.contact-details-combined')
                           
                        </a>
                    </li>
                    <li>
                        <a href="{{url('ministry-details')}}">
                            
                            <span>03</span>@lang('web/ministry-details.ministry-details-combined')
                           
                        </a>
                    </li>
                </ul>
            </div>
            <div class="step-form">
                
                <h4 class="inner-head">@lang('web/ministry-details.ministry-details-combined')</h4>
              
                <form id="formSubmit" action="{{ route('ministry-details') }}" class="row" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" class="active-input mt-2" value="preview">
                    <div class="col-lg-12">
                        <label for="">@lang('web/ministry-details.ministry-name') </label>
                        <input type="text" autocomplete="text" name="ministry_name" onkeypress="return /[a-z A-Z ]/i.test(event.key)" placeholder="@lang('web/ministry-details.enter') @lang('web/ministry-details.ministry-name')" class="active-input mt-2" value="{{$resultData['result']['ministry_name']}}">
                        
                    </div>
                    <div class="col-lg-6">
                        <label for="">@lang('web/ministry-details.postal-address') <span>*</span></label>
                        <input type="text" autocomplete="text" name="ministry_address" placeholder="@lang('web/ministry-details.enter') @lang('web/ministry-details.address')" class="mt-2" required value="{{$resultData['result']['ministry_address']}}">
                    </div>
                    <div class="col-lg-6">
                        <label for="">@lang('web/ministry-details.zip-postal-code')</label>
                         <input type="text" autocomplete="text" name="ministry_zip_code"  placeholder="@lang('web/ministry-details.enter') @lang('web/ministry-details.zip-postal-code')" class="mt-2" value="{{$resultData['result']['ministry_zip_code']}}">
                       
                    </div>
                    <div class="col-lg-4">
                        <label for="country">@lang('web/ministry-details.country') <span>*</span></label>
                        <div class="common-select">
                           <select id="country" placeholder="@lang('web/ministry-details.select')" data-state_id="{{$resultData['result']['ministry_state_id']}}" data-city_id="{{$resultData['result']['ministry_city_id']}}" class="mt-2 country selectbox test" name="ministry_country_id">
                            
                                <option value="">@lang('web/app.select_code')</option>
                                @foreach($country as $con)
                                <option @if($resultData['result']['ministry_country_id']==$con['id']){{'selected'}}@endif value="{{ $con['id'] }}">{{ ucfirst($con['name']) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="alaska">@lang('web/ministry-details.state')/@lang('web/ministry-details.province') <span>*</span></label>
                        <div class="common-select">
                            <select id="alaska" autocomplete="off" placeholder="@lang('web/ministry-details.select')" class="mt-2 test statehtml selectbox" name="ministry_state_id">
                                                         
                            </select>
                        </div>
                        <div style="display: @if($resultData['result']['ministry_state_id'] == 0) block @else none @endif" id="OtherStateDiv">
                            <input type="text" autocomplete="off" placeholder="@lang('web/ministry-details.enter') @lang('web/contact-details.state')/@lang('web/contact-details.province')" name="ministry_state_name" id="ministryStateName" class="mt-2" value="{{$resultData['result']['ministry_state_name']}}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="Illinois">@lang('web/ministry-details.city') <span>*</span></label>
                        <div class="common-select">
                            <select id="Illinois" autocomplete="off" placeholder="@lang('web/ministry-details.select')" class="mt-2 test cityHtml selectbox" name="ministry_city_id">
                            
                            </select>
                        </div>
                        <div style="display: @if($resultData['result']['ministry_city_id'] == 0) block @else none @endif" id="OtherCityDiv">
                            <input type="text" autocomplete="off" placeholder="@lang('web/ministry-details.enter') @lang('web/ministry-details.city')" name="ministry_city_name" id="ministryCityName" class="mt-2" value="{{$resultData['result']['ministry_city_name']}}">
                        </div>

                    </div>
                    <div class="col-lg-12">
                        <label>@lang('web/ministry-details.pastor-trainer') <span>*</span></label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                                <input type="radio" name="ministry_pastor_trainer" value="Yes" id="yes" class="form__radio-input" @if($resultData['result']['ministry_pastor_trainer']=='Yes'){{'checked'}}@endif>
                                <label class="form__label-radio" for="yes" class="form__radio-label" data-bs-toggle="modal" href="#exampleModalToggle4" value="Yes">
                                  <span class="form__radio-button"></span> @lang('web/ministry-details.yes')
                                </label>
                            </div>
                            <div class="form__radio-group">
                                <input type="radio" name="ministry_pastor_trainer" value="No" id="no" class="form__radio-input" @if($resultData['result']['ministry_pastor_trainer']=='No'){{'checked'}}@endif>
                                <label class="form__label-radio" for="no" class="form__radio-label" data-bs-toggle="modal" href="#exampleModalToggle5"  >
                                  <span class="form__radio-button"></span> @lang('web/ministry-details.no')
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="formSubmit">@lang('web/ministry-details.preview')</button>
                           
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- banner-end -->

    <!-- minister-popup -->
    <div class="login-modal minister-modal">
        <div class="modal fade" id="exampleModalToggle4" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
            tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h4>@lang('web/ministry-details.involved-in-training')</h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <h5>@lang('web/ministry-details.involved-in-training-desc')</h5>
                        <form id="pastoralLeaderDetail" action="{{ route('pastoral-leaders-detailupdate') }}" class="row" enctype="multipart/form-data">
                            @csrf
                            @php
                                $ministryPastorDetail=json_decode($resultData['result']['ministry_pastor_trainer_detail'],true);
                            @endphp
                            <div class="col-lg-12">
                                <div class="form-check text-center">
                                    <label class="form-check-label">@lang('web/ministry-details.non-formal-pastoral')</label>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox" type="radio" value="@lang('web/ministry-details.practitioner')" required name="non_formal_trainor" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif >
                                                <label for="styled-checkbox" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-2" type="radio" value="@lang('web/ministry-details.facilitator')" required name="non_formal_trainor" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
                                                <label for="styled-checkbox-2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-3" type="radio" value="@lang('web/ministry-details.strategist')" required name="non_formal_trainor" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
                                                <label for="styled-checkbox-3" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-4" type="radio" value="@lang('web/ministry-details.donor')" required name="non_formal_trainor" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
                                                <label for="styled-checkbox-4" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-na1" type="radio" value="N/A" required name="non_formal_trainor" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']=='N/A'){{'checked'}}@endif>
                                                <label for="styled-na1" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check text-center">
                                    <label class="form-check-label">@lang('web/ministry-details.formal-theological-education')</label>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox5" type="radio" value="{{Lang::get('web/ministry-details.practitioner')}}"  required name="formal_theological" @if(isset($ministryPastorDetail['formal_theological']) && $ministryPastorDetail['formal_theological']==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif>
                                                <label for="styled-checkbox5" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-6" type="radio" value="{{Lang::get('web/ministry-details.facilitator')}}"  required name="formal_theological" @if(isset($ministryPastorDetail['formal_theological']) && $ministryPastorDetail['formal_theological']==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
                                                <label for="styled-checkbox-6" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-7" type="radio" value="{{Lang::get('web/ministry-details.strategist')}}"  required name="formal_theological" @if(isset($ministryPastorDetail['formal_theological']) && $ministryPastorDetail['formal_theological']==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
                                                <label for="styled-checkbox-7" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-8" type="radio" value="{{Lang::get('web/ministry-details.donor')}}" required name="formal_theological" @if(isset($ministryPastorDetail['formal_theological']) && $ministryPastorDetail['formal_theological']==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
                                                <label for="styled-checkbox-8" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
                                            </li>
                                            
                                            <li>
                                                <input class="styled-checkbox" id="styled-na2" type="radio" value="N/A" required name="formal_theological" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']=='N/A'){{'checked'}}@endif>
                                                <label for="styled-na2" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check  text-center">
                                    <label class="form-check-label">@lang('web/ministry-details.informal-personal-mentoring')</label>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox9" type="radio" value="{{Lang::get('web/ministry-details.practitioner')}}" name="informal_personal" @if(isset($ministryPastorDetail['informal_personal']) && $ministryPastorDetail['informal_personal']==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif>
                                                <label for="styled-checkbox9" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-10" type="radio" value="{{Lang::get('web/ministry-details.facilitator')}}" name="informal_personal" @if(isset($ministryPastorDetail['informal_personal']) && $ministryPastorDetail['informal_personal']==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
                                                <label for="styled-checkbox-10" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-11" type="radio" value="{{Lang::get('web/ministry-details.strategist')}}" name="informal_personal" @if(isset($ministryPastorDetail['informal_personal']) && $ministryPastorDetail['informal_personal']==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
                                                <label for="styled-checkbox-11" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-12" type="radio" value="{{Lang::get('web/ministry-details.donor')}}" required name="informal_personal" @if(isset($ministryPastorDetail['informal_personal']) && $ministryPastorDetail['informal_personal']==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
                                                <label for="styled-checkbox-12" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
                                            </li>
                                            
                                            <li>
                                                <input class="styled-checkbox" id="styled-na3" type="radio" value="N/A" required name="informal_personal" @if(isset($ministryPastorDetail['non_formal_trainor']) && $ministryPastorDetail['non_formal_trainor']=='N/A'){{'checked'}}@endif>
                                                <label for="styled-na3" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check  text-center">
                                    <label class="form-check-label">@lang('web/ministry-details.willing-to-commit')</label>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li>
                                                <input class="styled-checkbox" id="commit-yes" type="radio" required value="Yes" name="willing_to_commit" @if(isset($ministryPastorDetail['willing_to_commit']) && $ministryPastorDetail['willing_to_commit']=='Yes'){{'checked'}}@endif>
                                                <!-- //Vineet - 080123 -->
                                                <!-- <label for="commit-yes" data-bs-toggle="tooltip" data-bs-placement="top" title="Yes">Yes</label> -->
                                                <label for="commit-yes" data-bs-toggle="tooltip" data-bs-placement="top" title="Yes">@lang('web/ministry-details.yes')</label>
                                                <!-- //Vineet - 080123 -->
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="commit-no" type="radio" required value="No" name="willing_to_commit" @if(isset($ministryPastorDetail['willing_to_commit']) && $ministryPastorDetail['willing_to_commit']=='No'){{'checked'}}@endif>
                                                <!-- //Vineet - 080123 -->
                                                <!-- <label for="commit-no" data-bs-toggle="tooltip" data-bs-placement="top" title="No">No</label> -->
                                                <label for="commit-no" data-bs-toggle="tooltip" data-bs-placement="top" title="No">@lang('web/ministry-details.no')</label>
                                                <!-- //Vineet - 080123 -->
                                            </li>
                                        </ul>
                                        <!-- //Vineet - 080123 -->
                                        <!-- <textarea autocomplete="text" placeholder="Enter Comments" name="comment" required class="mt-2" >@if(isset($ministryPastorDetail['comment'])) {{$ministryPastorDetail['comment']}} @endif</textarea> -->
                                        <textarea autocomplete="text" placeholder="@lang('web/ministry-details.enter-comments')" name="comment" required class="mt-2" >@if(isset($ministryPastorDetail['comment'])) {{$ministryPastorDetail['comment']}} @endif</textarea>
                                        <!-- //Vineet - 080123 -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <h4>@lang('web/ministry-details.involved-in-strengthening')</h4>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li> 
                                                <input class="styled-checkbox" id="styled-checkbox13" type="radio" value="1-10" required name="howmany_pastoral" @if(isset($ministryPastorDetail['howmany_pastoral']) && $ministryPastorDetail['howmany_pastoral']=='1-10'){{'checked'}}@endif>
                                                <label for="styled-checkbox13">1-10</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-14" type="radio" value="10-100" required name="howmany_pastoral" @if(isset($ministryPastorDetail['howmany_pastoral']) && $ministryPastorDetail['howmany_pastoral']=='10-100'){{'checked'}}@endif> 
                                                <label for="styled-checkbox-14">10-100</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-15" type="radio" value="100+" required name="howmany_pastoral" @if(isset($ministryPastorDetail['howmany_pastoral']) && $ministryPastorDetail['howmany_pastoral']=='100+'){{'checked'}}@endif>
                                                <label for="styled-checkbox-15">100+</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <h4>@lang('web/ministry-details.future-pastor-trainers')</h4>
                                    <div class="input-box">
                                        <ul class="unstyled centered" style="justify-content: space-evenly;">
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox16" type="radio" value="1-10" required name="howmany_futurepastor" @if(isset($ministryPastorDetail['howmany_futurepastor']) && $ministryPastorDetail['howmany_futurepastor']=='1-10'){{'checked'}}@endif>
                                                <label for="styled-checkbox16">1-10</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-17" type="radio" value="10-100" required name="howmany_futurepastor" @if(isset($ministryPastorDetail['howmany_futurepastor']) && $ministryPastorDetail['howmany_futurepastor']=='10-100'){{'checked'}}@endif>
                                                <label for="styled-checkbox-17">10-100</label>
                                            </li>
                                            <li>
                                                <input class="styled-checkbox" id="styled-checkbox-18" type="radio" value="100+" required name="howmany_futurepastor" @if(isset($ministryPastorDetail['howmany_futurepastor']) && $ministryPastorDetail['howmany_futurepastor']=='100+'){{'checked'}}@endif>
                                                <label for="styled-checkbox-18">100+</label>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="ok-btn">
                                    <button type="submit" class="main-btn bg-gray-btn" form="pastoralLeaderDetail">@lang('web/ministry-details.ok')</button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModalToggle5" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0 text-center">
                        <h4>@lang('web/ministry-details.pastoral-training-to-your-ministries')</h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <form id="pastoralLeaderDetailNo" action="{{ route('pastoral-leaders-detailupdate') }}" class="row p-0" enctype="multipart/form-data">
                        @csrf
                            <div class="col-lg-12">
                                <div class="group-main">
                                    <input type="radio" style="display:none" id="pastorno_yes" required value="Yes" name="pastorno" @if($resultData['result']['doyouseek_postoral']=='Yes'){{'checked'}}@endif/>
                                    <label style="cursor:pointer" class="main-btn bg-gray-btn yes-btn active-btn @if($resultData['result']['doyouseek_postoral']=='Yes'){{'acive-btn'}}@endif" for="pastorno_yes">@lang('web/ministry-details.yes')</label>
                                    <input type="radio" style="display:none" checked id="pastorno_no" required value="No"  name="pastorno" @if($resultData['result']['doyouseek_postoral']=='No'){{'checked'}}@endif/>
                                    <label style="cursor:pointer" class="main-btn bg-gray-btn no-btn @if($resultData['result']['doyouseek_postoral']=='No'){{'acive-btn'}}@endif" for="pastorno_no">@lang('web/ministry-details.no')</label>
                                </div>
                            </div>
                            
                            <div>
                                <div class="col-lg-12">

                                    <label for="" style="display: @if($resultData['result']['doyouseek_postoral']=='Yes') block @else none @endif" id="envision_training_div">@lang('web/ministry-details.envision-training-pastors') </label>
                                    <label for="" style="display: @if($resultData['result']['doyouseek_postoral']=='Yes') none @else block @endif" id="Comment_Div">@lang('web/ministry-details.comment') </label>

                                    <textarea placeholder="Write Here" class="mt-2" id="pastoryes_comment" name="comment">{{$resultData['result']['doyouseek_postoralcomment']}}</textarea>
                                </div> 
                            </div>
                            <div class="col-lg-12">
                                <div class="step-next register-submit">
                                    <button type="submit" class="main-btn bg-gray-btn" form="pastoralLeaderDetailNo">@lang('web/ministry-details.submit')</button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection


@push('custom_js')
<script src="{{asset('js/intlTelInput.js')}}"></script>

<script>



    var input = document.querySelector("#phone");
    var errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    window.addEventListener("load", function () {
        errorMsg = document.querySelector("#error-msg"),
            validMsg = document.querySelector("#valid-msg");
        var iti = window.intlTelInput(input, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js"
        });
        window.intlTelInput(input, {
            geoIpLookup: function (callback) {
                $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            },
            initialCountry: "auto",
            placeholderNumberType: "MOBILE",
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js",
        });
        $(validMsg).addClass("hide");
        input.addEventListener('blur', function () {
            reset();
            if (input.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                } else {
                    input.classList.add("error");
                    var errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
        });
        input.addEventListener('change', reset);
        input.addEventListener('keyup', reset);
    });
    var reset = function () {
        input.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("hide");
        validMsg.classList.add("hide");
    };
    $(document).ready(function () {
        $("#phone").val("+917773859");
    });
</script>

<script>
    var input1 = document.querySelector("#home");
    var errorMap1 = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    window.addEventListener("load", function () {
        errorMsg = document.querySelector("#error-msg"),
            validMsg = document.querySelector("#valid-msg");
        var iti = window.intlTelInput(input1, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js"
        });
        window.intlTelInput(input, {
            geoIpLookup: function (callback) {
                $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            },
            initialCountry: "auto",
            placeholderNumberType: "MOBILE",
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js",
        });
        $(validMsg).addClass("hide");
        input1.addEventListener('blur', function () {
            reset();
            if (input1.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                } else {
                    input1.classList.add("error");
                    var errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap1[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
        });
        input1.addEventListener('change', reset);
        input1.addEventListener('keyup', reset);
    });
    var reset = function () {
        input1.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("hide");
        validMsg.classList.add("hide");
    };
    $(document).ready(function () {
        $("#home").val("+917773859");
    });
</script>

<script>
    var input2 = document.querySelector("#whatsapp");
    var errorMap2 = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    window.addEventListener("load", function () {
        errorMsg = document.querySelector("#error-msg"),
            validMsg = document.querySelector("#valid-msg");
        var iti = window.intlTelInput(input2, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js"
        });
        window.intlTelInput(input, {
            geoIpLookup: function (callback) {
                $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            },
            initialCountry: "auto",
            placeholderNumberType: "MOBILE",
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@16.0.2/build/js/utils.js",
        });
        $(validMsg).addClass("hide");
        input2.addEventListener('blur', function () {
            reset();
            if (input1.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                } else {
                    input1.classList.add("error");
                    var errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap2[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
        });
        input2.addEventListener('change', reset);
        input2.addEventListener('keyup', reset);
    });
    var reset = function () {
        input2.classList.remove("error");
        errorMsg.innerHTML = "";
        errorMsg.classList.add("hide");
        validMsg.classList.add("hide");
    };
    $(document).ready(function () {
        $("#whatsapp").val("+917773859");
    });
</script>

<script>
$(document).ready(function () {
    $(".yes-btn").click(function () {
        $(this).addClass("acive-btn");
        $(".no-btn").removeClass("acive-btn");
    });
});
$(document).ready(function () {
    $(".no-btn").click(function () {
        $(this).addClass("acive-btn");
        $(".yes-btn").removeClass("acive-btn");
    });
});
</script>
<!-- table-show -->


<script>

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
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                location.href = "{{ route('profile') }}";
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $("form#pastoralLeaderDetail").submit(function(e) {
        e.preventDefault();
 
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();
  
        let formData=new FormData(this);
        formData.append('ministry_pastor_trainer','Yes');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
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
                $('#exampleModalToggle4').modal('toggle');
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $("form#pastoralLeaderDetailNo").submit(function(e) {
        e.preventDefault();
 
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();
  
        let formData=new FormData(this);
        formData.append('ministry_pastor_trainer','No');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
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
                $('#exampleModalToggle5').modal('toggle');
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
        $('.statehtml').fSelect();
        $('.cityHtml').fSelect(); 
    });

    
    $('.statehtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherStateDiv").css('display','block');
            $("#ministryStateName").attr('required',true);
        }else{
            $("#OtherStateDiv").css('display','none');
            $("#ministryStateName").attr('required',false);
        }
    }); 
    $('.cityHtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherCityDiv").css('display','block');
            $("#ministryCityName").attr('required',true);
        }else{
            $("#OtherCityDiv").css('display','none');
            $("#ministryCityName").attr('required',false);
        }
    }); 

    
    $(document).ready(function () {
        $('input:radio[name=pastorno]').change(function () {
          
            if ($("input[name='pastorno']:checked").val() == 'Yes') {

                $('#envision_training_div').show();
                $('#Comment_Div').hide();
            }
            if ($("input[name='pastorno']:checked").val() == 'No') {
                
                $('#envision_training_div').hide();
                $('#Comment_Div').show();
            }
        });
    });


</script>
@endpush