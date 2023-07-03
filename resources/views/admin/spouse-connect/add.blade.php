@extends('layouts/master')

@section('title')
@if($result) Edit @else Add @endif Spouse Connect @endsection
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
				<h3>@if($result) @lang('admin.edit') @else @lang('admin.add') @endif Spouse Connect</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Spouse</li>
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
						<a href="{{ route('admin.spouse-connect.list') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i>  @lang('admin.list')</a>
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
							<div class="col-sm-4" >
								<p>Male - Any Male candidate who is coming alone(whether Married or Unmarried), is at Stage-3 and no Spouse confirmations Pending</p>
								
								<div class="pb-3" tabindex="0"><span class="current">
									<input type="text" style="height: 50px;" class="form-control" id="myInput" onkeyup="userSearch()" placeholder="Search Users.." title="User"></span>
								</div>
								<div style="border: 1px solid #00000014;height:400px;padding:10px;overflow: scroll;" id="div1" ondrop="drop(event)" ondragover="allowDrop(event)">
									
								@if(!empty($users) && count($users)>0)
									@foreach($users as $user)

										@php $results = \App\Models\User::where([['user_type', '!=', '1'], ['parent_id', $user->id],['added_as', 'Spouse']])->first() @endphp
                                        
										@if(!$results  && $user->gender == '1')
											<div class="SelectLocality" draggable="true" ondragstart="drag(event)" id="drag{{$user->id}}"  data-id="Male">
												
												<input value="{{$user->id}}" type="hidden" name="users[]" > 
												{{$user->name}} {{$user->last_name}} ({{$user->email}})
												
												<hr>
											</div>
										@endif
									@endforeach
								@endif
									
								</div>
								
							</div>
							<div class="col-sm-4" >
								<p>Female - Any Female candidate who is coming alone(whether Married or Unmarried), is at Stage-3 and no Spouse confirmations Pending</p>
								
								<div class="pb-3" tabindex="0"><span class="current">
									<input type="text" style="height: 50px;" class="form-control" id="spousesearch" onkeyup="userSearch2()" placeholder="Search Users.." title="User"></span>
								</div>
								<div style="border: 1px solid #00000014;height:400px;padding:10px;overflow: scroll;" id="div2" ondrop="drop(event)" ondragover="allowDrop(event)" data-div="Female">
									
								@if(!empty($users) && count($users)>0)
									@foreach($users as $user)

										@php $results = \App\Models\User::where([['user_type', '!=', '1'], ['parent_id', $user->id],['added_as', 'Spouse']])->first() @endphp
                                        
										@if(!$results && $user->gender == '2')
											<div class="SelectLocality" draggable="true" ondragstart="drag(event)" id="drag{{$user->id}}" data-id="Female">
												
												<input id="user{{$user->id}}" value="{{$user->id}}" type="hidden" name="users[]" > 
												{{$user->name}} {{$user->last_name}} ({{$user->email}})
												
												<hr>
											</div>
										@endif
									@endforeach
								@endif
									
								</div>
								
							</div>

							<div class="col-sm-4" >

							<form id="form" action="{{ route('admin.spouse-connect.add') }}" method="post" enctype="multipart/form-data" autocomplete="off">
								@csrf
								<input id="type" value="add" type="hidden" name="type" > 
								<div class="row" >

									<div class="col-sm-12 pb-3" >
										<h5>Husband </h5>
										<div style="border: 1px solid #00000014;height:50px;padding:10px"  id="div3" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
										</div>
									</div>
									<div class="col-sm-12" >
										<h5>Spouse</h5>
										<div style="border: 1px solid #00000014;height:370px;padding:10px;overflow: scroll;"  id="div4" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
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

		var dataId = $('#'+ev.target.id).attr('data-id');
		
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


<script>
	function userSearch2() {
		var input, filter, ul, li, a, i, txtValue;
		input = document.getElementById("spousesearch");
		filter = input.value.toUpperCase();
		ul = document.getElementById("div2");
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
