@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Information @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.information')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.information')</li>
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
                    <a href="{{ route('admin.information.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>@lang('admin.information') @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.information.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							
							<label for="input">English Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="title" placeholder="Enter title" value="@if($result){{ $result->title }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea id="" class="form-control summernote" name="description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->description }}@endif</textarea>
								</div>
							</div>

							<label for="input">Spanish  Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="sp_title" placeholder="Enter title" value="@if($result){{ $result->sp_title }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea id="" class="form-control summernote" name="sp_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->sp_description }}@endif</textarea>
								</div>
							</div>

							<label for="input">French Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="fr_title" placeholder="Enter title" value="@if($result){{ $result->fr_title }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea id="" class="form-control summernote" name="fr_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->fr_description }}@endif</textarea>
								</div>
							</div>

							<label for="input">Portuguese  Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="pt_title" placeholder="Enter title" value="@if($result){{ $result->pt_title }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea id="" class="form-control summernote" name="pt_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->pt_description }}@endif</textarea>
								</div>
							</div>
						</div>
						
						
						@if(\Auth::user()->designation_id != 11)
						<div class="btn-showcase text-center">
							@if(!$result)
							<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
							@endif
							<button class="btn btn-primary" type="submit" form="form">@lang('admin.submit')</button>
						</div>
						@endif
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