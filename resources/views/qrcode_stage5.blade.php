
@extends('layouts/app')

@section('title',__(Lang::get('web/home.qr-code')))

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>

            <br>
            <br>
            <br>
            <div >
                <div class="card p-5">
                    <br>
                    <div class="card-header">
                        <h2>@lang('web/home.your-qr-code')</h2>
                    </div>
                    <br>
                    <div class="card-body">
                        {!! $resultData['result']['qrcode'] !!}
                    </div>
                </div>

            </div>
            <br>
            <br>
            <br>
           
        </div>
    </div>
    <!-- banner-end -->

@endsection


@push('custom_js')

<script>

    

</script>
@endpush