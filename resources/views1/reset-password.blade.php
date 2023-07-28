@extends('layouts.app')

@section('title','Reset Password')

@section('content')

    <div class="login-modal">
        <div class="modal fade" id="resetPasswordModal" aria-hidden="true" aria-labelledby="resetPasswordModalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/reset-password.title')</h2>
                        <h5 style="text-align:center;padding-top: 50px;">@lang('web/reset-password.description')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding-top:0px">
                        <form id="reset-password-form" action="{{ route('reset.password') }}" class="row" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="token" value="{{$token}}">

                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/reset-password.email') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="email" name="email" placeholder="Enter email address" value="{{$email}}" required>
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/reset-password.password') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="password" name="password" placeholder="Enter password">
                                        <i class="toggle-password fas fa-eye-slash eye-wrap"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/reset-password.confirm') @lang('web/reset-password.password') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="password" name="password_confirmation" placeholder="Re-enter password">
                                        <i class="toggle-password fas fa-eye-slash eye-wrap"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <button type="submit" class="login-btn" form="reset-password-form">@lang('web/reset-password.reset') @lang('web/reset-password.password')</button>
                                </div>
                                <div class="signup">
                                    <p>@lang('web/reset-password.back-to-forgot') <a href="javascript:void(0);" onclick="openForgotPasswordModal()">@lang('web/reset-password.click-to-forgot')</a></p>
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

<script>

$(document).ready(function() {
    $('#resetPasswordModal').modal('show');
});

$("form#reset-password-form").submit(function(e) {

    e.preventDefault();

    var formId = $(this).attr('id');
    var formAction = $(this).attr('action');
    var btnhtml = $("button[form=" + formId + "]").html();

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
            if (data.reload) {
                showMsg('success', data.message);
                $('form#reset-password-form')[0].reset();
                location.href = "{{ route('home') }}";
            }
            submitButton(formId, btnhtml, false);
        },
        cache: false,
        contentType: false,
        processData: false,
    });
});
</script>

@endpush