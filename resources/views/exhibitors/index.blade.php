
@extends('exhibitors/layouts/app')

@section('title',__(Lang::get('web/app.home')))

@push('custom_css')
    <style>
        .requirement .requirement-wrapper {
            display:flex;
        }
        .testimonial-wrapper {
            padding: 0 0 100px 0;
            padding-bottom: 174px !important;
        }
        .gpro-wrapper {
            position: relative;
            padding-bottom: 72px !important;
        }
        .banner-wrapper{
            position: relative;
        }
        .banner-wrapper::after{
            position: absolute;
            content: "";
            background: #0006;
            width: 100%;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 5;
        }
        .ban-wrap{
            position: relative;
            z-index: 99;
        }
        .footer-box:hover{

            background-color: #ffcd34;
            
        }
    </style>
@endpush

@section('content')

    <!-- banner-start -->
    <div class="banner-wrapper" style="background-image: url({{ asset('images/exhibitor_home.jpg')}})">
        <div class="container ban-wrap">
            <div class="banner-head">
                <!-- <p>@lang('web/home.banner-description2')</p> -->
                <h1>@lang('web/home.banner-heading') </h1>
                <h2 style="color:white;margin-top:33px">Exhibitor Portal</h2>
                <h3 style="color:white;margin-top:33px">@lang('web/home.date_heading') </h3>
                <h3 style="color:white">“@lang('web/home.banner-description1')”</h3> 
                
                <ul class="date-map">
				<!--
                    <li>
                        <a href="javascript:;"><i class="fas fa-map-marker-alt"></i>2108 Selah Way Brattleboro, VT
                            05301</a>
                    </li>
					-->
                    <!-- <li>
                        <a href="javascript:;"><i class="fas fa-calendar-alt"></i>@lang('web/home.november') 12-17, 2023 (D.V.)</a>
                    </li> -->
                </ul>
                <div class="timer-wrapper">
                    <div id="countdown">
                        <ul>
                            <li><span id="days"></span> @lang('web/home.days')</li>
                            <li><span id="hours"></span>@lang('web/home.hours')</li>
                            <li><span id="minutes"></span>@lang('web/home.minutes')</li>
                            <li><span id="seconds"></span>@lang('web/home.seconds')</li>
                        </ul>
                    </div>
                </div>
               
                <div class="banner-btn">
                    <ul>
                        @if(!\Session::has('gpro_exhibitor'))
                        <li><a href="{{url('exhibitor-register')}}" class="main-btn" >@lang('web/home.register') @lang('web/home.now')</a></li>
                        @endif
                        <li><a href="{{ url('exhibitor-policy') }}" class="main-btn">Exhibitor Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- banner-end -->

    <!-- video section start -->
    
    
    <br>
    <footer style="padding-top: 66px;padding-bottom: 66px;">
        <div class="container">
            <div class="row">
                <h2 class="main-head">Registration Options</h2>
                <h5 style="text-align:center;padding-top: 50px;"></h5>
                <div class="col-lg-6">
                    <div class="footer-box" style="text-align: left;">
                        <h4 style="font-size: 20px;">Option 1 – Includes single room at Dreams Hotel.</h4>
                        <span style="text-align: left;font-size: 20px;"><b>Cost to attend the Congress –</b></span> <br><br>
                        <span style="text-align: left;font-size: 20px;">a.   The cost for each exhibitor is $1,500 USD (includes registration fee and single room occupancy for 5 nights).</span><br>
                        <span style="text-align: left;font-size: 20px;">b.   If your spouse accompanies you, the cost will be $1,850.00 (includes registration fee for two people, and single room occupancy for 5 nights).</span><br>
                        <span style="text-align: left;font-size: 20px;">c.   If you bring a teammate who needs an additional room, the cost will be $2,500.00 (includes registration fee for two people, and two single rooms for 5 nights).</span><br>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-box" style="text-align: left;">
                        <h4 style="font-size: 20px;">Option 2 – Exhibitors provide their own accommodations.</h4>
                        <span style="text-align: left;font-size: 20px;"><b>Cost to attend the Congress –</b></span> <br><br>
                        <span style="text-align: left;font-size: 20px;">Registration fee – $750 USD for one person; $300 USD for each additional person.</span>
                    </div>
                </div>
            </div>
           
        </div>
    </footer>
    <!-- testimonial-end -->

@endsection 

@push('custom_js')
    <script>
        countdownStart();
    </script>
@endpush
