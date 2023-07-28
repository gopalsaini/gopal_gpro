


@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Delivery Affinity @endsection
@section('content')
<style>
	hr {
		margin: 3px 0 !important;
	}
</style>
<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Delivery Affinity</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Delivery Affinity</li>
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
                    <a href="{{ route('admin.Delivery_Affinity.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i> @lang('admin.list')</a>
                </ul>
            </div>
        </div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-body add-post">
					
						
						<div class="row">
							<div class="col-sm-6" >
								<h5>Select Users </h5>
								
								<div class="pb-3" tabindex="0"><span class="current">
									<input type="text" style="height: 50px;" class="form-control" id="myInput" onkeyup="userSearch()" placeholder="Search Users.." title="User"></span>
								</div>
								<div style="border: 1px solid #00000014;height:500px;padding:10px;overflow: scroll;" id="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
									
								@if(!empty($users) && count($users)>0)
									@foreach($users as $user)

										<div class="SelectLocality" draggable="true" ondragstart="drag(event)" id="drag{{$user->id}}" >
											
											<input id="user{{$user->id}}" value="{{$user->id}}" type="hidden" name="users[]" > 
											{{$user->name}} {{$user->last_name}} ({{$user->email}})
											
											<hr>
										</div>
										
									@endforeach
								@endif
									
								</div>
								
							</div>

							<div class="col-sm-6" >

							<form id="form" action="{{ route('admin.Delivery_Affinity.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
								@csrf
								<input id="type" value="@if(!empty($result)) {{$result['id']}} @else {{'0'}}@endif " type="hidden" name="id" > 
								<div class="row" >
									<div class="col-sm-12 pb-3">
										<label for="input">Enter Name <span style="color:red">*</span></label>
										<input class="form-control" type="text" name="name" placeholder="Enter Name" value="" required>
									</div>
									
									<div class="col-sm-12" >
										<h5>Group Members</h5>
										<div style="border: 1px solid #00000014;height:400px;padding:10px;overflow: scroll;"  id="div3" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
										
										</div>
									
									</div>
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
	function allowDrop(ev) {
		ev.preventDefault();
	}

	function drag(ev) {
		ev.dataTransfer.setData("text", ev.target.id);
		
	}

	function drop(ev) {
		
		ev.preventDefault();
		if(ev.target.id){
			var data = ev.dataTransfer.getData("text");
			
			ev.target.appendChild(document.getElementById(data));
		}else{
			return
		}
		
	}
</script>

<script>
	function userSearch() {
		var input, filter, ul, li, a, i, txtValue;
		input = document.getElementById("myInput");
		filter = input.value.toUpperCase();
		ul = document.getElementById("div1");
		a = ul.getElementsByClassName("SelectLocality");
		for (i = 0; i < a.length; i++) {
			txtValue = a[i].textContent || a[i].innerText;
			if (txtValue.toUpperCase().indexOf(filter) > -1) {
				a[i].style.display = "";
			} else {
				a[i].style.display = "none";
			}
		}
	}

	
</script>
@endpush

