@extends('layouts/master')

@section('title',__('User Profile'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.user') @lang('admin.profile') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.profile')</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row default-according style-1" id="accordionoc">
        
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon" aria-expanded="true"
                                aria-controls="collapse11"><i class="fas fa-user"></i>
                                @lang('admin.user') @lang('admin.demographic')<span>1</span></button>
                        </h5>
                    </div>
                    <div class="collapse" id="collapseicon" aria-labelledby="collapseicon"
                            data-bs-parent="#accordionoc" style="">
                            <p class="my-2"><b>@lang('admin.personal') @lang('admin.details')</b></p>
                            <div class="px-3 table table-bordered table-hover table-responsive">
                                <table class="table table-border table-hover table-responsive">
                                    @php $Spouse =  $result['spouse']; @endphp
                                    <tbody>
                                        <tr>
                                            <td><strong>Given Name :</strong> {{$result['name']}}</td>
                                            <td><strong>Last Name :</strong> {{$result['last_name']}}</td>
                                            <td><strong>@lang('admin.email') :</strong> {{$result['email'] ?? '-'}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.dob') :</strong>
                                            {{$result['dob'] ?? '-'}}</td>
                                            <td><strong>Mobile :</strong> {{$result['mobile'] ?? '-'}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.citizenship') :</strong> {{\App\Helpers\commonHelper::getCountryNameById($result['citizenship']) ?? '-'}}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td><strong>Passport Number :</strong> {{$result['passport_number'] ?? '-'}}</td>
                                            <td><strong>Passport Copy :</strong> {!! $result['passport_copy'] ?? '-' !!}</td>
                                            
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.gender') :</strong> @if($result['gender']=='1'){{'Male'}}@elseif($result['gender']=='2'){{'Female'}}@else{{'N/A'}}@endif
                                            </td> 
                                                <td><strong>User Type :</strong> 
                                                @php 
                                                
                                                    if($result['parent_id'] != Null){

                                                        if($result['added_as'] == 'Group'){

                                                            echo  '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
                                                            
                                                        }elseif($result['added_as'] == 'Spouse'){

                                                            echo '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
                                                            
                                                        }

                                                    }
                                                @endphp
                                            </td> 
                                            
                                        </tr>
                                        @if($result['business_name'])
                                            <tr>
                                                <td><strong>Organization Name :</strong> {{$result['business_name'] ?? '-'}}</td>
                                                <td><strong>Business Identification No :</strong> {!! $result['business_identification_no'] ?? '-' !!}</td>
                                                
                                            </tr>
                                        @endif

                                        @if($result['website'])
                                            <tr>
                                                <td><strong>Website :</strong> {{$result['website'] ?? '-'}}</td>
                                                <td><strong>Logo :</strong> {!! $result['logo'] ?? '-' !!}</td>
                                                
                                            </tr>
                                        @endif
                                        <tr>

                                            @if($result['parent_id'] != Null && $result['added_as'] == 'Spouse')

                                                <td colspan="2"><strong>Are you coming along with your spouse to the Congress? :</strong> Yes
                                                </td>
                                                <td>Spouse : <a href="{{url('admin/exhibitor/profile/'.$result['parent_id'])}}" >{{ $result['parent_name']}} </a></td>
                                            @elseif($Spouse) 
                                                <td colspan="2"><strong>Are you coming along with your spouse to the Congress? :</strong> @if($Spouse) Yes @else No @endif
                                                </td>
                                                @if($Spouse)<td>Spouse : <a href="{{url('admin/exhibitor/profile/'.$Spouse['id'])}}" >{{$Spouse['name']}} {{$Spouse['last_name']}}</a></td>@endif

                                            @endif
                                            
                                           
                                        <tr>


                                    </tbody>
                                </table>
                            </div>
                            
                            
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#Sponsorship" aria-expanded="false"
                                aria-controls="collapse11"><i class="fas fa-file"></i>
                                Sponsorship Letter</button>
                        </h5>
                    </div>
                    <div class="collapse" id="Sponsorship" aria-labelledby="collapseicon2"
                            data-bs-parent="#accordionoc" style="">
                        <div class="table table-bordered table-hover table-responsive">
                            <table class="table table-border table-hover table-responsive">
                                <tbody>
                                    @php $query = \App\Models\Exhibitors::where('user_id',$result['id'])->first(); @endphp
                                    @if($query)
                                    
                                    <tr>
                                        <td colspan="5"><strong> Is this a diplomatic passport? : </strong>{{$query['diplomatic_passport']}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"><strong>Sponsorship :</strong> 
                                            @php

                                                if($query->sponsorship_letter){

                                                    echo  '<a href="'.asset('uploads/file/'.$query->sponsorship_letter).'" target="_blank" class="btn btn-sm btn-outline-success m-1">View File</a>';
                                                }else{

                                                    echo  '<div class="span badge rounded-pill pill-badge-success">N/A</div>';

                                                }
                                            @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Financial  :</strong>
                                            @php
                                                if($query->financial_letter){

                                                $financialLetter = explode(',',$query->financial_letter);

                                                echo  '<a href="'.asset('uploads/file/'.$financialLetter[0]).'" target="_blank" class="text-blue"> File 1,&nbsp; &nbsp; &nbsp;</a>
                                                        <a href="'.asset('uploads/file/'.$financialLetter[1]).'" target="_blank" class="text-blue"> File 2</a>';
                                                }else{

                                                echo '<div class="span badge rounded-pill pill-badge-success">N/A</div>';

                                                }

                                            @endphp

                                        </td>
                                            

                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($result['business_owner'] == null)
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon2" aria-expanded="false"
                                aria-controls="collapse11"><i class="fas fa-money"></i>
                                @lang('admin.payment') @lang('admin.details')<span>2</span></button>
                        </h5>
                    </div>
                    <div class="collapse" id="collapseicon2" aria-labelledby="collapseicon2"
                            data-bs-parent="#accordionoc" style="">
                        <div class="table table-bordered table-hover table-responsive">
                            <table class="table table-border table-hover table-responsive">
                                <tbody>
                                    <tr>
                                        <td><strong>@lang('admin.amount') @lang('admin.in') @lang('admin.process')
                                                :</strong>
                                            ${{ $result['AmountInProcess'] }}</td>
                                        <td><strong>@lang('admin.accepted') @lang('admin.amount') :</strong>
                                            ${{ $result['AcceptedAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('admin.rejected') @lang('admin.amount') :</strong>
                                            ${{ $result['RejectedAmount'] }}</td>
                                        <td><strong> @lang('admin.pending') @lang('admin.amount') :</strong>
                                            ${{ $result['PendingAmount'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('admin.total') @lang('admin.amount') :</strong>
                                            ${{ $result['amount'] }}</td>

                                        <td><strong>@lang('admin.payment') Refund :</strong>
                                            ${{$result['WithdrawalBalance']}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                <div class="card-header bg-primary p-2 px-1">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon3" aria-expanded="false"
                                aria-controls="collapse11"><i class="fas fa-history"></i>
                                @lang('admin.payment') @lang('admin.history')<span>3</span></button>
                        </h5>
                    </div>
                    <div class="collapse" id="collapseicon3" aria-labelledby="collapseicon3"
                            data-bs-parent="#accordionoc" style="">
                            
                        <div class="table table-bordered table-hover table-responsive">
                            <table class="display datatables" id="tablelist">
                                <thead>
                                    <tr>
                                        <th> @lang('admin.id') </th>
                                        <th> @lang('admin.user') @lang('admin.name') </th>
                                        <th> @lang('admin.created_at') </th>
                                        <th> @lang('admin.transfer-id') </th>
                                        <th> @lang('admin.utr-no') </th>
                                        <th> @lang('admin.Mode') </th>
                                        <th> @lang('admin.type') </th>
                                        <th> @lang('admin.mode') </th>
                                        <th> @lang('admin.amount') </th>
                                        <th> @lang('admin.status') </th>
                                        <th> @lang('admin.updated_at') </th>
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
                                        <th> @lang('admin.user') @lang('admin.name') </th>
                                        <th> @lang('admin.created_at') </th>
                                        <th> @lang('admin.transfer-id') </th>
                                        <th> @lang('admin.utr-no') </th>
                                        <th> @lang('admin.Mode') </th>
                                        <th> @lang('admin.type') </th>
                                        <th> @lang('admin.mode') </th>
                                        <th> @lang('admin.amount') </th>
                                        <th> @lang('admin.status') </th>
                                        <th> @lang('admin.updated_at') </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2 px-1">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon8" aria-expanded="false"
                                aria-controls="collapse118"><i class="fas fa-comments"></i>
                                @lang('admin.comment') <span>3</span></button>
                        </h5>
                    </div>
                    <div class="collapse p-3" id="collapseicon8" aria-labelledby="collapseicon8" data-bs-parent="#accordionoc" style="">
                        
                            <form id="form" action="{{ url('admin/exhibitor/exhibitor-comment-submit') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                <input type="hidden" value="@if($id){{ $id }} @else 0 @endif" name="user_id" required />
                                <input type="hidden" value="{{\Auth::user()->id}}" name="admin_id" required />
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div class="form-group">
                                            <label for="input">@lang('admin.comment'):</label>
                                            <textarea name="comment" class="form-control" cols="30" rows="5" placeholder="Enter comment here..." required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 d-flex justify-content-center align-items-center">
                                        <div class="btn-showcase text-center">
                                            <button class="btn btn-primary" type="submit" form="form">@lang('admin.submit')</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                       
                        <div class="row">
                            <div class="table table-bordered table-hover table-responsive">
                                <table class="display datatables" id="commentstablelist">
                                    <thead>
                                        <tr>
                                            <th> @lang('admin.id') </th>
                                            <th> Comment By </th>
                                            <th> @lang('admin.comment') </th>
                                            <th> @lang('admin.created_at') </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="3">
                                                <div id="loader" class="spinner-border" role="status"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th> @lang('admin.id') </th>
                                            <th> Comment By </th>
                                            <th> @lang('admin.comment') </th>
                                            <th> @lang('admin.created_at') </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2 px-1">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon9" aria-expanded="false"
                                aria-controls="collapse119"><i class="fa fa-history"></i>
                                User History Details <span>3</span></button>
                        </h5>
                    </div>
                    <div class="collapse p-3" id="collapseicon9" aria-labelledby="collapseicon9" data-bs-parent="#accordionoc" style="">
                        
                        <div class="row">
                            <div class="table table-bordered table-hover table-responsive">
                                <table class="display datatables" id="userHistoryList">
                                    <thead>
                                        <tr>
                                            <th> S. N. </th>
                                            <th> Action </th>
                                            <th>  Action Taken By </th>
                                            <th> Action Date </th>
                                            <th> Action Time </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="3">
                                                <div id="loader" class="spinner-border" role="status"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th> S. N. </th>
                                            <th> Action </th>
                                            <th>  Action Taken By </th>
                                            <th> Action Date </th>
                                            <th> Action Time </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-12">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="card-header bg-primary p-2 px-1">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon10" aria-expanded="false"
                                aria-controls="collapse120"><i class="fa fa-envelope"></i>
                                User Emails<span>3</span></button>
                        </h5>
                    </div>
                    <div class="collapse p-3" id="collapseicon10" aria-labelledby="collapseicon10" data-bs-parent="#accordionoc" style="">
                        
                        <div class="row">
                            <div class="table table-bordered table-hover table-responsive">
                                <table class="display datatables" id="userMailTriggerList">
                                    <thead>
                                        <tr>
                                            <th> S. N. </th>
                                            <th> Subject </th>
                                            <th> Action Date </th>
                                            <th> Action Time </th>
                                            <th> Action </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="3">
                                                <div id="loader" class="spinner-border" role="status"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th> S. N. </th>
                                            <th> Subject </th>
                                            <th> Action Date </th>
                                            <th> Action Time </th>
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
    </div>
</div>


<div class="modal fade " id="userMailTriggerListModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="userMailTriggerListModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userMailTriggerListModel">User Mail </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="messageMail"></span>
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
                "url": "{{ url('admin/exhibitor/get-exhibitor-payment-history/'.$id) }}",
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
                    "data": "created_at"
                },
                {
                    "data": "transaction"
                },
                {
                    "data": "utr"
                },
                {
                    "data": "bank"
                },
                {
                    "data": "type"
                },
                {
                    "data": "mode"
                },
                {
                    "data": "amount"
                },
                {
                    "data": "payment_status"
                },
                {
                    "data": "updated_at"
                }
            ]
        });

        $('#commentstablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ordering": true,

            "ajax": {
                "url": "{{ url('admin/exhibitor/get-exhibitor-comment-history') }}",
                "dataType": "json",
                "data": {
                    'user_id': "{{$id}}"
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
            ],
            "columns": [{
                    "data": null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1 + '.';
                    },
                    className: "text-center font-weight-bold"
                },
                {
                    "data": "comment_by"
                },
                {
                    "data": "comment"
                },
                {
                    "data": "created_at"
                },
            ]
        });

        
        $('#userHistoryList').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ordering": true,

            "ajax": {
                "url": "{{ url('admin/exhibitor/get-exhibitor-action-history') }}",
                "dataType": "json",
                "data": {
                    'user_id': "{{$id}}"
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
            ],
            "columns": [{
                    "data": null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1 + '.';
                    },
                    className: "text-center font-weight-bold"
                },
                {
                    "data": "action"
                },
                {
                    "data": "admin"
                },
                {
                    "data": "date"
                },
                {
                    "data": "time"
                },
            ]
        });

    });

    function fill_datatable() {

    }
    $('#other_user_id').fSelect();

    $('#other_user').change(function(){

        if(this.value == 'Yes'){
            
            $('#UserDiv').css('display','block');
            $('#ReferenceDiv').css('display','none');
            $('#other_user_id').attr('required',true);
            $('#other_user_id').val('');
            $('#referenceNumber').attr('required',false);
            $('#referenceNumber').val('');

        }else{
            
            $('#UserDiv').css('display','none');
            $('#ReferenceDiv').css('display','block');
            $('#other_user_id').attr('required',false);
            $('#referenceNumber').attr('required',true);
            $('#referenceNumber').val('');
            $('#other_user_id').val('');

        }

    });


    $('#userMailTriggerList').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ url('admin/exhibitor/get-exhibitor-user-mail-trigger-list') }}",
            "dataType": "json",
            "data": {
                'user_id': "{{$id}}"
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
        ],
        "columns": [{
                "data": null,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1 + '.';
                },
                className: "text-center font-weight-bold"
            },
            {
                "data": "subject"
            },
            {
                "data": "date"
            },
            {
                "data": "time"
            },
            {
                "data": "action"
            },
        ]

    });

    $('.messageGet').click(function() {
        $('#messageMail').html('');
        var id = $(this).data('id');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ url('admin/exhibitor/exhibitor-mail-trigger-model') }}",
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
                $('#messageMail').html(data.message);
                $('#userMailTriggerListModel').modal('show');

            }
        });
    });
</script>
@endpush