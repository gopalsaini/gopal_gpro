@extends('layouts/master')

@section('title',__('Stage One '.ucfirst($type)))

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
                <h3> @lang('admin.stage') @lang('admin.one') @lang('admin.'.$type) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$type)</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.one')</li>
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
                <div class="card-header">
                    <h5>@lang('admin.pending')</h5>
                </div>
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
                                    <th> Status </th>
                                    <th> Group </th>
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
                                    <th> Status </th>
                                    <th> Group </th>
                                    <!-- <th> @lang('admin.status') </th> -->
                                    <th> @lang('admin.action') </th>
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
                    <h5>@lang('admin.waiting')</h5>
                </div>
                <div class="card-body">
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

        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>@lang('admin.rejected')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist2">
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
                                    <th> Status </th>
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
                <h5 class="modal-title" id="exampleModalLongTitle">User Profile Status</h5>
                <button type="button" class="close" onclick="modalHide()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="m-0">
            <form id="form" action="{{ route('admin.user.profile.status') }}" method="post">
                @csrf
                <div class="modal-body px-3">
                    <input type="hidden" name="user_id" value="0" required />
                    <input type="hidden" name="status" required />

                    <div class="row" >
                        <div class="col-sm-12">
                            <div id="ProfileStatusData"></div>

                            <div class="form-group">
                                <div class="form-line">
                                    <label for="inputName">@lang('admin.remark')</label>
                                    <textarea name="remark" class="form-control" cols="30" rows="5" placeholder="Enter remark here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger px-4 mx-2" onclick="modalHide()">@lang('admin.close')</button>
                    <button type="submit" class="btn btn-dark px-4 mx-2">@lang('admin.save')</button>
                </div>
            </form>
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
                "url": "{{ route('admin.user.list.stage.one', ["$type"]) }}",
                "dataType": "json",
                
                data: function (d) {
                    d.email = $('.searchEmail').val(),
                    d.status = 'Review'
                },
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
                    "data": "group"
                },
                {
                    "data": "action"
                }
            ]
        });

        
        $(".searchEmail").keyup(function(){
            table.draw();
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

        $('#tablelist1').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.list.stage.one', ["$type"]) }}",
                "dataType": "json",
                "data": {
                    status: 'Waiting'
                },
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

        $('#tablelist2').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.list.stage.one', ["$type"]) }}",
                "dataType": "json",
                "data": {
                    status: 'Rejected'
                },
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
        });
    });

    function fill_datatable() {
        $('.profile-status').click(function() {
            var id = $(this).data('id');
            
            var status = $(this).data('status');

            if (status == 'Approved') {
                $('.approved-section').show();

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{url('admin/user/get-profile-base-price')}}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': id,
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
                        $('#ProfileStatusData').html(data.html);
                    }
                });


            } else {
                $('.approved-section').hide();
            }

            $("#exampleModalLongTitle").html('User Profile '+status);
            $('#exampleModalCenter').modal('show');
            $('input[name="user_id"]').val(id);
            $('input[name="status"]').val(status);
            return false;

        });
    }

    function modalHide() {
        $('#exampleModalCenter').modal('hide');
        $('input[name="user_id"]').val(0);
        $('input[name="status"]').val(null);
        $('form#form')[0].reset();
    }

    

    
</script>
@endpush