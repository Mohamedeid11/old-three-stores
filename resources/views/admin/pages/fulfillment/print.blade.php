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
			<style>@media print {body {font-size: 15px;}}</style>
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body onload="print()">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
				    <th>Order</th>
				    <th>Name</th>
				    <th>Phone</th>
					<th>City</th>
					<th>Product</th>
					<th>Color</th>
					<th>Size</th>
					<th>Qty</th>
					<th>Order Date</th>
					<th>Product Note</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($orders as $order)
                    @foreach ($order->itemsq as $item)
    					<tr>
    						<td>{{$order->order_number}}</td>
							<td>{{$order->client_info->name}}</td>
							<td>{{$order->client_info->phone}}</td>
							<td>@if($order->city > 0) {{$order->city_info->title}} @endif</td>
							<td>{{$item->product_info->title}}</td>
							<td>@if($item->color > 0) {{$item->color_info->title}} @endif</td>
							<td>@if($item->size > 0) {{$item->size_info->title}} @endif</td>
							<td>{{$item->qty}}</td>		
							<td>{{date('Y-m-d', strtotime($order->created_at))}}</td>
							<td>{{$item->note}}</td>		
    					</tr>
    				@endforeach
				@endforeach
			</tbody>
		</table>
	</body>
</html>

