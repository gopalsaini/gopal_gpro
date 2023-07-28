@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif FAQ @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Sessions</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Session</li>
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
                    <a href="{{ route('admin.session.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>Session @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.session.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">Name <span>*</span></label>
									<input class="form-control" type="text" name="name" placeholder="Enter Name" value="@if($result){{ $result->name }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">Session Name <span>*</span></label>
									<input class="form-control" type="text" name="session_name" placeholder="Enter Name" value="@if($result){{ $result->session_name }}@endif" required>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="input">Date <span>*</span></label>
									<input class="form-control" type="date" name="date" placeholder="Enter Date" value="@if($result){{ $result->date }}@endif" required>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="input">Start Time <span>*</span></label>
									<input class="form-control" type="time" name="start_time" placeholder="Enter Start Time" value="@if($result){{ $result->start_time }}@endif" required>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="input">End time <span>*</span></label>
									<input class="form-control" type="time" name="end_time" placeholder="Enter End time" value="@if($result){{ $result->end_time }}@endif" required>
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