@extends('layouts/master')

@section('title',__('help List'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> Help List </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page">Help</li>
                    <li class="breadcrumb-item" aria-current="page">List</li>
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
                                    <th> #ID </th>
                                    <th> Name</th>
                                    <th> Email </th>
                                    <th> Mobile</th>
                                    <th> Message </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="5">
                                        <div id="loader" class="spinner-border" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> #ID </th>
                                    <th> Name</th>
                                    <th> Email </th>
                                    <th> Mobile</th>
                                    <th> Message </th>
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
    
    $('#tablelist').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.help.list') }}",
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
        "order": [0, 'asc'],
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
                "data": "message"
            }
        ]
    });
});

</script>
@endpush