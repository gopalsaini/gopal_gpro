@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif PreRecorded Video @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif PreRecorded Video</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">PreRecorded Video</li>
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
                    <a href="{{ route('admin.pre-recorded-video.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i> PreRecorded Video @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.pre-recorded-video.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<label for="input">Speaker:</label><br>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">Name:</label>
									<input class="form-control" type="text" name="name" placeholder="Enter name" value="@if($result){{ $result->name }}@endif">
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<div class="form-line">
										<label for="image"> Video <label class="text-danger">*</label></label>
										<input type="file" id="video" class="form-control"  name="video" @if(!$result) required @endif  data-type="single" data-video-preview="category_preview" accept="video/*"  class="form-control" >
									</div>
								</div>
								<div class="form-group previewimages col-md-6" id="category_preview">
									@if($result && $result->video!='')
									<video width="100" height="100" controls>
										<source src="{{ asset('/uploads/pre-recorded-video/'.$result->video) }}">
									</video>
									@endif  
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