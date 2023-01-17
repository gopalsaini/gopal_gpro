
@extends('layouts/app')

@section('title',__(Lang::get('web/app.home')))

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
    </style>
@endpush

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container">
           
            <div class="step-form">
                <h4 class="inner-head">@lang('web/change-password.change') @lang('web/change-password.password')</h4>
                <form id="formSubmit" action="{{ route('user.change-password') }}" class="row" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12">
                        <label for="">@lang('web/change-password.enter') @lang('web/change-password.old') @lang('web/change-password.password')<span>*</span></label>
                        <input type="password" name="old_password" required placeholder="@lang('web/change-password.enter') @lang('web/change-password.old') @lang('web/change-password.password')" class="active-input mt-2" >
                    </div>
                    <div class="col-lg-12">
                        <label for="">@lang('web/change-password.enter') @lang('web/change-password.new') @lang('web/change-password.password')<span>*</span></label>
                        <input type="password" name="new_password" placeholder="@lang('web/change-password.enter') @lang('web/change-password.new') @lang('web/change-password.password')" class="mt-2" required >
                    </div>
                    <div class="col-lg-12">
                        <label for="">@lang('web/change-password.enter') @lang('web/change-password.confirm') @lang('web/change-password.password')<span>*</span></label>
                        <input type="password" name="confirm_password" placeholder="@lang('web/change-password.enter') @lang('web/change-password.confirm') @lang('web/change-password.password')" class="mt-2" required >
                    </div>
                    <div class="col-lg-6">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="formSubmit">@lang('web/change-password.submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- banner-end -->

@endsection


@push('custom_js')
<script src="{{asset('js/intlTelInput.js')}}"></script>

<script>

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
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                location.href = "{{ route('profile-update') }}";
                
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


</script>
@endpush