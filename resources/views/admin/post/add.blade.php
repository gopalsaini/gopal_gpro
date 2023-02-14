@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Post @endsection
@section('content')
@push('custom_css')
<link href="{{ asset('admin-assets/dragimage/dist/image-uploader.min.css')}}" rel="stylesheet">
@endpush
<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Post</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Post</li>
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
                    <a href="{{ route('admin.post.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i> Post @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.post.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">Title:</label>
									<input class="form-control" type="text" name="title" placeholder="Enter title" value="@if($result){{ $result->title }}@endif">
								</div>
							</div>
							
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input"> Description:</label>
									<textarea class="form-control summernote" id="summernote" name="description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->description }}@endif</textarea>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group">
									<div class="form-line">
										<label for="inputName">Images<label
											class="text-danger">*</label></label></label>
										<div class="input-images" style="padding-top: .5rem;"></div>
										<!--<p style="color:red;width:100%">Size must be 800*800</p>-->
									</div>
									<p>
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


<script type="text/javascript" src="{{ asset('admin-assets/dragimage/dist/image-uploader.min.js')}}"></script>
<script>  
   let preloaded = [
   	@if(!empty($result->uploadfile))
   		
   		@php $uploadfiles = explode(',',$result->uploadfile);  @endphp 
   			@foreach($uploadfiles as $key=>$uploadfile)
   				{id: "{{$uploadfile}}", src: "{{ asset('uploads/post')}}/{{$uploadfile}}"},
   			@endforeach
   	@endif
   	
   ];
   
   $('.input-images').imageUploader({
   	extensions: ['.jpg', '.jpeg', '.png', '.gif', '.svg'],
   	mimes: ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'],
   	preloaded: preloaded,
   	imagesInputName: 'uploadfile',
   	preloadedInputName: 'images',
   	maxSize: 2 * 1024 * 1024,
   	maxFiles: 10
   });
   
   $(function() {
   	$( ".uploaded" ).sortable({
   		update: function() {
   		}
   	});
   });
   
   
   function resetFormData(){ 
   	$('.image-uploader').removeClass('has-files');
   	$('.uploaded').html('');
   }
</script>
@endpush