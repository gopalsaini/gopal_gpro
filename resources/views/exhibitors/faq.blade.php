
@extends('layouts/app')

@section('title',__(Lang::get('web/faq.title')))

@section('content')

    <style>
    .price-country-bg span {
        position: inherit !important;
        
    }
    .accordion-button {
    
        display: block !important;
    }
</style>
    <div class="inner-banner-wrapper">
        <div class="container">
            <div class="step-form">
                <h4 class="inner-head inner-head-2">@lang('web/faq.title')</h4>
                <h5 style="text-align:center;padding-top: 50px;"><span style="font-weight: normal;">"</span>@lang('web/faq.description')<span style="font-weight: normal;">"</span></h5>
                <h5 style="text-align:center;padding-top: 10px;">@lang('web/faq.author')</h5>

                @if(count($categories) > 0)
                @foreach ($categories as $category)
                <div class="row mt-5">
                    <div class="col-lg-12 price-country-bg position-relative">
                        @php $categoryName = \App\Models\Category::where('id',$category->category)->where('status','1')->first(); @endphp
                        
                        @if(App::getLocale() == 'pt')
                            <h5 style="padding-bottom: 10px;">{{$categoryName->pt_name ?? ''}}</h5>
                        @elseif(App::getLocale() == 'sp')
                            <h5 style="padding-bottom: 10px;">{{$categoryName->sp_name ?? ''}}</h5>
                        @elseif(App::getLocale() == 'fr')
                            <h5 style="padding-bottom: 10px;">{{$categoryName->fr_name ?? ''}}</h5>
                        @else
                            <h5 style="padding-bottom: 10px;">{{$categoryName->name ?? ''}}</h5>
                        @endif

                        
                        @if(count($faqs) > 0)
                            <div class="accordion payment-accordion" id="accordionExample{{$category->category}}">
                                @foreach ($faqs as $key=>$faq)
                                @if($faq->category == $category->category)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{$key}}">
                                        <button class="accordion-button @if($key != '0') collapsed @endif" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{$key}}" aria-expanded="true"
                                            aria-controls="collapse{{$key}}">
                                            @if(App::getLocale() == 'pt')
                                                <strong>@lang('web/faq.Question') : </strong> &nbsp;{{ $faq->pt_question }}
                                            @elseif(App::getLocale() == 'sp')
                                                <strong>@lang('web/faq.Question') : </strong> &nbsp;{{ $faq->sp_question }}
                                            @elseif(App::getLocale() == 'fr')
                                                <strong>@lang('web/faq.Question') : </strong> &nbsp;{{ $faq->fr_question }}
                                            @else
                                                <strong>@lang('web/faq.Question') : </strong> &nbsp;{{ $faq->question }}
                                            @endif

                                        </button>
                                    </h2>
                                    <div id="collapse{{$key}}" class="accordion-collapse collapse @if($key == '0') show @endif"
                                        aria-labelledby="heading{{$key}}" data-bs-parent="#accordionExample{{$category->category}}">
                                        <div class="accordion-body">
                                            @if(App::getLocale() == 'pt')
                                                <strong>@lang('web/faq.Answer') : </strong> <p>&nbsp;{!! $faq->pt_answer !!}</p>
                                            @elseif(App::getLocale() == 'sp')
                                                <strong>@lang('web/faq.Answer') : </strong> <p>&nbsp;{!! $faq->sp_answer !!}</p>
                                            @elseif(App::getLocale() == 'fr')
                                                <strong>@lang('web/faq.Answer') : </strong> <p>&nbsp;{!! $faq->fr_answer !!}</p>
                                            @else
                                                <strong>@lang('web/faq.Answer') : </strong> <p>&nbsp;{!! $faq->answer !!}</p>
                                            @endif
                                        
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        
                        @endif
                    </div>
                </div>
                @endforeach
                
                @endif
            </div>
        </div>
    </div>

@endsection