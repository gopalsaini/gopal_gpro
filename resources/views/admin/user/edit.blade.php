@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif User @endsection

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
		.fs-dropdown {
		width: 100%;
	}	

	.list-group-item-action{
		background-color: #ffcd34 !important;
	}

	@media (min-width: 576px){

		.modal-dialog {
			max-width: 51%;
			margin: 1.75rem auto;
		}
	}
    </style>
@endpush
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.user')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
					@if($result)
						<li class="breadcrumb-item" aria-current="page">@lang('admin.edit')</li>
					@else
						<li class="breadcrumb-item" aria-current="page">@lang('admin.add')</li>
					@endif
				</ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.user.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" />

						<div class="detail-price-wrap">
							<div class="row">
								<div class="col-sm-12">
									<div class="list-group flex-row text-center" id="list-tab" role="tablist">
										<a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">Personal Details </a>
										<a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#contact-details" role="tab" aria-controls="list-profile">Contact Details</a>
										@if($result && $result->designation_id != '4' && $result->designation_id != '6')
											<a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#ministry-details" role="tab" aria-controls="list-profile">Ministry Details</a>
										@endif
									</div>
								</div>
								
								<div class="col-sm-12" style="margin-top: 20px;">
									<div class="tab-content" id="nav-tabContent">
										<div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
											<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">@lang('admin.salutation'):</label>
															<select placeholder="Mr."  class="form-control" name="salutation">
																<option value="">--Select--</option>
																<option @if($result && $result->salutation=='Mr.'){{'selected'}}@endif value="Mr.">Mr.</option>
																<option @if($result && $result->salutation=='Ms.'){{'selected'}}@endif value="Ms.">Ms.</option>
																<option @if($result && $result->salutation=='Mrs'){{'selected'}}@endif value="Mrs">Mrs.</option>
																<option @if($result && $result->salutation=='Dr.'){{'selected'}}@endif value="Dr">Dr.</option>
																<option @if($result && $result->salutation=='Pastor'){{'selected'}}@endif value="Pastor">Pastor</option>
																<option @if($result && $result->salutation=='Bishop'){{'selected'}}@endif value="Bishop">Bishop</option>
																<option @if($result && $result->salutation=='Rev.'){{'selected'}}@endif value="Rev.">Rev.</option>
																<option @if($result && $result->salutation=='Prof.'){{'selected'}}@endif value="Prof.">Prof.</option>
															</select>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">@lang('admin.first') @lang('admin.name'):</label>
															<input class="form-control" type="text" name="first_name" placeholder="Enter first name" value="@if($result){{ $result->name }}@endif" >
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">@lang('admin.last') @lang('admin.name'):</label>
															<input class="form-control" type="text" name="last_name" placeholder="Enter last name" value="@if($result){{ $result->last_name }}@endif" >
														</div>
													</div>
													
												</div>

												
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label for="input">@lang('admin.gender'):</label>
															<select id="name" placeholder="- Select -" class="form-control" name="gender" >
																<option value="">- Select -</option>
																<option @if($result && $result->gender=='1'){{'selected'}}@endif value="1">@lang('admin.male')</option>
																<option @if($result && $result->gender=='2'){{'selected'}}@endif value="2">@lang('admin.female')</option>
															</select>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="input">@lang('admin.dob'):</label>
															<input type="date" value="{{$result->dob}}" placeholder="DD/ MM/ YYYY" class="form-control" name="dob" >
														</div>
													</div>
													<div class="col-lg-3">
														<div class="form-check">
															<label class="form-check-label">@lang('web/app.Languages') <b>*</b> </label>
															<div class="input-box">
																<select name="language" id="language" required class="selectLanguage form-control">
																	<option @if($result && $result->language=='en'){{'selected'}}@endif value="en">English</option>
																	<option @if($result && $result->language=='sp'){{'selected'}}@endif value="sp">Spanish</option>
																	<option @if($result && $result->language=='fr'){{'selected'}}@endif value="fr">French</option>
																	<option @if($result && $result->language=='pt'){{'selected'}}@endif value="pt">Portuguese</option>
																</select>
																
															</div>
														</div>
													</div>
													@if($result && ($result->designation_id == '4' || $result->designation_id == '6'))
														<div class="col-lg-3">
															<label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
															<div class="common-select">
																<select id="citizen" class="mt-2 test" name="citizenship" >
																	<option value="">--@lang('web/ministry-details.select')--</option>
																	@foreach($country as $con)
																		
																		<option @if($result['citizenship'] && $result['citizenship']==$con['id']){{'selected'}}@endif value="{{$con['id']}}">{{$con['name']}} </option>
																	
																	@endforeach
																</select>
															</div>
														</div>  
														<div class="col-lg-3">
															<label for="status">@lang('web/profile-details.maritalstatus') <span>*</span></label>
															<div class="common-select">
																<select id="status" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 form-control" name="marital_status" required>
																	<option value="">- @lang('web/ministry-details.select') -</option>
																	<option  @if($result['marital_status']=='Married'){{'selected'}}@endif value="Married">@lang('web/home.Married')</option>
																	<option  @if($result['marital_status']=='Unmarried'){{'selected'}}@endif value="Unmarried">@lang('web/home.Unmarried')</option>
																</select>
															</div>
														</div>
														<div class="col-lg-12 unmarried" style="display: @if($result['marital_status']=='Unmarried'){{'block'}}@else{{'none'}}@endif">
															<label>@lang('web/profile-details.stay-in-twin-or-single') - <span>*</span></label>
															<div class="radio-wrap">
																<div class="form__radio-group">
																	<input type="radio" name="room" value="Single" id="yes" class="form__radio-input" @if($result['room']=='Single'){{'checked'}}@endif>
																	<label class="form__label-radio" for="yes" class="form__radio-label" >
																	<span class="form__radio-button"></span> @lang('web/profile-details.single-room')
																	</label>
																</div>
																<div class="form__radio-group">
																	<input type="radio" name="room" value="Sharing" id="no" class="form__radio-input" @if($result['room']=='Sharing'){{'checked'}}@endif>
																	<label class="form__label-radio" for="no" class="form__radio-label" >
																	<span class="form__radio-button"></span> @lang('web/profile-details.twin')
																	</label>
																</div>
															</div>
														</div>
													@endif
												</div>
											</div>
										<div class="tab-pane fade" id="contact-details" role="tabpanel" aria-labelledby="list-profile-list">
											<div class="col-lg-12 mt-12">
												<div class="form-group">
													<label for="input">Address:</label>
													<input type="text" autocomplete="off" placeholder="Address" class="form-control" required name="contact_address" value="@if($result) {{$result->contact_address}} @endif">
												</div>
											</div>
											<div class="row">
												<div class="col-sm-4">
														<div class="form-group">
															<label for="input">Country:</label>
															<select id="country" placeholder="- @lang('web/app.select_code') -" data-state_id="{{$result->contact_state_id}}" data-city_id="@if($result){{ $result->contact_city_id }}@endif" class="mt-2 country selectbox test" name="contact_country_id">
																<option value="">--select country--</option>
																@foreach($country as $con)
																<option data-phoneCode="{{ $con['phonecode'] }}" @if($result['contact_country_id']==$con['id']){{'selected'}}@endif value="{{ $con['id'] }}">{{ ucfirst($con['name']) }}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-lg-4">
														<label for="alaska">State <span>*</span></label>
														<div class="common-select">
															<select id="alaska" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 statehtml test selectbox" name="contact_state_id">
																
															</select>
														</div>
														
														<div style="display: @if($result['contact_state_id'] && $result['contact_state_id'] == 0) none   @else block @endif " id="OtherStateDiv">
															<input type="text" autocomplete="off" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.state')/@lang('web/contact-details.province')" name="contact_state_name" id="ContactStateName" class="mt-2" value="{{$result['contact_state_name']}}">
														</div>
													</div>
													<div class="col-lg-4">
														<label for="Illinois">City <span>*</span></label>
														<div class="common-select">
															<select id="Illinois" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 cityHtml test selectbox" name="contact_city_id">
																
															</select>
														</div>
														<div style="display:@if($result['contact_city_id'] && $result['contact_city_id'] == 0) block  @else none @endif" id="OtherCityDiv">
															<input type="text" autocomplete="off" placeholder="@lang('web/contact-details.enter') @lang('web/contact-details.city')" name="contact_city_name" class="mt-2" id="ContactCityName" value="{{$result['contact_city_name']}}">
														</div>
													</div>
												</div>

												<div class="row">
													
													<div class="col-sm-6">
														<div class="form-group row">
															<label for="input">@lang('admin.mobile'):</label>
															<div class="col-sm-5">
																<select class="form-control test phoneCode" name="user_mobile_code"> 
																	<option value="" >--@lang('web/app.select_code')--</option>
																	@foreach($country as $con)
																		<option @if($result['phone_code']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
																	@endforeach
																</select>
															</div>
															<div class="col-sm-7">
																<input type="tel" class="form-control" name="mobile" onkeypress="return /[0-9 ]/i.test(event.key)"  value="{{$result->mobile}}" autocomplete="off" placeholder="Enter mobile number">
															</div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group row">
															<label for="input">business Mobile:</label>
															<div class="col-sm-5">
																<select class="form-control test phoneCode" name="contact_business_codenumber"> 
																	<option value="" >--@lang('web/app.select_code')--</option>
																	@foreach($country as $con)
																		<option @if($result['contact_business_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
																	@endforeach
																</select>
															</div>
															<div class="col-sm-7">
																<input type="tel" class="form-control" name="contact_business_number" onkeypress="return /[0-9 ]/i.test(event.key)"  value="{{$result->contact_business_number}}" autocomplete="off" placeholder="Enter business number">
															</div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group row">
															<label for="input">WhatsApp Number:</label>
															<div class="col-sm-5">
																<select class="form-control test phoneCode" name="contact_whatsapp_codenumber"> 
																	<option value="" >--@lang('web/app.select_code')--</option>
																	@foreach($country as $con)
																		<option @if($result['contact_whatsapp_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
																	@endforeach
																</select>
															</div>
															<div class="col-sm-7">
																<input type="tel" class="form-control" name="contact_whatsapp_number" onkeypress="return /[0-9 ]/i.test(event.key)"  value="{{$result->contact_whatsapp_number}}" autocomplete="off" placeholder="Enter whatsapp number">
															</div>
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">Zip Code:</label>
															<input type="tel" class="form-control" name="contact_zip_code" onkeypress="return /[0-9 ]/i.test(event.key)"  value="{{$result->contact_zip_code}}" autocomplete="off" placeholder="Enter contact zip code">
														</div>
													</div>
												</div>
												
												
										</div>
										<div class="tab-pane fade" id="ministry-details" role="tabpanel" aria-labelledby="list-profile-list">
											<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">Ministry Name:</label>
															<input class="form-control" type="text" name="ministry_name" placeholder="Enter ministry name" value="@if($result){{ $result->ministry_name }}@endif" >
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">Ministry Zip Code:</label>
															<input class="form-control" type="tel" name="ministry_zip_code" placeholder="Enter ministry zip code" value="@if($result){{ $result->ministry_zip_code }}@endif" >
														</div>
													</div>
													<div class="col-sm-4">
														<div class="form-group">
															<label for="input">Ministry Address:</label>
															<input type="text" class="form-control" name="ministry_address"   value="{{$result->ministry_address}}" autocomplete="off" placeholder="Enter ministry address">
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-lg-4">
														<label for="country">Ministry Country: <span>*</span></label>
														<div class="common-select">
															<select id="country" placeholder="--select country--" data-state_id="{{$result['ministry_state_id']}}" data-city_id="{{$result['ministry_city_id']}}" class="mt-2 MinistryCountry selectbox test" name="ministry_country_id">
															
																<option value="">--select country--</option>
																@foreach($country as $con)
																<option @if($result['ministry_country_id']==$con['id']){{'selected'}}@endif value="{{ $con['id'] }}">{{ ucfirst($con['name']) }}</option>
																@endforeach
															</select>
														</div>
													</div>
													<div class="col-lg-4">
														<label for="alaska">Ministry State: <span>*</span></label>
														<div class="common-select">
															<select id="alaska" autocomplete="off" placeholder="select state" class="mt-2 test MinistryStatehtml selectbox" name="ministry_state_id">
																						
															</select>
														</div>
														<div style="display: @if($result['ministry_state_id'] && $result['ministry_state_id'] == 0) block  @else none @endif " id="OtherMinistryStateDiv">
															<input type="text" autocomplete="off" placeholder="Enter State" name="ministry_state_name" id="ministryStateName" class="mt-2 field" value="{{$result['ministry_state_name']}}">
														</div>
													</div>
													<div class="col-lg-4">
														<label for="Illinois">City: <span>*</span></label>
														<div class="common-select">
															<select id="Illinois" autocomplete="off" placeholder="select city" class="mt-2 test MinistryCityHtml selectbox" name="ministry_city_id">
															
															</select>
														</div>
														<div style="display: @if($result['ministry_city_id'] && $result['ministry_city_id'] == 0) block  @else none @endif " id="OtherMinistryCityDiv">
															<input type="text" autocomplete="off" placeholder="Enter city" name="ministry_city_name" id="ministryCityName" class="mt-2 field" value="{{$result['ministry_city_name']}}">
														</div>

													</div>	

													<div class="col-lg-12 PastorTrainer">
														<label>@lang('web/ministry-details.pastor-trainer') <span>*</span></label>
														<div class="radio-wrap">
															<div class="form__radio-group">
																<input type="radio" name="ministry_pastor_trainer" value="Yes" id="yes" class="form__radio-input " @if($result['ministry_pastor_trainer']=='Yes'){{'checked'}}@endif disabled>
																<label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
																<span class="form__radio-button"></span> @lang('web/ministry-details.yes')
																</label>

																<input type="radio" name="ministry_pastor_trainer" value="No" id="no" class="form__radio-input " @if($result['ministry_pastor_trainer']=='No'){{'checked'}}@endif disabled>
																<label class="form__label-radio" for="no" class="form__radio-label"  >
																<span class="form__radio-button"></span> @lang('web/ministry-details.no')
																</label>
															</div>
														</div>
													</div>		

													<div class="col-lg-12" style="display: @if($result['ministry_pastor_trainer']=='Yes'){{'block'}} @else none @endif">
													@php $pastorTrainerDetail = json_decode($result['ministry_pastor_trainer_detail']); 
															
													@endphp
														<div class="col-lg-12">
															<div class="form-check ">
																<label class="form-check-label">@lang('web/ministry-details.non-formal-pastoral')</label>
																<div class="input-box">
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox" type="radio" value="@lang('web/ministry-details.practitioner')"  name="non_formal_trainor" @if(isset($pastorTrainerDetail->non_formal_trainor) && $pastorTrainerDetail->non_formal_trainor==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif >
																			<label for="styled-checkbox" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-2" type="radio" value="@lang('web/ministry-details.facilitator')"  name="non_formal_trainor" @if(isset($pastorTrainerDetail->non_formal_trainor) && $pastorTrainerDetail->non_formal_trainor==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
																			<label for="styled-checkbox-2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-3" type="radio" value="@lang('web/ministry-details.strategist')"  name="non_formal_trainor" @if(isset($pastorTrainerDetail->non_formal_trainor) && $pastorTrainerDetail->non_formal_trainor==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
																			<label for="styled-checkbox-3" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-4" type="radio" value="@lang('web/ministry-details.donor')"  name="non_formal_trainor" @if(isset($pastorTrainerDetail->non_formal_trainor) && $pastorTrainerDetail->non_formal_trainor==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
																			<label for="styled-checkbox-4" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-na1" type="radio" value="N/A"  name="non_formal_trainor" @if(isset($pastorTrainerDetail->non_formal_trainor) && $pastorTrainerDetail->non_formal_trainor=='N/A'){{'checked'}}@endif>
																			<label for="styled-na1" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="col-lg-12">
															<div class="form-check ">
																<label class="form-check-label">@lang('web/ministry-details.formal-theological-education')</label>
																<div class="input-box">
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox5" type="radio" value="{{Lang::get('web/ministry-details.practitioner')}}"   name="formal_theological" @if(isset($pastorTrainerDetail->formal_theological) && $pastorTrainerDetail->formal_theological==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif>
																			<label for="styled-checkbox5" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-6" type="radio" value="{{Lang::get('web/ministry-details.facilitator')}}"   name="formal_theological" @if(isset($pastorTrainerDetail->formal_theological) && $pastorTrainerDetail->formal_theological==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
																			<label for="styled-checkbox-6" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-7" type="radio" value="{{Lang::get('web/ministry-details.strategist')}}"   name="formal_theological" @if(isset($pastorTrainerDetail->formal_theological) && $pastorTrainerDetail->formal_theological==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
																			<label for="styled-checkbox-7" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-8" type="radio" value="{{Lang::get('web/ministry-details.donor')}}"  name="formal_theological" @if(isset($pastorTrainerDetail->formal_theological) && $pastorTrainerDetail->formal_theological==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
																			<label for="styled-checkbox-8" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
																		</li>
																		
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-na2" type="radio" value="N/A"  name="formal_theological" @if(isset($pastorTrainerDetail->formal_theological) && $pastorTrainerDetail->formal_theological=='N/A'){{'checked'}}@endif>
																			<label for="styled-na2" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="col-lg-12">
															<div class="form-check ">
																<label class="form-check-label">@lang('web/ministry-details.informal-personal-mentoring')</label>
																<div class="input-box">
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox9" type="radio" value="{{Lang::get('web/ministry-details.practitioner')}}" name="informal_personal" @if(isset($pastorTrainerDetail->informal_personal) && $pastorTrainerDetail->informal_personal==Lang::get('web/ministry-details.practitioner')){{'checked'}}@endif>
																			<label for="styled-checkbox9" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.practitioner-tooltip')">@lang('web/ministry-details.practitioner')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-10" type="radio" value="{{Lang::get('web/ministry-details.facilitator')}}" name="informal_personal" @if(isset($pastorTrainerDetail->informal_personal) && $pastorTrainerDetail->informal_personal==Lang::get('web/ministry-details.facilitator')){{'checked'}}@endif>
																			<label for="styled-checkbox-10" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.facilitator-tooltip')">@lang('web/ministry-details.facilitator')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-11" type="radio" value="{{Lang::get('web/ministry-details.strategist')}}" name="informal_personal" @if(isset($pastorTrainerDetail->informal_personal) && $pastorTrainerDetail->informal_personal==Lang::get('web/ministry-details.strategist')){{'checked'}}@endif>
																			<label for="styled-checkbox-11" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.strategist-tooltip')">@lang('web/ministry-details.strategist')</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-12" type="radio" value="{{Lang::get('web/ministry-details.donor')}}"  name="informal_personal" @if(isset($pastorTrainerDetail->informal_personal) && $pastorTrainerDetail->informal_personal==Lang::get('web/ministry-details.donor')){{'checked'}}@endif>
																			<label for="styled-checkbox-12" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('web/ministry-details.donor-tooltip')">@lang('web/ministry-details.donor')</label>
																		</li>
																		
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-na3" type="radio" value="N/A"  name="informal_personal" @if(isset($pastorTrainerDetail->informal_personal) && $pastorTrainerDetail->informal_personal=='N/A'){{'checked'}}@endif>
																			<label for="styled-na3" data-bs-toggle="tooltip" data-bs-placement="top" title="Not Applicable">N/A </label>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														<div class="col-lg-12">
															<div class="form-check ">
																<label class="form-check-label">@lang('web/ministry-details.willing-to-commit')</label>
																<div class="input-box">
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li>
																			<input class="styled-checkbox field field_Yes" id="commit-yes" type="radio"  value="Yes" name="willing_to_commit" @if(isset($pastorTrainerDetail->willing_to_commit) && $pastorTrainerDetail->willing_to_commit=='Yes'){{'checked'}}@endif>
																			<label for="commit-yes" data-bs-toggle="tooltip" data-bs-placement="top" title="Yes">@lang('web/ministry-details.yes')</label>
																			&nbsp;&nbsp;&nbsp;&nbsp;
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="commit-no" type="radio"  value="No" name="willing_to_commit" @if(isset($pastorTrainerDetail->willing_to_commit) && $pastorTrainerDetail->willing_to_commit=='No'){{'checked'}}@endif>
																			<label for="commit-no" data-bs-toggle="tooltip" data-bs-placement="top" title="No">@lang('web/ministry-details.no')</label>
																			
																		</li>
																	</ul>
																	<textarea autocomplete="text" placeholder="@lang('web/ministry-details.enter-comments')" name="comment"  class="mt-2 field field_Yes form-control" >@if(isset($pastorTrainerDetail->comment)) {{$pastorTrainerDetail->comment}} @endif</textarea>
																	
																</div>
															</div>
														</div>
														<div class="col-lg-12">
															<div class="form-check">
																<h4>@lang('web/ministry-details.involved-in-strengthening')</h4>
																<div class="input-box">
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li> 
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox13" type="radio" value="1-10"  name="howmany_pastoral" @if(isset($pastorTrainerDetail->howmany_pastoral) && $pastorTrainerDetail->howmany_pastoral=='1-10'){{'checked'}}@endif>
																			<label for="styled-checkbox13">1-10</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-14" type="radio" value="10-100"  name="howmany_pastoral" @if(isset($pastorTrainerDetail->howmany_pastoral) && $pastorTrainerDetail->howmany_pastoral=='10-100'){{'checked'}}@endif> 
																			<label for="styled-checkbox-14">10-100</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-15" type="radio" value="100+"  name="howmany_pastoral" @if(isset($pastorTrainerDetail->howmany_pastoral) && $pastorTrainerDetail->howmany_pastoral=='100+'){{'checked'}}@endif>
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
																	<ul class="unstyled centered" style="justify-content: space-between;display: flex;">
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox16" type="radio" value="1-10"  name="howmany_futurepastor" @if(isset($pastorTrainerDetail->howmany_futurepastor) && $pastorTrainerDetail->howmany_futurepastor=='1-10'){{'checked'}}@endif>
																			<label for="styled-checkbox16">1-10</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-17" type="radio" value="10-100"  name="howmany_futurepastor" @if(isset($pastorTrainerDetail->howmany_futurepastor) && $pastorTrainerDetail->howmany_futurepastor=='10-100'){{'checked'}}@endif>
																			<label for="styled-checkbox-17">10-100</label>
																		</li>
																		<li>
																			<input class="styled-checkbox field field_Yes" id="styled-checkbox-18" type="radio" value="100+"  name="howmany_futurepastor" @if(isset($pastorTrainerDetail->howmany_futurepastor) && $pastorTrainerDetail->howmany_futurepastor=='100+'){{'checked'}}@endif>
																			<label for="styled-checkbox-18">100+</label>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
													</div>	

													<div class="col-lg-4 " style="display: @if($result['ministry_pastor_trainer']=='No'){{'block'}} @else none @endif">
														<div class="col-lg-12">
															<br><br>
															<label for="styled-checkbox-18">Do you seek to add Pastoral Training to your ministries?</label>
															<div class="group-main">
																<input type="radio" id="pastorno_yes"  value="Yes" name="pastorno" @if($result['doyouseek_postoral']=='Yes'){{'checked'}}@endif disabled>
																<label style="cursor:pointer" class="main-btn bg-gray-btn yes-btn active-btn @if($result['doyouseek_postoral']=='Yes'){{'acive-btn'}}@endif" for="pastorno_yes">@lang('web/ministry-details.yes')</label>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<input type="radio" id="pastorno_no" class=" " value="No"  name="pastorno" @if($result['doyouseek_postoral']=='No'){{'checked'}}@endif disabled>
																<label style="cursor:pointer" class="main-btn bg-gray-btn no-btn @if($result['doyouseek_postoral']=='No'){{'acive-btn'}}@endif" for="pastorno_no">@lang('web/ministry-details.no')</label>
															</div>
														</div>
													
														<div>
															<div class="col-lg-12">

																<label for="" style="display: @if($result['doyouseek_postoral']=='Yes') block @else none @endif" id="envision_training_div">@lang('web/ministry-details.envision-training-pastors') </label>
																<label for="" style="display: @if($result['doyouseek_postoral']=='Yes') none @else block @endif" id="Comment_Div">@lang('web/ministry-details.comment') </label>

																<textarea placeholder="Write Here"  class="mt-2  form-control"  id="pastoryes_comment" name="doyouseek_postoral_comment">{{$result['doyouseek_postoralcomment']}}</textarea>
															</div> 
														</div>
													</div>		
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

						<div class="col-sm-12">
							<div class="btn-showcase text-center">
								@if(!$result)
								<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
								@endif
								<button class="btn btn-primary" type="submit" form="form">@lang('admin.submit')</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


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
                                    <a href="javascript:;" class="yes-btn btn btn-primary">@lang('web/profile-details.yes')</a>
                                    <a href="javascript:;" class="no-btn btn btn-primary">@lang('web/profile-details.no')</a>
                                </div>
                            </div>
                        </form>
                        <div class="comment-form mt-3" >
							<div id="" style="display:none">
								<label for="">@lang('web/profile-details.spouse-already-registered') </label>
								<div class="radio-wrap">
									<div class="form__radio-group">
										<input type="radio" value="Yes" name="size" id="already_registered_yes" class="form__radio-input">
										<label class="form__label-radio register-yes" for="already_registered_yes" class="form__radio-label">
											<span class="form__radio-button"></span> @lang('web/profile-details.yes')
										</label>
									</div>
									<div class="form__radio-group">
										<input type="radio" value="No"  checked name="size" id="already_registered_no" class="form__radio-input">
										<label class="form__label-radio register-no" for="already_registered_no" class="form__radio-label">
											<span class="form__radio-button"></span> @lang('web/profile-details.no')
										</label>
									</div>
								</div>
							</div>
                            <div  id="AlreadyRegisteredYes" class="divHide"  style="display:none">
                                <form id="alreadyHasSpouse" action="{{ url('admin/user/spouse-update') }}" class="p-0" enctype="multipart/form-data">
                                    <!-- <label for="">@lang('web/profile-details.registered') @lang('web/profile-details.email')</label> -->
                                    <label for="">@lang('web/profile-details.registered-email')</label>
                                    <input type="email" placeholder="@lang('web/app.enter_email')" class="form-control" name="email" autocomplete="off">
                                    <input type="hidden" name="id"  required value="0">
                                    <input type="hidden" name="user_id"  required value="{{$result['id']}}">

                                    <div class="col-lg-12 mt-2">
                                        <div class="step-next register-submit">
                                            <button type="submit" class="btn btn-success" form="alreadyHasSpouse">@lang('web/profile-details.submit')</button> 
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="spouse-form " id="AlreadyRegisteredQuestion">
                                <form id="donttHasSpouse" action="{{ url('admin/user/spouse-update') }}" class="row" enctype="multipart/form-data">
									@csrf
									<input type="hidden" name="user_id"  required value="{{$result['id']}}">
                                    <input type="hidden" name="id"  required value="@if($SpouseDetails && $SpouseDetails->id) {{$SpouseDetails->id}} @else {{'0'}} @endif">
									<label for="name">@lang('web/profile-details.name') <span>*</span></label>
                                    <div class="col-lg-4 mt-2">
                                        <div class="select-option">
                                            
                                            <select id="name" class="form-control" required name="salutation">
                                                <option  value="@lang('web/profile-details.Mr.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Mr.')) selected @endif>@lang('web/profile-details.Mr.')</option>
                                                <option  value="@lang('web/profile-details.Ms.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Ms.')) selected @endif>@lang('web/profile-details.Ms.')</option>
                                                <option  value="@lang('web/profile-details.Mrs.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Mrs.')) selected @endif>@lang('web/profile-details.Mrs.')</option>
                                                <option  value="@lang('web/profile-details.Dr.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Dr.')) selected @endif>@lang('web/profile-details.Dr.')</option>
                                                <option  value="@lang('web/profile-details.Pastor')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Pastor')) selected @endif>@lang('web/profile-details.Pastor')</option>
                                                <option  value="@lang('web/profile-details.Bishop')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Bishop')) selected @endif>@lang('web/profile-details.Bishop')</option>
                                                <option  value="@lang('web/profile-details.Rev.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Rev.')) selected @endif>@lang('web/profile-details.Rev.')</option>
                                                <option  value="@lang('web/profile-details.Prof.')" @if($SpouseDetails && $SpouseDetails->salutation == Lang::get('web/profile-details.Prof.')) selected @endif>@lang('web/profile-details.Prof.')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <input type="text" autocomplete="off" placeholder="@lang('web/app.enter') @lang('web/app.first_name')" class="form-control" required name="first_name" value="@if($SpouseDetails) {{$SpouseDetails->name}} @endif">
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <input type="text" autocomplete="off" placeholder="@lang('web/app.enter') @lang('web/app.last_name')" class="form-control" required name="last_name" value="@if($SpouseDetails) {{$SpouseDetails->last_name}} @endif">
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="">@lang('web/profile-details.email') <span>*</span> </label>
                                        <input type="email" autocomplete="off" placeholder="@lang('web/app.enter_email')" class="form-control" required name="email" value="@if($SpouseDetails) {{$SpouseDetails->email}} @endif">
                                    </div>
                                    <div class="col-lg-4 mt-2">
                                        <label for="">@lang('web/profile-details.gender') <span>*</span></label>
                                        <div class="common-select">
                                            <select id="name" placeholder="- @lang('web/ministry-details.select') -" class="form-control" required name="gender">
                                                <option value="">- @lang('web/ministry-details.select') -</option>
                                                <option value="1" @if($SpouseDetails && $SpouseDetails->gender == '1') selected @endif>@lang('web/profile-details.male')</option>
                                                <option value="2" @if($SpouseDetails && $SpouseDetails->gender == '2') selected @endif>@lang('web/profile-details.female')</option>
                                            </select>
                                        </div>
                                    </div>
									<div class="col-lg-4 mt-2">
										<div class="form-group">		
											<label for="input">Password:</label>
											<input class="form-control" type="text" id="spouse_password" required name="spouse_password" placeholder="Enter Password" value="" >					
										</div>
									</div>
                                    <div class="col-lg-6 mt-2">
                                        <label for="">@lang('web/profile-details.dob') <span>*</span></label>
                                        <input type="date" placeholder="DD/ MM/ YYYY" class="form-control" required name="date_of_birth" value="@if($SpouseDetails){{$SpouseDetails->dob}}@endif">
                                    </div>
                                    <div class="col-lg-6 mt-2">
                                        <label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
                                        <div class="common-select"> 
                                            <select id="citizen" placeholder="- Select -" class="form-control test" name="citizenship">
                                                <option value="">--@lang('web/ministry-details.select')--</option>
                                                @foreach($country as $con)
                                                    <option @if($SpouseDetails && $SpouseDetails->id==$con['id']){{'selected'}}@endif value="{{$con['id']}}">{{$con['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
									<div class="col-lg-12 mt-12">
										<div class="form-group">
											<label for="input">Address:</label>
                                        	<input type="text" autocomplete="off" placeholder="Address" class="form-control" required name="contact_address" value="@if($SpouseDetails) {{$SpouseDetails->contact_address}} @endif">
                                    	</div>
                                    </div>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label for="input">Country:</label>
												<select id="country" placeholder="- @lang('web/app.select_code') -" data-state_id="@if($SpouseDetails) {{$SpouseDetails->contact_state_id}} @endif" data-city_id="@if($SpouseDetails){{ $SpouseDetails->contact_city_id }}@endif" class="mt-2 country selectbox test" name="spouse_contact_country_id">
													<option value="">--select country--</option>
													@foreach($country as $con)
													<option data-phoneCode="{{ $con['phonecode'] }}" @if($SpouseDetails && $SpouseDetails['contact_country_id']==$con['id']){{'selected'}}@endif value="{{ $con['id'] }}">{{ ucfirst($con['name']) }}</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-lg-4">
											<label for="alaska">State <span>*</span></label>
											<div class="common-select">
												<select id="alaska" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 statehtml test selectbox" name="spouse_contact_state_id">
													
												</select>
											</div>
											
										</div>
										<div class="col-lg-4">
											<label for="Illinois">City <span>*</span></label>
											<div class="common-select">
												<select id="Illinois" autocomplete="off" placeholder="- @lang('web/ministry-details.select') -" class="mt-2 cityHtml test selectbox" name="spouse_contact_city_id">
													
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										
										<div class="col-sm-6">
											<div class="form-group row">
												<label for="input">@lang('admin.mobile'):</label>
												<div class="col-sm-5">
													<select class="form-control test phoneCode" name="spouse_user_mobile_code"> 
														<option value="" >--@lang('web/app.select_code')--</option>
														@foreach($country as $con)
															<option @if($SpouseDetails && $SpouseDetails['phone_code']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
														@endforeach
													</select>
												</div>
												<div class="col-sm-7">
													<input type="tel" class="form-control" name="spouse_mobile" onkeypress="return /[0-9 ]/i.test(event.key)"  value="@if($SpouseDetails) {{$SpouseDetails->mobile}} @endif" autocomplete="off" placeholder="Enter mobile number">
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group row">
												<label for="input">business Mobile:</label>
												<div class="col-sm-5">
													<select class="form-control test phoneCode" name="spouse_contact_business_codenumber"> 
														<option value="" >--@lang('web/app.select_code')--</option>
														@foreach($country as $con)
															<option @if($SpouseDetails &&  $SpouseDetails['contact_business_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
														@endforeach
													</select>
												</div>
												<div class="col-sm-7">
													<input type="tel" class="form-control" name="spouse_contact_business_number" onkeypress="return /[0-9 ]/i.test(event.key)"  value="@if($SpouseDetails) {{$SpouseDetails->contact_business_number}} @endif" autocomplete="off" placeholder="Enter business number">
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group row">
												<label for="input">WhatsApp Number:</label>
												<div class="col-sm-5">
													<select class="form-control test phoneCode" name="spouse_contact_whatsapp_codenumber"> 
														<option value="" >--@lang('web/app.select_code')--</option>
														@foreach($country as $con)
															<option @if($SpouseDetails && $SpouseDetails['contact_whatsapp_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
														@endforeach
													</select>
												</div>
												<div class="col-sm-7">
													<input type="tel" class="form-control" name="spouse_contact_whatsapp_number" onkeypress="return /[0-9 ]/i.test(event.key)"  value="@if($SpouseDetails) {{$SpouseDetails->contact_whatsapp_number}} @endif" autocomplete="off" placeholder="Enter whatsapp number">
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label for="input">Zip Code:</label>
												<input type="tel" class="form-control" name="spouse_contact_zip_code" onkeypress="return /[0-9 ]/i.test(event.key)"  value="@if($SpouseDetails) {{$SpouseDetails->contact_zip_code}} @endif" autocomplete="off" placeholder="Enter contact zip code">
											</div>
										</div>
									</div>
                                    <div class="col-lg-12 mt-2">
                                        <div class="step-next register-submit">
                                            <button type="submit" class="btn btn-success" form="donttHasSpouse">@lang('web/profile-details.submit')</button> 
                                        </div>
                                    </div>
                                </form>
                            </div> 
                        </div>
                        <div class="room-form  divHide"  id="ShowRoom" style="display:none">
                            <label for="">@lang('web/profile-details.stay-in-twin-or-single')</label>
                            <form id="updateRoom" action="{{ url('admin/user/room-update') }}" class="p-0" enctype="multipart/form-data">
								@csrf
								<input type="hidden" name="user_id"  required value="{{$result['id']}}">
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
                                <div class="col-lg-12 mt-2">
                                    <div class="step-next register-submit">
                                        <button type="submit" class="btn btn-success" form="updateRoom">@lang('web/profile-details.submit')</button> 
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
@endsection

@push('custom_js')

<script>
	 $(document).ready(function(){
        $('.test').fSelect();
        $('.statehtml').fSelect();
        $('.cityHtml').fSelect(); 
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

	$("select#status").change(function(){ 

		var citizenship = $('#citizen').val();

		if(citizenship == ''){
			
			
			alert('Please select citizenship first');

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
            placeholder: "-- Select -- ",
            numDisplayed: 5,
            overflowText: '{n} selected',
            noResultsText: 'No results found',
            searchText: 'Search',
            showSearch: true
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

<script>  
	$(document).ready(function(){

			$(".HidePastorTrainer").hide();

		$('input[name=ministry_pastor_trainer]').change(function(){
		
			$(".HidePastorTrainer").hide();
			$(".field").attr('required',false);
			
			$(".AddFelidPastorTrainer_"+$(this).val()).show();
			$(".field_"+$(this).val()).attr('required',true);
		});
	});

		
	
	$('.MinistryStatehtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherMinistryStateDiv").css('display','block');
            $("#ministryStateName").attr('required',true);
        }else{
            $("#OtherMinistryStateDiv").css('display','none');
            $("#ministryStateName").attr('required',false);
        }
    }); 
    $('.MinistryCityHtml').on('change', function() {
        if(this.value == '0'){
            $("#OtherMinistryCityDiv").css('display','block');
            $("#ministryCityName").attr('required',true);
        }else{
            $("#OtherMinistryCityDiv").css('display','none');
            $("#ministryCityName").attr('required',false);
        }
    });


	$('.MinistryCountry').change(function() {
		
		MinistryStateId = parseInt($(this).data('state_id'));
		MinistryCityId = parseInt($(this).data('city_id'));
		MinistryCountryId = $(this).val();
		
		$.ajax({
			url: baseUrl + '/get-state?country_id=' + MinistryCountryId,
			dataType: 'json',
			type: 'get',
			error: function(xhr, textStatus) {

				if (xhr && xhr.responseJSON.message) {
					showMsg('error', xhr.responseJSON.message);
				} else {
					showMsg('error', xhr.statusText);
				}
			},
			success: function(data) {
				$('.MinistryStatehtml').fSelect('destroy')
				$('.MinistryStatehtml').html(data.html);

				$('.MinistryStatehtml option').each(function() {
					if (this.value == MinistryStateId)
					$('.MinistryStatehtml').val(MinistryStateId);
						

				});

				$('.MinistryStatehtml').fSelect('create');

			},
			cache: false,
			timeout: 5000
		});

	});


	$(document).ready(getMinistryCity);


	function getMinistryCity() {

		$('.MinistryStatehtml').change(function() {

			
			$.ajax({
				url: baseUrl + '/get-city?state_id=' + $(this).val(),
				dataType: 'json',
				type: 'get',
				error: function(xhr, textStatus) {

					if (xhr && xhr.responseJSON.message) {
						showMsg('error', xhr.responseJSON.message);
					} else {
						showMsg('error', xhr.statusText);
					}
				},
				success: function(data) {

					$('.MinistryCityHtml').fSelect('destroy');
					$('.MinistryCityHtml').html(data.html);

					$('.MinistryCityHtml option').each(function() {
						if (this.value == MinistryCityId)
						$('.MinistryCityHtml').val(MinistryCityId);
							
					});

					$('.MinistryCityHtml').fSelect('create');
				},
				cache: false,
				timeout: 5000
			});
		});

		

	}

	$(document).ready(function(){
		$('.fSelect').fSelect();
		@if($result)
			$(".MinistryCountry").trigger('change'); 
		@endif
	});

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
                    sweetAlertMsg('error', xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) { 
                $('#exampleModalToggle4').modal('toggle');
                submitButton(formId, btnhtml, false);
                sweetAlertMsg('success', data.message);
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
                    sweetAlertMsg('error', xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) { 
                $('#exampleModalToggle4').modal('toggle');
                submitButton(formId, btnhtml, false);
                sweetAlertMsg('success', data.message);
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
                    sweetAlertMsg('error', xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) { 
                $('#exampleModalToggle4').modal('toggle');
                submitButton(formId, btnhtml, false);
                sweetAlertMsg('success', data.message);
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
                    sweetAlertMsg('error', xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                if (data.error == false) {
                    location.href = "{{ route('contact-details') }}";
                }
                submitButton(formId, btnhtml, false);
                sweetAlertMsg('success', data.message);
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $(document).ready(function () {
        $(".yes-btn").click(function () {
            $('.divHide').hide();

            $('#AlreadyRegisteredQuestion').show();
            $("#ShowRoom").hide();
        });
    });

    $(document).ready(function () {
        $(".no-btn").click(function () {
			
			$('.divHide').hide();
            $('#AlreadyRegisteredQuestion').hide();
            $("#ShowRoom").show();
        });
    });

    $(document).ready(function(){

		$("input[type='radio'][name='size']").click(function() {

			if($(this).val() == "Yes"){

				$('.divHide').hide();
				$("#AlreadyRegisteredYes").show();
            	$("#AlreadyRegisteredNo").hide();

            }else{

				$('.divHide').hide();
				$("#AlreadyRegisteredYes").hide();
            	$("#AlreadyRegisteredNo").show();
			}
		});

    });
   
</script>
@endpush
