<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Three Stores Dashboard</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
			WebFont.load({
				google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
				active: function() {sessionStorage.fonts = true;}
			});
		</script>
		<link rel="stylesheet" href="{{asset('ckeditor/css/samples.css')}}">
		<link rel="stylesheet" href="{{asset('ckeditor/toolbarconfigurator/lib/codemirror/neo.css')}}">

		<link rel="stylesheet" href="{{asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.css')}}">	
		<link rel="stylesheet" href="{{asset('assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css')}}">	
		<link rel="stylesheet" href="{{asset('assets/vendors/general/tether/dist/css/tether.css')}}">	
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css')}}">		
		<link rel="stylesheet" href="{{asset('assets/vendors/general/select2/dist/css/select2.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css')}}">	
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css')}}">		
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css')}}">	
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/select2/dist/css/select2.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/ion-rangeslider/css/ion.rangeSlider.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/nouislider/distribute/nouislider.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/owl.carousel/dist/assets/owl.carousel.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/dropzone/dist/dropzone.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/summernote/dist/summernote.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/animate.css/animate.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/toastr/build/toastr.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/morris.js/morris.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/sweetalert2/dist/sweetalert2.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/general/socicon/css/socicon.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/line-awesome/css/line-awesome.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/flaticon/flaticon.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/flaticon2/flaticon.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/fontawesome5/css/all.min.css')}}">
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/datatables/datatables.bundle.css')}}">

		<!--begin::Global Theme Styles(used by all pages) -->
        <link rel="stylesheet" href="{{asset('assets/demo/default/base/style.bundle.css')}}">
        <!--end::Global Theme Styles -->
        <!--begin::Layout Skins(used by all pages) -->
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/base/light.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/menu/light.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/brand/dark.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/aside/dark.css')}}">
		<!--end::Layout Skins -->
		<link rel="stylesheet" href="{{ asset('css/custom.css')}}">
		<link href="{{asset('favicon.ico')}}" rel="shortcut icon" type="image/vnd.microsoft.icon">
		<link rel="shortcut icon" type="image/ico" href="{{asset('session1.png')}}" />

	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-page--loading">

		<!-- begin:: Page -->

		<!-- begin:: Header Mobile -->
		<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
			<div class="kt-header-mobile__logo">
				<a href="{{url('/admin')}}">
					<img alt="Logo" src="{{asset('assets/media/logos/logo-4.png')}}" />
				</a>
			</div>
			<div class="kt-header-mobile__toolbar">
				<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
				<button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
			</div>
		</div>

		<!-- end:: Header Mobile -->
		<div class="kt-grid kt-grid--hor kt-grid--root">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

			<!--------------------- Start Side Bar --------------------->



				<!-- end:: Aside -->
				<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
					<!-- begin:: Header -->
					<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">
                    <a href="{{url('/admin')}}" id="profile_logo"><img alt="Logo" src="{{asset('assets/media/logos/logo-4.png')}}" /></a>

						<!-- begin:: Header Menu -->
						<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
						<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
							<div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
								<ul class="kt-menu__nav ">
								
								</ul>
							</div>
						</div>

						<!-- end:: Header Menu -->

						<!-- begin:: Header Topbar -->
						<div class="kt-header__topbar">

							<!--begin: Search -->

							<!--begin: Search -->
							<!--
							<div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" id="kt_quick_search_toggle">
								<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
									<span class="kt-header__topbar-icon">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect id="bound" x="0" y="0" width="24" height="24" />
												<path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" id="Path-2" fill="#000000" fill-rule="nonzero" opacity="0.3" />
												<path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" id="Path" fill="#000000" fill-rule="nonzero" />
											</g>
										</svg> </span>
								</div>
								<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
									<div class="kt-quick-search kt-quick-search--inline" id="kt_quick_search_inline">
										<form method="get" class="kt-quick-search__form">
											<div class="input-group">
												<div class="input-group-prepend"><span class="input-group-text"><i class="flaticon2-search-1"></i></span></div>
												<input type="text" class="form-control kt-quick-search__input" placeholder="Search...">
												<div class="input-group-append"><span class="input-group-text"><i class="la la-close kt-quick-search__close"></i></span></div>
											</div>
										</form>
										<div class="kt-quick-search__wrapper kt-scroll" data-scroll="true" data-height="300" data-mobile-height="200">
										</div>
									</div>
								</div>
							</div>
							-->
							<!--end: Search -->

							<!--end: Search -->


							

							
						<div class="kt-header__topbar-item kt-header__topbar-item--user">
								<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
									<div class="kt-header__topbar-user">
										<span class="kt-header__topbar-username kt-hidden-mobile">{{Auth::guard('admin')->user()->name}}</span>
										@if(Auth::guard('admin')->user()->image == '')
											<img alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset('user.png')}}" />
										@else
											<img alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset(Auth::guard('admin')->user()->image)}}" />
										@endif	
									</div>
								</div>
								<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

									<!--begin: Head -->
									<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" 
									style="background-image: url({{asset('assets/media/misc/bg-1.jpg')}})">
										<div class="kt-user-card__avatar">
											@if(Auth::guard('admin')->user()->image == '')
												<img class="kt-hidden" alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset('user.png')}}" />
												<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
													<img alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset('user.png')}}" />
												</span>	
											@else
												<img class="kt-hidden" alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset(Auth::guard('admin')->user()->image)}}" />
												<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
													<img alt="{{Auth::guard('admin')->user()->name}}" src="{{ asset(Auth::guard('admin')->user()->image)}}" />
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

					<!-- end:: Header -->

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
		<script src="{{ asset('assets/vendors/general/ion-rangeslider/js/ion.rangeSlider.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/typeahead.js/dist/typeahead.bundle.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/handlebars/dist/handlebars.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/inputmask/dist/jquery.inputmask.bundle.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.date.extensions.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/inputmask/dist/inputmask/inputmask.numeric.extensions.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/nouislider/distribute/nouislider.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/owl.carousel/dist/owl.carousel.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/autosize/dist/autosize.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/clipboard/dist/clipboard.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/dropzone/dist/dropzone.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/summernote/dist/summernote.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/markdown/lib/markdown.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/bootstrap-markdown/js/bootstrap-markdown.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/components/vendors/bootstrap-markdown/init.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/components/vendors/bootstrap-notify/init.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/jquery-validation/dist/jquery.validate.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/jquery-validation/dist/additional-methods.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/components/vendors/jquery-validation/init.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/toastr/build/toastr.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/raphael/raphael.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/morris.js/morris.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/chart.js/dist/Chart.bundle.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/vendors/bootstrap-session-timeout/dist/bootstrap-session-timeout.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/vendors/jquery-idletimer/idle-timer.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/waypoints/lib/jquery.waypoints.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/counterup/jquery.counterup.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/sweetalert2/dist/sweetalert2.min.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/components/vendors/sweetalert2/init.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/jquery.repeater/src/lib.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/jquery.repeater/src/jquery.input.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/jquery.repeater/src/repeater.js')}}"></script>
		<script src="{{ asset('assets/vendors/general/dompurify/dist/purify.js')}}"></script>
		<script src="{{ asset('assets/demo/default/base/scripts.bundle.js')}}"></script>
		<script src="{{ asset('assets/app/custom/general/crud/forms/widgets/select2.js')}}" type="text/javascript"></script>

		<script src="{{ asset('assets/vendors/custom/fullcalendar/fullcalendar.bundle.js')}}"></script>
		<script src="{{ asset('assets/app/custom/general/dashboard.js')}}"></script>
		<script src="{{ asset('assets/app/bundle/app.bundle.js')}}"></script>
		<script src="{{ asset('assets/vendors/custom/datatables/datatables.bundle.js')}}"></script>
		<script src="{{ asset('assets/app/custom/general/crud/datatables/data-sources/html.js')}}"></script>
		<script src="{{ asset('js/custom.js')}}"></script>
	</body>

	<!-- end::Body -->
</html>