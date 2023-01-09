@extends('layouts/master')

@section('title',__('Stage Three '.ucfirst($type)))

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
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.two')</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.user.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>@lang('admin.add') @lang('admin.user')</a>
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
                                    <th> @lang('admin.name') </th>
                                    <th> @lang('admin.email') </th>
                                    <th> @lang('admin.mobile') </th>
                                    <th> @lang('admin.payment') </th>
                                    <!-- <th> @lang('admin.status') </th> -->
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="7">
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
                                    <th> @lang('admin.payment') </th>
                                    <!-- <th> @lang('admin.status') </th> -->
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

@endsection

@push('custom_js')

<script>
$(document).ready(function() {
    fill_datatable();
    $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.list.stage.three', [".$type."]) }}",
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
        "order": [0, 'asc'],
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
                "data": "profile"
            },
            // {
            //     "data": "status"
            // },
            {
                "data": "action"
            }
        ]
    });

    $('#exampleModalCenter').on('hidden.bs.modal', function (e) {
        modalHide();
    })
});

function fill_datatable() {
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
</script>
@endpush