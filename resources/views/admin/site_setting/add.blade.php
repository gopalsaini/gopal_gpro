@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Site Setting @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Site Setting</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Site Setting</li>
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
                    <a href="{{ route('admin.site-setting.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i> Site Setting @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.site-setting.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">

							<div class="col-sm-12">
							<label for="input">English Language:</label>
								<div class="form-group">
									<label for="input">Title:</label>
									<input class="form-control" type="text" name="en_title" placeholder="Enter title" value="@if($result){{ $result->en_title }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<label for="input">French Language:</label>
								<div class="form-group">
									<label for="input">Title:</label>
									<input class="form-control" type="text" name="fr_title" placeholder="Enter title" value="@if($result){{ $result->fr_title }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<label for="input">Spanish Language:</label>
								<div class="form-group">
									<label for="input">Title:</label>
									<input class="form-control" type="text" name="sp_title" placeholder="Enter title" value="@if($result){{ $result->sp_title }}@endif">
								</div>
							</div>
							<div class="col-sm-12">
								<label for="input">Portuguese Language:</label>
								<div class="form-group">
									<label for="input">Title:</label>
									<input class="form-control" type="text" name="pt_title" placeholder="Enter title" value="@if($result){{ $result->pt_title }}@endif">
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

	<script>

		$('.summernote').summernote({
			placeholder: 'Enter Description',
			tabsize: 2,
			height: 200,
		});

		
	</script>
@endpush