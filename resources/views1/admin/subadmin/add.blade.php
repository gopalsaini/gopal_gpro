@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Sub Admin @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Sub Admin</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Sub Admin</li>
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
						<a href="{{ route('admin.subadmin.list')}}" class="btn btn-primary"><i class="fas fa-list me-2"></i> @lang('admin.list')</a>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.subadmin.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">Select Role : <span style="color:red">*</span></label>
								<select name="designation_id" class="form-control" required>
									<option value="">Select</option>

									@if (count($designations) > 0)
										@foreach ($designations as $designation)
											
											<option value="{{$designation->id}}" @if($result && $result->designation_id == $designation->id) selected @endif>{{$designation->designations}}</option>
											
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
							<div class="form-group">
								<label for="input">Password: <span style="color:red">*</span></label>
								<input class="form-control" type="password" name="password" placeholder="Enter Password" value="@if($result){{ $result->password }}@endif" required>
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