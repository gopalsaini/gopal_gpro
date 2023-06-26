@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Role @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Role</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Role</li>
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
						<a href="{{ route('admin.role.list')}}" class="btn btn-primary"><i class="fas fa-list me-2"></i> @lang('admin.list')</a>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.role.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="col-sm-12">
							
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