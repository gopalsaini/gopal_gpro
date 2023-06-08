
@extends('layouts/app')

@section('title',__('travel-information'))

@section('content')
<style>
    .step-form .detail-wrap {
        /* display: block !important; */
    }
    form {
        padding: 20px 0 0 !important;
    }
    .main-btn-comment {
        background-color: #58595B;
        min-width: 112px;
        width: fit-content;
        color: #ffffff;
        height: 45px;
        position: relative;
        font-size: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 5px;
        z-index: 1;
        overflow: hidden;
    }

    .fs-dropdown {
        
        width: 33% !important;
    }
</style>
<div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; $TravelInfoShow = false; @endphp
                
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
                                
                                Passport Information Declined By Admin
    
                            @elseif($passportInfo['status']=='Approve')
                                
                                Submit your travel info
                            @endif
                        </h5>

                    </div>
                    
                </div>

                @if(App::getLocale() == 'pt')
                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries_and_Visas_Portuguese.pdf') }}" >clicando aqui </a></p>
                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/IMMIGRATION_REPUBLIC_OF_PANAMA_Portuguese.pdf') }}" > clicando aqui</a></p>
                @elseif(App::getLocale() == 'sp')

                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries-and-Visas-sp.pdf') }}" >clic aquí</a></p>
                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/immigrationletterSPanish.pdf') }}" >clic aquí</a></p>
                
                @elseif(App::getLocale() == 'fr')

                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries_and_Visas_FR.pdf') }}" >cliquant ici</a></p>
                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/IMMIGRATION_REPUBLIC_OF_PANAMA _French.pdf') }}" >cliquant ici</a></p>
                
                @else

                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'countries_which_require_authorized')}}: <a target="_blank" href="{{ asset('pdf/Countries-and-Visas-en.pdf') }}" >Click Here</a></p>
                    <p>{{\App\Helpers\commonHelper::ApiMessageTranslaterLabel(\Session::get('lang'),'requirements_for_authorized_and_stamped_visa')}}: <a target="_blank" href="{{ asset('pdf/IMMIGRATION_REPUBLIC_OF_PANAMA_English.pdf') }}" >Click Here</a></p>
                @endif
      
                <h4 class="inner-head section-gap">@lang('web/wizard.Passport_Info')</h4>
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/wizard.Given_name')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            {{$passportInfo['salutation']}} 
                             
                        </li>
                        
                        <li>
                            <p>@lang('web/wizard.Surname')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            
                            {{$passportInfo['name']}} {{$passportInfo['last_name']}}</span>
                        </li>
                        
                        <!-- <li>
                            <p>@lang('web/profile.dob')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($passportInfo['dob']!=''){{ date('d-m-Y',strtotime($passportInfo['dob'])) }}@endif</span>
                        </li> -->
                        <li>
                            <p>@lang('web/wizard.Passport_Number')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$passportInfo['passport_no']}} </span>
                        </li>
                    
                        <li>
                            <p>@lang('web/wizard.Passport_Copy') :</p>
                            @if($passportInfo['passport_copy'] != '')
                                @foreach(explode(",",rtrim($passportInfo['passport_copy'], ',')) as $key=>$img)
                                    <a href="{{ asset('/uploads/passport/'.$img) }}" target="_blank"> 
                                        <span>&nbsp; &nbsp; &nbsp; File {{$key+1}} </span>
                                    </a>
                                @endforeach
                            @endif
                        </li>
                    
                        
                    </ul>
                    
                    <ul>
                        <li>
                            <p>@lang('web/wizard.which_country_passport_will_you_use_to_come_to_panama')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountry2NameById($passportInfo['country_id'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/wizard.is_this_a_diplomatic_passport') : {{$passportInfo['diplomatic_passport'] == 'Yes' ? Lang::get('web/wizard.yes') : Lang::get('web/wizard.no')}}</p>
                        </li>
                        
                        <!-- <li>
                            <p>@lang('web/profile.citizenship')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($passportInfo['citizenship'])}}</span>
                        </li> -->
                        
                        <li>
                            <p>@lang('web/wizard.Admin_Status')</p>
                            @if($passportInfo['admin_status'] =='Pending')
                                <span class="text-warning">:&nbsp; &nbsp; &nbsp; {{$passportInfo['admin_status']}}</span>
                            @elseif($passportInfo['admin_status'] =='Approved')
                                <span class="text-success">:&nbsp; &nbsp; &nbsp; {{$passportInfo['admin_status']}}</span>
                            @elseif($passportInfo['admin_status'] =='Decline')
                                <span class="text-danger">:&nbsp; &nbsp; &nbsp; Declined</span>
                            @endif
                        </li>
                        
                    </ul>
                </div>
                <div class="detail-wrap">
                    <ul>
                        @if($passportInfo['visa_residence'])
                            <li >
                                <p >@lang('web/wizard.do_you_have_a_valid_visa_or_residence') : </p>
                                <p>{{$passportInfo['visa_residence'] == 'Yes' ? Lang::get('web/wizard.yes') : Lang::get('web/wizard.no')}}</p>
                            </li>
                        @endif

                        @if($passportInfo['multiple_entry_visa_country'])
                            <li>
                                <p >@lang('web/wizard.do_you_have_a_valid_visa_or_residence_yes') : </p>
                                <p>{{$passportInfo['multiple_entry_visa_country'] == 'Yes' ? Lang::get('web/wizard.yes') : Lang::get('web/wizard.no')}}</p>
                            </li>
                        @endif

                        @if($passportInfo['multiple_entry_visa'])
                            <li>
                                <p >@lang('web/wizard.step_7_question') : </p>
                                <p>{{$passportInfo['multiple_entry_visa'] == 'Yes' ? Lang::get('web/wizard.yes') : Lang::get('web/wizard.no')}}</p>
                            </li> 
                        @endif

                        @if($passportInfo['passport_valid'])
                            <li>
                                <p>@lang('web/wizard.is_your_passport_valid_until') : {{$passportInfo['passport_valid'] == 'Yes' ? Lang::get('web/wizard.yes') : Lang::get('web/wizard.no')}} </p>
                            </li>
                        @endif
                       
                        @if($passportInfo['passport_valid'] == 'Yes')
                            <li>
                                <p >@lang('web/wizard.What_countries_among') : </p>
                                @if($passportInfo['valid_residence_country'] != '')

                                    @php $countryDoc = json_decode($passportInfo['valid_residence_country'],true); @endphp

                                    @foreach($countryDoc as $key=>$img)
                                    
                                        @if($img['id'] == '15')
                                            <p>@lang('web/wizard.Visa_Residence_Proof_for') 
                                                <a href="{{ asset('/uploads/passport/'.$img['file']) }}" target="_blank"> 
                                                    <span>&nbsp;  European Union  </span>
                                                </a>
                                            </p>&nbsp; &nbsp; &nbsp;
                                        @else

                                            <p>@lang('web/wizard.Visa_Residence_Proof_for')
                                                <a href="{{ asset('/uploads/passport/'.$img['file']) }}" target="_blank"> 
                                                    <span>&nbsp;  {{\App\Helpers\commonHelper::getCountry2NameById($img['id'])}}  </span>
                                                </a>
                                            </p>&nbsp; &nbsp; &nbsp;

                                        @endif
                                        
                                    @endforeach
                                @endif
                            </li>
                        @endif

                        @if($passportInfo['admin_status'] =='Decline')
                            <li>
                                <p style="color:red">@lang('web/wizard.Passport_Information_Declined_By_admin')</p><br>
                            </li>
                            <li>
                                <p>@lang('web/wizard.Decline_remark') : {!! $passportInfo['admin_remark'] !!}</p><br>
                                <div class="col-lg-12 mt-5">
                                    <div class="step-next">
                                        <a style="margin: 0 auto;" href="{{url('passport-info')}}" class="main-btn">@lang('web/wizard.Resubmit_Passport_Info') </a>
                                    </div>
                                </div>
                            </li>
                        @endif
                            
                        
                    </ul>
                </div>
                @if($passportInfo['admin_status'] =='Approved')
                    <div class="detail-wrap">
                        
                            <div class="row">
                                
                                <div class="col-md-12" >

                                @php 
                                
                                    $doNotRequireVisa = ['82','6','7','10','194','11','12','14','15','17','20','22','23','21','255','27','28','29','31','33','34','26','40','37','39','44','57','238','48','53','55','59','61','64','66','231','200','201','207','233','69','182','73','74','75','79','81','87','90','94','97','98','99','232','105','100','49','137','202','106','107','108','109','113','114','117','120','125','126','127','251','130','132','133','135','140','142','143','144','145','146','147','152','153','159','165','158','156','168','171','172','173','176','177','179','58','256','252','116','181','191','185','192','188','253','196','197','199','186','204','213','214','219','216','222','223','225','228','230','235','237','240']; 
                                    $RequireVisa = ['1','3','4','16','18','19','24','35','36','43','115','54','65','68','70','80','93','67','102','103','104','111','112','118','248','119','122','121','123','124','134','149','139','150','151','154','160','161','166','167','169','51','183','195','198','215','203','208','209','210','217','218','224','226','229','236','245','254','246','2','5','8','9','13','25','30','32','41','46','47','52','60','63','71','72','76','77','78','84','85','86','88','89','91','92','96','110','128','129','136','138','141','148','155','157','162','163','164','170','175','178','180','184','187','189','190','193','205','206','211','221','227','234','241','242','243','244','249','250']; 
                                    $restricted = ['38','45','56','62','174','83','95','101','131','42','50','212','220','239','247']; 
		
                                @endphp
                                    @if(in_array($passportInfo['country_id'],$doNotRequireVisa))

                                        @php $TravelInfoShow = true; @endphp
                                        <div class="row">
                                            <div class="alphabet-vd-box">
                                                <h4>@lang('web/wizard.Document_required_for_Visa')  </h4><br>
                                                
                                                <div class="step-next" style="display: flex;">
                                                    <a href="{{ asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Bank_Letter_Certification') </a>
                                                    @if($passportInfo['financial_letter'])
                                                        <a href="{{ asset('uploads/file/'.$passportInfo['financial_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_English_Version')</a>
                                                    @endif
                                                    <a href="{{ asset('uploads/file/'.$passportInfo['financial_spanish_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_Spanish_Version')</a>

                                                </div>
                                            </div>
                                        </div>
                                        <br><br>
                                    @elseif(in_array($passportInfo['country_id'],$RequireVisa))

                                        @php $TravelInfoShow = false; @endphp
                                        <div class="row">
                                        <h4>@lang('web/wizard.Document_required_for_Visa') </h4><br><br>
                                            <div class="step-next">
                                                <a href="{{ asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Bank_Letter_Certification')</a> 
                                                @if($passportInfo['financial_letter'])
                                                    <a href="{{ asset('uploads/file/'.$passportInfo['financial_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_English_Version')</a>
                                                @endif
                                                <a href="{{ asset('uploads/file/'.$passportInfo['financial_spanish_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_Spanish_Version')</a>
                                            </div>
                                            <div class="alphabet-vd-box mt-2">
                                                <a href="{{ asset('uploads/file/Visa_Request_Form.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Visa_Request_Form')</a>
                                                <a href="{{ asset('uploads/file/DOCUMENTS_REQUIRED_FOR_VISA_PROCESSING.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Documents_Required_for_Visa_Processing')</a>
                                            </div>
                                        </div>
                                        <br><br>
                                        <div class="col-lg-12">
                                            @if($passportInfo['visa_granted'] == null)

                                                
                                                <label class="form-check-label">@lang('web/wizard.your_Visa_Granted') <span>*</span></label>
                                                <div class="radio-wrap">
                                                    <div class="form__radio-group">
                                                        <input type="radio" name="diplomatic_passport" value="Yes" id="yes" class="form__radio-input">
                                                        <label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
                                                            <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.yes')
                                                        </label>
                                                    </div>
                                                    <div class="form__radio-group">
                                                        <input type="radio" name="diplomatic_passport" value="No" id="no" class="form__radio-input">
                                                        <label class="form__label-radio" for="no" class="form__radio-label">
                                                            <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.no')
                                                        </label>
                                                    </div>
                                                </div>
                                            @else

                                                @if($passportInfo['visa_granted'] == 'Yes')
                                                    @php $TravelInfoShow = true; @endphp
                                                @endif
                                                <label class="form-check-label">@lang('web/wizard.your_Visa_Granted') : {{$passportInfo['visa_granted']}}</label><br>
                                                @if($passportInfo['visa_granted'] == 'Yes')
                                                    <div class="alphabet-vd-box mt-2">
                                                        <a href="{{ asset('uploads/visa_file/'.$passportInfo['visa_granted_docs']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.View')</a>
                                                    </div>
                                                @endif
                                                <br>
                                                
                                            @endif
                                        </div>
                                        
                                    @elseif(in_array($passportInfo['country_id'],$restricted))

                                        @php $TravelInfoShow = false; @endphp
                                        <div class="row">
                                            <h4>@lang('web/wizard.Document_required_for_Visa') </h4><br><br>
                                            <div class="step-next">
                                                <a href="{{ asset('uploads/file/BANK_LETTER_CERTIFICATION.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Bank_Letter_Certification') </a> 
                                                @if($passportInfo['financial_letter'])
                                                    <a href="{{ asset('uploads/file/'.$passportInfo['financial_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_English_Version')</a>
                                                @endif
                                                <a href="{{ asset('uploads/file/'.$passportInfo['financial_spanish_letter']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Acceptance_Letter_Spanish_Version') </a>
                                            </div>
                                            <div class="alphabet-vd-box mt-2">
                                                <a href="{{ asset('uploads/file/Visa_Request_Form.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Visa_Request_Form')</a>
                                                <a href="{{ asset('uploads/file/DOCUMENTS_REQUIRED_FOR_VISA_PROCESSING.pdf') }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.Documents_Required_for_Visa_Processing')</a>
                                            </div>
                                        </div>
                                        <br>
                                        <p style='background-color:yellow; display: inline;'>@lang('web/wizard.admin_provide_name1') <b >{{$passportInfo['admin_provide_name']}}</b>.@lang('web/wizard.admin_provide_name2') <b >{{$passportInfo['admin_provide_email']}}</b>. @lang('web/wizard.admin_provide_name3')</p><br>
                                        <div class="col-lg-12">
                        
                                            @if($passportInfo['visa_granted'] == null)

                                                
                                                <label class="form-check-label">@lang('web/wizard.your_Visa_Granted') <span>*</span></label><br><br>
                                                <div class="radio-wrap">
                                                    <div class="form__radio-group">
                                                        <input type="radio" name="visa_granted" value="Yes" id="yes" class="form__radio-input">
                                                        <label class="form__label-radio" for="yes" class="form__radio-label" value="Yes">
                                                            <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.yes')
                                                        </label>
                                                    </div>
                                                    <div class="form__radio-group">
                                                        <input type="radio" name="visa_granted" value="No" id="no" class="form__radio-input">
                                                        <label class="form__label-radio" for="no" class="form__radio-label">
                                                            <span class="form__radio-button" style="border:1px solid #dc3545"></span> @lang('web/wizard.no')
                                                        </label>
                                                    </div>
                                                </div>
                                            @else

                                                @if($passportInfo['visa_granted'] == 'Yes')
                                                    @php $TravelInfoShow = true; @endphp
                                                @endif
                                                <label class="form-check-label">@lang('web/wizard.your_Visa_Granted') : {{$passportInfo['visa_granted']}}</label><br>
                                                @if($passportInfo['visa_granted'] == 'Yes')
                                                    <div class="alphabet-vd-box mt-2">
                                                        <a href="{{ asset('uploads/visa_file/'.$passportInfo['visa_granted_docs']) }}" target="_blank" class="text-blue btn btn-primary"> <i class="fa fa-file" aria-hidden="true"></i> @lang('web/wizard.View')</a>
                                                    </div>
                                                @endif
                                                <br>
                                                
                                            @endif
                                        </div>
                                    @endif

                                    @if($passportInfo['status'] =='Pending' && $passportInfo['sponsorship_letter'])
                                        
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
                                        
                                    @endif
                                </div>
                            </div>
                    
                    </div>
                @endif

                
                
                <div id="TravelInfoShowDiv" style="display:@if($TravelInfoShow) block @else none @endif">
                    @if($passportInfo['status']=='Approve')
                        <h4 class="inner-head section-gap">@lang('web/wizard.travel_Info')</h4>
                    
                        <div class="detail-wrap">
                            
                            @php 
                            
                            $result = \App\Models\TravelInfo::join('users','users.id','=','travel_infos.user_id')->where('travel_infos.user_id',$userId)->first(); 
                            if($result){
                                $result = $result->toArray();
                            }
                            
                            @endphp

                            @if($result && $result['admin_status'] == '1')

                                @if($result['final_file'] != '')
                                    <div class="row step-form">   
                                    
                                        <!-- <h4>visa letter file</h4> -->
                                        <!-- <div class="row">
                                            <div class="alphabet-vd-box">
                                                <iframe width="100%" height="400"  src="{{asset('uploads/file/'.$result['final_file'])}}#toolbar=0" title="Phonics Song for Children (Official Video) Alphabet Song | Letter Sounds | Signing for babies | ASL" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="step-next">
                                                <a href="{{asset('uploads/file/'.$result['final_file'])}}" target="_blank" class="main-btn bg-gray-btn" >Download</a>
                                            
                                            </div>
                                        </div> -->
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
                                                            <h5 style="margin-top:20px; "><b>@lang('web/home.arrival-to-panama-attendee') </b></h5>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$flight_details1->arrival_flight_number}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$flight_details1->arrival_start_location}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$flight_details1->arrival_date_departure}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$flight_details1->arrival_date_arrival}}</p></div>

                                                            <h5 style="margin-top:20px; "><b>@lang('web/home.departure-from-panama') - </b></h5>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$flight_details1->departure_flight_number}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$flight_details1->departure_start_location}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$flight_details1->departure_date_departure}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$flight_details1->departure_date_arrival}}</p></div>
                                                        </div>
                                                    @endif

                                                    @if($return_flight_details1)
                                                    
                                                        <div class="row col-sm-12" style="margin-left:10px">
                                                            <h5 style="margin-top:20px; "><b>@lang('web/home.arrival-to-panama-spouse') </b></h5>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$return_flight_details1->spouse_arrival_flight_number}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$return_flight_details1->spouse_arrival_start_location}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$return_flight_details1->spouse_arrival_date_departure}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$return_flight_details1->spouse_arrival_date_arrival}}</p></div>

                                                            <h5 style="margin-top:20px; "><b>@lang('web/home.departure-from-panama') - </b></h5>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$return_flight_details1->spouse_departure_flight_number}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$return_flight_details1->spouse_departure_start_location}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$return_flight_details1->spouse_departure_date_departure}}</p></div>
                                                            <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$return_flight_details1->spouse_departure_date_arrival}}</p></div>
                                                        </div>
                                                    @endif
                                                    
                                                
                                            @endif

                                        @else
                                            <h5>@lang('web/home.travel-info-not-available')</h5>
                                        @endif
                                    </div>
                                @endif
                                
                            @elseif($result && $result['user_status'] == '1')
                                <div class="row step-form">              
                                    <h4>@lang('web/home.admin-verifying-visa-info')</h4>
                                </div>
                            @elseif($result)
                                <!-- <div class="step-form" style="display: @if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) block @else none @endif"> -->
                                <div class="step-form" style="display: none">
                                    <h4>@lang('web/home.verify-visa-letter-info')</h4>
                                    
                                        @if($result)

                                            <div class="row">
                                                
                                                @if ($result['flight_details'])
                                                    @if ($result['flight_details'])
                                                        @php $flight_details1 = json_decode($result['flight_details']); @endphp
                                                        @php $return_flight_details1 = json_decode($result['return_flight_details']); @endphp
                                                            @if ($flight_details1)
                                                            
                                                                <h5 style="margin-top:20px; "><b>@lang('admin.flight') @lang('admin.details') </b></h5>
                                                                <div class="row col-sm-12" style="margin-left:10px">
                                                                    <h5 style="margin-top:20px; "><b>@lang('web/home.arrival-to-panama-attendee') </b></h5>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$flight_details1->arrival_flight_number}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$flight_details1->arrival_start_location}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$flight_details1->arrival_date_departure}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$flight_details1->arrival_date_arrival}}</p></div>

                                                                    <h5 style="margin-top:20px; "><b>@lang('web/home.departure-from-panama') - </b></h5>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$flight_details1->departure_flight_number}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$flight_details1->departure_start_location}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$flight_details1->departure_date_departure}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$flight_details1->departure_date_arrival}}</p></div>
                                                                </div>
                                                            @endif

                                                            @if($return_flight_details1)
                                                            
                                                                <div class="row col-sm-12" style="margin-left:10px">
                                                                    <h5 style="margin-top:20px; "><b>@lang('web/home.arrival-to-panama-spouse')</b></h5>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$return_flight_details1->spouse_arrival_flight_number}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$return_flight_details1->spouse_arrival_start_location}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$return_flight_details1->spouse_arrival_date_departure}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$return_flight_details1->spouse_arrival_date_arrival}}</p></div>

                                                                    <h5 style="margin-top:20px; "><b>@lang('web/home.departure-from-panama') - </b></h5>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.flight-number') :</strong> {{$return_flight_details1->spouse_departure_flight_number}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.start-location') :</strong> {{$return_flight_details1->spouse_departure_start_location}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-departure') :</strong> {{$return_flight_details1->spouse_departure_date_departure}}</p></div>
                                                                    <div class="col-sm-4"><p><strong> @lang('web/home.date-time-of-arrival') :</strong> {{$return_flight_details1->spouse_departure_date_arrival}}</p></div>
                                                                </div>
                                                            @endif
                                                            
                                                        
                                                    @endif

                                                @else
                                                    <h5>@lang('web/home.travel-info-not-available')</h5>
                                                @endif
                                            </div>
                                        @endif

                                    </div>
                                @endif

                            <div style="display:  none " id="TravelInfoEditDiv">
                                <form id="formSubmit1" action="{{ url('travel-information-remark-submit') }}" class="row" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" required value="@if($travelInfo['result'] && $travelInfo['result']['id']) {{$travelInfo['result']['id']}} @endif" placeholder="id" class="mt-2" >

                                        <div class="arrival">
                                            <h5><b>@lang('web/home.enter-remarks') -</b></h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.enter-remarks') <span style="color:red">*</span></h6>
                                                            <p><textarea  name="remark" required placeholder="@lang('web/home.enter-remarks')" class="form-control" >@if($travelInfo['result'] && $travelInfo['result']['remark']) {{$travelInfo['result']['remark']}} @endif</textarea></p>
                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-12">
                                            <div class="step-next">
                                                <button type="submit" class="main-btn bg-gray-btn" form="formSubmit1">@lang('web/help.submit')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        
                            @if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number'])
                                <div >
                                    <div class="step-form">
                                        <h4>@lang('web/home.flight-info')</h4>
                                        <div class="arrival">
                                            <h5><b>@lang('web/home.arrival-to-panama-attendee')</b> - &nbsp; &nbsp; @lang('web/home.attendee-name') {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        <h6>@lang('web/home.flight-number') : </h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) {{$travelInfo['result']['arrival_flight_number']}} @endif</p>
                                                    
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        <h6>@lang('web/home.start-location') :</h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_start_location']) {{$travelInfo['result']['arrival_start_location']}} @endif</p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                        <h6>@lang('web/home.date-time-of-departure') : </h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_date_departure']) {{$travelInfo['result']['arrival_date_departure']}} @endif </p>

                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                        <h6>@lang('web/home.date-time-of-arrival') :</h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['arrival_date_arrival']) {{$travelInfo['result']['arrival_date_arrival']}} @endif</p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="arrival">
                                            <h5><b>@lang('web/home.departure-from-panama') -</b></h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                    
                                                        <h6>@lang('web/home.flight-number') :</h6> <br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_flight_number']) {{$travelInfo['result']['departure_flight_number']}} @endif</p>
                                                    
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                    
                                                        <h6>@lang('web/home.start-location') :</h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_start_location']) {{$travelInfo['result']['departure_start_location']}} @endif </p>

                                                    
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                        <h6>@lang('web/home.date-time-of-departure') :</h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_date_departure']) {{$travelInfo['result']['departure_date_departure']}} @endif </p>

                                                        
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                        <h6>@lang('web/home.date-time-of-arrival') :</h6><br>
                                                        <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['departure_date_arrival']) {{$travelInfo['result']['departure_date_arrival']}} @endif</p>

                                                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- @if($SpouseInfoResult)

                                            <div class="arrival">
                                                <h5><b>@lang('web/home.arrival-to-panama-spouse') </b> - &nbsp; &nbsp; Spouse Name: {{$SpouseInfoResult->name}} {{$SpouseInfoResult->last_name}}</h5>
                                            </div>
                                            <div class="information-wrapper">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                    
                                                            <h6>@lang('web/home.flight-number') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_flight_number']) {{$travelInfo['result']['spouse_arrival_flight_number']}} @endif </p>
                                                            
                                                    </div>
                                                    <div class="col-md-6">
                                                        
                                                            <h6>@lang('web/home.start-location') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_start_location']) {{$travelInfo['result']['spouse_arrival_start_location']}} @endif </p>

                                                    </div>
                                                    <div class="col-md-6">
                                                        
                                                            <h6>@lang('web/home.date-time-of-departure') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_departure']) {{$travelInfo['result']['spouse_arrival_date_departure']}} @endif </p>

                                                    </div>
                                                    <div class="col-md-6">
                                                    
                                                            <h6>@lang('web/home.date-time-of-arrival') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_arrival_date_arrival']) {{$travelInfo['result']['spouse_arrival_date_arrival']}} @endif </p>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="arrival">
                                                <h5><b>@lang('web/home.departure-from-panama') -</b></h5>
                                            </div>
                                            <div class="information-wrapper">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="info">
                                                            
                                                            <h6>@lang('web/home.flight-number') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_flight_number']) {{$travelInfo['result']['spouse_departure_flight_number']}} @endif </p>
                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="info">
                                                        
                                                            <h6>@lang('web/home.start-location') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_start_location']) {{$travelInfo['result']['spouse_departure_start_location']}} @endif </p>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="info">
                                                            
                                                            <h6>@lang('web/home.date-time-of-departure') <br></h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_departure']) {{$travelInfo['result']['spouse_departure_date_departure']}} @endif</p>

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="info">
                                                            
                                                            <h6>@lang('web/home.date-time-of-arrival') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['spouse_departure_date_arrival']) {{$travelInfo['result']['spouse_departure_date_arrival']}} @endif</p>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif -->
                                        <div class="arrival">
                                            <h5><b>@lang('web/home.emergency-contact-info') -</b></h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                            <h6>@lang('web/home.mobile') :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['mobile']) {{$travelInfo['result']['mobile']}} @endif </p>
                                                    
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        
                                                            <h6>@lang('web/home.name')  :</h6><br>
                                                            <p>&nbsp;&nbsp;@if($travelInfo['result'] && $travelInfo['result']['name']) {{$travelInfo['result']['name']}} @endif </p>

                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="hotel-info">
                                            <h4>@lang('web/home.hotel-info')</h4>
                                            <h5>@lang('web/home.attendee-name') {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                            <div class="information-wrapper">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="info">
                                                            <div class="information-box">
                                                                <h6>@lang('web/home.check-in-date-time')</h6>
                                                                <p>@lang('web/home.user-information-only')</p>
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
                                                                <h6>@lang('web/home.check-in-date-time')</h6>
                                                                <p>@lang('web/home.user-information-only')</p>
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
                                            
                                            
                                                <h4>@lang('web/app.LogisticsInformation')</h4>
                                                <div class="col-lg-12">
                                                    <label for="">@lang('web/home.spouse-picked-by-gpro-from-airport')</label>
                                                    <div class="radio-wrap">
                                                        <div class="form__radio-group">
                                                            @if($travelInfo['result'] && $travelInfo['result']['logistics_picked']) {{$travelInfo['result']['logistics_picked']}} @endif
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <label for="">@lang('web/home.spouse-dropped-by-gpro-at-airport')</label>
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
                                        <h4>@lang('web/home.flight-info')</h4>
                                        <div class="arrival">
                                            <h5><b>@lang('web/home.arrival-to-panama-attendee')</b> - &nbsp; &nbsp; @lang('web/home.attendee-name'): {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                            <p>@lang('web/home.flight-details-landing-in-panama')</p>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.flight-number') <span style="color:red">*</span></h6>
                                                            <p><input type="text" name="arrival_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_flight_number']) {{$travelInfo['result']['arrival_flight_number']}} @endif" placeholder="@lang('web/home.flight-number')" class="mt-2" ></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.start-location') <span style="color:red">*</span></h6>
                                                            <p><input type="text" name="arrival_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_start_location']) {{$travelInfo['result']['arrival_start_location']}} @endif" placeholder="@lang('web/home.start-location')" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.date-time-of-departure') <span style="color:red">*</span></h6>
                                                            <p><input type="datetime-local" name="arrival_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_date_departure']) {{$travelInfo['result']['arrival_date_departure']}} @endif" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.date-time-of-arrival') <span style="color:red">*</span></h6>
                                                            <p><input type="datetime-local" name="arrival_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['arrival_date_arrival']) {{$travelInfo['result']['arrival_date_arrival']}} @endif" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="arrival">
                                            <h5><b>@lang('web/home.departure-from-panama') -</b></h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.flight-number') <span style="color:red">*</span></h6>
                                                            
                                                            <p><input type="text" name="departure_flight_number" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_flight_number']) {{$travelInfo['result']['departure_flight_number']}} @endif" placeholder="@lang('web/home.flight-number')" class="mt-2" ></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.start-location') <span style="color:red">*</span></h6>
                                                            <p><input type="text" name="departure_start_location" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_start_location']) {{$travelInfo['result']['departure_start_location']}} @endif" placeholder="@lang('web/home.start-location')" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.date-time-of-departure') <span style="color:red">*</span></h6>
                                                            <p><input type="datetime-local" name="departure_date_departure" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_date_departure']) {{$travelInfo['result']['departure_date_departure']}} @endif" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.date-time-of-arrival') <span style="color:red">*</span></h6>
                                                            <p><input type="datetime-local" name="departure_date_arrival" required value="@if($travelInfo['result'] && $travelInfo['result']['departure_date_arrival']) {{$travelInfo['result']['departure_date_arrival']}} @endif" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="arrival">
                                            <h5><b>@lang('web/home.emergency-contact-info') -</b></h5>
                                        </div>
                                        <div class="information-wrapper">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.mobile')  <span style="color:red">*</span></h6>
                                                            <p><input type="text" onkeypress="return /[0-9 ]/i.test(event.key)"  name="mobile" required value="@if($travelInfo['result'] && $travelInfo['result']['mobile']) {{$travelInfo['result']['mobile']}} @endif" placeholder="Enter Mobile" class="mt-2" ></p>
                                                    
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="info">
                                                        <div class="information-box">
                                                            <h6>@lang('web/home.name') <span style="color:red">*</span></h6>
                                                            <p><input type="text" name="name" required value="@if($travelInfo['result'] && $travelInfo['result']['name']) {{$travelInfo['result']['name']}} @endif" placeholder="Enter Name" class="mt-2" ></p>

                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="hotel-info">
                                            <h4>@lang('web/home.hotel-info')</h4>
                                            <h5>@lang('web/home.attendee-name'): {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</h5>
                                            <div class="information-wrapper">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="info">
                                                            <div class="information-box">
                                                                <h6>@lang('web/home.check-in-date-time')</h6>
                                                                <p>@lang('web/home.user-information-only')</p>
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
                                                                <h6>@lang('web/home.check-in-date-time')</h6>
                                                                <p>@lang('web/home.user-information-only')</p>
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
                                           

                                            <div class="arrival">
                                                <p class="note">@lang('web/home.travel_note')</p>
                                                
                                            </div>
                                            
                                                @if($resultData['result']['added_as'] == null && !$SpouseInfoResult) 
                                                    <h5>@lang('web/home.like_to_share_your_room')</h5>
                                                        <div class="col-lg-6">
                                                            <br><br>
                                                            <select class="form-control test" name="share_your_room_with"> 
                                                                <option value="" >--@lang('web/home.attendee-name')--</option>
                                                                @php 
                                                                $users = \App\Models\User::where([['status', '!=', '1']])
                                                                        ->where(function ($query) {
                                                                            $query->where('added_as',null)
                                                                                ->orWhere('added_as', '=', 'Group');
                                                                        })->where('id','!=',$resultData['result']['id'])->where('stage','>','2')->where('gender',$resultData['result']['gender'])->orderBy('updated_at', 'desc')->get();
                                                                @endphp

                                                                @if($users)
                                                                    @foreach($users as $con)
                                                                        <option value="{{$con['id']}}">{{$con['name']}} {{$con['last_name']}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        
                                                        </div>
                                                @endif

                                                <h4>@lang('web/app.LogisticsInformation')</h4>
                                                <div class="col-lg-12">
                                                    <label for="">@lang('web/home.spouse-picked-by-gpro-from-airport')</label>
                                                    <div class="radio-wrap">
                                                        <div class="form__radio-group">
                                                            <input type="radio" name="logistics_picked" id="picked_yes" value="Yes" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_picked'] == 'Yes') checked @endif>
                                                            <label class="form__label-radio" for="picked_yes">
                                                            <span class="form__radio-button"></span> @lang('web/ministry-details.yes')
                                                            </label>
                                                        </div>
                                                        <div class="form__radio-group">
                                                            <input type="radio" name="logistics_picked" id="picked_no" value="No" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_picked'] == 'No') checked @endif>
                                                            <label class="form__label-radio" for="picked_no">
                                                            <span class="form__radio-button"></span> @lang('web/ministry-details.no')
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <label for="">@lang('web/home.spouse-dropped-by-gpro-at-airport')</label>
                                                    <div class="radio-wrap">
                                                        <div class="form__radio-group">
                                                            <input type="radio" name="logistics_dropped" id="picked_yes2" value="Yes"  class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_dropped'] == 'Yes') checked @endif>
                                                            <label class="form__label-radio" for="picked_yes2">
                                                            <span class="form__radio-button"></span> @lang('web/ministry-details.yes')
                                                            </label>
                                                        </div>
                                                        <div class="form__radio-group">
                                                            <input type="radio" name="logistics_dropped" id="picked_no2" value="No" class="form__radio-input" @if($travelInfo['result'] && $travelInfo['result']['logistics_dropped'] == 'Yes') checked @endif>
                                                            <label class="form__label-radio" for="picked_no2">
                                                            <span class="form__radio-button"></span> @lang('web/ministry-details.no')
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="step-next">
                                                <button type="submit" class="main-btn bg-gray-btn" form="formSubmit">@lang('web/profile-details.submit')</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                                
                    @endif

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
                    <h5 class="modal-title" id="exampleModalLongTitle">@lang('web/wizard.Passport_Info')</h5>
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

    
    
    <div class="login-modal" >
        <div class="modal fade" id="WhyVisaIsNotGranted" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/wizard.Why_Visa_is_not_Granted')</h2>
                        <!-- <h5 style="text-align:center;padding-top: 50px;">Log in to submit your application</h5> -->
                        <button type="button" class="btn-close" onclick="modalHide()"  data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <br><br>
                    <div class="modal-body" style="padding-top:0px">
                        <form id="PassportVisaIsNotGranted" action="{{ url('api/visa-is-not-granted') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <label for="inputName" class="d-flex">Visa Not Granted Proof &nbsp;&nbsp;<label class="text-danger">(@lang('web/app.Accepted_File_Formats'))*</label></label>
                            <input type="file"  name="visa_not_granted_docs" accept="image/*, .jpg,.png,.pdf" id="visa_not_granted_docs" class="">
                            <br>                                         
                            <label for="inputName" class="d-flex pt-5">@lang('web/app.enter') @lang('web/home.comment') &nbsp;&nbsp; <label class="text-danger"> *</label></label>
                            <textarea name="remark" id="remark" cols="10" rows="5" class="form-control" required></textarea>
                            <div class="modal-footer">
                                
                                <button type="submit" class=" main-btn main-btn-comment" form="PassportVisaIsNotGranted">@lang('web/home.submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="VisaIsGranted" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/wizard.Visa_is_Granted')</h2>
                        <!-- <h5 style="text-align:center;padding-top: 50px;">Log in to submit your application</h5> -->
                        <button type="button" class="btn-close" onclick="modalHide()"  data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <br><br>
                    <div class="modal-body" style="padding-top:0px">
                        <label for="inputName">@lang('web/wizard.Visa_changed_Later') </label>
                        <form id="PassportVisaIsGranted" action="{{ url('api/visa-granted') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <label class="d-flex">@lang('web/wizard.Upload_visa_file') &nbsp;&nbsp;<label class="text-danger">(@lang('web/app.Accepted_File_Formats'))*</label></label>
                            <input type="hidden"  name="type" id="type" value="Yes" required class="form-control">
                            <input type="file"  name="visa_file" id="visa_file" required class="">
                            <div class="modal-footer">
                                
                                <button type="submit" class=" main-btn main-btn-comment" form="PassportVisaIsGranted">@lang('web/wizard.yes')</button>
                                <button type="button" class=" main-btn main-btn-comment close" onclick="modalHide()" aria-label="Close">@lang('web/wizard.no')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
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
                location.href = "{{url('travel-information')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });

    $("form#PassportVisaIsNotGranted").submit(function(e) {

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
                location.href = "{{url('travel-information')}}";
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
        $('#WhyVisaIsNotGranted').modal('hide');
        $('#VisaIsGranted').modal('hide');
        $('#row_id').val(0);
        $('#remark').val(null);
    }

</script>

<script>

    $(document).ready(function() {
        

        $('#yes').change(function() {
            $('#TravelInfoShowDiv').hide();
            if ($(this).is(':checked')) {

                // $('#TravelInfoShowDiv').show();
                $('#VisaIsGranted').modal('show');

            } else {

                $('#TravelInfoShowDiv').hide();
            }
            
        });

        $('#no').change(function() {

            $('#TravelInfoShowDiv').hide();
            $('#WhyVisaIsNotGranted').modal('show');
            
        });
    });

    
    $("form#PassportVisaIsGranted").submit(function(e) {

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
                location.href = "{{url('travel-information')}}";
            },
            cache: false,
            contentType: false,
            processData: false
        });

    });

</script>
@endpush