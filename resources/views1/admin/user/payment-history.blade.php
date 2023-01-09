@extends('layouts/master')

@section('title',__('Payment History'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.user') @lang('admin.payment') @lang('admin.history') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.payment') @lang('admin.history')</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="6">
                                        <div id="loader" class="spinner-border" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Sponsored Payment</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist_sponsor">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
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
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Donate Payment</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist_donate">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
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
                                    <th> @lang('admin.created_at') </th>
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.Mode') </th>
                                    <th> @lang('admin.type') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.updated_at') </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
            "url": "{{ route('admin.user.payment.history', [$id]) }}",
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
                className: "text-center",
                targets: "_all"
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
                "data": "created_at"
            },
            {
                "data": "user_name"
            },
            {
                "data": "transaction"
            },
            {
                "data": "utr"
            },
            {
                "data": "bank"
            },
            {
                "data": "type"
            },
            {
                "data": "mode"
            },
            {
                "data": "amount"
            },
            {
                "data": "payment_status"
            },
            {
                "data": "updated_at"
            }
        ]
    });

    $('#tablelist_sponsor').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.sponsored.Payment.History', [$id]) }}",
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
                className: "text-center",
                targets: "_all"
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
                "data": "created_at"
            },
            {
                "data": "user_name"
            },
            {
                "data": "transaction"
            },
            {
                "data": "utr"
            },
            {
                "data": "bank"
            },
            {
                "data": "type"
            },
            {
                "data": "mode"
            },
            {
                "data": "amount"
            },
            {
                "data": "payment_status"
            },
            {
                "data": "updated_at"
            }
        ]
    });

    $('#tablelist_donate').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.donate.Payment.History', [$id]) }}",
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
                className: "text-center",
                targets: "_all"
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
                "data": "created_at"
            },
            {
                "data": "user_name"
            },
            {
                "data": "transaction"
            },
            {
                "data": "utr"
            },
            {
                "data": "bank"
            },
            {
                "data": "type"
            },
            {
                "data": "mode"
            },
            {
                "data": "amount"
            },
            {
                "data": "payment_status"
            },
            {
                "data": "updated_at"
            }
        ]
    });
});

function fill_datatable() {

}
</script>
@endpush