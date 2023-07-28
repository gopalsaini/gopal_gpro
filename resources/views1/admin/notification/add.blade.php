@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Notification @endsection
@section('content')

@push('custom_css')
<style>
	.note-editor .link-dialog .checkbox label:before{
		display: none;
	}
	.note-editor .link-dialog .checkbox input{
		margin-right: 7px !important;
		opacity: 1 !important;
	}
	.fs-wrap {
		line-height: 2;
	}
	
	.fs-dropdown {
		width: 100%;
	}
</style>
@endpush

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Notification</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Notification</li>
					@if($result)
						<li class="breadcrumb-item" aria-current="page">@lang('admin.edit')</li>
					@else
						<li class="breadcrumb-item" aria-current="page">@lang('admin.add')</li>
					@endif
				</ol>
			</div>
			<div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.notification.list') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>List Notification</a>
                    </ul>
                </div>
            </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.notification.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<label for="input">Select User <span style="color:red">*</span></label>
									<select class="form-control test" name="user_id[]" multiple> 
										<option value="" >-- Select User--</option>
										@php $users = \App\Models\User::where([['status', '!=', '1']])->orderBy('updated_at', 'desc')->get(); @endphp

										@if($users)
											@foreach($users as $con)
												<option value="{{$con['id']}}">{{$con['name']}} {{$con['last_name']}}</option>
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Title: <span style="color:red">*</span></label>
									<input class="form-control" type="text" name="title" placeholder="Enter Title" value="" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Message: <span style="color:red">*</span></label>
									<textarea id="summernote" class="form-control" name="message" placeholder="Enter Message" cols="30" rows="5"></textarea>
								</div>
							</div>
						</div>
						<div class="btn-showcase text-center">
							@if(!$result)
							<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
							@endif
							<button class="btn btn-primary" type="submit" form="form">@lang('admin.submit')</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('custom_js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
	$(".js-example-tags").select2({
		tags: true
	});

	$('.test').fSelect({
    placeholder: 'Select some options',
    numDisplayed: 3,
    overflowText: '{n} selected',
    noResultsText: 'No results found',
    searchText: 'Search',
    showSearch: true
});

</script>
@endpush