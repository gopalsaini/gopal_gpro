
@extends('layouts/app')

@section('title',__(Lang::get('web/help.help')))

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
                <h4 class="inner-head">@lang('web/help.help')</h4>
                <form id="form" action="{{ route('help') }}" class="row" enctype="multipart/form-data">
                    @csrf
                    <div class="col-lg-12">
                        <label for="">@lang('web/help.name')<span>*</span></label>
                        <input type="text" name="name" placeholder="@lang('web/app.enter') @lang('web/help.name')" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-12">
                        <label for="">@lang('web/help.email')<span>*</span></label>
                        <input type="email" name="email" placeholder="@lang('web/app.enter_email')" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-12">
                        <label for="">@lang('web/help.mobile')<span>*</span></label>
                        <input type="text" name="mobile" placeholder="@lang('web/app.enter') @lang('web/help.mobile')" class="active-input mt-2" required>
                    </div>
                    <div class="col-lg-12">
                        <label for="">@lang('web/help.message')<span>*</span></label>
                        <textarea name="message" class="form-control active-input mt-2" cols="30" rows="5" placeholder="@lang('web/app.enter') @lang('web/help.message')" required></textarea>
                    </div>
                    <div class="col-lg-6">
                        <div class="step-next">
                            <button type="submit" class="main-btn" form="form">@lang('web/help.submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- banner-end -->

@endsection