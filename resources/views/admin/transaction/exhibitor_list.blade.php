@extends('layouts/master')

@section('title',__('Transaction List'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.transaction') @lang('admin.list') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.transaction')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.list')</li>
                </ol>
            </div>
            @if(\Auth::user()->designation_id == '1')
            <div class="col-sm-3">
                <div class="bookmark">
                    <ul>
                        <a href="{{ url('admin/user/transaction-data/download') }}" class="btn btn-outline-primary"><i class="fas fa-file me-2"></i>Download </a>
                    </ul>
                </div>
            </div>
            @endif
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
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.payment') @lang('admin.by') </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> @lang('admin.date') & @lang('admin.time') </th>
                                    <th> Decline Remark</th>
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
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> @lang('admin.payment') @lang('admin.by') </th>
                                    
                                    <th> @lang('admin.mode') </th>
                                    
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> @lang('admin.date') & @lang('admin.time') </th>
                                    <th> Decline Remark</th>
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


@endsection

@push('custom_js')

<script>
    $(document).ready(function() {
        fill_datatable();
        $('#tablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/exhibitor/exhibitor-transaction-list') }}",
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
                    "data": "user_name"
                },
                {
                    "data": "payment_by"
                },
                {
                    "data": "method"
                },
                {
                    "data": "transaction_id"
                },
                {
                    "data": "bank_transaction_id"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "payment_status"
                },
                {
                    "data": "created_at"
                },
                {
                    "data": "decline_remark"
                },
                {
                    "data": "action"
                }
            ]
        });
    });

    function fill_datatable() {

        $('.declineRemark').click(function(){
           
            $('#declineRemark').modal('show');
            $('#transaction_id').val($(this).data('id'));
            $('#status').val($(this).data('type'));
        });


        $('.-change').click(function() {
            var status = $(this).data('type');
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.transaction.status') }}",
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


</script>
@endpush