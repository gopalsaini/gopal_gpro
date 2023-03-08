
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
                <form id="Passport" action="{{ route('passport.info') }}" class="row" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-6">
                        <label for="">@lang('web/help.name')<span>*</span></label>
                        <input type="text" name="name" placeholder="@lang('web/app.enter') @lang('web/help.name')" value="{{$resultData['result']['name']}}" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Passport Number<span>*</span></label>
                        <input type="text" name="passport_no" placeholder="Enter Passport Number" class="active-input mt-2" required>
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
                        <label for="country">@lang('web/contact-details.country') <span>*</span></label>
                        <div class="common-select">
                            <select id="country" class="mt-2 test"  name="country_id">
                                <option value="">--@lang('web/ministry-details.select')--</option>
                                @foreach($country as $con)
                                    <option  data-phoneCode="{{ $con['phonecode'] }}" class="active-input mt-2" value="{{ $con['id'] }}">{{ ucfirst($con['name']) }} </option>
                                @endforeach
                            </select>
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
                location.href = "{{url('profile')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });

</script>


@endpush