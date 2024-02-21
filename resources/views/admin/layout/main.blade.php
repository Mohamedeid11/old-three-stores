<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Three Stores Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <link rel="stylesheet" href="{{asset('ckeditor/css/samples.css')}}">
    <link rel="stylesheet" href="{{asset('ckeditor/toolbarconfigurator/lib/codemirror/neo.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/tether/dist/css/tether.css')}}">
    <link rel="stylesheet"
          href="{{asset('assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/select2/dist/css/select2.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet"
          href="{{asset('assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css')}}">
    <link rel="stylesheet"
          href="{{asset('assets/vendors/general/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/summernote/dist/summernote.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/animate.css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/morris.js/morris.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/general/socicon/css/socicon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/line-awesome/css/line-awesome.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/flaticon/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/flaticon2/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/fontawesome5/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}">
    <!--begin::Global Theme Styles(used by all pages) -->
    <link rel="stylesheet" href="{{asset('assets/demo/default/base/style.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('assets/demo/default/base/custom.css')}}">
    <!--end::Global Theme Styles -->
    <!--begin::Layout Skins(used by all pages) -->
    <link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/base/light.css')}}">
    <link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/menu/light.css')}}">
    <link rel="stylesheet" href="{{asset('assets/demo/default/skins/brand/dark.css')}}">
    <link rel="stylesheet" href="{{asset('assets/demo/default/skins/aside/dark.css')}}">
    <!--end::Layout Skins -->
    <link rel="stylesheet" href="{{ asset('css/custom.css')}}">
    <link href="{{asset('favicon.ico')}}" rel="shortcut icon" type="image/vnd.microsoft.icon">
    <link rel="shortcut icon" type="image/ico" href="{{asset('session1.png')}}"/>

    <link href="
