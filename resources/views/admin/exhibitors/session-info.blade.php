@extends('layouts/master')

@section('title',__('Session Information'))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.session') @lang('admin.information') </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.session')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.information')</li>
                </ol>
            </div>
        </div>
    </div>
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
                                                
                                                $sessionInfo = \App\Models\DaySession::where('id',$dayValue->session_id)->first();
                                                if($sessionInfo){
                                                    $day .= 'Date :'.$dayValue->day.', ';
                                                    $day .= 'Name :'.$sessionInfo->session_name.', ';
                                                    $day .= 'Session Join :'.$dayValue->session.', ';
                                                    $day .= 'Start Time :'.$sessionInfo->start_time.', ';
                                                    $day .= 'End Time :'.$sessionInfo->end_time;
                                                    $day .= '<br>';
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
                                        @if ($result->SessionInfo[0]->user_status == '1')
                                        <div class="span badge rounded-pill pill-badge-success">
                                            Verify</div>
                                        @elseif ($result->SessionInfo[0]->user_status == '0')
                                        <div class="span badge rounded-pill pill-badge-danger">
                                            Reject</div>
                                        @elseif ($result->SessionInfo[0]->user_status === null)
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

@endsection