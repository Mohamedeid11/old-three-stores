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
        <link rel="stylesheet" href="{{asset('assets/demo/default/base/custom.css')}}">
        <!--end::Global Theme Styles -->
        <!--begin::Layout Skins(used by all pages) -->
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/base/light.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/header/menu/light.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/brand/dark.css')}}">
		<link rel="stylesheet" href="{{asset('assets/demo/default/skins/aside/dark.css')}}">
		<!--end::Layout Skins -->
		<link rel="stylesheet" href="{{ asset('css/custom.css')}}">
		<link rel="stylesheet" href="{{ asset('css/chat.css')}}">
		<script src="https://js.pusher.com/4.1/pusher.min.js"></script>
		<link href="{{asset('favicon.ico')}}" rel="shortcut icon" type="image/vnd.microsoft.icon">
		<link rel="shortcut icon" type="image/ico" href="{{asset('session1.png')}}" />
			<style>
			body {display: block; height: auto; background: white;}
			page[size="A4"] {background: white; display: block; margin: 0 auto;}
			
			.invoice_page {padding: 15px; padding-top: 50px;}
			@media print {
				page[size="A4"] {min-height: 38.312cm; width: 100%;}
				body, page[size="A4"] {margin: 0; padding: 0; font-size: 20px;}
				page[size="A4"] {margin-bottom: 38.312cm;}
				page[size="A4"]:last-child {margin-bottom: 0;}
				.mt-3 {margin-top: 30px;}
				td span {float: right;}
			}
			</style>
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body onload="print()">

		@foreach ($orders as $order)
		    @if($order->invoice != '')
    			<page size="A4">
    				<div class="invoice_page">
    					<div class="container-fluid">
    						<!--<div class="row">-->
    						<!--	<div class="col-4"></div>-->
    						<!--	<div class="col-4"><img src="{{asset('assets/media/logos/logo-4.png')}}"></div>-->
    						<!--	<div class="col-4 text-right">-->
    						<!--		<p><b>HARAM - Giza</b></p>-->
    						<!--		<p><b>01112224521</b></p>-->
    						<!--		<p>{{date('d F Y')}}</p>-->
    						<!--	</div>-->
    						<!--</div>-->
    						<!--<hr />-->
    						<!--<div class="row">-->
    						<!--	<div class="col-6"><h1 class="text-center">{{$order->order_number}}</h1></div>-->
    						<!--	<div class="col-6"><h1 class="text-center">{{$order->client_info->name}}</h1></div>-->
    						<!--</div>-->
    						<!--<hr />-->
    						<!--<table class="table table-striped">-->
    						<!--<thead>-->
    						<!--	<tr>-->
    						<!--		<th>Address</th>-->
    						<!--		<th>City</th>-->
    						<!--		<th>Phone</th>-->
    						<!--		<th>Order Date</th>-->
    						<!--	</tr>-->
    						<!--</thead>-->
    						<!--<tbody>-->
    						<!--	<tr>-->
    						<!--		<td>{{$order->address}}</td>-->
    						<!--		<td>@if($order->city > 0) {{$order->city_info->title}} @endif</td>-->
    						<!--		<td>{{$order->client_info->phone}}</td>-->
    						<!--		<td>{{date('d F Y', strtotime($order->created_at))}}</td>-->
    						<!--	</tr>-->
    						<!--</table>-->
    						<!--<table class="table table-striped table-bordered">-->
    						<!--	<thead>-->
    						<!--		<tr>-->
    						<!--			<th>Item</th>-->
    						<!--			@if(buy_order_has_colors($order->id))-->
    						<!--			<th>Color</th>-->
    						<!--			@endif-->
    						<!--			@if(buy_order_has_sizes($order->id))-->
    						<!--			<th>Size</th>-->
    						<!--			@endif-->
    						<!--			<th>Qty</th>-->
    						<!--			<th>Unit Price</th>-->
    									
    						<!--			<th>Total Price</th>-->
    						<!--		</tr>-->
    						<!--	</thead>-->
    						<!--	<tbody>-->
    						<!--		@foreach ($order->itemsq as $item)-->
    						<!--			<tr>-->
    						<!--				<td>{{$item->product_info->title}}</td>-->
    						<!--				@if(buy_order_has_colors($order->id))-->
    						<!--				<td>@if($item->color > 0) {{$item->color_info->title}} @endif</td>-->
    						<!--				@endif-->
    						<!--				@if(buy_order_has_sizes($order->id))-->
    						<!--				<td>@if($item->size > 0) {{$item->size_info->title}} @endif</td>-->
    						<!--				@endif-->
    						<!--				<td>{{$item->qty}}</td>-->
    						<!--				<td>{{$item->price}} EGP</td>-->
    										
    						<!--				<td>{{$item->qty * $item->price}} EGP</td>-->
    						<!--			</tr>-->
    						<!--		@endforeach-->
    						<!--		@for ($i = 1; $i <= 4; $i++)-->
    						<!--			<tr>-->
    						<!--				<td><br /><br /></td>-->
    						<!--				@if(buy_order_has_colors($order->id))-->
    						<!--				<td></td>-->
    						<!--				@endif-->
    						<!--				@if(buy_order_has_sizes($order->id))-->
    						<!--				<td></td>-->
    						<!--				@endif-->
    						<!--				<td></td>-->
    						<!--				<td></td>-->
    										
    						<!--				<td></td>-->
    						<!--			</tr>-->
    						<!--		@endfor-->
    						<!--	<tr>-->
    						<!--		<td @if(buy_order_has_colors($order->id) && buy_order_has_sizes($order->id)) colspan="4" -->
    						<!--		@elseif(!buy_order_has_colors($order->id) && !buy_order_has_sizes($order->id)) colspan="2"-->
    						<!--		@else colspan="3" @endif><p>{{$order->note}}</p></td>-->
    						<!--		<td colspan="2">-->
    						<!--			<p><b>Subtotal : </b> <span>{{$order->total_price}} EGP</span></p>-->
    						<!--			<p><b>Shipping Fees : </b> <span>{{$order->shipping_fees}} EGP</span></p>-->
    						<!--			<br />-->
    						<!--			<h3><b>Total : </b> <span>{{$order->shipping_fees + $order->total_price}} EGP</span></h3>-->
    						<!--		</td>-->
    						<!--	</tr>-->
    						<!--</table>-->
    						<<img src="{{asset($order->invoice)}}" class="img-responsive" />
    					</div>
    				</div>
    			</page>
    		@endif
		@endforeach
		
	</body>
</html>

