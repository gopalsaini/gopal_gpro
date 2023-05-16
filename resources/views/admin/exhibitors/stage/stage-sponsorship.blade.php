@extends('layouts/master')

@section('title',__('Sponsorship Letter'))

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
                <h3> Payment Success </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">Payment Success</li>
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
        <div class="col-md-12 mb-3">
            <div class="btn-group col-md-12">
                <a href="{{ url('admin/exhibitor/user') }}" class="btn btn-outline-primary @if ($stageno == 'all') active @endif">All User</a>
                <a href="{{ url('admin/exhibitor/payment-pending') }}" class="btn btn-outline-primary @if ($stageno == 'Payment-Pending') active @endif">Payment Pending</a>
                <a href="{{ url('admin/exhibitor/sponsorship') }}" class="btn btn-outline-primary @if ($stageno == 'sponsorship') active @endif">Payment Paid</a>
                <!-- <a href="{{ url('admin/exhibitor/qrcode') }}" class="btn btn-outline-primary @if ($stageno == 'qrcode') active @endif">QR Code </a> -->
                
            </div>
        </div>
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
                                    <th> Citizenship </th>
                                    <th> Business Name </th>
                                    <!-- <th> Identification Number </th> -->
                                    <th> Status </th>
                                    <th> Payment </th>
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
                                    <th> Citizenship </th>
                                    <th> Business Name </th>
                                    <!-- <th> Identification Number </th>  -->
                                    <th> Status </th>
                                    <th> Payment </th>
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

@endsection

@push('custom_js')

<script>
    $(document).ready(function() {
        var table1 = $('#tablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/exhibitor/get-exhibitor-payment-success') }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.searchEmail').val(),
                    d.status = 'Review'
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
                    "data": "Citizenship"
                },
                {
                    "data": "sponsorship"
                },
                // {
                //     "data": "financial"
                // },
                {
                    "data": "status"
                },
                {
                    "data": "payment"
                },
                {
                    "data": "action"
                }
            ]
        });

        
        $(".searchEmail").keyup(function(){
            table1.draw();
            
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