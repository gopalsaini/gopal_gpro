@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif User @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.user')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
					@if($result)
						<li class="breadcrumb-item" aria-current="page">@lang('admin.edit')</li>
					@else
						<li class="breadcrumb-item" aria-current="page">@lang('admin.add')</li>
					@endif
				</ol>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.user.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="col-sm-12">

							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.salutation'):</label>
										<select placeholder="Mr." required class="form-control" name="salutation">
											<option value="">--Select--</option>
											<option @if($result && $result->salutation=='Mr.'){{'selected'}}@endif value="Mr.">Mr.</option>
											<option @if($result && $result->salutation=='Ms.'){{'selected'}}@endif value="Ms.">Ms.</option>
											<option @if($result && $result->salutation=='Mrs'){{'selected'}}@endif value="Mrs">Mrs.</option>
											<option @if($result && $result->salutation=='Dr.'){{'selected'}}@endif value="Dr">Dr.</option>
											<option @if($result && $result->salutation=='Pastor'){{'selected'}}@endif value="Pastor">Pastor</option>
											<option @if($result && $result->salutation=='Bishop'){{'selected'}}@endif value="Bishop">Bishop</option>
											<option @if($result && $result->salutation=='Rev.'){{'selected'}}@endif value="Rev.">Rev.</option>
											<option @if($result && $result->salutation=='Prof.'){{'selected'}}@endif value="Prof.">Prof.</option>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.first') @lang('admin.name'):</label>
										<input class="form-control" type="text" name="first_name" placeholder="Enter first name" value="@if($result){{ $result->name }}@endif" required>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.last') @lang('admin.name'):</label>
										<input class="form-control" type="text" name="last_name" placeholder="Enter last name" value="@if($result){{ $result->last_name }}@endif" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.phone') @lang('admin.code'):</label>
										<select class="form-control" name="phone_code"> 
											<option value="" >--@lang('admin.code')--</option>
											@foreach($countries as $country)
												<option @if($result->phone_code==$country->phonecode){{'selected'}}@endif value="{{$country->phonecode}}">+{{$country->phonecode}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.mobile'):</label>
										<input type="tel" class="form-control" name="mobile" onkeypress="return /[0-9 ]/i.test(event.key)" required value="{{$result->mobile}}" autocomplete="off" placeholder="Enter mobile number">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.gender'):</label>
										<select id="name" placeholder="- Select -" class="form-control" name="gender" required>
											<option value="">- Select -</option>
											<option @if($result && $result->gender=='1'){{'selected'}}@endif value="1">@lang('admin.male')</option>
											<option @if($result && $result->gender=='2'){{'selected'}}@endif value="2">@lang('admin.female')</option>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.dob'):</label>
										<input type="date" value="{{$result->dob}}" placeholder="DD/ MM/ YYYY" class="form-control" name="dob" required>
									</div>
								</div>
								<!-- <div class="col-sm-4">
									<div class="form-group">
										<label for="input">@lang('admin.citizenship'):</label>
										<select class="form-control" name="citizenship" required>
											<option value="">--Select--</option>
											@foreach($countries as $country)
												<option @if($result && $result->citizenship == $country->name) {{'selected'}} @endif value="{{$country->name}}">{{$country->name}}</option>
											@endforeach
										</select>
									</div>
								</div> -->
							</div>
							
							<div class="btn-showcase text-center">
								@if(!$result)
								<button class="btn btn-light" type="reset">@lang('admin.reset')</button>
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