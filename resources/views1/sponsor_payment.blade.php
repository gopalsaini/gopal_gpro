
@extends('layouts/app')

@section('title',__('Payment'))

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            @if(count($errors))

                <div class="alert alert-danger">

                    <strong>@lang('web/sponsor-payment.whoops')</strong> @lang('web/sponsor-payment.problems-with-input')

                    <br/>

                    <ul>

                        @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="step-form">
                <div class="tabs-wrapper">
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" aria-labelledby="pills-home-tab">
                            <div class="accordion payment-accordion" id="accordionExample2">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" >
                                        <button class="accordion-button" type="button" >
                                            Payment confirmation 
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
                                        <div class="accordion-body">
                                            <div class="modal-body">
                                                <form id="formSubmit" action="{{ route('sponsor-payments-pay') }}" class="row" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="col-lg-6">
                                                        <label for="">@lang('web/sponsor-payment.name') <span>*</span></label>
                                                        <input type="text" name="name" value="{{$linkPayment->name}}" readonly onkeypress="return /[a-z A-Z ]/i.test(event.key)" required placeholder="Type Name here" class="active-input mt-2" >
                                                        <input type="hidden" name="user_id" required placeholder="Type Name here" class="active-input mt-2" value="{{$linkPayment->user_id}}" >
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <label for="">@lang('web/sponsor-payment.email') <span>*</span></label>
                                                        <input type="email" name="email" readonly placeholder="Type Email Id here" class="mt-2" required value="{{$linkPayment->email}}">
                                                    </div>
                                                    @php $totalPendingAmount = \App\Helpers\commonHelper::getTotalPendingAmount($linkPayment->user_id, true); @endphp
                                                    <p>@lang('web/sponsor-payment.max-payment') ${{$totalPendingAmount}}</p>
                                                    <div class="col-lg-6">
                                                        <label for="">@lang('web/sponsor-payment.requested-amount') <span>*</span></label>
                                                        <input type="tel" name="amount" required onkeypress="return /[0-9 ]/i.test(event.key)" value="{{$linkPayment->amount}}" placeholder="Type Requested Amount Here" class="mt-2" >
                                                    </div>
                                                    
                                                    @if($totalPendingAmount > 0)
                                                        <div class="col-lg-6">
                                                            <div class="step-next">
                                                                <button type="submit" class="main-btn bg-gray-btn" form="formSubmit">@lang('web/sponsor-payment.submit')</button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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

    $('#PartialPaymentOnline').click(function(){
        $("#PartialPaymentOnlineDiv").toggle();
        $("#PartialPaymentOfflineDiv").css('display','none');
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
                // showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                if(data.urlPage){
                    window.location.href = data.url;
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
</script>
@endpush