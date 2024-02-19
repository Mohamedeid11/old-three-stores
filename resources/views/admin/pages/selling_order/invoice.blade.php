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
		<link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">

		<style>
			body { display: block; height: auto; background: white; }
			page[size="A5"] { background: white; display: block; margin: 0 auto; }
			.bar-code { font-family: 'Libre Barcode 39', cursive; font-size: 60px; line-height: 1; color: black; }
			.invoice_page { padding: 15px; padding-top: 50px; }

			@media print {
				@page {
					size: A5 landscape;
					margin: 0;
				}

				body, page[size="A5"] {
					width: 100%;
					height: 100%;
					margin: 0;
					padding: 0;
					font-size: 20px;
					-webkit-print-color-adjust: exact;
				}

				.mt-3 { margin-top: 30px; }
				td { -webkit-print-color-adjust: exact; }
				.table-striped tbody tr:nth-child(even) td { background: #ebedf2 !important; }
				.table-striped tbody tr:last-child td { background: #ffff !important; }
				.table-striped tbody tr:last-child td p { margin-bottom: 15px; }
				.table-striped tbody tr:last-child td p b { font-weight: 500; }
			}
		</style>

	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body onload="print()">

	@foreach ($orders as $order)
		@if($store_invoice)
			<page size="A5">
				<div class="invoice_page">
					<div class="container-fluid">
						<div class="row">
							<div class="col-4">
								<img src="{{asset('assets/media/logos/logo-4.png')}}">
							</div>
							<div class="col-8 text-right">
								<img src="{{asset(DNS1D::getBarcodePNGPath($order->order_number, 'C39'))}}" alt="barcode"   />
							</div>
						</div>
						<div class="row">
							<div class="col-3">
								<p style="margin-bottom:0">Haram St - Giza</p>
								<p style="margin-bottom:0">01112224521</p>
								<p>{{date('d F Y')}}</p>
							</div>

							<div class="col-6 align-middle"><h1 class="text-center align-middle" style="color: black; font-size: 50px; font-weight: 400;">{{$order->client_info->name}}</h1></div>
							<div class="col-3 align-middle">
								<h1 class="text-center align-middle" style="color: black; font-size: 50px; font-weight: 400;">#{{$order->order_number}}</h1>
							</div>

						</div>
						<table class="table" style="border-top:solid 2px #ccc;">
							<tr>
								<td style="width: 150px;">{{date('d-M', strtotime($order->created_at))}}</td>
								<td style="width: 150px;">{{$order->client_info->phone}}
									@if($order->client_info->phone_2 != '')
										- {{$order->client_info->phone_2}}
									@endif
								</td>
								<td>{{$order->address}} - {{$order->city_info->title}}</td>
							</tr>
						</table>
						<table class="table table-striped" style="border-top:solid 2px #ccc; margin-top:10px;">
							<thead>
							<tr>
								<th style="text-align:center; color: #3F51B5; font-weight: bold;">Description</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold;">Qty</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold; width: 150px;">Unit</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold; width: 150px;">Price</th>

							</tr>
							</thead>
							<tbody>
							@foreach ($order->items as $item)
								<tr>
									<td>{{$item->product_info->title}} @if($item->color > 0) - {{$item->color_info->title}} @endif @if($item->size > 0) - {{$item->size_info->title}} @endif</td>
									<td style="text-align:center;">{{$item->qty}}</td>
									<td style="text-align:center;">{{$item->price}} EGP</td>
									<td style="text-align:center;">{{$item->qty * $item->price}} EGP</td>
								</tr>
							@endforeach
							@for ($i = 0; $i < 4 - count($order->items); $i++)
								<tr>
									<td><br /></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							@endfor
							<tr>
								<td colspan="2" ><span><b>Payment Status</b> : @if($order->payment_status!='partly_paid') {{$order->payment_status}}  @else Partly Paid  @endif  @if($order->payment_status=='partly_paid')  {{$order->payment_amount}} EGP @endif</span>
								<span class="ml-5"><b>Shipping Fees</b> :<b> {{$order->shipping_fees}}</b> EGP</span></td>
								<td colspan="2" ><b>Total</b> : <b>{{$order->shipping_fees + $order->total_price - $order->payment_amount}} </b> EGP</td>
							</tr>
							<tr>
								<td colspan="4" style="text-align: left;">
									<p>Note : {{$order->note}}</p>
								</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</page>
		@endif
		@if($order->mylerz_barcode != '')
			<page size="A5">
				<div class="invoice_page">
					<div class="container-fluid">
						<div class='row' style="margin-bottom:25px; border:solid 1px black; padding:15px 30px;">
							<div class="col-8 text-center">
								<div class="row">
									<div class="col-7">
										<div class="row" style="margin-bottom:20px;">
											<div class="col-6">
												<img id='barcode'
													 src="https://api.qrserver.com/v1/create-qr-code/?data={{$order->mylerz_barcode}}&amp;size=500x500"
													 alt=""
													 title="{{$order->order_number}}"
													 width="100"
													 height="100" />
											</div>
											<div class="col-6">
												<img src="{{asset('threemylerz.PNG')}}" style="height:100px;" />
											</div>
										</div>
									</div>
									<div class="col-5">
										<p style="color:black; margin-bottom:15px; font-size: 25px; font-weight:bold;">Haram - {{$order->city_info->mylerz_district}}</p>
										<p style="color:black; margin-bottom:15px; font-size: 20px; font-weight:bold;">Cash-on Delivery</p>
										<p style="color:black; margin-bottom:0px; font-size: 20px; font-weight:bold;">{{$order->shipping_fees + $order->total_price}}</p>
									</div>
								</div>
								<p style="font-weight: 600; color: black; font-size: 25px; margin-bottom:10px; text-align:center;">
									<img src="{{asset(DNS1D::getBarcodePNGPath($order->mylerz_barcode, 'C39'))}}" alt="barcode" style="height:80px;"  />
								</p>

							</div>
							<div class="col-4 text-center" style="border-left:solid 1px black;">
								<img src="{{asset('assets/media/logos/logo-4.png')}}">
								<p style="color:black; margin:25px 0; font-size: 30px;">01112224521</p>
								<p style="color:black; margin:0; font-size: 45px; font-weight:bold;">{{$order->order_number}}</p>
							</div>
						</div>

						<table class="table" style="border-top:solid 2px #ccc;">
							<tr>
								<td style="width: 150px;">{{date('d-M', strtotime($order->created_at))}}</td>
								<td style="width: 150px;">{{$order->client_info->phone}}
									@if($order->client_info->phone_2 != '')
										- {{$order->client_info->phone_2}}
									@endif
								</td>
								<td style="font-weight:bold;color:black;">{{$order->client_info->name}}</td>
								<td>{{$order->address}}</td>
								<td>{{$order->city_info->title}}</td>
							</tr>
						</table>
						<table class="table table-striped" style="border-top:solid 2px #ccc; margin-top:10px;">
							<thead>
							<tr>
								<th style="text-align:center; color: #3F51B5; font-weight: bold;">Description</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold;">Qty</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold; width: 250px;">Unit</th>
								<th style="text-align:center; color: #3F51B5; font-weight: bold; width: 250px;">Price</th>

							</tr>
							</thead>
							<tbody>
							@foreach ($order->items as $item)
								<tr>
									<td>{{$item->product_info->title}} @if($item->color > 0) - {{$item->color_info->title}} @endif @if($item->size > 0) - {{$item->size_info->title}} @endif</td>
									<td style="text-align:center;">{{$item->qty}}</td>
									<td style="text-align:center;">{{$item->price}} EGP</td>
									<td style="text-align:center;">{{$item->qty * $item->price}} EGP</td>
								</tr>
							@endforeach
							@for ($i = 0; $i < 4 - count($order->items); $i++)
								<tr>
									<td><br /></td>
									<td></td>
									<td></td>
									<td style="text-align:center;">0.00 EGP</td>
								</tr>
							@endfor
							<tr style="border-top:solid 1px;">
								<td colspan="2" style="text-align: left; background-color:white !important;">
									<p>Note : {{$order->note}}</p>
								</td>
								<td style="text-align:center; background-color:#f7f8fa !important;"><b>Shipping Fees</b> : {{$order->shipping_fees}} EGP</td>
								<td style="text-align:center; background-color:#f7f8fa !important; color:red;"><b>Total</b> : {{$order->shipping_fees + $order->total_price}} EGP</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</page>
		@endif
	@endforeach

	</body>
</html>