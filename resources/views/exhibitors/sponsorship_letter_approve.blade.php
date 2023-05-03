
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
                                <label for="inputName">Enter Decline Remark <label class="text-danger">*</label></label>
                                <form id="Passport" action="{{ route('sponsorshipLetterReject') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <textarea name="remark" id="remark" cols="10" rows="5" class="form-control" required></textarea>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger " onclick="modalHide()">Close</button>
                <button type="submit" class="btn btn-dark " form="Passport">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('custom_js')

<script>
  
    $("form#Passport").submit(function(e) {

        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        var form_data = new FormData(this);

        var btnhtml = $("button[form=" + formId + "]").html();

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            headers: {
                "Authorization": "Bearer {{\Session::get('gpro_exhibitor')}}"
            },
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.status + ': ' + xhr.statusText);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                showMsg('success', data.message);
                location.href = "{{url('profile')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });

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

        
    });

    function modalHide() {
        
        $('#exampleModalCenter').modal('hide');
        $('#row_id').val(0);
        $('#remark').val(null);
    }
		
</script>
@endpush