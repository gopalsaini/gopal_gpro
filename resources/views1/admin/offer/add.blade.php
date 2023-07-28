@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Offer @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.offer')</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.offer')</li>
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
                    <a href="{{ route('admin.offer.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>@lang('admin.offer') @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.offer.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<div class="row">
							<!-- <div class="col-sm-4">
								<div class="form-group">
									<label for="input">@lang('admin.offer') @lang('admin.type'):</label>
									<select name="offer_type" class="form-control" required>
										<option value="1" @if($result && $result->offer_type == '1') selected @endif>One</option>
										<option value="2" @if($result && $result->offer_type == '2') selected @endif>Two</option>
										<option value="3" @if($result && $result->offer_type == '3') selected @endif>Three</option>
									</select>
								</div>
							</div> -->
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.name'):</label>
									<input class="form-control" type="text" name="name" placeholder="Enter offer name" value="@if($result){{ $result->name }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.code'):</label>
									<input class="form-control" type="text" name="code" placeholder="Enter offer code" value="@if($result){{ $result->code }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.start') @lang('admin.date'):</label>
									<input class="form-control" type="date" name="start_date" value="@if($result){{ $result->start_date }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.end') @lang('admin.date'):</label>
									<input class="form-control" type="date" name="end_date" value="@if($result){{ $result->end_date }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.discount') @lang('admin.type'):</label>
									<select name="discount_type" class="form-control">
										<option value="1" @if($result && $result->discount_type == '1') selected @endif>%</option>
										<option value="2" @if($result && $result->discount_type == '2') selected @endif>Flat</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.discount') @lang('admin.value'):</label>
									<input class="form-control" type="number" name="discount_value" placeholder="Enter discount value" value="@if($result){{ $result->discount_value }}@endif" required>
								</div>
							</div>
							<!-- <div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.is') @lang('admin.partial') @lang('admin.amount'):</label>
									<select name="is_partial_amount" class="form-control -change" required>
										<option value="1" @if($result && $result->is_partial_amount == '1') selected @endif>Yes</option>
										<option value="0" @if($result && $result->is_partial_amount == '0') selected @endif>No</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6 partial_amount_div" @if($result && $result->is_partial_amount == '0') style="display: none;" @endif>
								<div class="form-group">
									<label for="input">@lang('admin.partial') @lang('admin.amount'):</label>
									<input class="form-control" type="number" name="partial_amount" placeholder="Enter partial amount" value="@if($result){{ $result->partial_amount }}@endif" required>
								</div>
							</div> -->
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
	$(document).ready(function () {
		$('.-change').change(function() {
			if($(this).val() == '1') {
				$('.partial_amount_div').show();
			} else {
				$('.partial_amount_div').hide();
			}
		});
	})
</script>
@endpush