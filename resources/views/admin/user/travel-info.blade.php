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
                        <div class="col-sm-4"><p><strong> Airline Name :</strong> {{$flight_details->arrival_airline_name}}</p></div>

                        <h5 style="margin-top:20px; "><b>Departure from Panama - </b></h5>
                        <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$flight_details->departure_flight_number}}</p></div>
                        <div class="col-sm-4"><p><strong> Start Location :</strong> {{$flight_details->departure_start_location}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$flight_details->departure_date_departure}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$flight_details->departure_date_arrival}}</p></div>
                        <div class="col-sm-4"><p><strong> Airline Name :</strong> {{$flight_details->departure_airline_name}}</p></div>
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

            
            <h5 style="margin-top:20px"><b>Do you know who would you like to share your room with?</b></h5>
            <div class="col-lg-12" style="margin-left:10px">
                <div class="radio-wrap">
                    <div class="form__radio-group">
                        @if($result->share_your_room_with) {{\App\Helpers\commonHelper::getUserNameById($result->share_your_room_with)}}  @else N/A @endif
                        
                    </div>
                </div>
            </div>


        @else
            <h5>Travel Info Not Available</h5>
        @endif
    </div>
</div>

@endsection