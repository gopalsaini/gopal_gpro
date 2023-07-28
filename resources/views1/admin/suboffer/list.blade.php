@extends('layouts/master')

@section('title',__('Sub Offers List'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> {{ \App\Helpers\commonHelper::getDataById('Offer', $offer_id, 'name') }} @lang('admin.sub') @lang('admin.offer') @lang('admin.list') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">{{ \App\Helpers\commonHelper::getDataById('Offer', $offer_id, 'name') }}</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.sub') @lang('admin.offer')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.list')</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.sub.offer.add', [$offer_id]) }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>@lang('admin.add') @lang('admin.sub') @lang('admin.offer')</a>
                    </ul>
                </div>
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
                                    <th> @lang('admin.offer') @lang('admin.code') </th>
                                    <th> @lang('admin.name') </th>
                                    <th> @lang('admin.initial') @lang('admin.amount') </th>
                                    <th> @lang('admin.final') @lang('admin.amount') </th>
                                    <th> @lang('admin.instant') @lang('admin.discount') </th>
                                    <th> @lang('admin.status') </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="8">
                                        <div id="loader" class="spinner-border" role="status"></div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> @lang('admin.offer') @lang('admin.code') </th>
                                    <th> @lang('admin.name') </th>
                                    <th> @lang('admin.initial') @lang('admin.amount') </th>
                                    <th> @lang('admin.final') @lang('admin.amount') </th>
                                    <th> @lang('admin.instant') @lang('admin.discount') </th>
                                    <th> @lang('admin.status') </th>
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
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.sub.offer.list', [$offer_id]) }}",
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
                "data": "offer_code"
            },
            {
                "data": "name"
            },
            {
                "data": "initial_amount"
            },
            {
                "data": "final_amount"
            },
            {
                "data": "instant_discount"
            },
            {
                "data": "status"
            },
            {
                "data": "action"
            }
        ]
    });
});

function fill_datatable() {
    $('.-change').change(function() {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.sub.offer.status', [$offer_id]) }}",
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