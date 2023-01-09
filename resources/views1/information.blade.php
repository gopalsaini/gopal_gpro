
@extends('layouts/app')

@section('title')
    @if(!empty($information)) {{ $information->title }} @else No Data Found @endif
@endsection

@section('content')

   
    <div class="inner-banner-wrapper">
        <div class="container">
           
            <div class="step-form">

                @if(App::getLocale() == 'pt')

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->pt_title }} @else No Data Found @endif</h4>
                    <p>@if(!empty($information)) {!! $information->pt_description !!} @endif</p>
                    
                @elseif(App::getLocale() == 'sp')

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->sp_title }} @else No Data Found @endif</h4>
                    <p>@if(!empty($information)) {!! $information->sp_description !!} @endif</p>

                @elseif(App::getLocale() == 'fr')
                    
                    <h4 class="inner-head">@if(!empty($information)) {{ $information->fr_title }} @else No Data Found @endif</h4>
                    <p>@if(!empty($information)) {!! $information->fr_description !!} @endif</p>

                @else

                    <h4 class="inner-head">@if(!empty($information)) {{ $information->title }} @else No Data Found @endif</h4>
                    <p>@if(!empty($information)) {!! $information->description !!} @endif</p>

                @endif
            
                
            </div>
        </div>
    </div>

@endsection