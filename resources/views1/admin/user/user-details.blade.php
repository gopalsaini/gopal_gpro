@extends('layouts/master')

@section('title',__('User Details'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.user') @lang('admin.details') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.details')</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>@lang('admin.user') @lang('admin.details')</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs border-tab nav-primary" id="info-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="info-home-tab" data-bs-toggle="tab"
                                href="#info-home" role="tab" aria-controls="info-home" aria-selected="true"><i
                                    class="fas fa-user"></i>@lang('admin.user') @lang('admin.profile')</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="profile-info-tab" data-bs-toggle="tab"
                                href="#info-profile" role="tab" aria-controls="info-profile" aria-selected="false"><i
                                class="fas fa-history"></i>@lang('admin.payment')
                                @lang('admin.history')</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-info-tab" data-bs-toggle="tab"
                                href="#info-contact" role="tab" aria-controls="info-contact" aria-selected="false"><i
                                    class="fas fa-plane"></i>@lang('admin.travel') @lang('admin.info')</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" id="session-info-tab" data-bs-toggle="tab"
                                href="#info-session" role="tab" aria-controls="info-session" aria-selected="false"><i
                                class="fas fa-wrench"></i>@lang('admin.session') @lang('admin.info')</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="info-tabContent">
                        <div class="tab-pane fade active show" id="info-home" role="tabpanel"
                            aria-labelledby="info-home-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>@lang('admin.personal') @lang('admin.details')</h6>
                                            <div class="table table-bordered table-hover table-responsive">
                                                <table class="table table-border table-hover table-responsive">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>@lang('admin.name') :</strong>
                                                                {{$result->name ?? '-'}}</td>
                                                            <td><strong>@lang('admin.email') :</strong>
                                                                {{$result->email ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.mobile') :</strong>
                                                                {{$result->mobile ?? '-'}}</td>
                                                            <td><strong>@lang('admin.registration') @lang('admin.type')
                                                                    :</strong> {{$result->reg_type ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.dob') :</strong>
                                                                {{$result->dob ?? '-'}}</td>
                                                            <td><strong>@lang('admin.citizenship') :</strong>
                                                                {{$result->citizenship ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.marital') @lang('admin.status')
                                                                    :</strong> {{$result->marital_status ?? '-'}}</td>
                                                            <td><strong>@lang('admin.contact') @lang('admin.address')
                                                                    :</strong> {{$result->contact_address ?? '-'}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>@lang('admin.payment') @lang('admin.details')</h6>
                                            <div class="table table-bordered table-hover table-responsive">
                                                <table class="table table-border table-hover table-responsive">
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>@lang('admin.amount') @lang('admin.in')
                                                                    @lang('admin.process') :</strong>
                                                                {{ \App\Helpers\commonHelper::getTotalAmountInProcess($result->id) }}
                                                            </td>
                                                            <td><strong>@lang('admin.accepted') @lang('admin.amount')
                                                                    :</strong>
                                                                {{ \App\Helpers\commonHelper::getTotalAcceptedAmount($result->id) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.rejected') @lang('admin.amount')
                                                                    :</strong>
                                                                {{ \App\Helpers\commonHelper::getTotalRejectedAmount($result->id) }}
                                                            </td>
                                                            <td><strong> @lang('admin.pending') @lang('admin.amount')
                                                                    :</strong>
                                                                {{ \App\Helpers\commonHelper::getTotalPendingAmount($result->id) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.payment') @lang('admin.status')
                                                                    :</strong>
                                                                @if(\App\Helpers\commonHelper::getTotalPendingAmount($result->id))
                                                                <div class="span badge rounded-pill pill-badge-warning">
                                                                    Pending</div> @else <div
                                                                    class="span badge rounded-pill pill-badge-success">
                                                                    Completed</div> @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>@lang('admin.contact') @lang('admin.details')</h6>
                                            <div class="table table-bordered table-hover table-responsive">
                                                <table class="table table-border table-hover table-responsive">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="3"><strong>@lang('admin.contact')
                                                                    @lang('admin.address') :</strong>
                                                                {{$result->contact_address ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.contact') @lang('admin.country')
                                                                    :</strong> {{$result->contact_country_id ?? '-'}}
                                                            </td>
                                                            <td><strong>@lang('admin.contact') @lang('admin.state')
                                                                    :</strong> {{$result->contact_state_id ?? '-'}}</td>
                                                            <td><strong>@lang('admin.contact') @lang('admin.city')
                                                                    :</strong> {{$result->contact_city_id ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.contact') @lang('admin.zip')
                                                                    @lang('admin.code') :</strong>
                                                                {{$result->contact_zip_code ?? '-'}}</td>
                                                            <td><strong>@lang('admin.contact') @lang('admin.business')
                                                                    @lang('admin.number') :</strong>
                                                                {{$result->contact_business_number ?? '-'}}</td>
                                                            <td><strong>@lang('admin.contact') @lang('admin.whatsapp')
                                                                    @lang('admin.number') :</strong>
                                                                {{$result->contact_whatsapp_number ?? '-'}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>@lang('admin.ministry') @lang('admin.details')</h6>
                                            <div class="table table-bordered table-hover table-responsive">
                                                <table class="table table-border table-hover table-responsive">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2"><strong>@lang('admin.ministry')
                                                                    @lang('admin.name') :</strong>
                                                                    @if($result->ministry_name == '') Independent @else {{$result->ministry_name}} @endif</td>
                                                            <td><strong>@lang('admin.ministry') @lang('admin.zip')
                                                                    @lang('admin.code') :</strong>
                                                                {{$result->ministry_zip_code ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>@lang('admin.ministry')
                                                                    @lang('admin.address') :</strong>
                                                                {{$result->ministry_address ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.ministry') @lang('admin.country')
                                                                    :</strong> {{$result->ministry_country_id ?? '-'}}
                                                            </td>
                                                            <td><strong>@lang('admin.ministry') @lang('admin.state')
                                                                    :</strong> {{$result->ministry_state_id ?? '-'}}
                                                            </td>
                                                            <td><strong>@lang('admin.ministry') @lang('admin.city')
                                                                    :</strong> {{$result->ministry_city_id ?? '-'}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="info-profile" role="tabpanel" aria-labelledby="profile-info-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="display datatables" id="tablelist">
                                                    <thead>
                                                        <tr>
                                                            <th> @lang('admin.id') </th>
                                                            <th> @lang('admin.user') @lang('admin.name') </th>
                                                            <th> @lang('admin.created_at') </th>
                                                            <th> @lang('admin.type') </th>
                                                            <th> @lang('admin.mode') </th>
                                                            <th> @lang('admin.amount') </th>
                                                            <th> @lang('admin.status') </th>
                                                            <th> @lang('admin.updated_at') </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center" colspan="8">
                                                                <div id="loader" class="spinner-border" role="status">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th> @lang('admin.id') </th>
                                                            <th> @lang('admin.user') @lang('admin.name') </th>
                                                            <th> @lang('admin.created_at') </th>
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
                        </div>
                        <div class="tab-pane fade" id="info-contact" role="tabpanel" aria-labelledby="contact-info-tab">
                            <div class="row">
                                @if($result->TravelInfo)
                                    @if($result->TravelInfo->flight_details)
                                        @php 
                                        
                                        $flight_details = json_decode($result->TravelInfo->flight_details); @endphp
                                        @php $return_flight_details = json_decode($result->TravelInfo->return_flight_details); 
                                        
                                        @endphp
                                            @if ($flight_details)
                                            
                                                <h5 style="margin-top:20px; "><b>@lang('admin.flight') @lang('admin.details') </b></h5>
                                                <div class="row col-sm-12" style="margin-left:10px">
                                                    <h5 style="margin-top:20px; "><b>Arrival to Panama - Attendee </b></h5>
                                                    <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$flight_details->arrival_flight_number}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Start Location :</strong> {{$flight_details->arrival_start_location}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$flight_details->arrival_date_departure}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$flight_details->arrival_date_arrival}}</p></div>

                                                    <h5 style="margin-top:20px; "><b>Departure from Panama - </b></h5>
                                                    <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$flight_details->departure_flight_number}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Start Location :</strong> {{$flight_details->departure_start_location}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$flight_details->departure_date_departure}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$flight_details->departure_date_arrival}}</p></div>
                                                </div>
                                            @endif
                                            @if ($return_flight_details)
                                                <div class="row col-sm-12" style="margin-left:10px">
                                                    <h5 style="margin-top:20px; "><b>Arrival to Panama -   Spouse</b></h5>
                                                    <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$return_flight_details->spouse_arrival_flight_number}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Start Location :</strong> {{$return_flight_details->spouse_arrival_start_location}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$return_flight_details->spouse_arrival_date_departure}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$return_flight_details->spouse_arrival_date_arrival}}</p></div>

                                                    <h5 style="margin-top:20px; "><b>Departure from Panama - </b></h5>
                                                    <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$return_flight_details->spouse_departure_flight_number}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Start Location :</strong> {{$return_flight_details->spouse_departure_start_location}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$return_flight_details->spouse_departure_date_departure}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$return_flight_details->spouse_departure_date_arrival}}</p></div>
                                                </div>
                                        
                                        
                                            @endif

                                            @if ($result->TravelInfo->mobile)
                                            
                                                <h5 style="margin-top:20px; "><b>Emergency Contact Information </b></h5>
                                                <div class="row col-sm-12" style="margin-left:10px">
                                                    <div class="col-sm-4"><p><strong> Mobile :</strong> {{$result->TravelInfo->mobile}}</p></div>
                                                    <div class="col-sm-4"><p><strong> Name :</strong> {{$result->TravelInfo->name}}</p></div>
                                                </div>
                                            @endif
                                        @endif

                                    @if ($result->TravelInfo->hotel_information)
                                        @php $hotel_information = json_decode($result->TravelInfo->hotel_information); @endphp
                                        @if (count($hotel_information) > 0)
                                            @foreach ($hotel_information as $key => $hotel_info)
                                                <h5 style="margin-top:20px"><b>@lang('admin.hotel') @lang('admin.information')</b></h5>
                                                <div class="row col-sm-12" style="margin-left:10px">
                                                    @foreach ($hotel_info as $index => $info)
                                                        <div class="col-sm-4"><p><strong>@lang('admin.'.$index) :</strong> {{$info}}</p></div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif

                                    <h5 style="margin-top:20px;"><b>@lang('admin.status')</b></h5>
                                    <div class="row col-sm-12" style="margin-left:10px">
                                        <div class="col-sm-6">
                                            <p><strong>@lang('admin.travel') @lang('admin.information') :</strong> 
                                                @if ($result->TravelInfo)
                                                    @if ($result->TravelInfo->user_status == '1')
                                                        <div class="span badge rounded-pill pill-badge-success">Verify</div>
                                                    @elseif ($result->TravelInfo->user_status == '0')
                                                        <div class="span badge rounded-pill pill-badge-danger">Reject</div>
                                                    @elseif ($result->TravelInfo->user_status === null)
                                                        <div class="span badge rounded-pill pill-badge-warning">In Process</div>
                                                    @endif
                                                @else
                                                    <div class="span badge rounded-pill pill-badge-warning">Pending</div>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p><strong>@lang('admin.admin') @lang('admin.status') :</strong>
                                                @if ($result->TravelInfo)
                                                    @if ($result->TravelInfo->admin_status == '1')
                                                        <div class="span badge rounded-pill pill-badge-success">Approved</div>
                                                    @elseif ($result->TravelInfo->admin_status == '0')
                                                        <div class="span badge rounded-pill pill-badge-danger">Reject</div>
                                                    @elseif ($result->TravelInfo->admin_status === null)
                                                        <div class="span badge rounded-pill pill-badge-warning">Pending</div>
                                                    @endif
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <h5>Travel Info Not Available</h5>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="info-session" role="tabpanel" aria-labelledby="session-info-tab">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table table-bordered table-hover table-responsive">
                                                <table class="table table-border table-hover table-responsive">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2"><strong>@lang('admin.name') :</strong>
                                                                {{$result->name ?? '-'}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.day') & @lang('admin.session') :</strong></td>
                                                            <td>
                                                                @php
                                                                if (count($result->SessionInfo) > 0) {
                                                                    $day = '';
                                                                    foreach ($result->SessionInfo as $dayValue) {
                                                                        
                                                                        if($dayValue->session_id != ''){

                                                                            $sesions = explode(',',$dayValue->session_id);

                                                                            if(!empty($sesions) && count($sesions) >0){

                                                                                foreach ($sesions as $sessionId) {
                                                                                    
                                                                                    $sessionInfo = \App\Models\DaySession::where('id',$sessionId)->first();

                                                                                    if($sessionInfo){


                                                                                        $day .= 'Date :'.$dayValue->day.', ';
                                                                                        $day .= 'Name :'.$sessionInfo->session_name.', ';
                                                                                        $day .= 'Session Join :'.$dayValue->session.', ';
                                                                                        $day .= 'Start Time :'.$sessionInfo->start_time.', ';
                                                                                        $day .= 'End Time :'.$sessionInfo->end_time;
                                                                                        $day .= '<br>';
                                                                                    }
                                                                                }
                                                                                
                                                                            }
                                                                                
                                                                        }
                                                                            
                                                                        
                                                                    }
                                                                    echo $day;
                                                                }else {
                                                                    echo '-';
                                                                }
                                                                @endphp
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>@lang('admin.session') @lang('admin.information')
                                                                    :</strong>
                                                                @if (count($result->SessionInfo) > 0)
                                                                @if ($result->SessionInfo[0]->admin_status == '1')
                                                                <div class="span badge rounded-pill pill-badge-success">
                                                                    Verify</div>
                                                                @elseif ($result->SessionInfo[0]->admin_status == '0')
                                                                <div class="span badge rounded-pill pill-badge-danger">
                                                                    Reject</div>
                                                                @elseif ($result->SessionInfo[0]->admin_status === null)
                                                                <div class="span badge rounded-pill pill-badge-warning">
                                                                    In Process</div>
                                                                @endif
                                                                @else
                                                                <div class="span badge rounded-pill pill-badge-warning">
                                                                    Pending</div>
                                                                @endif
                                                            </td>
                                                            <td><strong>@lang('admin.admin') @lang('admin.status')
                                                                    :</strong>
                                                                @if (count($result->SessionInfo) > 0)
                                                                @if ($result->SessionInfo[0]->admin_status == '1')
                                                                <div class="span badge rounded-pill pill-badge-success">
                                                                    Approved</div>
                                                                @elseif ($result->SessionInfo[0]->admin_status == '0')
                                                                <div class="span badge rounded-pill pill-badge-danger">
                                                                    Reject</div>
                                                                @elseif ($result->SessionInfo[0]->admin_status === null)
                                                                <div class="span badge rounded-pill pill-badge-warning">
                                                                    Pending</div>
                                                                @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            "url": "{{ route('admin.user.payment.history', [$id]) }}",
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
        }, ],
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
                "data": "type"
            },
            {
                "data": "mode"
            },
            {
                "data": "amount"
            },
            {
                "data": "status"
            },
            {
                "data": "updated_at"
            }
        ]
    });
});

function fill_datatable() {

}
</script>
@endpush