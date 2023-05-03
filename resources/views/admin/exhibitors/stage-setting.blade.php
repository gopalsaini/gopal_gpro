@extends('layouts/master')

@section('title',__('User Stage Setting'))

@section('content')

<div class="container-fluid">
	<div class="page-header">
		<div class="row">
			<div class="col-sm-6">
				<h3>@lang('admin.user') @lang('admin.stage') @lang('admin.setting') </h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
					<li class="breadcrumb-item" aria-current="page">@lang('admin.stage') @lang('admin.setting')</li>
				</ol>
			</div>
		</div>
	</div> <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display datatables" id="tablelist">
                            <thead>
                                <tr>
                                    <th> @lang('admin.user') @lang('admin.type') </th>
                                    <th> @lang('admin.stage') @lang('admin.zero') </th>
                                    <th> @lang('admin.stage') @lang('admin.one') </th>
                                    <th> @lang('admin.stage') @lang('admin.two') </th>
                                    <th> @lang('admin.stage') @lang('admin.three') </th>
                                    <th> @lang('admin.stage') @lang('admin.four') </th>
                                    <th> @lang('admin.stage') @lang('admin.five') </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
								@if (count($results) > 0)
								@foreach ($results as $result)
									<tr>
										<td>@lang('admin.'.strtolower($result->Designation->designations))</td>
										<td><input type="checkbox" name="stage_zero" @if($result->stage_zero == '1') checked @endif></td>
										<td><input type="checkbox" name="stage_one" @if($result->stage_one == '1') checked @endif></td>
										<td><input type="checkbox" name="stage_two" @if($result->stage_two == '1') checked @endif></td>
										<td><input type="checkbox" name="stage_three" @if($result->stage_three == '1') checked @endif></td>
										<td><input type="checkbox" name="stage_four" @if($result->stage_four == '1') checked @endif></td>
										<td><input type="checkbox" name="stage_five" @if($result->stage_five == '1') checked @endif></td>
										<td>
											@if ($result->stage_zero == '0' && $result->stage_one == '0' && $result->stage_two == '0' && $result->stage_three == '0' && $result->stage_four == '0' && $result->stage_five == '0')
											<a class="btn btn-sm btn-primary px-3 save" data-id="{{$result->id}}">@lang('admin.save')</a>
											@endif
										</td>
									</tr>
								@endforeach
								@endif
                            </tbody>
                            <tfoot>
                                <tr>
									<th> @lang('admin.user') @lang('admin.type') </th>
                                    <th> @lang('admin.stage') @lang('admin.zero') </th>
                                    <th> @lang('admin.stage') @lang('admin.one') </th>
                                    <th> @lang('admin.stage') @lang('admin.two') </th>
                                    <th> @lang('admin.stage') @lang('admin.three') </th>
                                    <th> @lang('admin.stage') @lang('admin.four') </th>
                                    <th> @lang('admin.stage') @lang('admin.five') </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom_js')

<script>
$(document).ready(function() {
    fill_datatable();
    $('#tablelist').DataTable({
        "processing": true,
        "searching": true,
        "ordering": true,

        "fnDrawCallback": function() {
            fill_datatable();
        },
        "order": [1, 'asc'],
		"columnDefs": [{
                className: "text-center",
                targets: "_all"
            },
            {
                orderable: false,
                targets: [-1]
            },
        ],
    });
});

function fill_datatable() {
    $('.save').click(function() {
        var button = $(this);
        var id = $(this).data('id');
        var stage_zero = $(this).closest('tr').find('input[name="stage_zero"]').prop('checked') == true ? 1 : 0;
        var stage_one = $(this).closest('tr').find('input[name="stage_one"]').prop('checked') == true ? 1 : 0;
        var stage_two = $(this).closest('tr').find('input[name="stage_two"]').prop('checked') == true ? 1 : 0;
        var stage_three = $(this).closest('tr').find('input[name="stage_three"]').prop('checked') == true ? 1 : 0;
        var stage_four = $(this).closest('tr').find('input[name="stage_four"]').prop('checked') == true ? 1 : 0;
        var stage_five = $(this).closest('tr').find('input[name="stage_five"]').prop('checked') == true ? 1 : 0;

        $.ajax({
            type: "POST",
            dataType: "json",
            url: "{{ route('admin.user.stage.setting') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id': id,  'stage_zero': stage_zero, 'stage_one': stage_one, 'stage_two': stage_two, 'stage_three': stage_three, 'stage_four': stage_four, 'stage_five': stage_five 
            },
            beforeSend: function() {
                $('#preloader').css('display', 'block');
            },
            error: function(xhr, textStatus) {

                if (xhr && xhr.responseJSON.message) {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.responseJSON.message);
                } else {
                    sweetAlertMsg('error', xhr.status + ': ' + xhr.statusText);
                }
                $('#preloader').css('display', 'none');
            },
            success: function(data) {
                $('#preloader').css('display', 'none');
                sweetAlertMsg('success', data.message);
				if (data) {
					button.hide();
				}
            }
        });
    });
}
</script>
@endpush