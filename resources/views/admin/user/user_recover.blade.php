@extends('layouts/master')

@section('title')
    User Recover
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>User Recover</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
					<li class="breadcrumb-item" aria-current="page">User Recover</li>
				</ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.user.recover') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="col-sm-12">

							<div class="form-group">
                                <label for="input">Type <span style="color:red">*</span></label>
                                <select class="form-select" name="type" required aria-label="Default select example">
                                    <option selected>Select Type </option>
                                    <option value="1">User Recover</option>
                                    <option value="2">User Stage Move</option>
                                </select>
							</div>

							<div class="form-group">
								<label for="input">Email: <span style="color:red">*</span></label>
								<input class="form-control" type="email" name="email" placeholder="Enter email address"  required>
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