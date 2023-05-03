
@extends('layouts/app')

@section('title',__(Lang::get('web/app.myprofile')))

@section('content')

    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>
            <div class="step-form">
                <div class="application-wrap">
                    <div class="application-content">
                        
                        @if($passportInfo['admin_status']=='Pending')
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_631_9)">
                                <path d="M15 0.0703125C6.72656 0.0703125 0 6.75 0 15C0 23.25 6.72656 29.9297 15 29.9297C23.2734 29.9297 30 23.25 30 15C30 6.75 23.2734 0.0703125 15 0.0703125ZM15 28.4297C7.54688 28.4297 1.5 22.4297 1.5 15C1.5 7.57031 7.54688 1.57031 15 1.57031C22.4531 1.57031 28.5 7.59375 28.5 15C28.5 22.4297 22.4531 28.4297 15 28.4297ZM20.1328 11.2734L18.9844 10.1484C18.7734 9.9375 18.4453 9.9375 18.2344 10.1484L15 13.3594L11.7656 10.1484C11.5547 9.9375 11.2266 9.9375 11.0156 10.1484L9.86719 11.2734C9.65625 11.4844 9.65625 11.8125 9.86719 12.0234L13.1016 15.2344L9.86719 18.4687C9.65625 18.6797 9.65625 19.0078 9.86719 19.2187L11.0156 20.3437C11.2266 20.5547 11.5547 20.5547 11.7656 20.3437L15 17.1328L18.2344 20.3437C18.4453 20.5547 18.7734 20.5547 18.9844 20.3437L20.1328 19.2187C20.3438 19.0078 20.3438 18.6797 20.1328 18.4687L16.8984 15.2344L20.1328 12.0234C20.3438 11.8125 20.3438 11.4844 20.1328 11.2734Z" fill="#FB4949"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_631_9">
                                <rect width="30" height="30" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                    
                        @elseif($passportInfo['admin_status']=='Decline')
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_631_9)">
                                <path d="M15 0.0703125C6.72656 0.0703125 0 6.75 0 15C0 23.25 6.72656 29.9297 15 29.9297C23.2734 29.9297 30 23.25 30 15C30 6.75 23.2734 0.0703125 15 0.0703125ZM15 28.4297C7.54688 28.4297 1.5 22.4297 1.5 15C1.5 7.57031 7.54688 1.57031 15 1.57031C22.4531 1.57031 28.5 7.59375 28.5 15C28.5 22.4297 22.4531 28.4297 15 28.4297ZM20.1328 11.2734L18.9844 10.1484C18.7734 9.9375 18.4453 9.9375 18.2344 10.1484L15 13.3594L11.7656 10.1484C11.5547 9.9375 11.2266 9.9375 11.0156 10.1484L9.86719 11.2734C9.65625 11.4844 9.65625 11.8125 9.86719 12.0234L13.1016 15.2344L9.86719 18.4687C9.65625 18.6797 9.65625 19.0078 9.86719 19.2187L11.0156 20.3437C11.2266 20.5547 11.5547 20.5547 11.7656 20.3437L15 17.1328L18.2344 20.3437C18.4453 20.5547 18.7734 20.5547 18.9844 20.3437L20.1328 19.2187C20.3438 19.0078 20.3438 18.6797 20.1328 18.4687L16.8984 15.2344L20.1328 12.0234C20.3438 11.8125 20.3438 11.4844 20.1328 11.2734Z" fill="#FB4949"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_631_9">
                                <rect width="30" height="30" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>  
                        
                        @endif 

                    <h5>
                        @if($passportInfo['admin_status']=='Pending')

                            Admin Approval Pending 
                            
                        @elseif($passportInfo['admin_status']=='Decline')
                            
                            Passport Information Decline By Admin
 
                        @endif
                    </h5>
                        
                    </div>
                    
                </div>
                
                <h4 class="inner-head section-gap">Passport Info</h4>
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/profile.full-name')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            @lang('web/profile-details.'.(\App\Helpers\commonHelper::ministryPastorTrainerDetail($passportInfo['salutation'])))  
                                    
                            
                            {{$passportInfo['name']}} {{$passportInfo['last_name']}}</span>
                        </li>
                        
                        <li>
                            <p>@lang('web/profile.dob')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($passportInfo['dob']!=''){{ date('d-m-Y',strtotime($passportInfo['dob'])) }}@endif</span>
                        </li>
                        <li>
                            <p>Passport No</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$passportInfo['passport_no']}} </span>
                        </li>
                        <li>
                            <p>Passport Copy</p>
                            @if($passportInfo['passport_copy'] != '')
                                @foreach(explode(",",rtrim($passportInfo['passport_copy'], ',')) as $key=>$img)
                                    <a href="{{ asset('/uploads/passport/'.$img) }}" target="_blank"> 
                                        <span>:&nbsp; &nbsp; &nbsp; View {{$key+1}} </span>
                                    </a>
                                @endforeach
                            @endif
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.citizenship')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['citizenship'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.country')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['country_id'])}}</span>
                        </li>
                        <li>
                            <p>Admin Status</p>
                            <span class="text-success">:&nbsp; &nbsp; &nbsp; {{$passportInfo['admin_status']}}</span>
                        </li>
                    </ul>
                </div>
                    
               
                <h4 class="inner-head section-gap">Sponsorship Info</h4>
               
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/profile.full-name')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            @lang('web/profile-details.'.(\App\Helpers\commonHelper::ministryPastorTrainerDetail($passportInfo['salutation'])))  
                                    
                            
                            {{$passportInfo['name']}} {{$passportInfo['last_name']}}</span>
                        </li>
                        
                        <li>
                            <p>@lang('web/profile.dob')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($passportInfo['dob']!=''){{ date('d-m-Y',strtotime($passportInfo['dob'])) }}@endif</span>
                        </li>
                        
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.citizenship')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['citizenship'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.country')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['country_id'])}}</span>
                        </li>

                        <li>
                            <p>Admin Status</p>
                            <span class="text-success">:&nbsp; &nbsp; &nbsp; {{$passportInfo['admin_status']}}</span>
                        </li>
                    </ul>
                        
                </div>

                <div class="panel-body" >
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="step-next">
                                <a class="main-btn bg-gray-btn" href="{{url('sponsorship-confirm/confirm/'.$passportInfo['id'])}}">Confirm</a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="step-next">
                                <a class="main-btn bg-gray-btn -change" data-id="{{$passportInfo['id']}}" href="javascript:void(0);">Decline</a>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="inner-head section-gap">Travel Info</h4>
               
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/profile.full-name')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            @lang('web/profile-details.'.(\App\Helpers\commonHelper::ministryPastorTrainerDetail($passportInfo['salutation'])))  
                                    
                            
                            {{$passportInfo['name']}} {{$passportInfo['last_name']}}</span>
                        </li>
                        
                        <li>
                            <p>@lang('web/profile.dob')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($passportInfo['dob']!=''){{ date('d-m-Y',strtotime($passportInfo['dob'])) }}@endif</span>
                        </li>
                        <li>
                            <p>Passport No</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$passportInfo['passport_no']}} </span>
                        </li>
                        <li>
                            <p>Passport Copy</p><span>:&nbsp; &nbsp; &nbsp; 
                            @if($passportInfo['passport_copy'] != '')
                                @foreach(explode(",",rtrim($passportInfo['passport_copy'], ',')) as $key=>$img)
                                &nbsp; &nbsp; &nbsp; 
                                    <a href="{{ asset('/uploads/passport/'.$img) }}" target="_blank"> 
                                        View{{$key+1}} 
                                    </a>
                                @endforeach
                            @endif</span>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.citizenship')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['citizenship'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.country')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['country_id'])}}</span>
                        </li>

                        <li>
                            <p>Admin Status</p>
                            <span class="text-success">:&nbsp; &nbsp; &nbsp; {{$passportInfo['admin_status']}}</span>
                        </li>
                    </ul>
                        
                </div>
            </div>
        </div>
    </div>
    <!-- banner-end -->

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-3">
                <h5 class="modal-title" id="exampleModalLongTitle">Passport Info</h5>
                <button type="button" class="close" onclick="modalHide()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr class="m-0">
            <div class="modal-body px-3">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="form-line">
                                <label for="inputName">Enter Decline Remark <label class="text-danger">*</label></label>
                                <form id="Passport" action="{{ route('sponsorshipLetterReject') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <textarea name="remark" id="remark" cols="10" rows="5" class="form-control" required></textarea>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger " onclick="modalHide()">Close</button>
                <button type="submit" class="btn btn-dark " form="Passport">Submit</button>
            </div>
        </div>
    </div>
</div>
    
@endsection


@push('custom_js')

<script>
  
    $("form#Passport").submit(function(e) {

        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        var form_data = new FormData(this);

        var btnhtml = $("button[form=" + formId + "]").html();

        $.ajax({
            url: formAction,
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
            headers: {
                "Authorization": "Bearer {{\Session::get('gpro_user')}}"
            },
            beforeSend: function() {
                submitButton(formId, btnhtml, true);
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    showMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    showMsg('error', xhr.status + ': ' + xhr.statusText);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {
                showMsg('success', data.message);
                location.href = "{{url('profile')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });

    $(document).ready(function() {

        $('#exampleModalCenter').on('hidden.bs.modal', function (e) {
            modalHide();
        })

        $('.-change').click(function() {
            var id = $(this).data('id');

            $('#exampleModalCenter').modal('show');
            $('#row_id').val(id);
            $('#url').val(null);

        });

        
    });

    function modalHide() {
        
        $('#exampleModalCenter').modal('hide');
        $('#row_id').val(0);
        $('#remark').val(null);
    }
		
</script>
@endpush