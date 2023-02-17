
	
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
				
				<div class="col-lg-12 PastorTrainer"><br>
					<label>@lang('web/ministry-details.pastor-trainer') <span>*</span></label><br>
					<div class="radio-wrap">
						<div class="form__radio-group">
							<input type="radio" name="ministry_pastor_trainer" value="Yes" id="yes" class="form__radio-input " @if($result['ministry_pastor_trainer']=='Yes'){{'checked'}}@endif >
							<label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
							<span class="form__radio-button"></span> @lang('web/ministry-details.yes')
							</label>

							<input type="radio" name="ministry_pastor_trainer" value="No" id="no" class="form__radio-input " @if($result['ministry_pastor_trainer']=='No'){{'checked'}}@endif >
							<label class="form__label-radio" for="no" class="form__radio-label"  >
							<span class="form__radio-button"></span> @lang('web/ministry-details.no')
							</label>
						</div>
					</div>
				</div>		

				<div class="col-lg-12 AddFelidPastorTrainer_Yes HidePastorTrainer" style="display: @if($result['ministry_pastor_trainer']=='Yes'){{'block'}} @else none @endif">
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

				<div class="col-lg-12 AddFelidPastorTrainer_No HidePastorTrainer" style="display: @if($result['ministry_pastor_trainer']=='No'){{'block'}} @else none @endif">
					<div class="col-lg-12">
						<br><br>
						<label for="styled-checkbox-18">Do you seek to add Pastoral Training to your ministries?</label>
						<div class="group-main">
							<input type="radio" id="pastorno_yes"  value="Yes" name="pastorno" @if($result['doyouseek_postoral']=='Yes'){{'checked'}}@endif >
							<label style="cursor:pointer" class="main-btn bg-gray-btn yes-btn active-btn @if($result['doyouseek_postoral']=='Yes'){{'acive-btn'}}@endif" for="pastorno_yes">@lang('web/ministry-details.yes')</label>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="radio" id="pastorno_no" class=" " value="No"  name="pastorno" @if($result['doyouseek_postoral']=='No'){{'checked'}}@endif >
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
		@if(!$result)
			$(".HidePastorTrainer").hide();
		@endif
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
</script>
