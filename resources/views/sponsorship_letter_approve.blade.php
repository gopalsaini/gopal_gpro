
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
                                        <a class="main-btn bg-gray-btn" href="{{url('sponsorship-confirm/confirm/'.$passportInfo->id)}}">Confirm</a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="step-next">
                                        <a class="main-btn bg-gray-btn -change" data-id="{{$passportInfo->id}}" href="javascript:void(0);">Decline</a>
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

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-3">
                <h5 class="modal-title" id="exampleModalLongTitle">Passport Info</h5>
                <button type="button" class="close" onclick="modalHide()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="m-0">
            <div class="modal-body px-3">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="inputName">Remark <label class="text-danger">*</label></label>
                                <form id="PassportInfoReject" action="{{ route('sponsorshipLetterReject') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <textarea name="remark" id="" cols="30" rows="10" class="form-control" required></textarea>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger px-4 mx-2" onclick="modalHide()">Close</button>
                <button type="button" class="btn btn-dark px-4 mx-2" onclick="clickOnStatusChangeBtn()">Upload</button>
            </div>
        </div>
    </div>
</div>
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

    $("form#PassportInfoReject").submit(function(e) {

        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            async: false,
            beforeSend: function() {
                $('#preloader').css('display', 'block');
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }

                $('#preloader').css('display', 'none');
            },
            success: function(data) {

                setTimeout(() => {
                    location.reload();
                }, 500);
                sweetAlertMsg('success', data.message);
                $('#preloader').css('display', 'none');
            },
            cache: false,
            contentType: false,
            processData: false,
            timeout: 5000
        });

});

    function clickOnStatusChangeBtn() {
        var id = $('#row_id').val();
        var url = $('#url').val();
        if (id !== 0 && url != '') {
            $('form#PassportInfoReject').trigger('submit');
            modalHide();
        } else if (url == '') {
            sweetAlertMsg('error', '403 : Remark field is required');
        } else {
            sweetAlertMsg('error', '403 : Something went wrong');
        }
    }

$(document).ready(function() {

$('#exampleModalCenter').on('hidden.bs.modal', function (e) {
    modalHide();
})

$('.-change').click(function() {
    var id = $(this).data('id');

    $('#exampleModalCenter').modal('show');
    $('#row_id').val(id);
    $('#url').val(null);

});

function modalHide() {
    $('#exampleModalCenter').modal('hide');
    $('#row_id').val(0);
    $('#url').val(null);
}
});


		
</script>
@endpush