https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css
" rel="stylesheet">

    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">

    <style>
        .lds-grid {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .lds-grid div {
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            animation: lds-grid 1.2s linear infinite;
        }

        .lds-grid div:nth-child(1) {
            top: 8px;
            left: 8px;
            animation-delay: 0s;
        }

        .lds-grid div:nth-child(2) {
            top: 8px;
            left: 32px;
            animation-delay: -0.4s;
        }

        .lds-grid div:nth-child(3) {
            top: 8px;
            left: 56px;
            animation-delay: -0.8s;
        }

        .lds-grid div:nth-child(4) {
            top: 32px;
            left: 8px;
            animation-delay: -0.4s;
        }

        .lds-grid div:nth-child(5) {
            top: 32px;
            left: 32px;
            animation-delay: -0.8s;
        }

        .lds-grid div:nth-child(6) {
            top: 32px;
            left: 56px;
            animation-delay: -1.2s;
        }

        .lds-grid div:nth-child(7) {
            top: 56px;
            left: 8px;
            animation-delay: -0.8s;
        }

        .lds-grid div:nth-child(8) {
            top: 56px;
            left: 32px;
            animation-delay: -1.2s;
        }

        .lds-grid div:nth-child(9) {
            top: 56px;
            left: 56px;
            animation-delay: -1.6s;
        }

        @keyframes lds-grid {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .loader-ajax {
            width: 100vw;
            height: 100%;
            background: #555656e6;
            position: fixed;
            display: flex;
            align-items: center;
            z-index: 1112;
            justify-content: center;
        }


    </style>
    <style>
        .lds-hourglass {
            width: 100vw;
            height: 100%;
            background: #555656e6;
            position: fixed;
            display: flex;
            align-items: center;
            z-index: 1112;
            justify-content: center;
        }

        .lds-hourglass:after {
            content: " ";
            display: block;
            border-radius: 50%;
            width: 0;
            height: 0;
            margin: 8px;
            box-sizing: border-box;
            border: 32px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-hourglass 1.2s infinite;
        }

        @keyframes lds-hourglass {
            0% {
                transform: rotate(0);
                animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
            }
            50% {
                transform: rotate(900deg);
                animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
            }
            100% {
                transform: rotate(1800deg);
            }
        }

    </style>

    <style>
        @keyframes placeHolderShimmer {
            0% {
                background-position: -468px 0
            }
            100% {
                background-position: 468px 0
            }
        }

        .linear-background {
            animation-duration: 1s;
            animation-fill-mode: forwards;
            animation-iteration-count: infinite;
            animation-name: placeHolderShimmer;
            animation-timing-function: linear;
            background: #f6f7f8;
            background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
            background-size: 1000px 104px;
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .inter-draw {
            background: #FFF;
            width: 100%;
            height: 100px;
            position: absolute;
            top: 100px;
        }

        .inter-right--top {
            background: #FFF;
            width: 100%;
            height: 20px;
            position: absolute;
            top: 20px;
            left: 100px;
        }

        .inter-right--bottom {
            background: #FFF;
            width: 100%;
            height: 50px;
            position: absolute;
            top: 60px;
            left: 100px;
        }

        .inter-crop {
            background: #FFF;
            width: 20px;
            height: 100%;
            position: absolute;
            top: 0;
            left: 100px;
        }
    </style>
    <style>
        #client_search {
            border: 1px solid black; /* Set the default border color */
        }

        #client_search.error {
            border-color: red; /* Set the border color when there's an error */
        }
    </style>
    @yield('styles')
</head>
<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
<!-- begin:: Page -->
<!-- begin:: Header Mobile -->

<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">

    <div class="kt-header-mobile__logo">

        <a href="{{url('/')}}">

            <img alt="Logo" src="{{asset('assets/media/logos/logo-4.png')}}"/>

        </a>

    </div>

    <div class="kt-header-mobile__toolbar">

        <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler">
            <span></span></button>

        <button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>

        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more"></i></button>

    </div>

</div>


<!-- end:: Header Mobile -->

<div class="kt-grid kt-grid--hor kt-grid--root">

    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">


        <!--------------------- Start Side Bar --------------------->


        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>

        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
             id="kt_aside">

            <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">

                <div class="kt-header__topbar">


                    <!-- <div class="kt-header__topbar-item">

                        <div class="kt-header__topbar-wrapper">

                            <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand">

                                    <i class="fas fa-envelope"></i>

                            </span>

                            </div>

                        </div> -->


                    <div class="kt-header__topbar-item kt-header__topbar-item--user">

                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">

                            <div class="kt-header__topbar-user">

                                <span class="kt-header__topbar-username kt-hidden-mobile">{{Auth::guard('admin')->user()->name}}</span>

                                @if(Auth::guard('admin')->user()->image == '')

                                    <img alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset('user.png')}}"/>

                                @else

                                    <img alt="{{Auth::guard('admin')->user()->name}}"
                                         src="{{ asset(Auth::guard('admin')->user()->image)}}"/>

                                @endif

                            </div>

                        </div>

                        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">


                            <!--begin: Head -->

                            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"

                                 style="background-image: url({{asset('assets/media/misc/bg-1.jpg')}})">

                                <div class="kt-user-card__avatar">

                                    @if(Auth::guard('admin')->user()->image == '')

                                        <img class="kt-hidden" alt="{{Auth::guard('admin')->user()->name}}"
                                             src="{{ asset('user.png')}}"/>

                                        <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">

													<img alt="{{Auth::guard('admin')->user()->name}}"
                                                         src="{{ asset('user.png')}}"/>

												</span>

                                    @else

                                        <img class="kt-hidden" alt="{{Auth::guard('admin')->user()->name}}"
                                             src="{{ asset(Auth::guard('admin')->user()->image)}}"/>

                                        <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">

													<img alt="{{Auth::guard('admin')->user()->name}}"
                                                         src="{{ asset(Auth::guard('admin')->user()->image)}}"/>

												</span>

                                    @endif

                                </div>

                                <div class="kt-user-card__name">{{Auth::guard('admin')->user()->name}}</div>

                                <!-- <div class="kt-user-card__badge">

                                    <span class="btn btn-success btn-sm btn-bold btn-font-md">23 messages</span>

                                </div> -->

                            </div>


                            <!--end: Head -->


                            <!--begin: Navigation -->

                            <div class="kt-notification">

                                <a href="{{url('profile')}}" class="kt-notification__item">

                                    <div class="kt-notification__item-icon">

                                        <i class="flaticon2-calendar-3 kt-font-success"></i>

                                    </div>

                                    <div class="kt-notification__item-details">

                                        <div class="kt-notification__item-title kt-font-bold">

                                            My Profile

                                        </div>

                                        <div class="kt-notification__item-time">

                                            Account settings and more

                                        </div>

                                    </div>

                                </a>

                                <a href="{{url('change_password')}}" class="kt-notification__item">

                                    <div class="kt-notification__item-icon">

                                        <i class="flaticon2-lock kt-font-danger"></i>

                                    </div>

                                    <div class="kt-notification__item-details">

                                        <div class="kt-notification__item-title kt-font-bold">

                                            Change Password

                                        </div>

                                        <div class="kt-notification__item-time">

                                            Change Account Password

                                        </div>

                                    </div>

                                </a>


                                <div class="kt-notification__custom">

                                    <a href="{{url('logout')}}" class="btn btn-label-brand btn-sm btn-bold">Sign Out</a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            </div>

            <!-- end:: Aside -->


            <!--------------------- Navigation Side Bar ------------------------------------------>

            <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">

                <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1"
                     data-ktmenu-dropdown-timeout="500">

                    <ul class="kt-menu__nav">

                        <!--------------------- Dashboard --------------------->

                        <li class="kt-menu__item" aria-haspopup="true">

                            <a href="{{url('/')}}" class="kt-menu__link ">

                                <span class="kt-menu__link-icon"><i class="fa fa-home"></i></span>

                                <span class="kt-menu__link-text">Dashboard</span>

                                <span class="kt-menu__link-badge"></span>

                            </a>

                        </li>

