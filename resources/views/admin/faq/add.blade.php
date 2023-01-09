@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif FAQ @endsection
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
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.faq')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.faq')</li>
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
                    <a href="{{ route('admin.faq.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>@lang('admin.faq') @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.faq.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.category'):</label>
									<select class="form-control" name="category">
										<option value="">--Select One--</option>
										@if(count($faqs) > 0)
										@foreach ($faqs as $faq)
											<option value="{{$faq->id}}" @if($result && $result->category == $faq->id) selected @endif>@if(!empty($faq)) {{$faq->name}} @else NULL @endif</option>
										@endforeach
										@endif
									</select>
								</div>
							</div>
							<label for="input">English Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.question'):</label>
									<input class="form-control" type="text" name="question" placeholder="Enter question" value="@if($result){{ $result->question }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.answer'):</label>
									<textarea id="" class="form-control summernote" name="answer" placeholder="Enter answer" cols="30" rows="5">@if($result){{ $result->answer }}@endif</textarea>
								</div>
							</div>

							<label for="input">Spanish  Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.question'):</label>
									<input class="form-control" type="text" name="spanish_question" placeholder="Enter question" value="@if($result){{ $result->sp_question }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.answer'):</label>
									<textarea id="summernote" class="form-control summernote" name="spanish_answer" placeholder="Enter answer" cols="30" rows="5">@if($result){{ $result->sp_answer }}@endif</textarea>
								</div>
							</div>

							<label for="input">French Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.question'):</label>
									<input class="form-control" type="text" name="french_question" placeholder="Enter question" value="@if($result){{ $result->fr_question }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.answer'):</label>
									<textarea id="summernote" class="form-control summernote" name="french_answer" placeholder="Enter answer" cols="30" rows="5">@if($result){{ $result->fr_answer }}@endif</textarea>
								</div>
							</div>

							<label for="input">Portuguese  Language:</label><br>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.question'):</label>
									<input class="form-control" type="text" name="portuguese_question" placeholder="Enter question" value="@if($result){{ $result->pt_question }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.answer'):</label>
									<textarea id="summernote" class="form-control summernote" name="portuguese_answer" placeholder="Enter answer" cols="30" rows="5">@if($result){{ $result->pt_answer }}@endif</textarea>
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