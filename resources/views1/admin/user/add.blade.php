@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif User @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>Send Invitation</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
					
						<li class="breadcrumb-item" aria-current="page">Send Invitation</li>
					
				</ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.user.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">@lang('admin.designation'): <span style="color:red">*</span></label>
								<select name="designation_id" class="form-control" required>
									<option value="">Select</option>

									@if (count($designations) > 0)
										@foreach ($designations as $designation)
											@if($designation->id == 2)
											<option value="{{$designation->id}}" @if($result && $result->designation_id == $designation->id) selected @endif>{{$designation->designations}}</option>
											@endif
										@endforeach
									@endif
								</select>
							</div>
							<div class="form-group">
								<label for="input">@lang('admin.email'): <span style="color:red">*</span></label>
								<input class="form-control" type="email" name="email" placeholder="Enter email address" value="@if($result){{ $result->email }}@endif" required>
							</div>
							<div class="form-group">
								<label for="input">Name: <span style="color:red">*</span></label>
								<input class="form-control" type="text" name="name" placeholder="Enter Name" value="@if($result){{ $result->name }}@endif" required>
							</div>
							<div class="btn-showcase text-center">
								@if(!$result)
								<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
								@endif
								<button class="btn btn-primary" type="submit" form="form">@lang('admin.submit')</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection