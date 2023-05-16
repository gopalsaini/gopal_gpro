<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title') | @lang('web/home.banner-heading')</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('Favicon.png')}}" />

    <!-- css-files -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fSelect.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script>
        var baseUrl = "{{ url('/') }}";
    </script>

    <style>
        .selectLanguage {
            width: 100%;
            height: 50px;
            background-color: #F9F9F9;
            border: 1px solid transparent;
            padding: 10px 15px;
            position: relative;
            margin-top: 12px;
        }
        .requirement .requirement-wrapper {
          
            bottom: auto !important;
        }
        footer {
            padding-top: 62px !important;
        }
    </style>

    @stack('custom_css')
    @php  $informations = \App\Models\Information::where('status', '1')->get(); @endphp
</head>

<body>
    
    <!---header section start-->
    <div class="header">
        <div class="container-fluid custom-container">
            <div class="row">
                <div class=" col-6 col-sm-6 col-md-6 col-lg-2 col-xl-3">
                    <a href="{{ url('exhibitor-index') }}" class="header-logo">
                        <img src="{{ asset('assets/images/logo1.png') }}" height="110px" style="background: white; border-radius: 0 0 15px 15px;">
                    </a>
                </div>
                <div class="col-lg-6 col-xl-5 d-lg-block d-xl-block d-none">
                    <ul class="header-menu">
                        <li><a href="{{ url('exhibitor-index') }}">@lang('web/app.home')</a></li>
                        @if(!\Session::has('gpro_user'))
                        <li><a href="{{url('exhibitor-register')}}" >@lang('web/app.register')</a></li>
                        @else
                        <li><a href="{{ url('profile') }}">@lang('web/app.myprofile')</a></li>
                        @endif
                        <li><a href="{{ url('/exhibitor-index#contact-us') }}">@lang('web/app.contactus')</a></li>
                        <!-- <li><a href="{{ route('pricing') }}">@lang('web/app.pricing')</a></li>
                        <li><a href="{{ route('help') }}">@lang('web/app.help')</a></li> -->
                    </ul>
                </div>
                <div class=" col-6 col-sm-6 col-md-6 col-lg-4 col-xl-4">
                    <div class="header-icons">
                        
                        <ul class="toggle-login">
                            @if(!\Session::has('gpro_user'))
                                <li class="d-xs-none d-none d-sm-block d-lg-block d-xl-block"><a href="javascript:void(0);" onclick="openLoginModal()" class="main-btn">@lang('web/app.login')</a></li>
                            @else
                                <li class="d-xs-none d-none d-sm-block d-lg-block d-xl-block"><a href="{{url('logout')}}"  class="main-btn">@lang('web/app.logout')</a></li>
                            @endif
                            <li class="d-xl-none d-lg-none d-block">
                            <li class="d-xl-none d-lg-none d-block">
                                <div class="position-relative">
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false"
                                        aria-label="Toggle navigation">
                                        <!--just add these span here-->
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                        <!--/end span-->
                                    </button>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDarkDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-language" style="font-size: 30px;"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                    <li>
                                        <a class="dropdown-item language @if(App::getLocale() == 'en') active @endif" href="javascript:void(0);" data-lang="en">
                                            <img class="img-fluid me-3" src="{{ asset('admin-assets/images/flag/english.png') }}" alt="">English @if(App::getLocale() == 'en')
                                            <i class="fas fa-check"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item language @if(App::getLocale() == 'sp') active @endif" href="javascript:void(0);" data-lang="sp">
                                            <img class="img-fluid me-3" src="{{ asset('admin-assets/images/flag/spanish.png') }}" alt="">
                                                Spanish @if(App::getLocale() == 'sp') <i class="fas fa-check"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item language @if(App::getLocale() == 'fr') active @endif" href="javascript:void(0);" data-lang="fr">
                                            <img style="max-width: 36%;" class="img-fluid me-3" src="{{ asset('admin-assets/images/flag/france.png') }}" alt="">
                                                French  @if(App::getLocale() == 'fr') <i class="fas fa-check"></i>@endif
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item language @if(App::getLocale() == 'pt') active @endif" href="javascript:void(0);" data-lang="pt">
                                            <img style="max-width: 36%;" class="img-fluid" src="{{ asset('images/Portuguese.jpg') }}" alt="">
                                            &nbsp;&nbsp;Portuguese  @if(App::getLocale() == 'pt') <i class="fas fa-check"></i>@endif
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar-css -->
    <div id="sidebar">
        <div class="respnsv-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
        </div>
        <div id="cssmenu">
            <ul>
                <li><a href="{{ url('exhibitor-index') }}">@lang('web/app.home')</a></li>
                @if(!\Session::has('gpro_user'))
                <li><a href="{{url('exhibitor-register')}}" >@lang('web/app.register')</a></li>
                @else
                <li><a href="{{ url('profile') }}">@lang('web/app.myprofile')</a></li>
                @endif
                <li><a href="{{ url('/exhibitor-index#contact-us') }}">@lang('web/app.contactus')</a></li>
                <!-- <li><a href="{{ route('pricing') }}">@lang('web/app.pricing')</a></li>
                <li><a href="{{ route('help') }}">@lang('web/app.help')</a></li> -->
                @if(!\Session::has('gpro_user'))
                    <li><a href="javascript:void(0);" onclick="openLoginModal()">@lang('web/app.login')</a></li>
                @else
                    <li><a href="{{url('logout')}}">@lang('web/app.logout')</a></li>
                @endif
            </ul>
        </div>
    </div>
    <!--header section end-->

    @yield('content')

    <!-- Login Registration Modals -->
    <div class="login-modal">
        <div class="modal fade" id="loginModal" aria-hidden="true" aria-labelledby="loginModalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/app.login-heading')</h2>
                        <h5 style="text-align:center;padding-top: 50px;">@lang('web/app.login-description')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding-top:0px">
                        <form id="login" action="{{ route('login') }}" class="row" enctype="multipart/form-data">
                            @csrf
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.email') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="email" name="email" placeholder="@lang('web/app.enter_email')" required>
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.passowrd') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="password" name="password" placeholder="@lang('web/app.enter') @lang('web/app.passowrd')">
                                        <i class="toggle-password fas fa-eye-slash eye-wrap"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <button type="submit" class="login-btn" form="login">@lang('web/app.login')</button>
                                    <!-- //Vineet - 080123 -->
                                    <!-- <a href="javascript:void(0);" class="forget" onclick="openForgotPasswordModal()">@lang('web/app.forgot') @lang('web/app.passowrd')?</a> -->
                                    <a href="javascript:void(0);" class="forget" onclick="openForgotPasswordModal()">@lang('web/app.forgot-password')?</a>
                                    <!-- //Vineet - 080123 -->
                                </div>
                                <div class="signup">
                                    <p>@lang('web/app.dont-have-account') <a href="javascript:void(0);" onclick="openRegistrationModal()">@lang('web/app.click-here-to-sign-up')</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="modal fade" id="forgotPasswordModal" aria-hidden="true" aria-labelledby="forgotPasswordModalLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/app.forgot-password-heading')</h2>
                        <h5 style="text-align:center;padding-top: 50px;">@lang('web/app.forgot-password-description')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding-top:0px">
                        <form id="forgot-password-form" action="{{ route('forgot.password') }}" class="row" enctype="multipart/form-data">
                            @csrf
                            <div class="col-lg-12">
                                <div class="form-check">
                                    <label class="form-check-label">@lang('web/app.email') <b>*</b> </label>
                                    <div class="input-box">
                                        <input type="email" name="email" placeholder="@lang('web/app.enter_email')" required>
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div>
                                    <button type="submit" class="login-btn" form="forgot-password-form">@lang('web/app.request-passowrd-reset')</button>
                                </div>
                                <div class="signup">
                                    <p>@lang('web/app.back-to-login') <a href="javascript:void(0);" onclick="openLoginModal()">@lang('web/app.click-here-to-sign-in')</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="registrationCompletedModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0">
                        <h2 class="main-head">@lang('web/app.welcome')</h2>
                        <!-- <h5>Log in to submit your application</h5> -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="thank-popup">
                            <img src="{{ asset('assets/images/right.png') }}" alt="img">
                            <h4>@lang('web/app.thank-you')</h4>
                            <p>@lang('web/app.successfully-signed-up')</p>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="main-btn" data-bs-dismiss="modal">@lang('web/app.ok')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="verificationModal" aria-hidden="true" aria-labelledby="verificationModalLabel"
            tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" id="verificationHtml"> </div>
            </div>
        </div>
    </div>

    <!-- footer-start -->
    <footer class="footer-inner" id="contact-us">
        <div class="container">
            <div class="requirement">
                <div class="requirement-wrapper">
                    <div class="requirement-left">
                        <div>
                            <span>
                                <svg width="60" height="48" viewBox="0 0 60 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23.6314 34.014C23.6549 33.937 23.7022 33.8737 23.7433 33.8043C23.7818 33.7393 23.8101 33.6716 23.8619 33.6172C23.8698 33.6088 23.8721 33.5964 23.8805 33.588L48.0889 9.66602L18.7815 32.8191L20.8033 44.6454L23.6129 34.0491C23.616 34.0358 23.6275 34.027 23.6315 34.0137L23.6314 34.014Z" fill="#111111"></path>
                                    <path d="M0.106445 27.6213L17.4257 31.3059L54.2768 2.19434L0.106445 27.6213Z" fill="#111111"></path>
                                    <path d="M22.5654 45.9063L29.6771 38.5496L25.2252 35.873L22.5654 45.9063Z" fill="#111111"></path>
                                    <path d="M26.2319 34.1125L31.8136 37.4684C31.8145 37.4689 31.8149 37.4698 31.8154 37.4702L47.9185 47.1513L59.8942 0.849609L26.2319 34.1125ZM48.8461 35.5903L47.3763 40.9813C47.2529 41.4325 46.845 41.7284 46.3992 41.7284C46.3112 41.7284 46.2214 41.7173 46.132 41.6926C45.5924 41.5453 45.2739 40.9879 45.4212 40.4488L46.891 35.0578C47.0383 34.5177 47.5947 34.1979 48.1352 34.3466C48.6748 34.4939 48.9929 35.0507 48.846 35.5903L48.8461 35.5903ZM49.9722 31.4608C49.8488 31.9119 49.441 32.2078 48.9951 32.2078C48.9071 32.2078 48.8169 32.1968 48.728 32.172C48.1883 32.0247 47.8698 31.4674 48.0171 30.9282L50.741 20.9432C50.8887 20.404 51.4456 20.0842 51.9852 20.2319C52.5249 20.3792 52.8433 20.9366 52.696 21.4757L49.9722 31.4608Z" fill="#111111"></path>
                                </svg>
                            </span>
                        </div>
                        <div>
                            <h4>@lang('web/app.footer-heading')</h4>
                            <p>@lang('web/app.footer-description')</p>
                        </div>
                    </div>
                    <div class="requirement-right">
                        @if(!\Session::has('gpro_user'))
                            <a href="{{url('exhibitor-register')}}" class="main-btn">Register your space</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="footer-logo">
                <a href="{{ url('exhibitor-index') }}">
                    <img src="{{ asset('assets/images/logo1.png') }}" alt="logo" style="width: 24%;">
                </a>
            </div>
            <div class="footer-menu" style="margin-top: 96px;">
                <ul>
                    <li>
                        <a href="{{ url('exhibitor-index') }}">@lang('web/app.home')</a>
                    </li>
                    @if(!\Session::has('gpro_user'))
                    <li>
                        <a href="{{url('exhibitor-register')}}" >@lang('web/app.register')</a>
                    </li>
                    @endif
                   
                    @if(count($informations) > 0)
                        @foreach ($informations as $information)
                        <li>
                            <a target="_blank" href="{{ route('information', [$information->slug]) }}">@if($information->title == 'Privacy Policy') @lang('web/app.Privacy_Policy') @else @lang('web/app.Terms_and_Conditions') @endif </a>
                        </li>
                        @endforeach
                    @endif
                   
                </ul>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer-box">
                        <h4>@lang('web/app.contactus')</h4>
                        <!-- //Vineet - 080123 -->
                        <p>info@gprocongress.org</p> 
                        <!-- //Vineet - 080123 -->
                        <p>P.O. Box 793772, Dallas, TX 75379</p>
                        <p>(1)972-528-6100</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-box">
                        <h4>@lang('web/app.social-handles')</h4>
                        <ul class="footer-social">
                            <li>
                                <a target="_blank" href="https://www.facebook.com/GProCommission/"><i class="fab fa-facebook-f"></i></a>
                            </li>
                            <li>
                                <a target="_blank" href="https://twitter.com/gprocommission"><i class="fab fa-twitter"></i></a>
                            </li>
                            <li>
                                <a target="_blank" href="https://www.instagram.com/gprocommission/"><i class="fab fa-instagram"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="bottom-footer">
                <p>@lang('web/app.copyright') © 2022 <a href="javascript:;">@lang('web/app.gprocongress')</a>. @lang('web/app.all-rights-reserved').</p>
            </div>
        </div>
    </footer>
    <!-- footer-end -->

    <!-- js-files -->
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script> -->
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.js') }}"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/fSelect.js') }}"></script>


    <script>

    $(document).ready(function() {
        @if(Session::has('gpro_error'))
        showMsg('error', "{{ Session::get('gpro_error') }}");
        @elseif(Session::has('gpro_success'))
        showMsg('success', "{{ Session::get('gpro_success') }}");
        @endif
        
        @if(Session::has('registration_completed'))
            $('#registrationCompletedModal').modal('show');
            @php \Session::forget('registration_completed'); @endphp
        @endif

        
        @if(Session::has('reset_password'))
            $('#loginModal').modal('show');
            @php \Session::forget('reset_password'); @endphp
        @endif

        // addToCart();

        // getTotalCartProduct();
    });
    </script>

    <script>

    
    $('.cover').on('click', function() {
        $(this).children().css({
            'z-index': 1,
            'opacity': 1
        });
        $(this).children().trigger('play');
    });
    $('video').on('click', function() {
        console.log('a');
    });
    </script>

    <script>
    // -------- password
    $(".toggle-password").click(function() {
        $(this).toggleClass("fas fa-eye");
        $(this).toggleClass("fas fa-eye-slash");
        input = $(this).parent().find("input");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
    </script>

    <script>
    $(document).ready(function() {
        $(".click-me").click(function() {
            $(this).parent().toggleClass("show-icon");
        });

        $('#country').change(function() {
            if ($('.phoneCode').length > 0) {
                $('.phoneCode').fSelect('destroy');
                $('.phoneCode').val($(this).find(':selected').attr('data-phoneCode')).change();
                $('.phoneCode').fSelect('create');
            }
        });
    });
    </script>

    <script>
    var video = document.getElementById("myVideo");
    var btn = document.getElementById("myBtn");
    var para = document.getElementById("para");

    var mobVideo = document.getElementById("mobVideo");
    var btn3 = document.getElementById("myBtn3");

    function myFunction() {
        if (video.paused) {
            video.play();
            btn.src = "{{ asset('assets/images/pause.webp') }}";
        } else {
            video.pause();
            btn.src = "{{ asset('assets/images/play-icon.png') }}";
            para.style.display = "block";
            // para.innerHTML= "lorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsum"
        }
    }

    function myFunction3() {
        if (mobVideo.paused) {
            mobVideo.play();
            btn3.src = "{{ asset('assets/images/pause.webp') }}";
        } else {
            mobVideo.pause();
            btn3.src = "{{ asset('assets/images/play-icon.png') }}";
            para.style.display = "block";
            // para.innerHTML= "lorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsum"
        }
    }


    $(".click-btn, .overlay-video").click(function() {
        $(".main-img").toggleClass("disp-img");
    });
    $(".click-btn, .overlay-video").click(function() {
        $("#para").toggleClass("display-para");
    });
    </script>

    <script>
    var video1 = document.getElementById("myVideo1");
    var btn1 = document.getElementById("myBtn1");
    var para1 = document.getElementById("para1");

    function myFunction2() {
        if (video1.paused) {
            video1.play();
            btn1.src = "{{ asset('assets/images/pause.webp') }}";
        } else {
            video1.pause();
            btn1.src = "{{ asset('assets/images/play-icon.png') }}";
            para1.style.display = "block";
            // para.innerHTML= "lorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsumlorem ipsum"
        }
    }

    // $(".click-btn, .overlay-video").click(function(){
    // $(".main-img").toggleClass("disp-img");
    // });
    // $(".click-btn, .overlay-video").click(function(){
    // $("#para").toggleClass("display-para");
    // });
    </script>

    <script>
        
        $("form#registration").submit(function(e) {
            e.preventDefault();
        
            var formId = $(this).attr('id');
            var formAction = $(this).attr('action');
            var btnhtml = $("button[form="+formId+"]").html();
        
            $.ajax({
                url: formAction,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: new FormData(this),
                dataType: 'json',
                type: 'post',
                beforeSend: function() {
                    submitButton(formId, btnhtml, true);
                },
                error: function(xhr, textStatus) {
        
                    if (xhr && xhr.responseJSON.message) {
                        showMsg('error', xhr.responseJSON.message);
                    } else {
                        showMsg('error', xhr.statusTex);
                    }
        
                    submitButton(formId, btnhtml, false);
        
                },
                success: function(data) {
        
                    $('#registrationModal').modal('hide');
                    // $('#verificationHtml').html(data.html);
                    // $('#verificationModal').modal('show');
                    // $('#timer_left').css('display', 'inline-block');
                    // $('#sendotp').css('display', 'none');
                    // var resendOtpTime = 30;
                    // interval = setInterval(() => {
                    //     if (resendOtpTime > 0) {
                    //         resendOtpTime--;
                    //         $('#timer_left').html("00:" + ("0" + resendOtpTime).slice(-2));
                    //     } else {
        
                    //         $('#timer_left').css('display', 'none');
                    //         $('#sendotp').css('display', 'inline-block');
                    //         clearInterval(interval);
                    //     }
                    // }, 1000);
        
                    // setTimeout(() => {
        
                    //     $('.firstotp').focus();
        
                    //     initilizeVerify();
        
                    // }, 500);
        
                    showMsg('success', data.message);
                    submitButton(formId, btnhtml, false);
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        
        });
    
        $("form#login").submit(function(e) {
            e.preventDefault();

            var formId = $(this).attr('id');
            var formAction = $(this).attr('action');
            var btnhtml = $("button[form="+formId+"]").html();

            $.ajax({
                url: formAction,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: new FormData(this),
                dataType: 'json',
                type: 'post',
                beforeSend: function() {
                    submitButton(formId, btnhtml, true);
                },
                error: function(xhr, textStatus) {

                    if (xhr && xhr.responseJSON.message) {
                        showMsg('error', xhr.responseJSON.message);
                    } else {
                        showMsg('error', xhr.statusTex);
                    }

                    submitButton(formId, btnhtml, false);

                },
                success: function(data) {

                    if(data.otp_verified=='Yes'){

                            if (data.token) {
                           
                            location.href = "{{ url('profile') }}";
                        }
                        submitButton(formId, btnhtml, false);

                    }else{

                        $('#loginModal').modal('hide');
                        // $('#verificationHtml').html(data.html);
                        // $('#verificationModal').modal('show');
                        // $('#timer_left').css('display', 'inline-block');
                        // $('#sendotp').css('display', 'none');
                        // var resendOtpTime = 30;
                        // interval = setInterval(() => {
                        //     if (resendOtpTime > 0) {
                        //         resendOtpTime--;
                        //         $('#timer_left').html("00:" + ("0" + resendOtpTime).slice(-2));
                        //     } else {
            
                        //         $('#timer_left').css('display', 'none');
                        //         $('#sendotp').css('display', 'inline-block');
                        //         clearInterval(interval);
                        //     }
                        // }, 1000);
            
                        // setTimeout(() => {

                        //     $('.firstotp').focus();
            
                        //     initilizeVerify();
            
                        // }, 500);
            
                        showMsg('success', data.message);
                        submitButton(formId, btnhtml, false);

                    }
                    
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        });

        $("form#forgot-password-form").submit(function(e) {

            e.preventDefault();

            var formId = $(this).attr('id');
            var formAction = $(this).attr('action');
            var btnhtml = $("button[form=" + formId + "]").html();

            $.ajax({
                url: formAction,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: new FormData(this),
                dataType: 'json',
                type: 'post',
                beforeSend: function() {
                    submitButton(formId, btnhtml, true);
                },
                error: function(xhr, textStatus) {

                    if (xhr && xhr.responseJSON.message) {
                        showMsg('error', xhr.responseJSON.message);
                    } else {
                        showMsg('error', xhr.statusTex);
                    }

                    submitButton(formId, btnhtml, false);

                },
                success: function(data) {
                    if (data.reset) {
                        showMsg('success', data.message);
                        $('form#forgot-password-form')[0].reset();
                        $('#forgotPasswordModal').modal('hide');
                    }
                    submitButton(formId, btnhtml, false);
                },
                cache: false,
                contentType: false,
                processData: false,
            });

        });
    
        function sendOtp() {
        
            $("#sendotp").click(function(e) {
        
                var email = $(this).data('email');
        
                var btnhtml = $(this).html();
        
                e.preventDefault();
        
                if (email.length == '') {
        
                    showMsg('error', 'Email field is required.');
        
                } else {
        
                    $.ajax({
                        url: "{{ url('send-otp') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'email': email
                        },
                        dataType: 'json',
                        type: 'POST',
                        beforeSend: function() {
                            submitButton('sendotp', btnhtml, true);
                        },
                        error: function(xhr, textStatus) {
        
                            if (xhr && xhr.responseJSON.message) {
                                showMsg('error', xhr.responseJSON.message);
                            } else {
                                showMsg('error', xhr.statusTex);
                            }
        
                            submitButton('sendotp', btnhtml, false);
        
                        },
                        success: function(data, typeValue) {
        
                            showMsg('success', data.message);
                            submitButton('sendotp', btnhtml, false);
        
                            // $('#timer_left').css('display', 'inline-block');
                            // $('#sendotp').css('display', 'none');
                            // var resendOtpTime = 30;
                            // interval = setInterval(() => {
                            //     if (resendOtpTime > 0) {
                            //         resendOtpTime--;
                            //         $('#timer_left').html("00:" + ("0" + resendOtpTime).slice(-2));
                            //     } else {
        
                            //         $('#timer_left').css('display', 'none');
                            //         $('#sendotp').css('display', 'inline-block');
                            //         clearInterval(interval);
                            //     }
                            // }, 1000);
                            // setTimeout(() => {
        
                            //     $('.firstotp').focus();
        
                            // }, 100);
                        },
                        cache: false,
                    });
                }
        
            });
        
        }
    
        function initilizeVerify() {
        
            autoOtpFocus();
        
            sendOtp();
        
            $("form#verification").submit(function(e) {
        
                e.preventDefault();
        
                var formId = $(this).attr('id');
                var formAction = $(this).attr('action');
                var btnhtml = $("button[form="+formId+"]").html();
        
                $.ajax({
                    url: formAction,
                    data: new FormData(this),
                    dataType: 'json',
                    type: 'post',
                    beforeSend: function() {
                        submitButton(formId, btnhtml, true);
                    },
                    error: function(xhr, textStatus) {
        
                        if (xhr && xhr.responseJSON.message) {
                            showMsg('error', xhr.responseJSON.message);
                        } else {
                            showMsg('error', xhr.statusTex);
                        }
        
                        submitButton(formId, btnhtml, false);
        
                    },
                    success: function(data) {
        
                        showMsg('success', data.message);
                        submitButton(formId, btnhtml, false);
                        location.href = "{{ url('profile') }}";
        
                    },
                    cache: false,
                    contentType: false,
                    processData: false,
                });
        
            });
        
        }
    
        function autoOtpFocus() {
        
            $(".otp").keyup(function() {
                if (this.value.length == this.maxLength) {
                    $(this).next('.otp').focus();
                }
            });
        }
        
        $(document).ready(function() {
            autoOtpFocus();
            sendOtp();
        });
    
        function openLoginModal() {
            $('.modal').modal('hide');
            $('form#login')[0].reset();
            $('#loginModal').modal('show');

            if ($('#sidebar').hasClass('show')) {
                $(".navbar-toggler").trigger('click');
            }
        }
        
        function openRegistrationModal() {
            $('.modal').modal('hide');
            $('form#registration')[0].reset();
            $('#registrationModal').modal('show');

            if ($('#sidebar').hasClass('show')) {
                $(".navbar-toggler").trigger('click');
            }
        }
        
        function openForgotPasswordModal() {
            $('.modal').modal('hide');
            $('form#forgot-password-form')[0].reset();
            $('#forgotPasswordModal').modal('show');

            if ($('#sidebar').hasClass('show')) {
                $(".navbar-toggler").trigger('click');
            }
        }

        function fSelectRequired($form_id) {
            var required = false;
            $("form#"+$form_id+" .test").each(function(key, val) {
                if($(this).is('select')){
                    if ($(this).val() == '') {
                        required = true;
                        showMsg('error', `The ${$(this).attr('name')} field is required.`);
                        $(this).closest('.fs-wrap').find('.fs-dropdown').removeClass('hidden');
                        $(this).closest('.fs-wrap').find('.fs-dropdown input').focus();
                        return false;
                    }
                }
            });

            if (required) {
                return true;
            } else {
                return false;
            }
        }
        
        @if(\Request::path() == 'registration')
            $(document).ready(function(){
                $('#registrationModal').modal('show');
                
            });
        @endif

        @if(\Request::path() == 'login')
            $(document).ready(function(){
                $('#loginModal').modal('show');
                
            });
        @endif


        window.addEventListener( "pageshow", function ( event ) {
            var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
            if ( historyTraversal ) {
                // Handle page restore.
                //alert('refresh');
                window.location.reload();
            }
        });
   
        $(document).ready(function() {
            $('input').on('input', function() {
                if (this.value.match(/^https?:\/\//i) || this.value.match(/^www\./i)) {
                this.value = '';
                }
            });
        });

</script>
    @stack('custom_js')
</body>

</html>