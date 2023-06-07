@extends('layouts/master')

@section('title',__('Stage Three '.ucfirst($type)))

@push('custom_css')
    <style>
        .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        } 
    </style>
    <style>
        .odd{
            position: relative;
        }

        .group-user-list{
            position: absolute;
            left: 87px;
        }
        .dataTables_wrapper table.dataTable tbody td:nth-child(2)  { 
            padding-left: 45px !important;
        }
        .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        } 

        .group-user-list {
            background: url('{{asset("admin-assets/images/details_open.png")}}') no-repeat center center;
            cursor: pointer;
            width: 25px;
            height: 25px;
        }
        .shown .group-user-list {
            background: url('{{asset("admin-assets/images/details_close.png")}}') no-repeat center center;
        }
    </style>
    <style>
        

        .list-group-item-action{
            background-color: #ffcd34 !important;
        }
    </style>
@endpush

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.stage') @lang('admin.three') {{$type}} </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">{{$type}}</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.stage')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.three')</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.user.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>Send Invitation</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('admin.user.stage-bar')
    
    <div class="row">
        <div class="col-sm-12">
            <div class="list-group flex-row text-center" id="list-tab" role="tablist">
                <a class="list-group-item list-group-item-action active" id="list-all-list" data-bs-toggle="list" href="#list-all" role="tab" aria-controls="list-all"><span style="font-size: 16px;">All</span> </a>
                <a class="list-group-item list-group-item-action " id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home"><span style="font-size: 16px;">Passport Info</span> </a>
                <a class="list-group-item list-group-item-action" id="list-contact-list" data-bs-toggle="list" href="#contact-details" role="tab" aria-controls="list-contact"><span style="font-size: 16px;">Document</span></a>
                <a class="list-group-item list-group-item-action" id="list-travel-list" data-bs-toggle="list" href="#travel-details" role="tab" aria-controls="list-travel"><span style="font-size: 16px;">Travel Info</span></a>
            </div>
        </div>
        
        <div class="col-sm-12" style="margin-top: 20px;">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="list-all" role="tabpanel" aria-labelledby="list-all-list">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <input type="text" name="email" class="form-control passportListAllEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="passportListAll">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Email </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Category  </th>
                                                    <th> Status </th>
                                                    <th> Action  </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Email </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Category  </th>
                                                    <th> Status </th>
                                                    <th> Action  </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>No Visa Needed</h5>
                                    <input type="text" name="email" class="form-control passportEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="passportListNoVisaNeeded">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries  </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
                                                    <th> @lang('admin.action') </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Visa Needed</h5>
                                    <input type="text" name="email" class="form-control passportVisaNeededEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="passportListVisaNeeded">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries  </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
                                                    <th> @lang('admin.action') </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Restricted countries</h5>
                                    <input type="text" name="email" class="form-control passportRestrictedCountriesEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="passportListRestrictedCountries">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number</th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number</th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Passport Copy  </th>
                                                    <th> Visa/Residence Proof of Countries  </th>
                                                    <th> Return Remarks  </th>
                                                    <th> Status </th>
                                                    <th> Admin Action </th>
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
                <div class="tab-pane fade" id="contact-details" role="tabpanel" aria-labelledby="list-contact-list">
                    <div class="row">
                       
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>No Visa Needed</h5>
                                    <input type="text" name="email" class="form-control sponsorshipNoVisaNeededEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="sponsorshipListNoVisaNeeded">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Visa Needed</h5>
                                    <input type="text" name="email" class="form-control sponsorshipVisaNeededEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="sponsorshipListVisaNeeded">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Restricted Countries</h5>
                                    <input type="text" name="email" class="form-control sponsorshipRestrictedCountriesEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="sponsorshipListRestrictedCountries">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Support Name  </th>
                                                    <th> Support Email ID </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Acceptance Letter  </th>
                                                    <th> Other Documents  </th>
                                                    <th> Support Name  </th>
                                                    <th> Support Email ID </th>
                                                    <th> Status  </th>
                                                    <th> Visa Status </th>
                                                    <th> @lang('admin.action') </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Visa is not Granted</h5>
                                    <input type="text" name="email" class="form-control visaIsNotGrantedEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="visaIsNotGranted">
                                            <thead>
                                                <tr>
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Category  </th>
                                                    <th> Comment  </th>
                                                    <th> Visa Not Granted Proof  </th>
                                                    <th> Refund Money  </th>
                                                    <th> Refund Amount  </th>
                                                    <th> Reference Number </th>
                                                    <th> @lang('admin.action') </th>
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
                                                    <th> #ID </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country  </th>
                                                    <th> Category  </th>
                                                    <th> Comment  </th>
                                                    <th> Visa Not Granted Proof   </th>
                                                    <th> Refund Money  </th>
                                                    <th> Refund Amount  </th>
                                                    <th> Reference Number </th>
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
                <div class="tab-pane fade" id="travel-details" role="tabpanel" aria-labelledby="list-travel-list">
                    <div class="row">
                       
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <input type="text" name="email" class="form-control searchEmail " placeholder="Search ...">
                                    <br>
                                    <div class="table-responsive">
                                        <table class="display datatables" id="tablelist">
                                            <thead>
                                                <tr>
                                                    <th> @lang('admin.id') </th>
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country </th>
                                                    <th> Category </th>
                                                    <th> Visa Document </th>
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
                                                    <th> Given Name, Surname </th>
                                                    <th> Passport Number </th>
                                                    <th> Passport issued by Country</th>
                                                    <th> Category</th>
                                                    <th> Visa Document </th>
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
                
            </div>
        </div>
    </div>

    
