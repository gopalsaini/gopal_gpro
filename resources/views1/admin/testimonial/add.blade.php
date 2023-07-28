@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Testimonial @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.testimonial')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.testimonial')</li>
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
                    <a href="{{ route('admin.testimonial.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>@lang('admin.testimonial') @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.testimonial.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<label for="input">English Language:</label><br>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="title" placeholder="Enter title" value="@if($result){{ $result->title }}@endif">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.designation'):</label>
									<input class="form-control" type="text" name="designation" placeholder="Enter designation" value="@if($result){{ $result->designation }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea class="form-control" name="description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->description }}@endif</textarea>
								</div>
							</div>

							<label for="input">Spanish  Language:</label><br>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="sp_title" placeholder="Enter title" value="@if($result){{ $result->sp_title }}@endif">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.designation'):</label>
									<input class="form-control" type="text" name="sp_designation" placeholder="Enter designation" value="@if($result){{ $result->sp_designation }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea class="form-control" name="sp_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->sp_description }}@endif</textarea>
								</div>
							</div>

							<label for="input">French Language:</label><br>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="fr_title" placeholder="Enter title" value="@if($result){{ $result->fr_title }}@endif">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.designation'):</label>
									<input class="form-control" type="text" name="fr_designation" placeholder="Enter designation" value="@if($result){{ $result->fr_designation }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea class="form-control" name="fr_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->fr_description }}@endif</textarea>
								</div>
							</div>

							<label for="input">Portuguese  Language:</label><br>

							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="pt_title" placeholder="Enter title" value="@if($result){{ $result->pt_title }}@endif">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.designation'):</label>
									<input class="form-control" type="text" name="pt_designation" placeholder="Enter designation" value="@if($result){{ $result->pt_designation }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea class="form-control" name="pt_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->pt_description }}@endif</textarea>
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