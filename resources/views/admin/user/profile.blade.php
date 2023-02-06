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
                            <button class="btn btn-link text-white" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon0" aria-expanded="true"
                                aria-controls="collapse1"><i class="fas fa-database"></i>
                                @lang('admin.stage') <span>0</span></button>
                        </h5>
                    </div>
                    <div class="collapse show" id="collapseicon0" aria-labelledby="collapseicon0"
                            data-bs-parent="#accordionoc" style="">
                            <div class="table table-bordered table-hover table-responsive">
                                <table class="table table-border table-hover table-responsive">
                                    <tbody>
                                        <tr>      
                                            <td><strong>@lang('admin.stage') 0 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 0) pill-badge-secondary @elseif($result->stage > 0) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 0) In Process @elseif($result->stage > 0) Completed @else Pending @endif </div>
                                            </td> 
                                            <td><strong>@lang('admin.stage') 1 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 1) pill-badge-secondary @elseif($result->stage > 1) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 1) In Process @elseif($result->stage > 1) Completed @else Pending @endif </div>
                                            </td> 
                                            <td><strong>@lang('admin.stage') 2 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 2) pill-badge-secondary @elseif($result->stage > 2) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 2) In Process @elseif($result->stage > 2) Completed @else Pending @endif </div>
                                            </td> 
                                            <td><strong>@lang('admin.stage') 3 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 3) pill-badge-secondary @elseif($result->stage > 3) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 3) In Process @elseif($result->stage > 3) Completed @else Pending @endif </div>
                                            </td> 
                                            <td><strong>@lang('admin.stage') 4 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 4) pill-badge-secondary @elseif($result->stage > 4) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 4) In Process @elseif($result->stage > 4) Completed @else Pending @endif </div>
                                            </td> 
                                            <td><strong>@lang('admin.stage') 5 :</strong>
                                                <div class="span badge rounded-pill @if($result->stage == 5) pill-badge-secondary @elseif($result->stage > 5) pill-badge-success @else pill-badge-warning @endif">@if($result->stage == 5) In Process @elseif($result->stage > 5) Completed @else Pending @endif </div>
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
                                    @php $Spouse = \App\Models\User::where('parent_id',$result->id)->where('added_as','Spouse')->first(); @endphp
                                    <tbody>
                                        <tr>
                                            <td><strong>@lang('admin.name') :</strong> {{$result->salutation.' '.$result->name.' '.$result->last_name}}</td>
                                            <td><strong>@lang('admin.email') :</strong> {{$result->email ?? '-'}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.dob') :</strong>
                                            {{$result->dob ?? '-'}}</td>
                                            <td><strong></strong> </td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.citizenship') :</strong> {{\App\Helpers\commonHelper::getCountryNameById($result->citizenship) ?? '-'}}
                                            </td>
                                            <td><strong>@lang('admin.marital') @lang('admin.status') :</strong>
                                                {{$result->marital_status ?? '-'}}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.gender') :</strong> @if($result->gender=='1'){{'Male'}}@elseif($result->gender=='2'){{'Female'}}@else{{'N/A'}}@endif
                                        </td> 
                                            <td><strong>User Type :</strong> 
                                            @php 
                                            
                                                if($result->parent_id != Null){

                                                if($result->added_as == 'Group'){

                                                    echo  '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
                                                    
                                                }elseif($result->added_as == 'Spouse'){

                                                    echo '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';
                                                    
                                                }

                                                }else {

                                                $groupName = \App\Models\user::where('parent_id', $result->id)->where('added_as','Group')->first();
                                                $spouseName = \App\Models\user::where('parent_id', $result->id)->where('added_as','Spouse')->first();

                                                if($groupName){

                                                    echo '<div class="span badge rounded-pill pill-badge-secondary">Group</div>';
                                                    
                                                }else if($spouseName){

                                                    echo '<div class="span badge rounded-pill pill-badge-success">Spouse</div>';

                                                }else{

                                                    echo '<div class="span badge rounded-pill pill-badge-warning">Individual</div>';
                                                }
                                                    

                                                }
                                            @endphp
                                        </td> 
                                            <td><strong>Group Owner Name :</strong> 
                                            
                                            @php
                                                $groupName = \App\Models\user::where('parent_id', $result->id)->where('added_as','Group')->get();

                                                if($result->parent_id != Null && $result->added_as == 'Group'){

                                                echo \App\Helpers\commonHelper::getUserNameById($result->parent_id);

                                                }else if(count($groupName) > 0) {

                                                echo ucfirst($result->name.' '.$result->last_name);

                                                }else{

                                                    echo  'N/A';
                                                }
                                            @endphp
                                        </td> 
                                        </tr>
                                       
                                        <tr>

                                            @if($result->parent_id != Null && $result->added_as == 'Spouse')

                                                <td colspan="2"><strong>Are you coming along with your spouse to the Congress? :</strong> Yes
                                                </td>
                                                <td>Spouse : <a href="{{url('admin/user/user-profile/'.$result->parent_id)}}" >{{ \App\Helpers\commonHelper::getUserNameById($result->parent_id)}} </a></td>
                                            @elseif($Spouse) 
                                                <td colspan="2"><strong>Are you coming along with your spouse to the Congress? :</strong> @if($Spouse) Yes @else No @endif
                                                </td>
                                                @if($Spouse)<td>Spouse : <a href="{{url('admin/user/user-profile/'.$Spouse->id)}}" >{{$Spouse->name}} {{$Spouse->last_name}}</a></td>@endif

                                            @endif
                                            
                                           
                                        <tr>
                                        @if(!$Spouse && $result->room !=null)
                                            <tr>
                                                <td colspan="2"><strong>Stay in Twin sharing room or Single Room :</strong> {{$result->room}}
                                                </td> 
                                            <tr>

                                        @endif
                                        
                                        @if($Spouse && $Spouse->spouse_confirm_status=='Approve')

                                            <tr>
                                                <td colspan="2"><strong>Spouse confirmation received :</strong> {{$Spouse->spouse_confirm_status}}
                                                </td> 
                                            <tr>
                                          
                                        @elseif($Spouse)
                                            <tr>
                                                <td colspan="2"><strong style="color:red">Spouse confirmation received : {{$Spouse->spouse_confirm_status}}</strong>
                                                </td> 
                                            <tr>
                                        @endif

                                        @php $history = \App\Models\SpouseStatusHistory::where([['spouse_id', $result->spouse_id], ['parent_id', $result->id]])->first(); @endphp

                                        @if($history && $history->status=='Reject')
                                            <tr>
                                                <td colspan="2">
                                                    <strong style="color:red">{{$history->remark}}</strong>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <p class="my-2"><b>@lang('admin.contact') @lang('admin.details')</b></p>
                            <div class="px-3 table table-bordered table-hover table-responsive">
                            <table class="table table-border table-hover table-responsive">
                                    <tbody>
                                        <tr>
                                            <td><strong>@lang('admin.country') :</strong>
                                                {{ \App\Helpers\commonHelper::getDataById('Country', $result->contact_country_id, 'name') ?? '-'}}</td>
                                            <td><strong>@lang('admin.state') :</strong>
                                                @if($result->contact_state_id == 0) {{$result->contact_state_name}} @else {{\App\Helpers\commonHelper::getStateNameById($result->contact_state_id) ?? '-'}} @endif
                                            <td><strong>@lang('admin.city') :</strong>
                                                @if($result->contact_city_id == 0) {{$result->contact_city_name}} @else {{\App\Helpers\commonHelper::getCityNameById($result->contact_city_id) ?? '-'}} @endif
                                        </tr>
                                        <tr>
                                            <td><strong>@lang('admin.mobile') :</strong>
                                                +{{$result->phone_code ?? '-'}} {{$result->mobile ?? '-'}}</td>
                                            <td><strong>@lang('admin.business') @lang('admin.number')
                                                    :</strong> +{{$result->contact_business_codenumber ?? '-'}} {{$result->contact_business_number ?? '-'}}</td>
                                            <td><strong>@lang('admin.whatsapp') @lang('admin.number')
                                                    :</strong> +{{$result->contact_whatsapp_codenumber ?? '-'}}{{$result->contact_whatsapp_number ?? '-'}}</td>
                                        </tr> 
                                        <tr>
                                            <td><strong>@lang('admin.zip') @lang('admin.code') :</strong>
                                                {{$result->contact_zip_code ?? '-'}}</td>
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
                                            ${{ \App\Helpers\commonHelper::getTotalAmountInProcess($result->id) }}</td>
                                        <td><strong>@lang('admin.accepted') @lang('admin.amount') :</strong>
                                            ${{ \App\Helpers\commonHelper::getTotalAcceptedAmount($result->id) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('admin.rejected') @lang('admin.amount') :</strong>
                                            ${{ \App\Helpers\commonHelper::getTotalRejectedAmount($result->id) }}</td>
                                        <td><strong> @lang('admin.pending') @lang('admin.amount') :</strong>
                                            ${{ \App\Helpers\commonHelper::getTotalPendingAmount($result->id) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('admin.total') @lang('admin.amount') :</strong>
                                            ${{ $result->amount }}</td>

                                        <td><strong>@lang('admin.payment') Refund :</strong>
                                            ${{\App\Helpers\commonHelper::userWithdrawalBalance($result->id)}}
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
                    <div class="card-header bg-primary p-2">
                        <h5 class="mb-0 px-2">
                            <button class="btn btn-link text-white collapsed" data-bs-toggle="collapse"
                                data-bs-target="#collapseicon4" aria-expanded="false"
                                aria-controls="collapse11"><i class="fas fa-school"></i>
                                @lang('admin.ministry') @lang('admin.details')<span>4</span></button>
                        </h5>
                    </div>
                    <div class="collapse" id="collapseicon4" aria-labelledby="collapseicon2"
                            data-bs-parent="#accordionoc" style="">
                        <div class="table table-bordered table-hover table-responsive">
                            <table class="table table-border table-hover table-responsive">
                                <tbody>
                                    <tr>
                                        <td colspan="4"><strong>@lang('admin.name') :</strong>
                                        @if($result->ministry_name == '') Independent @else {{$result->ministry_name}} @endif </td>
                                        <td><strong>@lang('admin.zip') @lang('admin.code')
                                                :</strong> {{$result->ministry_zip_code ?? '-'}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"><strong>@lang('admin.address') :</strong>
                                            {{$result->ministry_address ?? '-'}}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>@lang('admin.country') :</strong>
                                       
                                            {{ \App\Helpers\commonHelper::getDataById('Country', $result->ministry_country_id, 'name') ?? '-'}}</td>
                                        <td><strong>@lang('admin.state') :</strong>
                                            @if($result->ministry_state_id == 0) {{$result->ministry_state_name}} @else {{\App\Helpers\commonHelper::getStateNameById($result->ministry_state_id) ?? '-'}} @endif

                                        <td><strong>@lang('admin.city') :</strong>
                                            @if($result->ministry_city_id == 0) {{$result->ministry_city_name}} @else {{\App\Helpers\commonHelper::getCityNameById($result->ministry_city_id) ?? '-'}} @endif

                                    </tr>

									<tr>
                                        <td colspan="5"><strong>Are you a Pastor Trainer (PTer) :</strong>
                                            {{ $result->ministry_pastor_trainer}}</td> 
                                    </tr>


                                    @if($result->ministry_pastor_trainer=='Yes')
                                        @php
                                            $ministry_pastor_trainer_detail=json_decode($result->ministry_pastor_trainer_detail,true);
                                        @endphp
                                        <tr>
                                            <td><strong>Non-formal Pastoral Training :</strong>@if(!empty($ministry_pastor_trainer_detail)){{ \App\Helpers\commonHelper::ministryPastorTrainerDetail($ministry_pastor_trainer_detail['non_formal_trainor'])}}@endif</td>
                                            <td><strong>Formal Theological Education :</strong>@if(!empty($ministry_pastor_trainer_detail)){{\App\Helpers\commonHelper::ministryPastorTrainerDetail($ministry_pastor_trainer_detail['formal_theological'])}}@endif</td>
                                            <td><strong>Informal Personal Mentoring :</strong>@if(!empty($ministry_pastor_trainer_detail)){{\App\Helpers\commonHelper::ministryPastorTrainerDetail($ministry_pastor_trainer_detail['informal_personal'])}}@endif</td>
                                            <td><strong>Are you willing to commit to train one trainer of pastors per year for the next 7 years? :</strong>@if(!empty($ministry_pastor_trainer_detail) && isset($ministry_pastor_trainer_detail['willing_to_commit'])){{$ministry_pastor_trainer_detail['willing_to_commit']}}@endif</td>
                                            <td><strong>Comment :</strong>@if(!empty($ministry_pastor_trainer_detail) && isset($ministry_pastor_trainer_detail['comment']) && isset($ministry_pastor_trainer_detail['comment'])){{$ministry_pastor_trainer_detail['comment']}}@endif</td>
                                        </tr>
                                        <tr>
                                            
                                            <td colspan="5"><strong>How many pastoral leaders are you involved in strengthening each year :</strong> @if(!empty($ministry_pastor_trainer_detail)){{$ministry_pastor_trainer_detail['howmany_pastoral']}}@endif</td>
                                        </tr>
										<tr>
											<td colspan="5"><strong>How many of them can serve as future pastor trainers? :</strong> @if(!empty($ministry_pastor_trainer_detail)){{$ministry_pastor_trainer_detail['howmany_futurepastor']}}@endif</td>
                                      	</tr>
                                    @endif


                                    @if($result->ministry_pastor_trainer=='No') 
                                        <tr>
                                            <td colspan="3"><strong>Do you seek to add Pastoral Training to your ministries? :</strong> {{$result->doyouseek_postoral}}</td>
                                        </tr>
                                        @if($result->doyouseek_postoral == 'Yes')
                                        <tr>
                                            <td colspan="3"><strong>What ways do you envision training pastors?:</strong> {{$result->doyouseek_postoralcomment}}</td>
                                        </tr>
                                        @endif
                                        <tr> 
                                            <td colspan="3"><strong>Comment :</strong> {{$result->doyouseek_postoralcomment}}</td>
                                        </tr>
                                    @endif
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
                                data-bs-target="#collapseicon5" aria-expanded="false"
                                aria-controls="collapse11"><i class="fas fa-plane"></i>
                                @lang('admin.travel') @lang('admin.info')<span>5</span></button>
                        </h5>
                    </div>
                    <div class="collapse" id="collapseicon5" aria-labelledby="collapseicon2"
                            data-bs-parent="#accordionoc" style="">
                            <div class="row">
                            @if($result->TravelInfo)

                            
                                @if($result->TravelInfo && $result->TravelInfo->admin_status == '1')

                                    @if($result->TravelInfo->final_file != '')
                                        
                                        <h5 style="margin-top:20px; "><b>Visa letter file</b></h5>
                                        <div class="row col-sm-12" style="margin-left:10px">
                                            <a href="{{asset('uploads/file/'.$result->TravelInfo->final_file)}}" target="_blank">View File</a>
                                        </div>
                                    
                                    @endif

                                @else
                                    @if($result->TravelInfo && $result->TravelInfo->draft_file != '')
                                        
                                        <h5 style="margin-top:20px; "><b>Draft visa letter file</b></h5>
                                        <div class="row col-sm-12" style="margin-left:10px">
                                            <a href="{{asset('uploads/file/'.$result->TravelInfo->draft_file)}}" target="_blank">View File</a>
                                        </div>


                                    @endif

                                @endif

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
                            <div class="card-header" style="width:25%">
                                <button type="button" class="btn btn-primary-light" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                    Refund Payment
                                </button>
                            </div>
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
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Sponsored Payment</h5>
                                </div>
                                <div class="card-header" style="width:25%">
                                    <button type="button" class="btn btn-primary-light" data-bs-toggle="modal" data-bs-target="#SponsoredStaticBackdrop">
                                        Refund Payment
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="display datatables" id="tablelist_sponsor">
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
                                                    <td class="text-center" colspan="7">
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
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Donate Payment</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="display datatables" id="tablelist_donate">
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
                                                    <td class="text-center" colspan="7">
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
                </div>
            </div>
        </div>

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
                        
                            <form id="form" action="{{ route('admin.user.comment.to.user') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                <input type="hidden" value="@if($id){{ $id }} @else 0 @endif" name="user_id" required />
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
                    <div class="collapse p-3" id="collapseicon10" aria-labelledby="collapseicon10" >
                        
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

<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Payment Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentRefund" action="{{ route('admin.user.refund.amount') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" value="@if($id){{ $id }} @else 0 @endif" name="user_id" required />
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="input">Enter Amount:</label>
                                <input type="test" class="form-control" value="" name="amount" required />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="input">Enter Reference Number:</label>
                                <input type="test" class="form-control" value="" name="reference_number" required />
                            </div>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-center align-items-center">
                            <div class="btn-showcase text-center">
                                <button class="btn btn-primary" type="submit" form="paymentRefund">@lang('admin.submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="SponsoredStaticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="SponsoredStaticBackdrop" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="SponsoredStaticBackdrop">Sponsored Payment Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sponsoredPaymentRefund" action="{{ route('admin.user.sponsored.refund.amount') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <input type="hidden" value="@if($id){{ $id }} @else 0 @endif" name="user_id" required />
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="input">Enter Amount:</label>
                                <input type="test" class="form-control" value="" name="amount" required />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="input">Transfer amount to other user</label>
                                <select name="other_user" id="other_user" class="form-control" >
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12" id="ReferenceDiv">
                            <div class="form-group">
                                <label for="input">Enter Reference Number:</label>
                                <input type="text" class="form-control" value="" name="reference_number" id="referenceNumber" required />
                            </div>
                        </div>

                        <div class="col-sm-12" style="display:none" id="UserDiv">
                            <div class="form-group">
                                <label for="input">Select User:</label>
                                <select name="other_user_id" id="other_user_id" class="form-control" >
                                    <option value="">Select User</option>
                                    @php  $users = \App\Models\User::where([['stage', '1'], ['profile_status', 'Review'], ['status', '0']])->where(function ($query) {
                                                $query->where('added_as',null)
                                                    ->orWhere('added_as', '=', 'Group');
                                                })->get();
                                    @endphp 
                                    @if(!empty($users) && count($users)>0)
                                        
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}} {{$user->last_name}}</option>
                                        @endforeach
                                    @endif
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 d-flex justify-content-center align-items-center">
                            <div class="btn-showcase text-center">
                                <button class="btn btn-primary" type="submit" form="sponsoredPaymentRefund">@lang('admin.submit')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="userMailTriggerListModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="userMailTriggerListModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">User Mail </h5>
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
            "url": "{{ route('admin.user.comment.to.user') }}",
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
            "url": "{{ route('admin.user.userHistoryList') }}",
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

    $('#tablelist_sponsor').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.sponsored.Payment.History', [$id]) }}",
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
                "data": "created_at"
            },
            {
                "data": "user_name"
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

    $('#tablelist_donate').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.donate.Payment.History', [$id]) }}",
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
                "data": "created_at"
            },
            {
                "data": "user_name"
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


    $("form#sponsoredPaymentRefund").submit(function(e) {
     
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
                submitButton(formId, btnhtml, false);
            },
            success: function(data) {
                if (data.error) {
                    sweetAlertMsg('error', data.message);
                } else {

                    if (data.reset) {
                        $('#' + formId)[0].reset();
                        $('#SponsoredStaticBackdrop').modal('hide');
                    }

                    sweetAlertMsg('success', data.message);

                }
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });

    });

    $("form#paymentRefund").submit(function(e) {
     
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
                submitButton(formId, btnhtml, false);
            },
            success: function(data) {
                if (data.error) {
                    sweetAlertMsg('error', data.message);
                } else {

                    if (data.reset) {
                        $('#' + formId)[0].reset();

                    }

                    sweetAlertMsg('success', data.message);

                }
                submitButton(formId, btnhtml, false);
            },
            cache: false,
            contentType: false,
            processData: false,
        });

    });

    $('#userMailTriggerList').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": true,
        "ordering": true,

        "ajax": {
            "url": "{{ route('admin.user.userMailTriggerList') }}",
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
            url: "{{ route('admin.user.userMailTriggerListModel') }}",
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