
@extends('layouts/app')

@section('title')
    @if(!empty($information)) {{ $information->title }} @endif
@endsection

@section('content')
<style>
    .MsoNormal span,.MsoNormal,p{
        margin: 0cm !important;
        font-size: 15px !important;
        font-family: Calibri, sans-serif !important;
        color: rgb(0, 0, 0) !important;
        letter-spacing: normal !important;
    }
</style>
   
    <div class="inner-banner-wrapper">
        <div class="container">
           
            <div class="step-form">

                @if(App::getLocale() == 'pt')

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->pt_title }}  @endif</h4>
                    <p>@if(!empty($information)) {!! $information->pt_description !!} @endif</p>
                    
                @elseif(App::getLocale() == 'sp')

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->sp_title }}  @endif</h4>
                    <p>@if(!empty($information)) {!! $information->sp_description !!} @endif</p>

                @elseif(App::getLocale() == 'fr')
                    
                    <h4 class="inner-head">@if(!empty($information)) {{ $information->fr_title }}  @endif</h4>
                    <p>@if(!empty($information)) {!! $information->fr_description !!} @endif</p>

                @else

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->title }} @endif</h4>
                    <p>@if(!empty($information)) {!! $information->description !!} @endif</p>

                @endif
            
                
            </div>
        </div>
    </div>

@endsection