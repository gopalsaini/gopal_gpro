@extends('layouts/master')

@section('title')
User Database Management
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>User Database Management</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
					<li class="breadcrumb-item" aria-current="page">User Database Management</li>
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
								<label for="input">Email: <span style="color:red">*</span></label>
								<input id="email" class="form-control ministryUpdate" type="email" name="email" placeholder="Enter email address"  required>
							</div>
							<div class="form-group">
                                <label for="input">Type <span style="color:red">*</span></label>
                                <select class="form-select ministryUpdate" name="type" required aria-label="Default select example" id="option">
                                    <option selected>Select Type </option>
                                    <option value="1">Recover Deleted user</option>
                                    <option value="2">Move user from Stage2 to Stage-1</option>
                                    <option value="3">Separating Couples</option>
                                    <option value="4">Ministry Update</option>
                                </select>
							</div>
							<div id="MinistryHtml"></div>
							<br><br>
							<div class="btn-showcase text-center">
								@if(!$result)
								<button class="btn btn-light" type="reset" id="DataReset">@lang('admin.reset')</button>
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
@push('custom_js')

<script>

	$('#DataReset').click(function(){
		$('#MinistryHtml').html("");
	});

	$('.ministryUpdate').change( function() {

		$('#MinistryHtml').html("");

        if($("#option").val() == '4'){

			if($('#email').val() == ''){
				
				alert('Please enter Email id first!');
				$('.ministryUpdate option:first').prop('selected',true).trigger( "change" );;
			}else{
				$('#preloader').css('display', 'block');
				$.ajax({

					url: "{{url('admin/user/get-ministry-data/user?emailId=')}}" + $('#email').val(),
					dataType: 'json',
					type: 'get',
					error: function(xhr, textStatus) {
						if (xhr && xhr.responseJSON.message) {
							sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
						} else {
							sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
						}
						$('#MinistryHtml').html('');
						$('#preloader').css('display', 'none');
					},
					success: function(data) {
						
						$('#MinistryHtml').html(data.html);
						$('#preloader').css('display', 'none');

					},
					cache: false,
					timeout: 5000
				});

			}

        }
    }); 
</script>
@endpush
