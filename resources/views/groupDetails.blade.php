
@extends('layouts/app')

@section('title',__(Lang::get('web/payment.group').Lang::get('web/contact-details.details')))

@push('custom_css')
    <style>
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

     <!-- sidebar-css -->
     <div id="sidebar">
        <div class="respnsv-logo">
            <img src="images/logo.png" alt="logo">
        </div>
        <div id="cssmenu">
            <ul>
                <li><a href="javascript:;">@lang('web/app.home')</a></li>
                <li><a href="javascript:;">@lang('web/app.register')</a></li>
                <li><a href="javascript:;">@lang('web/app.contactus')</a></li>
            </ul>
        </div>
    </div>
    <!--header section end-->

    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="text-center">@lang('web/group-details.title')</h4>
                <form id="formSubmit" action="{{ route('groupinfo-update') }}" class="row" enctype="multipart/form-data">
                    <div class="group-main">
                        <label  class="main-btn bg-gray-btn yes-btn" for="isgrpup_yes" style="cursor:pointer">@lang('web/group-details.yes')</label>
                        <input type="radio" name="is_group" value="Yes" id="isgrpup_yes" style="display:none"/>
                        <label class="main-btn bg-gray-btn no-btn" for="isgrpup_no"  style="cursor:pointer">@lang('web/group-details.no')</a>
                        <input type="radio" name="is_group" value="No" id="isgrpup_no" style="display:none"/>
                    </div>
                    <div class="yes-table"> 
                        <div class="group-register-box">
                            <div class="fieldsGroup">
                                <div class="row tbContainer">
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.name')</label>
                                        <input type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.name')" class="mt-2 requiredField" name="user_name" value="{{$resultData['result']['name']}}">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.email')</label>
                                        <input type="text" placeholder="@lang('web/app.enter_email')" class="mt-2 requiredField" name="user_email" value="{{$resultData['result']['email']}}">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.whatsapp')</label>
                                        <select class="form-control test" name="user_whatsup_code">
                                            <option value="" >--@lang('web/app.select_code')--</option>
                                            @foreach($country as $con)
                                                <option @if($resultData['result']['contact_whatsapp_codenumber']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                                            @endforeach
                                        </select>
                                        <input style="margin-left: -4px;width: 70%;" type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.whatsapp')" class="mt-2 requiredField"  onkeypress="return /[0-9 ]/i.test(event.key)" name="contact_whatsapp_number" value="{{$resultData['result']['contact_whatsapp_number']}}">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.mobile')</label>
                                        <select class="form-control test" name="user_mobile_code"> 
                                            <option value="" >--@lang('web/app.select_code')--</option>
                                            @foreach($country as $con)
                                                <option @if($resultData['result']['phone_code']==$con['phonecode']){{'selected'}}@endif value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                                            @endforeach
                                        </select>
                                        <input style="margin-left:-4px;width: 70%;" type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.mobile')" class="mt-2 requiredField" onkeypress="return /[0-9 ]/i.test(event.key)" name="contact_business_number" value="{{$resultData['result']['contact_business_number']}}">
                                    </div>  
                                </div>
                                <div class="row tbContainer"> 
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.name')</label>
                                        <input type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.name')" class="mt-2 requiredField"  name="name[]">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.email')</label>
                                        <input type="text" placeholder="@lang('app.enter_email')" class="mt-2 requiredField"  name="email[]">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="">@lang('web/group-details.whatsapp')</label>
                                        <select class="form-control test" name="whatsapp_code[]">
                                            <option value="">--@lang('web/app.select_code')--</option>
                                            @foreach($country as $con)
                                                <option value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                                            @endforeach
                                        </select>
                                        <input style="margin-left: -4px;width: 70%;" type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.whatsapp')" class="mt-2 requiredField" onkeypress="return /[0-9 ]/i.test(event.key)" name="whatsup[]">
                                    </div>
                                    <div class="col-lg-3"> 
                                        <label for="">@lang('web/app.select_code')</label>
                                        <select class="form-control test" name="mobile_code[]">
                                            <option value="">--@lang('web/app.select_code')--</option>
                                            @foreach($country as $con)
                                                <option value="{{$con['phonecode']}}">+{{$con['phonecode']}}</option>
                                            @endforeach
                                        </select>
                                        <input style="margin-left: -4px;width: 70%;" type="text" placeholder="@lang('web/app.enter') @lang('web/group-details.mobile')" class="mt-2 requiredField" onkeypress="return /[0-9 ]/i.test(event.key)"  name="mobile[]">
                                        <a href="javascript:;" class="add-btn remove-btn" style="float:right;color:white;margin-top:10px">
                                            <span>
                                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.28125 4.46875V4.8125H12.7188V4.46875C12.7188 4.01291 12.5377 3.57574 12.2153 3.25341C11.893 2.93108 11.4558 2.75 11 2.75C10.5442 2.75 10.107 2.93108 9.78466 3.25341C9.46233 3.57574 9.28125 4.01291 9.28125 4.46875ZM7.90625 4.8125V4.46875C7.90625 3.64824 8.2322 2.86133 8.81239 2.28114C9.39258 1.70095 10.1795 1.375 11 1.375C11.8205 1.375 12.6074 1.70095 13.1876 2.28114C13.7678 2.86133 14.0938 3.64824 14.0938 4.46875V4.8125H19.25C19.4323 4.8125 19.6072 4.88493 19.7361 5.01386C19.8651 5.1428 19.9375 5.31766 19.9375 5.5C19.9375 5.68234 19.8651 5.8572 19.7361 5.98614C19.6072 6.11507 19.4323 6.1875 19.25 6.1875H18.2133L16.9125 17.578C16.8166 18.4169 16.4153 19.1911 15.7851 19.7531C15.1549 20.315 14.34 20.6254 13.4956 20.625H8.50437C7.66003 20.6254 6.84507 20.315 6.2149 19.7531C5.58472 19.1911 5.18342 18.4169 5.0875 17.578L3.78675 6.1875H2.75C2.56766 6.1875 2.3928 6.11507 2.26386 5.98614C2.13493 5.8572 2.0625 5.68234 2.0625 5.5C2.0625 5.31766 2.13493 5.1428 2.26386 5.01386C2.3928 4.88493 2.56766 4.8125 2.75 4.8125H7.90625ZM6.45425 17.4212C6.51165 17.9244 6.75218 18.3889 7.13001 18.7262C7.50784 19.0634 7.99655 19.2499 8.503 19.25H13.4963C14.0028 19.2499 14.4915 19.0634 14.8693 18.7262C15.2471 18.3889 15.4877 17.9244 15.5451 17.4212L16.83 6.1875H5.17069L6.45425 17.4212ZM8.9375 8.59375C9.11984 8.59375 9.2947 8.66618 9.42364 8.79511C9.55257 8.92405 9.625 9.09891 9.625 9.28125V16.1562C9.625 16.3386 9.55257 16.5135 9.42364 16.6424C9.2947 16.7713 9.11984 16.8438 8.9375 16.8438C8.75516 16.8438 8.5803 16.7713 8.45136 16.6424C8.32243 16.5135 8.25 16.3386 8.25 16.1562V9.28125C8.25 9.09891 8.32243 8.92405 8.45136 8.79511C8.5803 8.66618 8.75516 8.59375 8.9375 8.59375ZM13.75 9.28125C13.75 9.09891 13.6776 8.92405 13.5486 8.79511C13.4197 8.66618 13.2448 8.59375 13.0625 8.59375C12.8802 8.59375 12.7053 8.66618 12.5764 8.79511C12.4474 8.92405 12.375 9.09891 12.375 9.28125V16.1562C12.375 16.3386 12.4474 16.5135 12.5764 16.6424C12.7053 16.7713 12.8802 16.8438 13.0625 16.8438C13.2448 16.8438 13.4197 16.7713 13.5486 16.6424C13.6776 16.5135 13.75 16.3386 13.75 16.1562V9.28125Z" fill="#EF0000"/>
                                                </svg>
                                            </span>
                                            @lang('web/group-details.delete')
                                        </a>
                                    </div>  
                                </div>
                                <div class="add-remove-btn"> 
                                    <a href="javascript:;" class="addInput add-btn remove hidden">
                                        <span>
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.6668 1.83203H7.3335C4.30025 1.83203 1.8335 4.29878 1.8335 7.33203V19.2487C1.8335 19.4918 1.93007 19.725 2.10198 19.8969C2.27389 20.0688 2.50705 20.1654 2.75016 20.1654H14.6668C17.7001 20.1654 20.1668 17.6986 20.1668 14.6654V7.33203C20.1668 4.29878 17.7001 1.83203 14.6668 1.83203ZM18.3335 14.6654C18.3335 16.6875 16.689 18.332 14.6668 18.332H3.66683V7.33203C3.66683 5.30986 5.31133 3.66536 7.3335 3.66536H14.6668C16.689 3.66536 18.3335 5.30986 18.3335 7.33203V14.6654Z" fill="#04B700"/>
                                                <path d="M11.9165 6.41797H10.0832V10.0846H6.4165V11.918H10.0832V15.5846H11.9165V11.918H15.5832V10.0846H11.9165V6.41797Z" fill="#04B700"/>
                                            </svg>
                                        </span>
                                        @lang('web/group-details.add') @lang('web/group-details.more')
                                    </a>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="table-btn" style="display: flex;justify-content: center;"> 
                        <button type="submit" class="main-btn bg-gray-btn" form="formSubmit">@lang('web/group-details.submit')</button>
                    </div> 
                </form>
                <div class="no-table">
                </div>
            </div>
        </div>
    </div>
    <!-- banner-end -->


