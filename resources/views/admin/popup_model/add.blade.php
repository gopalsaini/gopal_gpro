@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Popup Model @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Popup Model</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Popup Model</li>
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
                    <a href="{{ route('admin.popup-model.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>Popup Model @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.popup-model.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							
							<div class="col-sm-6">
								<div class="form-group">
									<label class="form-group-label">PopUp Type </label>
									<div class="input-box">
										<select name="type" id="option" required class="selectType form-control PopUpType">
											<option value="0">--select type--</option>
											<option @if($result && $result->type=='1'){{'selected'}}@endif value="1">Early Bird</option>
											<option @if($result && $result->type=='2'){{'selected'}}@endif value="2">Passport Info</option>
											<option @if($result && $result->type=='3'){{'selected'}}@endif value="3">Travel Info</option>
											<option @if($result && $result->type=='4'){{'selected'}}@endif value="4">Session Info</option>
										</select>
										
									</div>
								</div>
							</div>
						
								
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">Expired Date:</label><br>
									<input class="form-control" type="date" name="expired_date" placeholder="Enter title" value="@if($result){{ $result->expired_date }}@endif" required>
								</div>
							</div>
							<div style="display:none" id="type1">Hey How are you Early Bird</div>
							<div style="display:none" id="type2">Hey How are you Passport</div>
							<div style="display:none" id="type3">Hey How are you Travel</div>
							<div style="display:none" id="type4">Hey How are you Session</div>
							

							<label for="input">English Language:</label><br>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.title'):</label>
									<input class="form-control" type="text" name="en_title" placeholder="Enter title" value="@if($result){{ $result->en_title }}@endif" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<label for="input">@lang('admin.description'):</label>
									<textarea id="" class="form-control summernote" name="en_description" placeholder="Enter description" cols="30" rows="5">@if($result){{ $result->en_description }}@endif</textarea>
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

	<script>

		

		$('.PopUpType').change(function(){
			var option = $(this).val();
			if(option == '1') {

				$('#type1').css('display', 'block');
				$('#type2, #type3, #type4').css('display', 'none');
			} else if(option == '2') {

				$('#type2').css('display', 'block');
				$('#type1, #type3, #type4').css('display', 'none');
			} else if(option == '3') {

				$('#type3').css('display', 'block');
				$('#type1, #type2, #type4').css('display', 'none');
			} else if(option == '0'){

				$('#type1, #type2, #type3').css('display', 'none');
			} else {

				$('#type4').css('display', 'block');
				$('#type1, #type2, #type3').css('display', 'none');
			}
		})
	</script>
@endpush