@extends('layouts/master')

@section('title')
View Information 
@section('content')

<div class="container-fluid">
	
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">@lang('admin.title'):</label><br>
								{{ $result->title }}
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label for="input">@lang('admin.description'):</label><br>
								{!! $result->description !!}
							</div>
						</div>
					</div>
						
				</div>
			</div>
		</div>
	</div>
</div>

@endsection