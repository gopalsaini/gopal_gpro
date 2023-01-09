@php $segments = Request::segments(); @endphp
@if (count($segments) > 2 && count($segments) <= 4)
<div class="page-header">
    <div class="row">
        <div class="col-sm-6">
            <h3>
                @if ($segments[2] == 'list')
                {{ucwords($segments[1].'s')}}
                @elseif ($segments[2] == 'add')
                Add {{ucwords($segments[1])}}
                @elseif ($segments[2] == 'edit')
                Edit {{ucwords($segments[1])}}
                @endif
            </h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                @if ($segments[2] == 'list')
                <li class="breadcrumb-item active" aria-current="page">{{ucwords($segments[1])}}</li>
                @elseif ($segments[2] == 'add')
                <li class="breadcrumb-item"><a href="{{ route('admin.'.$segments[1].'.list') }}">{{ucwords($segments[1])}}</a></li>
                @elseif ($segments[2] == 'edit')
                <li class="breadcrumb-item"><a href="{{ route('admin.'.$segments[1].'.list') }}">{{ucwords($segments[1])}}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{$segments[2]}}</li>
            </ol>
        </div>
        <div class="col-sm-6">
            <div class="bookmark">
                <ul>
                    @if ($segments[2] == 'list')
                    <a href="{{ route('admin.'.$segments[1].'.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>Add
                            {{ucwords($segments[1])}}</a>
                    @elseif ($segments[2] == 'add' || $segments[2] == 'edit')
                    <a href="{{ route('admin.'.$segments[1].'.list') }}"
                            class="btn btn-primary"><i class="fas fa-list me-2"></i>{{ucwords($segments[1].'s')}}</a>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
