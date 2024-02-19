<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Three Stores Dashboard</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="csrf-token" content="YPSVg8kQmMaQ091CKuXPznaN2UtNTKjvZoj3OjHg">

		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<script>
			WebFont.load({
				google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
				active: function() {sessionStorage.fonts = true;}
			});
		</script>
		<link rel="stylesheet" href="http://three-store.com/ckeditor/css/samples.css">
		<link rel="stylesheet" href="http://three-store.com/ckeditor/toolbarconfigurator/lib/codemirror/neo.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css">	
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/tether/dist/css/tether.css">	
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css">		
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/select2/dist/css/select2.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-timepicker/css/bootstrap-timepicker.css">	
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-daterangepicker/daterangepicker.css">		
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css">	
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-select/dist/css/bootstrap-select.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/owl.carousel/dist/assets/owl.theme.default.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/summernote/dist/summernote.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/animate.css/animate.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/morris.js/morris.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/general/socicon/css/socicon.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/custom/vendors/line-awesome/css/line-awesome.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/custom/vendors/flaticon/flaticon.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/custom/vendors/flaticon2/flaticon.css">

		<link rel="stylesheet" href="http://three-store.com/assets/vendors/custom/vendors/fontawesome5/css/all.min.css">
		<link rel="stylesheet" href="http://three-store.com/assets/vendors/custom/datatables/datatables.bundle.css">

		<!--begin::Global Theme Styles(used by all pages) -->
        <link rel="stylesheet" href="http://three-store.com/assets/demo/default/base/style.bundle.css">
        <link rel="stylesheet" href="http://three-store.com/assets/demo/default/base/custom.css">
        <!--end::Global Theme Styles -->
        <!--begin::Layout Skins(used by all pages) -->
		<link rel="stylesheet" href="http://three-store.com/assets/demo/default/skins/header/base/light.css">
		<link rel="stylesheet" href="http://three-store.com/assets/demo/default/skins/header/menu/light.css">
		<link rel="stylesheet" href="http://three-store.com/assets/demo/default/skins/brand/dark.css">
		<link rel="stylesheet" href="http://three-store.com/assets/demo/default/skins/aside/dark.css">
		<!--end::Layout Skins -->
		<link rel="stylesheet" href="http://three-store.com/css/custom.css">
		<link href="http://three-store.com/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
		<link rel="shortcut icon" type="image/ico" href="http://three-store.com/session1.png" />
        	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

        <p>Order Count : {{$orders->count()}}</p>
        <table class="table table-bordered table-striped">
            <thead>
                <th>#</th>
                <th>Order Number</th>
                <th>Status</th>
                <th>Pcs</th>
                <th>Fullfilment</th>
                <th>Date</th>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td><b>{{$order->id}}</b></td>
                        <td><b>{{$order->order_number}}</b></td>
                        <td>{{$order->status_info->title}}</td>
                        <td>{{$order->itemsq->sum('qty')}}</td>
                        <td>{{$order->fullfilment_checker->count()}}</td>
                        <td>{{$order->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</table>
{{$orders->links()}}