
@extends('exhibitors/layouts/app')

@section('title',__(Lang::get('web/payment.payment')))

@section('content')

   
    <!-- banner-start -->
    <div class="inner-banner-wrapper">
        <div class="container custom-container2">
            <div class="step-form-wrap step-preview-wrap">
                @php $userId =$resultData['result']['id']; @endphp
                @include('exhibitors.sidebar', compact('groupInfoResult','userId'))
            </div>

            <div class="step-form">
                <div class="tabs-wrapper">
                    <ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                                aria-selected="true">
                                <svg width="35" height="36" viewBox="0 0 35 36" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M26.1395 3.13477C24.4781 3.13477 22.8721 3.59302 21.543 4.45226C21.8199 4.79595 22.0414 5.19693 22.2629 5.5979C23.3705 4.91051 24.6996 4.50954 26.0841 4.50954C30.2377 4.50954 33.6158 8.00375 33.6158 12.2999C33.6158 16.1951 30.7915 19.4605 27.1917 19.9757C27.6348 20.3194 28.0224 20.7204 28.4101 21.1214C32.176 20.0903 35 16.5388 35 12.2999C35 7.25909 31.068 3.13477 26.1392 3.13477H26.1395Z"
                                        fill="#111111" />
                                    <path
                                        d="M27.2466 15.337C27.1358 15.3943 26.9697 15.4516 26.8036 15.5089H26.6928C26.4159 15.5662 26.1944 15.5662 25.9729 15.5662C25.8067 15.5662 25.696 15.5662 25.5298 15.5089C25.3637 15.5089 25.1975 15.4516 25.0868 15.3943C24.8099 15.2797 24.533 15.2225 24.3115 15.1079C24.1453 15.0506 23.9792 14.9361 23.8684 14.8788L23.5361 16.2535C23.7577 16.3681 24.0899 16.4827 24.3668 16.5972C24.7545 16.7118 25.1422 16.7691 25.5852 16.8264V18.4303H26.8589V16.7691C27.0805 16.7118 27.2466 16.7118 27.4127 16.6545C27.8004 16.54 28.1327 16.3108 28.4096 16.0817C28.6865 15.8526 28.908 15.5662 29.0188 15.2225C29.1849 14.8788 29.2403 14.5351 29.2403 14.1341C29.2403 13.504 29.0741 12.9885 28.7419 12.5875C28.4096 12.1865 27.9665 11.9574 27.3574 11.7855C27.1912 11.7282 26.9697 11.671 26.8036 11.671C26.6928 11.671 26.6374 11.6137 26.5267 11.6137C26.2498 11.5564 26.0282 11.4991 25.8067 11.4418C25.696 11.4418 25.6406 11.3846 25.5298 11.3846C25.2529 11.27 25.0314 11.1554 24.8653 11.0409C24.6991 10.869 24.5884 10.5826 24.5884 10.2389C24.5884 9.83794 24.7545 9.49424 25.0868 9.3224C25.1975 9.26511 25.3637 9.15055 25.5298 9.09327C25.7513 9.03599 25.9729 8.9787 26.2498 8.9787C26.4159 8.9787 26.582 8.9787 26.8036 9.03599C27.0805 9.09327 27.3574 9.15055 27.6343 9.26511C28.0773 9.43696 28.465 9.66609 28.8526 9.9525H28.908V8.40588C28.5757 8.23404 28.1881 8.06219 27.6896 7.94762C27.3574 7.89034 27.0805 7.83306 26.7482 7.77578V6.17188H25.4744V7.83306C24.8653 7.94762 24.3668 8.17675 23.9792 8.52045C23.8684 8.63501 23.7023 8.74957 23.5915 8.92142C23.8684 10.0098 24.0346 11.1554 24.0346 12.3584V12.4156C24.2561 12.5875 24.5884 12.702 24.9206 12.8166C25.0868 12.8739 25.2529 12.9312 25.4744 12.9312C25.5852 12.9312 25.6406 12.9885 25.7513 12.9885C26.0282 13.0457 26.3051 13.103 26.4713 13.1603C26.582 13.1603 26.6928 13.2176 26.7482 13.2176C27.0805 13.3322 27.302 13.4467 27.4127 13.5613C27.5789 13.7331 27.6896 14.0195 27.6896 14.3632C27.6896 14.6496 27.6343 14.8215 27.5789 14.9933C27.5789 15.1079 27.4681 15.2225 27.2466 15.337Z"
                                        fill="#111111" />
                                    <path
                                        d="M11.3529 22.2672C6.03644 22.2672 1.71679 17.7992 1.71679 12.3001C1.71679 6.80104 6.03644 2.33303 11.3529 2.33303C16.6694 2.33303 20.9891 6.80104 20.9891 12.3001C20.9891 14.5914 20.2138 16.7109 18.9954 18.3721C19.7707 18.1429 20.546 18.0284 21.3214 17.9711C22.2074 16.3099 22.7613 14.3623 22.7613 12.3001C22.7613 5.82725 17.6663 0.5 11.3529 0.5C9.80229 0.5 8.30703 0.843693 6.92252 1.41652C4.04275 2.73401 1.77217 5.19714 0.664562 8.23277C0.221521 9.49298 0 10.8678 0 12.2998C0 13.5027 0.166141 14.7057 0.498422 15.8513C1.05222 17.6843 2.04907 19.3455 3.37819 20.7203L3.82123 21.1786C4.20889 21.465 4.59655 21.8087 5.0396 22.0951C5.48264 22.3815 5.92568 22.6679 6.36872 22.897C6.53486 23.0116 6.75638 23.0689 6.92252 23.1834C8.30703 23.7563 9.80229 24.0999 11.3529 24.0999C11.9067 24.0999 12.5159 24.0427 13.0697 23.9854C13.3466 23.2407 13.7343 22.496 14.2327 21.8659C13.3466 22.0951 12.3498 22.2672 11.3529 22.2672Z"
                                        fill="#111111" />
                                    <path
                                        d="M10.577 4.39453V6.51397C10.0232 6.62854 9.4694 6.80038 9.02636 7.0868C8.86022 7.20136 8.74945 7.25864 8.58331 7.37321C7.86337 8.00331 7.47571 8.80526 7.47571 9.83634C7.47571 10.581 7.64185 11.2111 8.02951 11.7267C8.41717 12.2422 9.02636 12.6432 9.85706 12.8723L10.5216 13.0441C10.6324 13.0441 10.7431 13.1014 10.8539 13.1014C11.2416 13.1587 11.5185 13.2733 11.74 13.3306C11.8507 13.3878 12.0169 13.3878 12.1276 13.4451C12.5153 13.5597 12.8476 13.7315 13.0137 13.9034C13.2352 14.1325 13.346 14.4762 13.346 14.9345C13.346 15.2782 13.2906 15.5646 13.1799 15.7364C13.0691 15.9083 12.903 16.0801 12.6814 16.2519C12.5153 16.3665 12.3492 16.4238 12.0723 16.4811C12.0169 16.4811 11.9615 16.4811 11.9615 16.5384C11.6292 16.5956 11.2969 16.6529 11.02 16.6529C10.8539 16.6529 10.6324 16.6529 10.4662 16.5956C10.2447 16.5956 10.0786 16.5384 9.85706 16.4811C9.4694 16.3665 9.13712 16.2519 8.80483 16.0801C8.47255 15.9083 8.19565 15.7364 7.97413 15.6218C7.75261 15.45 7.58647 15.3354 7.42033 15.2209H7.30957V17.3403C7.69723 17.5694 8.25103 17.7986 8.86022 17.9704C9.35864 18.1423 9.85706 18.1995 10.4109 18.2568V20.319H12.0169V18.1995C12.2938 18.1423 12.5153 18.085 12.7368 18.0277C12.9583 17.9131 13.2352 17.8559 13.4568 17.684C13.6783 17.5694 13.8998 17.3976 14.066 17.2257C14.3982 16.9393 14.6751 16.5384 14.8967 16.1374C15.0628 15.6791 15.1736 15.2782 15.1736 14.7626C15.1736 13.9607 14.952 13.2733 14.5644 12.815C14.1767 12.3568 13.5675 12.0131 12.7368 11.7839L12.0723 11.6121C11.9615 11.5548 11.8507 11.5548 11.6846 11.5548C11.3523 11.4975 11.0754 11.4402 10.7985 11.3257C10.6878 11.2684 10.5216 11.2684 10.4109 11.2111C10.0232 11.0965 9.7463 10.9247 9.58016 10.7529L9.4694 10.6383C9.30326 10.4092 9.1925 10.0655 9.1925 9.66449C9.1925 9.43537 9.24788 9.20624 9.30326 9.03439C9.41402 8.80526 9.58016 8.63341 9.80168 8.46157C9.96782 8.347 10.1893 8.28972 10.3555 8.23244C10.6324 8.11787 10.9647 8.11787 11.2969 8.11787C11.5185 8.11787 11.74 8.11787 11.9615 8.17516C12.2938 8.23244 12.6814 8.347 13.0137 8.46157C13.5675 8.6907 14.1213 8.97711 14.5644 9.37808H14.6751V7.37321C14.2875 7.14408 13.7337 6.97223 13.1245 6.80038C12.7368 6.68582 12.3492 6.62854 11.9615 6.57126V4.39453H10.577Z"
                                        fill="#111111" />
                                    <path
                                        d="M21.598 19.3457C17.2783 19.3457 13.7891 22.9545 13.7891 27.4228C13.7891 31.8912 17.278 35.4999 21.598 35.4999C25.918 35.4999 29.4069 31.8912 29.4069 27.4228C29.4069 22.9545 25.918 19.3457 21.598 19.3457ZM24.5331 30.4585C24.367 30.8021 24.1455 31.0886 23.8686 31.375C23.5363 31.6614 23.204 31.8332 22.8163 32.0051C22.6502 32.0624 22.4841 32.1196 22.2625 32.1196V33.8381H20.9334V32.1769C20.4904 32.1769 20.1027 32.0624 19.6597 31.9478C19.1613 31.7759 18.7182 31.6041 18.3859 31.4323V29.7138H18.4413C18.5521 29.8284 18.7182 29.9429 18.8844 30.0575C19.0505 30.172 19.272 30.2866 19.5489 30.4585L20.3796 30.8021C20.5458 30.8594 20.7119 30.8594 20.878 30.9167C21.0442 30.9167 21.1549 30.974 21.3211 30.974C21.5426 30.974 21.8195 30.974 22.0964 30.8594H22.2072C22.4287 30.8021 22.5948 30.7449 22.7056 30.6876C22.8717 30.573 23.0379 30.4585 23.0932 30.2866C23.204 30.1148 23.204 29.9429 23.204 29.6565C23.204 29.3128 23.0932 29.0264 22.9271 28.8546C22.761 28.6827 22.5394 28.5681 22.2072 28.4536C22.1518 28.339 22.041 28.339 21.9303 28.2817C21.7641 28.2244 21.4872 28.1672 21.2103 28.1099C21.0996 28.1099 20.9888 28.0526 20.9334 28.0526C20.7119 27.9953 20.5458 27.938 20.3796 27.8808C19.7151 27.6516 19.2166 27.3652 18.9397 26.9642C18.6075 26.5633 18.4413 26.0477 18.4413 25.4749C18.4413 24.6729 18.7182 23.9856 19.3274 23.47C19.7704 23.1263 20.2689 22.8972 20.9334 22.7826V21.0642H22.2625V22.7254C22.5948 22.7254 22.8717 22.7826 23.204 22.8972C23.7024 23.0118 24.0901 23.1836 24.4224 23.3555V25.0166H24.367C23.9793 24.7302 23.5917 24.4438 23.0932 24.272C22.8163 24.1574 22.5394 24.1001 22.2625 24.0428C22.0964 24.0428 21.8749 23.9856 21.7087 23.9856C21.4318 23.9856 21.1549 24.0428 20.9334 24.1001C20.7673 24.1574 20.6011 24.2147 20.4904 24.3293C20.1581 24.5584 19.992 24.8448 19.992 25.3031C19.992 25.704 20.1027 25.9904 20.2689 26.1623C20.435 26.3341 20.6565 26.4487 20.9334 26.5633C21.0442 26.5633 21.0996 26.6205 21.2103 26.6778C21.4318 26.7351 21.6534 26.7924 21.9303 26.8497C22.041 26.8497 22.1518 26.907 22.2072 26.907C22.3733 26.9642 22.5948 26.9642 22.761 27.0215C23.4255 27.1934 23.924 27.4798 24.2009 27.8808C24.5331 28.2817 24.6993 28.7973 24.6993 29.4847C24.7547 29.7711 24.6993 30.1148 24.5331 30.4585Z"
                                        fill="#111111" />
                                </svg>
                                @lang('web/payment.registered_fee') </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                            aria-labelledby="pills-home-tab">
                            <div class="payment-order-wrap">
                                <div class="payment-order-box">
                                    <ul>
                                        <li>
                                            <p>@lang('web/payment.payment') @lang('web/payment.Accepted')</p>
                                            <b>${{\App\Helpers\commonHelper::getTotalAcceptedAmount($resultData['result']['id'], true)}}</b>
                                        </li>
                                        <li>
                                            <p>@lang('web/payment.payment') @lang('web/payment.in-process')</p>
                                            <b>${{\App\Helpers\commonHelper::getTotalAmountInProcess($resultData['result']['id'], true)}}</b>
                                        </li>
                                        <li>
                                            <p>@lang('web/payment.payment') @lang('web/payment.declined')</p>
                                            <b>${{\App\Helpers\commonHelper::getTotalRejectedAmount($resultData['result']['id'], true)}}</b>
                                        </li>
                                        <li>
                                            <p>@lang('web/payment.balance') </p>
                                            <b>${{\App\Helpers\commonHelper::getTotalPendingAmount($resultData['result']['id'], true)}}</b>
                                        </li>
                                    </ul>
                                </div>
                                <div class="fess-wrap">
                                    <span>
                                        <p>@lang('web/payment.registered_fee') </p>
                                        <h4>${{$resultData['result']['amount']}}</h4>
                                    </span>
                                </div>
                            </div>
                            <div class="method-wrap">
                                <h4>@lang('web/payment.payment-method')</h4>
                                <a style="display:none" href="javascript:;">@lang('web/payment.how-does-it-work')</a>
                                <div class="card-box credit-box">
                                    <div class="right-pay">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C2.23864 0 0 2.23864 0 5C0 7.76136 2.23864 10 5 10C7.76136 10 10 7.76136 10 5C10 2.23864 7.76136 0 5 0ZM7.16727 4.15455C7.20718 4.10893 7.23756 4.05579 7.25663 3.99826C7.2757 3.94073 7.28307 3.87996 7.27831 3.81954C7.27354 3.75912 7.25674 3.70026 7.2289 3.64642C7.20105 3.59259 7.16272 3.54487 7.11616 3.50606C7.0696 3.46726 7.01575 3.43817 6.95777 3.42048C6.8998 3.4028 6.83887 3.39689 6.77858 3.4031C6.71829 3.40932 6.65985 3.42752 6.6067 3.45665C6.55355 3.48578 6.50676 3.52525 6.46909 3.57273L4.51455 5.91773L3.50318 4.90591C3.41745 4.82311 3.30263 4.77729 3.18345 4.77833C3.06427 4.77937 2.95027 4.82717 2.86599 4.91145C2.78172 4.99572 2.73391 5.10973 2.73288 5.22891C2.73184 5.34809 2.77766 5.46291 2.86045 5.54864L4.22409 6.91227C4.26875 6.95691 4.32222 6.99175 4.3811 7.01457C4.43997 7.0374 4.50295 7.04771 4.56603 7.04484C4.62911 7.04198 4.6909 7.026 4.74746 6.99793C4.80402 6.96986 4.85411 6.93032 4.89455 6.88182L7.16727 4.15455Z" fill="#27C637"/>
                                        </svg>
                                    </div>
                                    <div class="card-icon">
                                        <span>
                                            <svg width="38" height="31" viewBox="0 0 38 31" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.70382 0.367605C7.19063 0.367605 5.94409 1.63668 5.94409 3.16295V6.35366H2.7714C1.25848 6.35366 0 7.62089 0 9.14661V27.5445C0 29.0705 1.25848 30.3398 2.7714 30.3398L29.296 30.3401C30.8089 30.3401 32.0652 29.0708 32.0652 27.5448V24.3447H35.2286C36.7418 24.3447 38 23.078 38 21.5515V3.16253C38 1.63626 36.7418 0.367188 35.2286 0.367188L8.70382 0.367605ZM8.70382 2.76299H35.2284C35.4664 2.76299 35.6228 2.92312 35.6228 3.16293V21.5519C35.6228 21.792 35.4664 21.9497 35.2284 21.9497H32.065V9.14676C32.065 8.95588 32.0443 8.77061 32.007 8.58989C31.7459 7.32486 30.62 6.35357 29.2957 6.35357H8.30936V3.16285C8.30936 2.92278 8.46574 2.7629 8.70376 2.7629L8.70382 2.76299ZM2.7714 8.74904H29.296C29.3553 8.74904 29.4078 8.75867 29.4561 8.77711H29.4584C29.6018 8.83325 29.6904 8.96746 29.6904 9.14684V11.9141H2.37667V9.14684C2.37667 8.9065 2.53252 8.74904 2.77107 8.74904L2.7714 8.74904ZM2.37701 14.3119H29.6907V15.7878H2.37701V14.3119ZM2.37701 18.1832H29.6907V27.545C29.6907 27.7854 29.5349 27.945 29.2963 27.945L2.77174 27.9453C2.53346 27.9453 2.37735 27.7857 2.37735 27.5453L2.37701 18.1832ZM21.2579 19.996C20.943 19.9974 20.6414 20.1249 20.4198 20.3503C20.1979 20.5756 20.0739 20.8809 20.0752 21.1986V24.6371C20.0763 24.9531 20.2014 25.256 20.4229 25.4795C20.6445 25.7028 20.9446 25.8289 21.2579 25.8303H26.0076C26.3225 25.8314 26.6252 25.7065 26.8487 25.4827C27.0721 25.259 27.1985 24.9547 27.1999 24.6372V21.1986C27.2009 20.8794 27.0758 20.5728 26.8518 20.3472C26.6281 20.1213 26.3241 19.9951 26.0077 19.9962L21.2579 19.996ZM6.07637 20.5436C5.75672 20.5363 5.44793 20.6593 5.21945 20.8847C4.99097 21.1101 4.86216 21.4188 4.86216 21.7413C4.86216 22.0637 4.99098 22.3722 5.21945 22.5976C5.44793 22.823 5.75672 22.9462 6.07637 22.939H12.9832C13.3026 22.9462 13.6116 22.823 13.8401 22.5976C14.0686 22.3722 14.1974 22.0637 14.1974 21.7413C14.1974 21.4188 14.0686 21.1101 13.8401 20.8847C13.6116 20.6593 13.3026 20.5363 12.9832 20.5436H6.07637ZM22.4501 22.3917H24.8249V23.4445H22.4501V22.3917ZM5.95356 23.5402C5.52894 23.5624 5.14834 23.8113 4.95509 24.1933C4.7616 24.5753 4.7852 25.0322 5.01659 25.3921C5.24771 25.7519 5.65192 25.9599 6.07652 25.9377H9.02204H9.02178C9.44639 25.9377 9.83893 25.7094 10.0512 25.3383C10.2636 24.9675 10.2636 24.5104 10.0512 24.1396C9.83894 23.7685 9.44639 23.5402 9.02178 23.5402H6.07626H6.07652C6.03544 23.538 5.99436 23.538 5.95354 23.5402L5.95356 23.5402Z"
                                                    fill="#58595B" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="credit-content">
                                        <h6>@lang('web/payment.credit-card')</h6>
                                        <p>@lang('web/payment.pay-using-credit-card')</p>
                                    </div>
                                </div>
                                
                                <div class="accordion payment-accordion" id="accordionExample2">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseTwo" aria-expanded="true"
                                                aria-controls="collapseTwo">
                                                @lang('web/payment.payment-history') 
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse show"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
                                            <div class="accordion-body">
                                                <table class="table">
                                                    <thead>
                                                      <tr>
                                                        <th>@lang('web/payment.date')</th>
                                                        <th>@lang('web/payment.amount')</th>
                                                        <th>@lang('web/payment.transfer-id')</th>
                                                        <th>@lang('web/payment.utr-no')</th>
                                                        <th>@lang('web/payment.Mode')</th>
                                                        <th>@lang('web/payment.payment-pay')</th>
                                                        <th>@lang('web/payment.payment-for')</th>
                                                        <th>@lang('web/payment.Status')</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(!empty($transactions))
                                                            @foreach($transactions as $transaction)
                                                                <tr>
                                                                    <th>{{date('d-M-Y',strtotime($transaction->created_at))}}</th>
                                                                    <td>${{$transaction->amount}}</td>
                                                                    <td>{{$transaction->order_id}}</td>
                                                                    <td>{{$transaction->bank_transaction_id}}</td>
                                                                    <td>{{$transaction->bank}} Transfer</td>
                                                                    <td>{{\App\Helpers\commonHelper::getParticularNameById($transaction->particular_id)}}</td>
                                                                    <td>
                                                                        @if($transaction->particular_id == 1 || $transaction->particular_id == 2)
                                                                            Registration
                                                                        @else
                                                                            Donation
                                                                        @endif
                                                                    </td>
                                                                    <td>@if($transaction->payment_status == 0) @lang('web/home.pending') @elseif($transaction->payment_status == 2) @lang('web/home.confirm') @else @lang('web/home.decline') @endif</td>
                                                                    
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                            aria-labelledby="pills-profile-tab">
                            <div class="method-wrap">
                                <h4>@lang('web/payment.payment-method')</h4>
                                <a href="javascript:;">@lang('web/payment.how-does-it-work')</a>
                                <div class="card-box credit-box">
                                    <div class="right-pay">
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5 0C2.23864 0 0 2.23864 0 5C0 7.76136 2.23864 10 5 10C7.76136 10 10 7.76136 10 5C10 2.23864 7.76136 0 5 0ZM7.16727 4.15455C7.20718 4.10893 7.23756 4.05579 7.25663 3.99826C7.2757 3.94073 7.28307 3.87996 7.27831 3.81954C7.27354 3.75912 7.25674 3.70026 7.2289 3.64642C7.20105 3.59259 7.16272 3.54487 7.11616 3.50606C7.0696 3.46726 7.01575 3.43817 6.95777 3.42048C6.8998 3.4028 6.83887 3.39689 6.77858 3.4031C6.71829 3.40932 6.65985 3.42752 6.6067 3.45665C6.55355 3.48578 6.50676 3.52525 6.46909 3.57273L4.51455 5.91773L3.50318 4.90591C3.41745 4.82311 3.30263 4.77729 3.18345 4.77833C3.06427 4.77937 2.95027 4.82717 2.86599 4.91145C2.78172 4.99572 2.73391 5.10973 2.73288 5.22891C2.73184 5.34809 2.77766 5.46291 2.86045 5.54864L4.22409 6.91227C4.26875 6.95691 4.32222 6.99175 4.3811 7.01457C4.43997 7.0374 4.50295 7.04771 4.56603 7.04484C4.62911 7.04198 4.6909 7.026 4.74746 6.99793C4.80402 6.96986 4.85411 6.93032 4.89455 6.88182L7.16727 4.15455Z" fill="#27C637"/>
                                        </svg>
                                    </div>
                                    <div class="card-icon">
                                        <span>
                                            <svg width="38" height="31" viewBox="0 0 38 31" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.70382 0.367605C7.19063 0.367605 5.94409 1.63668 5.94409 3.16295V6.35366H2.7714C1.25848 6.35366 0 7.62089 0 9.14661V27.5445C0 29.0705 1.25848 30.3398 2.7714 30.3398L29.296 30.3401C30.8089 30.3401 32.0652 29.0708 32.0652 27.5448V24.3447H35.2286C36.7418 24.3447 38 23.078 38 21.5515V3.16253C38 1.63626 36.7418 0.367188 35.2286 0.367188L8.70382 0.367605ZM8.70382 2.76299H35.2284C35.4664 2.76299 35.6228 2.92312 35.6228 3.16293V21.5519C35.6228 21.792 35.4664 21.9497 35.2284 21.9497H32.065V9.14676C32.065 8.95588 32.0443 8.77061 32.007 8.58989C31.7459 7.32486 30.62 6.35357 29.2957 6.35357H8.30936V3.16285C8.30936 2.92278 8.46574 2.7629 8.70376 2.7629L8.70382 2.76299ZM2.7714 8.74904H29.296C29.3553 8.74904 29.4078 8.75867 29.4561 8.77711H29.4584C29.6018 8.83325 29.6904 8.96746 29.6904 9.14684V11.9141H2.37667V9.14684C2.37667 8.9065 2.53252 8.74904 2.77107 8.74904L2.7714 8.74904ZM2.37701 14.3119H29.6907V15.7878H2.37701V14.3119ZM2.37701 18.1832H29.6907V27.545C29.6907 27.7854 29.5349 27.945 29.2963 27.945L2.77174 27.9453C2.53346 27.9453 2.37735 27.7857 2.37735 27.5453L2.37701 18.1832ZM21.2579 19.996C20.943 19.9974 20.6414 20.1249 20.4198 20.3503C20.1979 20.5756 20.0739 20.8809 20.0752 21.1986V24.6371C20.0763 24.9531 20.2014 25.256 20.4229 25.4795C20.6445 25.7028 20.9446 25.8289 21.2579 25.8303H26.0076C26.3225 25.8314 26.6252 25.7065 26.8487 25.4827C27.0721 25.259 27.1985 24.9547 27.1999 24.6372V21.1986C27.2009 20.8794 27.0758 20.5728 26.8518 20.3472C26.6281 20.1213 26.3241 19.9951 26.0077 19.9962L21.2579 19.996ZM6.07637 20.5436C5.75672 20.5363 5.44793 20.6593 5.21945 20.8847C4.99097 21.1101 4.86216 21.4188 4.86216 21.7413C4.86216 22.0637 4.99098 22.3722 5.21945 22.5976C5.44793 22.823 5.75672 22.9462 6.07637 22.939H12.9832C13.3026 22.9462 13.6116 22.823 13.8401 22.5976C14.0686 22.3722 14.1974 22.0637 14.1974 21.7413C14.1974 21.4188 14.0686 21.1101 13.8401 20.8847C13.6116 20.6593 13.3026 20.5363 12.9832 20.5436H6.07637ZM22.4501 22.3917H24.8249V23.4445H22.4501V22.3917ZM5.95356 23.5402C5.52894 23.5624 5.14834 23.8113 4.95509 24.1933C4.7616 24.5753 4.7852 25.0322 5.01659 25.3921C5.24771 25.7519 5.65192 25.9599 6.07652 25.9377H9.02204H9.02178C9.44639 25.9377 9.83893 25.7094 10.0512 25.3383C10.2636 24.9675 10.2636 24.5104 10.0512 24.1396C9.83894 23.7685 9.44639 23.5402 9.02178 23.5402H6.07626H6.07652C6.03544 23.538 5.99436 23.538 5.95354 23.5402L5.95356 23.5402Z"
                                                    fill="#58595B" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="credit-content">
                                        <h6>@lang('web/payment.credit-card')</h6>
                                        <p>@lang('web/payment.credit-card_description')</p>
                                    </div>
                                </div>
                                <div class="accordion payment-accordion" id="accordionExample2">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseTwo" aria-expanded="true"
                                                aria-controls="collapseTwo">
                                                @lang('web/payment.payment') @lang('web/payment.history')
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse show"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionExample2">
                                            <div class="accordion-body">
                                                <table class="table">
                                                    <thead>
                                                      <tr>
                                                        <th>@lang('web/payment.date')</th>
                                                        <th>@lang('web/payment.amount')</th>
                                                        <th>Status</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if(!empty($transactions))
                                                            @foreach($transactions as $transaction)
                                                                <tr>
                                                                    <th>{{date('d-M-Y',strtotime($transaction->created_at))}}</th>
                                                                    <td>${{$transaction->amount}}</td>
                                                                    <td>@if($transaction->status == null) Pending @else {{$transaction->status}} @endif</td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(\App\Helpers\commonHelper::getTotalPendingAmount($resultData['result']['id']) != '0')
                <div class="register-next">
                    <a class="main-btn bg-gray-btn" href="{{url('online-payment-full/stripe')}}">@lang('web/payment.make') @lang('web/payment.payment')</a>
                </div>
                @endif
            </div>

        </div>
    </div>
    <!-- banner-end -->