</div>


<div class="modal fade" id="passportRemarkModal" tabindex="-1" aria-labelledby="passportRemarkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passportRemarkModalLabel">User/Admin Passport Remark</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p id="PassportRemark"></p>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">User Flight Information Remark</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <p id="Remark"></p>
      </div>
      
    </div>
  </div>
</div>



<div class="modal fade" id="draftLetterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send User Upload File Letter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="formSubmit" action="{{ url('admin/user/upload-draft-information') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="travelId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="info">
                                <div class="information-box">
                                    <h6>Upload File <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="file" type="file" required class="form-control" /></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="formSubmit">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="sendSponsorshipLetterModal" tabindex="-1" aria-labelledby="sendSponsorshipLetterLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sendSponsorshipLetterLabel">Upload Sponsorship Letter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="sponsorshipSubmit" action="{{ url('admin/user/upload-sponsorship-letter') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="SponsorshipId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="info">
                                <div class="information-box">
                                    <h6>Upload Sponsorship Letter <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="file" type="file" required class="form-control" /></p>
                            
                                </div>
                                <div class="information-box">
                                    <h6> Financial Latter Upload Letter (English)<span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="financial_english_letter" type="file" required class="form-control" /></p>
                            
                                </div>
                                <div class="information-box">
                                    <h6>Financial Latter Upload Letter (Spanish) <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="financial_spanish_letter" type="file" required class="form-control" /></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="sponsorshipSubmit">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="finalLetterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Send Final Visa Letter </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="formSubmit1" action="{{ url('admin/user/upload-final-information') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="finalId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <input type="hidden" id="Upload" name="type" value="2">
                        <div class="col-lg-12" >
                            <div class="info">
                                <div class="information-box">
                                    <h6>Upload File <span style="color:red">*</span></h6>
                                    <p><input accept="application/pdf" name="file" id="fileData" type="file" class="form-control" /></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="formSubmit1">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>

<div class="modal fade" id="passportApproveRestricted" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Need to provide Name and email ID of the person who will coordinate with Candidate </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <form id="passportRestrictedSubmit" action="{{ url('admin/user/passport/approve/restricted') }}" class="row" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" required value="" placeholder="id" id="finalId" class="mt-2" >

                <div class="information-wrapper">
                    <div class="row">
                        <input type="hidden" id="Upload" name="type" value="2">
                        <div class="col-lg-12" >
                            <div class="info">
                                <div class="information-box">
                                    <input type="hidden" name="id" id="passportId" cols="10" rows="5" class="form-control" required />

                                    @php  $coordinate = ['test'=>'test@gmail.com','test2'=>'test2@gmail.com','test3'=>'test3@gmail.com'];  @endphp
                                    <h6>Enter Name <span style="color:red">*</span></h6>
                                    <p>
                                        <select id="coordinateName" class="form-control"  name="name" required>
                                            <option  value="">-- Select --</option>
                                            @foreach($coordinate as $key=>$val)
                                                <option  data-email="{{ $val }}" value="{{ $key }}">{{ ucfirst($key) }} </option>
                                            @endforeach
                                        </select>
                                    </p>

                                    <h6>Enter Email <span style="color:red">*</span></h6>
                                    <p><input name="email" value="" required id="emailData" type="email" class="form-control" readonly/></p>
                            
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="step-next">
                        <button type="submit" class="btn btn-sm btn-primary m-1 text-white" form="passportRestrictedSubmit">Submit</button>
                    </div>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-3">
                <h5 class="modal-title" id="exampleModalLongTitle">Passport Info Decline</h5>
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
                                <label for="inputName">User Language : </label> <label id="user_lang"></label> <br>
                                <label for="inputName">Enter Decline Remark <label class="text-danger">*</label></label>
                                <form id="Passport" action="{{ url('admin/user/passport/decline') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                                    @csrf
                                    <textarea name="remark" id="remark" cols="10" rows="5" class="form-control" required></textarea>
                                    <input type="hidden" name="id" id="row_id" cols="10" rows="5" class="form-control" required />

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
                    <input type="hidden" value="" name="user_id" required id="refundUserId"/>
                    <p>Total Accepted Amount : <span id="totalAcceptedAmount">0.00</span></p>
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

@endsection

@push('custom_js')

<script>

    $(document).ready(function () {
            $('input:radio[name=type]').change(function () {
            
                if ($("input[name='type']:checked").val() == '1') {

                    $('#UploadFileDiv').hide();
                    $('#fileData').attr('required',false);
                    
                }else{
                    $('#UploadFileDiv').show();
                    $('#fileData').attr('required',true);
                }
                
            });
        });


    $(document).ready(function() {

        
        var passportListAllEmail = $('#passportListAll').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/all/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.passportListAllEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                }, 
                {
                    "data": "category"
                },
                {
                    "data": "status"
                },
                {
                    "data": "action"
                }
            ]
        });

        var passport = $('#passportListNoVisaNeeded').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/list/no-visa-needed/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.passportEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                }, 
                {
                    "data": "passport_copy"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "remark"
                },
                {
                    "data": "status"
                }, 
                {
                    "data": "admin_status"
                },
                {
                    "data": "action"
                }
            ]
        });

        var passportVisaNeeded = $('#passportListVisaNeeded').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/list/visa-needed/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.passportVisaNeededEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                }, 
                {
                    "data": "passport_copy"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "remark"
                },
                {
                    "data": "status"
                }, 
                {
                    "data": "admin_status"
                },
                {
                    "data": "action"
                }
            ]
        });


        var passportRestrictedCountries = $('#passportListRestrictedCountries').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/list/restricted/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.passportRestrictedCountriesEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                }, 
                {
                    "data": "passport_copy"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "remark"
                },
                {
                    "data": "status"
                }, 
                {
                    "data": "admin_status"
                },
                {
                    "data": "action"
                }
            ]
        });


        var sponsorshipListNoVisaNeeded = $('#sponsorshipListNoVisaNeeded').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/sponsorship/no-visa-needed/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.sponsorshipNoVisaNeededEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                },
                {
                    "data": "financial_letter"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "status"
                },
                {
                    "data": "visa_status"
                },
                {
                    "data": "action"
                }
            ]
        });

        var sponsorshipListVisaNeeded = $('#sponsorshipListVisaNeeded').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/sponsorship/visa-needed/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.sponsorshipVisaNeededEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                },
                {
                    "data": "financial_letter"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "status"
                },
                {
                    "data": "visa_status"
                },
                {
                    "data": "action"
                }
            ]
        });

        var sponsorshipListRestrictedCountries = $('#sponsorshipListRestrictedCountries').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/restricted-list/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.sponsorshipRestrictedCountriesEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                },
                {
                    "data": "financial_letter"
                },
                {
                    "data": "valid_residence_country"
                },
                {
                    "data": "support_name"
                },
                {
                    "data": "support_email"
                },
                {
                    "data": "status"
                },
                {
                    "data": "visa_status"
                },
                {
                    "data": "action"
                }
            ]
        });

        var visaIsNotGranted = $('#visaIsNotGranted').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ url('admin/user/passport/visa-is-not-granted/'.$type) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.visaIsNotGrantedEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport_no"
                },
                {
                    "data": "country_id"
                },
                {
                    "data": "category"
                },
                {
                    "data": "category"
                },
                {
                    "data": "visa_not_granted_docs"
                },
                {
                    "data": "Refund"
                },
                {
                    "data": "refund_amount"
                },
                {
                    "data": "reference_number"
                },
                {
                    "data": "action"
                }
            ]
        });


        var table = $('#tablelist').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "ordering": false,

            "ajax": {
                "url": "{{ route('admin.user.list.stage.three', ["$type"]) }}",
                "dataType": "json",
                "async": false,
                "type": "get",
                data: function (d) {
                    d.email = $('.searchEmail').val()
                },
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
                    className: "text-left",
                    targets: "_all"
                },
                {
                    orderable: false,
                    targets: [-1]
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
                    "data": "name"
                },
                {
                    "data": "passport"
                },
                {
                    "data": "country"
                },
                {
                    "data": "category"
                },
                {
                    "data": "visa_doc"
                },
                {
                    "data": "action"
                }
            ]
        });
        
        $(".searchEmail").keyup(function(){
            table.draw();
        });

        $(".passportListAllEmail").keyup(function(){
            passportListAllEmail.draw();
        });

        $(".passportEmail").keyup(function(){
            passport.draw();
        });

        $(".passportVisaNeededEmail").keyup(function(){
            passportVisaNeeded.draw();
        });

        $(".passportRestrictedCountriesEmail").keyup(function(){
            RestrictedCountries.draw();
        });

        $(".sponsorshipNoVisaNeededEmail").keyup(function(){
            sponsorshipListNoVisaNeeded.draw();
        });

        $(".sponsorshipVisaNeededEmail").keyup(function(){
            sponsorshipListVisaNeeded.draw();
        });

        $(".sponsorshipRestrictedCountriesEmail").keyup(function(){
            sponsorshipListRestrictedCountries.draw();
        });

        $(".visaIsNotGrantedEmail").keyup(function(){
            visaIsNotGranted.draw();
        });

    });

    function fill_datatable() {

        $('.ViewRemark').click(function() {
        
            $('#exampleModal').modal('show');
            $('#Remark').html($(this).data('remark'));
        });

        

        $('.ViewPassportRemark').click(function() {
        
            $('#passportRemarkModal').modal('show');
            $('#PassportRemark').html($(this).data('remark'));
        });

        $('.sendDraftLetter').click(function() {
        
            $('#draftLetterModal').modal('show');
            $('#travelId').val($(this).data('id'));
        });

        $('.sendSponsorshipLetter').click(function() {
        
            $('#sendSponsorshipLetterModal').modal('show');
            $('#SponsorshipId').val($(this).data('id'));
        });

        $('.sendFinalLetter').click(function() {
        
            $('#finalLetterModal').modal('show');
            $('#finalId').val($(this).data('id'));
        });

        $('.passportApproveRestricted').click(function() {
        
            $('#passportApproveRestricted').modal('show');
            $('#passportId').val($(this).data('id'));
        });

        $('.sendEmail').click(function() {
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.user.send.travel.info.reminder') }}",
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
                    sweetAlertMsg('success', data.message);
                }
            });
        });

        $('.-change').click(function() {
            var status = $(this).data('type');
            var id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.user.travel.info.status') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'id': id,
                    'status': status,
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
                    $('#tablelist').DataTable().ajax.reload(null, false);
                }
            });
        });

            
        $('.passportReject').click(function() {
            var id = $(this).data('id');
            var lang = $(this).data('lang');
            
            $('#exampleModalCenter').modal('show');
            $('#row_id').val(id);
            $('#user_lang').html(lang);
            $('#remark').val(null);

        });

        
            
        $('#staticBackdropButton').click(function() {
            var id = $(this).data('id');
            $('#totalPendingAmount').html($(this).data('pending_amount'));
            $('#totalAcceptedAmount').html($(this).data('accepted_mount'));
            
            $('#staticBackdrop').modal('show');
            $('#refundUserId').val(id);

        });

    }

 
    $("form#formSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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
                $('#draftLetterModal').modal('hide');
                window.location.reload();
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

 
    $("form#sponsorshipSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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
                $('#sendSponsorshipLetterModal').modal('hide');
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

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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
                $('#finalLetterModal').modal('hide');
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
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }

                submitButton(formId, btnhtml, false);

            },
            success: function(data) {

                sweetAlertMsg('success', data.message);
                location.reload();
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


    });

    function modalHide() {

        $('#exampleModalCenter').modal('hide');
        $('#row_id').val(0);
        $('#remark').val(null);
    }

    $("form#passportRestrictedSubmit").submit(function(e) {
        e.preventDefault();
        
        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');

        $.ajax({
            url: formAction,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            dataType: 'json',
            type: 'post',
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
                $('#passportApproveRestricted').modal('hide');
                $('#passportId').val(null);
                window.location.reload();
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
                        $('#staticBackdrop').modal('hide');
                        window.location.reload();

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


    $('#coordinateName').change(function(){
        $('#emailData').val(($(this).children('option:selected').data('email')));
    });

</script>
@endpush