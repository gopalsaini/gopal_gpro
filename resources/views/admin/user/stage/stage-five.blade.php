@extends('layouts/master')

@section('title',__('Stage Five '.ucfirst($type)))

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
                <h3> @lang('admin.stage') @lang('admin.four') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.five')</li>
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
                                    <th> @lang('admin.profile') </th>
                                    <th> @lang('admin.payment') </th>
                                    <th> @lang('admin.travel') @lang('admin.info') </th>
                                    <th> @lang('admin.session') @lang('admin.info') </th>
                                    <th> Group </th>
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
                                    <th> @lang('admin.user') </th>
                                    <th> @lang('admin.profile') </th>
                                    <th> @lang('admin.payment') </th>
                                    <th> @lang('admin.travel') @lang('admin.info') </th>
                                    <th> @lang('admin.session') @lang('admin.info') </th>
                                    <th> Group </th>
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

    var table =   $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": false,

        "ajax": {
            "url": "{{ route('admin.user.list.stage.five', ["$type"]) }}",
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
                "data": "profile"
            },
            {
                "data": "payment"
            },
            {
                "data": "travel_info"
            },
            {
                "data": "session_info"
            },
            {
                "data": "group"
            },
            {
                "data": "action"
            }
        ]
    });
   
    $('#tablelist tbody').on('click', '.group', function () {

        var email = $(this).data('email');

        var tr = $(this).parents('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {

            $('#preloader').css('display', 'block');
            $.post("{{ route('admin.user.group.users.list') }}", { _token: "{{ csrf_token() }}", email: email }, function(data) {
                row.child(data.html).show();
                $('#preloader').css('display', 'none');
            }, "json");

            tr.addClass('shown');
        }
    });
});

function fill_datatable() {
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