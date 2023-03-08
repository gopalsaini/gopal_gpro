
@extends('layouts/app')

@section('title',__('sponsorship letter approve'))

@section('content')
<br>
<br>
<br>
<div class="">
        <div class="container custom-container2">
            
            <div class="row" style="justify-content: center;">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default credit-card-box">
                        <div class="panel-heading   " >
                            <div class="row display-tr text-center" >

                               
                            </div>                    
                        </div>
                        <br><br>
                        <div class="panel-body" >

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="step-next">
                                        <a class="main-btn bg-gray-btn" href="{{url('sponsorship-confirm/confirm/'.$linkPayment->spouse_confirm_token)}}">Confirm</a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="step-next">
                                        <a class="main-btn bg-gray-btn" href="{{url('sponsorship-confirm/decline/'.$linkPayment->spouse_confirm_token)}}">Decline</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>        
                </div>
            </div>
            
        </div>

    </div>
    <br>
<br>
<br>
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