
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

                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="input">@lang('admin.dob'):</label>
                            <input type="date" value="{{$resultData['result']['dob']}}" placeholder="DD/ MM/ YYYY" class="active-input mt-2" required name="dob" >
                        </div>
                    </div>
                   
                    <div class="col-lg-4">
                        <label for="citizen">@lang('web/profile-details.citizenship') <span>*</span></label>
                        <div class="common-select">
                            <select id="citizen" class="mt-2 test" name="citizenship" >
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($citizenship as $con)
                                    
                                    <option @if($resultData['result']['citizenship'] >0 && $resultData['result']['citizenship']==$con['country_id']){{'selected'}}@endif value="{{$con['country_id']}}">{{$con['country_name']}} </option>
                                   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <label for="country">Country Currently Staying <span>*</span></label>
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
                        <div class="form-check">
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

</script>


@endpush