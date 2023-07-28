<div class="row">
    <h5 style="margin-bottom:20px;"><b>@lang('admin.personal') @lang('admin.information')</b></h5>
    <div class="row col-sm-12" style="margin-left:10px">
        <div class="col-sm-6"><p><strong>@lang('admin.name') :</strong> {{$name ?? '-'}}</p></div>
        <div class="col-sm-6"><p><strong>@lang('admin.email') :</strong> {{$email ?? '-'}}</p></div>
    </div>
    @if ($flight_details)
        @if ($flight_details)
            @php $flight_details1 = json_decode($flight_details); @endphp
            @php $return_flight_details1 = json_decode($return_flight_details); @endphp
                @if ($flight_details1)
                
                    <h5 style="margin-top:20px; "><b>@lang('admin.flight') @lang('admin.details') </b></h5>
                    <div class="row col-sm-12" style="margin-left:10px">
                        <h5 style="margin-top:20px; "><b>Arrival to Panama - Attendee </b></h5>
                        <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$flight_details1->arrival_flight_number}}</p></div>
                        <div class="col-sm-4"><p><strong> Start Location :</strong> {{$flight_details1->arrival_start_location}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$flight_details1->arrival_date_departure}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$flight_details1->arrival_date_arrival}}</p></div>

                        <h5 style="margin-top:20px; "><b>Departure from Panama - </b></h5>
                        <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$flight_details1->departure_flight_number}}</p></div>
                        <div class="col-sm-4"><p><strong> Start Location :</strong> {{$flight_details1->departure_start_location}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$flight_details1->departure_date_departure}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$flight_details1->departure_date_arrival}}</p></div>
                    </div>
                @endif

                @if($return_flight_details1)
                
                    <div class="row col-sm-12" style="margin-left:10px">
                        <h5 style="margin-top:20px; "><b>Arrival to Panama -   Spouse</b></h5>
                        <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$return_flight_details1->spouse_arrival_flight_number}}</p></div>
                        <div class="col-sm-4"><p><strong> Start Location :</strong> {{$return_flight_details1->spouse_arrival_start_location}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$return_flight_details1->spouse_arrival_date_departure}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$return_flight_details1->spouse_arrival_date_arrival}}</p></div>

                        <h5 style="margin-top:20px; "><b>Departure from Panama - </b></h5>
                        <div class="col-sm-4"><p><strong> Flight Number :</strong> {{$return_flight_details1->spouse_departure_flight_number}}</p></div>
                        <div class="col-sm-4"><p><strong> Start Location :</strong> {{$return_flight_details1->spouse_departure_start_location}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Departure :</strong> {{$return_flight_details1->spouse_departure_date_departure}}</p></div>
                        <div class="col-sm-4"><p><strong> Date & Time of Arrival :</strong> {{$return_flight_details1->spouse_departure_date_arrival}}</p></div>
                    </div>
                @endif
                
            
        @endif

        @if ($hotel_information)
            @php $hotel_information = json_decode($hotel_information); @endphp
            @if (count($hotel_information) > 0)
                @foreach ($hotel_information as $key => $hotel_info)
                    <h5 style="margin-bottom:20px"><b>@lang('admin.hotel') {{$key+1}} @lang('admin.information')</b></h5>
                    <div class="row col-sm-12" style="margin-left:10px">
                        @foreach ($hotel_info as $index => $info)
                            <div class="col-sm-6"><p><strong>@lang('admin.'.$index) :</strong> {{$info}}</p></div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        @endif

        <h5 style="margin-bottom:20px;"><b>@lang('admin.status')</b></h5>
        <div class="row col-sm-12" style="margin-left:10px">
            <div class="col-sm-6">
                <p><strong>@lang('admin.travel') @lang('admin.information') :</strong> 
                    @if ($flight_details)
                        @if ($user_status == '1')
                            <div class="span badge rounded-pill pill-badge-success">Verify</div>
                        @elseif ($user_status == '0')
                            <div class="span badge rounded-pill pill-badge-danger">Reject</div>
                        @elseif ($user_status === null)
                            <div class="span badge rounded-pill pill-badge-warning">In Process</div>
                        @endif
                    @else
                        <div class="span badge rounded-pill pill-badge-warning">Pending</div>
                    @endif
                </p>
            </div>
            <div class="col-sm-6">
                <p><strong>@lang('admin.admin') @lang('admin.status') :</strong>
                    @if ($flight_details)
                        @if ($admin_status == '1')
                            <div class="span badge rounded-pill pill-badge-success">Approved</div>
                        @elseif ($admin_status == '0')
                            <div class="span badge rounded-pill pill-badge-danger">Reject</div>
                        @elseif ($admin_status === null)
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