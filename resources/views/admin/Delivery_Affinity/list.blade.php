@extends('layouts/master')

@section('title',__('Delivery Affinity'))

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
                <h3> Delivery Affinity </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">Admin</li>
                    <li class="breadcrumb-item" aria-current="page">List</li>
                </ol>
            </div>
            
			<div class="col-sm-6">
				<div class="bookmark">
					<ul>
						<a href="{{ route('admin.Delivery_Affinity.add')}}" class="btn btn-primary"><i class="fas fa-list me-2"></i> Add</a>
					</ul>
				</div>
			</div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <input type="text" name="email" class="form-control searchEmailSubAdmin" placeholder="Search ...">
                            <br>
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> S.N. </th>
                                    <th> Name </th>
                                    <th> Status</th>
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
                                    <th> S.N. </th>
                                    <th> Name </th>
                                    <th> Status</th>
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
    var table = $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.Delivery_Affinity.list') }}",
            "dataType": "json",
            "async": false,
            "type": "get",
            data: function (d) {
                d.email = $('.searchEmailSubAdmin').val()
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
                "data": "status"
            },
        ]
    });

    $(".searchEmailSubAdmin").keyup(function(){
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
            $.post("{{ route('admin.Delivery_Affinity.get-user-data') }}", { _token: "{{ csrf_token() }}", id: email }, function(data) {
                row.child(data.html).show();
                $('#preloader').css('display', 'none');
            }, "json");

            tr.addClass('shown');
        }
    });

});

function fill_datatable() {
    $('.-change').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.Delivery_Affinity.status')}}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id,
                'status': status
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
</script>
@endpush