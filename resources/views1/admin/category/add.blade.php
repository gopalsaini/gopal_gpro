@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif FAQ Category @endsection
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
</style>
@endpush

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Category</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Category</li>
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
                    <a href="{{ route('admin.category.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>Category @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.category.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							
							<label for="input">English Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Category Name:</label>
									<input class="form-control" type="text" name="name" placeholder="Enter name" value="@if($result){{ $result->name }}@endif" required>
								</div>
							</div>

							<label for="input">Spanish  Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Name:</label>
									<input class="form-control" type="text" name="sp_name" placeholder="Enter Name" value="@if($result){{ $result->sp_name }}@endif" required>
								</div>
							</div>

							<label for="input">French Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Name:</label>
									<input class="form-control" type="text" name="fr_name" placeholder="Enter name" value="@if($result){{ $result->fr_name }}@endif" required>
								</div>
							</div>

							<label for="input">Portuguese  Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">Name:</label>
									<input class="form-control" type="text" name="pt_name" placeholder="Enter name" value="@if($result){{ $result->pt_name }}@endif" required>
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

$('.summernote').summernote({
    placeholder: 'Enter Description',
    tabsize: 2,
    height: 200,
});

	$(".js-example-tags").select2({
		tags: true
	});
</script>
@endpush