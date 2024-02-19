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
        .bar_codes {text-align:center; border:solid 1px whitesmoke; padding: 5px; border-radius: 5px;}
        </style>
	</head>

	<!-- end::Head -->

	<!-- begin::Body -->
	<body>

		@for ($i = 0; $i < count($bar_codes); $i++)
            <div class="container bg-white p-5 mt-5">
                <div class="row">
                    <div class="col-4 mb-3 text-center"><h3>{{$bar_codes[$i]}}</h3></div>
                    <div class="col-4 mb-3 text-center">
                        <p style="font-family: 'Libre Barcode 39'; font-size: 50px; margin-bottom: 0; line-height: 1; color: black;">*{{$bar_codes[$i]}}*</p>
                    </div>
                    <div class="col-4 mb-3 text-center">
                        <img src="{{asset(DNS2D::getBarcodePNGPath($bar_codes[$i], 'QRCODE', 5, 5))}}" alt="barcode"   />
                    </div>
                    @foreach ($bar_codes_types as $type)
                    <div class="col-4 mb-3 text-center">
                        <div class="bar_codes">
                            {{$type}}
                            <br />
                            <img src="{{asset(DNS1D::getBarcodePNGPath($bar_codes[$i], $type))}}" alt="barcode"   />
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <hr />
        @endfor
		
	</body>
</html>

