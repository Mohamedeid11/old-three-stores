@extends('admin.layout.main')
@section('content')
<!-- begin:: Content -->
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon"><i class="kt-font-brand fas fa-list"></i></span>
                <h3 class="kt-portlet__head-title">Buying Order Details</h3>
            </div>
        </div>
        <div class="kt-portlet__body">
				<div class="form-group">
					<div class='row'>
						<div class='col-md-4'><p><b>Name : </b>{{$order->agent_info->name}}</p></div>
						<div class='col-md-8'><p><b>Address : </b>{{$order->agent_info->address}} 
						@if($order->agent_info->city_info) - {{$order->agent_info->city_info->title}} @endif
						</p></div>
						<div class='col-md-4'><p><b>Phone No. : </b>{{$order->agent_info->phone}}</p></div>
						<div class='col-md-4'><p><b>Email : </b>{{$order->agent_info->email}}</p></div>
					<!--	<div class='col-md-4'>-->
					<!--		<select name="status" class="form-control selling_order_status" num="{{$order->id}}" url="{{url('buying_order_status')}}">-->
					<!--			@foreach ($statuss as $status)-->
					<!--				<option value="{{$status->id}}" @if($status->id == $order->status) selected @endif>{{$status->title}}</option>-->
					<!--			@endforeach-->
					<!--		</select>-->
					<!--</div>-->
				</div>
				<hr />

				<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Product</th>
							<th>Qty</th>
							<th>Price</th>
							<th>Color</th>
							<th>Size</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($order->itemsq as $item)
							<tr>
								<td>{{$loop->iteration}}</td>
								<td>{{$item->product_info->title}}</td>
								<td>{{$item->qty}}</td>
								<td>{{$item->price}} EGP</td>
								<td>@if($item->color > 0) {{$item->color_info->title}} @endif</td>
								<td>@if($item->size > 0) {{$item->size_info->title}} @endif</td>
								<td>{{$item->qty * $item->price}} EGP</td>
							</tr>
					@endforeach
					<tr>
						<td colspan="6"></td>
						<td>{{$totals}} EGP</td>
					</tr>
				</table>

		</div>
	</div>
</div>					
@endsection