@extends('layouts/master')

@section('title')
View Notification @endsection
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
	
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">User :</label><br>
								{{\App\Helpers\commonHelper::getUserNameById($result->user_id)}}
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">Title :</label><br>
								{{ $result->title }}
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">Message:</label><br>
								{!!  $result->message !!}
							</div>
						</div>
					</div>
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
	$(".js-example-tags").select2({
		tags: true
	});
</script>
@endpush