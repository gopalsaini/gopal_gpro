
@extends('layouts/app')

@section('title',__('session'))

@section('content')

@push('custom_css')
<style>
.list-group-item.active {
        z-index: 2;
        color: #fff;
        background-color: #ffcd34;
        border-color: #ffcd34;
    }
    </style>
@endpush
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>

                
            <div >
                <form id="formSubmit" action="{{ url('session-information-submit') }}" class="row" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="step-form">
                        @if(!empty($sessions) && count($sessions)>0)
                            <div class="detail-price-wrap">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="list-group flex-row text-center" id="list-tab" role="tablist">
                                            @foreach($sessions as $key=>$session)
                                            <a class="list-group-item list-group-item-action {{$key == 0 ? 'active' : '' }}" id="list-home-list{{$key+1}}" data-bs-toggle="list" href="#list-home{{$key+1}}" role="tab" aria-controls="list-home">@lang('web/home.session') {{$key+1}}</a>
                                            @endforeach

                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="tab-content" id="nav-tabContent">
                                            @foreach($sessions as $key=>$session)
                                            <div class="tab-pane fade show {{$key == 0 ? 'active' : '' }}" id="list-home{{$key+1}}" role="tabpanel" aria-labelledby="list-home-list{{$key+1}}">
                                                <ul>
                                                    <li>
                                                        <label for="">@lang('web/home.name'): {{$session->name}}, @lang('web/home.session') : {{$session->session_name}},  @lang('web/app.Date') : {{$session->date}}</label>
                                                    </li>
                                                    <li>
                                                        <label for="">@lang('web/home.start-time') : {{$session->start_time}}, End time {{$session->end_time}} </label>
                                                    </li>
                                                    <li>
                                                        <div class="radio-wrap">
                                                            <input type="hidden" name="session_id[]" value="{{$session->id}}" class="form__radio-input" >
                                                            <input type="hidden" name="session_date[]" value="{{$session->date}}" class="form__radio-input" >
                                                            <div class="form__radio-group">
                                                                <input type="checkbox" name="session[]" id="yes{{$session->id}}" value="Yes" class="form__radio-input" >
                                                                <label class="form__label-radio" for="yes{{$session->id}}">
                                                                <span class="form__radio-button"></span> @lang('web/ministry-details.yes')
                                                                </label>
                                                            </div>
                                                            <div class="form__radio-group">
                                                                <input type="checkbox" name="session[]" id="no{{$session->id}}" value="No" class="form__radio-input" >
                                                                <label class="form__label-radio" for="no{{$session->id}}">
                                                                <span class="form__radio-button"></span> @lang('web/ministry-details.no')
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($userSessionInfo) && isset($userSessionInfo[0]) && $userSessionInfo[0]->submit_status == '0')
                            <div style="display:flex">
                                <div class="col-lg-3">
                                    <div class="step-next">
                                        <button type="submit" class="main-btn bg-gray-btn" form="formSubmit"> @lang('web/home.preview')</button>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="step-next">
                                        <a href="{{ url('session-information-final-submit') }}" class="main-btn bg-gray-btn">@lang('web/home.final-submit')</a>
                                    </div>
                                </div>
                            </div>
                            
                        @else

                        
                            <div class="col-lg-12">
                                <div class="step-next">
                                    <button type="submit" class="main-btn bg-gray-btn" form="formSubmit"> @lang('web/home.preview')</button>
                                </div>
                            </div>
                            

                        @endif
                    </div>
                </form>
            </div>
           
        </div>
    </div>
    <!-- banner-end -->

@endsection


@push('custom_js')

<script>

    $('#TravelInfoEdit').click(function(){
        $("#TravelInfoEditDiv").toggle();
    });

    $('#PartialPaymentOffline').click(function(){
       
        $("#PartialPaymentOfflineDiv").toggle();
        $("#PartialPaymentOnlineDiv").css('display','none');
    });

    $('#FullPaymentOffline').click(function(){
        
        $("#FullPaymentOfflineDiv").toggle();
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
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                $('#formSubmit')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                setTimeout(() => {
                    window.location.href = "{{url('session-information')}}";
                }, 2000);
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

</script>
@endpush