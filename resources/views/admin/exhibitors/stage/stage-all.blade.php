@extends('layouts/master')

@section('title',__('Stage All '.ucfirst($type)))

@push('custom_css')
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
                <h3> Exhibitor Users </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    
                    <li class="breadcrumb-item" aria-current="page">All Exhibitor Users</li>
                </ol>
            </div>
            @if(\Auth::user()->designation_id == '1')
            <div class="col-sm-3">
                <div class="bookmark">
                    <ul>
                        <a href="{{ url('admin/user/stage-all-download-excel-file') }}" class="btn btn-outline-primary"><i class="fas fa-file me-2"></i>Download Report</a>
                    </ul>
                </div>
            </div>
            @endif
            <div class="col-sm-3">
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
                
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="email" class="form-control searchEmail " placeholder="Search ...">
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url('admin/user/attendees/stage/all') }}" class="btn btn-outline-primary"><i class="fas fa-filter"></i>Reset Filter</a>

                            </div>
                        </div>
                        <br>
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> Name </th>
                                    <th> Email </th>
                                    <th> Citizenship </th> 
                                    <th> Business Name </th>  
                                    <!-- <th> Identification Number </th>    -->
                                    <th> Mobile  </th>   
                                    <th> Website </th>   
                                    <th> Comment </th>   
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
                                    <th> Name </th>
                                    <th> Email </th>
                                    <th> Citizenship </th> 
                                    <th> Business Name </th>  
                                    <!-- <th> Identification Number </th>    -->
                                    <th> Mobile  </th>   
                                    <th> Website </th>   
                                    <th> Comment </th>     
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
                <h5 class="modal-title" id="">Exhibitor information</h5>
                <button type="button" class="close" onclick="modalHide()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="m-0">
            <form id="form" action="{{ url('admin/exhibitor/post-exhibitor-profile-status') }}" method="post">
                @csrf
                <div class="modal-body px-3">
                    <input type="hidden" name="user_id" value="0" required />
                    <input type="hidden" name="admin_id" value="{{\Auth::user()->id}}" required />
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
    fill_datatable();
    var table = $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ordering": false,

        "ajax": {
            "url": "{{ url('admin/exhibitor/get-exhibitor-user-data') }}",
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
                orderable: true,
                targets: [-1, -2]
            }
        ],
        "columns": [{
                "data": null,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + '.';
                },
                className: "text-center font-weight-bold"
            },
            {
                "data": "name",
            }, 
            {
                "data": "user_name",
            }, 
            {
                "data": "stage0"
            },
            {
                "data": "stage1"
            },
            {
                "data": "stage2"
            },
            // {
            //     "data": "stage3"
            // },
            {
                "data": "stage4"
            },
            {
                "data": "stage5"
            },
            {
                "data": "action"
            }
        ]
    });

    $(".searchEmail").keyup(function(){
        table.draw();
    });

    $('#tablelist tbody').on('click', '.group-user-list', function () {
        var email = $(this).data('email');
        
        var tr = $(this).parents('tr');
        var row = table.row(tr);
 
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {

            $('#preloader').css('display', 'block');
            $.post("{{ url('admin/exhibitor/get-group-user-data') }}", { _token: "{{ csrf_token() }}", email: email }, function(data) {
                row.child(data.html).show();
                $('#preloader').css('display', 'none');
            }, "json");

            tr.addClass('shown');
        }
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
                    type: "get",
                    dataType: "json",
                    url: "{{ url('admin/exhibitor/get-profile-base-price') }}",
                    
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