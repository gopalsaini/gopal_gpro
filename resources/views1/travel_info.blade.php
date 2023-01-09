
@extends('layouts/app')

@section('title',__('travel-information'))

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>

                @php $result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$userId)->first(); 
                if($result){
                    $result = $result->toArray();
                }
                
                @endphp

                @if($result && $result['admin_status'] == '1')

                    @if($result['final_file'] != '')
                        <div class="row step-form">   
                            <br> <br> <br>
                            <h4>visa letter file</h4>
                            <div class="row">
                                <div class="alphabet-vd-box">
                                    <iframe width="100%" height="400
                                    "  src="{{asset('uploads/file/'.$result['final_file'])}}#toolbar=0" title="Phonics Song for Children (Official Video) Alphabet Song | Letter Sounds | Signing for babies | ASL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row step-form">  
                                    
                            @if ($result['flight_details'])
                                @if ($result['flight_details'])
                                    @php $flight_details1 = json_decode($result['flight_details']); @endphp
                                    @php $return_flight_details1 = json_decode($result['return_flight_details']); @endphp
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

                            @else
                                <h5>Travel Info Not Available</h5>
                            @endif
                        </div>
                    @endif
                    
                @elseif($result && $result['user_status'] == '1')
                    <div class="row step-form">              
                        <h4>Administration verifying visa letter information</h4>
                    </div>
                @elseif($result)
                    <div class="step-form" style="display: @if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) block @else none @endif">
                        <h4>Verify your visa letter information</h4>
                        
                            @if ($result && $result['draft_file'] != '')
                                <br> <br> <br>
                                <div class="row">
                                    <div class="alphabet-vd-box">
                                        <iframe width="100%" height="400
                                        "  src="{{asset('uploads/file/'.$result['draft_file'])}}#toolbar=0" title="Phonics Song for Children (Official Video) Alphabet Song | Letter Sounds | Signing for babies | ASL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                                    

                            @elseif($result)

                                <div class="row">
                                    
                                    @if ($result['flight_details'])
                                        @if ($result['flight_details'])
                                            @php $flight_details1 = json_decode($result['flight_details']); @endphp
                                            @php $return_flight_details1 = json_decode($result['return_flight_details']); @endphp
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

                                    @else
                                        <h5>Travel Info Not Available</h5>
                                    @endif
                                </div>
                            @endif

                        <div class="information-wrapper">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="step-next">
                                        <a href="{{ url('travel-information-verify') }}" class="main-btn bg-gray-btn" >Approved</a>
                                    
                                    </div>
                                </div>
                                @if ($result && $result['draft_file'] == '')
                                    <div class="col-lg-3">
                                        <div class="step-next">
                                            <button type="button" id="TravelInfoEdit" class="main-btn bg-gray-btn" >Edit</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                <div style="display:  none " id="TravelInfoEditDiv">
                    <form id="formSubmit1" action="{{ url('travel-information-remark-submit') }}" class="row" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" required value="@if($travelInfo['result'] && $travelInfo['result']['id']) {{$travelInfo['result']['id']}} @endif" placeholder="id" class="mt-2" >

                            <div class="arrival">
                                <h5><b>Enter Remarks -</b></h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Remark <span style="color:red">*</span></h6>
                                                <p><textarea  name="remark" required placeholder="Enter Remark" class="form-control" >@if($travelInfo['result'] && $travelInfo['result']['remark']) {{$travelInfo['result']['remark']}} @endif</textarea></p>
                                        
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-lg-12">
                                <div class="step-next">
                                    <button type="submit" class="main-btn bg-gray-btn" form="formSubmit1">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            
                @if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number'])
                    <div >
                        <div class="step-form">
                            <h4>Flight Information</h4>
                            <div class="arrival">
                                <h5><b>Arrival to Panama</b> - &nbsp; &nbsp; Attendee Name: {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info">
                                            <h6>Flight Number : </h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) {{$travelInfo['result']['arrival_flight_number']}} @endif</p>
                                        
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            <h6>Start Location :</h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_start_location']) {{$travelInfo['result']['arrival_start_location']}} @endif</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                            <h6>Date & Time of Departure : </h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_date_departure']) {{$travelInfo['result']['arrival_date_departure']}} @endif </p>

                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                            <h6>Date & Time of Arrival :</h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_date_arrival']) {{$travelInfo['result']['arrival_date_arrival']}} @endif</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="arrival">
                                <h5><b>Departure from Panama -</b></h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info">
                                           
                                            <h6>Flight Number :</h6> <br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_flight_number']) {{$travelInfo['result']['departure_flight_number']}} @endif</p>
                                           
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                           
                                            <h6>Start Location :</h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_start_location']) {{$travelInfo['result']['departure_start_location']}} @endif </p>

                                           
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                            <h6>Date & Time of Departure :</h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_date_departure']) {{$travelInfo['result']['departure_date_departure']}} @endif </p>

                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                            <h6>Date & Time of Arrival :</h6><br>
                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_date_arrival']) {{$travelInfo['result']['departure_date_arrival']}} @endif</p>

                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($SpouseInfoResult)

                                <div class="arrival">
                                    <h5><b>Arrival to Panama </b> - &nbsp; &nbsp; Spouse Name: {{$SpouseInfoResult->name}} {{$SpouseInfoResult->last_name}}</h5>
                                </div>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-md-6">
                                           
                                                <h6>Flight Number :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_flight_number']) {{$travelInfo['result']['spouse_arrival_flight_number']}} @endif </p>
                                                
                                        </div>
                                        <div class="col-md-6">
                                            
                                                <h6>Start Location :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_start_location']) {{$travelInfo['result']['spouse_arrival_start_location']}} @endif </p>

                                        </div>
                                        <div class="col-md-6">
                                            
                                                <h6>Date & Time of Departure :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_departure']) {{$travelInfo['result']['spouse_arrival_date_departure']}} @endif </p>

                                        </div>
                                        <div class="col-md-6">
                                           
                                                <h6>Date & Time of Arrival :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_arrival']) {{$travelInfo['result']['spouse_arrival_date_arrival']}} @endif </p>

                                        </div>
                                    </div>
                                </div>
                                <div class="arrival">
                                    <h5><b>Departure from Panama -</b></h5>
                                </div>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="info">
                                                
                                                <h6>Flight Number :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_flight_number']) {{$travelInfo['result']['spouse_departure_flight_number']}} @endif </p>
                                            
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="info">
                                               
                                                <h6>Start Location :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_start_location']) {{$travelInfo['result']['spouse_departure_start_location']}} @endif </p>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="info">
                                                
                                                <h6>Date & Time of Departure <br></h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_departure']) {{$travelInfo['result']['spouse_departure_date_departure']}} @endif</p>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="info">
                                                
                                                <h6>Date & Time of Arrival :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_arrival']) {{$travelInfo['result']['spouse_departure_date_arrival']}} @endif</p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="arrival">
                                <h5><b>Emergency Contact Information -</b></h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                                <h6>Mobile :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['mobile']) {{$travelInfo['result']['mobile']}} @endif </p>
                                        
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            
                                                <h6>Name :</h6><br>
                                                <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['name']) {{$travelInfo['result']['name']}} @endif </p>

                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="hotel-info">
                                <h4>Hotel Information</h4>
                                <h5>Attendee Name: {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Check In Date & Time (configurable by
                                                        Admin for certain Individuals)</h6>
                                                    <p>(Pre-Filled) – User Information Only</p>
                                                    <p>
                                                        <span>
                                                            <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                            </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Check In Date & Time (configurable by
                                                        Admin for certain Individuals)</h6>
                                                    <p>(Pre-Filled) – User Information Only</p>
                                                    <p>
                                                        <span>
                                                            <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                            </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($SpouseInfoResult)

                                    <h5>Spouse Name: {{$SpouseInfoResult->name}} {{$SpouseInfoResult->last_name}}</h5>
                                    <div class="information-wrapper">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="info">
                                                    <div class="information-box">
                                                        <h6>Check In Date & Time (configurable by
                                                            Admin for certain Individuals)</h6>
                                                        <p>(Pre-Filled) – User Information Only</p>
                                                        <p>
                                                            <span>
                                                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                                </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="info">
                                                    <div class="information-box">
                                                        <h6>Check In Date & Time (configurable by
                                                            Admin for certain Individuals)</h6>
                                                        <p>(Pre-Filled) – User Information Only</p>
                                                        <p>
                                                            <span>
                                                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                                </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                
                                    <h4>Logistics Information</h4>
                                    <div class="col-lg-12">
                                        <label for="">Would you and your spouse like to be picked by Gpro Congress from Airport:</label>
                                        <div class="radio-wrap">
                                            <div class="form__radio-group">
                                                @if($travelInfo['result'] && $travelInfo['result']['logistics_picked']) {{$travelInfo['result']['logistics_picked']}} @endif
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="">Would you and your spouse like to be dropped by Gpro Congress at Airport:</label>
                                        <div class="radio-wrap">
                                            <div class="form__radio-group">
                                                @if($travelInfo['result'] && $travelInfo['result']['logistics_dropped']) {{$travelInfo['result']['logistics_dropped']}} @endif
                                               
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                @endif
            
                <div style="display: @if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) none @else block @endif" id="TravelInfoEditDiv">
                    <form id="formSubmit" action="{{ url('travel-information-submit') }}" class="row" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" required value="@if($travelInfo['result'] && $travelInfo['result']['id']) {{$travelInfo['result']['id']}} @endif" placeholder="id" class="mt-2" >

                        <div class="step-form">
                            <h4>Flight Information</h4>
                            <div class="arrival">
                                <h5><b>Arrival to Panama</b> - &nbsp; &nbsp; Attendee Name: {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                <p>Please fill in Flight details of the Flight landing in Panama or departing from Panama.</p>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Flight Number <span style="color:red">*</span></h6>
                                                <p><input type="text" name="arrival_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) {{$travelInfo['result']['arrival_flight_number']}} @endif" placeholder="PN101" class="mt-2" ></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Start Location <span style="color:red">*</span></h6>
                                                <p><input type="text" name="arrival_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_start_location']) {{$travelInfo['result']['arrival_start_location']}} @endif" placeholder="Location" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Date & Time of Departure <span style="color:red">*</span></h6>
                                                <p><input type="datetime-local" name="arrival_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_date_departure']) {{$travelInfo['result']['arrival_date_departure']}} @endif" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Date & Time of Arrival <span style="color:red">*</span></h6>
                                                <p><input type="datetime-local" name="arrival_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_date_arrival']) {{$travelInfo['result']['arrival_date_arrival']}} @endif" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="arrival">
                                <h5><b>Departure from Panama -</b></h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Flight Number <span style="color:red">*</span></h6>
                                                
                                                <p><input type="text" name="departure_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_flight_number']) {{$travelInfo['result']['departure_flight_number']}} @endif" placeholder="PN101" class="mt-2" ></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Start Location <span style="color:red">*</span></h6>
                                                <p><input type="text" name="departure_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_start_location']) {{$travelInfo['result']['departure_start_location']}} @endif" placeholder="Location" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Date & Time of Departure <span style="color:red">*</span></h6>
                                                <p><input type="datetime-local" name="departure_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_date_departure']) {{$travelInfo['result']['departure_date_departure']}} @endif" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Date & Time of Arrival <span style="color:red">*</span></h6>
                                                <p><input type="datetime-local" name="departure_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_date_arrival']) {{$travelInfo['result']['departure_date_arrival']}} @endif" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($SpouseInfoResult)

                                <div class="arrival">
                                    <h5><b>Arrival to Panama </b> - &nbsp; &nbsp; Spouse Name: {{$SpouseInfoResult->name}} {{$SpouseInfoResult->last_name}}</h5>
                                    <p>Please fill in Flight details of the Flight landing in Panama or departing from Panama.</p>
                                </div>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Flight Number <span style="color:red">*</span></h6>
                                                    <p><input type="text" name="spouse_arrival_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_flight_number']) {{$travelInfo['result']['spouse_arrival_flight_number']}} @endif" placeholder="PN101" class="mt-2" ></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Start Location <span style="color:red">*</span></h6>
                                                    <p><input type="text" name="spouse_arrival_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_start_location']) {{$travelInfo['result']['spouse_arrival_start_location']}} @endif" placeholder="Location" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Date & Time of Departure <span style="color:red">*</span></h6>
                                                    <p><input type="datetime-local" name="spouse_arrival_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_departure']) {{$travelInfo['result']['spouse_arrival_date_departure']}} @endif" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Date & Time of Arrival <span style="color:red">*</span></h6>
                                                    <p><input type="datetime-local" name="spouse_arrival_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_arrival']) {{$travelInfo['result']['spouse_arrival_date_arrival']}} @endif" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="arrival">
                                    <h5><b>Departure from Panama -</b></h5>
                                </div>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Flight Number <span style="color:red">*</span></h6>
                                                    <p><input type="text" name="spouse_departure_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_flight_number']) {{$travelInfo['result']['spouse_departure_flight_number']}} @endif" placeholder="PN101" class="mt-2" ></p>
                                            
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Start Location <span style="color:red">*</span></h6>
                                                    <p><input type="text" name="spouse_departure_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_start_location']) {{$travelInfo['result']['spouse_departure_start_location']}} @endif" placeholder="Location" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Date & Time of Departure <span style="color:red">*</span></h6>
                                                    <p><input type="datetime-local" name="spouse_departure_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_departure']) {{$travelInfo['result']['spouse_departure_date_departure']}} @endif" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Date & Time of Arrival <span style="color:red">*</span></h6>
                                                    <p><input type="datetime-local" name="spouse_departure_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_arrival']) {{$travelInfo['result']['spouse_departure_date_arrival']}} @endif" class="mt-2" ></p>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="arrival">
                                <h5><b>Emergency Contact Information -</b></h5>
                            </div>
                            <div class="information-wrapper">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Mobile <span style="color:red">*</span></h6>
                                                <p><input type="text" onkeypress="return /[0-9 ]/i.test(event.key)"  name="mobile" required value="@if($travelInfo['result'] && $travelInfo['result']['mobile']) {{$travelInfo['result']['mobile']}} @endif" placeholder="Enter Mobile" class="mt-2" ></p>
                                        
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="info">
                                            <div class="information-box">
                                                <h6>Name <span style="color:red">*</span></h6>
                                                <p><input type="text" name="name" required value="@if($travelInfo['result'] && $travelInfo['result']['name']) {{$travelInfo['result']['name']}} @endif" placeholder="Enter Name" class="mt-2" ></p>

                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="hotel-info">
                                <h4>Hotel Information</h4>
                                <h5>Attendee Name: {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                <div class="information-wrapper">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Check In Date & Time (configurable by
                                                        Admin for certain Individuals)</h6>
                                                    <p>(Pre-Filled) – User Information Only</p>
                                                    <p>
                                                        <span>
                                                            <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                            </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="info">
                                                <div class="information-box">
                                                    <h6>Check In Date & Time (configurable by
                                                        Admin for certain Individuals)</h6>
                                                    <p>(Pre-Filled) – User Information Only</p>
                                                    <p>
                                                        <span>
                                                            <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                            </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($SpouseInfoResult)

                                    <h5>Spouse Name: {{$SpouseInfoResult->name}} {{$SpouseInfoResult->last_name}}</h5>
                                    <div class="information-wrapper">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="info">
                                                    <div class="information-box">
                                                        <h6>Check In Date & Time (configurable by
                                                            Admin for certain Individuals)</h6>
                                                        <p>(Pre-Filled) – User Information Only</p>
                                                        <p>
                                                            <span>
                                                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                                </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="info">
                                                    <div class="information-box">
                                                        <h6>Check In Date & Time (configurable by
                                                            Admin for certain Individuals)</h6>
                                                        <p>(Pre-Filled) – User Information Only</p>
                                                        <p>
                                                            <span>
                                                                <svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.35 1.2H8.25V0.6C8.25 0.44087 8.19205 0.288258 8.08891 0.175736C7.98576 0.0632141 7.84587 0 7.7 0C7.55413 0 7.41424 0.0632141 7.31109 0.175736C7.20795 0.288258 7.15 0.44087 7.15 0.6V1.2H3.85V0.6C3.85 0.44087 3.79205 0.288258 3.68891 0.175736C3.58576 0.0632141 3.44587 0 3.3 0C3.15413 0 3.01424 0.0632141 2.91109 0.175736C2.80795 0.288258 2.75 0.44087 2.75 0.6V1.2H1.65C1.21239 1.2 0.792709 1.38964 0.483274 1.72721C0.173839 2.06477 0 2.52261 0 3V10.2C0 10.6774 0.173839 11.1352 0.483274 11.4728C0.792709 11.8104 1.21239 12 1.65 12H9.35C9.78761 12 10.2073 11.8104 10.5167 11.4728C10.8262 11.1352 11 10.6774 11 10.2V3C11 2.52261 10.8262 2.06477 10.5167 1.72721C10.2073 1.38964 9.78761 1.2 9.35 1.2ZM9.9 10.2C9.9 10.3591 9.84205 10.5117 9.73891 10.6243C9.63576 10.7368 9.49587 10.8 9.35 10.8H1.65C1.50413 10.8 1.36424 10.7368 1.26109 10.6243C1.15795 10.5117 1.1 10.3591 1.1 10.2V6H9.9V10.2ZM9.9 4.8H1.1V3C1.1 2.84087 1.15795 2.68826 1.26109 2.57574C1.36424 2.46321 1.50413 2.4 1.65 2.4H2.75V3C2.75 3.15913 2.80795 3.31174 2.91109 3.42426C3.01424 3.53679 3.15413 3.6 3.3 3.6C3.44587 3.6 3.58576 3.53679 3.68891 3.42426C3.79205 3.31174 3.85 3.15913 3.85 3V2.4H7.15V3C7.15 3.15913 7.20795 3.31174 7.31109 3.42426C7.41424 3.53679 7.55413 3.6 7.7 3.6C7.84587 3.6 7.98576 3.53679 8.08891 3.42426C8.19205 3.31174 8.25 3.15913 8.25 3V2.4H9.35C9.49587 2.4 9.63576 2.46321 9.73891 2.57574C9.84205 2.68826 9.9 2.84087 9.9 3V4.8Z" fill="#58595B"/>
                                                                </svg>&nbsp; &nbsp; 12/10/2022 &nbsp; &nbsp; 10:20 am
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="arrival">
                                    <p class="note">Note: If you are reaching early, You can contact Hotel and get discounted rates. Details for same would be <br> shared with you in an email.</p>
                                    
                                </div>
                                
                                    @if($resultData['result']['added_as'] == null) 
                                        <h5>Do you know who would you like to share your room with?</h5>
                                            <div class="col-lg-6">
                                                <br><br>
                                                <select class="form-control test" name="share_your_room_with"> 
                                                    <option value="" >--@lang('web/contact-details.code')--</option>
                                                    @php 
                                                    $users = \App\Models\User::where([['status', '!=', '1']])
                                                            ->where(function ($query) {
                                                                $query->where('added_as',null)
                                                                    ->orWhere('added_as', '=', 'Group');
                                                            })->orderBy('updated_at', 'desc')->get();
                                                    @endphp

                                                    @if($users)
                                                        @foreach($users as $con)
                                                            <option value="{{$con['id']}}">{{$con['name']}} {{$con['last_name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            
                                            </div>
                                    @endif

                                    <h4>Logistics Information</h4>
                                    <div class="col-lg-12">
                                        <label for="">Would you and your spouse like to be picked by Gpro Congress from Airport:</label>
                                        <div class="radio-wrap">
                                            <div class="form__radio-group">
                                                <input type="radio" name="logistics_picked" id="yes" value="Yes" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_picked'] == 'Yes') checked @endif>
                                                <label class="form__label-radio" for="yes">
                                                <span class="form__radio-button"></span> Yes
                                                </label>
                                            </div>
                                            <div class="form__radio-group">
                                                <input type="radio" name="logistics_picked" id="no" value="No" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_picked'] == 'No') checked @endif>
                                                <label class="form__label-radio" for="no">
                                                <span class="form__radio-button"></span> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="">Would you and your spouse like to be dropped by Gpro Congress at Airport:</label>
                                        <div class="radio-wrap">
                                            <div class="form__radio-group">
                                                <input type="radio" name="logistics_dropped" id="yes2" value="Yes"  class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_dropped'] == 'Yes') checked @endif>
                                                <label class="form__label-radio" for="yes2">
                                                <span class="form__radio-button"></span> Yes
                                                </label>
                                            </div>
                                            <div class="form__radio-group">
                                                <input type="radio" name="logistics_dropped" id="no2" value="No" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_dropped'] == 'Yes') checked @endif>
                                                <label class="form__label-radio" for="no2">
                                                <span class="form__radio-button"></span> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                            <div class="col-lg-12">
                                <div class="step-next">
                                    <button type="submit" class="main-btn bg-gray-btn" form="formSubmit">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div> 
        </div>
    </div>
    <!-- banner-end -->

@endsection


@push('custom_js')

<script>

    $('.test').fSelect();

    $('#TravelInfoEdit').click(function(){
        $("#TravelInfoEditDiv").toggle();
    });

    $('#PartialPaymentOffline').click(function(){
       
        $("#PartialPaymentOfflineDiv").toggle();
        $("#PartialPaymentOnlineDiv").css('display','none');
    });

    $('#FullPaymentOffline').click(function(){
        
        $("#FullPaymentOfflineDiv").toggle();
    });
    
    $("form#formSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                $('#formSubmit')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                window.location.reload();
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    
    $("form#formSubmit1").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.statusTex);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                $('#formSubmit1')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                window.location.reload();
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });


</script>
@endpush