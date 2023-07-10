@extends('layouts/master')

@section('title',__('Room Upgrade List'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> Room Upgrade @lang('admin.list') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">Room Upgrade</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.list')</li>
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
                                    <th> @lang('admin.user') @lang('admin.name') </th>
                                    <th> Room Category </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> @lang('admin.date') & @lang('admin.time') </th>
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
                                    <th> Room Category </th>
                                    <th> @lang('admin.mode') </th>
                                    <th> @lang('admin.transfer-id') </th>
                                    <th> @lang('admin.utr-no') </th>
                                    <th> @lang('admin.amount') </th>
                                    <th> @lang('admin.payment') @lang('admin.status') </th>
                                    <th> @lang('admin.date') & @lang('admin.time') </th>
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
                "url": "{{ route('admin.room-upgrade.list') }}",
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
                    "data": "category"
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
                $('#declineRemark').modal('hide');
                $('#tablelist').DataTable().ajax.reload(null, false);
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
</script>
@endpush