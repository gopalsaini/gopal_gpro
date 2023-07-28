
@extends('layouts/app')

@section('title',__('Home'))

@push('custom_css')
    <style>
        .fs-dropdown {
            width: 31.4%;
        }

        .fs-label-wrap{
            height: 50.5px;
            background: #F9F9F9;
            border:0px;
        }

        .fs-label-wrap .fs-label{
            padding: 13px 22px 6px 8px;
        }
    </style>
    <style>
        
        .list-group-item.active {
            z-index: 2;
            color: #fff;
            background-color: #ffcd34;
            border-color: #ffcd34;
        }
    </style>
@endpush
@section('content')

     <!-- sidebar-css -->
     <div id="sidebar">
        <div class="respnsv-logo">
            <img src="images/logo.png" alt="logo">
        </div>
        <div id="cssmenu">
            <ul>
                <li><a href="javascript:;">Home</a></li>
                <li><a href="javascript:;">Register</a></li>
                <li><a href="javascript:;">Contact-Us</a></li>
            </ul>
        </div>
    </div>
    <!--header section end-->

    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap">
                <ul>
                    <li>
                        <a href="{{url('profile-update')}}" class="active">
                            <span>01</span>@lang('web/profile-details.personal') @lang('web/profile-details.details')
                        </a>
                    </li>
                    <li>
                        <a href="{{url('contact-details')}}">
                            <span>02</span>@lang('web/profile-details.contact') @lang('web/profile-details.personal')
                        </a>
                    </li>
                    <li>
                        <a href="{{url('ministry-details')}}">
                            <span>03</span>@lang('web/profile-details.ministry') @lang('web/profile-details.details')
                        </a>
                    </li>
                </ul>
            </div>
            <div class="step-form">
                <h4 class="inner-head">@lang('web/profile-details.personal') @lang('web/profile-details.details')</h4>
                <form id="formSubmit" action="{{ route('profile-update') }}" class="row" enctype="multipart/form-data">
                            @csrf
                    <div class="col-lg-2">
                        <div class="select-option">
                            <label for="name">@lang('web/profile-details.name') <span>*</span></label>
                            <select id="name" placeholder="Mr." required class="mt-2" name="salutation">
                                <option value="">--Select--</option>
                                <option @if($resultData['result']['salutation']=='Mr.'){{'selected'}}@endif value="Mr.">Mr.</option>
                                <option @if($resultData['result']['salutation']=='Ms.'){{'selected'}}@endif value="Ms.">Ms.</option>
                                <option @if($resultData['result']['salutation']=='Mrs'){{'selected'}}@endif value="Mrs">Mrs.</option>
                                <option @if($resultData['result']['salutation']=='Dr.'){{'selected'}}@endif value="Dr">Dr.</option>
                                <option @if($resultData['result']['salutation']=='Pastor'){{'selected'}}@endif value="Pastor">Pastor</option>
                                <option @if($resultData['result']['salutation']=='Bishop'){{'selected'}}@endif value="Bishop">Bishop</option>
                                <option @if($resultData['result']['salutation']=='Rev.'){{'selected'}}@endif value="Rev.">Rev.</option>
                                <option @if($resultData['result']['salutation']=='Prof.'){{'selected'}}@endif value="Prof.">Prof.</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <input type="text" name="first_name" onkeypress="return /[a-z A-Z ]/i.test(event.key)" required placeholder="Type your First or Given name" class="mt-2" value="{{$resultData['result']['name']}}" autocomplete="off">
                    </div>
                    <div class="col-lg-5">
                        <input type="text" name="last_name" onkeypress="return /[a-z A-Z ]/i.test(event.key)" required placeholder="Type your Last or Family name" class="mt-2" value="{{$resultData['result']['last_name']}}" autocomplete="off">
                    </div>
                    <div class="col-lg-6">
                        <label for="">@lang('web/profile-details.gender') <span>*</span></label>
                        <div class="common-select">
                            <select id="gender" placeholder="- Select -" class="mt-2" name="gender" required>
                                <option value="">- Select -</option>
                                <option @if($resultData['result']['gender']=='1'){{'selected'}}@endif value="1">@lang('web/profile-details.male')</option>
                                <option @if($resultData['result']['gender']=='2'){{'selected'}}@endif value="2">@lang('web/profile-details.female')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">@lang('web/profile-details.dob') <span>*</span></label>
                        <input type="date" value="{{$resultData['result']['dob']}}" placeholder="DD/ MM/ YYYY" class="mt-2" name="dob" required>
                    </div>
                    <div class="col-lg-6">
                        <label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
                        <div class="common-select">
                            <select id="citizen" class="mt-2 test" name="citizenship" onchange="getPricing(this)">
                                <option value="">--Select--</option>
                                @foreach($country as $con)
                                    
                                    <option @if($resultData['result']['citizenship'] >0 && $resultData['result']['citizenship']==$con['country_id']){{'selected'}}@endif value="{{$con['country_id']}}">{{$con['country_name']}} </option>
                                   
                                @endforeach
                            </select>
                        </div>
                    </div>  
                    @if($resultData['result']['added_as']=='Spouse')
                        <input type="hidden" name="marital_status" value="Married" /> 
                    @else
                        <div class="col-lg-6">
                            <label for="status">@lang('web/profile-details.maritalstatus') <span>*</span></label>
                            <div class="common-select">
                                <select id="status" placeholder="- Select -" class="mt-2" name="marital_status" required>
                                    <option value="">- Select -</option>
                                    <option  @if($resultData['result']['marital_status']=='Married'){{'selected'}}@endif value="Married">Married</option>
                                    <option  @if($resultData['result']['marital_status']=='Unmarried'){{'selected'}}@endif value="Unmarried">Unmarried</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12 unmarried" style="display: @if($resultData['result']['marital_status']=='Unmarried'){{'block'}}@else{{'none'}}@endif">
                            <label>@lang('web/profile-details.stay-in-twin-or-single') - <span>*</span></label>
                            <div class="radio-wrap">
                                <div class="form__radio-group">
                                    <input type="radio" name="room" value="Single" id="yes" class="form__radio-input" @if($resultData['result']['room']=='Single'){{'checked'}}@endif>
                                    <label class="form__label-radio" for="yes" class="form__radio-label" >
                                    <span class="form__radio-button"></span> @lang('web/profile-details.single-room')
                                    </label>
                                </div>
                                <div class="form__radio-group">
                                    <input type="radio" name="room" value="Sharing" id="no" class="form__radio-input" @if($resultData['result']['room']=='Sharing'){{'checked'}}@endif>
                                    <label class="form__label-radio" for="no" class="form__radio-label" >
                                    <span class="form__radio-button"></span> @lang('web/profile-details.twin')
                                    </label>
                                </div>
                            </div>

                                        
                            <div class="detail-price-wrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="list-group flex-row text-center" id="list-tab" role="tablist">
                                            <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">WITH EARLY BIRD</a>
                                            <a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">WITHOUT EARLY BIRD</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                                                <ul>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WEB">0.00</span></span>
                                                    </li>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-room')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WEB">0.00</span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                                <ul>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WOEB">0.00</span></span>
                                                    </li>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-room')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WOEB">0.00</span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="formSubmit">@lang('web/profile-details.next')</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- banner-end -->

    <div class="login-modal minister-modal prsnl-modal">
        <div class="modal fade" id="exampleModalToggle4" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0 text-center">
                        <h4>@lang('web/profile-details.coming-with-spouse')</h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <form class="row p-0">
                            <div class="col-lg-12">
                                <div class="group-main">
                                    <a href="javascript:;" class="main-btn bg-gray-btn yes-btn">@lang('web/profile-details.yes')</a>
                                    <a href="javascript:;" class="main-btn bg-gray-btn no-btn">@lang('web/profile-details.no')</a>
                                </div>
                            </div>
                        </form>
                        <div class="comment-form">
                            <label for="">@lang('web/profile-details.spouse-already-registered') </label>
                            <div class="radio-wrap">
                                <div class="form__radio-group">
                                    <input type="radio" name="size" id="already_registered_yes" class="form__radio-input">
                                    <label class="form__label-radio register-yes" for="already_registered_yes" class="form__radio-label">
                                        <span class="form__radio-button"></span> @lang('web/profile-details.yes')
                                    </label>
                                </div>
                                <div class="form__radio-group">
                                    <input type="radio" name="size" id="already_registered_no" class="form__radio-input">
                                    <label class="form__label-radio register-no" for="already_registered_no" class="form__radio-label">
                                        <span class="form__radio-button"></span> @lang('web/profile-details.no')
                                    </label>
                                </div>
                            </div>
                            <div class="register-none">
                                <form id="alreadyHasSpouse" action="{{ route('spouse-update') }}" class="p-0" enctype="multipart/form-data">
                                    <label for="">@lang('web/profile-details.registered') @lang('web/profile-details.email')</label>
                                    <input type="email" placeholder="Type Email Address" class="mt-2" name="email" autocomplete="off">
                                    <input type="hidden" name="id"  required value="0">

                                    <div class="col-lg-12">
                                        <div class="step-next register-submit">
                                            <button type="submit" class="main-btn bg-gray-btn" form="alreadyHasSpouse">@lang('web/profile-details.submit')</button> 
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="spouse-form">
                                <form id="donttHasSpouse" action="{{ route('spouse-update') }}" class="row" enctype="multipart/form-data">
                                    <input type="hidden" name="id"  required value="@if($SpouseDetails && $SpouseDetails->id) {{$SpouseDetails->id}} @else {{'0'}} @endif">

                                    <div class="col-lg-2">
                                        <div class="select-option">
                                            <label for="name">@lang('web/profile-details.name') <span>*</span></label>
                                            <select id="name" class="mt-2" required name="salutation">
                                                <option  value="Mr." @if($SpouseDetails && $SpouseDetails->salutation == 'Mr.') selected @endif>Mr.</option>
                                                <option  value="Ms." @if($SpouseDetails && $SpouseDetails->salutation == 'Ms.') selected @endif>Ms.</option>
                                                <option  value="Mrs" @if($SpouseDetails && $SpouseDetails->salutation == 'Mrs') selected @endif>Mrs.</option>
                                                <option  value="Dr" @if($SpouseDetails && $SpouseDetails->salutation == 'Dr') selected @endif>Dr.</option>
                                                <option  value="Pastor" @if($SpouseDetails && $SpouseDetails->salutation == 'Pastor') selected @endif>Pastor</option>
                                                <option  value="Bishop" @if($SpouseDetails && $SpouseDetails->salutation == 'Bishop') selected @endif>Bishop</option>
                                                <option  value="Rev." @if($SpouseDetails && $SpouseDetails->salutation == 'Rev.') selected @endif>Rev.</option>
                                                <option  value="Prof." @if($SpouseDetails && $SpouseDetails->salutation == 'Prof.') selected @endif>Prof.</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <input type="text" autocomplete="off" placeholder="Type your First or Given name" class="mt-2" required name="first_name" value="@if($SpouseDetails) {{$SpouseDetails->name}} @endif">
                                    </div>
                                    <div class="col-lg-5">
                                        <input type="text" autocomplete="off" placeholder="Type your Last or Family name" class="mt-2" required name="last_name" value="@if($SpouseDetails) {{$SpouseDetails->last_name}} @endif">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="">@lang('web/profile-details.email') <span>*</span> </label>
                                        <input type="email" autocomplete="off" placeholder="Type your Email Address" class="mt-2" required name="email" value="@if($SpouseDetails) {{$SpouseDetails->email}} @endif">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="">@lang('web/profile-details.gender') <span>*</span></label>
                                        <div class="common-select">
                                            <select id="name" placeholder="- Select -" class="mt-2" required name="gender">
                                                <option value="">- Select -</option>
                                                <option value="1" @if($SpouseDetails && $SpouseDetails->gender == '1') selected @endif>@lang('web/profile-details.male')</option>
                                                <option value="2" @if($SpouseDetails && $SpouseDetails->gender == '2') selected @endif>@lang('web/profile-details.female')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="">@lang('web/profile-details.dob') <span>*</span></label>
                                        <input type="date" placeholder="DD/ MM/ YYYY" class="mt-2" required name="date_of_birth" value="@if($SpouseDetails){{$SpouseDetails->dob}}@endif">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
                                        <div class="common-select"> 
                                            <select id="citizen" placeholder="- Select -" class="mt-2 test" name="citizenship">
                                                <option value="">--Select--</option>
                                                @foreach($country as $con)
                                                    <option @if($SpouseDetails && $SpouseDetails->id==$con['country_id']){{'selected'}}@endif value="{{$con['country_id']}}">{{$con['country_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="step-next register-submit">
                                            <button type="submit" class="main-btn bg-gray-btn" form="donttHasSpouse">@lang('web/profile-details.submit')</button> 
                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                        <div class="room-form">
                            <label for="">@lang('web/profile-details.stay-in-twin-or-single')</label>
                            <form id="updateRoom" action="{{ route('room-update') }}" class="p-0" enctype="multipart/form-data">
                                <div class="radio-wrap">
                                    <div class="form__radio-group">
                                        <input type="radio" name="room" id="single-room" class="form__radio-input" value="Single" required>
                                        <label class="form__label-radio" for="single-room" class="form__radio-label">
                                            <span class="form__radio-button"></span> @lang('web/profile-details.single-room')
                                        </label>
                                    </div>
                                    <div class="form__radio-group">
                                        <input type="radio" name="room" id="twin-share" class="form__radio-input" value="Sharing" required>
                                        <label class="form__label-radio" for="twin-share" class="form__radio-label">
                                            <span class="form__radio-button"></span> @lang('web/profile-details.twin')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="step-next register-submit">
                                        <button type="submit" class="main-btn bg-gray-btn" form="updateRoom">@lang('web/profile-details.submit')</button> 
                                    </div>
                                </div>
                            </form>

                                    
                            <div class="detail-price-wrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="list-group flex-row text-center" id="list-tab" role="tablist">
                                            <a class="list-group-item list-group-item-action active" id="list-home-list1" data-bs-toggle="list" href="#list-home1" role="tab" aria-controls="list-home">@lang('web/pricing.with_early_bird')</a>
                                            <a class="list-group-item list-group-item-action" id="list-profile-list1" data-bs-toggle="list" href="#list-profile1" role="tab" aria-controls="list-profile">@lang('web/pricing.with_out_early_bird')</a>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="list-home1" role="tabpanel" aria-labelledby="list-home-list1">
                                                <ul>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WEB">0.00</span></span>
                                                    </li>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-room')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WEB">0.00</span></span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane fade" id="list-profile1" role="tabpanel" aria-labelledby="list-profile-list1">
                                                <ul>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WOEB">0.00</span></span>
                                                    </li>
                                                    <li>
                                                        <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-room')</p>
                                                        <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WOEB">0.00</span></span>
                                                    </li>
                                                </ul>
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
    </div>



@endsection


@push('custom_js')

<script>

    $("form#alreadyHasSpouse").submit(function(e) {
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        let formData = new FormData(this);
        formData.append('is_spouse', 'Yes')
        formData.append('is_spouse_registered', 'Yes')

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
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
    
    $("form#donttHasSpouse").submit(function(e) {
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        if (fSelectRequired(formId)) {
            return false;
        }

        let formData = new FormData(this);
        formData.append('is_spouse', 'Yes')
        formData.append('is_spouse_registered', 'No')

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
                submitButton(formId, btnhtml, false);
                showMsg('success', data.message);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
    
    $("form#updateRoom").submit(function(e) {
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        let formData = new FormData(this); 

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
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
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
                if (data.error == false) {
                    location.href = "{{ route('contact-details') }}";
                }
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

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

    $(document).ready(function(){
        $(".yes-btn").click(function(){
            $(".comment-form").addClass("comment-show");
            $(".room-form").removeClass("room-show");
        });
    });
    $(document).ready(function(){
        $(".no-btn").click(function(){
            $(".comment-form").removeClass("comment-show");
            $(".room-form").addClass("room-show");
        });
    });
    $(document).ready(function(){
        $(".register-yes").click(function(){
            $(".register-none").addClass("register-show");
            $(".spouse-form").removeClass("spouse-show");
        });
    });
    $(document).ready(function(){
        $(".register-no").click(function(){
            $(".register-none").removeClass("register-show");
            $(".spouse-form").addClass("spouse-show");
        });
    });

    $(document).ready(function(){
        $("select#status").change(function(){ 

            var citizenship = $('#citizen').val();

            if(citizenship == ''){
                
                
                showMsg('error', ' Please select citizenship first');

                $('#status').val('');

            }else{

                var selectedStatus= $(this).children("option:selected").val();
            
                $('.unmarried').find('input').prop('checked', false);

                if(selectedStatus=='Married'){

                    $('#exampleModalToggle4').modal('toggle'); 

                }else{

                    $('#exampleModalToggle4').modal('hide'); 
                }  

                if(selectedStatus=='Unmarried'){

                    $('.unmarried').css('display','block');
                    $('.unmarried').find('input').prop('required',true);

                }else{
                    $('.unmarried').css('display','none');
                    $('.unmarried').find('input').prop('required',false);
                }
            }
            
            
        });

        $('.test').fSelect({
            placeholder: '-- Select -- ',
            numDisplayed: 5,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
        });

    });

</script>

<script>
    function getPricing(select) {
        
        var country = $(select).val();
      
        if (country.length > 0) {

            $('.loader').html('<div class="spinner-border" role="status"></div>');
            $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('....');
            var pricing = @php echo json_encode($country); @endphp;
           
            var filter = pricing.filter( (item) => {
               
                return item.country_id == country ? item : false;
            });
            
            setTimeout(() => {
                
                if (filter.length > 0) {
                   
                    var SingleRoomWEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                  
                    if(SingleRoomWEB < 1075){
                        SingleRoomWEB = 975;
                    }else{
                        SingleRoomWEB = ((parseInt(filter[0].base_price)+parseInt(400))-parseInt(100));
                    }

                    var SingleRoomWOEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                    if(SingleRoomWOEB < 1075){
                        SingleRoomWOEB = 1075;
                    }else{
                        SingleRoomWOEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                    }


                    $('.detail-price-wrap').find('.price').html(filter[0].base_price);
                    $('.detail-price-wrap').find('.twin-sharing-WEB').html((parseInt(filter[0].twin_sharing_per_person_deluxe_room)-parseInt(100)));
                    $('.detail-price-wrap').find('.twin-sharing-WOEB').html(filter[0].twin_sharing_per_person_deluxe_room);
                    $('.detail-price-wrap').find('.single-room-WEB').html(SingleRoomWEB);
                    $('.detail-price-wrap').find('.single-room-WOEB').html(SingleRoomWOEB);
                    $('.detail-price-wrap').find('.single-trainer-WEB').html(((parseInt(filter[0].base_price)+parseInt(1250))-parseInt(100)));
                    $('.detail-price-wrap').find('.single-trainer-WOEB').html(((parseInt(filter[0].base_price)+parseInt(1250))));
                    $('.detail-price-wrap').find('.early-bird').html(filter[0].early_bird_cost);
                    $('.detail-price-wrap').find('.trainers-early').html(filter[0].both_are_trainers_deluxe_room_early_bird);
                    $('.detail-price-wrap').find('.trainers-after-WOEB').html(filter[0].both_are_trainers_deluxe_room_after_early_bird);
                } else {
                    $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('0.00');
                }
                $('.loader').html('');
            }, 300);
        } else {
            $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('0.00');
        }
    }
</script>

@endpush