@endsection


@push('custom_js')

<script>

    $('#PartialPaymentOnline').click(function(){
        $("#PartialPaymentOnlineDiv").toggle();
        $("#PartialPaymentOfflineDiv").css('display','none');
    });

    $('#PartialPaymentOffline').click(function(){
       
        $("#PartialPaymentOfflineDiv").toggle();
        $("#PartialPaymentOnlineDiv").css('display','none');
    });

    $('#FullPaymentOffline').click(function(){
        
        $("#FullPaymentOfflineDiv").toggle();
    });
    
    $("form#formSubmit").submit(function(e) {
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
                $('#formSubmit')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#fullAmount').modal('hide');
                $('#SponsoredAmount').modal('hide');
                $('#partialamount').modal('hide');
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
    
    $("form#cashFormSubmit").submit(function(e) {
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
                $('#formSubmit')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#CashAmount').modal('hide');
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $("form#formSubmit1").submit(function(e) {
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
                $('#formSubmit1')[0].reset();
                // showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#partialamount').modal('hide');
                if(data.urlPage){
                    window.location.href = data.url;
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    
    $("form#formSubmit2").submit(function(e) {
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
                $('#formSubmit2')[0].reset();
                // showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#SponsoredAmount').modal('hide');
                if(data.urlPage){
                    window.location.href = data.url;
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
    
    $("form#formSubmit3").submit(function(e) {
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
                $('#formSubmit3')[0].reset();
                // showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#fullAmount').modal('hide');
                if(data.urlPage){
                    window.location.href = data.url;
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    $("form#DonateformSubmit").submit(function(e) {
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
                $('#formSubmit1')[0].reset();
                // showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                $('#DonateAmount').modal('hide');
                if(data.urlPage){
                    window.location.href = data.url;
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });

    
    $("form#formSubmitPartialOffline").submit(function(e) {
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
                $('#formSubmitPartialOffline')[0].reset();
                showMsg('success', data.message);
                submitButton(formId, btnhtml, false);
                
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
</script>

<script>  
		$(document).ready(function(){
			$('.addInFullAmountPaymentField').on('change', function() {

                $(".divShowForOnChangePaymentType").hide();
                $(".ExtraFieldOnChangePaymentInFullAmount").show();
                if ( this.value == 'Wire'){
                    $("#showWire").show();
                    $("#showInFullAmount").show();
                    $("#showInFullAmount").hide();
                    $("#senderNameRequiredInFullAmount").attr('required',true);
                    $("#AmountRequiredInFullAmount").attr('required',true);
                    $("#reference_numberRequiredIn_FullAmount").attr('required',true);
                    $("#countryOfSenderRequiredInFullAmount").attr('required',false);

                    
                }else if ( this.value == 'MG'){
                    $("#countryOfSenderRequired").attr('required',true);
                    $("#senderNameRequiredInFullAmount").attr('required',true);
                    $("#AmountRequiredInFullAmount").attr('required',true);
                    $("#countryOfSenderRequiredInFullAmount").attr('required',true);

                }else{
                    $("#showWU").show();
                }
			});
		});
</script>
<script>  
		$(document).ready(function(){
			$('.addPaymentTypeInPartial').on('change', function() {

                $(".divShowForOnChangePaymentInPartial").hide();
                $(".ExtraFieldOnChangePaymentInPartial").show();
                if ( this.value == 'Wire'){
                    $("#showInPartialWire").show();
                    $("#showInPartialWU").hide();
                    $("#senderNameRequired").attr('required',true);
                    $("#AmountRequired").attr('required',true);
                    $("#reference_numberRequired").attr('required',true);
                    $("#countryOfSenderRequired").attr('required',false);
                    
                }else if ( this.value == 'MG'){
                    $("#showInPartialMG").show();
                    $("#countryOfSenderRequired").attr('required',true);
                    $("#senderNameRequired").attr('required',true);
                    $("#AmountRequired").attr('required',true);

                }else{
                    $("#senderNameRequired").attr('required',true);
                    $("#countryOfSenderRequired").attr('required',true);
                    $("#AmountRequired").attr('required',true);

                }
			});
		});


    $('.btn-close').click(function() {

        $('#formSubmit2').trigger("reset");
        $('#formSubmit3').trigger("reset");
        $('#formSubmit').trigger("reset");
 });
</script>
@endpush