{{--                        <!--------------------- fulfillments --------------------->--}}

{{--                        <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">--}}

{{--                            <a href="{{url('fulfillments')}}" class="kt-menu__link">--}}

{{--                                <span class="kt-menu__link-icon"><i class="fa fa-user" aria-hidden="true"></i></span>--}}

{{--                                <span class="kt-menu__link-text"> Testing  </span>--}}

{{--                            </a>--}}

{{--                        </li>--}}

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Reports'))
                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">
                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                    <span class="kt-menu__link-icon"><i class="fa fa-chart-line"></i></span>
                                    <span class="kt-menu__link-text">Reports</span>
                                    <span class="kt-menu__link-badge"></span>
                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="kt-menu__submenu">
                                    <span class="kt-menu__arrow"></span>
                                    <ul class="kt-menu__subnav">
                                        <li class="kt-menu__item" aria-haspopup="true">
                                            <a href="{{url('product_reports')}}" class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>
                                                <span class="kt-menu__link-text">Products</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item" aria-haspopup="true">
                                            <a href="{{url('city_reports')}}" class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>
                                                <span class="kt-menu__link-text">Cities</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item" aria-haspopup="true">
                                            <a href="{{url('reps_reports')}}" class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>
                                                <span class="kt-menu__link-text">Reps</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item" aria-haspopup="true">
                                            <a href="{{url('moderators_reports')}}" class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>
                                                <span class="kt-menu__link-text">Moderators</span>
                                            </a>
                                        </li>
                                        <li class="kt-menu__item" aria-haspopup="true">
                                            <a href="{{url('ads_reports')}}" class="kt-menu__link ">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>
                                                <span class="kt-menu__link-text">ADS</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Admins'))

                        <!--------------------- Admins --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class="fa fa-user-alt"></i></span>

                                    <span class="kt-menu__link-text">Admins</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_admin'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('admins/create')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Add</span>

                                                </a>

                                            </li>

                                        @endif

                                        <li class="kt-menu__item " aria-haspopup="true">

                                            <a href="{{url('admins/')}}" class="kt-menu__link ">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                <span class="kt-menu__link-text">View</span>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Cities')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Payment Methods')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Colors')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Sizes')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Order Status')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Order Category'))

                        <!--------------------- Settings --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class="fa fa-cogs"></i></span>

                                    <span class="kt-menu__link-text">Settings</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">


                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Cities'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('cities')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Cities</span>

                                                </a>

                                            </li>

                                        @endif

                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Payment Methods'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('pay_methods')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Pay Methods</span>

                                                </a>

                                            </li>

                                        @endif

                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Colors'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('colors')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Colors</span>

                                                </a>

                                            </li>

                                        @endif

                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Sizes'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('sizes')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Sizes</span>

                                                </a>

                                            </li>

                                        @endif

                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Order Status'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('order_status')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Order Status</span>

                                                </a>

                                            </li>

                                        @endif

                                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Order Category'))

                                            <li class="kt-menu__item" aria-haspopup="true">

                                                <a href="{{url('order_category')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                    <span class="kt-menu__link-text">Order Category</span>

                                                </a>

                                            </li>

                                        @endif

                                    </ul>

                                </div>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Clients'))

                        <!--------------------- Clients --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fa fa-users"></i></span>

                                    <span class="kt-menu__link-text"> Clients</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_client'))

                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">

                                                <a href="{{url('clients/create')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Add</span>

                                                </a>

                                            </li>

                                        @endif

                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('clients')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">View</span>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Agents'))

                        <!--------------------- Agents --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fa fa-user-tie"></i></span>

                                    <span class="kt-menu__link-text"> Agents</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('agents/create')}}" class="kt-menu__link ">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">Add</span>

                                            </a>

                                        </li>

                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('agents')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">View</span>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Categories'))

                        <!--------------------- Categories --------------------->

                            <li class="kt-menu__item" aria-haspopup="true">

                                <a href="{{url('categories')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fa fa-list"></i></span>

                                    <span class="kt-menu__link-text"> Categories</span>

                                    <span class="kt-menu__link-badge"></span>

                                </a>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Products'))
                        <!--------------------- Products --------------------->
                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">
                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                                    <span class="kt-menu__link-icon"><i class=" fas fa-boxes"></i></span>
                                    <span class="kt-menu__link-text"> Products</span>
                                    <span class="kt-menu__link-badge"></span>
                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                </a>
                                <div class="kt-menu__submenu">
                                    <span class="kt-menu__arrow"></span>
                                    <ul class="kt-menu__subnav">
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_product'))
                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">
                                                <a href="{{url('products/create')}}" class="kt-menu__link ">
                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Add</span>
                                                </a>
                                            </li>
                                        @endif

                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">
                                            <a href="{{url('products')}}" class="kt-menu__link">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">View</span>
                                            </a>
                                        </li>
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'tags'))
                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">
                                                <a href="{{url('products_tags')}}" class="kt-menu__link ">
                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Tags</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if(false)

                        <!--------------------- Order Notes --------------------->

                            <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                                <a href="{{url('orders_notes')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-clipboard-list"></i></span>

                                    <span class="kt-menu__link-text"> Order Notes</span>

                                </a>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders'))

                        <!--------------------- Selling Order --------------------->



                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-sign-out-alt"></i></span>

                                    <span class="kt-menu__link-text"> Selling Order</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order'))

                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">

                                                <a href="{{url('selling_order/create')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Add</span>

                                                </a>

                                            </li>

                                        @endif

                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('selling_order')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">View</span>

                                            </a>

                                        </li>
                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'tags'))
                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">
                                                <a href="{{url('orders_tags')}}" class="kt-menu__link ">
                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Tags</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>

                                </div>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Reps Delivery'))

                        <!--------------------- Reps Delivery --------------------->

                            <li class="kt-menu__item" aria-haspopup="true">

                                <a href="{{url('reps_delivery')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-shipping-fast"></i></span>

                                    <span class="kt-menu__link-text"> Rep's Delivery</span>

                                </a>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Buying Orders'))

                        <!--------------------- Buying Order --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-sign-in-alt"></i></span>

                                    <span class="kt-menu__link-text"> Buying Order</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'add_buying_order'))

                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">

                                                <a href="{{url('buying_order/create')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">Add</span>

                                                </a>

                                            </li>

                                        @endif

                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('buying_order')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">View</span>

                                            </a>

                                        </li>
                                        <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('new_buying_order')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">Add To</span>

                                            </a>

                                        </li>

                                    </ul>

                                </div>

                            </li>

                        @endif



                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_inventory') || permission_checker(Auth::guard('admin')->user()->id, 'show_file_gard') )

                        <!--------------------- Clients --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-warehouse"></i></span>

                                    <span class="kt-menu__link-text"> Inventory</span>

                                    <span class="kt-menu__link-badge"></span>

                                    <i class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu">

                                    <span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_inventory'))

                                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                                data-ktmenu-submenu-toggle="hover">

                                                <a href="{{route('inventories.index')}}" class="kt-menu__link ">

                                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                            class="kt-menu__link-text">View</span>

                                                </a>

                                            </li>

                                        @endif

                                            @if(permission_checker(Auth::guard('admin')->user()->id, 'show_file_gard'))

                                            <li class="kt-menu__item " aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="{{url('file_gard')}}" class="kt-menu__link">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i><span
                                                        class="kt-menu__link-text">File Gard</span>

                                            </a>

                                        </li>
                                            @endif

                                    </ul>

                                </div>

                            </li>

                        @endif














                        <! ------------- ADS ----------- >
                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_ads'))


                        <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                            <a href="{{url('ads')}}" class="kt-menu__link">

                                <span class="kt-menu__link-icon"><i class=" fas fa-ad"></i></span>

                                <span class="kt-menu__link-text"> Ads</span>

                            </a>

                        </li>

                        @endif


                        <! ------------- Active Ads ----------- >

                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_active_ads'))

                        <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                            <a href="{{url('active_ads')}}" class="kt-menu__link">
                                <span class="kt-menu__link-icon"><i class=" fas fa-ad"></i></span>
                                <span class="kt-menu__link-text"> Active Ads</span>
                            </a>
                        </li>

                        @endif


                        <! ------------- File Gard  ----------- >

                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_file_gard'))


                        <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                            <a href="{{url('file_gard')}}" class="kt-menu__link">
                                <span class="kt-menu__link-icon"><i class="fa fa-file"></i></span>
                                <span class="kt-menu__link-text"> File Gard </span>
                            </a>
                        </li>
                        @endif









                        <! ------------- Logistics ----------- >

                        @if(permission_checker(Auth::guard('admin')->user()->id, 'show_logistic'))

                        <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                            <a href="{{url('logistics')}}" class="kt-menu__link">

                                <span class="kt-menu__link-icon"><i class=" fas fa-ad"></i></span>

                                <span class="kt-menu__link-text"> Logistics </span>

                            </a>

                        </li>
                        @endif


                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Fullfillment'))

                        <!--------------------- Fulfillment --------------------->

                            <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                                <a href="{{url('fulfillments')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-truck-loading"></i></span>

                                    <span class="kt-menu__link-text"> Fulfillment</span>

                                </a>

                            </li>

                        @endif












                        @if(false)

                        <!--------------------- Delivery --------------------->

                            <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                                <a href="{{url('delivery')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-truck"></i></span>

                                    <span class="kt-menu__link-text"> Delivery</span>

                                </a>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Order Notes'))

                        <!--------------------- Order Notes --------------------->

                            <li class="kt-menu__item" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">

                                <a href="{{url('orders_notes')}}" class="kt-menu__link">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-clipboard-list"></i></span>

                                    <span class="kt-menu__link-text"> Order Notes</span>

                                </a>

                            </li>

                        @endif

                        @if(permission_group_checker(Auth::guard('admin')->user()->id, 'Expanses Category')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Expanses')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Partners Category')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Partners')

                        || permission_group_checker(Auth::guard('admin')->user()->id, 'Profit & Loss'))

                        <!--------------------- Accounting --------------------->

                            <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                data-ktmenu-submenu-toggle="hover">

                                <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                    <span class="kt-menu__link-icon"><i class=" fas fa-list-alt"></i></span>

                                    <span class="kt-menu__link-text"> Accounting</span>

                                    <span class="kt-menu__link-badge"></span><i
                                            class="kt-menu__ver-arrow la la-angle-right"></i>

                                </a>

                                <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>

                                    <ul class="kt-menu__subnav">

                                        <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                <span class="kt-menu__link-text">Expanses</span>

                                                <span class="kt-menu__link-badge"></span><i
                                                        class="kt-menu__ver-arrow la la-angle-right"></i>

                                            </a>

                                            <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>

                                                <ul class="kt-menu__subnav">

                                                    <li class="kt-menu__item" aria-haspopup="true"><a
                                                                href="{{url('expanses_categories')}}"
                                                                class="kt-menu__link ">

                                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                            <span class="kt-menu__link-text">Categories</span></a>

                                                    </li>

                                                    <li class="kt-menu__item" aria-haspopup="true"><a
                                                                href="{{url('expanses')}}" class="kt-menu__link ">

                                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                            <span class="kt-menu__link-text">Exapnses</span></a>

                                                    </li>

                                                </ul>

                                            </div>

                                        </li>

                                        <li class="kt-menu__item  kt-menu__item--submenu" aria-haspopup="true"
                                            data-ktmenu-submenu-toggle="hover">

                                            <a href="javascript:;" class="kt-menu__link kt-menu__toggle">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                <span class="kt-menu__link-text">Partners</span>

                                                <span class="kt-menu__link-badge"></span><i
                                                        class="kt-menu__ver-arrow la la-angle-right"></i>

                                            </a>

                                            <div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>

                                                <ul class="kt-menu__subnav">

                                                    <li class="kt-menu__item" aria-haspopup="true"><a
                                                                href="{{url('partners_categories')}}"
                                                                class="kt-menu__link ">

                                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                            <span class="kt-menu__link-text">Categories</span></a>

                                                    </li>

                                                    <li class="kt-menu__item" aria-haspopup="true"><a
                                                                href="{{url('partners')}}" class="kt-menu__link ">

                                                            <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                            <span class="kt-menu__link-text">Partners</span></a>

                                                    </li>

                                                </ul>

                                            </div>

                                        </li>

                                        <li class="kt-menu__item" aria-haspopup="true"><a href="{{url('accounting')}}"
                                                                                          class="kt-menu__link ">

                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"></i>

                                                <span class="kt-menu__link-text">Profit & Loss</span></a>

                                        </li>

                                    </ul>


                                </div>

                            </li>

                        @endif

                    </ul>

                </div>

            </div>

            <!-- end:: Aside Menu -->

        </div>

        <!-- end:: Aside -->


        <!-- end:: Aside -->

        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">


            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">

                @yield('content')


            </div>

            <!-- begin:: Footer -->

            <div class="kt-footer kt-grid__item kt-grid kt-grid--desktop kt-grid--ver-desktop">

                <div class="kt-footer__socialmedia">

                    <a href="#" target="_blank" class="kt-link"><i class="fab fa-facebook-f"></i></a>

                    <a href="#" target="_blank" class="kt-link"><i class="fab fa-whatsapp"></i></a>

                    <a href="#" target="_blank" class="kt-link"><i class="fab fa-youtube"></i></a>

                </div>

                <div class="kt-footer__menu">

                    <a href="#" target="_blank" class="kt-footer__menu-link kt-link">About</a>

                    <a href="#" target="_blank" class="kt-footer__menu-link kt-link">Team</a>

                    <a href="#" target="_blank" class="kt-footer__menu-link kt-link">Contact</a>

                </div>

            </div>

            <!-- end:: Footer -->

        </div>


        <!-- begin::Global Config(global config for global JS sciprts) -->

        <script>

            var KTAppOptions = {

                "colors": {

                    "state": {

                        "brand": "#5d78ff",

                        "dark": "#282a3c",

                        "light": "#ffffff",

                        "primary": "#5867dd",

                        "success": "#34bfa3",

                        "info": "#36a3f7",

                        "warning": "#ffb822",

                        "danger": "#fd3995"

                    },

                    "base": {

                        "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],

                        "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]

                    }

                }

            };

        </script>


        <!-- end::Global Config -->


        <!--begin:: Global Mandatory Vendors -->

        <script src="{{ asset('assets/vendors/general/jquery/dist/jquery.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/popper.js/dist/umd/popper.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap/dist/js/bootstrap.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/js-cookie/src/js.cookie.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/moment/min/moment.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/tooltip.js/dist/umd/tooltip.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/perfect-scrollbar/dist/perfect-scrollbar.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/sticky-js/dist/sticky.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/wnumb/wNumb.js')}}"></script>

        <!--end:: Global Mandatory Vendors -->


        <!--begin:: Global Optional Vendors -->

        <script src="{{ asset('assets/vendors/general/jquery-form/dist/jquery.form.min.js')}}"></script>

        <script src="{{asset('ckeditor/ckeditor.js')}}"></script>

        <script src="{{asset('ckeditor/js/sample.js')}}"></script>


        <script src="{{ asset('assets/vendors/general/block-ui/jquery.blockUI.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/custom/components/vendors/bootstrap-datepicker/init.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-datetime-picker/js/bootstrap-datetimepicker.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/custom/components/vendors/bootstrap-timepicker/init.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-maxlength/src/bootstrap-maxlength.js')}}"></script>

        <script src="{{ asset('assets/vendors/custom/vendors/bootstrap-multiselectsplitter/bootstrap-multiselectsplitter.min.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-select/dist/js/bootstrap-select.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/bootstrap-switch/dist/js/bootstrap-switch.js')}}"></script>

        <script src="{{ asset('assets/vendors/custom/components/vendors/bootstrap-switch/init.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/select2/dist/js/select2.full.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/typeahead.js/dist/typeahead.bundle.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/handlebars/dist/handlebars.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/nouislider/distribute/nouislider.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/owl.carousel/dist/owl.carousel.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/autosize/dist/autosize.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/summernote/dist/summernote.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/raphael/raphael.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/morris.js/morris.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/waypoints/lib/jquery.waypoints.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/counterup/jquery.counterup.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/jquery.repeater/src/lib.js')}}"></script>

        <script src="{{ asset('assets/vendors/general/jquery.repeater/src/jquery.input.js')}}"></script>

        <script src="{{ asset('assets/demo/default/base/scripts.bundle.js')}}"></script>

        <script src="{{ asset('assets/app/custom/general/crud/forms/widgets/select2.js?a=1')}}"
                type="text/javascript"></script>

        <script src="{{ asset('assets/app/custom/general/dashboard.js')}}"></script>

        @yield('script-files')

        <script src="{{ asset('assets/app/bundle/app.bundle.js')}}"></script>

        <script src="{{ asset('assets/vendors/custom/datatables/datatables.bundle.js')}}"></script>

        <script src="{{ asset('assets/app/custom/general/crud/datatables/data-sources/html.js')}}"></script>

        <script src="{{ asset('assets/app/custom/general/crud/forms/widgets/bootstrap-timepicker.js')}}"
                type="text/javascript"></script>

        <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>

        <script src="{{ asset('js/custom.js')}}"></script>

        <script src="{{ asset('js/sweet.js')}}"></script>

        <!-- Include SweetAlert JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

@yield('scripts')


</body>


<!-- end::Body -->

</html>