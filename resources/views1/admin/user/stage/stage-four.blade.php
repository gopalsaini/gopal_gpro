@extends('layouts/master')

@section('title',__('Stage Four '.ucfirst($type)))

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
                <h3> @lang('admin.stage') @lang('admin.four') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.four')</li>
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
                                    <th> @lang('admin.day') & @lang('admin.session') </th>
                                    <th> @lang('admin.session') @lang('admin.info') </th>
                                    <th> @lang('admin.admin') @lang('admin.status') </th>
                                    <th> @lang('admin.action') </th>
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
                                    <th> @lang('admin.user') </th>
                                    <th> @lang('admin.day') & @lang('admin.session') </th>
                                    <th> @lang('admin.session') @lang('admin.info') </th>
                                    <th> @lang('admin.admin') @lang('admin.status') </th>
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
            "url": "{{ route('admin.user.list.stage.four', ["$type"]) }}",
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
                targets: [0, 1, 3, 4 , 5]
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
                "data": "day"
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
    $('.sendEmail').click(function() {
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.user.send.session.info.reminder') }}",
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
            url: "{{ route('admin.user.session.info.status') }}",
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