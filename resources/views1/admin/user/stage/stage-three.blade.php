@extends('layouts/master')

@section('title',__('Stage Three '.ucfirst($type)))

@push('custom_css')
    <style>
        .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        } 
    </style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.stage') @lang('admin.three') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.three')</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.user.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>Send Invitation</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @include('admin.user.stage-bar')
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.user') </th>
                                    <th> @lang('admin.email') </th>
                                    <th> @lang('admin.mobile') </th>
                                    <th>  User Remark </th>
                                    <th> @lang('admin.travel') @lang('admin.info') </th>
                                    <th> Visa Letter </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="8">
                                        <div id="loader" class="spinner-border" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.user') </th>
                                    <th> @lang('admin.email') </th>
                                    <th> @lang('admin.mobile') </th>
                                    <th>  User Remark</th>
                                    <th> @lang('admin.travel') @lang('admin.info') </th>
                                    <th> Visa Letter </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">User Flight Information Remark</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p id="Remark"></p>
      </div>
      
    </div>
  </div>
</div>



<div class="modal fade" id="draftLetterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send User Upload File Letter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="formSubmit" action="{{ url('admin/user/upload-draft-information') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="travelId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="info">
                                <div class="information-box">
                                    <h6>Upload File <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="file" type="file" required class="form-control" /></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="formSubmit">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="finalLetterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send Final Visa Letter </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="formSubmit1" action="{{ url('admin/user/upload-final-information') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="finalId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="info" > 
                                <h6>Select Type file <span style="color:red">*</span></h6>
                                <div class="information-box" style="display: flex;" >
                                    <input type="radio" checked id="Generated" name="type" value="1"> &nbsp;&nbsp;
                                    <label for="Generated">Generated </label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" id="Upload" name="type" value="2">&nbsp;&nbsp;
                                    <label for="Upload">Upload</label><br>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12" style="display:none" id="UploadFileDiv">
                            <div class="info">
                                <div class="information-box">
                                    <h6>Upload File <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="file" id="fileData" type="file" class="form-control" /></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="formSubmit1">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>

@endsection

@push('custom_js')

<script>

$(document).ready(function () {
        $('input:radio[name=type]').change(function () {
          
            if ($("input[name='type']:checked").val() == '1') {

                $('#UploadFileDiv').hide();
                $('#fileData').attr('required',false);
                
            }else{
                $('#UploadFileDiv').show();
                $('#fileData').attr('required',true);
            }
            
        });
    });


$(document).ready(function() {

    
    fill_datatable();
    $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": false,

        "ajax": {
            "url": "{{ route('admin.user.list.stage.three', ["$type"]) }}",
            "dataType": "json",
            "async": false,
            "type": "get",
            "error": function(xhr, textStatus) {
                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
            },
        },
        "fnDrawCallback": function() {
            fill_datatable();
        },
        "order": [0, 'desc'],
        "columnDefs": [{
                className: "text-left",
                targets: "_all"
            },
            {
                orderable: false,
                targets: [-1]
            },
        ],
        "columns": [{
                "data": null,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + '.';
                },
                className: "text-center font-weight-bold"
            },
            {
                "data": "user_name"
            },
            {
                "data": "email"
            },
            {
                "data": "mobile"
            },
            {
                "data": "remark"
            },
            {
                "data": "user_status"
            },
            {
                "data": "admin_status"
            },
            {
                "data": "action"
            }
        ]
    });
});

function fill_datatable() {

    $('.ViewRemark').click(function() {
    
        $('#exampleModal').modal('show');
        $('#Remark').html($(this).data('remark'));
    });

    $('.sendDraftLetter').click(function() {
    
        $('#draftLetterModal').modal('show');
        $('#travelId').val($(this).data('id'));
    });

    $('.sendFinalLetter').click(function() {
    
        $('#finalLetterModal').modal('show');
        $('#finalId').val($(this).data('id'));
    });

    $('.sendEmail').click(function() {
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.user.send.travel.info.reminder') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id
            },
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
                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
            }
        });
    });

    $('.-change').click(function() {
        var status = $(this).data('type');
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.user.travel.info.status') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id,
                'status': status,
            },
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
                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
                $('#tablelist').DataTable().ajax.reload(null, false);
            }
        });
    });
}

 
    $("form#formSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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

                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
                $('#draftLetterModal').modal('hide');
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    
    $("form#formSubmit1").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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

                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
                $('#finalLetterModal').modal('hide');
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });


</script>
@endpush