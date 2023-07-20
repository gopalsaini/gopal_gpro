@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif User @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>Send Invitation</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
					
						<li class="breadcrumb-item" aria-current="page">Send Invitation</li>
					
				</ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="Userform" action="{{ route('admin.user.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.designation'): <span style="color:red">*</span></label>
									<select name="designation_id" class="form-control userType" required>
										<option value="">Select</option>

										@if (count($designations) > 0)
											@foreach ($designations as $designation)
												@if($designation->id == 2 || $designation->id == 3 || $designation->id == 4 || $designation->id == 6)
												<option value="{{$designation->id}}" @if($result && $result->designation_id == $designation->id) selected @endif>{{$designation->designations}}</option>
												@endif
											@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input">@lang('admin.email'): <span style="color:red">*</span></label>
									<input class="form-control" type="email" name="email" placeholder="Enter email address" value="@if($result){{ $result->email }}@endif" required>
								</div>
							</div>
							
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input">Name: <span style="color:red">*</span></label>
									<input class="form-control" type="text" name="name" placeholder="Enter Name" value="@if($result){{ $result->name }}@endif" required>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="input">@lang('web/app.Languages'): <span style="color:red">*</span></label>
									<select name="language" id="language" required class="selectLanguage form-control">
										<option  value="en">English</option>
										<option  value="sp">Spanish</option>
										<option  value="fr">French</option>
										<option  value="pt">Portuguese</option>
									</select>
								</div>
							</div>
							<div class="col-lg-6 passwordShow" style="display:none">
								<div class="form-group">		
									<label for="input">Password:</label>
									<input class="form-control" type="text" id="password" name="password" placeholder="Enter Password" value="" >					
								</div>
							</div>
							<div class="btn-showcase text-center">
								@if(!$result)
								<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
								@endif
								<button class="btn btn-primary" type="submit" form="Userform">@lang('admin.submit')</button>
							</div>
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
	$("form#Userform").submit(function(e) {

		e.preventDefault();

		var formId = $(this).attr('id');
		var formAction = $(this).attr('action');
		var btnhtml = $("button[form="+formId+"]").html();

		$.ajax({
			url: formAction,
			data: new FormData(this),
			dataType: 'json',
			type: 'post',
			beforeSend: function() {
				submitButton(formId, btnhtml, true);
			},
			error: function(xhr, textStatus) {

				if (xhr && xhr.responseJSON.message) {
					sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
				} else {
					sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
				}
				submitButton(formId, btnhtml, false);
			},
			success: function(data) {
				if (data.error) {
					sweetAlertMsg('error', data.message);
				} else {

					if(data.ministryUpdate){
						$('#' + formId)[0].reset();
						$('#MinistryHtml').html("");
					}
					if (data.reset) {
						$('#' + formId)[0].reset();
						$('#MinistryHtml').html("");

						if($('.previewimages').length > 0) {
							$('.previewimages').html('');
						}
						if($('#summernote').length > 0) {
							$('#summernote').summernote('reset');
						}
						if($('.js-example-tags').length > 0){
							$(".js-example-tags").select2({
								allowClear: true
							});
						}

						if (data.userUpdateUrl != '') {
							location.href = data.userUpdateUrl;
						}
					}

					if (data.reload) {
						location.reload();
					}

					if (data.script) {
						resetFormData();
					}
					
					
					if (data.comment && $('#commentstablelist').length > 0) {
						$('#commentstablelist').DataTable().ajax.reload();
						$('#userHistoryList').DataTable().ajax.reload();
					}
					sweetAlertMsg('success', data.message);

				}
				submitButton(formId, btnhtml, false);
			},
			cache: false,
			contentType: false,
			processData: false,
		});

	});

	$('.userType').on('change', function() {
        if(this.value == '3' || this.value == '4' || this.value == '6'){

            $(".passwordShow").css('display','block');
            $("#password").attr('required',true);
            $("#password").val(null);
        }else{

            $(".passwordShow").css('display','none');
            $("#password").attr('required',false);
			$("#password").val(null);
        }
    }); 
</script>
@endpush