@extends('layouts/master')

@section('title',__('Stage Two '.ucfirst($type)))

@push('custom_css')
    <style>
        .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        }   
    </style>
        
    <style>
            .odd{
                position: relative;
            }

            .group-user-list{
                position: absolute;
                left: 87px;
            }
            .dataTables_wrapper table.dataTable tbody td:nth-child(2)  { 
                padding-left: 45px !important;
            }
            .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
                background-color: #ffc107 !important;
                border-color: #ffc107 !important;
            } 

            .group-user-list {
                background: url('{{asset("admin-assets/images/details_open.png")}}') no-repeat center center;
                cursor: pointer;
                width: 25px;
                height: 25px;
            }
            .shown .group-user-list {
                background: url('{{asset("admin-assets/images/details_close.png")}}') no-repeat center center;
            }
    </style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.stage') @lang('admin.two') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.two')</li>
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
                    <input type="text" name="email" class="form-control searchEmail " placeholder="Search ...">
                    <br>
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.name') </th>
                                    <th> @lang('admin.email') </th>
                                    <th> @lang('admin.mobile') </th>
                                    <th> @lang('admin.total') @lang('admin.amount') </th>
                                    <th> @lang('admin.amount') @lang('admin.in') @lang('admin.process') </th>
                                    <th> @lang('admin.accepted') @lang('admin.amount') </th>
                                    <th> @lang('admin.rejected') @lang('admin.amount') </th>
                                    <th> @lang('admin.pending') @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> User Type </th>
                                    <th> Group Owner Name </th>
                                    <th> Spouse Name </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="10">
                                        <div id="loader" class="spinner-border" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.name') </th>
                                    <th> @lang('admin.email') </th>
                                    <th> @lang('admin.mobile') </th>
                                    <th> @lang('admin.total') @lang('admin.amount') </th>
                                    <th> @lang('admin.amount') @lang('admin.in') @lang('admin.process') </th>
                                    <th> @lang('admin.accepted') @lang('admin.amount') </th>
                                    <th> @lang('admin.rejected') @lang('admin.amount') </th>
                                    <th> @lang('admin.pending') @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> User Type </th>
                                    <th> Group Owner Name </th>
                                    <th> Spouse Name </th>
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

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-3">
                <h5 class="modal-title" id="exampleModalLongTitle">@lang('admin.approve') @lang('admin.user')</h5>
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
                                <label for="inputName">@lang('admin.amount') <label class="text-danger">*</label></label>
                                <input type="hidden" id="row_id" value="0" required />
                                <input type="number" class="form-control" id="amount" placeholder="Enter amount value" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger px-4 mx-2" onclick="modalHide()">@lang('admin.close')</button>
                <button type="button" class="btn btn-dark px-4 mx-2" onclick="clickOnStatusChangeBtn()">@lang('admin.save')</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="cashPaymentModel" tabindex="-1" role="dialog" aria-labelledby="cashPayment"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-3">
                <h5 class="modal-title">Cash payment</h5>
                <button type="button" class="close" onclick="modalHide()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="m-0">
            <div class="modal-body px-3">
                <div class="row">
                    <form id="cashFormSubmit" action="{{ url('admin/user/cash-payment-submit') }}" class="row" enctype="multipart/form-data">
                                @csrf
                                
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="inputName">@lang('admin.amount') <label class="text-danger">*</label></label>
                                    <input type="hidden" name="user_id" id="user_id" value="0" required />
                                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount value" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <div class="form-line">
                                    <label for="inputName">Remark <label class="text-danger">*</label></label>
                                    <textarea class="form-control" id="remark" name="remark" placeholder="Enter Remark" required></textarea>
                                </div>
                            </div>
                        </div>
                              
                        <div class="col-lg-6">
                            <div class="step-next">
                                <button type="submit" class="btn btn-dark px-4 mx-2" form="cashFormSubmit">Submit</button>
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
        var table =  $('#tablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.list.stage.two', ["$type"]) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    
                    d.email = $('.searchEmail').val()
                },
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
                    className: "text-center",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1, -2]
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
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "mobile"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "amount_in_process"
                },
                {
                    "data": "accepted_amount"
                },
                {
                    "data": "rejected_amount"
                },
                {
                    "data": "pending_amount"
                },
                {
                    "data": "payment_status"
                },
                {
                    "data": "user_type"
                },
                {
                    "data": "group_owner_name"
                },
                {
                    "data": "spouse_name"
                },
                {
                    "data": "action"
                }
            ]
        });

        $('#exampleModalCenter').on('hidden.bs.modal', function (e) {
            modalHide();
        })

        $(".searchEmail").keyup(function(){
            table.draw();
        });
    });

    function fill_datatable() {

        $('.cashPayment').click(function(){
            $('#cashPaymentModel').modal('show');
            $('#user_id').val($(this).data('id'))
        });

        $('.sendEmail').click(function() {
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.user.send.payment.reminder') }}",
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
            var status = $(this).prop('checked') == true ? 1 : 0;
            var id = $(this).data('id');

            if (status === 1) {
                $('#exampleModalCenter').modal('show');
                $('#row_id').val(id);
                $('#amount').val(null);
                return false;
            } else {
                var amount = null;
                statusChange(id, status, amount);
                return true;
            }
        });
    }

    function statusChange(id, status, amount) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.user.status') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id,
                'status': status,
                'amount': amount
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
    }

    function clickOnStatusChangeBtn() {
        var id = $('#row_id').val();
        var amount = $('#amount').val();
        if (id !== 0 && amount != '') {
            statusChange(id, 1, amount);
            modalHide();
        } else if (amount == '') {
            sweetAlertMsg('error', '403 : The amount field is required');
        } else {
            sweetAlertMsg('error', '403 : Something went wrong, please try again');
        }
    }

    function modalHide() {
        $('#exampleModalCenter').modal('hide');
        $('#row_id').val(0);
        $('#amount').val(null);
    }


    

    $("form#cashFormSubmit").submit(function(e) {
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
                $('#preloader').css('display', 'block');
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.statusTex);
                }

                $('#preloader').css('display', 'none');

            },
            success: function(data) {
                $('#cashFormSubmit')[0].reset();
                sweetAlertMsg('success', data.message);
                $('#preloader').css('display', 'none');
                $('#cashPaymentModel').modal('hide');
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
</script>
@endpush