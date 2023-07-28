@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Sub Offer @endsection
@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3> {{ \App\Helpers\commonHelper::getDataById('Offer', $offer_id, 'name') }} @if($result) @lang('admin.edit') @else @lang('admin.add') @endif @lang('admin.sub') @lang('admin.offer') </h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">{{ \App\Helpers\commonHelper::getDataById('Offer', $offer_id, 'name') }}</li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.sub') @lang('admin.offer')</li>
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
                    <a href="{{ route('admin.sub.offer.list', [$offer_id]) }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>@lang('admin.sub') @lang('admin.offer') @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					<form id="form" action="{{ route('admin.sub.offer.add', [$offer_id]) }}" method="post" enctype="multipart/form-data" autocomplete="off">
						@csrf
						<input type="hidden" value="@if($result){{ $result->id }} @else 0 @endif" name="id" required />
						<input type="hidden" value="{{ $offer_id }}" name="offer_id" required />
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.name'):</label>
									<input class="form-control" type="text" name="name" placeholder="Enter sub offer name" value="@if($result){{ $result->name }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.initial') @lang('admin.amount'):</label>
									<input class="form-control" type="number" name="initial_amount" placeholder="Enter initial amount" value="@if($result){{ $result->initial_amount }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.final') @lang('admin.amount'):</label>
									<input class="form-control" type="number" name="final_amount" placeholder="Enter final amount" value="@if($result){{ $result->final_amount }}@endif" required>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="input">@lang('admin.instant') @lang('admin.discount'):</label>
									<input class="form-control" type="number" name="instant_discount" placeholder="Enter discount value" value="@if($result){{ $result->instant_discount }}@endif" required>
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