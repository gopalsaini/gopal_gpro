@extends('layouts/master')

@section('title',__('Stage All'))

@push('custom_css')
    <style>
        .odd{
            position: relative;
        }

        .group-user-list{
            position: absolute;
            left: 87px;
        }
        .dataTables_wrapper table.dataTable tbody td:nth-child(2)  { 
            padding-left: 45px !important;
        }
        .btn-outline-primary:focus,.btn-outline-primary:hover, .btn-outline-primary.active { 
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        } 

        .group-user-list {
            background: url('{{asset("admin-assets/images/details_open.png")}}') no-repeat center center;
            cursor: pointer;
            width: 25px;
            height: 25px;
        }
        .shown .group-user-list {
            background: url('{{asset("admin-assets/images/details_close.png")}}') no-repeat center center;
        }
    </style>
    
@endpush 
@section('content') 
<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> Community List</h3>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
					<li class="breadcrumb-item" aria-current="page">Community</li>
					<li class="breadcrumb-item" aria-current="page">List</li>
					
				</ol>
            </div>
           
            <div class="col-sm-3">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.community.add') }}" class="btn btn-primary"><i class="fas fa-list me-2"></i> Group Add</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                
                    <div class="table-responsive">
                        
                        <table id="example2" class="display">
                            <thead>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> Name </th>
                                    <th> @lang('admin.user') </th>
                                    <th> Mobile </th>
                                    <th> @lang('admin.action') </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($datas) && count($datas)>0)
                                    @php $sn = 1; @endphp
                                    @foreach($datas as $data)
                                        @php $results = \App\Models\User::where([['user_type', '!=', '1'], ['parent_id', $data->id],['added_as', 'Group']])->get() @endphp
                                        @if(!empty($results) && count($results)>0)
                                            <tr>
                                                <td>{{$sn}}</td>
                                                <td>{{$data->name}} {{$data->last_name}}</td>
                                                <td><a href="javascript:void(0)" class="group-user-list" data-email="{{$data->email}}"></a> {{$data->email}}</td>
                                                <td>+{{$data->phone_code}} {{$data->mobile}}</td>
                                                
                                                <td>
                                                    <div style="display:flex"><a href="{{route('admin.community.group.update', ['id' => $data->id] )}}" title="Update Group" class="btn btn-sm btn-primary px-3 m-1 text-white "><i class="fas fa-edit"></i></a></div>
                                                </td>
                                            </tr>

                                            @php $sn++; @endphp
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> @lang('admin.id') </th>
                                    <th> Name </th>
                                    <th> @lang('admin.user') </th>
                                    <th> Mobile </th>
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

        var table = $('#example2').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
        });

        $('#example2 tbody').on('click', '.group-user-list', function () {
            var email = $(this).data('email');
            
            var tr = $(this).parents('tr');
            var row = table.row(tr);
    
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {

                $('#preloader').css('display', 'block');
                $.post("{{ route('admin.user.group.users.list.edit') }}", { _token: "{{ csrf_token() }}", email: email }, function(data) {
                    row.child(data.html).show();
                    $('#preloader').css('display', 'none');
                }, "json");

                tr.addClass('shown');
            }
        });
        
    });


    

function fill_datatable() {

   
}

</script>
@endpush