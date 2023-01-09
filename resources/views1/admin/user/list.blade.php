@extends('layouts/master')

@section('title',__(ucfirst($designation)))

@section('content')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h3> @lang('admin.stage') @lang('admin.one') @lang('admin.'.$designation) </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">@lang('admin.dashboard')</a></li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.user')</li>
                    <li class="breadcrumb-item" aria-current="page">@lang('admin.'.$designation)</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="bookmark">
                    <ul>
                        <a href="{{ route('admin.user.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-2"></i>@lang('admin.add') @lang('admin.user')</a>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 mb-3">
            <div class="btn-group w-100">
                <button class="btn btn-outline-primary"><a href="{{ route('admin.user.list.stage.one', [$designation]) }}">Stage One</a></button>
                <button class="btn btn-outline-primary"><a href="{{ route('admin.user.list.stage.two', [$designation]) }}">Stage Two</a></button>
                <button class="btn btn-outline-primary"><a href="{{ route('admin.user.list.stage.three', [$designation]) }}">Stage Three</a></button>
                <button class="btn btn-outline-primary"><a href="{{ route('admin.user.list.stage.four', [$designation]) }}">Stage Four</a></button>
                <button class="btn btn-outline-primary"><a href="{{ route('admin.user.list.stage.five', [$designation]) }}">Stage Five</a></button>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom_js')

<script>

</script>
@endpush