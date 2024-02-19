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
		<link rel="stylesheet" href="{{asset('assets/vendors/custom/vendors/fontawesome5/css/all.min.css')}}">
        <link href='https://fonts.googleapis.com/css?family=Libre Barcode 39' rel='stylesheet'>
		
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
		<link rel="shortcut icon" type="image/ico" href="{{asset('session1.png')}}" />

			<style>
			body {display: block; height: auto; background: white;}
			
			page {display: block; height: 210mm; width: 148.5mm;}
			.invoice_page {padding: 0 15px; margin-bottom: 15px;}
			@media print {
				@page { size: A5 landscape; margin: 0;}
				.invoice_page {padding: 15px 0; size: A5}	
				page[size="A5"] {height: 194mm;}
				body, page[size="A5"] {margin: 0; padding: 0; font-size: 20px;  -webkit-print-color-adjust: exact;}
				page[size="A5"] {margin-bottom: 38.312cm;}
				page[size="A5"]:last-child {margin-bottom: 0;}
				.mt-3 {margin-top: 30px;}
				td b {float: right;}
				td {-webkit-print-color-adjust: exact;}
				.table-striped tbody tr:nth-child(even) td {background: #ebedf2 !important;}
				.table-striped tbody tr:last-child td {background: #ffff !important;}
				.table-striped tbody tr:last-child td p {margin-bottom: 15px;}
				.table-striped tbody tr:last-child td p b {font-weight: 500;}
			}
			</style>
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body onload="print()">

		@foreach ($orders as $order)
			<page size="A5">
				<div class="invoice_page">
					<div class="container">
						<div class="row">
							<div class="col-6">
							    <img src="{{asset('assets/media/logos/logo-4.png')}}">
							</div>
							<div class="col-6 text-right">
							    <p style="font-family: 'Libre Barcode 39'; font-size: 60px; margin-bottom: 0; line-height: 1; color: black;">*{{$order->order_number}}*</p>
							</div>
						</div>
						<div class="row">
                            <div class="col-3">
								<p style="margin-bottom:0">Haram St - Giza</p>
								<p style="margin-bottom:0">01112224521</p>
							    <p>{{date('d F Y')}}</p>
							</div>
                            
							<div class="col-6 align-middle"><h1 class="text-center align-middle" style="color: black; font-size: 30px; font-weight: 400;">{{$order->client_info->name}}</h1></div>
							<div class="col-3 align-middle">
							    <h1 class="text-center align-middle" style="color: black; font-size: 30px; font-weight: 400;">#{{$order->order_number}}</h1>
							</div>

						</div>

						<table class="table" style="border-top:solid 2px #ccc;">
						<tbody>
							<tr>
								<td style="width: 200px; vertical-align:middle;">{{date('d-F-Y', strtotime($order->created_at))}}</td>
								<td style="width: 200px; vertical-align:middle;">{{$order->client_info->phone}}
								@if($order->client_info->phone_2 != '')- {{$order->client_info->phone_2}}@endif
								</td>
								<td colspan="2"  style="vertical-align:middle;">{{$order->address}} - {{$order->city_info->title}}</td>
							</tr>
						</table>
						<table class="table table-striped" style="border-top:solid 2px #ccc; margin-top:10px;">
							<thead>
								<tr>
									<th class="text-center" style="color: #3F51B5; font-weight: bold;">Total Price</th>
									<th class="text-center" style="color: #3F51B5; font-weight: bold;">Unit Price</th>
									<th class="text-center" style="color: #3F51B5; font-weight: bold;">Qty</th>
									<th style="color: #3F51B5; font-weight: bold;">Description</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($order->items as $item)
									<tr>
										<td class="text-center">{{$item->qty * $item->price}} EGP</td>
										<td class="text-center">{{$item->price}} EGP</td>
										<td class="text-center">{{$item->qty}}</td>
										<td>{{$item->product_info->title}} @if($item->color > 0) - {{$item->color_info->title}} @endif @if($item->size > 0) - {{$item->size_info->title}} @endif</td>
									</tr>
								@endforeach
								@for ($i = 0; $i < 3 - count($order->items); $i++)
							        <tr>
										<td><br /></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								@endfor
    							<tr>

    								<td>
    								    <p><span>{{$order->total_price}} EGP</span></p>
    								    <p><span>{{$order->shipping_fees}} EGP</span></p>
    								</td>
    								<td colspan="2">
    								    <p><b>Subtotal</b></p>
    									<p><b>Shipping Fees</b></p>
    								</td>
    								<td style="text-align: right;">
    								    <p>Notes</p>
    								    <p>{{$order->note}}</p>
    								</td>
    							</tr>
								<tr>
									<td colspan="4"><h2 style="color: red;">{{$order->shipping_fees + $order->total_price}} EGP</h2></td>
								</tr>
    						</tbody>
						</table>
					</div>
				</div>
			</page>
		@endforeach
		
	</body>
</html>

