<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('admin-assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/png" href="{{ asset('Favicon.png')}}" />
    <title>@yield('title') | Gpro</title>

    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/feather-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/style.css') }}">
    <link rel="stylesheet" id="color" href="{{ asset('admin-assets/css/color-1.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/responsive.css') }}">

    <link href="{{ asset('plugins/summernote/summernote-lite.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/sweetalert/sweetalert.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/preloader/preloader.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/fSelect.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1.1/css/all.min.css"
        integrity="sha512-ioRJH7yXnyX+7fXTQEKPULWkMn3CqMcapK0NNtCN8q//sW7ZeVFcbMJ9RvX99TwDg6P8rAH2IqUSt2TLab4Xmw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @stack('custom_css')

    <script>
    var baseUrl = "{{ url('/') }}";
    </script>
    <style>
        .btn {
            font-size: 14px;
            padding: 6px 9px !important;
            font-weight: 600;
        }
        
        

    </style>
</head>

<body>
    <!-- Loader starts-->
    <div class="loader-wrapper">
        <div class="theme-loader">
            <div class="loader-p"></div>
        </div>
    </div>
    <div class="loader-wrapper" id="preloader" style="display: none; background: #ffffffbf;">
        <div class="theme-loader">
            <div class="loader-p"></div>
        </div>
    </div>
    <!-- Loader ends-->

    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <div class="page-main-header">
            <div class="main-header-right row m-0">
                <div class="main-header-left">
                    <div class="logo-wrapper"><a href="{{ url('admin/dashboard') }}"><strong>GProCongress</strong></a></div>
                    <div class="dark-logo-wrapper"><a href="{{url('admin/dashboard') }}"><img class="img-fluid"
                                src="{{ asset('admin-assets/images/logo/logo.png') }}" alt=""></a></div>
                    <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center"
                            id="sidebar-toggle"></i></div>
                </div>
                <div class="left-menu-header col">
                    <ul>
                        <li>
                            <form class="form-inline search-form">
                                <div class="search-bg"><i class="fa fa-search"></i>
                                    <input class="form-control-plaintext" placeholder="Search here.....">
                                </div>
                            </form><span class="d-sm-none mobile-search search-bg"><i class="fa fa-search"></i></span>
                        </li>
                    </ul>
                </div>
                <div class="nav-right col pull-right right-menu p-0">
                    <ul class="nav-menus">
                        <li><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i
                                    data-feather="maximize"></i></a></li>
                        <!-- <li class="onhover-dropdown">
                            <div class="bookmark-box"><i data-feather="star"></i></div>
                            <div class="bookmark-dropdown onhover-show-div">
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="fa fa-search"></i></span></div>
                                        <input class="form-control" type="text" placeholder="Search for bookmark...">
                                    </div>
                                </div>
                                <ul class="m-t-5">
                                    <li class="add-to-bookmark"><i class="bookmark-icon"
                                            data-feather="inbox"></i>Email<span class="pull-right"><i
                                                data-feather="star"></i></span></li>
                                    <li class="add-to-bookmark"><i class="bookmark-icon"
                                            data-feather="message-square"></i>Chat<span class="pull-right"><i
                                                data-feather="star"></i></span></li>
                                    <li class="add-to-bookmark"><i class="bookmark-icon"
                                            data-feather="command"></i>Feather Icon<span class="pull-right"><i
                                                data-feather="star"></i></span></li>
                                    <li class="add-to-bookmark"><i class="bookmark-icon"
                                            data-feather="airplay"></i>Widgets<span class="pull-right"><i
                                                data-feather="star"> </i></span></li>
                                </ul>
                            </div>
                        </li> -->
                        <li class="onhover-dropdown">
                            <div class="notification-box"><i data-feather="bell"></i><span class="dot-animated"></span>
                            </div>
                            <ul class="notification-dropdown onhover-show-div">
                                @php $notifications = \App\Models\Notification::orderBy('id','desc')->limit(5)->get(); @endphp
                                @if(!empty($notifications) && count($notifications)>0)
                                    @foreach($notifications as $notification)
                                        
                                        <li class="noti-primary">
                                            <div class="media"><span class="notification-bg bg-light-primary"><i
                                                        class="fa fa-user"> </i></span>
                                                        
                                                <div class="media-body ms-2">
                                                    <p><a href="{{url('admin/notification/view/'.$notification->id)}}">{{$notification->title}}</a> </p><span>{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div style="font-size: 8px;">{{\App\Helpers\commonHelper::getUserNameById($notification->user_id)}}</div>
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <p class="f-w-700 mb-0">Notifications not found</p>
                                    </li>
                                @endif
                                
                            </ul>
                        </li>
                        <!-- <li>
                            <div class="mode"><i class="fa fa-moon-o"></i></div>
                        </li> -->
                        <!-- <li class="onhover-dropdown"><i class="fas fa-language" style="font-size: 30px;"></i>
                            <ul class="chat-dropdown onhover-show-div">
                                <li>
                                    <div class="media"><img class="img-fluid me-3"
                                            src="{{ asset('admin-assets/images/flag/english.png') }}" alt="">
                                        <div class="media-body language" data-lang="en"><span>English</span></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="media"><img class="img-fluid me-3"
                                            src="{{ asset('admin-assets/images/flag/INR.png') }}" alt="">
                                        <div class="media-body language" data-lang="hi"><span>Hindi</span></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="media"><img class="img-fluid me-3"
                                            src="{{ asset('admin-assets/images/flag/spanish.png') }}" alt="">
                                        <div class="media-body language" data-lang="sp"><span>Spanish</span></div>
                                    </div>
                                </li>
                            </ul>
                        </li> -->
                        <li class="onhover-dropdown p-0">
                            <a href="#" class="btn btn-primary-light" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i data-feather="log-out"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
                <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
            </div>
        </div>
        <!-- Page Header Ends-->
        <!-- Page Body Start-->
        <div class="page-body-wrapper sidebar-icon">
            <!-- Page Sidebar Start-->
            <header class="main-nav close_icon">
                <div class="sidebar-user text-center"><a class="setting-primary" title="Change Password"
                        href="{{url('admin/change-password')}}"><i data-feather="settings"></i></a><img
                        class="img-90 rounded-circle" src="{{asset('admin-assets/images/dashboard/1.png')}}" alt="">
                    <div class="badge-bottom"><span class="badge badge-primary">New</span></div><a
                        href="user-profile.html">
                        <h6 class="mt-3 f-14 f-w-600">{{ ucfirst(\Auth::user()->name)}}</h6>
                    </a>
                    <p class="mb-0 font-roboto">{{\Auth::user()->email}}</p>
                    {{-- <ul>
                        <li><span><span class="counter">19.8</span>k</span>
                            <p>Follow</p>
                        </li>
                        <li><span>2 year</span>
                            <p>Experince</p>
                        </li>
                        <li><span><span class="counter">95.2</span>k</span>
                            <p>Follower </p>
                        </li>
                    </ul> --}}
                </div>
                <nav>





                </nav>

                <aside id="leftsidebar" class="sidebar">
                    <!-- Menu -->
                    <div class="menu">
                        @php echo \App\Helpers\commonHelper::getSidebarMenu(); @endphp
                    </div>
                    <!-- #Menu -->
                </aside>
            </header>
            <!-- Page Sidebar Ends-->
            <div class="page-body dashboard-2-main">
                <!-- Container-fluid starts-->
                @yield('content')
                <!-- Container-fluid Ends-->
            </div>
            <!-- footer start-->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 footer-copyright">
                            <p class="mb-0">Copyright {{date('Y')}}-{{date('y', strtotime('+1 year'))}} © GProCongress All
                                rights reserved.</p>
                        </div>
                        <div class="col-md-6">
                            <p class="pull-right mb-0">Hand crafted & made with <i
                                    class="fa fa-heart font-secondary"></i></p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>



    <div id="authorizeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Authorise Account</h4>
                </div>
                <div class="modal-body">
                    <p>First you need to authorise your account</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a
                        href="https://cloud.digitalocean.com/v1/oauth/authorize?client_id=ea38d5d90808e2cebeec74a660cc773d607d5d8daaeac2cf4e1e740c6f82ccd9&redirect_uri=https://206.189.140.154/digitalocean/admin/dashboard&response_type=code">
                        <button type="button" class="btn btn-warning">Authorise</button>
                    </a>
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('admin-assets/js/app.min.js')}}"></script>
    <script src="{{ asset('admin-assets/js/index.js')}}"></script>
    <script src="{{ asset('admin-assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/icons/feather-icon/feather-icon.js') }}"></script> 
    <script src="{{ asset('admin-assets/js/config.js') }}"></script>
    <script src="{{ asset('admin-assets/js/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/script.js') }}"></script>
    <script src="{{ asset('admin-assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>

    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/common.js') }}"></script>

    <script src="{{ asset('assets/js/fSelect.js') }}"></script>
    <script>
    $(document).ready(function() {
        @if(Session::has('5fernsadminerror'))
        sweetAlertMsg('error', "{{ Session::get('5fernsadminerror') }}");
        @elseif(Session::has('5fernsadminsuccess'))
        sweetAlertMsg('success', "{{ Session::get('5fernsadminsuccess') }}");
        @endif

        if ($(window).width() > 767)
        {
            $('.main-nav').removeClass('close_icon');
        }
    });
    var baseUrl = "{{ url('/') }}";


    $('#sidebar-toggle').click(function() {

        $('.main-nav').toggleClass('close_icon');

    });

    
    </script>

    @stack('custom_js')


   

</body>

</html>