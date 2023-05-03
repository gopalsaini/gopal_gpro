<!-- Otp Section -->
<div class="modal-header d-block border-0">
    <h2 class="main-head">@lang('web/verification.title')</h2>
    <h5>@lang('web/verification.description') <strong>{{ $data['email'] }}</strong> </h5>
    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
</div>
<div class="modal-body" style="padding-top:0px">
    <form id="verification" action="{{ route('validate.otp') }}" class="row" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="email" value="{{ $data['email'] }}" required/>

        <div class="col-lg-12">
            <div class="form-check">
                <label class="form-check-label">@lang('web/verification.otp') <b>*</b> </label>
                <div class="otpContainer d-flex">
                    <input size="1" maxlength="1" type='tel' id='first' name='otp1' autocomplete="off" required onkeypress="return /[0-9 ]/i.test(event.key)" class='form-control text-center otp firstotp' style="width:25%" />
                    <input size="1" maxlength="1" type='tel' id='second' name='otp2' autocomplete="off" required onkeypress="return /[0-9 ]/i.test(event.key)" class='form-control text-center otp' style="width:25%"/>
                    <input size="1" maxlength="1" type='tel' id='third' name='otp3' autocomplete="off" required onkeypress="return /[0-9 ]/i.test(event.key)" class='form-control text-center otp'  style="width:25%"/>
                    <input size="1" maxlength="1" type='tel' id='fourth' name='otp4' autocomplete="off" required onkeypress="return /[0-9 ]/i.test(event.key)" class='form-control text-center otp' style="width:25%"/>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div>
                <button type="submit" class="login-btn" form="verification">@lang('web/verification.submit')</button>
            </div>
            <div class="signup">
                <p>@lang('web/verification.not-received-your-otp')
                    <span id="timer_left">00:00</span>
                    <a id="sendotp" href="javascript:void(0);" data-email="{{ $data['email'] }}" form="sendotp"> @lang('web/verification.resend-code')</a>
                </p>
            </div>
        </div>
    </form>
</div> 

<script>
    sendOtp();
    </script>