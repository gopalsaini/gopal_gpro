
@extends('layouts/app')

@section('title',__(Lang::get('web/app.myprofile')))

@section('content')

    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =\Session::get('gpro_result')['id']; @endphp
                @include('sidebar', compact('groupInfoResult','userId'))
            </div>
            <div class="step-form">
                <div class="application-wrap">
                    <div class="application-content">
                        @if($resultData['result']['profile_submit_type'] == 'preview')
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3333 10.667C18.0697 10.667 18.6667 11.264 18.6667 12.0003V17.3337C18.6667 18.0701 18.0697 18.667 17.3333 18.667H13.3333C12.597 18.667 12 18.0701 12 17.3337C12 16.5973 12.597 16.0003 13.3333 16.0003H16V12.0003C16 11.264 16.5969 10.667 17.3333 10.667Z" fill="#ffff"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.0003 5.33366C10.1093 5.33366 5.33366 10.1093 5.33366 16.0003C5.33366 21.8914 10.1093 26.667 16.0003 26.667C21.8914 26.667 26.667 21.8914 26.667 16.0003C26.667 10.1093 21.8914 5.33366 16.0003 5.33366ZM2.66699 16.0003C2.66699 8.63652 8.63652 2.66699 16.0003 2.66699C23.3641 2.66699 29.3337 8.63652 29.3337 16.0003C29.3337 23.3641 23.3641 29.3337 16.0003 29.3337C8.63652 29.3337 2.66699 23.3641 2.66699 16.0003Z" fill="#ffff"/>
                            </svg>

                        @else 

                            @if($resultData['result']['profile_status']=='Pending')
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_631_9)">
                                    <path d="M15 0.0703125C6.72656 0.0703125 0 6.75 0 15C0 23.25 6.72656 29.9297 15 29.9297C23.2734 29.9297 30 23.25 30 15C30 6.75 23.2734 0.0703125 15 0.0703125ZM15 28.4297C7.54688 28.4297 1.5 22.4297 1.5 15C1.5 7.57031 7.54688 1.57031 15 1.57031C22.4531 1.57031 28.5 7.59375 28.5 15C28.5 22.4297 22.4531 28.4297 15 28.4297ZM20.1328 11.2734L18.9844 10.1484C18.7734 9.9375 18.4453 9.9375 18.2344 10.1484L15 13.3594L11.7656 10.1484C11.5547 9.9375 11.2266 9.9375 11.0156 10.1484L9.86719 11.2734C9.65625 11.4844 9.65625 11.8125 9.86719 12.0234L13.1016 15.2344L9.86719 18.4687C9.65625 18.6797 9.65625 19.0078 9.86719 19.2187L11.0156 20.3437C11.2266 20.5547 11.5547 20.5547 11.7656 20.3437L15 17.1328L18.2344 20.3437C18.4453 20.5547 18.7734 20.5547 18.9844 20.3437L20.1328 19.2187C20.3438 19.0078 20.3438 18.6797 20.1328 18.4687L16.8984 15.2344L20.1328 12.0234C20.3438 11.8125 20.3438 11.4844 20.1328 11.2734Z" fill="#FB4949"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_631_9">
                                    <rect width="30" height="30" fill="white"/>
                                    </clipPath>
                                    </defs>
                                </svg>
                            @elseif($resultData['result']['profile_status']=='Approved')
                                <svg width="36" height="35" viewBox="0 0 36 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M34.9682 16.7567L33.1803 14.0073L33.6123 10.7495C33.6723 10.2167 33.4203 9.67169 32.9644 9.39293L30.1685 7.69738L29.1606 4.57286C28.9927 4.0522 28.5247 3.68889 27.9965 3.62834L24.7565 3.32556L22.5006 0.963903C22.1165 0.564349 21.5527 0.431071 21.0485 0.624904L18.0003 1.78764L14.9524 0.637084C14.4483 0.443252 13.8724 0.576529 13.5003 0.976083L11.2445 3.33774L8.00446 3.64052C7.46446 3.70107 7.00832 4.06441 6.84032 4.58504L5.82032 7.70984L3.03619 9.39321C2.56824 9.67165 2.31619 10.2048 2.38824 10.7498L2.8203 14.0076L1.03237 16.7569C0.74442 17.2171 0.74442 17.7986 1.03237 18.2586L2.8203 21.008L2.38824 24.2536C2.31619 24.7986 2.56824 25.3314 3.03619 25.6102L5.82032 27.3058L6.82826 30.4303C6.9962 30.9509 7.45237 31.3142 7.99239 31.363L11.2324 31.6779L13.4883 34.0396C13.8724 34.4272 14.4362 34.5724 14.9403 34.3786L18.0003 33.2277L21.0483 34.3783C21.2041 34.4388 21.3603 34.4632 21.5162 34.4632C21.8762 34.4632 22.2362 34.3177 22.5003 34.0393L24.7562 31.6776L27.9962 31.3749C28.5241 31.3143 28.9921 30.951 29.1603 30.4303L30.1683 27.3058L32.9641 25.6103C33.42 25.3318 33.6841 24.7987 33.6121 24.2537L33.18 20.9959L34.968 18.2465C35.2682 17.7983 35.2682 17.2171 34.9682 16.7567L34.9682 16.7567ZM25.5959 14.0439L16.8002 22.9216C16.5482 23.176 16.2002 23.3093 15.8402 23.3093C15.4923 23.3093 15.1323 23.176 14.8802 22.9216L10.4044 18.4043C9.87641 17.8715 9.87641 17.0116 10.4044 16.4666C10.9323 15.9337 11.7964 15.9337 12.3244 16.4666L15.8402 20.015L23.6642 12.1185C24.1922 11.5857 25.0563 11.5857 25.5963 12.1185C26.1242 12.6392 26.1242 13.5113 25.596 14.0441L25.5959 14.0439Z" fill="#111111"/>
                                </svg>
                            @elseif($resultData['result']['profile_status']=='Rejected')
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_631_9)">
                                    <path d="M15 0.0703125C6.72656 0.0703125 0 6.75 0 15C0 23.25 6.72656 29.9297 15 29.9297C23.2734 29.9297 30 23.25 30 15C30 6.75 23.2734 0.0703125 15 0.0703125ZM15 28.4297C7.54688 28.4297 1.5 22.4297 1.5 15C1.5 7.57031 7.54688 1.57031 15 1.57031C22.4531 1.57031 28.5 7.59375 28.5 15C28.5 22.4297 22.4531 28.4297 15 28.4297ZM20.1328 11.2734L18.9844 10.1484C18.7734 9.9375 18.4453 9.9375 18.2344 10.1484L15 13.3594L11.7656 10.1484C11.5547 9.9375 11.2266 9.9375 11.0156 10.1484L9.86719 11.2734C9.65625 11.4844 9.65625 11.8125 9.86719 12.0234L13.1016 15.2344L9.86719 18.4687C9.65625 18.6797 9.65625 19.0078 9.86719 19.2187L11.0156 20.3437C11.2266 20.5547 11.5547 20.5547 11.7656 20.3437L15 17.1328L18.2344 20.3437C18.4453 20.5547 18.7734 20.5547 18.9844 20.3437L20.1328 19.2187C20.3438 19.0078 20.3438 18.6797 20.1328 18.4687L16.8984 15.2344L20.1328 12.0234C20.3438 11.8125 20.3438 11.4844 20.1328 11.2734Z" fill="#FB4949"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_631_9">
                                    <rect width="30" height="30" fill="white"/>
                                    </clipPath>
                                    </defs>
                                </svg>  
                            @elseif($resultData['result']['profile_status']=='Waiting')
                                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_631_111)">
                                    <path d="M15 0.0703125C6.72656 0.0703125 0 6.75 0 15C0 23.25 6.72656 29.9297 15 29.9297C23.2734 29.9297 30 23.25 30 15C30 6.75 23.2734 0.0703125 15 0.0703125ZM15 28.4297C7.54688 28.4297 1.5 22.4297 1.5 15C1.5 7.57031 7.54688 1.57031 15 1.57031C22.4531 1.57031 28.5 7.59375 28.5 15C28.5 22.4297 22.4531 28.4297 15 28.4297Z" fill="#9F1717"/>
                                    <path d="M16.6155 21.6219C16.4835 21.655 16.349 21.6844 16.2155 21.7092C15.8665 21.7746 15.6357 22.1127 15.7003 22.4646C15.7322 22.6377 15.8298 22.7819 15.9623 22.8761C16.0989 22.9729 16.2727 23.0166 16.4498 22.9834C16.6088 22.9537 16.7691 22.9187 16.9263 22.8792C17.271 22.7928 17.481 22.4411 17.395 22.0941C17.3092 21.7468 16.9603 21.5354 16.6155 21.6219Z" fill="#9F1717"/>
                                    <path d="M21.3732 12.9364C21.4182 13.0732 21.5043 13.1848 21.6129 13.262C21.774 13.3763 21.9848 13.4147 22.186 13.3477C22.5233 13.2349 22.706 12.8686 22.5944 12.529C22.5436 12.3742 22.4872 12.2189 22.4271 12.0678C22.2952 11.7355 21.921 11.5737 21.591 11.7066C21.2613 11.8395 21.1007 12.2165 21.2327 12.5488C21.2832 12.6759 21.3305 12.8063 21.3732 12.9364Z" fill="#9F1717"/>
                                    <path d="M18.7056 20.6963C18.5921 20.7718 18.475 20.8448 18.3571 20.9132C18.0493 21.0921 17.9439 21.4884 18.1213 21.7983C18.1695 21.8826 18.2337 21.9515 18.3076 22.0041C18.506 22.1448 18.7755 22.1658 18.9999 22.0356C19.1401 21.9542 19.2796 21.8675 19.4147 21.7775C19.711 21.5803 19.7925 21.1783 19.5967 20.8797C19.4009 20.5811 19.002 20.499 18.7056 20.6963Z" fill="#9F1717"/>
                                    <path d="M22.9939 14.7465C22.9799 14.3889 22.6809 14.1106 22.3258 14.1245C21.9711 14.1386 21.6946 14.4399 21.7085 14.7974C21.7138 14.9341 21.7152 15.0728 21.7121 15.2093C21.7071 15.4336 21.816 15.6335 21.9852 15.7536C22.086 15.8252 22.2084 15.8684 22.341 15.8715C22.696 15.8794 22.9902 15.5958 22.9981 15.238C23.0017 15.0748 23.0003 14.9096 22.9939 14.7465Z" fill="#9F1717"/>
                                    <path d="M21.2677 18.9902C20.983 18.775 20.5803 18.8334 20.3671 19.1197C20.2854 19.2293 20.1994 19.3376 20.1114 19.4418C19.8813 19.7141 19.9138 20.1232 20.1841 20.3551C20.1995 20.3683 20.2151 20.3804 20.2313 20.3917C20.5 20.5826 20.8735 20.5387 21.0908 20.2819C21.196 20.1575 21.2985 20.0282 21.396 19.8972C21.6093 19.611 21.5516 19.205 21.2677 18.9902Z" fill="#9F1717"/>
                                    <path d="M22.2122 16.649C21.8733 16.5419 21.5123 16.732 21.4061 17.0734C21.3655 17.2039 21.3204 17.3349 21.2718 17.4633C21.165 17.7458 21.2673 18.0568 21.5019 18.2234C21.5449 18.2538 21.5923 18.2796 21.6438 18.2992C21.9756 18.4268 22.3473 18.2591 22.4738 17.9247C22.5315 17.772 22.5851 17.6161 22.6336 17.461C22.7397 17.1195 22.5511 16.756 22.2122 16.649Z" fill="#9F1717"/>
                                    <path d="M13.8099 21.7143C13.2349 21.6103 12.6832 21.4332 12.1601 21.1862C12.1539 21.1829 12.1484 21.1792 12.1419 21.1762C12.0186 21.1178 11.8956 21.0552 11.7763 20.9898C11.7759 20.9893 11.7751 20.989 11.7745 20.9887C11.5556 20.8673 11.342 20.733 11.1345 20.5858C8.10884 18.4389 7.38135 14.2126 9.51287 11.1648C9.97636 10.5023 10.5373 9.94963 11.1626 9.51158C11.1703 9.50617 11.178 9.5008 11.1856 9.49536C13.3891 7.96599 16.3812 7.86292 18.7209 9.43449L18.2184 10.1659C18.0787 10.3695 18.1646 10.5178 18.4092 10.4956L20.5921 10.2988C20.837 10.2766 20.9834 10.0632 20.9176 9.82501L20.3314 7.69759C20.2658 7.45909 20.0978 7.43055 19.9579 7.63409L19.4542 8.36721C17.7371 7.20615 15.6777 6.76342 13.6335 7.12047C13.4276 7.15637 13.2247 7.2003 13.0244 7.25143C13.0229 7.25171 13.0217 7.25188 13.0204 7.25216C13.0127 7.25406 13.0048 7.25659 12.9973 7.25871C11.2346 7.71467 9.69671 8.75025 8.5993 10.2278C8.59005 10.2388 8.58052 10.2497 8.57179 10.2617C8.53529 10.3112 8.49908 10.3618 8.46362 10.4125C8.40563 10.4955 8.34847 10.5806 8.29378 10.6657C8.28694 10.6759 8.28171 10.6864 8.27572 10.6967C7.37013 12.1103 6.93333 13.7445 7.00824 15.4082C7.00841 15.4137 7.0081 15.4192 7.00824 15.4248C7.0155 15.5873 7.02829 15.7521 7.04566 15.9143C7.04659 15.9248 7.0489 15.9347 7.05065 15.9451C7.0686 16.1083 7.09113 16.2717 7.11937 16.4352C7.40635 18.1029 8.18737 19.6037 9.35769 20.7714C9.3604 20.7741 9.36322 20.7771 9.36597 20.7799C9.36694 20.781 9.368 20.7816 9.36893 20.7826C9.68336 21.095 10.0254 21.3839 10.3936 21.6452C11.3573 22.3292 12.4305 22.7814 13.5829 22.9897C13.9326 23.053 14.2667 22.8185 14.3294 22.4664C14.3921 22.1141 14.1595 21.7774 13.8099 21.7143Z" fill="#9F1717"/>
                                    <path d="M14.6046 9.82422C14.317 9.82422 14.084 10.0591 14.084 10.3484V15.5711L18.8259 18.0403C18.9022 18.0801 18.9839 18.0988 19.0643 18.0988C19.2527 18.0988 19.4346 17.9955 19.5271 17.8153C19.659 17.558 19.5591 17.2419 19.3037 17.1091L15.1246 14.9327V10.3484C15.1246 10.0591 14.8919 9.82422 14.6046 9.82422Z" fill="#9F1717"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_631_111">
                                    <rect width="30" height="30" fill="white"/>
                                    </clipPath>
                                    </defs>
                                </svg> 
                            @elseif($resultData['result']['profile_status']=='Review')
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3333 10.667C18.0697 10.667 18.6667 11.264 18.6667 12.0003V17.3337C18.6667 18.0701 18.0697 18.667 17.3333 18.667H13.3333C12.597 18.667 12 18.0701 12 17.3337C12 16.5973 12.597 16.0003 13.3333 16.0003H16V12.0003C16 11.264 16.5969 10.667 17.3333 10.667Z" fill="#ffff"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.0003 5.33366C10.1093 5.33366 5.33366 10.1093 5.33366 16.0003C5.33366 21.8914 10.1093 26.667 16.0003 26.667C21.8914 26.667 26.667 21.8914 26.667 16.0003C26.667 10.1093 21.8914 5.33366 16.0003 5.33366ZM2.66699 16.0003C2.66699 8.63652 8.63652 2.66699 16.0003 2.66699C23.3641 2.66699 29.3337 8.63652 29.3337 16.0003C29.3337 23.3641 23.3641 29.3337 16.0003 29.3337C8.63652 29.3337 2.66699 23.3641 2.66699 16.0003Z" fill="#ffff"/>
                                </svg> 
                            @endif 

                        @endif 
                        <h5>
                        @if($resultData['result']['profile_submit_type'] == 'preview')
                        
                            @lang('web/profile.preview') 
                        
                        @elseif($resultData['result']['profile_status']=='Review')

                            @lang('web/profile.review')
                            
                        @elseif($resultData['result']['profile_status']=='Approved')
                            
                            @lang('web/profile.application_approved')
                            
                        @elseif($resultData['result']['profile_status']=='Rejected')
                            
                            @lang('web/profile.application') @lang('web/home.decline') 

                        @elseif($resultData['result']['profile_status']=='Waiting')
                            
                            @lang('web/profile.application') @lang('web/home.waiting') 
                        @else
                            @lang('web/profile.application') @lang('web/home.pending') 
                        @endif
                    </h5>
                        
                    </div>

                    @if($resultData['result']['profile_status']=='Review')
                        <!-- <div class="edit-btn">
                            <a href="{{url('profile-update')}}" class="main-btn bg-gray-btn">
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.974 5.40783L19.092 7.52483M18.336 3.54283L12.609 9.26983C12.3131 9.56533 12.1113 9.94181 12.029 10.3518L11.5 12.9998L14.148 12.4698C14.558 12.3878 14.934 12.1868 15.23 11.8908L20.957 6.16383C21.1291 5.99173 21.2656 5.78742 21.3588 5.56256C21.4519 5.33771 21.4998 5.09671 21.4998 4.85333C21.4998 4.60994 21.4519 4.36895 21.3588 4.14409C21.2656 3.91923 21.1291 3.71492 20.957 3.54283C20.7849 3.37073 20.5806 3.23421 20.3557 3.14108C20.1309 3.04794 19.8899 3 19.6465 3C19.4031 3 19.1621 3.04794 18.9373 3.14108C18.7124 3.23421 18.5081 3.37073 18.336 3.54283V3.54283Z" stroke="#" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.5 15V18C19.5 18.5304 19.2893 19.0391 18.9142 19.4142C18.5391 19.7893 18.0304 20 17.5 20H6.5C5.96957 20 5.46086 19.7893 5.08579 19.4142C4.71071 19.0391 4.5 18.5304 4.5 18V7C4.5 6.46957 4.71071 5.96086 5.08579 5.58579C5.46086 5.21071 5.96957 5 6.5 5H9.5" stroke="#" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                @lang('web/profile.edit')
                            </a>
                        </div> -->
                    @endif
                </div>

                @if($resultData['result']['profile_status']=='Approved' || $resultData['result']['profile_status']=='Rejected')
                    <p class="@if($resultData['result']['profile_status']=='Approved') text-success @else text-danger @endif">{{$resultData['result']['remark']}}</p>
                @endif
                <!-- //Vineet - 080123 -->
                <!-- <h4 class="inner-head">@lang('web/profile.personal') @lang('web/profile.details')</h4> -->
                <h4 class="inner-head">@lang('web/profile-details.personal-details-combined')</h4>
                <!-- //Vineet - 080123 -->
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/profile.full-name')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                            @lang('web/profile-details.'.(\App\Helpers\commonHelper::ministryPastorTrainerDetail($resultData['result']['salutation'])))  
                                    
                            {{$resultData['result']['name']}} {{$resultData['result']['last_name']}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.gender')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                                @if($resultData['result']['gender']=='1')
                                    @lang('web/profile-details.male') 
                                   
                                @elseif($resultData['result']['gender']=='2')

                                    @lang('web/profile-details.female')
                                      
                                @endif
                            </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.dob')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($resultData['result']['dob']!=''){{ date('d-m-Y',strtotime($resultData['result']['dob'])) }}@endif</span>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.citizenship')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($resultData['result']['citizenship'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.marital-status')</p>
                            <span>:&nbsp; &nbsp; &nbsp; 
                                @if($resultData['result']['marital_status']=='Married')
                                    @lang('web/home.Married')
                                   
                                @elseif($resultData['result']['marital_status']=='Unmarried')

                                    @lang('web/home.Unmarried')
                                      
                                @endif
                            </span>
                        </li>
                    </ul>
                    </div>
                    <div class="detail-wrap">
                        @php $Spouse = \App\Models\User::where('parent_id',$resultData['result']['id'])->where('added_as','Spouse')->first(); @endphp

                        @php $SpouseParent = \App\Models\User::where('id',$resultData['result']['parent_id'])->first(); @endphp
                        <ul>
                            @if($SpouseParent)

                                <!-- //Vineet - 080123 -->
                                <!-- <li colspan="2"><strong>@lang('web/home.coming-along-with-spouse') :</strong> @lang('web/profile-details.yes') -->
                                <li colspan="2"><strong>@lang('web/home.coming-along-with-spouse') : </strong> &nbsp;@lang('web/profile-details.yes')
                                <!-- //Vineet - 080123 -->
                                </li>
                                <li>@lang('web/home.spouse') : {{$SpouseParent->name}} {{$SpouseParent->last_name}}</li>
                            @else
                                <!-- //Vineet - 080123 -->
                                <!-- <li colspan="2"><strong>@lang('web/home.coming-along-with-spouse') :</strong> @if($Spouse) @lang('web/profile-details.yes') @else @lang('web/profile-details.no') @endif -->
                                <li colspan="2"><strong>@lang('web/home.coming-along-with-spouse') : </strong> &nbsp;@if($Spouse) @lang('web/profile-details.yes') @else @lang('web/profile-details.no') @endif
                                <!-- //Vineet - 080123 -->
                                </li>
                                @if($Spouse)<li>@lang('web/home.spouse') : {{$Spouse['name']}} {{$Spouse['last_name']}}</li>@endif

                            @endif
                        
                        <ul>

                        @if(!$Spouse && $resultData['result']['room'] !=null)
                            <ul>
                                <li colspan="2"><strong>@lang('web/home.stay-in-twin-sharing-or-single') :</strong> &nbsp;
                                @if($resultData['result']['room'] == 'Single')
                                    @lang('web/profile-details.single-room')
                                @else
                                    @lang('web/profile-details.twin')
                                @endif
                               
                                </li> 
                            <ul>

                        @endif
                        
                        @if($Spouse && $Spouse->spouse_confirm_status=='Approve')
                            <ul>
                                <td colspan="2"><strong>@lang('web/home.spouse-confirmation-received') :</strong>  @lang('web/app.Approve') 
                                </li> 
                            <ul>
                            
                        @elseif($Spouse && $Spouse->spouse_confirm_status=='Confirm')
                            <ul>
                                <li colspan="2"><strong style="color:red">@lang('web/home.spouse-confirmation-received') : @lang('web/app.confirm') </strong>
                                </li> 
                            <ul>
                        @elseif($Spouse && $Spouse->spouse_confirm_status=='Pending')
                            <ul>
                                <li colspan="2"><strong style="color:red">@lang('web/home.spouse-confirmation-received') : @lang('web/home.pending') </strong>
                                </li> 
                            <ul>
                        @else($Spouse && $Spouse->spouse_confirm_status=='Decline')
                            <ul>
                                <li colspan="2"><strong style="color:red">@lang('web/home.spouse-confirmation-received') : @lang('web/app.Declined') </strong>
                                </li> 
                            <ul>
                        @endif

                        @php $history = \App\Models\SpouseStatusHistory::where([['spouse_id', $resultData['result']['spouse_id']], ['parent_id', $resultData['result']['id']]])->first(); @endphp

                        @if($history && $history->status=='Reject')
                            <ul>
                                <li colspan="2"><strong style="color:red">{{$history->remark}}</strong>
                                </li> 
                            <ul>
                        @endif
                </div>
                <!-- //Vineet - 080123 -->
                <!-- <h4 class="inner-head section-gap">@lang('web/profile.contact') @lang('web/profile.details')</h4> -->
                <h4 class="inner-head section-gap">@lang('web/contact-details.contact-details-combined')</h4>
                <!-- //Vineet - 080123 -->
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/profile.postal-address')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$resultData['result']['contact_address']}} </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.zip-code')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$resultData['result']['contact_zip_code']}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.country')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{\App\Helpers\commonHelper::getCountryNameById($resultData['result']['contact_country_id'])}}</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.state')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($resultData['result']['contact_state_id'] == 0) {{$resultData['result']['contact_state_name']}} @else {{\App\Helpers\commonHelper::getStateNameById($resultData['result']['contact_state_id'])}} @endif</span>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.city')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($resultData['result']['contact_city_id'] == 0) {{$resultData['result']['contact_city_name']}} @else {{\App\Helpers\commonHelper::getCityNameById($resultData['result']['contact_city_id'])}} @endif </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.phone')</p>
                            <span>:&nbsp; &nbsp; &nbsp; +{{$resultData['result']['phone_code']}} {{$resultData['result']['mobile']}}</span>
                        </li>
                        @if($resultData['result']['contact_business_number'])
                            <li>
                                <p>@lang('web/profile.business-or-home')</p>
                                <span>:&nbsp; &nbsp; &nbsp; +{{$resultData['result']['contact_business_codenumber']}} {{$resultData['result']['contact_business_number']}}</span>
                            </li>
                        @endif
                        @if($resultData['result']['contact_business_number']!=$resultData['result']['contact_whatsapp_number'])
                            <li>
                                <p>@lang('web/profile.whatsapp')</p>
                                <span>:&nbsp; &nbsp; &nbsp;  +{{$resultData['result']['contact_whatsapp_codenumber']}} {{$resultData['result']['contact_whatsapp_number']}}</span>
                            </li> 
                        @endif
                    </ul>
                </div> 
                <!-- //Vineet - 080123 -->
                <!-- <h4 class="inner-head section-gap">@lang('web/profile.ministry') @lang('web/profile.details')</h4> -->
                <h4 class="inner-head section-gap">@lang('web/ministry-details.ministry-details-combined')</h4>
                <!-- //Vineet - 080123 -->
                <div class="detail-wrap">
                    <ul>
                        <li>
                            <p>@lang('web/ministry-details.ministry-name')</p>
                            <span>:&nbsp; &nbsp; &nbsp;  @if($resultData['result']['ministry_name'] == '') Independent @else {{ucfirst($resultData['result']['ministry_name'])}} @endif </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.ministry') @lang('web/profile.postal-address')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$resultData['result']['ministry_address']}} </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.zip-code')</p>
                            <span>:&nbsp; &nbsp; &nbsp; {{$resultData['result']['ministry_zip_code']}}</span>
                        </li>
                    </ul>
                    <ul>
                        <li>
                            <p>@lang('web/profile.country')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if((int) $resultData['result']['ministry_country_id']>0){{\App\Helpers\commonHelper::getCountryNameById($resultData['result']['ministry_country_id'])}}@endif</span>
                        </li>
                        <li>
                            <p>@lang('web/profile.state')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($resultData['result']['ministry_state_id'] == 0) {{$resultData['result']['ministry_state_name']}} @else {{\App\Helpers\commonHelper::getStateNameById($resultData['result']['ministry_state_id'])}} @endif  </span>
                        </li>
                        <li>
                            <p>@lang('web/profile.city')</p>
                            <span>:&nbsp; &nbsp; &nbsp; @if($resultData['result']['ministry_city_id'] == 0) {{$resultData['result']['ministry_city_name']}} @else {{\App\Helpers\commonHelper::getCityNameById($resultData['result']['ministry_city_id'])}} @endif</span>
                        </li>
                    </ul>
                </div>
                <div class="preview-form">
                    <label for="" class="d-block">@lang('web/profile.pastor-trainer')</label>
                    <div class="radio-wrap">
                        <div class="form__radio-group">
                        @if($resultData['result']['ministry_pastor_trainer']=='Yes')@lang('web/profile-details.yes') @else @lang('web/profile-details.no') @endif
                        </div> 
                    </div>
                </div>
                @if($resultData['result']['ministry_pastor_trainer']=='Yes')
                    @php
                        $ministryYesDetail=json_decode($resultData['result']['ministry_pastor_trainer_detail'],true);
    
                    @endphp
                    <h4 class="section-gap">@lang('web/profile.involved-in-training-pastoral-leaders')</h4>
                    <div class="detail-wrap minister-gap">
                        <ul>
                            <li>
                                <p>@lang('web/profile.non-formal-pastoral-training')</p>
                                <span>:&nbsp; &nbsp; &nbsp;  
                                    
                                @if(!empty($ministryYesDetail))

                                    @lang('web/ministry-details.'.strtolower(\App\Helpers\commonHelper::ministryPastorTrainerDetail($ministryYesDetail['non_formal_trainor'])))  
                                    
                                @endif </span>
                            </li>
                            <li>
                                <p>@lang('web/profile.formal-theological-education')</p>
                                <span>:&nbsp; &nbsp; &nbsp; 
                                    @if(!empty($ministryYesDetail))

                                        @lang('web/ministry-details.'.strtolower(\App\Helpers\commonHelper::ministryPastorTrainerDetail($ministryYesDetail['formal_theological'])))  
                                        
                                    @endif 
                                </span>
                            </li>
                            <li>
                                <p>@lang('web/profile.informal-personal-mentoring')</p>
                                <span>:&nbsp; &nbsp; &nbsp; 
                                    @if(!empty($ministryYesDetail))
                                        
                                        @lang('web/ministry-details.'.strtolower(\App\Helpers\commonHelper::ministryPastorTrainerDetail($ministryYesDetail['informal_personal'])))  
                                        
                                    @endif
                                </span>
                            </li>
                            <li>@lang('web/home.willing-to-commit-to-trainer-of-pastors') : <strong> &nbsp;@if(!empty($ministryYesDetail) && isset($ministryYesDetail['willing_to_commit']))@if($ministryYesDetail['willing_to_commit'] == 'Yes') @lang('web/profile-details.yes') @else @lang('web/profile-details.no') @endif @endif</strong></li>
                            <li>@lang('web/home.comment') : <strong> &nbsp;@if(!empty($ministryYesDetail) && isset($ministryYesDetail['comment'])){{$ministryYesDetail['comment']}}@endif</strong></li>

                        </ul>
                    </div>
                    <h4 class="section-gap">@lang('web/profile.involved-in-strengthening')</h4>
                    <div class="detail-wrap minister-gap">
                        <ul>
                            <span>@if(!empty($ministryYesDetail)){{$ministryYesDetail['howmany_pastoral']}}@endif </span>
                        </ul>
                    </div>
                    <h4 class="section-gap">@lang('web/profile.future-pastor-trainers')</h4>
                    <div class="detail-wrap minister-gap">
                        <ul>
                            <span>@if(!empty($ministryYesDetail)){{$ministryYesDetail['howmany_futurepastor']}}@endif</span>
                        </ul>
                    </div>
                @else

                        <label for="" class="d-block">@lang('web/profile.Training-to-your-ministry')</label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                            @if(!empty($resultData['result']['doyouseek_postoral'])) @if($resultData['result']['doyouseek_postoral'] == 'Yes') @lang('web/profile-details.yes') @else @lang('web/profile-details.no') @endif @endif 
                            </div> 
                        </div>
                    @if($resultData['result']['doyouseek_postoral'] == 'Yes')

                        <label for="" class="d-block">@lang('web/profile.envision-training')</label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                            @if(!empty($resultData['result']['doyouseek_postoralcomment'])){{$resultData['result']['doyouseek_postoralcomment']}}@endif 
                            </div> 
                        </div>
                    @else

                        <label for="" class="d-block">@lang('web/home.comment')</label>
                        <div class="radio-wrap">
                            <div class="form__radio-group">
                            @if(!empty($resultData['result']['doyouseek_postoralcomment'])){{$resultData['result']['doyouseek_postoralcomment']}}@endif 
                            </div> 
                        </div>

                    @endif

                @endif

                @if($resultData['result']['profile_submit_type']=='preview')
                    <div style="display:flex;align-items: center;">
                        <div class="step-next" style="padding: 81px 0 0;;margin-top: 50px;">
                            <a href="{{url('profile-update')}}" class="main-btn bg-gray-btn m-1" >@lang('web/home.edit')</a>
                        </div>
                        <div class="register-next">
                            <form id="formSubmit" action="{{ route('ministry-details') }}" class="" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" class="active-input mt-2" value="submit">
                            
                                <div class="step-next">
                                    <button type="submit" class="main-btn" form="formSubmit">@lang('web/home.submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- banner-end -->

    <div class="modal fade" id="registrationCompletedModal" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
        tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-block border-0 text-center">
                        <h4 class="main-head">@lang('web/app.thank-you')</h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <div class="thank-popup text-center">
                            <!-- //Vineet - 080123 -->
                            <!-- <img src="http://127.0.0.1:8000/assets/images/right.png" alt="img"><br><br><br> -->
                            <img src="{{ asset('assets/images/right.png') }}" alt="img"><br><br><br>
                            <!-- //Vineet - 080123 -->
                            <h4>@lang('web/home.thanks-for-submission')</h4>
                            <p><br></p>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="main-btn bg-gray-btn m-1" data-bs-dismiss="modal">@lang('web/app.ok')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
@endsection


@push('custom_js')


<script>


    
    $("form#formSubmit").submit(function(e) {
        e.preventDefault();

        var formId = $(this).attr('id');
        var formAction = $(this).attr('action');
        var btnhtml = $("button[form="+formId+"]").html();

        if (fSelectRequired(formId)) {
            return false;
        }

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
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#registrationCompletedModal').modal('toggle');
                setTimeout(() => {

                    location.href = "{{ route('profile') }}";

                }, 3000);

                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    

    </script>

@endpush