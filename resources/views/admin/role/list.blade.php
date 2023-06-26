@extends('layouts/master')

@section('title',__('Role'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> Role </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">Admin</li>
                    <li class="breadcrumb-item" aria-current="page">List</li>
                </ol>
            </div>
            
			<div class="col-sm-6">
				<div class="bookmark">
					<ul>
						<a href="{{ route('admin.role.add')}}" class="btn btn-primary"><i class="fas fa-list me-2"></i> Add</a>
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
                                    <th> Action </th>
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
                                    <th> Action </th>
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
            "url": "{{ route('admin.role.list') }}",
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
            {
                "data": "action"
            },
        ]
    });

    $(".searchEmailSubAdmin").keyup(function(){
        table.draw();
    });
});

function fill_datatable() {
    $('.-change').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.role.status')}}",
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