
@extends('exhibitors/layouts/app')

@section('title',__(Lang::get('web/home.qr-code')))

@section('content')


    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =$resultData['result']['id']; @endphp
                @include('exhibitors.sidebar', compact('userId'))
            </div>
            <div class="step-form">
                  
                <h4 class="inner-head">@lang('web/home.your-qr-code')</h4>
                <!-- //Vineet - 080123 -->
                    <div class="detail-wrap">
                        <ul>
                            <li>
                                <div class="row">
                                    <div class="alphabet-vd-box">
                                        {!! $resultData['result']['qrcode'] !!}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    
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