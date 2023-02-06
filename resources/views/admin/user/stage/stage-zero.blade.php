@extends('layouts/master')

@section('title',__('Stage Zero '.ucfirst($type)))

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
                <h3> @lang('admin.stage') @lang('admin.zero') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.zero')</li>
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
                                    <th> Name </th>
                                    <th> @lang('admin.email') </th>
                                    <th> Status </th>
                                    <th> User Type </th>
                                    <th> Group Owner Name </th>
                                    <th> Spouse Name </th>
                                    <th> @lang('admin.action') </th>
                                    <th> Created on </th>
                                    <th> Updated on </th>
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
                                    <th> Name </th>
                                    <th> @lang('admin.email') </th>
                                    <th> Status </th>
                                    <th> User Type </th>
                                    <th> Group Owner Name </th>
                                    <th> Spouse Name </th>
                                    <th> @lang('admin.action') </th>
                                    <th> Created on </th>
                                    <th> Updated on </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Spouse pending confirmation </h5>
            </div>
            <div class="card-body">
                <input type="text" name="email" class="form-control searchEmailSpousePending " placeholder="Search ...">
                <br>
                <div class="table-responsive">
                    <table class="display datatables" id="tablelist1">
                        <thead>
                            <tr>
                                <th> @lang('admin.id') </th>
                                <th> @lang('admin.name') </th>
                                <th> @lang('admin.email') </th>
                                <th> @lang('admin.mobile') </th>
                                <th> Status </th>
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
                                <th> Status</th>
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
@endsection

@push('custom_js')

<script>
    $(document).ready(function() {
      
        var table = $('#tablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.list.stage.zero', ["$type"]) }}",
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
            "columnDefs": [{
                    className: "text-center",
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
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "profile"
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
                },
                {
                    "data": "created_at"
                },
                {
                    "data": "updated_at"
                },
            ]
        });

        $(".searchEmail").keyup(function(){
            table.draw();
        });

    });

    function fill_datatable() {

       
        $('.sendEmail').click(function() {
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.user.send.profile.update.reminder') }}",
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

        
        
    }

    

    var table = $('#tablelist1').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.spouse.pending')}}",
                "dataType": "json",
                
                "async": false,
                "type": "get",
                data: function (d) {
                    
                    d.email = $('.searchEmailSpousePending').val(),
                    d.status = 'Waiting'
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

        $(".searchEmailSpousePending").keyup(function(){
            table.draw();
        });

</script>
@endpush