@endsection


@push('custom_js')

    <script>
        $(document).ready(function () {
            $(".yes-btn").click(function () { 
                $(this).addClass("acive-btn");
                $(".no-btn").removeClass("acive-btn");
                $('.tbContainer input').addClass('required'); 
                $('.requiredField').attr('required',true);
            });
        });
        $(document).ready(function () {
            $(".no-btn").click(function () {
                $(this).addClass("acive-btn");
                $(".yes-btn").removeClass("acive-btn");
                $('.tbContainer input').removeClass('required');
                $('.requiredField').attr('required',false);
            });
        });

        $(document).ready(function(){
            $(".yes-btn").click(function(){
                $(".yes-table").addClass("table-show");
            });
        });
        $(document).ready(function(){
            $(".no-btn").click(function(){
                $(".yes-table").removeClass("table-show");
            });
            
        });

        $(document).ready(function() {
  
            var $addInput = $('a.addInput');
            $addInput.on("click", function(e) {
                var old=$('.test');
                old.fSelect('destroy');
                e.preventDefault();
                var $this = $(this);
                var $lastTbContainer = $this.closest('.fieldsGroup').children('.tbContainer:last');
                var $clone = $lastTbContainer.clone();
                $clone.find('button').removeClass('hidden');
                $clone.find('input').val('');
                
                $lastTbContainer.after($clone);

                removeGroupInfoRaw(); 

                old.fSelect('create');
                $('.test').fSelect();
               

            });  

        }); 

        $(document).ready(removeGroupInfoRaw);
        function removeGroupInfoRaw(){

            $(".remove-btn").click(function(){ 
                if($('.tbContainer').length>2){
                    $(this).parent().parent().remove();
                }else{
                    showMsg('error', 'You can not delete this row');
                }
                
            });

        }

        $(document).ready(function() {
  
            $("form#formSubmit").submit(function(e) {

                e.preventDefault();

                var formId = $(this).attr('id');
                var formAction = $(this).attr('action');
                var btnhtml = $("button[form="+formId+"]").html();

                if ($('.yes-table').hasClass('table-show') && fSelectRequired(formId)) {
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
                        submitButton(formId, btnhtml, false);
                        showMsg('success', data.message);
                        location.href = "{{ route('profile-update') }}";
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                });

            });

        }); 


        $('.test').fSelect({
			placeholder: "@lang('web/ministry-details.select')",
			overflowText: '{n} selected',
			noResultsText: '',
			searchText: 'Search',
			showSearch: true
		});

    </script>
 

@endpush