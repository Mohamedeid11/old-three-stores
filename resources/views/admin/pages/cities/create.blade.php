@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-globe"></i></span>
                <h3 class="kt-portlet__head-title">Add City</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
			<form class="kt-form" method="post" action="{{url('cities')}}" enctype="multipart/form-data">
				{{csrf_field()}}
				<div class="row g-4">
					<div class="col-md-6 form-group">
						<label>Name</label>												
						<input class="form-control" type="text" placeholder="Name" name="title" value="{{old('title')}}" id="title" />
						@if ($errors->has('title'))<span class="form-text text-danger"><strong>{{ $errors->first('title') }}</strong></span>@endif
					</div>
					<div class="col-md-6 form-group">	
						<label>Shipment Amount</label>												
						<input class="form-control" type="number" step="0.01" placeholder="Shipment Amount" name="shipment" value="{{old('shipment')}}" id="shipment" />
						@if ($errors->has('shipment'))<span class="form-text text-danger"><strong>{{ $errors->first('shipment') }}</strong></span>@endif
					</div>
					<div class="col-md-6 form-group">
						<label>Mylerz City</label>
						<select class="form-control" name="mylerz_neighborhood" id="mylerz_neighborhood"
						data-url="{{url('city_zones')}}">
							<option value="" selected>Choose Mylerz City</option>
							@foreach ($neighborhoods as $neighborhood)
								<option value="{{$neighborhood['Code']}}">{{$neighborhood['EnName']}}</option>
							@endforeach
						</select>
						@if ($errors->has('mylerz_city'))<span class="form-text text-danger"><strong>{{ $errors->first('mylerz_city') }}</strong></span>@endif
					</div>
					<div class="col-md-6 form-group">
						<label>Mylerz Zone</label>
						<select class="form-control" name="mylerz_district" id="mylerz_district">
							<option value="" selected>Choose Mylerz District</option>
							
						</select>
						@if ($errors->has('mylerz_district'))<span class="form-text text-danger"><strong>{{ $errors->first('mylerz_district') }}</strong></span>@endif
					</div>
					<div class="col-md-12 form-group">	
						<label><input type="checkbox" name="mylerz_shipping" value="1" data-url="{{url('mylerz_shipping/get_neighborhoods')}}" /> Mylerz Shipping ?</label>
					</div>
				</div>
				<div class="kt-portlet__foot">
					<div class="kt-form__actions text-right">
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>					
@endsection