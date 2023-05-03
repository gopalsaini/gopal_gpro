
@extends('exhibitors/layouts/app')

@section('title',__(Lang::get('web/app.myprofile')))

@section('content')

    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =$resultData['result']['id']; 
                
                $passportInfo=\App\Models\Exhibitors::where('user_id',$userId)->first(); @endphp
                @include('exhibitors.sidebar', compact('userId'))
            </div>
            <div class="step-form">
                  
                <h4 class="inner-head">Sponsorship Letter Information</h4>
                <!-- //Vineet - 080123 -->
                    <br> <br> <br>
                        <b>Spanish : </b>
                        
                        <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries-and-Visas-sp.pdf') }}" >clic aquí</a></p>
                        <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/immigrationletterSPanish.pdf') }}" >clic aquí</a></p>
                        <br>
                        <b>English : </b>
                        <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries-and-Visas-en.pdf') }}" >Click Here</a></p>
                        <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/IMMIGRATION_REPUBLIC_OF_PANAMA_English.pdf') }}" >Click Here</a></p>
                    

                    <h4 class="inner-head section-gap">Passport Info</h4>
                    <div class="detail-wrap">
                        <ul>
                            <li>
                                <p>Given Name</p>
                                <span>:&nbsp; &nbsp; &nbsp; 
                                {{$passportInfo['name']}} 
                                
                            </li>
                            <li>
                                <p>Surname</p>
                                <span>:&nbsp; &nbsp; &nbsp; 
                                
                                {{$passportInfo['last_name']}}</span>
                            </li>
                            
                            <li>
                                <p>@lang('web/profile.dob')</p>
                                <span>:&nbsp; &nbsp; &nbsp; @if($passportInfo['dob']!=''){{ date('d-m-Y',strtotime($passportInfo['dob'])) }}@endif</span>
                            </li>
                            <li>
                                <p>Passport No</p>
                                <span>:&nbsp; &nbsp; &nbsp; {{$passportInfo['passport_number']}} </span>
                            </li>
                            <li>
                                <p>Passport Copy</p>
                                <span>:&nbsp; &nbsp; &nbsp; 
                                   
                                <a target="_blank" href="{{asset('uploads/passport/'.$passportInfo['passport_copy'])}}">View</a>
                                    
                                </span>
                            </li>
                            <li>
                                <p>Is this a diplomatic passport? : {{$passportInfo['diplomatic_passport']}}</p>
                            </li>
                        </ul>
                        <ul>
                            <li>
                                <p>@lang('web/profile.citizenship')</p>
                                <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['citizenship'])}}</span>
                            </li>
                            
                        </ul>
                    </div>
                        
                    
                        <h4 class="inner-head section-gap">Sponsorship Info</h4>
                    
                        <div class="detail-wrap">
                            
                                <div class="row">
                                    
                                    <div class="col-md-12" >
                                        @if($passportInfo['financial_letter'])
                                            <div class="row">
                                                <div class="alphabet-vd-box">
                                                    <h4>Financial Letter</h4><br>
                                                    @php $financialLetter = explode(',',$passportInfo['financial_letter']);@endphp
                                                    <div class="step-next" style="display: flex;">
                                                        <a style="margin-right: 50px;" href="{{asset('uploads/file/'.$financialLetter[0])}}" target="_blank" class="main-btn">File 1</a>
                                                        <a style="" href="{{asset('uploads/file/'.$financialLetter[1])}}" target="_blank" class="main-btn">File 2</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <br><br>
                                        @endif

                                        @if($passportInfo['sponsorship_letter'])
                                            <div class="row">
                                            <h4>Sponsorship Letter</h4><br><br>
                                                <div class="step-next">
                                                    <a style="" href="{{asset('uploads/file/'.$passportInfo['sponsorship_letter'])}}" target="_blank" class="main-btn">File</a>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                        
                        </div>
                    
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- banner-end -->

    
@endsection


@push('custom_js')


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
                $('#registrationCompletedModal').modal('toggle');
                setTimeout(() => {

                    location.href = "{{ route('profile') }}";

                }, 3000);

                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    

    </script>

@endpush