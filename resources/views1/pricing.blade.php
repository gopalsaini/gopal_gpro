@extends('layouts/app')

@section('title',__('Pricing'))

@section('content')

@push('custom_css')
<style>
    .loader{
        z-index: 1;
    }
    .loader .spinner-border{
        background: white;
    }
    .fs-dropdown {
        width: 94.3% !important;
    }
    .fs-label-wrap .fs-label {
        padding: 14px 22px 14px 16px !important;
    }
    .fs-label-wrap .fs-arrow {
        display: none;
    }

    .list-group-item.active {
        z-index: 2;
        color: #fff;
        background-color: #ffcd34;
        border-color: #ffcd34;
    }
</style>
@endpush

<div class="inner-banner-wrapper">
    <div class="container">
        <div class="step-form">
            <h4 class="inner-head inner-head-2">@lang('web/pricing.pricing')</h4>
            <h5 style="text-align:center;padding-top: 50px;">@lang('web/pricing.description')</h5>
            <form class="row">
                <div class="col-lg-12 price-country-bg position-relative">
                    <label for="">@lang('web/pricing.country-name')<span class="loader"></span></label>
                    <div class="common-select">
                        <select name="pricing" class="mt-2 select-icon fSelect" onchange="getPricing(this)">
                            @if (count($pricing) > 0)
                                <option value="">--@lang('web/pricing.select_one')--</option>
                            @foreach ($pricing as $price)
                                <option value="{{ $price->country_name }}">{{ $price->country_name }}</option>
                            @endforeach
                            @else
                                <option value="">No Data Found</option>
                            @endif
                        </select>
                        <span>
                            <svg width="18" height="11" viewBox="0 0 18 11" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M0 1.75297L1.75297 0L8.93345 7.18049L16.1139 0L17.8669 1.75297L8.93345 10.6864L0 1.75297Z"
                                    fill="black" />
                            </svg>
                        </span>
                    </div>
                </div>
                
                <div class="detail-price-wrap">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="list-group flex-row text-center" id="list-tab" role="tablist">
                                <a class="list-group-item list-group-item-action active" id="list-home-list" data-bs-toggle="list" href="#list-home" role="tab" aria-controls="list-home">@lang('web/pricing.with_early_bird')</a>
                                <a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">@lang('web/pricing.with_out_early_bird')</a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
                                    <ul>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WEB">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single_room_per_person')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WEB">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.deluxe-room-early-bird')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="trainers-early">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single_spouse_trainer')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="single-trainer-WEB">0.00</span></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                    <ul>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.twin-sharing')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="twin-sharing-WOEB">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-room')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="single-room-WOEB">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.deluxe-room-early-bird')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="trainers-after-WOEB">0.00</span></span>
                                        </li>
                                        <li>
                                            <p><span><img src="{{ asset('assets/images/vector.svg') }}" alt=""></span>@lang('web/pricing.single-trainer')</p>
                                            <span>:&nbsp; &nbsp; &nbsp;$<span class="single-trainer-WOEB">0.00</span></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                </div>
            </form>
        </div>
        <div class="step-form">
            <div class="col-md-12">
                <p>RREACH’s desire is to make the cost of attending the Congress affordable for qualified pastor trainers. Because delegates have to bear their travel costs, and with the potential for inflation worldwide, RREACH seeks to raise funds, and hopes to generously subsidize the price for all who attend the Congress with significant discounts and/or scholarships. After considering national socio-economic factors, fees for the Congress have been established per country.</p><p>Please note that everyone’s registration fees are less than the total cost per delegate to attend the Congress. RREACH is not attempting to recover our costs from the registration fees.</p><p>RREACH is offering great value for delegates who attend the Congress. Depending on which country you are coming from, your registration fee may be less than the cost of a two-night stay at the hotel – but you receive much more than that in return! Your registration fee includes:</p><p>1. 6-day admission to all programs and events at the Congress;</p><p>2. Twin-sharing accommodations for 5 nights at the Westin Playa Bonita Panama Hotel;</p><p>3. All meals, snacks, and non-alcoholic beverages during the Congress;</p><p>4. Transportation between the airport and the hotel on November 11, 12, 17 and 18;</p><p>5. Translation available in French, Portuguese, and Spanish; and</p><p>6. GProCongress II printed and digital materials.</p><p>Your registration fee DOES NOT include:</p><p>1. Round-trip airfare to/from Panama City; or</p><p>2. Any passport and/or visa fees you incur in connection with coming to the Congress.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom_js')
<script>
    function getPricing(select) {
        
        var country = $(select).val();
        if (country.length > 0) {

            $('.loader').html('<div class="spinner-border" role="status"></div>');
            $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('....');
            var pricing = @php echo json_encode($pricing); @endphp;

            var filter = pricing.filter( (item) => {
                return item.country_name === country ? item : false;
            });
            

           
            setTimeout(() => {
                if (filter.length > 0) {

                    var SingleRoomWEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                    if(SingleRoomWEB < 1075){
                        SingleRoomWEB = 975;
                    }else{
                        SingleRoomWEB = ((parseInt(filter[0].base_price)+parseInt(400))-parseInt(100));
                    }

                    var SingleRoomWOEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                    if(SingleRoomWOEB < 1075){
                        SingleRoomWOEB = 1075;
                    }else{
                        SingleRoomWOEB = ((parseInt(filter[0].base_price)+parseInt(400)));
                    }


                    $('.detail-price-wrap').find('.price').html(filter[0].base_price);
                    $('.detail-price-wrap').find('.twin-sharing-WEB').html((parseInt(filter[0].twin_sharing_per_person_deluxe_room)-parseInt(100)));
                    $('.detail-price-wrap').find('.twin-sharing-WOEB').html(filter[0].twin_sharing_per_person_deluxe_room);
                    $('.detail-price-wrap').find('.single-room-WEB').html(SingleRoomWEB);
                    $('.detail-price-wrap').find('.single-room-WOEB').html(SingleRoomWOEB);
                    $('.detail-price-wrap').find('.single-trainer-WEB').html(((parseInt(filter[0].base_price)+parseInt(1250))-parseInt(100)));
                    $('.detail-price-wrap').find('.single-trainer-WOEB').html(((parseInt(filter[0].base_price)+parseInt(1250))));
                    $('.detail-price-wrap').find('.early-bird').html(filter[0].early_bird_cost);
                    $('.detail-price-wrap').find('.trainers-early').html(filter[0].both_are_trainers_deluxe_room_early_bird);
                    $('.detail-price-wrap').find('.trainers-after-WOEB').html(filter[0].both_are_trainers_deluxe_room_after_early_bird);
                } else {
                    $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('0.00');
                }
                $('.loader').html('');
            }, 300);
        } else {
            $('.detail-price-wrap').find('.price, .twin-sharing, .early-bird, .trainers-early, .trainers-after').html('0.00');
        }
    }
</script>
@endpush

@push('custom_js')

    <script>

        $('.fSelect').fSelect({
			placeholder: 'Select some options',
			overflowText: '{n} selected',
			noResultsText: 'No results found',
			searchText: 'Search',
			showSearch: true
		});

    </script>

@endpush