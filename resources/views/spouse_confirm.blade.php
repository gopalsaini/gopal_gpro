
@extends('layouts/app')

@section('title',__('spouse confirm'))

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

                                @if(App::getLocale() == 'pt')
                                    
                                    <h6 class="panel-title display-td">{{$user->salutation}} {{$user->name}}{{$user->last_name}} inscreveu-se para o II CongressoGPro como seu cônjuge. Poderia por favor confirmar que {{$linkPayment->salutation}} {{$linkPayment->name}}{{$linkPayment->last_name}} é seu cônjuge e que juntos têm planos de participar? Por favor clique aqui para confirmar</h6>

                                @elseif(App::getLocale() == 'sp')
                                    <h6 class="panel-title display-td">{{$user->salutation}} {{$user->name}}{{$user->last_name}} se ha inscrito en el GProCongress II como su cónyuge. ¿Podría confirmar que {{$linkPayment->salutation}} {{$linkPayment->name}}{{$linkPayment->last_name}} es su cónyuge y que ambos tienen previsto asistir? Haga clic aquí para confirmar</h6>

                                @elseif(App::getLocale() == 'fr')
                                <h6 class="panel-title display-td">{{$user->salutation}} {{$user->name}}{{$user->last_name}} s’est inscrit/e au GProCongrès II en tant que conjoint/e. Pourriez-vous confirmer que {{$linkPayment->salutation}} {{$linkPayment->name}}{{$linkPayment->last_name}} est votre conjoint/e et que vous avez tous les deux l’intention d’y assister? Veuillez cliquer ici pour confirmer</h6>

                                @else
                                    <h6 class="panel-title display-td">{{$user->salutation}} {{$user->name}}{{$user->last_name}} has registered for the GProCongress II as your spouse. Would you please confirm that {{$linkPayment->salutation}} {{$linkPayment->name}}{{$linkPayment->last_name}} is your spouse and that you both plan to attend? Please click here to confirm</h6>

                                @endif
                               
                            </div>                    
                        </div>
                        <br><br>
                        <div class="panel-body" >

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="step-next">
                                        <a class="main-btn bg-gray-btn" href="{{url('spouse-confirm/confirm/'.$linkPayment->spouse_confirm_token)}}">Confirm</a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="step-next">
                                        <a class="main-btn bg-gray-btn" href="{{url('spouse-confirm/decline/'.$linkPayment->spouse_confirm_token)}}">Decline</a>
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