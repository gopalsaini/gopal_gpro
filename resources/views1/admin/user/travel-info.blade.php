@extends('layouts/master')

@section('title',__('Travel Information'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.travel') @lang('admin.information') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.travel')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.information')</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">


        @if($result->TravelInfo && $result->TravelInfo->admin_status == '1')

            @if($result->TravelInfo->final_file != '')

                <div class="row step-form">   
                    <br>
                    <h4>visa letter file</h4>
                    <div class="row">
                        <div class="alphabet-vd-box">
                            <iframe width="100%" height="400
                            "  src="{{asset('uploads/file/'.$result->TravelInfo->final_file)}}#toolbar=0" title="Phonics Song for Children (Official Video) Alphabet Song | Letter Sounds | Signing for babies | ASL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

            @endif

        @else
            @if($result->TravelInfo && $result->TravelInfo->draft_file != '')
                <br> 
                <div class="row">
                    <div class="alphabet-vd-box">
                        <iframe width="100%" height="400
                        "  src="{{asset('uploads/file/'.$result->TravelInfo->draft_file)}}#toolbar=0" title="Phonics Song for Children (Official Video) Alphabet Song | Letter Sounds | Signing for babies | ASL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>

            @endif

        @endif
        <h5 style="margin-bottom:20px;"><b>@lang('admin.personal') @lang('admin.information')</b></h5>
        <div class="row col-sm-12" style="margin-left:10px">
            <div class="col-sm-4"><p><strong>@lang('admin.name') :</strong> {{$result->name}}</p></div>
            <div class="col-sm-4"><p><strong>@lang('admin.email') :</strong> {{$result->email}}</p></div>
            <div class="col-sm-4"><p><strong>@lang('admin.mobile') :</strong> {{$result->mobile}}</p></div>
        </div>
        @if ($result->TravelInfo)
            @if ($result->TravelInfo->flight_details)
                @php $flight_details = json_decode($result->TravelInfo->flight_details); @endphp
                @php $return_flight_details = json_decode($result->TravelInfo->return_flight_details); @endphp
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
                @if($return_flight_details)
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

@endsection