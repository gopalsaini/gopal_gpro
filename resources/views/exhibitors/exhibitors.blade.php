@extends('exhibitors/layouts/app')

@section('title',__('Exhibitors'))

@push('custom_css')
<style>
    .fs-label-wrap {
        height: 50.5px;
        background: #F9F9F9;
        border: 0px;
    }

    .fs-wrap {
        width: 100%;
    }

    .fs-label-wrap .fs-label {
        padding: 15px 15px 2px 7px;
    }

    .form__radio-button::after {

        background-color: #0603e0 !important;
    }
    .form-check-label{
        line-height: 3;
    }
    .form-check {
        
        padding-left: 0px !important;
    }
    .step-form form .radio-wrap {
        padding-bottom: 15px;
        padding-top: 0px !important;
    }
    form label {
        font-size: 14px !important;
    }
</style>
@endpush

@section('content')

<!-- banner-start -->
<div class="inner-banner-wrapper">
    <div class="container">
        <div class="step-form">
            <h2 class="main-head">@lang('web/app.exhibitors_registration')</h2>
            <h5 style="text-align:center;padding-top: 50px;"></h5>
            <form id="formSubmit" action="{{ route('exhibitors-register') }}" method="post" class="row" enctype="multipart/form-data">
                @csrf
                <div class="yes-table  table-show">
                    <div class="group-register-box">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-check">
                                    <label for="input" class="form-check-label">@lang('web/app.first_name') <span>*</span></label>
                                    <input type="text" readonly value="{{\Session::get('gpro_result')['name']}}" onkeypress="return /[a-z A-Z ]/i.test(event.key)" name="name" id="Name" placeholder="@lang('web/app.first_name')" required>

                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check">
                                    <label class="form-check-label" for="input">@lang('web/app.last_name') <span>*</span></label>
                                    <input type="text" readonly value="{{\Session::get('gpro_result')['last_name']}}"  onkeypress="return /[a-z A-Z ]/i.test(event.key)" name="salutation" id="Name" placeholder="@lang('web/app.last_name')" required>
                                
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.email') <span>*</span></label>
                                    <div class="input-box">
                                        <input type="email" readonly value="{{\Session::get('gpro_result')['email']}}"  required name="email" placeholder="@lang('web/app.enter') @lang('web/app.enter_email')">

                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.business_name') <span>*</span></label>
                                    <div class="input-box">
                                        <input type="text" required onkeypress="return /[a-z A-Z ]/i.test(event.key)" name="business_name" id="businessName" placeholder="@lang('web/app.enter') @lang('web/app.business_name')">

                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-lg-4">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.business_number') <span>*</span></label>
                                    <div class="input-box">
                                        <input type="tel" required name="business_identification_no" id="businessIdentificationNo" placeholder="@lang('web/app.enter') @lang('web/app.business_number')">

                                    </div>
                                </div>
                            </div> -->
                            
                            <div class="col-lg-4">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.Website')  <span style="color:black">(@lang('web/app.Optional'))</span></label>
                                    <div class="input-box">
                                        <input type="text" name="website" id="Website" placeholder="@lang('web/app.enter') @lang('web/app.Website')">

                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/ministry-details.enter-comments')  <span>*</span></label>
                                    <div class="input-box">
                                        <textarea type="text" class="form-control" name="comment" required id="Comment" placeholder="@lang('web/ministry-details.enter-comments')"></textarea>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check d-flex">
                                    <label for="checkbox"  class="form-check-label ps-4">@lang('web/app.approval_and_agree_to')  <a href="{{url('exhibitor-policy')}}" target="_blank">@lang('web/app.exhibitor_policy') </a><b style="color:red">*</b> </label>
                                    <div class="input-box" style="position:absolute;">
                                        <input type="checkbox" name="checkbox" id="checkbox" checked required>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="table-btn" style="display: flex;justify-content: center;">
                        <button type="submit" class="main-btn bg-gray-btn" form="formSubmit">@lang('web/group-details.submit')</button>
                    </div>
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
    $(document).ready(function() {
        $(".yes-btn").click(function() {
            $(this).addClass("acive-btn");
            $(".no-btn").removeClass("acive-btn");
            $('.tbContainer input').addClass('required');
            $('.requiredField').attr('required', true);
        });
    });
    $(document).ready(function() {
        $(".no-btn").click(function() {
            $(this).addClass("acive-btn");
            $(".yes-btn").removeClass("acive-btn");
            $('.tbContainer input').removeClass('required');
            $('.requiredField').attr('required', false);
        });
    });

    $(document).ready(function() {
        $(".yes-btn").click(function() {
            $(".yes-table").addClass("table-show");
        });
    });
    $(document).ready(function() {
        $(".no-btn").click(function() {
            $(".yes-table").removeClass("table-show");
        });

    });

    $(document).ready(function() {
        var totalRow = 1;
        var $addInput = $('a.addInput');
        $addInput.on("click", function(e) {
            
            totalRow++;

            var old = $('.test');
            old.fSelect('destroy');
            e.preventDefault();
            var $this = $(this);
            var $lastTbContainer = $this.closest('.fieldsGroup').children('.tbContainer:last');
            var $clone = $lastTbContainer.clone();
            $clone.find('button').removeClass('hidden');
            $clone.find('.alongSpouse').attr('data-id', totalRow);
            $clone.find('.alongSpouseClass').attr('id', 'alongSpouse'+totalRow);
            $clone.find('input').val('');
            $clone.find('#alongSpouse'+totalRow).hide();
            
            $lastTbContainer.after($clone);

            removeGroupInfoRaw();
            alongSpouseChange();
            old.fSelect('create');
            $('.test').fSelect();


        });

    });

    $(document).ready(removeGroupInfoRaw);

    function removeGroupInfoRaw() {

        $(".remove-btn").click(function() {
            if ($('.tbContainer').length > 1) {
                $(this).parent().parent().remove();
            } else {
                showMsg('error', 'You can not delete this row');
            }

        });

    }

    $(document).ready(function() {

        $("form#formSubmit").submit(function(e) {

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
                    submitButton(formId, btnhtml, false);
                    showMsg('success', data.message);
                    location.href = "{{url('exhibitor-index')}}";
                    
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


    $(document).ready(function() {

        $('.AddSpouse').hide();

        $('input:radio[name=coming_with_spouse]').change(function() {

            if ($("input[name='coming_with_spouse']:checked").val() == 'Yes') {

                $('.AddSpouse').show();
            }
            if ($("input[name='coming_with_spouse']:checked").val() == 'No') {

                $('.AddSpouse').hide();

            }
        });
    });

    $(document).ready(function() {

        $('.addAlongGroup').hide();
        $('.addMoreBtn').hide();

        $('input:radio[name=any_one_coming_with_along]').change(function() {

            if ($("input[name='any_one_coming_with_along']:checked").val() == 'Yes') {

                $('.addAlongGroup').show();
                $('.addMoreBtn').show();
                $('.comingAlongWithSpouse').show();

            }
            if ($("input[name='any_one_coming_with_along']:checked").val() == 'No') {

                $('.addAlongGroup').hide();
                $('.addMoreBtn').hide();
                $('.comingAlongWithSpouse').hide();


            }
        });
    });

    $(document).ready(function() {

        $('.comingAlongWithSpouse').hide();
        $('.comingAlongWithSpouseShow').hide();

        $('input:radio[name=coming_along_with_spouse]').change(function() {

            if ($("input[name='coming_along_with_spouse']:checked").val() == 'Yes') {

                $('.comingAlongWithSpouseShow').show();
            }
            if ($("input[name='coming_along_with_spouse']:checked").val() == 'No') {

                $('.comingAlongWithSpouseShow').hide();
                $('.comingAlongWithSpouseShow').hide();

            }
        });
    });

    

    alongSpouseChange();

function alongSpouseChange(){
    
    $('.alongSpouse').change(function(){
        
        if($(this).val() == 'Yes'){

            $('#alongSpouse'+$(this).data('id')).show();

        }else{

            $('#alongSpouse'+$(this).data('id')).hide();

        }
    });
}
   
</script>


@endpush